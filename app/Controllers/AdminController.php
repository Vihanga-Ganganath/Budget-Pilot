<?php
// 1. මුලින්ම User Model ෆයිල් එක ලෝඩ් කරගන්නවා
require_once '../app/Models/User.php';

class AdminController extends Controller {

    private $userModel;

    // 2. Constructor එකක් හදලා ඒක ඇතුළේ User Model එකෙන් ඔබ්ජෙක්ට් එකක් හදාගන්නවා
    public function __construct() {
        $this->userModel = new User();
    }
    // Default method (If someone just types /admin)
    public function index() {
        $this->view('admin/dashboard');
    }

    public function dashboard() {
        // 🔒 පිටුව ආරක්ෂා කිරීම: කවුරුහරි ලොග් වෙලා නැත්නම් හෝ Admin නෙවෙයි නම්
        if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
            // එයාව බලෙන්ම Login පිටුවට Redirect කරනවා
            header('Location: ' . URLROOT . '/admin/login');
            exit(); // මෙතනින් කේතය රන් වෙන එක සම්පූර්ණයෙන්ම නවත්වනවා
        }

        // ලොග් වෙලා ඉන්න Admin කෙනෙක් නම් විතරක් Dashboard View එක ලෝඩ් කරනවා
        $this->view('admin/dashboard');
    }

    public function users() {
        // 🔒 පිටුව ආරක්ෂා කිරීම: කවුරුහරි ලොග් වෙලා නැත්නම් හෝ Admin නෙවෙයි නම්
        if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
            header('Location: ' . URLROOT . '/admin/login');
            exit();
        }

        // Model එක Load කරගැනීම
        $userModel = $this->model('User'); 
        
        // ඩේටාබේස් එකෙන් Stats ලබා ගැනීම
        $stats = $userModel->getUserStats();

        // --- Pagination ගණනය කිරීම් ---
        $limit = 6; 
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1; 
        $offset = ($currentPage - 1) * $limit; 
        
        $totalUsers = $stats->total_users;
        $totalPages = ceil($totalUsers / $limit); 

        // අදාළ පිටුවට අදාළ යූසර්ස්ලා පමණක් ලබා ගැනීම
        $users = $userModel->getUsers($limit, $offset);

        // View එකට යැවිය යුතු සියලුම දත්ත එකතු කිරීම
        $data = [
            'users' => $users,
            'stats' => $stats,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'limit' => $limit,
            'offset' => $offset,
            'totalUsers' => $totalUsers
        ];

        // View එක Load කිරීම
        $this->view('admin/admin_user_management', $data);
    }

    public function suppliers() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
            header('Location: ' . URLROOT . '/admin/login');
            exit();
        }

        $data = [
            'pending'          => $this->userModel->getPendingSuppliers(),
            'recent_decisions' => $this->userModel->getRecentSupplierDecisions(),
            'pending_count'    => $this->userModel->countPendingSuppliers(),
            'flash'            => $_SESSION['admin_flash'] ?? ''
        ];
        unset($_SESSION['admin_flash']);

        $this->view('admin/admin_supplier_approvals', $data);
    }

    public function approveSupplier() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
            header('Location: ' . URLROOT . '/admin/login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['supplier_id'])) {
            $id = (int) $_POST['supplier_id'];
            $this->userModel->updateSupplierStatus($id, 'active');
            $_SESSION['admin_flash'] = 'Supplier approved successfully.';
        }

        header('Location: ' . URLROOT . '/admin/suppliers');
        exit();
    }

    public function rejectSupplier() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
            header('Location: ' . URLROOT . '/admin/login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['supplier_id'])) {
            $id = (int) $_POST['supplier_id'];
            $this->userModel->updateSupplierStatus($id, 'suspended');
            $_SESSION['admin_flash'] = 'Supplier registration rejected.';
        }

        header('Location: ' . URLROOT . '/admin/suppliers');
        exit();
    }


    public function products() {

        // 🔒 පිටුව ආරක්ෂා කිරීම: කවුරුහරි ලොග් වෙලා නැත්නම් හෝ Admin නෙවෙයි නම්
        if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
            // එයාව බලෙන්ම Login පිටුවට Redirect කරනවා
            header('Location: ' . URLROOT . '/admin/login');
            exit(); // මෙතනින් කේතය රන් වෙන එක සම්පූර්ණයෙන්ම නවත්වනවා
        }

        // ලොග් වෙලා ඉන්න Admin කෙනෙක් නම් විතරක් admin_product_moderation View එක ලෝඩ් කරනවා

        $this->view('admin/admin_product_moderation');
    }

    public function security() {

        // 🔒 පිටුව ආරක්ෂා කිරීම: කවුරුහරි ලොග් වෙලා නැත්නම් හෝ Admin නෙවෙයි නම්
        if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
            // එයාව බලෙන්ම Login පිටුවට Redirect කරනවා
            header('Location: ' . URLROOT . '/admin/login');
            exit(); // මෙතනින් කේතය රන් වෙන එක සම්පූර්ණයෙන්ම නවත්වනවා
        }

        // ලොග් වෙලා ඉන්න Admin කෙනෙක් නම් විතරක් admin_security_audit View එක ලෝඩ් කරනවා

        $this->view('admin/admin_security_audit');
    }

    public function reports() {

        // 🔒 පිටුව ආරක්ෂා කිරීම: කවුරුහරි ලොග් වෙලා නැත්නම් හෝ Admin නෙවෙයි නම්
        if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
            // එයාව බලෙන්ම Login පිටුවට Redirect කරනවා
            header('Location: ' . URLROOT . '/admin/login');
            exit(); // මෙතනින් කේතය රන් වෙන එක සම්පූර්ණයෙන්ම නවත්වනවා
        }

        // ලොග් වෙලා ඉන්න Admin කෙනෙක් නම් විතරක් admin_reports View එක ලෝඩ් කරනවා

        $this->view('admin/admin_reports');
    }

    public function settings() {

        // 🔒 පිටුව ආරක්ෂා කිරීම: කවුරුහරි ලොග් වෙලා නැත්නම් හෝ Admin නෙවෙයි නම්
        if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
            // එයාව බලෙන්ම Login පිටුවට Redirect කරනවා
            header('Location: ' . URLROOT . '/admin/login');
            exit(); // මෙතනින් කේතය රන් වෙන එක සම්පූර්ණයෙන්ම නවත්වනවා
        }

        // ලොග් වෙලා ඉන්න Admin කෙනෙක් නම් විතරක් admin_settings View එක ලෝඩ් කරනවා

        $this->view('admin/admin_settings');
    }

    public function login() {
        // දත්ත යවන්න Array එකක් හදාගන්නවා
        $data = [
            'error' => '',
            'success' => ''
        ];

        // කවුරුහරි Form එක Submit කරලා නම් (POST Request එකක් නම්)
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            // 🔴 3. මෙන්න මෙතන තමයි ඔයා ඇහුව වෙනස!
            // Hardcode කරපු කෑල්ල අයින් කරලා, Model එක හරහා Database එකෙන් චෙක් කරනවා.
            // Role එක විදිහට 'admin' කියලත් යවනවා.
            $loggedInUser = $this->userModel->login($email, $password, 'admin');

            // Check for successful login (must be a row array, not an error array)
            if ($loggedInUser && is_array($loggedInUser) && !isset($loggedInUser['error'])) {

                $_SESSION['user_id'] = $loggedInUser['id'];
                $_SESSION['user_email'] = $loggedInUser['email'];
                $_SESSION['user_name'] = $loggedInUser['name'];
                $_SESSION['user_role'] = $loggedInUser['role'];

                $data['success'] = 'Signed in successfully. Redirecting...';
                
            } else {
                $data['error'] = 'Invalid email or password! Please try again.';
            }
        }

        // View එක ලෝඩ් කරනකොට $data ටිකත් යවනවා
        $this->view('admin/login', $data);
    }


    public function logout() {
        // Session එකේ තියෙන ඔක්කොම දත්ත මකලා දානවා
        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_role']);
        
        // මුළු Session එකම විනාශ කරලා දානවා
        session_destroy();

        // ආපහු බලෙන්ම Login පේජ් එකට යවනවා
        header('Location: ' . URLROOT . '/admin/login');
        exit();
    }
}
?>