<?php
/*
 * Household model - family members of the logged-in account holder.
 * Tables used: users, user_profiles, budgets, transactions
 */
class Household {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    // Everyone in the household (account holder first).
    public function getMembers($householdId) {
        $stmt = $this->db->prepare(
            "SELECT u.id, u.name, u.email, u.household_role, p.profile_picture_url
             FROM users u
             LEFT JOIN user_profiles p ON p.user_id = u.id
             WHERE u.household_id = ?
             ORDER BY u.household_role = 'head' DESC, u.name"
        );
        $stmt->execute([$householdId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // One family member (only a 'member' of this household). Returns the row or false.
    public function getMember($id, $householdId) {
        $stmt = $this->db->prepare(
            "SELECT id, name, email FROM users WHERE id = ? AND household_id = ? AND household_role = 'member'"
        );
        $stmt->execute([$id, $householdId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // True if another customer already uses this email ($exceptId = the user being edited).
    public function emailUsed($email, $exceptId = 0) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND role = 'customer' AND id <> ?");
        $stmt->execute([$email, $exceptId]);
        return $stmt->fetchColumn() > 0;
    }

    // Adds a family member under the account holder's household.
    public function addMember($householdId, $name, $email, $password) {
        $stmt = $this->db->prepare(
            "INSERT INTO users (household_id, role, household_role, name, email, password_hash)
             VALUES (?, 'customer', 'member', ?, ?, ?)"
        );
        $stmt->execute([$householdId, $name, $email, password_hash($password, PASSWORD_DEFAULT)]);

        $stmt = $this->db->prepare("INSERT INTO user_profiles (user_id) VALUES (?)");
        $stmt->execute([$this->db->lastInsertId()]);
    }

    // Updates a member's name and email (and password only if a new one is given).
    public function updateMember($id, $householdId, $name, $email, $password) {
        if ($password != '') {
            $stmt = $this->db->prepare(
                "UPDATE users SET name = ?, email = ?, password_hash = ?
                 WHERE id = ? AND household_id = ? AND household_role = 'member'"
            );
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $id, $householdId]);
        } else {
            $stmt = $this->db->prepare(
                "UPDATE users SET name = ?, email = ? WHERE id = ? AND household_id = ? AND household_role = 'member'"
            );
            $stmt->execute([$name, $email, $id, $householdId]);
        }
    }

    // Deletes a member (their profile and their expenses are deleted too - ON DELETE CASCADE).
    public function removeMember($id, $householdId) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ? AND household_id = ? AND household_role = 'member'");
        $stmt->execute([$id, $householdId]);
    }

    // This month's budget, money spent by everyone, and what is left.
    public function getSummary($householdId) {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(amount), 0) FROM budgets
             WHERE household_id = ? AND budget_month = DATE_FORMAT(CURDATE(), '%Y-%m-01')"
        );
        $stmt->execute([$householdId]);
        $budget = (float) $stmt->fetchColumn();

        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(amount), 0) FROM transactions
             WHERE household_id = ? AND type = 'expense'
             AND DATE_FORMAT(transaction_date, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')"
        );
        $stmt->execute([$householdId]);
        $spent = (float) $stmt->fetchColumn();

        return ['budget' => $budget, 'spent' => $spent, 'left' => $budget - $spent];
    }
}
?>
