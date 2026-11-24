<?php
/**
 * ExpenseModel
 * Handles Module: Expenses (Tracking, Categories, and Totals).
 * Interconnects with BudgetModel (to calculate Remaining Budget) and Live Cart checkout.
 * Tables used: expenses, transactions, transaction_categories, users
 */
require_once __DIR__ . '/Expense.php';

class ExpenseModel extends Expense {
    private $db;

    public function __construct() {
        parent::__construct();
        $this->db = (new Database())->getConnection();
    }

    /**
     * Resolves household_id for a given user if not provided.
     *
     * @param int $userId
     * @param int|null $householdId
     * @return int|null
     */
    private function resolveHouseholdId($userId, $householdId = null) {
        if (!empty($householdId)) {
            return (int)$householdId;
        }

        if (!empty($userId)) {
            $stmt = $this->db->prepare("SELECT household_id FROM users WHERE id = ? LIMIT 1");
            $stmt->execute([(int)$userId]);
            $hId = $stmt->fetchColumn();
            return $hId ? (int)$hId : null;
        }

        return null;
    }

    /**
     * Add a new expense record.
     * Inserts into the expenses table and keeps transactions table synchronized.
     *
     * @param int $userId
     * @param string|int $category Category name or category ID
     * @param float $amount
     * @param string|null $date Date in 'YYYY-MM-DD' format (defaults to CURDATE())
     * @param string $description
     * @param int|null $householdId
     * @return bool
     */
    public function addExpense($userId, $category, $amount = null, $date = null, $description = '', $householdId = null, $customName = null) {
        // Compatibility: support parent Expense signature if called as addExpense($householdId, $userId, $categoryId, $amount, $date, $description)
        if (is_numeric($userId) && is_numeric($category) && is_numeric($amount) && $householdId !== null) {
            $hIdArg = (int)$userId;
            $uIdArg = (int)$category;
            $catIdArg = (int)$amount;
            $amtArg = (float)$date;
            $dtArg = (string)$description;
            $descArg = (string)$householdId;
            $cNameArg = !empty($customName) ? (string)$customName : null;

            return $this->saveExpenseRecord($uIdArg, $catIdArg, $amtArg, $dtArg, $descArg, $hIdArg, $cNameArg);
        }

        return $this->saveExpenseRecord((int)$userId, $category, (float)$amount, $date, $description, $householdId, $customName);
    }

    /**
     * Internal helper to insert expense into expenses table and transactions table.
     */
    private function saveExpenseRecord($userId, $category, $amount, $date = null, $description = '', $householdId = null, $customName = null) {
        $userId = (int)$userId;
        $amount = max(0.00, (float)$amount);
        $date = !empty($date) && strtotime($date) ? date('Y-m-d', strtotime($date)) : date('Y-m-d');
        $householdId = $this->resolveHouseholdId($userId, $householdId);
        $description = trim((string)$description);
        $cleanCustomName = !empty($customName) ? trim((string)$customName) : null;

        $categoryId = null;
        $categoryName = 'General';

        if (is_numeric($category)) {
            $categoryId = (int)$category;
            $stmt = $this->db->prepare("SELECT name FROM transaction_categories WHERE id = ? LIMIT 1");
            $stmt->execute([$categoryId]);
            $categoryName = $stmt->fetchColumn() ?: 'Category #' . $categoryId;
        } else {
            $categoryName = !empty($category) ? trim((string)$category) : 'General';
            $stmt = $this->db->prepare("SELECT id FROM transaction_categories WHERE name LIKE ? LIMIT 1");
            $stmt->execute(['%' . $categoryName . '%']);
            $categoryId = $stmt->fetchColumn() ?: null;

            if (!$categoryId) {
                // Default to first active expense category
                $defStmt = $this->db->query("SELECT id FROM transaction_categories WHERE type = 'expense' LIMIT 1");
                $categoryId = $defStmt ? (int)$defStmt->fetchColumn() : 1;
            }
        }

        // 1. Insert into expenses table
        $query = "
            INSERT INTO expenses (
                household_id, user_id, category_id, category, custom_name, amount, expense_date, description, created_at
            ) VALUES (
                :household_id, :user_id, :category_id, :category, :custom_name, :amount, :expense_date, :description, NOW()
            )
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':household_id', $householdId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':category', $categoryName, PDO::PARAM_STR);
        $stmt->bindValue(':custom_name', $cleanCustomName, $cleanCustomName !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':amount', $amount);
        $stmt->bindValue(':expense_date', $date, PDO::PARAM_STR);
        $stmt->bindValue(':description', $description, PDO::PARAM_STR);
        $saved = $stmt->execute();

        // 2. Also record in transactions table for backward compatibility with customer dashboard
        try {
            $txDesc = !empty($cleanCustomName) ? "{$cleanCustomName} - {$description}" : $description;
            if (empty($description) && !empty($cleanCustomName)) {
                $txDesc = $cleanCustomName;
            }
            $txStmt = $this->db->prepare("
                INSERT INTO transactions (
                    household_id, user_id, category_id, type, amount, transaction_date, description
                ) VALUES (
                    ?, ?, ?, 'expense', ?, ?, ?
                )
            ");
            $txStmt->execute([$householdId, $userId, $categoryId, $amount, $date, $txDesc]);
        } catch (Throwable $e) {
            error_log('ExpenseModel mirror to transactions notice: ' . $e->getMessage());
        }

        return $saved;
    }

    /**
     * Calculate the total SUM of expenses for a specific category and month.
     *
     * @param int $userId
     * @param string|int $category Category name or category ID
     * @param string|null $month Month format 'YYYY-MM' (defaults to current month)
     * @param int|null $householdId
     * @return float Total spent
     */
    public function getTotalExpensesByCategory($userId, $category, $month = null, $householdId = null) {
        $userId = (int)$userId;
        $householdId = $this->resolveHouseholdId($userId, $householdId);

        if (empty($month)) {
            $month = date('Y-m');
        } else {
            $month = substr($month, 0, 7); // Ensure YYYY-MM
        }

        $categoryId = is_numeric($category) ? (int)$category : null;
        $categoryName = is_numeric($category) ? '' : trim((string)$category);

        // 1. Query expenses table
        $query = "
            SELECT COALESCE(SUM(amount), 0.00) AS total 
            FROM expenses 
            WHERE (
                user_id = :user_id 
                OR (:household_id IS NOT NULL AND household_id = :household_id)
            )
            AND (
                (:category_id IS NOT NULL AND category_id = :category_id)
                OR (:category_name != '' AND (category = :category_name OR category LIKE :category_like))
            )
            AND DATE_FORMAT(expense_date, '%Y-%m') = :month
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':household_id', $householdId, PDO::PARAM_INT);
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':category_name', $categoryName, PDO::PARAM_STR);
        $stmt->bindValue(':category_like', '%' . $categoryName . '%', PDO::PARAM_STR);
        $stmt->bindValue(':month', $month, PDO::PARAM_STR);
        $stmt->execute();

        $total = (float)$stmt->fetchColumn();

        // 2. If expenses table total is 0, check transactions table as fallback
        if ($total <= 0.00) {
            $txQuery = "
                SELECT COALESCE(SUM(t.amount), 0.00) AS total
                FROM transactions t
                LEFT JOIN transaction_categories tc ON t.category_id = tc.id
                WHERE (t.user_id = :user_id OR (:household_id IS NOT NULL AND t.household_id = :household_id))
                  AND t.type = 'expense'
                  AND DATE_FORMAT(t.transaction_date, '%Y-%m') = :month
                  AND (
                      (:category_id IS NOT NULL AND t.category_id = :category_id)
                      OR (:category_name != '' AND (tc.name LIKE :category_like OR t.description LIKE :category_like))
                  )
            ";
            $txStmt = $this->db->prepare($txQuery);
            $txStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $txStmt->bindValue(':household_id', $householdId, PDO::PARAM_INT);
            $txStmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
            $txStmt->bindValue(':category_name', $categoryName, PDO::PARAM_STR);
            $txStmt->bindValue(':category_like', '%' . $categoryName . '%', PDO::PARAM_STR);
            $txStmt->bindValue(':month', $month, PDO::PARAM_STR);
            $txStmt->execute();

            $total = (float)$txStmt->fetchColumn();
        }

        return round($total, 2);
    }
}
?>
