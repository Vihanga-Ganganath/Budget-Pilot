<?php
// 1. මුලින්ම User Model ෆයිල් එක ලෝඩ් කරගන්නවා
require_once '../app/Models/User.php';
require_once '../app/Models/Notice.php';
require_once '../app/Models/SecurityAudit.php';

class AdminController extends Controller {

    private $userModel;
    private $noticeModel;

    // 2. Constructor එකක් හදලා ඒක ඇතුළේ User Model එකෙන් ඔබ්ජෙක්ට් එකක් හදාගන්නවා
    public function __construct() {
        $this->userModel = new User();
        $this->noticeModel = new Notice();
    }
    // Default method (If someone just types /admin)
    public function index() { $this->dashboard(); }

    public function dashboard() {
        $this->requireAdmin();
        require_once '../app/Models/AdminDashboard.php';
        $model=new AdminDashboard();
        $data=$model->getData();
        $data['userName']=$_SESSION['user_name'] ?? 'Admin';
        $this->view('admin/dashboard',$data);
    }

    public function users() {
        $this->requireAdmin();
        $search = trim(substr((string)($_GET['q'] ?? ''),0,100));
        $role = (string)($_GET['role'] ?? 'all');
        $status = (string)($_GET['status'] ?? 'all');
        $limit = 10; $page = max(1,(int)($_GET['page'] ?? 1));
        $stats = $this->userModel->getUserStats();
        $result = $this->userModel->searchManagedUsers($search,$role,$status,$limit,($page-1)*$limit);
        $pages = max(1,(int)ceil($result['total']/$limit));
        $page = min($page,$pages);
        if ($page !== max(1,(int)($_GET['page'] ?? 1))) $result=$this->userModel->searchManagedUsers($search,$role,$status,$limit,($page-1)*$limit);
        $data = ['users'=>$result['users'],'stats'=>$stats,'currentPage'=>$page,'totalPages'=>$pages,'limit'=>$limit,'offset'=>($page-1)*$limit,'totalUsers'=>$result['total'],'search'=>$search,'roleFilter'=>$role,'statusFilter'=>$status,
            'flash'=>$_SESSION['admin_flash'] ?? '', 'flash_type'=>$_SESSION['admin_flash_type'] ?? 'success', 'modal_error'=>$_SESSION['admin_modal_error'] ?? ''];
        unset($_SESSION['admin_flash'],$_SESSION['admin_flash_type'],$_SESSION['admin_modal_error']);
        if (empty($_SESSION['user_manage_csrf'])) $_SESSION['user_manage_csrf']=bin2hex(random_bytes(32));
        $this->view('admin/admin_user_management',$data);
    }

    public function manageUser() {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !hash_equals($_SESSION['user_manage_csrf'] ?? '', (string)($_POST['csrf'] ?? ''))) {
            http_response_code(403); exit('Invalid request or security token.');
        }
        $id=filter_var($_POST['id'] ?? null,FILTER_VALIDATE_INT);
        $action=(string)($_POST['action'] ?? '');
        $existing=$id ? $this->userModel->findManagedUser($id) : false;
        $message='User not found.'; $type='error';
        if ($existing) {
            try {
                if ($action === 'update') {
                    $role=(string)($_POST['role'] ?? ''); $status=(string)($_POST['status'] ?? '');
                    if (!in_array($role,['admin','customer','supplier'],true) || !in_array($status,['active','suspended','locked','pending'],true)) {
                        $message='Invalid role or status.';
                    } elseif ($existing['role']==='admin' && $role!=='admin') {
                        $message='Admin accounts cannot be changed to another role.';
                    } elseif ((int)$id === (int)$_SESSION['user_id'] && $status!=='active') {
                        $message='You cannot disable your own admin account.';
                    } else {
                        $this->userModel->updateManagedUser($id,$role,$status);
                        $message='User updated successfully.'; $type='success';
                    }
                } elseif ($action === 'delete') {
                    if ($existing['role']==='admin') $message='Admin accounts cannot be deleted.';
                    else { $this->userModel->deleteManagedUser($id); $message='User deleted successfully.'; $type='success'; }
                }
            } catch (PDOException $e) { $message='Unable to save. The account may have related records or the role/email combination may already exist.'; }
        }
        $_SESSION['admin_flash']=$message; $_SESSION['admin_flash_type']=$type;
        header('Location: '.URLROOT.'/admin/users'); exit();
    }

    // ─── Add Admin User ───────────────────────────────────────────────────────
    public function addAdmin() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
            header('Location: ' . URLROOT . '/admin/login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URLROOT . '/admin/users');
            exit();
        }

        $name     = trim($_POST['full_name']  ?? '');
        $email    = trim($_POST['email']      ?? '');
        $password = $_POST['password']        ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';

        // ── Server-side validation ────────────────────────────────────────────
        $errors = [];

        if (empty($name)) {
            $errors[] = 'Full name is required.';
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'A valid email address is required.';
        }
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        }
        if ($password !== $confirm) {
            $errors[] = 'Passwords do not match.';
        }

        if (!empty($errors)) {
            $_SESSION['admin_modal_error'] = implode(' ', $errors);
            header('Location: ' . URLROOT . '/admin/users?open_modal=1');
            exit();
        }

        // ── Persist ───────────────────────────────────────────────────────────
        $result = $this->userModel->createAdmin([
            'name'          => $name,
            'email'         => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        if ($result === true) {
            $_SESSION['admin_flash']      = "Admin account for <strong>{$name}</strong> ({$email}) has been created successfully.";
            $_SESSION['admin_flash_type'] = 'success';
        } elseif ($result === 'duplicate') {
            $_SESSION['admin_flash']      = "An admin account already exists for <strong>{$email}</strong>. No changes were made.";
            $_SESSION['admin_flash_type'] = 'warning';
        } else {
            $_SESSION['admin_flash']      = 'Something went wrong. Please try again.';
            $_SESSION['admin_flash_type'] = 'error';
        }

        header('Location: ' . URLROOT . '/admin/users');
        exit();
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
        $this->requireAdmin();
        require_once '../app/Models/AdminProductModeration.php';
        $model=new AdminProductModeration();
        $search=trim(substr((string)($_GET['q']??''),0,100));
        $status=(string)($_GET['status']??'Pending');
        if (!in_array($status,['all','Pending','Approved','Rejected'],true)) $status='Pending';
        $category=max(0,(int)($_GET['category']??0));
        $sort=($_GET['sort']??'newest')==='oldest'?'oldest':'newest';
        $incomplete=($_GET['incomplete']??'')==='1';
        if (empty($_SESSION['product_moderation_csrf'])) $_SESSION['product_moderation_csrf']=bin2hex(random_bytes(32));
        $filters=[$search,$status,$category,$incomplete,$sort];
        if (($_GET['export']??'')==='csv') {
            $this->requireAdmin();
            $rows=$model->listing(...array_merge($filters,[1,10000]))['rows'];
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="product-moderation.csv"');
            $output=fopen('php://output','w');
            fputcsv($output,['ID','Product','SKU(s)','Category','Supplier','Supplier Email','Status','Created']);
            foreach($rows as $r) { $cells=[$r['id'],$r['name'],$r['skus'],$r['category_name'],$r['supplier_name'],$r['supplier_email'],$r['admin_verification'],$r['created_at']]; foreach($cells as &$cell) { $cell=(string)$cell; if (preg_match('/^[\\s]*[=+@-]/u',$cell)) $cell="'".$cell; } unset($cell); fputcsv($output,$cells); }
            fclose($output);exit;
        }
        $data=['stats'=>$model->stats(),'categories'=>$model->categories(),'filters'=>['q'=>$search,'status'=>$status,'category'=>$category,'incomplete'=>$incomplete,'sort'=>$sort],
            'results'=>$model->listing(...array_merge($filters,[max(1,(int)($_GET['page']??1))])),
            'flash'=>$_SESSION['product_moderation_flash']??'','flash_type'=>$_SESSION['product_moderation_flash_type']??'success',
            'csrf'=>$_SESSION['product_moderation_csrf']];
        unset($_SESSION['product_moderation_flash'],$_SESSION['product_moderation_flash_type']);
        $this->view('admin/admin_product_moderation',$data);
    }

    public function moderateProduct() {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD']!=='POST' || empty($_SESSION['product_moderation_csrf']) ||
            !hash_equals($_SESSION['product_moderation_csrf'],(string)($_POST['csrf']??''))) {
            http_response_code(403);exit('Invalid request or security token.');
        }
        $id=filter_var($_POST['id']??null,FILTER_VALIDATE_INT);
        $action=(string)($_POST['action']??'');
        $type='error';$message='Invalid moderation request.';
        if ($id && in_array($action,['approve','reject'],true)) {
            try {
                require_once '../app/Models/AdminProductModeration.php';
                $message=(new AdminProductModeration())->moderate((int)$id,$action);$type='success';
            } catch(Throwable $e) {$message=$e instanceof RuntimeException?$e->getMessage():'Could not update product. Please check the database.';error_log('Product moderation: '.$e->getMessage());}
        }
        $_SESSION['product_moderation_flash']=$message;
        $_SESSION['product_moderation_flash_type']=$type;
        header('Location: '.URLROOT.'/admin/products');exit;
    }

    public function security() {
        $this->requireSecurityAdmin();
        try {
            $audit = new SecurityAudit();
            $data = $audit->overview();
            foreach ($data['alerts'] as &$alert) {
                $resolvedAt = $audit->resolved($alert['email'],$alert['role'],$alert['ip_address']);
                $alert['resolved'] = $resolvedAt && strtotime($resolvedAt) >= strtotime($alert['last_attempt']);
            }
            unset($alert);
            $data['error'] = '';
        } catch (Throwable $e) {
            error_log('Security page: '.$e->getMessage());
            $data = ['error'=>'Security audit tables are not available. Import security_audit_migration.sql first.', 'failed'=>0,'resets'=>0,'sessions'=>0,'alerts'=>[],'recent'=>[]];
        }
        $data['flash'] = $_SESSION['security_flash'] ?? '';
        unset($_SESSION['security_flash']);
        if (empty($_SESSION['security_csrf'])) $_SESSION['security_csrf'] = bin2hex(random_bytes(32));
        $this->view('admin/security', $data);
    }
    private function requireSecurityAdmin() {
        if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
            header('Location: '.URLROOT.'/admin/login'); exit();
        }
    }
    private function securityPost() {
        $this->requireSecurityAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['security_csrf']) || !hash_equals($_SESSION['security_csrf'], (string)($_POST['csrf'] ?? ''))) {
            http_response_code(403); exit('Invalid request');
        }
    }
    public function resolveSecurityAlert() {
        $this->securityPost();
        $email=trim($_POST['email'] ?? ''); $role=trim($_POST['role'] ?? ''); $ip=trim($_POST['ip'] ?? '');
        if ($email !== '' && in_array($role,['admin','supplier','customer'],true) && filter_var($ip,FILTER_VALIDATE_IP)) {
            (new SecurityAudit())->resolve($email,$role,$ip,(int)$_SESSION['user_id']);
            $_SESSION['security_flash']='Alert marked as reviewed. New failures will appear again.';
        }
        header('Location: '.URLROOT.'/admin/security');exit();
    }
    public function revokeSecuritySessions() {
        $this->securityPost();
        $count=(new SecurityAudit())->revokeOthers((int)$_SESSION['user_id']);
        $_SESSION['security_flash']=$count.' tracked sessions revoked (excluding your account).';
        header('Location: '.URLROOT.'/admin/security');exit();
    }
    public function exportSecurityAudit() {
        $this->requireSecurityAdmin();
        $rows=(new SecurityAudit())->exportRows();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="security-audit.csv"');
        header('Cache-Control: no-store');
        $out=fopen('php://output','w');
        fputcsv($out,['Date and time','Email','Role','IP','Browser','Result']);
        foreach($rows as $row) {
            foreach($row as &$value) { $value=(string)$value; if (preg_match('/^[=+@\-\t\r]/',$value)) $value="'".$value; } unset($value);
            $row['successful']=$row['successful'] ? 'Success':'Failed';
            fputcsv($out,array_values($row));
        }
        fclose($out);exit();
    }

    public function reports() {
        if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
            header('Location: '.URLROOT.'/admin/login'); exit();
        }
        require_once '../app/Models/AdminReports.php';
        $days = filter_input(INPUT_GET, 'days', FILTER_VALIDATE_INT);
        $data = (new AdminReports())->get($days ?: 30);
        $this->view('admin/admin_reports', $data);
    }

    public function settings() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
            header('Location: ' . URLROOT . '/admin/login');
            exit();
        }

        $userId  = (int) $_SESSION['user_id'];
        $profile = $this->userModel->getAdminProfile($userId);
        $avatar  = $this->userModel->getAvatarByUserId($userId);

        $data = [
            'profile'    => $profile,
            'avatar'     => $avatar,
            'flash'      => $_SESSION['admin_flash']      ?? '',
            'flash_type' => $_SESSION['admin_flash_type'] ?? 'success',
        ];
        unset($_SESSION['admin_flash'], $_SESSION['admin_flash_type']);

        $this->view('admin/admin_settings', $data);
    }

    // ─── Avatar: Upload ───────────────────────────────────────────────────────
    public function uploadAvatar() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
            header('Location: ' . URLROOT . '/admin/login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['avatar'])) {
            header('Location: ' . URLROOT . '/admin/settings');
            exit();
        }

        $file    = $_FILES['avatar'];
        $userId  = (int) $_SESSION['user_id'];
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 3 * 1024 * 1024; // 3 MB

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['admin_flash']      = 'Upload failed. Please try again.';
            $_SESSION['admin_flash_type'] = 'error';
            header('Location: ' . URLROOT . '/admin/settings');
            exit();
        }

        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowed)) {
            $_SESSION['admin_flash']      = 'Only JPEG, PNG, GIF and WebP images are allowed.';
            $_SESSION['admin_flash_type'] = 'error';
            header('Location: ' . URLROOT . '/admin/settings');
            exit();
        }
        if ($file['size'] > $maxSize) {
            $_SESSION['admin_flash']      = 'Image must be under 3 MB.';
            $_SESSION['admin_flash_type'] = 'error';
            header('Location: ' . URLROOT . '/admin/settings');
            exit();
        }

        // Delete old avatar file if one exists
        $oldAvatar = $this->userModel->getAvatarByUserId($userId);
        if ($oldAvatar) {
            $oldPath = dirname(APPROOT) . '/public/assets/avatars/admin/' . $oldAvatar;
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        // Build unique filename and save
        $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = 'admin_' . $userId . '_' . time() . '.' . $ext;
        $dest     = dirname(APPROOT) . '/public/assets/avatars/admin/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $dest)) {
            $this->userModel->updateAdminAvatar($userId, $filename);
            $_SESSION['admin_flash']      = 'Profile picture updated successfully.';
            $_SESSION['admin_flash_type'] = 'success';
        } else {
            $_SESSION['admin_flash']      = 'Could not save the file. Check folder permissions.';
            $_SESSION['admin_flash_type'] = 'error';
        }

        header('Location: ' . URLROOT . '/admin/settings');
        exit();
    }

    // ─── Avatar: Delete ───────────────────────────────────────────────────────
    public function deleteAvatar() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
            header('Location: ' . URLROOT . '/admin/login');
            exit();
        }

        $userId    = (int) $_SESSION['user_id'];
        $oldAvatar = $this->userModel->getAvatarByUserId($userId);

        if ($oldAvatar) {
            $oldPath = dirname(APPROOT) . '/public/assets/avatars/admin/' . $oldAvatar;
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
            $this->userModel->clearAdminAvatar($userId);
            $_SESSION['admin_flash']      = 'Profile picture removed.';
            $_SESSION['admin_flash_type'] = 'success';
        } else {
            $_SESSION['admin_flash']      = 'No profile picture to remove.';
            $_SESSION['admin_flash_type'] = 'warning';
        }

        header('Location: ' . URLROOT . '/admin/settings');
        exit();
    }


    // ─── Admin: Notice Management CRUD ───────────────────────────────────────
    private function requireAdmin() {
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
            header('Location: ' . URLROOT . '/admin/login');
            exit();
        }
    }

    public function notices() {
        $this->requireAdmin();
        $data = [
            'notices' => $this->noticeModel->getAll(),
            'stats' => $this->noticeModel->getStats(),
            'flash' => $_SESSION['admin_flash'] ?? '',
            'flash_type' => $_SESSION['admin_flash_type'] ?? 'success'
        ];
        unset($_SESSION['admin_flash'], $_SESSION['admin_flash_type']);
        $this->view('admin/admin_notice_management', $data);
    }

    private function noticePayload() {
        $audience = $_POST['audience'] ?? 'all';
        $status = $_POST['status'] ?? 'active';
        return [
            'title' => trim($_POST['title'] ?? ''),
            'message' => trim($_POST['message'] ?? ''),
            'audience' => in_array($audience, ['customer','supplier','all'], true) ? $audience : 'all',
            'status' => in_array($status, ['active','inactive'], true) ? $status : 'active',
            'expires_at' => !empty($_POST['expires_at']) ? $_POST['expires_at'] : null,
        ];
    }

    public function createNotice() {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: '.URLROOT.'/admin/notices'); exit(); }
        $data = $this->noticePayload();
        if ($data['title'] === '' || $data['message'] === '') {
            $_SESSION['admin_flash']='Title and message are required.'; $_SESSION['admin_flash_type']='error';
        } else {
            $data['created_by'] = (int)$_SESSION['user_id'];
            $ok = $this->noticeModel->create($data);
            $_SESSION['admin_flash'] = $ok ? 'Notice created successfully.' : 'Could not create the notice.';
            $_SESSION['admin_flash_type'] = $ok ? 'success' : 'error';
        }
        header('Location: '.URLROOT.'/admin/notices'); exit();
    }

    public function updateNotice() {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: '.URLROOT.'/admin/notices'); exit(); }
        $id=(int)($_POST['notice_id'] ?? 0); $data=$this->noticePayload();
        if (!$id || $data['title']==='' || $data['message']==='') {
            $_SESSION['admin_flash']='Invalid notice data.'; $_SESSION['admin_flash_type']='error';
        } else {
            $ok=$this->noticeModel->update($id,$data);
            $_SESSION['admin_flash']=$ok?'Notice updated successfully.':'Could not update the notice.';
            $_SESSION['admin_flash_type']=$ok?'success':'error';
        }
        header('Location: '.URLROOT.'/admin/notices'); exit();
    }

    public function deleteNotice() {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: '.URLROOT.'/admin/notices'); exit(); }
        $id=(int)($_POST['notice_id'] ?? 0); $ok=$id ? $this->noticeModel->delete($id) : false;
        $_SESSION['admin_flash']=$ok?'Notice deleted successfully.':'Could not delete the notice.';
        $_SESSION['admin_flash_type']=$ok?'success':'error';
        header('Location: '.URLROOT.'/admin/notices'); exit();
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
            (new SecurityAudit())->record($email, 'admin', (bool)($loggedInUser && !isset($loggedInUser['error'])), $loggedInUser['id'] ?? null);

            // Check for successful login (must be a row array, not an error array)
            if ($loggedInUser && is_array($loggedInUser) && !isset($loggedInUser['error'])) {

                session_regenerate_id(true);
                $_SESSION['user_id'] = $loggedInUser['id'];
                $_SESSION['user_email'] = $loggedInUser['email'];
                $_SESSION['user_name'] = $loggedInUser['name'];
                $_SESSION['user_role'] = $loggedInUser['role'];

                $data['success'] = 'Signed in successfully. Redirecting...';
                header('Location: ' . URLROOT . '/admin/dashboard');
                exit();
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