<?php
/*
 * Budget model - income sources, savings goal and the monthly plan.
 * Tables used: transaction_categories, user_profiles, users, transactions, budgets, savings_goals
 */
class Budget {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    // ===================== CATEGORIES =====================

    // The plan categories with their suggested % (transaction_categories.suggested_percent).
    public function getCategories() {
        return $this->db->query(
            "SELECT id, name, suggested_percent FROM transaction_categories
             WHERE household_id IS NULL AND type = 'expense' AND is_active = 1
             ORDER BY id"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    // Id of a global category (household_id NULL), e.g. 'Additional Income'. Inserts it if missing.
    private function categoryId($name, $type) {
        $stmt = $this->db->prepare(
            "SELECT id FROM transaction_categories WHERE household_id IS NULL AND name = ? AND type = ?"
        );
        $stmt->execute([$name, $type]);
        $id = $stmt->fetchColumn();
        if ($id) return $id;

        $stmt = $this->db->prepare("INSERT INTO transaction_categories (household_id, name, type) VALUES (NULL, ?, ?)");
        $stmt->execute([$name, $type]);
        return $this->db->lastInsertId();
    }

    // ===================== INCOME =====================

    // 1 + 2: monthly income of the account holder and of each family member
    public function getIncomes($householdId) {
        $stmt = $this->db->prepare(
            "SELECT u.id, u.name, u.household_role, COALESCE(p.monthly_income, 0) AS monthly_income
             FROM users u
             LEFT JOIN user_profiles p ON p.user_id = u.id
             WHERE u.household_id = ?
             ORDER BY u.household_role = 'head' DESC, u.name"
        );
        $stmt->execute([$householdId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Changes one person's monthly income (only people in this household).
    public function updateIncome($userId, $householdId, $amount) {
        $stmt = $this->db->prepare(
            "UPDATE user_profiles p JOIN users u ON u.id = p.user_id
             SET p.monthly_income = ?
             WHERE u.id = ? AND u.household_id = ?"
        );
        $stmt->execute([$amount, $userId, $householdId]);
    }

    // 3: additional income for this month (saved in transactions as type 'income')
    public function getAdditionalIncomes($householdId) {
        $stmt = $this->db->prepare(
            "SELECT id, description, amount FROM transactions
             WHERE household_id = ? AND type = 'income'
             AND DATE_FORMAT(transaction_date, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')
             ORDER BY id"
        );
        $stmt->execute([$householdId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addAdditionalIncome($householdId, $userId, $name, $amount) {
        $stmt = $this->db->prepare(
            "INSERT INTO transactions (household_id, user_id, category_id, type, amount, transaction_date, description)
             VALUES (?, ?, ?, 'income', ?, CURDATE(), ?)"
        );
        $stmt->execute([$householdId, $userId, $this->categoryId('Additional Income', 'income'), $amount, $name]);
    }

    public function removeAdditionalIncome($id, $householdId) {
        $stmt = $this->db->prepare("DELETE FROM transactions WHERE id = ? AND household_id = ? AND type = 'income'");
        $stmt->execute([$id, $householdId]);
    }

    // ===================== SAVINGS GOAL =====================

    // The household's savings goal row from savings_goals (created at sign-up).
    public function getSavingsGoal($householdId) {
        $stmt = $this->db->prepare(
            "SELECT name, target_amount, current_amount, status FROM savings_goals
             WHERE household_id = ? AND name = 'Monthly Savings Goal'"
        );
        $stmt->execute([$householdId]);
        $goal = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$goal) {
            // accounts made before this change: copy the sign-up value from user_profiles once
            $stmt = $this->db->prepare(
                "SELECT p.financial_goals FROM users u JOIN user_profiles p ON p.user_id = u.id
                 WHERE u.household_id = ? AND u.household_role = 'head'"
            );
            $stmt->execute([$householdId]);
            $this->setSavingsGoal($householdId, (float) $stmt->fetchColumn());
            return $this->getSavingsGoal($householdId);
        }
        return $goal;
    }

    // Saves the savings goal amount (used when the account holder changes it in Settings).
    public function setSavingsGoal($householdId, $amount) {
        $stmt = $this->db->prepare("SELECT id FROM savings_goals WHERE household_id = ? AND name = 'Monthly Savings Goal'");
        $stmt->execute([$householdId]);
        $id = $stmt->fetchColumn();

        if ($id) {
            $stmt = $this->db->prepare("UPDATE savings_goals SET target_amount = ? WHERE id = ?");
            $stmt->execute([$amount, $id]);
        } else {
            $stmt = $this->db->prepare("INSERT INTO savings_goals (household_id, name, target_amount) VALUES (?, 'Monthly Savings Goal', ?)");
            $stmt->execute([$householdId, $amount]);
        }
    }

    // ===================== MONTHLY PLAN =====================

    // Saved limits for this month as [category_id => amount]
    public function getPlan($householdId) {
        $stmt = $this->db->prepare(
            "SELECT category_id, amount FROM budgets
             WHERE household_id = ? AND budget_month = DATE_FORMAT(CURDATE(), '%Y-%m-01')"
        );
        $stmt->execute([$householdId]);
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    // Saves this month's limits ($limits = [category_id => amount]). Updates the row if it already exists.
    public function savePlan($householdId, $limits) {
        $stmt = $this->db->prepare(
            "INSERT INTO budgets (household_id, category_id, amount, budget_month)
             VALUES (?, ?, ?, DATE_FORMAT(CURDATE(), '%Y-%m-01'))
             ON DUPLICATE KEY UPDATE amount = VALUES(amount)"
        );
        foreach ($limits as $categoryId => $amount) {
            $stmt->execute([$householdId, $categoryId, $amount]);
        }
    }
}
?>
