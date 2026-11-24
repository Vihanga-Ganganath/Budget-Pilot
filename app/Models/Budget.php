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
        if (!$householdId) {
            return [];
        }

        $stmt = $this->db->prepare(
            "SELECT u.id, u.name, u.household_role, 
                    COALESCE(
                        (SELECT i.amount FROM incomes i WHERE i.user_id = u.id AND i.month = MONTH(CURDATE()) AND i.year = YEAR(CURDATE()) LIMIT 1),
                        p.monthly_income, 
                        0.00
                    ) AS monthly_income
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
        if (!$householdId) return;

        // 1. Ensure user_profiles row exists and is updated
        $stmt = $this->db->prepare(
            "INSERT INTO user_profiles (user_id, monthly_income)
             VALUES (?, ?)
             ON DUPLICATE KEY UPDATE monthly_income = ?"
        );
        $stmt->execute([$userId, $amount, $amount]);

        // 2. Also keep incomes table synchronized for this month & year
        try {
            $month = (int)date('m');
            $year = (int)date('Y');
            $incStmt = $this->db->prepare(
                "INSERT INTO incomes (user_id, source, amount, month, year, created_at)
                 VALUES (?, 'Primary Income', ?, ?, ?, NOW())
                 ON DUPLICATE KEY UPDATE amount = VALUES(amount), updated_at = NOW()"
            );
            $incStmt->execute([$userId, $amount, $month, $year]);
        } catch (Throwable $e) {
            // Silently ignore if table not accessible
        }
    }

    // 3: additional income for this month (saved in transactions as type 'income')
    public function getAdditionalIncomes($householdId) {
        if (!$householdId) {
            return [];
        }

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
        if (!$householdId) {
            return [
                'name' => 'Monthly Savings Goal',
                'target_amount' => 0.0,
                'current_amount' => 0.0,
                'status' => 'active'
            ];
        }

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
        if (!$householdId) {
            return;
        }

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
        if (!$householdId) {
            return [];
        }

        $stmt = $this->db->prepare(
            "SELECT category_id, amount FROM budgets
             WHERE household_id = ? AND budget_month = DATE_FORMAT(CURDATE(), '%Y-%m-01')"
        );
        $stmt->execute([$householdId]);
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    // Saved full budget rows including due date, penalty rate, and status flags
    public function getDetailedPlan($householdId) {
        if (!$householdId) {
            return [];
        }

        $stmt = $this->db->prepare(
            "SELECT category_id, amount, due_date, penalty_rate, penalty_applied, is_paid 
             FROM budgets
             WHERE household_id = ? AND budget_month = DATE_FORMAT(CURDATE(), '%Y-%m-01')"
        );
        $stmt->execute([$householdId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = [];
        foreach ($rows as $r) {
            $result[$r['category_id']] = $r;
        }
        return $result;
    }

    // Saves this month's limits with optional due dates and penalty rates
    public function savePlan($householdId, $limits, $dueDates = [], $penaltyRates = []) {
        if (!$householdId) {
            return;
        }

        $stmt = $this->db->prepare(
            "INSERT INTO budgets (household_id, category_id, amount, budget_month, due_date, penalty_rate)
             VALUES (?, ?, ?, DATE_FORMAT(CURDATE(), '%Y-%m-01'), ?, ?)
             ON DUPLICATE KEY UPDATE 
                amount = VALUES(amount),
                due_date = VALUES(due_date),
                penalty_rate = VALUES(penalty_rate)"
        );
        foreach ($limits as $categoryId => $amount) {
            $dueDate = !empty($dueDates[$categoryId]) ? $dueDates[$categoryId] : null;
            $penaltyRate = isset($penaltyRates[$categoryId]) ? (float)$penaltyRates[$categoryId] : 0.00;
            $stmt->execute([$householdId, $categoryId, $amount, $dueDate, $penaltyRate]);
        }
    }

    /**
     * Fetch allocated Grocery Budget for this month for a given household.
     * Checks custom saved limits first; falls back to suggested % or default.
     *
     * @param int|null $householdId
     * @return float
     */
    public function getGroceryBudget($householdId) {
        if (!$householdId) {
            return 150.00;
        }

        // 1. Check if a budget limit was set specifically for Grocery for this month
        $stmt = $this->db->prepare(
            "SELECT b.amount 
             FROM budgets b
             JOIN transaction_categories tc ON b.category_id = tc.id
             WHERE b.household_id = ? 
               AND tc.name LIKE '%Grocer%'
               AND b.budget_month = DATE_FORMAT(CURDATE(), '%Y-%m-01')
             LIMIT 1"
        );
        $stmt->execute([$householdId]);
        $amount = $stmt->fetchColumn();

        if ($amount !== false && (float)$amount > 0) {
            return round((float)$amount, 2);
        }

        // 2. Fall back to suggested plan: (total income - savings goal) * 30%
        $incomes = $this->getIncomes($householdId);
        $totalIncome = 0.0;
        foreach ($incomes as $inc) {
            $totalIncome += (float)($inc['monthly_income'] ?? 0);
        }

        $extras = $this->getAdditionalIncomes($householdId);
        foreach ($extras as $ext) {
            $totalIncome += (float)($ext['amount'] ?? 0);
        }

        $savingsGoal = $this->getSavingsGoal($householdId);
        $goalAmount = (float)($savingsGoal['target_amount'] ?? 0);

        $available = max(0, $totalIncome - $goalAmount);

        $catStmt = $this->db->query("SELECT suggested_percent FROM transaction_categories WHERE name LIKE '%Grocer%' LIMIT 1");
        $percent = (float)($catStmt ? $catStmt->fetchColumn() : 30);
        if ($percent <= 0) $percent = 30;

        $suggestedGrocery = round($available * ($percent / 100), 2);

        return $suggestedGrocery > 0 ? $suggestedGrocery : 150.00;
    }

    /**
     * Helper to get grocery budget directly by user ID.
     *
     * @param int $userId
     * @return float
     */
    public function getGroceryBudgetByUserId($userId) {
        $stmt = $this->db->prepare("SELECT household_id FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $householdId = $stmt->fetchColumn();
        return $this->getGroceryBudget($householdId);
    }
}
?>
