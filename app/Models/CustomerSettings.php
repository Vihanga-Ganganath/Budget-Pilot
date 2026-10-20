<?php
/*
 * CustomerSettings model - the logged-in customer's own profile and password.
 * Tables used: users, user_profiles
 */
class CustomerSettings {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    // All the data entered at sign-up (users + user_profiles).
    public function getProfile($userId) {
        $stmt = $this->db->prepare(
            "SELECT u.id, u.name, u.email, u.nic, u.age, u.gender, u.household_role, u.password_hash, u.two_factor_enabled,
                    p.monthly_income, p.financial_goals, p.preferred_currency, p.profile_picture_url
             FROM users u
             LEFT JOIN user_profiles p ON p.user_id = u.id
             WHERE u.id = ?"
        );
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Saves the edited profile. $p = name, email, nic, age, gender, income, savings, currency, photo
    public function updateProfile($userId, $p) {
        $stmt = $this->db->prepare("UPDATE users SET name = ?, email = ?, nic = ?, age = ?, gender = ? WHERE id = ?");
        $stmt->execute([$p['name'], $p['email'], $p['nic'], $p['age'], $p['gender'], $userId]);

        // user_profiles: insert the row if it is missing, otherwise update it
        $stmt = $this->db->prepare(
            "INSERT INTO user_profiles (user_id, monthly_income, financial_goals, preferred_currency)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE monthly_income = VALUES(monthly_income),
                 financial_goals = VALUES(financial_goals), preferred_currency = VALUES(preferred_currency)"
        );
        $stmt->execute([$userId, $p['income'], $p['savings'], $p['currency']]);

        // new photo only when one was uploaded
        if ($p['photo']) {
            $stmt = $this->db->prepare("UPDATE user_profiles SET profile_picture_url = ? WHERE user_id = ?");
            $stmt->execute([$p['photo'], $userId]);
        }
    }

    // Saves a new password (the controller checks the current one first).
    public function updatePassword($userId, $newPassword) {
        $stmt = $this->db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $stmt->execute([password_hash($newPassword, PASSWORD_DEFAULT), $userId]);
    }

    // ===================== TWO-FACTOR (security PIN) =====================

    // Turns two-factor on and saves the 6-digit PIN (hashed, like a password).
    public function enableTwoFactor($userId, $pin) {
        $stmt = $this->db->prepare("UPDATE users SET two_factor_enabled = 1, two_factor_pin_hash = ? WHERE id = ?");
        $stmt->execute([password_hash($pin, PASSWORD_DEFAULT), $userId]);
    }

    // Turns two-factor off and deletes the PIN.
    public function disableTwoFactor($userId) {
        $stmt = $this->db->prepare("UPDATE users SET two_factor_enabled = 0, two_factor_pin_hash = NULL WHERE id = ?");
        $stmt->execute([$userId]);
    }
}
?>
