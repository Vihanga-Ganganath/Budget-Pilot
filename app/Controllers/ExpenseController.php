<?php
require_once '../app/Models/ExpenseModel.php';
require_once '../app/Models/BudgetModel.php';

/**
 * ExpenseController
 * Handles Module: Expenses.
 * Interconnects with BudgetModel to calculate Remaining Budget dynamically (Allocated Budget - Total Expenses).
 */
class ExpenseController extends Controller {
    private $expenseModel;
    private $budgetModel;

    public function __construct() {
        $this->expenseModel = new ExpenseModel();
        $this->budgetModel = new BudgetModel();
    }

    private function requireCustomer() {
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'customer') {
            header('Location: ' . URLROOT . '/customer/login');
            exit();
        }
    }

    /**
     * Display the expenses overview and record entry form.
     */
    public function index() {
        $this->add();
    }

    /**
     * Add Expense action and dynamic Category Spending / Remaining Budget display.
     */
    public function add() {
        $this->requireCustomer();

        $userId = (int)$_SESSION['user_id'];
        $householdId = $_SESSION['household_id'] ?? null;
        $currentMonth = date('Y-m-01');

        $categories = $this->budgetModel->getCategories();
        $data = ['error' => '', 'old' => $_POST];

        // One-click pay actions
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'mark_as_paid') {
            $this->markAsPaid();
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'mark_all_as_paid') {
            $this->markAllAsPaid();
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $amount      = (float)($_POST['amount'] ?? 0);
            $categoryId  = (int)($_POST['category'] ?? 0);
            $date        = $_POST['expense_date'] ?? date('Y-m-d');
            $description = trim($_POST['description'] ?? '');

            if ($amount <= 0) {
                $data['error'] = 'Please enter an expense amount greater than $0.00.';
            } elseif (!in_array($categoryId, array_column($categories, 'id'))) {
                $data['error'] = 'Please select a valid expense category.';
            } elseif (!strtotime($date)) {
                $data['error'] = 'Please provide a valid date.';
            } else {
                $this->expenseModel->addExpense($userId, $categoryId, $amount, $date, $description, $householdId);
                // Check if expense is for Rent, Loan, or Tuition and covers allocated budget
                $this->budgetModel->markBudgetPaidIfCovered($userId, $categoryId, $date, $householdId);
                $_SESSION['customer_flash'] = 'Expense of $' . number_format($amount, 2) . ' recorded successfully.';
                header('Location: ' . URLROOT . '/customer/addExpense');
                exit();
            }
        }

        // Build spending list and dynamically calculate Remaining Budget (Allocated Limit - Total Expenses)
        $spending = [];
        $plan = [];

        foreach ($categories as $cat) {
            $catId = (int)$cat['id'];
            $allocatedLimit = $this->budgetModel->getBudgetByCategory($userId, $catId, $currentMonth, $householdId);
            $totalSpent = $this->expenseModel->getTotalExpensesByCategory($userId, $catId, date('Y-m'), $householdId);
            $remaining = round($allocatedLimit - $totalSpent, 2);

            $spending[] = [
                'id'        => $catId,
                'name'      => $cat['name'],
                'total'     => $totalSpent,
                'limit'     => $allocatedLimit,
                'remaining' => $remaining
            ];

            $plan[$catId] = $allocatedLimit;
        }

        $data['categories']   = $categories;
        $data['spending']     = $spending;
        $data['plan']         = $plan;
        $data['pending_fixed'] = $this->budgetModel->getPendingFixedBudgets($userId, $householdId);
        $data['flash']        = $_SESSION['customer_flash'] ?? '';

        unset($_SESSION['customer_flash'], $_SESSION['customer_flash_type']);

        $this->view('customer/addExpense', $data);
    }

    /**
     * One-click action to mark an individual fixed budget as paid and record the expense.
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

    /**
     * One-click action to mark all pending fixed budgets as paid and record their expenses.
     */
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
}
?>
