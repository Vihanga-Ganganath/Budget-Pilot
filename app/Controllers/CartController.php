<?php
require_once '../app/Models/CartModel.php';
require_once '../app/Models/BudgetModel.php';
require_once '../app/Models/ExpenseModel.php';
require_once '../app/Models/RecommendationModel.php';

/**
 * CartController
 * Handles Module: Smart Purchase & Live Cart.
 * Interconnects with BudgetModel, ExpenseModel, and RecommendationModel.
 * Features:
 *  - Live Cart total calculation via direct SQL join with products
 *  - Real-time Grocery Budget vs. Total Expenses evaluation (Remaining Budget = Allocated - Spent)
 *  - Real-time warning flag ($is_budget_exceeded) when live cart total surpasses Remaining Budget
 *  - Checkout logic: Inserts total into expenses table (Category: 'Grocery') and clears cart_items
 *  - Automated Brand Comparison and cheaper alternative recommendations
 */
class CartController extends Controller {
    private $cartModel;
    private $budgetModel;
    private $expenseModel;
    private $recommendationModel;

    public function __construct() {
        $this->cartModel = new CartModel();
        $this->budgetModel = new BudgetModel();
        $this->expenseModel = new ExpenseModel();
        $this->recommendationModel = new RecommendationModel();
    }

    /**
     * Ensure the user is authenticated as a customer.
     */
    private function requireCustomer() {
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'customer') {
            header('Location: ' . URLROOT . '/customer/login');
            exit();
        }
    }

    /**
     * Load the cart page.
     * 1. Fetches user's active cart and real-time live cart value (JOIN products).
     * 2. Fetches allocated "Grocery Budget" and total grocery expenses already spent this month.
     * 3. Calculates the "Remaining Budget" (Allocated Budget - Total Expenses).
     * 4. Evaluates if live cart total surpasses the "Remaining Budget" for groceries to trigger real-time warning.
     * 5. Fetches recommended cheaper alternatives for items in the cart.
     * 6. Passes all data to customer/cart view.
     */
    public function index() {
        $this->requireCustomer();

        $userId = (int)$_SESSION['user_id'];
        $householdId = $_SESSION['household_id'] ?? null;
        $currentMonth = date('Y-m-01');

        // 1. Fetch user's active cart details and live cart value from CartModel (JOIN with products)
        $cartDetails = $this->cartModel->getUserCartDetails($userId);
        $totalCartValue = $this->cartModel->getTotalCartValue($userId);

        // 2. Fetch the user's allocated "Grocery Budget" from BudgetModel
        $groceryBudget = $this->budgetModel->getBudgetByCategory($userId, 'Grocery', $currentMonth, $householdId);
        if ($groceryBudget <= 0.00) {
            $groceryBudget = $this->budgetModel->getGroceryBudget($householdId);
        }

        // 3. Fetch total Grocery expenses already spent this month from ExpenseModel
        $spentGrocery = $this->expenseModel->getTotalExpensesByCategory($userId, 'Grocery', date('Y-m'), $householdId);

        // 4. Calculate "Remaining Budget" for groceries: Allocated Budget - Total Expenses
        $remainingBudget = round($groceryBudget - $spentGrocery, 2);

        // 5. Trigger real-time warning if live cart total surpasses the Remaining Budget
        $is_budget_exceeded = ($totalCartValue > $remainingBudget);

        // Budget progress percentage relative to remaining budget (or allocated budget)
        $budgetPercentage = $groceryBudget > 0 
            ? round((($spentGrocery + $totalCartValue) / $groceryBudget) * 100) 
            : 100;

        // 6. Fetch recommended brand alternatives for items in the cart (pure price comparison, no quantity tracking)
        $totalPotentialSavings = 0.00;

        foreach ($cartDetails['items'] as &$item) {
            $productId = (int)$item['product_id'];

            // Fetch brand alternatives sorted by effective price ASC
            $alts = $this->recommendationModel->getCartBrandAlternatives($productId);

            $hasCheaper = false;
            $maxSavings = 0.00;

            foreach ($alts as $alt) {
                $saving = (float)($alt['total_saving'] ?? 0.00);
                if ($saving > 0) {
                    $hasCheaper = true;
                    if ($saving > $maxSavings) {
                        $maxSavings = $saving;
                    }
                }
            }

            $item['alternatives'] = $alts;
            $item['has_cheaper']  = $hasCheaper;
            $item['max_savings']  = $maxSavings;

            $totalPotentialSavings += $maxSavings;
        }
        unset($item);

        // Fallback recommendations map
        $recommendations = $this->recommendationModel->getRecommendationsForCart($cartDetails['items'] ?? []);

        // 7. Pass data to the cart view
        $data = [
            'cart_items'          => $cartDetails['items'] ?? [],
            'subtotal'            => $totalCartValue,
            'total_items'         => $cartDetails['total_items'] ?? 0,
            'grocery_budget'      => $groceryBudget,
            'spent_grocery'       => $spentGrocery,
            'remaining_budget'    => $remainingBudget,
            'is_budget_exceeded'  => $is_budget_exceeded,
            'budget_percentage'   => $budgetPercentage,
            'recommendations'     => $recommendations,
            'potential_savings'   => round($totalPotentialSavings, 2),
            'flash_message'       => $_SESSION['cart_flash'] ?? '',
            'flash_type'          => $_SESSION['cart_flash_type'] ?? 'success'
        ];

        unset($_SESSION['cart_flash'], $_SESSION['cart_flash_type']);

        $this->view('customer/cart', $data);
    }

    /**
     * Add an item to the cart using standard HTML POST form submission (Post-Redirect-Get).
     *
     * @param int|null $productId
     */
    public function add($productId = null) {
        $this->requireCustomer();

        $userId = (int)$_SESSION['user_id'];
        $productId = (int)($productId ?? $_POST['product_id'] ?? $_GET['product_id'] ?? 0);
        $quantity = max(1, (int)($_POST['quantity'] ?? $_GET['quantity'] ?? 1));

        if ($productId > 0) {
            $this->cartModel->addItem($userId, $productId, $quantity);
            $_SESSION['cart_flash'] = 'Item added to your cart.';
            $_SESSION['cart_flash_type'] = 'success';
        } else {
            $_SESSION['cart_flash'] = 'Could not add product (invalid product ID).';
            $_SESSION['cart_flash_type'] = 'error';
        }

        // Post-Redirect-Get: Redirect user back to Grocery Catalog page instead of /cart
        header('Location: ' . URLROOT . '/customer/grocery');
        exit();
    }

    /**
     * Alias for add()
     *
     * @param int|null $productId
     */
    public function addToCart($productId = null) {
        return $this->add($productId);
    }

    /**
     * Swap a cart item with a brand alternative.
     * Handles standard POST form submissions (Post-Redirect-Get pattern).
     */
    public function swap() {
        $this->requireCustomer();

        $userId = (int)$_SESSION['user_id'];
        $cartItemId = (int)($_POST['cart_item_id'] ?? $_POST['cart_id'] ?? 0);
        $newProductId = (int)($_POST['new_product_id'] ?? $_POST['alternative_product_id'] ?? 0);

        if ($cartItemId > 0 && $newProductId > 0) {
            $swapped = $this->cartModel->swapCartItem($cartItemId, $userId, $newProductId);
            if ($swapped) {
                $_SESSION['cart_flash'] = 'Swapped to cheaper brand successfully!';
                $_SESSION['cart_flash_type'] = 'success';
            } else {
                $_SESSION['cart_flash'] = 'Could not swap item. Please try again.';
                $_SESSION['cart_flash_type'] = 'error';
            }
        } else {
            $_SESSION['cart_flash'] = 'Invalid swap request.';
            $_SESSION['cart_flash_type'] = 'error';
        }

        header('Location: ' . URLROOT . '/customer/cart');
        exit();
    }

    /**
     * Alias for swap() for backwards compatibility.
     */
    public function switchAlternative() {
        return $this->swap();
    }

    /**
     * Remove an item from the cart.
     *
     * @param int|null $cartId
     */
    public function remove($cartId = null) {
        $this->requireCustomer();

        $cartId = (int)($cartId ?? $_POST['cart_id'] ?? $_GET['cart_id'] ?? 0);
        if ($cartId > 0) {
            $this->cartModel->removeItem($cartId);
            $_SESSION['cart_flash'] = 'Item removed from your cart.';
            $_SESSION['cart_flash_type'] = 'info';
        }

        header('Location: ' . URLROOT . '/cart');
        exit();
    }

    /**
     * Update quantity of an item in the cart.
     */
    public function update() {
        $this->requireCustomer();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cartId = (int)($_POST['cart_id'] ?? 0);
            $quantity = (int)($_POST['quantity'] ?? 1);

            if ($cartId > 0) {
                $this->cartModel->updateQuantity($cartId, $quantity);
            }
        }

        header('Location: ' . URLROOT . '/cart');
        exit();
    }

    /**
     * Clear all items from the current user's cart.
     */
    public function clear() {
        $this->requireCustomer();

        $userId = (int)$_SESSION['user_id'];
        $this->cartModel->clearCart($userId);

        header('Location: ' . URLROOT . '/cart');
        exit();
    }

    /**
     * Checkout Logic:
     * 1. Calculates the live cart total.
     * 2. Inserts this amount into the expenses table (Category: 'Grocery').
     * 3. Empties the user's cart (DELETE FROM cart_items).
     * 4. Redirects with order and budget update confirmation.
     */
    public function checkout() {
        $this->requireCustomer();

        $userId = (int)$_SESSION['user_id'];
        $householdId = $_SESSION['household_id'] ?? null;

        // 1. Calculate live cart total
        $cartDetails = $this->cartModel->getUserCartDetails($userId);
        $totalCartValue = $this->cartModel->getTotalCartValue($userId);

        if (empty($cartDetails['items']) || $totalCartValue <= 0) {
            $_SESSION['cart_flash'] = 'Your cart is empty. Please add items before checking out.';
            $_SESSION['cart_flash_type'] = 'error';
            header('Location: ' . URLROOT . '/cart');
            exit();
        }

        // Build itemized description
        $itemNames = array_column($cartDetails['items'], 'product_name');
        $desc = 'Grocery Purchase: ' . implode(', ', array_slice($itemNames, 0, 3));
        if (count($itemNames) > 3) {
            $desc .= '...';
        }

        // 2. Insert this amount into the expenses table (Category: 'Grocery')
        $this->expenseModel->addExpense(
            $userId,
            'Grocery',
            $totalCartValue,
            date('Y-m-d'),
            $desc,
            $householdId
        );

        // 3. Empty the user's cart (DELETE FROM cart_items)
        $this->cartModel->clearCart($userId);

        // 4. Confirmation message
        $_SESSION['cart_flash'] = 'Order completed successfully! $' . number_format($totalCartValue, 2) . ' has been recorded to your Grocery expenses.';
        $_SESSION['cart_flash_type'] = 'success';

        header('Location: ' . URLROOT . '/cart');
        exit();
    }
}
?>
