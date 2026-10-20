<?php
/*
 * Expense model - Add Expense page and the Dashboard.
 * Tables used: transactions, transaction_categories, users
 */
class Expense {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    // Saves one expense (the person who adds it is recorded in user_id).
    public function addExpense($householdId, $userId, $categoryId, $amount, $date, $description) {
        $stmt = $this->db->prepare(
            "INSERT INTO transactions (household_id, user_id, category_id, type, amount, transaction_date, description)
             VALUES (?, ?, ?, 'expense', ?, ?, ?)"
        );
        $stmt->execute([$householdId, $userId, $categoryId, $amount, $date, $description]);
    }

    // This month's spending for every category (0 when nothing was spent).
    public function getSpendingByCategory($householdId) {
        $stmt = $this->db->prepare(
            "SELECT c.id, c.name, COALESCE(SUM(t.amount), 0) AS total
             FROM transaction_categories c
             LEFT JOIN transactions t ON t.category_id = c.id AND t.household_id = ? AND t.type = 'expense'
                  AND DATE_FORMAT(t.transaction_date, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')
             WHERE c.household_id IS NULL AND c.type = 'expense' AND c.is_active = 1
             GROUP BY c.id, c.name
             ORDER BY c.id"
        );
        $stmt->execute([$householdId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Every transaction of the household (expenses and income), newest first.
    public function getTransactions($householdId) {
        $stmt = $this->db->prepare(
            "SELECT t.type, t.amount, t.transaction_date, t.description, c.name AS category, u.name AS person
             FROM transactions t
             JOIN transaction_categories c ON c.id = t.category_id
             JOIN users u ON u.id = t.user_id
             WHERE t.household_id = ?
             ORDER BY t.transaction_date DESC, t.id DESC"
        );
        $stmt->execute([$householdId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
