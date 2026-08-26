<?php
// Load User Model for authentication
require_once '../app/Models/User.php';

class SupplierController extends Controller {

    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    // Default method — redirect to overview if logged in, else to login
    public function index() {
        if (isset($_SESSION['user_id']) && $_SESSION['user_role'] == 'supplier') {
            header('Location: ' . URLROOT . '/supplier/overview');
        } else {
            header('Location: ' . URLROOT . '/supplier/login');
        }
        exit();
    }

    // ─── Login / Logout ──────────────────────────────────────────────────────

    public function login() {
        $data = [
            'error'   => '',
            'success' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $loggedInUser = $this->userModel->login($email, $password, 'supplier');

            if ($loggedInUser) {
                $_SESSION['user_id']    = $loggedInUser['id'];
                $_SESSION['user_email'] = $loggedInUser['email'];
                $_SESSION['user_name']  = $loggedInUser['name'];
                $_SESSION['user_role']  = $loggedInUser['role'];

                $data['success'] = 'Signed in successfully. Redirecting...';
            } else {
                $data['error'] = 'Invalid email or password. Please try again.';
            }
        }

        $this->view('supplier/login', $data);
    }

    public function logout() {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_role']);

        session_destroy();

        header('Location: ' . URLROOT . '/supplier/login');
        exit();
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
