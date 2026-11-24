<?php
require_once '../app/Models/BudgetModel.php';
require_once '../app/Models/ExpenseModel.php';

/**
 * BudgetController
 * Handles Module: Budgets.
 * Manages income recording, monthly category budget planning,
 * and dynamic calculations of Total Income and Available to Spend.
 */
class BudgetController extends Controller {
    private $budgetModel;
    private $expenseModel;

    public function __construct() {
        $this->budgetModel = new BudgetModel();
        $this->expenseModel = new ExpenseModel();
    }

    private function requireCustomer() {
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'customer') {
            header('Location: ' . URLROOT . '/customer/login');
            exit();
        }
    }

    /**
     * Display budget planning dashboard.
     * Computes Total Income, Savings Goal, and Available to Spend.
     */
    public function index() {
        $this->requireCustomer();

        $userId = (int)$_SESSION['user_id'];
        $householdId = $_SESSION['household_id'] ?? null;
        $isHead = (($_SESSION['household_role'] ?? 'head') === 'head');
        $month = (int)date('m');
        $year = (int)date('Y');
        $budgetMonth = sprintf('%04d-%02d-01', $year, $month);

        // Handle inline POST submissions from budgets page
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'incomes') {
                $this->saveIncome();
                return;
            }

            if ($action === 'add_fixed' && $isHead) {
                $catId = (int)($_POST['category_id'] ?? 0);
                $customName = trim($_POST['custom_name'] ?? '');
                $amount = max(0.00, (float)($_POST['limit'] ?? 0));
                $dueDate = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
                $penaltyRate = isset($_POST['penalty_rate']) ? (float)$_POST['penalty_rate'] : 0.00;
                $durationMonths = !empty($_POST['duration_months']) ? (int)$_POST['duration_months'] : null;

                if ($catId > 0 && $amount > 0) {
                    $this->budgetModel->setOrUpdateBudget(
                        $userId, $catId, $amount, $month, $year, $dueDate, $penaltyRate, $customName, null, $durationMonths
                    );
                    $_SESSION['customer_flash'] = 'Fixed expense added successfully.';
                }
                header('Location: ' . URLROOT . '/customer/budgets');
                exit();
            }

            if ($action === 'delete_fixed' && $isHead) {
                $delId = (int)($_POST['budget_id'] ?? 0);
                if ($delId > 0) {
                    $this->budgetModel->deleteBudget($delId, $householdId);
                    $_SESSION['customer_flash'] = 'Fixed expense removed.';
                }
                header('Location: ' . URLROOT . '/customer/budgets');
                exit();
            }

            if ($action === 'save_plan') {
                $this->savePlan();
                return;
            }

            if ($action === 'add_income' && $isHead) {
                $name = trim($_POST['name'] ?? 'Additional Income');
                $amount = (float)($_POST['amount'] ?? 0);
                if ($amount > 0) {
                    $this->budgetModel->addAdditionalIncome($householdId, $userId, $name, $amount);
                    $_SESSION['customer_flash'] = 'Additional income added successfully.';
                }
                header('Location: ' . URLROOT . '/customer/budgets');
                exit();
            }

            if ($action === 'remove_income' && $isHead) {
                $this->budgetModel->removeAdditionalIncome((int)($_POST['id'] ?? 0), $householdId);
                $_SESSION['customer_flash'] = 'Additional income removed.';
                header('Location: ' . URLROOT . '/customer/budgets');
                exit();
            }
        }

        // 1. Run early reminder checks (1st to 7th of the month)
        $this->budgetModel->checkEarlyReminders($userId, $month, $year);

        // 2. Check overdue fixed expenses to apply penalties & notifications
        $this->budgetModel->checkOverdueBudgets($userId, $month, $year);

        // 3. Fetch total monthly income using the model
        $total_income = $this->budgetModel->getTotalMonthlyIncome($userId, $month, $year);

        // 4. Fetch savings goal and calculate Available to Spend
        $savingsGoal = $this->budgetModel->getSavingsGoal($householdId);
        $savings_goal = (float)($savingsGoal['target_amount'] ?? 0.00);
        $available_to_spend = max(0.00, $total_income - $savings_goal);

        // 5. Fetch categories and group into Fixed vs. Variable
        $categories = $this->budgetModel->getCategories();
        $incomes = $this->budgetModel->getIncomes($householdId);
        $extras = $this->budgetModel->getAdditionalIncomes($householdId);

        $fixedCategories = [];
        $variableCategories = [];
        foreach ($categories as $cat) {
            if (in_array(strtolower(trim($cat['name'])), ['rent', 'loan', 'tuition'])) {
                $fixedCategories[$cat['id']] = $cat;
            } else {
                $variableCategories[$cat['id']] = $cat;
            }
        }

        // Fetch all monthly budget rows saved for this month
        $allMonthlyBudgets = $this->budgetModel->getMonthlyBudgets($householdId, $budgetMonth);
        $savedFixed = [];
        $savedVariable = [];
        foreach ($allMonthlyBudgets as $b) {
            $catLower = strtolower(trim($b['category_name'] ?: $b['category']));
            if (in_array($catLower, ['rent', 'loan', 'tuition'])) {
                $savedFixed[] = $b;
            } else {
                $savedVariable[$b['category_id']] = $b;
            }
        }

        $fixedRows = [];
        $coveredBase = [];
        foreach ($savedFixed as $sf) {
            $fixedRows[] = [
                'budget_id'       => (int)$sf['budget_id'],
                'id'              => (int)$sf['category_id'],
                'category_id'     => (int)$sf['category_id'],
                'name'            => $sf['category_name'] ?: $sf['category'],
                'category_name'   => $sf['category_name'] ?: $sf['category'],
                'custom_name'     => $sf['custom_name'] ?? '',
                'limit'           => (float)$sf['amount'],
                'suggested'       => round($available_to_spend * (($sf['suggested_percent'] ?? 0) / 100), 2),
                'due_date'        => $sf['due_date'] ?? '',
                'penalty_rate'    => (float)($sf['penalty_rate'] ?? 0.00),
                'duration_months' => !empty($sf['duration_months']) ? (int)$sf['duration_months'] : null,
                'penalty_applied' => !empty($sf['penalty_applied']),
                'is_paid'         => !empty($sf['is_paid']),
                'is_custom'       => !empty($sf['custom_name'])
            ];
            if (empty($sf['custom_name'])) {
                $coveredBase[$sf['category_id']] = true;
            }
        }
        foreach ($fixedCategories as $catId => $fc) {
            if (!isset($coveredBase[$catId])) {
                $suggested = round($available_to_spend * ((float)$fc['suggested_percent'] / 100), 2);
                $fixedRows[] = [
                    'budget_id'       => 0,
                    'id'              => $catId,
                    'category_id'     => $catId,
                    'name'            => $fc['name'],
                    'category_name'   => $fc['name'],
                    'custom_name'     => '',
                    'limit'           => 0.00,
                    'suggested'       => $suggested,
                    'due_date'        => '',
                    'penalty_rate'    => 0.00,
                    'duration_months' => null,
                    'penalty_applied' => false,
                    'is_paid'         => false,
                    'is_custom'       => false
                ];
            }
        }

        $variableRows = [];
        $useSuggestion = isset($_GET['suggest']);
        foreach ($variableCategories as $catId => $vc) {
            $percent = (float)$vc['suggested_percent'];
            $suggested = round($available_to_spend * $percent / 100, 2);
            $savedRow = $savedVariable[$catId] ?? null;
            $limit = (!$useSuggestion && $savedRow) ? (float)$savedRow['amount'] : $suggested;

            $variableRows[] = [
                'budget_id'     => $savedRow ? (int)$savedRow['budget_id'] : 0,
                'id'            => $catId,
                'category_id'   => $catId,
                'name'          => $vc['name'],
                'percent'       => $percent,
                'suggested'     => $suggested,
                'limit'         => $limit
            ];
        }

        $totalPlanned = array_sum(array_column($fixedRows, 'limit')) + array_sum(array_column($variableRows, 'limit'));
        $totalSpent = 0.00;
        $totalPercent = 0;
        foreach ($categories as $cat) {
            $totalPercent += (float)($cat['suggested_percent'] ?? 0);
        }
        $rows = array_merge($fixedRows, $variableRows);

        // 6. Pass dynamic values to the view array ($data)
        $data = [
            'is_head'            => $isHead,
            'total_income'       => $total_income,
            'savings_goal'       => $savings_goal,
            'available_to_spend' => $available_to_spend,
            'total_planned'      => $totalPlanned,
            'total_percent'      => $totalPercent,
            'total_spent'        => $totalSpent,
            'incomes'            => $incomes,
            'extras'             => $extras,
            'fixed_rows'         => $fixedRows,
            'variable_rows'      => $variableRows,
            'fixed_categories'   => $fixedCategories,
            'flash'              => $_SESSION['customer_flash'] ?? '',
            'error'              => $_SESSION['customer_error'] ?? ''
        ];

        unset($_SESSION['customer_flash'], $_SESSION['customer_error']);
        $this->view('customer/budgets', $data);
    }

    /**
     * Save the monthly budget plan.
     * Loops through submitted budget categories array and saves each one using setOrUpdateBudget.
     */
    public function savePlan() {
        $this->requireCustomer();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = (int)$_SESSION['user_id'];
            $householdId = $_SESSION['household_id'] ?? null;
            $month = (int)date('m');
            $year = (int)date('Y');

            // 1. Process existing fixed items
            if (!empty($_POST['fixed']) && is_array($_POST['fixed'])) {
                foreach ($_POST['fixed'] as $item) {
                    $catId = (int)($item['category_id'] ?? 0);
                    $amount = max(0.00, (float)($item['limit'] ?? 0));
                    $customName = trim($item['custom_name'] ?? '');
                    $dueDate = !empty($item['due_date']) ? $item['due_date'] : null;
                    $penaltyRate = max(0.00, (float)($item['penalty_rate'] ?? 0));
                    $durationMonths = !empty($item['duration_months']) ? (int)$item['duration_months'] : null;
                    $budgetId = !empty($item['budget_id']) ? (int)$item['budget_id'] : null;

                    if ($catId > 0) {
                        $this->budgetModel->setOrUpdateBudget(
                            $userId, $catId, $amount, $month, $year, $dueDate, $penaltyRate, $customName, $budgetId, $durationMonths
                        );
                    }
                }
            }

            // 2. Process dynamically appended fixed items
            if (!empty($_POST['fixed_new']) && is_array($_POST['fixed_new'])) {
                foreach ($_POST['fixed_new'] as $item) {
                    $catId = (int)($item['category_id'] ?? 0);
                    $amount = max(0.00, (float)($item['limit'] ?? 0));
                    $customName = trim($item['custom_name'] ?? '');
                    $dueDate = !empty($item['due_date']) ? $item['due_date'] : null;
                    $penaltyRate = max(0.00, (float)($item['penalty_rate'] ?? 0));
                    $durationMonths = !empty($item['duration_months']) ? (int)$item['duration_months'] : null;

                    if ($catId > 0 && $amount > 0) {
                        $this->budgetModel->setOrUpdateBudget(
                            $userId, $catId, $amount, $month, $year, $dueDate, $penaltyRate, $customName, null, $durationMonths
                        );
                    }
                }
            }

            // 3. Process variable categories
            $varLimits = $_POST['variable'] ?? $_POST['limit'] ?? $_POST['limits'] ?? [];
            foreach ($varLimits as $catId => $amount) {
                $catId = (int)$catId;
                $amount = max(0.00, (float)$amount);
                if ($catId > 0) {
                    $this->budgetModel->setOrUpdateBudget(
                        $userId, $catId, $amount, $month, $year, null, 0.00, null, null
                    );
                }
            }

            $_SESSION['customer_flash'] = 'Monthly plan saved successfully.';
        }

        header('Location: ' . URLROOT . '/customer/budgets');
        exit();
    }

    /**
     * Save or update income for users.
     */
    public function markAsPaid() {
        $this->requireCustomer();
        $userId = (int)$_SESSION['user_id'];
        $householdId = $_SESSION['household_id'] ?? null;
        $budgetId = (int)($_POST['budget_id'] ?? 0);

        if ($budgetId > 0) {
            $success = $this->budgetModel->markBudgetAsPaid($budgetId, $userId, $householdId);
            if ($success) {
                $_SESSION['customer_flash'] = 'Fixed payment recorded as an expense and marked as paid.';
            } else {
                $_SESSION['customer_error'] = 'Could not mark payment as paid.';
            }
        }
        header('Location: ' . URLROOT . '/customer/addExpense');
        exit();
    }

    public function markAllAsPaid() {
        $this->requireCustomer();
        $userId = (int)$_SESSION['user_id'];
        $householdId = $_SESSION['household_id'] ?? null;

        $count = $this->budgetModel->markAllBudgetsAsPaid($userId, $householdId);
        if ($count > 0) {
            $_SESSION['customer_flash'] = "All {$count} pending fixed payments have been marked as paid and recorded.";
        } else {
            $_SESSION['customer_flash'] = 'No pending fixed payments were found to mark as paid.';
        }
        header('Location: ' . URLROOT . '/customer/addExpense');
        exit();
    }

    public function saveIncome() {
        $this->requireCustomer();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = (int)$_SESSION['user_id'];
            $householdId = $_SESSION['household_id'] ?? null;
            $month = (int)date('m');
            $year = (int)date('Y');

            if (isset($_POST['income']) && is_array($_POST['income'])) {
                foreach ($_POST['income'] as $targetUserId => $amount) {
                    $amt = max(0.00, (float)$amount);
                    $targetId = (int)$targetUserId;
                    $this->budgetModel->saveIncome($targetId, 'Primary Income', $amt, $month, $year);
                    $this->budgetModel->updateIncome($targetId, $householdId, $amt);
                }
            } else {
                $source = trim($_POST['source'] ?? 'Primary Income');
                $amount = (float)($_POST['amount'] ?? 0);
                $this->budgetModel->saveIncome($userId, $source, $amount, $month, $year);
                $this->budgetModel->updateIncome($userId, $householdId, $amount);
            }

            $_SESSION['customer_flash'] = 'Income saved successfully.';
        }

        header('Location: ' . URLROOT . '/customer/budgets');
        exit();
    }
}
?>
