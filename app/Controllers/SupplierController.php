<?php
require_once '../app/Models/User.php';
require_once '../app/Models/Notice.php';
require_once '../app/Models/Product.php';

class SupplierController extends Controller {

    private $userModel;
    private $productModel;

    public function __construct() {
        $this->userModel = new User();
        $this->productModel = new Product();
    }

    // Default — redirect appropriately
    public function index() {
        if (isset($_SESSION['user_id']) && $_SESSION['user_role'] == 'supplier') {
            header('Location: ' . URLROOT . '/supplier/overview');
        } else {
            header('Location: ' . URLROOT . '/supplier/login');
        }
        exit();
    }

    // ─── Login / Logout ───────────────────────────────────────────────────────

    public function login() {
        $data = [
            'error'      => '',
            'success'    => '',
            'registered' => isset($_GET['registered']) && $_GET['registered'] == '1'
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $result = $this->userModel->login($email, $password, 'supplier');

            if (is_array($result) && isset($result['error'])) {
                // Specific account-status error from model
                if ($result['error'] === 'pending') {
                    $data['error'] = 'Your account is awaiting admin approval. You will be able to log in once approved.';
                } elseif ($result['error'] === 'suspended') {
                    $data['error'] = 'Your account has been suspended. Please contact support.';
                }
            } elseif ($result) {
                // Successful login — set session
                $_SESSION['user_id']    = $result['id'];
                $_SESSION['user_email'] = $result['email'];
                $_SESSION['user_name']  = $result['name'];
                $_SESSION['user_role']  = $result['role'];

                header('Location: ' . URLROOT . '/supplier/overview');
                exit();
            } else {
                $data['error'] = 'Invalid email or password. Please try again.';
            }
        }

        $this->view('supplier/login', $data);
    }

    public function logout() {
        unset($_SESSION['user_id'], $_SESSION['user_email'],
              $_SESSION['user_name'], $_SESSION['user_role']);
        session_destroy();
        header('Location: ' . URLROOT . '/supplier/login');
        exit();
    }

    // ─── Registration ─────────────────────────────────────────────────────────

    public function register() {
        $data = [
            'errors'  => [],
            'success' => '',
            // Keep field values on validation failure
            'name'         => '',
            'email'        => '',
            'phone'        => '',
            'company_name' => '',
            'description'  => '',
            'category'     => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitise inputs
            $data['name']         = trim($_POST['name']         ?? '');
            $data['email']        = trim($_POST['email']        ?? '');
            $data['phone']        = trim($_POST['phone']        ?? '');
            $data['company_name'] = trim($_POST['company_name'] ?? '');
            $data['description']  = trim($_POST['description']  ?? '');
            $data['category']     = trim($_POST['category']     ?? '');
            $password             = $_POST['password']          ?? '';
            $password_confirm     = $_POST['password_confirm']  ?? '';
            $terms                = isset($_POST['terms']);

            // ── Validation ────────────────────────────────────────────────────
            if (empty($data['name'])) {
                $data['errors']['name'] = 'Full name is required.';
            }

            if (empty($data['email'])) {
                $data['errors']['email'] = 'Email address is required.';
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $data['errors']['email'] = 'Please enter a valid email address.';
            } elseif ($this->userModel->emailExistsForRole($data['email'], 'supplier')) {
                $data['errors']['email'] = 'This email is already registered as a supplier.';
            }

            if (empty($password)) {
                $data['errors']['password'] = 'Password is required.';
            } elseif (strlen($password) < 8) {
                $data['errors']['password'] = 'Password must be at least 8 characters.';
            } elseif ($password !== $password_confirm) {
                $data['errors']['password'] = 'Passwords do not match.';
            }

            if (empty($data['company_name'])) {
                $data['errors']['company_name'] = 'Company / brand name is required.';
            }

            if (empty($data['category'])) {
                $data['errors']['category'] = 'Please select a business category.';
            }

            if (!$terms) {
                $data['errors']['terms'] = 'You must agree to the Terms of Service.';
            }

            // ── Save if valid ─────────────────────────────────────────────────
            if (empty($data['errors'])) {
                $registerData = [
                    'name'          => $data['name'],
                    'email'         => $data['email'],
                    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                    'phone'         => $data['phone'],
                    'company_name'  => $data['company_name'],
                    'description'   => $data['description'],
                ];

                $result = $this->userModel->registerSupplier($registerData);

                if ($result === true) {
                    // Redirect to login with success notice
                    header('Location: ' . URLROOT . '/supplier/login?registered=1');
                    exit();
                } elseif ($result === 'duplicate') {
                    $data['errors']['email'] = 'This email is already registered as a supplier.';
                } else {
                    $data['errors']['general'] = 'Something went wrong. Please try again later.';
                }
            }
        }

        $this->view('supplier/register', $data);
    }

    // ─── Dashboard Pages (session-guarded) ───────────────────────────────────

    public function overview() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'supplier') {
            header('Location: ' . URLROOT . '/supplier/login');
            exit();
        }
        $noticeModel = new Notice();
        $data = [
            'notices'  => $noticeModel->getActiveForAudience('supplier'),
            // Products of the logged-in supplier (for the live stat cards)
            'products' => $this->productModel->getProductsBySupplier($_SESSION['user_id'])
        ];
        $this->view('supplier/overview', $data);
    }

    public function catalog() {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'supplier') {
        header('Location: ' . URLROOT . '/supplier/login');
        exit();
    }

    // Get products belonging to the logged-in supplier
    $products = $this->productModel->getProductsBySupplier(
        $_SESSION['user_id']
    );

    $data = [
        'products' => $products
    ];

    $this->view('supplier/catalog', $data);
}
    public function editProduct($id) {
    // Only logged-in suppliers can access this
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'supplier') {
        header('Location: ' . URLROOT . '/supplier/login');
        exit();
    }

    // Get this product only if it belongs to the logged-in supplier
    $product = $this->productModel->getProductById(
        $id,
        $_SESSION['user_id']
    );

    // Product doesn't exist or belongs to another supplier
    if (!$product) {
        header('Location: ' . URLROOT . '/supplier/catalog');
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $data = [
            'id' => $id,
            'supplier_id' => $_SESSION['user_id'],
            'category_id' => (int)($_POST['category_id'] ?? 0),
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'ingredients' => trim($_POST['ingredients'] ?? ''),
            'nutritional_value' => trim($_POST['nutritional_value'] ?? ''),
            'document_url' => trim($_POST['document_url'] ?? ''),

            'variant_name' => trim($_POST['variant_name'] ?? ''),
            'price' => $_POST['price'] ?? '',
            'stock_quantity' => $_POST['stock_quantity'] ?? '',

            'status' => $_POST['status'] ?? 'Active'
        ];

        if (!empty($data['name']) && $data['category_id'] > 0) {

            if ($this->productModel->updateProduct($data)) {

    // If supplier selected a new product image
    if (
        isset($_FILES['product_image']) &&
        $_FILES['product_image']['error'] === UPLOAD_ERR_OK
    ) {
        $file = $_FILES['product_image'];

        // Maximum file size: 5 MB
        if ($file['size'] <= 5 * 1024 * 1024) {

            // Check actual file type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            $allowedTypes = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp'
            ];

            if (isset($allowedTypes[$mimeType])) {

                $extension = $allowedTypes[$mimeType];

                $fileName =
                    'product_' .
                    $product['variant_id'] .
                    '_' .
                    uniqid() .
                    '.' .
                    $extension;

                $uploadDirectory =
                    dirname(__DIR__, 2) .
                    '/public/uploads/products/';

                $destination =
                    $uploadDirectory . $fileName;

                if (move_uploaded_file(
                    $file['tmp_name'],
                    $destination
                )) {

                    $imageUrl =
                        'uploads/products/' . $fileName;

                    $this->productModel->updateProductImage(
                        $product['variant_id'],
                        $imageUrl
                    );
                }
            }
        }
    }

    header(
        'Location: ' .
        URLROOT .
        '/supplier/catalog?updated=1'
    );
    exit();
}
        }
    }

    $data = [
        'product' => $product
    ];

    $this->view('supplier/edit-product', $data);
}
    public function deleteProduct($id = null) {

    // Only logged-in suppliers
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'supplier') {
        header('Location: ' . URLROOT . '/supplier/login');
        exit();
    }

    // Product ID must exist
    if (!$id) {
        header('Location: ' . URLROOT . '/supplier/catalog');
        exit();
    }

    $supplierId = $_SESSION['user_id'];

    if ($this->productModel->deleteProduct($id, $supplierId)) {
        header('Location: ' . URLROOT . '/supplier/catalog?deleted=1');
        exit();
    }

    header('Location: ' . URLROOT . '/supplier/catalog');
    exit();
}

    
    public function addProduct() {

    // Only logged-in suppliers can add products
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'supplier') {
        header('Location: ' . URLROOT . '/supplier/login');
        exit();
    }

    // Only accept POST requests
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $data = [
            'supplier_id'       => $_SESSION['user_id'],
            'category_id'       => $_POST['category_id'] ?? '',
            'name'              => trim($_POST['name'] ?? ''),
            'description'       => trim($_POST['description'] ?? ''),
            'ingredients'       => trim($_POST['ingredients'] ?? ''),
            'nutritional_value' => trim($_POST['nutritional_value'] ?? ''),
            'document_url'   => trim($_POST['document_url'] ?? ''),

            'variant_name'   => trim($_POST['variant_name'] ?? ''),
            'price'          => $_POST['price'] ?? '',
            // Stock field was removed from the Add Product form; default to 0
            'stock_quantity' => ($_POST['stock_quantity'] ?? '') === '' ? 0 : (int)$_POST['stock_quantity'],

            'status'         => $_POST['status'] ?? 'Active'
        ];

        // Basic required-field validation
        if (
            empty($data['name']) ||
            empty($data['category_id']) ||
            empty($data['variant_name']) ||
            $data['price'] === ''
        ) {
            header('Location: ' . URLROOT . '/supplier/catalog?error=required');
            exit();
        }

        $variantId = $this->productModel->createProduct($data);

if ($variantId) {

    // Check whether the supplier selected an image
    if (
        isset($_FILES['product_image']) &&
        $_FILES['product_image']['error'] === UPLOAD_ERR_OK
    ) {
        $file = $_FILES['product_image'];

        // Maximum size: 5 MB
        if ($file['size'] <= 5 * 1024 * 1024) {

            // Check the real MIME type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            $allowedTypes = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp'
            ];

            if (isset($allowedTypes[$mimeType])) {

                $extension = $allowedTypes[$mimeType];

                // Generate a unique safe filename
                $fileName = 'product_' . $variantId . '_' . uniqid() . '.' . $extension;

                $uploadDirectory = dirname(__DIR__, 2) . '/public/uploads/products/';
                $destination = $uploadDirectory . $fileName;

                if (move_uploaded_file($file['tmp_name'], $destination)) {

                    // This is the path stored in MySQL
                    $imageUrl = 'uploads/products/' . $fileName;

                    $this->productModel->saveProductImage(
                        $variantId,
                        $imageUrl,
                        'Front'
                    );
                }
            }
        }
    }

    header('Location: ' . URLROOT . '/supplier/catalog?added=1');
    exit();

} else {
            header('Location: ' . URLROOT . '/supplier/catalog?error=create');
            exit();
        }
    }

    // If somebody directly opens /supplier/addProduct
    header('Location: ' . URLROOT . '/supplier/catalog');
    exit();
}

    public function analytics() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'supplier') {
            header('Location: ' . URLROOT . '/supplier/login');
            exit();
        }
        $this->view('supplier/analytics');
    }

    public function verification() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'supplier') {
            header('Location: ' . URLROOT . '/supplier/login');
            exit();
        }
        $this->view('supplier/verification');
    }
}
?>
