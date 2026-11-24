<?php
/**
 * IncomeModel
 * Manages user income records (primary and secondary sources),
 * monthly aggregation, and synchronization with user_profiles.
 * Tables used: incomes, user_profiles, users, transactions
 */
class IncomeModel {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    /**
     * Save or update an income record for a user.
     *
     * @param int $user_id
     * @param string $source (e.g., 'Salary', 'Primary Income', 'Freelance')
     * @param float $amount
     * @param int|null $month (1-12, defaults to current month)
     * @param int|null $year (defaults to current year)
     * @return bool
     */
    public function saveIncome($user_id, $source = 'Primary Income', $amount = 0.00, $month = null, $year = null) {
        $user_id = (int)$user_id;
        $amount = max(0.00, (float)$amount);
        $source = !empty($source) ? trim($source) : 'Primary Income';
        $month = !empty($month) ? (int)$month : (int)date('m');
        $year = !empty($year) ? (int)$year : (int)date('Y');

        if ($user_id <= 0) {
            return false;
        }

        // 1. Insert or update in incomes table
        $query = "
            INSERT INTO incomes (user_id, source, amount, month, year, created_at)
            VALUES (:user_id, :source, :amount, :month, :year, NOW())
            ON DUPLICATE KEY UPDATE amount = :amount_update, updated_at = NOW()
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':source', $source, PDO::PARAM_STR);
        $stmt->bindValue(':amount', $amount);
        $stmt->bindValue(':amount_update', $amount);
        $stmt->bindValue(':month', $month, PDO::PARAM_INT);
        $stmt->bindValue(':year', $year, PDO::PARAM_INT);
        $saved = $stmt->execute();

        // 2. Sync to user_profiles table (crucial for profile & household queries)
        try {
            $upStmt = $this->db->prepare("
                INSERT INTO user_profiles (user_id, monthly_income)
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE monthly_income = ?
            ");
            $upStmt->execute([$user_id, $amount, $amount]);
        } catch (Throwable $e) {
            error_log('IncomeModel user_profiles sync error: ' . $e->getMessage());
        }

        return $saved;
    }

    /**
     * Get the total monthly income for a user (and their household).
     *
     * @param int $user_id
     * @param int|null $month
     * @param int|null $year
     * @return float
     */
    public function getTotalMonthlyIncome($user_id, $month = null, $year = null) {
        $user_id = (int)$user_id;
        if ($user_id <= 0) return 0.00;

        $month = !empty($month) ? (int)$month : (int)date('m');
        $year = !empty($year) ? (int)$year : (int)date('Y');

        // 1. Query incomes table for this user
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(amount), 0.00) 
            FROM incomes 
            WHERE user_id = :user_id AND month = :month AND year = :year
        ");
        $stmt->execute([':user_id' => $user_id, ':month' => $month, ':year' => $year]);
        $total = (float)$stmt->fetchColumn();

        // 2. Check user's household to aggregate family member incomes
        $hStmt = $this->db->prepare("SELECT household_id FROM users WHERE id = ? LIMIT 1");
        $hStmt->execute([$user_id]);
        $householdId = $hStmt->fetchColumn();

        if ($householdId) {
            // Aggregate family members from incomes table
            $famStmt = $this->db->prepare("
                SELECT COALESCE(SUM(i.amount), 0.00)
                FROM incomes i
                JOIN users u ON u.id = i.user_id
                WHERE u.household_id = :household_id AND u.id != :user_id 
                  AND i.month = :month AND i.year = :year
            ");
            $famStmt->execute([
                ':household_id' => $householdId,
                ':user_id'      => $user_id,
                ':month'        => $month,
                ':year'         => $year
            ]);
            $famTotal = (float)$famStmt->fetchColumn();
            $total += $famTotal;

            // If incomes table had no records, check user_profiles
            if ($total <= 0.00) {
                $profStmt = $this->db->prepare("
                    SELECT COALESCE(SUM(p.monthly_income), 0.00)
                    FROM users u
                    JOIN user_profiles p ON p.user_id = u.id
                    WHERE u.household_id = ?
                ");
                $profStmt->execute([$householdId]);
                $total = (float)$profStmt->fetchColumn();
            }

            // Also include additional incomes recorded in transactions
            $txStmt = $this->db->prepare("
                SELECT COALESCE(SUM(amount), 0.00) 
                FROM transactions 
                WHERE household_id = ? AND type = 'income'
                  AND MONTH(transaction_date) = ? AND YEAR(transaction_date) = ?
            ");
            $txStmt->execute([$householdId, $month, $year]);
            $total += (float)$txStmt->fetchColumn();
        } else {
            // If user has no household, check user_profiles as fallback
            if ($total <= 0.00) {
                $profStmt = $this->db->prepare("SELECT COALESCE(monthly_income, 0.00) FROM user_profiles WHERE user_id = ? LIMIT 1");
                $profStmt->execute([$user_id]);
                $total = (float)$profStmt->fetchColumn();
            }
        }

        return round($total, 2);
    }
}
?>
