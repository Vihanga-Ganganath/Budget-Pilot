<?php
require_once '../app/Models/BudgetModel.php';
require_once '../app/Models/ExpenseModel.php';
require_once '../app/Models/Notice.php';

/**
 * HomeController
 * Landing page and Dashboard loader.
 * Fetches dynamic Total Income and Available to Spend for authenticated customers.
 */
class HomeController extends Controller {
    private $budgetModel;
    private $expenseModel;
    private $noticeModel;

    public function __construct() {
        $this->budgetModel = new BudgetModel();
        $this->expenseModel = new ExpenseModel();
        $this->noticeModel = new Notice();
    }

    public function index() {
        // If a customer is logged in, show their dynamic financial dashboard
        if (isset($_SESSION['user_id']) && ($_SESSION['user_role'] ?? '') === 'customer') {
            $this->dashboard();
            return;
        }

        // Loads the main public landing page
        $this->view('home');
    }

    /**
     * Customer Dashboard with dynamic calculations:
     * $total_income, $savings_goal, and $available_to_spend.
     */
    public function dashboard() {
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'customer') {
            header('Location: ' . URLROOT . '/customer/login');
            exit();
        }

        $userId = (int)$_SESSION['user_id'];
        $householdId = $_SESSION['household_id'] ?? null;
        $month = (int)date('m');
        $year = (int)date('Y');

        // 1. Check early reminders (1st to 7th of the month)
        $this->budgetModel->checkEarlyReminders($userId, $month, $year);

        // 2. Check for overdue fixed expense budgets and apply penalties + notifications
        $this->budgetModel->checkOverdueBudgets($userId, $month, $year);

        // 3. Fetch total monthly income from model
        $total_income = $this->budgetModel->getTotalMonthlyIncome($userId, $month, $year);

        // 2. Fetch savings goal and calculate Available to Spend
        $savingsGoal = $this->budgetModel->getSavingsGoal($householdId);
        $savings_goal = (float)($savingsGoal['target_amount'] ?? 0.00);
        $available_to_spend = max(0.00, $total_income - $savings_goal);

        // 3. Fetch monthly expenses and plan
        $spending = $this->expenseModel->getSpendingByCategory($householdId);
        $spent = 0.00;
        foreach ($spending as $c) {
            $spent += (float)($c['total'] ?? 0);
        }

        $budget = array_sum($this->budgetModel->getPlan($householdId));
        $remaining = round($budget - $spent, 2);

        // 4. Pass dynamic values to the view array ($data)
        $data = [
            'total_income'       => $total_income,
            'savings_goal'       => $savings_goal,
            'available_to_spend' => $available_to_spend,
            'total_expenses'     => $spent,
            'remaining_budget'   => $remaining,
            'spending'           => $spending,
            'transactions'       => $this->expenseModel->getTransactions($householdId),
            'notices'            => $this->noticeModel->getActiveForAudience('customer'),
            'flash'              => $_SESSION['customer_flash'] ?? ''
        ];

        unset($_SESSION['customer_flash'], $_SESSION['customer_flash_type']);
        $this->view('customer/dashboard', $data);
    }
}
?>
