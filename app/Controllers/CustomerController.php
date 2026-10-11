<?php
require_once '../app/Models/Customer.php';
require_once '../app/Models/User.php';
require_once '../app/Models/Notice.php';

class CustomerController extends Controller {

    private $customerModel;
    private $userModel;
    private $noticeModel;

    public function __construct() {
        $this->customerModel = new Customer();
        $this->userModel = new User();
        $this->noticeModel = new Notice();
    }

    private function requireCustomer() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'customer') {
            header('Location: ' . URLROOT . '/customer/login');
            exit();
        }
    }

    private function setFlash($message, $type = 'success') {
        $_SESSION['customer_flash'] = $message;
        $_SESSION['customer_flash_type'] = $type;
    }

    public function index() {
        $this->view('Customer/index');
    }

    public function login() {
        $data = [
            'email' => '',
            'password' => '',
            'email_err' => '',
            'password_err' => '',
            'error' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $data['email'] = trim($_POST['email']);
            $data['password'] = trim($_POST['password']);
            
            if (empty($data['email'])) $data['email_err'] = 'Please enter email';
            if (empty($data['password'])) $data['password_err'] = 'Please enter password';
            
            if (empty($data['email_err']) && empty($data['password_err'])) {
                $loggedInUser = $this->userModel->login($data['email'], $data['password'], 'customer');
                
                if ($loggedInUser && is_array($loggedInUser) && !isset($loggedInUser['error'])) {
                    $_SESSION['user_id'] = $loggedInUser['id'];
                    $_SESSION['user_role'] = 'customer';
                    $_SESSION['user_name'] = $loggedInUser['name'];
                    
                    header('Location: ' . URLROOT . '/customer/dashboard');
                    exit();
                } else {
                    $data['error'] = 'Password incorrect or user not found';
                }
            }
        }

        $this->view('Customer/customer-login', $data);
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            // Example Admin-style registration handler
            $owner = [
                'name' => trim($_POST['name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'password' => trim($_POST['password'] ?? ''),
                'nic' => trim($_POST['nic'] ?? ''),
                'gender' => trim($_POST['gender'] ?? ''),
                'age' => trim($_POST['age'] ?? ''),
                'income' => trim($_POST['income'] ?? ''),
                'savings' => trim($_POST['savings'] ?? '')
            ];
            
            if (empty($owner['name']) || empty($owner['email']) || empty($owner['password'])) {
                $this->setFlash('Please fill out all required fields.', 'error');
                $this->view('Customer/create-account', ['owner' => $owner]);
                return;
            }
            
            if ($this->customerModel->emailExists($owner['email'])) {
                $this->setFlash('Email already registered.', 'error');
                $this->view('Customer/create-account', ['owner' => $owner]);
                return;
            }
            
            // Register household and owner
            $ownerId = $this->customerModel->registerHousehold($owner, []);
            if ($ownerId) {
                $this->setFlash('Account created successfully. Please login.');
                header('Location: ' . URLROOT . '/customer/login');
                exit();
            } else {
                $this->setFlash('Failed to create account.', 'error');
            }
        }
        $this->view('Customer/create-account');
    }

    public function logout() {
        unset($_SESSION['user_id'], $_SESSION['user_email'], $_SESSION['user_name'], $_SESSION['user_role']);
        session_destroy();
        header('Location: ' . URLROOT . '/customer/login');
        exit();
    }

    public function dashboard() {
        $this->requireCustomer();
        $data = [
            'notices' => $this->noticeModel->getActiveForAudience('customer'),
            'flash' => $_SESSION['customer_flash'] ?? '',
            'flash_type' => $_SESSION['customer_flash_type'] ?? 'success'
        ];
        unset($_SESSION['customer_flash'], $_SESSION['customer_flash_type']);
        $this->view('Customer/dashboard', $data);
    }

    public function budgets()       { $this->requireCustomer(); $this->view('Customer/budgets'); }
    
    public function grocery()       { $this->requireCustomer(); $this->view('Customer/grocery'); }
    public function cart()          { $this->requireCustomer(); $this->view('Customer/cart'); }
    public function analytics()     { $this->requireCustomer(); $this->view('Customer/analytics'); }
    public function household()     { $this->requireCustomer(); $this->view('Customer/household'); }
    public function notifications() { $this->requireCustomer(); $this->view('Customer/notifications'); }
    public function settings()      { $this->requireCustomer(); $this->view('Customer/settings'); }
    public function product()       { $this->requireCustomer(); $this->view('Customer/product'); }
    public function privacy()       { $this->view('Customer/privacy'); }
    public function terms()         { $this->view('Customer/terms'); }
    public function addExpense() {
        $this->requireCustomer();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Placeholder for DB logic
            header('Location: ' . URLROOT . '/customer/dashboard?msg=ExpenseAdded');
            exit();
        }
        $this->view('customer/addExpense');
    }
    public function product_detail() {
        $this->requireCustomer();
        $this->view('customer/product_detail');
    }
}


?>
