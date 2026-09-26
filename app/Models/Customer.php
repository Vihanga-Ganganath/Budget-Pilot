<?php
/*
 * Customer model — customer accounts and households.
 *
 * One household = one main account holder (household_role 'head') plus any
 * family members (household_role 'member'). Every person is a row in `users`
 * with role 'customer', and each has a `user_profiles` row.
 */
class Customer {
    private $db;

    /** Folder inside public/ where profile photos are saved. */
    const AVATAR_DIR = 'assets/img/avatars';

    /** Old folder - photo paths saved before the move still point here. */
    const OLD_AVATAR_DIR = 'uploads/avatars';

    /** Currencies the Settings page offers (stored in preferred_currency). */
    const CURRENCIES = ['USD', 'EUR', 'GBP', 'LKR', 'INR', 'AUD'];

    /** Forgot-password rules. */
    const RESET_MINUTES      = 10;   // a reset request works for this long
    const RESET_MAX_ATTEMPTS = 5;    // wrong NICs / PINs before the request is cancelled
    const RESET_PER_HOUR     = 5;    // reset requests one account can start per hour

    /* No email code any more: stage 'nic' -> 'pin' (only if two-factor is on) -> 'ready'. */
    const PASSWORD_RESETS_SQL = "CREATE TABLE IF NOT EXISTS password_resets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        stage ENUM('nic', 'pin', 'ready') DEFAULT 'nic',
        attempts TINYINT UNSIGNED DEFAULT 0,
        ip_address VARCHAR(45),
        expires_at DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_reset_user (user_id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";

    /** Photo files to remove once the current transaction has committed. */
    private $filesToDelete = [];

    /** Photo files written during a transaction, removed again on rollback. */
    private $filesWritten = [];

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->ensureSchema();
    }

    // ─── Database upgrade ────────────────────────────────────────────────────

    /** Checked once per request. */
    private static $schemaChecked = false;

    /**
     * Brings an older database up to date on its own, so nobody has to run
     * SQL by hand:
     *   users.date_of_birth (DATE) → users.age (number)
     *   user_profiles gets profile_picture_url / preferred_currency /
     *   financial_goals if it was created without them.
     * If anything here fails, the site keeps working with the old columns.
     */
    private function ensureSchema() {
        if (self::$schemaChecked || !$this->db) return;
        self::$schemaChecked = true;

        try {
            $users = $this->db->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_COLUMN);
            if (!in_array('age', $users, true)) {
                $this->db->exec("ALTER TABLE users ADD COLUMN age TINYINT UNSIGNED NULL AFTER nic");
            }
            if (in_array('date_of_birth', $users, true)) {
                $this->db->exec("ALTER TABLE users DROP COLUMN date_of_birth");
            }

            $profiles = $this->db->query("SHOW COLUMNS FROM user_profiles")->fetchAll(PDO::FETCH_COLUMN);
            $wanted = [
                'profile_picture_url' => "VARCHAR(255) NULL",
                'preferred_currency'  => "VARCHAR(10) DEFAULT 'LKR'",
                'financial_goals'     => "TEXT NULL",
            ];
            foreach ($wanted as $column => $type) {
                if (!in_array($column, $profiles, true)) {
                    $this->db->exec("ALTER TABLE user_profiles ADD COLUMN $column $type");
                }
            }

            // Forgot password: users.two_factor_enabled + users.two_factor_pin_hash
            // and the password_resets table.
            $users = $this->db->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_COLUMN);
            if (!in_array('two_factor_enabled', $users, true)) {
                $this->db->exec("ALTER TABLE users ADD COLUMN two_factor_enabled BOOLEAN DEFAULT FALSE");
            }
            if (!in_array('two_factor_pin_hash', $users, true)) {
                $this->db->exec("ALTER TABLE users ADD COLUMN two_factor_pin_hash VARCHAR(255) NULL AFTER two_factor_enabled");
            }
            // The old email-code version of password_resets (it had code_hash)
            // only holds short-lived requests, so it is simply rebuilt.
            if ($this->db->query("SHOW TABLES LIKE 'password_resets'")->fetchColumn()) {
                $resets = $this->db->query("SHOW COLUMNS FROM password_resets")->fetchAll(PDO::FETCH_COLUMN);
                if (in_array('code_hash', $resets, true)) {
                    $this->db->exec("DROP TABLE password_resets");
                }
            }
            $this->db->exec(self::PASSWORD_RESETS_SQL);
        } catch (PDOException $e) {
            error_log('ensureSchema: ' . $e->getMessage());
        }
    }

    // ─── Profile photos ──────────────────────────────────────────────────────

    /**
     * Saves a photo sent by the browser as a data URL
     * ("data:image/jpeg;base64,...") into public/assets/img/avatars/.
     * Returns the path stored in user_profiles.profile_picture_url
     * (e.g. 'assets/img/avatars/u5_1727000000_a1b2c3.jpg'), or null if the data
     * is not a real JPG/PNG/WEBP image under 2MB.
     */
    public function saveAvatarFile($userId, $dataUrl) {
        if (!is_string($dataUrl) || !preg_match('#^data:image/[a-z]+;base64,#', $dataUrl)) return null;

        $bytes = base64_decode(substr($dataUrl, strpos($dataUrl, ',') + 1), true);
        if ($bytes === false || $bytes === '' || strlen($bytes) > 2 * 1024 * 1024) return null;

        // Check the bytes really are an image, whatever the browser claimed.
        $info = @getimagesizefromstring($bytes);
        $types = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!$info || !isset($types[$info['mime']])) return null;

        $dir = APPROOT . '/../public/' . self::AVATAR_DIR;
        if (!is_dir($dir) && !@mkdir($dir, 0775, true)) return null;

        $name = 'u' . (int) $userId . '_' . time() . '_' . bin2hex(random_bytes(3)) . '.' . $types[$info['mime']];
        if (@file_put_contents($dir . '/' . $name, $bytes) === false) return null;

        $path = self::AVATAR_DIR . '/' . $name;
        $this->filesWritten[] = $path;
        return $path;
    }

    /** Full web address of a stored photo path (null when there is none). */
    public static function avatarUrl($path) {
        $path = self::avatarPath($path);
        return $path ? URLROOT . '/public/' . $path : null;
    }

    /** Maps an old 'uploads/avatars/...' path to the new img/avatars folder. */
    private static function avatarPath($path) {
        if ($path && strpos($path, self::OLD_AVATAR_DIR . '/') === 0) {
            return self::AVATAR_DIR . '/' . basename($path);
        }
        return $path;
    }

    /** Deletes a photo file, but only ever inside the avatars folder. */
    private function deleteAvatarFile($path) {
        $path = self::avatarPath($path);
        if (!$path || strpos($path, self::AVATAR_DIR . '/') !== 0) return;
        $file = APPROOT . '/../public/' . self::AVATAR_DIR . '/' . basename($path);
        if (is_file($file)) @unlink($file);
    }

    /** After a commit: remove photos that were replaced or whose owner was deleted. */
    private function afterCommit() {
        foreach ($this->filesToDelete as $path) $this->deleteAvatarFile($path);
        $this->filesToDelete = [];
        $this->filesWritten  = [];
    }

    /** After a rollback: remove photos written for rows that were never saved. */
    private function afterRollback() {
        foreach ($this->filesWritten as $path) $this->deleteAvatarFile($path);
        $this->filesToDelete = [];
        $this->filesWritten  = [];
    }

    /** '' / null → null, otherwise the amount with 2 decimals (for financial_goals). */
    private function goalValue($amount) {
        if ($amount === null || $amount === '' || !is_numeric($amount)) return null;
        return number_format((float) $amount, 2, '.', '');
    }

    // ─── Checks ──────────────────────────────────────────────────────────────

    /** True if this email already has a customer account. */
    public function emailExists($email) {
        $stmt = $this->db->prepare(
            "SELECT id FROM users WHERE email = :email AND role = 'customer' LIMIT 1"
        );
        $stmt->execute([':email' => strtolower(trim($email))]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    // ─── Create ──────────────────────────────────────────────────────────────

    /**
     * Creates a household with its main holder and optional members, all in
     * one transaction (either everyone is saved or nobody is).
     *
     * $owner   = [name, email, password, nic, gender, age, income, savings, avatar]
     * $members = [[name, email, password, avatar], ...]
     * (avatar is an optional data URL from the sign-up form)
     *
     * Returns the household id, 'duplicate' if an email is taken, or false.
     */
    public function registerHousehold($owner, $members = []) {
        try {
            $this->db->beginTransaction();

            // 1. Household — takes the lowest free number, so the number of a
            //    deleted household is used again (1 exists, 2 and 3 deleted → 2).
            $householdId = $this->nextHouseholdId();
            $stmt = $this->db->prepare("INSERT INTO households (id, name) VALUES (:id, :name)");
            $stmt->execute([':id' => $householdId, ':name' => $owner['name'] . "'s Household"]);

            // 2. Main account holder
            $ownerId = $this->insertPerson($householdId, 'head', $owner);

            // 3. Record who manages the household
            $stmt = $this->db->prepare(
                "UPDATE households SET created_by_user_id = :uid WHERE id = :hid"
            );
            $stmt->execute([':uid' => $ownerId, ':hid' => $householdId]);

            // 4. Family members
            foreach ($members as $m) {
                $this->insertPerson($householdId, 'member', $m);
            }

            $this->db->commit();
            $this->afterCommit();
            return $householdId;

        } catch (PDOException $e) {
            $this->db->rollBack();
            $this->afterRollback();
            if ($e->getCode() == 23000) return 'duplicate';   // unique email+role
            return false;
        }
    }

    /**
     * Lowest household number not in use: 1 if there is no household 1,
     * otherwise the first gap after an existing number (or MAX + 1).
     * Call inside the registerHousehold() transaction — the FOR UPDATE read
     * locks the households table so two sign-ups can't pick the same number.
     */
    private function nextHouseholdId() {
        $this->db->query("SELECT id FROM households FOR UPDATE")->fetchAll();

        $next = $this->db->query(
            "SELECT CASE
                 WHEN NOT EXISTS (SELECT 1 FROM households WHERE id = 1) THEN 1
                 ELSE (SELECT MIN(h.id) + 1
                       FROM households h
                       LEFT JOIN households n ON n.id = h.id + 1
                       WHERE n.id IS NULL)
             END"
        )->fetchColumn();

        return (int) $next;
    }

    /**
     * Adds members to an existing household.
     * Returns true, 'duplicate', or false.
     */
    public function addMembers($householdId, $members) {
        try {
            $this->db->beginTransaction();
            foreach ($members as $m) {
                $this->insertPerson($householdId, 'member', $m);
            }
            $this->db->commit();
            $this->afterCommit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            $this->afterRollback();
            if ($e->getCode() == 23000) return 'duplicate';
            return false;
        }
    }

    /** Inserts one person into `users` + `user_profiles`; returns the user id. */
    private function insertPerson($householdId, $householdRole, $p) {
        $stmt = $this->db->prepare(
            "INSERT INTO users
                (household_id, role, household_role, name, email, password_hash,
                 nic, gender, age, account_status, created_at)
             VALUES
                (:hid, 'customer', :hrole, :name, :email, :hash,
                 :nic, :gender, :age, 'active', NOW())"
        );
        $stmt->execute([
            ':hid'    => $householdId,
            ':hrole'  => $householdRole,
            ':name'   => trim($p['name']),
            ':email'  => strtolower(trim($p['email'])),
            ':hash'   => password_hash($p['password'], PASSWORD_DEFAULT),
            ':nic'    => !empty($p['nic']) ? strtoupper(trim($p['nic'])) : null,
            ':gender' => !empty($p['gender']) ? $p['gender'] : null,
            ':age'    => !empty($p['age']) ? (int) $p['age'] : null,
        ]);
        $userId = (int) $this->db->lastInsertId();

        // Photo chosen on the sign-up form → saved as a file, path kept in the profile.
        $photo = !empty($p['avatar']) ? $this->saveAvatarFile($userId, $p['avatar']) : null;

        $stmt = $this->db->prepare(
            "INSERT INTO user_profiles (user_id, monthly_income, profile_picture_url, financial_goals)
             VALUES (:uid, :income, :photo, :goal)"
        );
        $stmt->execute([
            ':uid'    => $userId,
            ':income' => isset($p['income']) && $p['income'] !== '' ? (float) $p['income'] : 0,
            ':photo'  => $photo,
            ':goal'   => $this->goalValue($p['savings'] ?? null),
        ]);

        return $userId;
    }

    // ─── Read ────────────────────────────────────────────────────────────────

    /**
     * Checks email + password for a customer.
     * Returns the user row, ['error' => 'locked'|'suspended'], or false.
     */
    public function login($email, $password) {
        $stmt = $this->db->prepare(
            "SELECT * FROM users WHERE email = :email AND role = 'customer' LIMIT 1"
        );
        $stmt->execute([':email' => strtolower(trim($email))]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return false;

        if (!password_verify($password, $row['password_hash'])) {
            $this->logLogin($row['id'], 'failed');
            return false;
        }
        if ($row['account_status'] === 'locked' || $row['account_status'] === 'suspended') {
            $this->logLogin($row['id'], 'locked_out');
            return ['error' => $row['account_status']];
        }

        $this->logLogin($row['id'], 'success');
        $stmt = $this->db->prepare("UPDATE users SET last_login_at = NOW() WHERE id = :id");
        $stmt->execute([':id' => $row['id']]);

        return $row;
    }

    /** Everyone in a household (head first). Never returns password hashes. */
    public function getHouseholdMembers($householdId) {
        $stmt = $this->db->prepare(
            "SELECT u.id, u.name, u.email, u.household_role, u.gender, u.nic, u.age,
                    u.account_status, u.created_at, u.last_login_at,
                    COALESCE(p.monthly_income, 0) AS monthly_income,
                    p.profile_picture_url, p.preferred_currency, p.financial_goals
             FROM users u
             LEFT JOIN user_profiles p ON p.user_id = u.id
             WHERE u.household_id = :hid AND u.role = 'customer'
             ORDER BY (u.household_role = 'head') DESC, u.id ASC"
        );
        try {
            $stmt->execute([':hid' => $householdId]);
        } catch (PDOException $e) {
            // Database without the new columns yet — load the basic list so
            // signing in still works.
            error_log('getHouseholdMembers: ' . $e->getMessage());
            $stmt = $this->db->prepare(
                "SELECT u.id, u.name, u.email, u.household_role, u.gender, u.nic,
                        u.account_status, u.created_at, u.last_login_at,
                        COALESCE(p.monthly_income, 0) AS monthly_income
                 FROM users u
                 LEFT JOIN user_profiles p ON p.user_id = u.id
                 WHERE u.household_id = :hid AND u.role = 'customer'
                 ORDER BY (u.household_role = 'head') DESC, u.id ASC"
            );
            $stmt->execute([':hid' => $householdId]);
        }
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Full web address of each photo, ready for the page to show.
        foreach ($rows as &$row) {
            foreach (['age', 'profile_picture_url', 'preferred_currency', 'financial_goals'] as $k) {
                if (!array_key_exists($k, $row)) $row[$k] = null;
            }
            $row['avatar_url'] = self::avatarUrl($row['profile_picture_url']);
        }
        unset($row);
        return $rows;
    }

    /**
     * Every household with its people, for the profile picker on the sign-in
     * page: [['id', 'name', 'members' => [['id','name','email','role','avatar'], ...]], ...]
     * Household number order, head first inside each household.
     * Never returns password hashes.
     */
    public function getLoginDirectory() {
        try {
            $stmt = $this->db->query(
                "SELECT h.id AS household_id, h.name AS household_name,
                        u.id, u.name, u.email, u.household_role,
                        p.profile_picture_url
                 FROM households h
                 JOIN users u ON u.household_id = h.id AND u.role = 'customer'
                 LEFT JOIN user_profiles p ON p.user_id = u.id
                 ORDER BY h.id ASC, (u.household_role = 'head') DESC, u.id ASC"
            );
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('getLoginDirectory: ' . $e->getMessage());
            return [];
        }

        $households = [];
        foreach ($rows as $r) {
            $hid = (int) $r['household_id'];
            if (!isset($households[$hid])) {
                $households[$hid] = ['id' => $hid, 'name' => $r['household_name'], 'members' => []];
            }
            $households[$hid]['members'][] = [
                'id'     => (int) $r['id'],
                'name'   => $r['name'],
                'email'  => $r['email'],
                'role'   => $r['household_role'] === 'head' ? 'Main' : 'Member',
                'avatar' => self::avatarUrl($r['profile_picture_url']),
            ];
        }
        return array_values($households);
    }

    /**
     * READ (one) — a single family member, only if they belong to this
     * household and are a 'member' (never the head). Returns the row or false.
     */
    public function getMember($householdId, $memberId) {
        $stmt = $this->db->prepare(
            "SELECT u.id, u.name, u.email, u.household_role, u.gender,
                    u.account_status, u.created_at, u.last_login_at
             FROM users u
             WHERE u.id = :id AND u.household_id = :hid
               AND u.role = 'customer' AND u.household_role = 'member'
             LIMIT 1"
        );
        $stmt->execute([':id' => $memberId, ':hid' => $householdId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ─── Update ──────────────────────────────────────────────────────────────

    /** True if another customer (not $userId) already uses this email. */
    public function emailTakenByOther($email, $userId) {
        $stmt = $this->db->prepare(
            "SELECT id FROM users
             WHERE email = :email AND role = 'customer' AND id <> :id LIMIT 1"
        );
        $stmt->execute([':email' => strtolower(trim($email)), ':id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    /**
     * Updates a customer's own account details and profile.
     * $d = [name, email, nic, gender, age, currency, savingsGoal, avatar]
     *   users         ← name, email, nic, gender, age
     *   user_profiles ← preferred_currency, financial_goals (savings goal),
     *                   profile_picture_url (only when a new photo is sent)
     * Returns ['ok' => true, 'avatarUrl' => url|null], 'duplicate',
     * 'badimage' (photo could not be saved), or false.
     */
    public function updateAccount($userId, $d) {
        // A new photo is written to disk first; the old file goes after the commit.
        $newPhoto = null;
        if (!empty($d['avatar'])) {
            $newPhoto = $this->saveAvatarFile($userId, $d['avatar']);
            if (!$newPhoto) return 'badimage';
        }

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare(
                "UPDATE users
                 SET name = :name, email = :email, nic = :nic, gender = :gender, age = :age
                 WHERE id = :id AND role = 'customer'"
            );
            $stmt->execute([
                ':name'   => trim($d['name']),
                ':email'  => strtolower(trim($d['email'])),
                ':nic'    => $d['nic'] !== '' ? strtoupper($d['nic']) : null,
                ':gender' => $d['gender'] !== '' ? $d['gender'] : null,
                ':age'    => $d['age'] !== null && $d['age'] !== '' ? (int) $d['age'] : null,
                ':id'     => $userId,
            ]);

            // Old photo path, so its file can be removed once the new one is saved.
            $stmt = $this->db->prepare("SELECT profile_picture_url FROM user_profiles WHERE user_id = :id");
            $stmt->execute([':id' => $userId]);
            $oldPhoto = $stmt->fetchColumn();

            // Insert the profile row if an older account has none, otherwise update it.
            $stmt = $this->db->prepare(
                "INSERT INTO user_profiles (user_id, preferred_currency, financial_goals, profile_picture_url)
                 VALUES (:id, :currency, :goal, :photo)
                 ON DUPLICATE KEY UPDATE
                    preferred_currency  = VALUES(preferred_currency),
                    financial_goals     = VALUES(financial_goals),
                    profile_picture_url = VALUES(profile_picture_url)"
            );
            $stmt->execute([
                ':id'       => $userId,
                ':currency' => $d['currency'],
                ':goal'     => $this->goalValue($d['savingsGoal']),
                ':photo'    => $newPhoto ?: ($oldPhoto ?: null),
            ]);

            if ($newPhoto && $oldPhoto) $this->filesToDelete[] = $oldPhoto;

            $this->db->commit();
            $this->afterCommit();
            return ['ok' => true, 'avatarUrl' => self::avatarUrl($newPhoto ?: ($oldPhoto ?: null))];

        } catch (PDOException $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            $this->afterRollback();
            $this->lastError = $e->getMessage();
            if ($e->getCode() == 23000) return 'duplicate';
            return false;
        }
    }

    /**
     * Changes a password after checking the current one.
     * Returns true, 'wrong' (current password incorrect), or false.
     */
    public function changePassword($userId, $current, $new) {
        $stmt = $this->db->prepare(
            "SELECT password_hash FROM users WHERE id = :id AND role = 'customer'"
        );
        $stmt->execute([':id' => $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row || !password_verify($current, $row['password_hash'])) return 'wrong';

        $stmt = $this->db->prepare("UPDATE users SET password_hash = :hash WHERE id = :id");
        return $stmt->execute([
            ':hash' => password_hash($new, PASSWORD_DEFAULT),
            ':id'   => $userId,
        ]);
    }

    /**
     * UPDATE — the head edits a family member.
     * $d = [name, email, gender, password]; an empty password keeps the old one.
     * Returns true, 'duplicate' (email taken), 'notfound', or false.
     */
    public function updateMember($householdId, $memberId, $d) {
        if (!$this->getMember($householdId, $memberId)) return 'notfound';

        try {
            $sql = "UPDATE users
                    SET name = :name, email = :email, gender = :gender";
            $params = [
                ':name'   => trim($d['name']),
                ':email'  => strtolower(trim($d['email'])),
                ':gender' => $d['gender'] !== '' ? $d['gender'] : null,
                ':id'     => $memberId,
                ':hid'    => $householdId,
            ];
            if ($d['password'] !== '') {
                $sql .= ", password_hash = :hash";
                $params[':hash'] = password_hash($d['password'], PASSWORD_DEFAULT);
            }
            $sql .= " WHERE id = :id AND household_id = :hid
                        AND role = 'customer' AND household_role = 'member'";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return true;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) return 'duplicate';
            return false;
        }
    }

    /**
     * DELETE — the head removes a family member from the household.
     * Deleting the `users` row also removes their user_profiles, login_history,
     * notifications and transactions rows (ON DELETE CASCADE in init.sql).
     * Returns true, 'notfound', or false.
     */
    // ─── Delete helpers ──────────────────────────────────────────────────────

    /**
     * Runs one clean-up statement. A table or column that doesn't exist in
     * this copy of the database is skipped, so the delete still works on
     * databases built from an older init.sql.
     */
    private function tidy($sql, $params) {
        try {
            $this->db->prepare($sql)->execute($params);
        } catch (PDOException $e) {
            $code = $e->errorInfo[0] ?? '';
            if ($code !== '42S02' && $code !== '42S22') throw $e;   // unknown table / column
        }
    }

    /**
     * Removes every row that points at this user, then the user.
     * Does not rely on ON DELETE CASCADE, because a database created from an
     * older schema may not have it, and MySQL then refuses the delete.
     * Call inside a transaction. Returns the number of `users` rows deleted.
     */
    private function purgeUser($userId) {
        $p = [':id' => $userId];

        // Their photo file is removed after the delete is committed.
        try {
            $stmt = $this->db->prepare("SELECT profile_picture_url FROM user_profiles WHERE user_id = :id");
            $stmt->execute($p);
            $photo = $stmt->fetchColumn();
            if ($photo) $this->filesToDelete[] = $photo;
        } catch (PDOException $e) {
            // Older database without the column — nothing to remove.
        }

        // Rows that belong to this user → delete.
        foreach ([
            'user_profiles', 'login_history', 'user_preferences',
            'notification_preferences', 'notifications', 'transactions',
            'product_reviews', 'user_favorites',
        ] as $table) {
            $this->tidy("DELETE FROM $table WHERE user_id = :id", $p);
        }
        $this->tidy(
            "DELETE i FROM shopping_list_items i
               JOIN shopping_lists l ON l.id = i.shopping_list_id
              WHERE l.created_by_user_id = :id", $p);
        $this->tidy("DELETE FROM shopping_lists WHERE created_by_user_id = :id", $p);

        // Logs that should outlive the user → keep, just unlink.
        foreach (['error_logs', 'search_analytics', 'page_views_log'] as $table) {
            $this->tidy("UPDATE $table SET user_id = NULL WHERE user_id = :id", $p);
        }

        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id AND role = 'customer'");
        $stmt->execute($p);
        return $stmt->rowCount();
    }

    /** Removes a household and everything filed under it. Call inside a transaction. */
    private function purgeHousehold($householdId) {
        $p = [':hid' => $householdId];
        $this->tidy(
            "DELETE i FROM shopping_list_items i
               JOIN shopping_lists l ON l.id = i.shopping_list_id
              WHERE l.household_id = :hid", $p);
        foreach (['shopping_lists', 'transactions', 'budgets', 'savings_goals',
                  'loans', 'transaction_categories'] as $table) {
            $this->tidy("DELETE FROM $table WHERE household_id = :hid", $p);
        }
        $this->db->prepare("DELETE FROM households WHERE id = :hid")->execute($p);
    }

    /** The last database error, kept so the controller can show it in debug mode. */
    public $lastError = null;

    public function deleteMember($householdId, $memberId) {
        if (!$this->getMember($householdId, $memberId)) return 'notfound';

        try {
            $this->db->beginTransaction();
            $deleted = $this->purgeUser($memberId);
            $this->db->commit();
            $this->afterCommit();
            return $deleted > 0;
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            $this->afterRollback();
            $this->lastError = $e->getMessage();
            error_log('deleteMember: ' . $e->getMessage());
            return false;
        }
    }

    // ─── Delete own account (Settings → Deactivate Account) ──────────────────

    /**
     * DELETE — a customer deletes their own account after re-entering their
     * password.
     *   - Family member  → only their `users` row is deleted.
     *   - Head, with members left → the oldest member becomes the new head,
     *     then the old head's row is deleted.
     *   - Head, alone    → their `users` row AND the `households` row are
     *     deleted (budgets, transactions, loans etc. cascade with it).
     * Deleting a `users` row also removes user_profiles, login_history,
     * notifications and their transactions (ON DELETE CASCADE).
     *
     * Returns ['ok' => true, 'promoted' => name|null],
     *         'wrong' (password incorrect), 'notfound', or false.
     */
    public function deleteAccount($userId, $password) {
        $stmt = $this->db->prepare(
            "SELECT id, household_id, household_role, password_hash
             FROM users WHERE id = :id AND role = 'customer' LIMIT 1"
        );
        $stmt->execute([':id' => $userId]);
        $me = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$me) return 'notfound';
        if (!password_verify($password, $me['password_hash'])) return 'wrong';

        $householdId = (int) $me['household_id'];
        $promoted    = null;

        try {
            $this->db->beginTransaction();

            if ($me['household_role'] === 'head') {
                // Is anyone else left in the household?
                $stmt = $this->db->prepare(
                    "SELECT id, name FROM users
                     WHERE household_id = :hid AND role = 'customer' AND id <> :id
                     ORDER BY id ASC LIMIT 1"
                );
                $stmt->execute([':hid' => $householdId, ':id' => $userId]);
                $next = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($next) {
                    // Hand the household over to the next member.
                    $stmt = $this->db->prepare(
                        "UPDATE users SET household_role = 'head' WHERE id = :id"
                    );
                    $stmt->execute([':id' => $next['id']]);

                    $stmt = $this->db->prepare(
                        "UPDATE households SET created_by_user_id = :uid WHERE id = :hid"
                    );
                    $stmt->execute([':uid' => $next['id'], ':hid' => $householdId]);
                    $promoted = $next['name'];
                }
            }

            // Delete the account itself (and everything that points at it).
            $this->purgeUser($userId);

            // A head with nobody left takes the empty household with them.
            if ($me['household_role'] === 'head' && $promoted === null && $householdId) {
                $this->purgeHousehold($householdId);
            }

            // Make sure the row really is gone before saying so.
            if ($this->userExists($userId)) {
                throw new PDOException('The account row was not deleted.');
            }

            $this->db->commit();
            $this->afterCommit();
            return ['ok' => true, 'promoted' => $promoted];

        } catch (PDOException $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            $this->afterRollback();
            $this->lastError = $e->getMessage();
            error_log('deleteAccount: ' . $e->getMessage());
            return false;
        }
    }

    /** True while this customer's `users` row still exists. */
    public function userExists($userId) {
        $stmt = $this->db->prepare("SELECT 1 FROM users WHERE id = :id AND role = 'customer' LIMIT 1");
        $stmt->execute([':id' => $userId]);
        return $stmt->fetchColumn() !== false;
    }

    /**
     * DELETE — the head deletes the WHOLE household: every person on it,
     * then the household itself. Needs the head's password.
     * Returns ['ok' => true, 'deleted' => n], 'wrong', 'nothead', 'notfound', or false.
     */
    public function deleteHousehold($userId, $password) {
        $stmt = $this->db->prepare(
            "SELECT id, household_id, household_role, password_hash
             FROM users WHERE id = :id AND role = 'customer' LIMIT 1"
        );
        $stmt->execute([':id' => $userId]);
        $me = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$me) return 'notfound';
        if (!password_verify($password, $me['password_hash'])) return 'wrong';
        if ($me['household_role'] !== 'head') return 'nothead';

        $householdId = (int) $me['household_id'];

        try {
            $this->db->beginTransaction();

            $ids = [(int) $userId];
            if ($householdId) {
                $stmt = $this->db->prepare(
                    "SELECT id FROM users WHERE household_id = :hid AND role = 'customer'"
                );
                $stmt->execute([':hid' => $householdId]);
                $ids = array_unique(array_merge($ids, array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN))));
            }

            foreach ($ids as $id) $this->purgeUser($id);
            if ($householdId) $this->purgeHousehold($householdId);

            // Make sure every row really is gone before saying so.
            foreach ($ids as $id) {
                if ($this->userExists($id)) throw new PDOException('User ' . $id . ' was not deleted.');
            }

            $this->db->commit();
            $this->afterCommit();
            return ['ok' => true, 'deleted' => count($ids)];
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            $this->afterRollback();
            $this->lastError = $e->getMessage();
            error_log('deleteHousehold: ' . $e->getMessage());
            return false;
        }
    }

    // ─── Forgot password ─────────────────────────────────────────────────────
    //
    //  No email is sent. The person proves who they are with what is already
    //  saved on their account:
    //    1. startReset()     email → a reset request at stage 'nic'
    //    2. checkResetNic()  NIC on file → stage 'pin' if two-factor is on
    //                        (users.two_factor_enabled = 1), otherwise 'ready'
    //    3. checkResetPin()  the security PIN set in Settings → stage 'ready'
    //    4. finishReset()    only a 'ready', unexpired request can set the password
    //
    //  Every step re-reads the row, so nobody can skip a step from the browser.

    /** The active customer an email belongs to, or false. */
    public function findForReset($email) {
        $stmt = $this->db->prepare(
            "SELECT id, name, email, account_status
             FROM users WHERE email = :email AND role = 'customer' LIMIT 1"
        );
        $stmt->execute([':email' => strtolower(trim($email))]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($row && $row['account_status'] === 'active') ? $row : false;
    }

    /**
     * Opens a new reset request for this person and cancels any older one.
     * Returns the request id, 'limit' (too many this hour) or false.
     */
    public function startReset($userId) {
        try {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM password_resets
                 WHERE user_id = :uid AND created_at > NOW() - INTERVAL 1 HOUR"
            );
            $stmt->execute([':uid' => $userId]);
            if ((int) $stmt->fetchColumn() >= self::RESET_PER_HOUR) return 'limit';

            // Only the newest request can be used.
            $this->db->prepare(
                "UPDATE password_resets SET expires_at = NOW() WHERE user_id = :uid AND expires_at > NOW()"
            )->execute([':uid' => $userId]);

            // Tidy rows nobody needs any more (kept a day for the hourly limit).
            $this->db->exec("DELETE FROM password_resets WHERE created_at < NOW() - INTERVAL 1 DAY");

            $stmt = $this->db->prepare(
                "INSERT INTO password_resets (user_id, stage, ip_address, expires_at)
                 VALUES (:uid, 'nic', :ip, NOW() + INTERVAL " . self::RESET_MINUTES . " MINUTE)"
            );
            $stmt->execute([':uid' => $userId, ':ip' => $_SERVER['REMOTE_ADDR'] ?? null]);
            return (int) $this->db->lastInsertId();

        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            error_log('startReset: ' . $e->getMessage());
            return false;
        }
    }

    /** The reset row if it is still usable and at the expected stage, else false. */
    private function activeReset($resetId, $userId, $stage) {
        $stmt = $this->db->prepare(
            "SELECT r.*, u.two_factor_enabled, u.two_factor_pin_hash, u.nic, u.password_hash
             FROM password_resets r
             JOIN users u ON u.id = r.user_id
             WHERE r.id = :rid AND r.user_id = :uid AND r.stage = :stage
               AND r.expires_at > NOW() AND u.account_status = 'active'
             LIMIT 1"
        );
        $stmt->execute([':rid' => $resetId, ':uid' => $userId, ':stage' => $stage]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /** One more wrong guess. Returns guesses left (0 = the request is now cancelled). */
    private function failResetAttempt($resetId) {
        $this->db->prepare(
            "UPDATE password_resets
             SET attempts = attempts + 1,
                 expires_at = IF(attempts >= :max, NOW(), expires_at)
             WHERE id = :rid"
        )->execute([':rid' => $resetId, ':max' => self::RESET_MAX_ATTEMPTS]);

        $stmt = $this->db->prepare("SELECT attempts FROM password_resets WHERE id = :rid");
        $stmt->execute([':rid' => $resetId]);
        return max(0, self::RESET_MAX_ATTEMPTS - (int) $stmt->fetchColumn());
    }

    /** True when the two-factor column is on AND a security PIN is saved. */
    private function twoFactorOn($row) {
        return !empty($row['two_factor_enabled']) && !empty($row['two_factor_pin_hash']);
    }

    /** NIC in one form for comparing: no spaces, upper case (…V / …X). */
    private static function cleanNic($nic) {
        return strtoupper(preg_replace('/\s+/', '', (string) $nic));
    }

    /**
     * Step 2: the NIC number on the account (every customer).
     * Returns 'pin' or 'ready' (the next step), ['wrong' => guesses left], or 'expired'.
     */
    public function checkResetNic($resetId, $userId, $nic) {
        $row = $this->activeReset($resetId, $userId, 'nic');
        if (!$row) return 'expired';

        // An account with no NIC saved can never match.
        $saved = self::cleanNic($row['nic']);
        if ($saved === '' || !hash_equals($saved, self::cleanNic($nic))) {
            $left = $this->failResetAttempt($resetId);
            return $left > 0 ? ['wrong' => $left] : 'expired';
        }

        // Linked to users.two_factor_enabled: with 2FA on, the NIC alone is
        // not enough — the security PIN is asked for as well.
        $next = $this->twoFactorOn($row) ? 'pin' : 'ready';
        $this->db->prepare("UPDATE password_resets SET stage = :s, attempts = 0 WHERE id = :rid")
                 ->execute([':s' => $next, ':rid' => $resetId]);
        return $next;
    }

    /**
     * Step 3 (two-factor accounts only): the 6-digit security PIN.
     * Returns 'ready', ['wrong' => guesses left], or 'expired'.
     */
    public function checkResetPin($resetId, $userId, $pin) {
        $row = $this->activeReset($resetId, $userId, 'pin');
        if (!$row) return 'expired';

        if (!password_verify((string) $pin, (string) $row['two_factor_pin_hash'])) {
            $left = $this->failResetAttempt($resetId);
            return $left > 0 ? ['wrong' => $left] : 'expired';
        }

        $this->db->prepare("UPDATE password_resets SET stage = 'ready', attempts = 0 WHERE id = :rid")
                 ->execute([':rid' => $resetId]);
        return 'ready';
    }

    /**
     * Step 4: saves the new password. Only works once every check has passed.
     * Returns true, 'same' (new = old password), 'expired', or false.
     */
    public function finishReset($resetId, $userId, $newPassword) {
        $row = $this->activeReset($resetId, $userId, 'ready');
        if (!$row) return 'expired';
        if (password_verify($newPassword, $row['password_hash'])) return 'same';

        try {
            $this->db->beginTransaction();
            $this->db->prepare("UPDATE users SET password_hash = :hash WHERE id = :uid")
                     ->execute([':hash' => password_hash($newPassword, PASSWORD_DEFAULT), ':uid' => $userId]);
            // Every open request for this person is used up (rows are kept, not
            // deleted, so the hourly limit still counts them).
            $this->db->prepare("UPDATE password_resets SET expires_at = NOW() WHERE user_id = :uid AND expires_at > NOW()")
                     ->execute([':uid' => $userId]);
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            $this->lastError = $e->getMessage();
            error_log('finishReset: ' . $e->getMessage());
            return false;
        }
    }

    // ─── Two-factor authentication (users.two_factor_enabled + PIN) ─────────

    /** ['enabled' => bool, 'hasNic' => bool] for the signed-in person. */
    public function twoFactorStatus($userId) {
        $stmt = $this->db->prepare(
            "SELECT two_factor_enabled, two_factor_pin_hash, nic FROM users WHERE id = :id AND role = 'customer'"
        );
        $stmt->execute([':id' => $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return [
            'enabled' => $row ? $this->twoFactorOn($row) : false,
            'hasNic'  => $row ? !empty($row['nic']) : false,
        ];
    }

    /**
     * Turns 2FA on (saving the PIN), changes the PIN while it's on, or turns
     * it off (removing the PIN). The PIN is checked by the controller first.
     * Returns true, 'wrong' (password), 'nonic' (can't turn on without a NIC), or false.
     */
    public function setTwoFactor($userId, $on, $password, $pin = null) {
        $stmt = $this->db->prepare("SELECT password_hash, nic FROM users WHERE id = :id AND role = 'customer'");
        $stmt->execute([':id' => $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row || !password_verify($password, $row['password_hash'])) return 'wrong';
        if ($on && empty($row['nic'])) return 'nonic';

        try {
            $stmt = $this->db->prepare(
                "UPDATE users SET two_factor_enabled = :on, two_factor_pin_hash = :pin WHERE id = :id"
            );
            return $stmt->execute([
                ':on'  => $on ? 1 : 0,
                ':pin' => $on ? password_hash((string) $pin, PASSWORD_DEFAULT) : null,
                ':id'  => $userId,
            ]);
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            error_log('setTwoFactor: ' . $e->getMessage());
            return false;
        }
    }

    // ─── Login history ───────────────────────────────────────────────────────

    private function logLogin($userId, $status) {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO login_history (user_id, ip_address, device_info, login_status)
                 VALUES (:uid, :ip, :device, :status)"
            );
            $stmt->execute([
                ':uid'    => $userId,
                ':ip'     => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
                ':device' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
                ':status' => $status,
            ]);
        } catch (PDOException $e) {
            // Login must still work even if the history insert fails.
        }
    }
}
?>
