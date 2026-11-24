<?php
/**
 * BudgetModel
 * Handles Module: Budgets (Monthly Budget Limits per Category, Income Tracking).
 * Interconnects with ExpenseModel, CartModel, and Customer views.
 * Tables used: budgets, incomes, user_profiles, transaction_categories, users, savings_goals
 */
require_once __DIR__ . '/Budget.php';

class BudgetModel extends Budget {
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
            if ($hId) {
                return (int)$hId;
            }

            // Fallback: assign to default household if not set
            $defaultHousehold = $this->db->query("SELECT id FROM households LIMIT 1")->fetchColumn();
            if ($defaultHousehold) {
                $up = $this->db->prepare("UPDATE users SET household_id = ? WHERE id = ?");
                $up->execute([$defaultHousehold, (int)$userId]);
                return (int)$defaultHousehold;
            }
        }

        return 1;
    }

    /**
     * Save or update an income record for a user.
     * Inserts into incomes table and synchronizes with user_profiles.
     *
     * @param int $user_id
     * @param string $source (e.g. 'Primary Income', 'Salary', 'Freelance')
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

        // 2. Sync to user_profiles table (ensures profile row exists and prevents $0.00 bug)
        try {
            $upStmt = $this->db->prepare("
                INSERT INTO user_profiles (user_id, monthly_income)
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE monthly_income = ?
            ");
            $upStmt->execute([$user_id, $amount, $amount]);
        } catch (Throwable $e) {
            error_log('BudgetModel user_profiles sync notice: ' . $e->getMessage());
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

    /**
     * Set or update a monthly budget limit for a specific category and month.
     * Supports ($user_id, $category, $amount, $month, $year) and ($user_id, $category, $amount, $monthString, $householdId).
     *
     * @param int $userId
     * @param string|int $category Category name or category ID
     * @param float $amount Allocated budget amount
     * @param int|string|null $month Month (1-12) or 'YYYY-MM' / 'YYYY-MM-DD'
     * @param int|null $year Year (e.g. 2026) or optional household ID
     * @return bool
     */
    public function setOrUpdateBudget($userId, $category, $amount, $month = null, $year = null, $dueDate = null, $penaltyRate = 0.00, $customName = null, $budgetId = null, $durationMonths = null) {
        $userId = (int)$userId;
        $amount = max(0.00, (float)$amount);
        $penaltyRate = max(0.00, (float)$penaltyRate);
        $cleanDueDate = (!empty($dueDate) && strtotime($dueDate)) ? date('Y-m-d', strtotime($dueDate)) : null;
        $cleanCustomName = !empty($customName) ? trim((string)$customName) : null;
        $cleanDurationMonths = (!empty($durationMonths) && (int)$durationMonths > 0) ? (int)$durationMonths : null;

        // Normalize month and year into 'YYYY-MM-01'
        if (is_numeric($year) && (int)$year > 1900) {
            $monthNum = !empty($month) ? (int)$month : (int)date('m');
            $budgetMonth = sprintf('%04d-%02d-01', (int)$year, $monthNum);
            $householdId = $this->resolveHouseholdId($userId, null);
        } elseif (is_string($month) && strpos($month, '-') !== false) {
            $budgetMonth = strlen($month) === 7 ? $month . '-01' : $month;
            $householdId = is_numeric($year) ? (int)$year : $this->resolveHouseholdId($userId, null);
        } else {
            $monthNum = !empty($month) ? (int)$month : (int)date('m');
            $yearNum = !empty($year) && (int)$year > 1900 ? (int)$year : (int)date('Y');
            $budgetMonth = sprintf('%04d-%02d-01', $yearNum, $monthNum);
            $householdId = $this->resolveHouseholdId($userId, null);
        }

        if (!$householdId) {
            $householdId = 1;
        }

        $categoryId = null;
        $categoryName = 'General';

        if (is_numeric($category)) {
            $categoryId = (int)$category;
            $stmt = $this->db->prepare("SELECT name FROM transaction_categories WHERE id = ? LIMIT 1");
            $stmt->execute([$categoryId]);
            $categoryName = $stmt->fetchColumn() ?: 'Category #' . $categoryId;
        } else {
            $categoryName = trim((string)$category);
            $stmt = $this->db->prepare("SELECT id FROM transaction_categories WHERE name LIKE ? LIMIT 1");
            $stmt->execute(['%' . $categoryName . '%']);
            $categoryId = $stmt->fetchColumn() ?: null;

            if (!$categoryId) {
                // Insert new category if missing
                $catIns = $this->db->prepare("INSERT INTO transaction_categories (name, type, is_active) VALUES (?, 'expense', 1)");
                $catIns->execute([$categoryName]);
                $categoryId = (int)$this->db->lastInsertId();
            }
        }

        // Enforce category-specific rules: only Loan supports penalty_rate & duration_months
        $isLoan = (strtolower(trim($categoryName)) === 'loan');
        if (!$isLoan) {
            $penaltyRate = 0.00;
            $cleanDurationMonths = null;
        }

        // 1. If budgetId is provided and exists, update directly by primary key
        if (!empty($budgetId) && (int)$budgetId > 0) {
            $upStmt = $this->db->prepare("
                UPDATE budgets 
                SET amount = :amount,
                    due_date = :due_date,
                    penalty_rate = :penalty_rate,
                    duration_months = :duration_months,
                    custom_name = :custom_name,
                    category_id = :category_id,
                    category = :category_name,
                    updated_at = NOW()
                WHERE id = :id AND (household_id = :household_id OR user_id = :user_id)
            ");
            return $upStmt->execute([
                ':amount'          => $amount,
                ':due_date'        => $cleanDueDate,
                ':penalty_rate'    => $penaltyRate,
                ':duration_months' => $cleanDurationMonths,
                ':custom_name'     => $cleanCustomName,
                ':category_id'     => $categoryId,
                ':category_name'   => $categoryName,
                ':id'              => (int)$budgetId,
                ':household_id'    => $householdId,
                ':user_id'         => $userId
            ]);
        }

        // 2. Check if a budget record already exists for this category, month, and custom_name
        if (!empty($cleanCustomName)) {
            $chk = $this->db->prepare("
                SELECT id FROM budgets 
                WHERE (household_id = :household_id OR user_id = :user_id)
                  AND category_id = :category_id 
                  AND custom_name = :custom_name 
                  AND budget_month = :budget_month
                LIMIT 1
            ");
            $chk->execute([
                ':household_id'  => $householdId,
                ':user_id'       => $userId,
                ':category_id'   => $categoryId,
                ':custom_name'   => $cleanCustomName,
                ':budget_month'  => $budgetMonth
            ]);
            $existingId = $chk->fetchColumn();
        } else {
            $chk = $this->db->prepare("
                SELECT id FROM budgets 
                WHERE (household_id = :household_id OR user_id = :user_id)
                  AND category_id = :category_id 
                  AND (custom_name IS NULL OR custom_name = '')
                  AND budget_month = :budget_month
                LIMIT 1
            ");
            $chk->execute([
                ':household_id'  => $householdId,
                ':user_id'       => $userId,
                ':category_id'   => $categoryId,
                ':budget_month'  => $budgetMonth
            ]);
            $existingId = $chk->fetchColumn();
        }

        if ($existingId) {
            $upStmt = $this->db->prepare("
                UPDATE budgets 
                SET amount = :amount,
                    due_date = :due_date,
                    penalty_rate = :penalty_rate,
                    duration_months = :duration_months,
                    custom_name = :custom_name,
                    updated_at = NOW()
                WHERE id = :id
            ");
            return $upStmt->execute([
                ':amount'          => $amount,
                ':due_date'        => $cleanDueDate,
                ':penalty_rate'    => $penaltyRate,
                ':duration_months' => $cleanDurationMonths,
                ':custom_name'     => $cleanCustomName,
                ':id'              => (int)$existingId
            ]);
        }

        // 3. Otherwise, insert a new budget row
        $query = "
            INSERT INTO budgets (
                household_id, user_id, category_id, category, custom_name, amount, budget_month, 
                due_date, penalty_rate, duration_months, created_at
            ) VALUES (
                :household_id, :user_id, :category_id, :category_name, :custom_name, :amount, :budget_month, 
                :due_date, :penalty_rate, :duration_months, NOW()
            )
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':household_id', $householdId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':category_name', $categoryName, PDO::PARAM_STR);
        $stmt->bindValue(':custom_name', $cleanCustomName, $cleanCustomName !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':amount', $amount);
        $stmt->bindValue(':budget_month', $budgetMonth, PDO::PARAM_STR);
        $stmt->bindValue(':due_date', $cleanDueDate, $cleanDueDate !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':penalty_rate', $penaltyRate);
        $stmt->bindValue(':duration_months', $cleanDurationMonths, $cleanDurationMonths !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);

        return $stmt->execute();
    }

    /**
     * Get all budgets saved for a household for a specific month.
     */
    public function getMonthlyBudgets($householdId, $monthDate = null) {
        if (!$householdId) return [];
        $budgetMonth = !empty($monthDate) ? date('Y-m-01', strtotime($monthDate)) : date('Y-m-01');

        $stmt = $this->db->prepare("
            SELECT b.id AS budget_id, b.household_id, b.user_id, b.category_id, 
                    COALESCE(tc.name, b.category) AS category_name,
                    b.category, b.custom_name, b.amount, b.budget_month, 
                    b.due_date, b.penalty_rate, b.duration_months, b.penalty_applied, b.is_paid,
                    tc.suggested_percent
            FROM budgets b
            LEFT JOIN transaction_categories tc ON b.category_id = tc.id
            WHERE b.household_id = ? AND b.budget_month = ?
            ORDER BY b.id ASC
        ");
        $stmt->execute([$householdId, $budgetMonth]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Delete a budget row by ID and household.
     */
    public function deleteBudget($budgetId, $householdId) {
        $stmt = $this->db->prepare("DELETE FROM budgets WHERE id = ? AND household_id = ?");
        return $stmt->execute([(int)$budgetId, (int)$householdId]);
    }

    /**
     * Get unpaid pending fixed budgets (Rent, Loan, Tuition) for the current month.
     */
    public function getPendingFixedBudgets($userId, $householdId = null, $month = null, $year = null) {
        $userId = (int)$userId;
        if ($userId <= 0) return [];

        $monthNum = !empty($month) ? (int)$month : (int)date('m');
        $yearNum = !empty($year) && (int)$year > 1900 ? (int)$year : (int)date('Y');
        $monthStr = sprintf('%04d-%02d', $yearNum, $monthNum);
        $householdId = $this->resolveHouseholdId($userId, $householdId);

        $stmt = $this->db->prepare("
            SELECT b.id, b.household_id, b.user_id, b.category_id, b.category, 
                   b.custom_name, b.amount, b.due_date, b.penalty_rate, b.duration_months, b.penalty_applied, b.is_paid,
                   COALESCE(tc.name, b.category) AS category_name
            FROM budgets b
            LEFT JOIN transaction_categories tc ON b.category_id = tc.id
            WHERE ((b.user_id = :user_id) OR (b.household_id IS NOT NULL AND b.household_id = :household_id))
              AND b.is_paid = 0
              AND b.amount > 0
              AND DATE_FORMAT(b.budget_month, '%Y-%m') = :month_str
              AND (LOWER(COALESCE(tc.name, b.category)) IN ('rent', 'loan', 'tuition'))
            ORDER BY b.due_date ASC, b.id ASC
        ");
        $stmt->execute([
            ':user_id'      => $userId,
            ':household_id' => $householdId,
            ':month_str'    => $monthStr
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * One-click action: mark a single budget as paid, automatically creating the expense record.
     */
    public function markBudgetAsPaid($budgetId, $userId, $householdId = null) {
        $budgetId = (int)$budgetId;
        $userId = (int)$userId;
        $householdId = $this->resolveHouseholdId($userId, $householdId);

        $stmt = $this->db->prepare("
            SELECT b.*, COALESCE(tc.name, b.category) AS category_name
            FROM budgets b
            LEFT JOIN transaction_categories tc ON b.category_id = tc.id
            WHERE b.id = :id AND ((b.user_id = :user_id) OR (b.household_id IS NOT NULL AND b.household_id = :household_id))
            LIMIT 1
        ");
        $stmt->execute([
            ':id'           => $budgetId,
            ':user_id'      => $userId,
            ':household_id' => $householdId
        ]);
        $budget = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$budget) return false;

        $amount = (float)$budget['amount'];
        $categoryId = (int)$budget['category_id'];
        $catName = $budget['category_name'] ?: 'Fixed Expense';
        $customName = $budget['custom_name'] ?? null;
        $date = date('Y-m-d');
        $desc = !empty($customName) ? "Fixed Payment: {$customName}" : "Fixed Payment: {$catName}";

        // 1. Insert expense record into expenses table
        $expStmt = $this->db->prepare("
            INSERT INTO expenses (
                household_id, user_id, category_id, category, custom_name, amount, expense_date, description, created_at
            ) VALUES (
                :household_id, :user_id, :category_id, :category, :custom_name, :amount, :expense_date, :description, NOW()
            )
        ");
        $expStmt->execute([
            ':household_id' => $householdId,
            ':user_id'      => $userId,
            ':category_id'  => $categoryId,
            ':category'     => $catName,
            ':custom_name'  => $customName,
            ':amount'       => $amount,
            ':expense_date' => $date,
            ':description'  => $desc
        ]);

        // 2. Also insert into transactions table for dashboard synchronization
        try {
            $txStmt = $this->db->prepare("
                INSERT INTO transactions (
                    household_id, user_id, category_id, type, amount, transaction_date, description
                ) VALUES (
                    ?, ?, ?, 'expense', ?, ?, ?
                )
            ");
            $txDesc = !empty($customName) ? "{$customName} ({$catName})" : $desc;
            $txStmt->execute([$householdId, $userId, $categoryId, $amount, $date, $txDesc]);
        } catch (Throwable $e) {
            error_log('markBudgetAsPaid transactions mirror: ' . $e->getMessage());
        }

        // 3. Mark the budget as paid in budgets table
        $up = $this->db->prepare("UPDATE budgets SET is_paid = 1, updated_at = NOW() WHERE id = ?");
        $up->execute([$budgetId]);

        return true;
    }

    /**
     * One-click action: mark ALL pending fixed budgets as paid.
     */
    public function markAllBudgetsAsPaid($userId, $householdId = null, $month = null, $year = null) {
        $pending = $this->getPendingFixedBudgets($userId, $householdId, $month, $year);
        $count = 0;
        foreach ($pending as $b) {
            if ($this->markBudgetAsPaid($b['id'], $userId, $householdId)) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Early reminder logic: Check if current date is between 1st and 7th of the month.
     * Inserts reminder notifications for unpaid fixed budgets with upcoming due dates.
     */
    public function checkEarlyReminders($userId, $month = null, $year = null, $force = false) {
        $userId = (int)$userId;
        if ($userId <= 0) return [];

        $day = (int)date('j');
        // Check if current date is between 1st and 7th of the month
        if (!$force && ($day < 1 || $day > 7)) {
            return [];
        }

        $monthNum = !empty($month) ? (int)$month : (int)date('m');
        $yearNum = !empty($year) && (int)$year > 1900 ? (int)$year : (int)date('Y');
        $monthStr = sprintf('%04d-%02d', $yearNum, $monthNum);
        $householdId = $this->resolveHouseholdId($userId, null);

        // Fetch unpaid fixed budgets for this month
        $stmt = $this->db->prepare("
            SELECT b.id, b.household_id, b.user_id, b.category_id, b.category, 
                   b.custom_name, b.amount, b.due_date, b.penalty_rate, 
                   COALESCE(tc.name, b.category) AS category_name
            FROM budgets b
            LEFT JOIN transaction_categories tc ON b.category_id = tc.id
            WHERE ((b.user_id = :user_id) OR (b.household_id IS NOT NULL AND b.household_id = :household_id))
              AND b.is_paid = 0
              AND b.due_date IS NOT NULL
              AND DATE_FORMAT(b.budget_month, '%Y-%m') = :month_str
              AND (LOWER(COALESCE(tc.name, b.category)) IN ('rent', 'loan', 'tuition'))
            ORDER BY b.due_date ASC
        ");
        $stmt->execute([
            ':user_id'      => $userId,
            ':household_id' => $householdId,
            ':month_str'    => $monthStr
        ]);
        $unpaidBudgets = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $sentReminders = [];

        foreach ($unpaidBudgets as $b) {
            $displayName = !empty($b['custom_name']) ? $b['custom_name'] : $b['category_name'];
            $dueDateFormatted = date('M j, Y', strtotime($b['due_date']));
            $penaltyRate = number_format((float)$b['penalty_rate'], 1);
            $targetUserId = !empty($b['user_id']) ? (int)$b['user_id'] : $userId;

            $title = "Payment Reminder: " . $displayName;
            if (strtolower(trim($b['category_name'])) === 'loan' && (float)$b['penalty_rate'] > 0) {
                $message = "Reminder: You have an upcoming payment for {$displayName} due on {$dueDateFormatted}. Pay on time to avoid a {$penaltyRate}% penalty.";
            } else {
                $message = "Reminder: You have an upcoming payment for {$displayName} due on {$dueDateFormatted}.";
            }

            // Check if already notified this month to avoid duplicates
            $chk = $this->db->prepare("
                SELECT COUNT(*) FROM notifications 
                WHERE user_id = :uid 
                  AND type = 'budget_alert' 
                  AND title = :title 
                  AND DATE_FORMAT(created_at, '%Y-%m') = :month_str
            ");
            $chk->execute([
                ':uid'       => $targetUserId,
                ':title'     => $title,
                ':month_str' => $monthStr
            ]);
            $alreadyNotified = (int)$chk->fetchColumn();

            if ($alreadyNotified === 0) {
                try {
                    $ins = $this->db->prepare("
                        INSERT INTO notifications (user_id, title, message, type, is_read, created_at)
                        VALUES (:uid, :title, :message, 'budget_alert', 0, NOW())
                    ");
                    $ins->execute([
                        ':uid'     => $targetUserId,
                        ':title'   => $title,
                        ':message' => $message
                    ]);
                    $sentReminders[] = [
                        'budget_id' => $b['id'],
                        'title'     => $title,
                        'message'   => $message
                    ];
                } catch (Throwable $e) {
                    error_log('checkEarlyReminders notification insert error: ' . $e->getMessage());
                }
            }
        }

        return $sentReminders;
    }

    /**
     * Check for overdue fixed expense budgets and apply penalties + notifications.
     *
     * @param int $user_id
     * @param int|null $month
     * @param int|null $year
     * @return array List of applied penalties
     */
    public function checkOverdueBudgets($user_id, $month = null, $year = null) {
        $user_id = (int)$user_id;
        if ($user_id <= 0) return [];

        $monthNum = !empty($month) ? (int)$month : (int)date('m');
        $yearNum = !empty($year) && (int)$year > 1900 ? (int)$year : (int)date('Y');
        $monthStr = sprintf('%04d-%02d', $yearNum, $monthNum);
        $today = date('Y-m-d');
        $householdId = $this->resolveHouseholdId($user_id, null);

        // Find budgets where:
        // - user_id or household_id matches
        // - budget_month matches the requested month/year
        // - due_date is past today
        // - is_paid is false (0)
        // - penalty_applied is false (0)
        $stmt = $this->db->prepare("
            SELECT b.id, b.household_id, b.user_id, b.category_id, b.category, b.custom_name, b.amount, 
                   b.due_date, b.penalty_rate, tc.name AS category_name
            FROM budgets b
            LEFT JOIN transaction_categories tc ON b.category_id = tc.id
            WHERE ((b.user_id = :user_id) OR (b.household_id IS NOT NULL AND b.household_id = :household_id))
              AND b.due_date IS NOT NULL
              AND b.due_date < :today
              AND b.is_paid = 0
              AND b.penalty_applied = 0
              AND DATE_FORMAT(b.budget_month, '%Y-%m') = :month_str
              AND LOWER(COALESCE(tc.name, b.category)) = 'loan'
        ");
        $stmt->execute([
            ':user_id'      => $user_id,
            ':household_id' => $householdId,
            ':today'        => $today,
            ':month_str'    => $monthStr
        ]);
        $overdueList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $penaltiesApplied = [];

        foreach ($overdueList as $b) {
            $budgetId = (int)$b['id'];
            $currentAmount = (float)$b['amount'];
            $penaltyRate = (float)($b['penalty_rate'] ?? 0.00);
            $baseCatName = !empty($b['category_name']) ? $b['category_name'] : (!empty($b['category']) ? $b['category'] : 'Fixed Expense');
            
            // Strictly enforce: automated penalties ONLY apply to Loan category
            if (strtolower(trim($baseCatName)) !== 'loan') {
                continue;
            }

            $catName = !empty($b['custom_name']) ? $b['custom_name'] : $baseCatName;
            $dueDateFormatted = date('M d, Y', strtotime($b['due_date']));

            // Calculate penalty: allocated_amount * penalty_rate / 100
            $penalty = 0.00;
            if ($penaltyRate > 0) {
                $penalty = round($currentAmount * ($penaltyRate / 100), 2);
            }
            $newAmount = $currentAmount + $penalty;

            // Update allocated_amount by adding penalty and set penalty_applied = 1
            $upStmt = $this->db->prepare("
                UPDATE budgets 
                SET amount = :new_amount, penalty_applied = 1, updated_at = NOW() 
                WHERE id = :id
            ");
            $upStmt->execute([
                ':new_amount' => $newAmount,
                ':id'         => $budgetId
            ]);

            // Insert a reminder warning message into the user's notifications table
            $targetUserId = !empty($b['user_id']) ? (int)$b['user_id'] : $user_id;
            $title = "Overdue Alert: {$catName} Payment Past Due";
            $message = "Your scheduled budget for {$catName} was due on {$dueDateFormatted} and remains unpaid. ";
            if ($penalty > 0) {
                $message .= "A late penalty of {$penaltyRate}% (\$" . number_format($penalty, 2) . ") has been added. New allocated total is \$" . number_format($newAmount, 2) . ".";
            } else {
                $message .= "Please record your payment of \$" . number_format($currentAmount, 2) . " as soon as possible.";
            }

            try {
                $notifStmt = $this->db->prepare("
                    INSERT INTO notifications (user_id, title, message, type, is_read, created_at)
                    VALUES (:user_id, :title, :message, 'budget_alert', 0, NOW())
                ");
                $notifStmt->execute([
                    ':user_id' => $targetUserId,
                    ':title'   => $title,
                    ':message' => $message
                ]);
            } catch (Throwable $e) {
                error_log("Failed to insert overdue notification: " . $e->getMessage());
            }

            $penaltiesApplied[] = [
                'budget_id'     => $budgetId,
                'category'      => $catName,
                'due_date'      => $b['due_date'],
                'penalty_rate'  => $penaltyRate,
                'penalty'       => $penalty,
                'old_amount'    => $currentAmount,
                'new_amount'    => $newAmount
            ];
        }

        return $penaltiesApplied;
    }

    /**
     * Retrieve the full budget row for a specific category and month.
     *
     * @param int $userId
     * @param string|int $category
     * @param int|string|null $month
     * @param int|null $year
     * @return array|null
     */
    public function getBudgetRowByCategory($userId, $category, $month = null, $year = null) {
        $userId = (int)$userId;
        if (is_numeric($year) && (int)$year > 1900) {
            $monthNum = !empty($month) ? (int)$month : (int)date('m');
            $budgetMonth = sprintf('%04d-%02d', (int)$year, $monthNum);
            $householdId = $this->resolveHouseholdId($userId, null);
        } elseif (is_string($month) && strpos($month, '-') !== false) {
            $budgetMonth = substr($month, 0, 7);
            $householdId = is_numeric($year) ? (int)$year : $this->resolveHouseholdId($userId, null);
        } else {
            $budgetMonth = date('Y-m');
            $householdId = $this->resolveHouseholdId($userId, null);
        }

        $categoryId = is_numeric($category) ? (int)$category : null;
        $categoryName = is_numeric($category) ? '' : trim((string)$category);

        $stmt = $this->db->prepare("
            SELECT id, household_id, user_id, category_id, category, custom_name, amount, 
                   due_date, penalty_rate, duration_months, penalty_applied, is_paid, budget_month
            FROM budgets 
            WHERE ((household_id IS NOT NULL AND household_id = :household_id) OR user_id = :user_id)
              AND (
                  (:category_id IS NOT NULL AND category_id = :category_id)
                  OR (:category_name != '' AND (category = :category_name OR category LIKE :category_like))
              )
              AND DATE_FORMAT(budget_month, '%Y-%m') = :budget_month
            ORDER BY id DESC
            LIMIT 1
        ");
        $stmt->bindValue(':household_id', $householdId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':category_name', $categoryName, PDO::PARAM_STR);
        $stmt->bindValue(':category_like', '%' . $categoryName . '%', PDO::PARAM_STR);
        $stmt->bindValue(':budget_month', $budgetMonth, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Checks if a newly added expense for Rent, Loan, or Tuition covers the allocated budget.
     * If total expenses cover the allocated amount, updates is_paid = 1.
     *
     * @param int $userId
     * @param int $categoryId
     * @param string|null $date
     * @param int|null $householdId
     * @return bool
     */
    public function markBudgetPaidIfCovered($userId, $categoryId, $date = null, $householdId = null) {
        $stmt = $this->db->prepare("SELECT name FROM transaction_categories WHERE id = ? LIMIT 1");
        $stmt->execute([(int)$categoryId]);
        $catName = $stmt->fetchColumn();
        if (!$catName || !in_array(strtolower(trim($catName)), ['rent', 'loan', 'tuition'])) {
            return false;
        }

        $monthStr = !empty($date) ? date('Y-m', strtotime($date)) : date('Y-m');
        $householdId = $this->resolveHouseholdId($userId, $householdId);

        // Fetch current budget row
        $bStmt = $this->db->prepare("
            SELECT id, amount, is_paid 
            FROM budgets 
            WHERE (user_id = :user_id OR (household_id IS NOT NULL AND household_id = :household_id))
              AND category_id = :category_id
              AND DATE_FORMAT(budget_month, '%Y-%m') = :month_str
            ORDER BY id DESC LIMIT 1
        ");
        $bStmt->execute([
            ':user_id'     => (int)$userId,
            ':household_id'=> (int)$householdId,
            ':category_id' => (int)$categoryId,
            ':month_str'   => $monthStr
        ]);
        $budget = $bStmt->fetch(PDO::FETCH_ASSOC);
        if (!$budget || (float)$budget['amount'] <= 0) {
            return false;
        }

        // Calculate total expenses for this category this month
        $spentStmt = $this->db->prepare("
            SELECT COALESCE(SUM(amount), 0.00) 
            FROM transactions 
            WHERE (user_id = :user_id OR household_id = :household_id)
              AND category_id = :category_id 
              AND type = 'expense'
              AND DATE_FORMAT(transaction_date, '%Y-%m') = :month_str
        ");
        $spentStmt->execute([
            ':user_id'     => (int)$userId,
            ':household_id'=> (int)$householdId,
            ':category_id' => (int)$categoryId,
            ':month_str'   => $monthStr
        ]);
        $totalSpent = (float)$spentStmt->fetchColumn();

        try {
            $expStmt = $this->db->prepare("
                SELECT COALESCE(SUM(amount), 0.00) 
                FROM expenses 
                WHERE (user_id = :user_id OR household_id = :household_id)
                  AND (category_id = :category_id OR category LIKE :cat_like)
                  AND DATE_FORMAT(expense_date, '%Y-%m') = :month_str
            ");
            $expStmt->execute([
                ':user_id'     => (int)$userId,
                ':household_id'=> (int)$householdId,
                ':category_id' => (int)$categoryId,
                ':cat_like'    => '%' . $catName . '%',
                ':month_str'   => $monthStr
            ]);
            $totalSpent = max($totalSpent, (float)$expStmt->fetchColumn());
        } catch (Throwable $e) {}

        // If total spent covers the allocated amount, set is_paid = 1
        if ($totalSpent >= (float)$budget['amount']) {
            $up = $this->db->prepare("UPDATE budgets SET is_paid = 1, updated_at = NOW() WHERE id = ?");
            $up->execute([$budget['id']]);
            return true;
        }

        return false;
    }

    /**
     * Retrieve the allocated budget limit for a specific category and month.
     *
     * @param int $userId
     * @param string|int $category
     * @param int|string|null $month Month (1-12) or 'YYYY-MM' / 'YYYY-MM-DD'
     * @param int|null $year Year (e.g. 2026) or household ID
     * @return float
     */
    public function getBudgetByCategory($userId, $category, $month = null, $year = null) {
        $userId = (int)$userId;
        
        if (is_numeric($year) && (int)$year > 1900) {
            $monthNum = !empty($month) ? (int)$month : (int)date('m');
            $budgetMonth = sprintf('%04d-%02d', (int)$year, $monthNum);
            $householdId = $this->resolveHouseholdId($userId, null);
        } elseif (is_string($month) && strpos($month, '-') !== false) {
            $budgetMonth = substr($month, 0, 7);
            $householdId = is_numeric($year) ? (int)$year : $this->resolveHouseholdId($userId, null);
        } else {
            $budgetMonth = date('Y-m');
            $householdId = $this->resolveHouseholdId($userId, null);
        }

        $categoryId = is_numeric($category) ? (int)$category : null;
        $categoryName = is_numeric($category) ? '' : trim((string)$category);

        $stmt = $this->db->prepare("
            SELECT amount FROM budgets 
            WHERE ((household_id IS NOT NULL AND household_id = :household_id) OR user_id = :user_id)
              AND (
                  (:category_id IS NOT NULL AND category_id = :category_id)
                  OR (:category_name != '' AND (category = :category_name OR category LIKE :category_like))
              )
              AND DATE_FORMAT(budget_month, '%Y-%m') = :budget_month
            ORDER BY id DESC
            LIMIT 1
        ");

        $stmt->bindValue(':household_id', $householdId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':category_name', $categoryName, PDO::PARAM_STR);
        $stmt->bindValue(':category_like', '%' . $categoryName . '%', PDO::PARAM_STR);
        $stmt->bindValue(':budget_month', $budgetMonth, PDO::PARAM_STR);
        $stmt->execute();

        $amount = $stmt->fetchColumn();

        if ($amount !== false && (float)$amount > 0) {
            return round((float)$amount, 2);
        }

        // If category is grocery-related and no custom row was set, use getGroceryBudget
        if (stripos($categoryName, 'grocer') !== false) {
            return $this->getGroceryBudget($householdId);
        }

        return 0.00;
    }
}
?>
