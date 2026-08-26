<?php
require_once '../app/Models/User.php';

class SupplierController extends Controller {

    private $userModel;

    public function __construct() {
        $this->userModel = new User();
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
        $this->view('supplier/overview');
    }

    public function catalog() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'supplier') {
            header('Location: ' . URLROOT . '/supplier/login');
            exit();
        }
        $this->view('supplier/catalog');
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
