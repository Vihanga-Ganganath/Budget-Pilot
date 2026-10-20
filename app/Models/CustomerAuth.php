<?php
/*
 * CustomerAuth model - customer LOGIN and CREATE ACCOUNT.
 * Tables used: households, users, user_profiles, savings_goals, login_history, password_resets
 */
class CustomerAuth {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    // ===================== LOGIN =====================

    // Find a customer by email. Returns the users row, or false.
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? AND role = 'customer'");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Checks email + password.
    // Returns the user row when it is correct, otherwise an error message (string).
    public function login($email, $password) {
        $user = $this->findByEmail($email);

        if (!$user) {
            return 'Email or password is incorrect.';
        }
        if (!password_verify($password, $user['password_hash'])) {
            $this->saveLoginHistory($user['id'], 'failed');
            return 'Email or password is incorrect.';
        }
        if ($user['account_status'] != 'active') {
            $this->saveLoginHistory($user['id'], 'locked_out');
            return 'Your account is ' . $user['account_status'] . '. Please contact support.';
        }

        $stmt = $this->db->prepare("UPDATE users SET last_login_at = NOW() WHERE id = ?");
        $stmt->execute([$user['id']]);
        $this->saveLoginHistory($user['id'], 'success');

        return $user;
    }

    // Adds one row to login_history (success / failed / locked_out).
    private function saveLoginHistory($userId, $status) {
        $stmt = $this->db->prepare(
            "INSERT INTO login_history (user_id, ip_address, device_info, login_status) VALUES (?, ?, ?, ?)"
        );
        $ip     = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $device = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);
        $stmt->execute([$userId, $ip, $device, $status]);
    }

    // ===================== CREATE ACCOUNT =====================

    public function emailExists($email) {
        return $this->findByEmail($email) ? true : false;
    }

    // Creates a household and its main account holder (head).
    // $owner = name, email, password, nic, gender, age, income, savings, photo
    // Returns the new household id, or false if something failed.
    public function register($owner) {
        try {
            $this->db->beginTransaction();

            // 1. households - reuse the lowest free number
            $householdId = $this->nextHouseholdId();
            $stmt = $this->db->prepare("INSERT INTO households (id, name) VALUES (?, ?)");
            $stmt->execute([$householdId, $owner['name'] . "'s Household"]);

            // 2. users + user_profiles - the main account holder
            $ownerId = $this->addUser($householdId, 'head', $owner);
            $stmt = $this->db->prepare(
                "INSERT INTO user_profiles (user_id, profile_picture_url, monthly_income, financial_goals)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$ownerId, $owner['photo'], $owner['income'], $owner['savings']]);

            // 3. savings_goals - the savings goal entered at sign-up
            $stmt = $this->db->prepare("INSERT INTO savings_goals (household_id, name, target_amount) VALUES (?, 'Monthly Savings Goal', ?)");
            $stmt->execute([$householdId, $owner['savings']]);

            // 4. remember who created the household
            $stmt = $this->db->prepare("UPDATE households SET created_by_user_id = ? WHERE id = ?");
            $stmt->execute([$ownerId, $householdId]);

            $this->db->commit();
            return $householdId;

        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // Inserts one customer into users. Returns the new user id.
    private function addUser($householdId, $householdRole, $p) {
        $stmt = $this->db->prepare(
            "INSERT INTO users (household_id, role, household_role, name, email, password_hash, nic, age, gender)
             VALUES (?, 'customer', ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $householdId,
            $householdRole,
            $p['name'],
            $p['email'],
            password_hash($p['password'], PASSWORD_DEFAULT),
            $p['nic'] ?? null,
            $p['age'] ?? null,
            $p['gender'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    // Lowest household number that is not used (1 exists, 2 deleted -> 2).
    private function nextHouseholdId() {
        $sql = "SELECT MIN(h.id + 1) FROM (SELECT 0 AS id UNION SELECT id FROM households) h
                WHERE h.id + 1 NOT IN (SELECT id FROM households)";
        return (int) $this->db->query($sql)->fetchColumn();
    }

    // ===================== FORGOT PASSWORD (password_resets) =====================

    // True when this account had 5 or more wrong PINs in the last 15 minutes.
    public function isResetBlocked($userId) {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM password_resets WHERE user_id = ? AND created_at > NOW() - INTERVAL 15 MINUTE"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchColumn() >= 5;
    }

    // Saves one wrong PIN (one row = one wrong try).
    public function addWrongPin($userId) {
        $stmt = $this->db->prepare(
            "INSERT INTO password_resets (user_id, stage, attempts, ip_address, expires_at) VALUES (?, 'pin', 1, ?, NOW())"
        );
        $stmt->execute([$userId, $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0']);
    }
}
?>
