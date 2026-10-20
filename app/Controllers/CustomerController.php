<?php
require_once '../app/Models/Customer.php';
require_once '../app/Models/User.php';
require_once '../app/Models/SecurityAudit.php';
require_once '../app/Models/Notice.php';
require_once '../app/Models/CustomerAuth.php';
require_once '../app/Models/Household.php';
require_once '../app/Models/CustomerSettings.php';
require_once '../app/Models/Budget.php';
require_once '../app/Models/Expense.php';

class CustomerController extends Controller {

    private $customerModel;
    private $userModel;
    private $noticeModel;
    private $authModel;
    private $householdModel;
    private $settingsModel;
    private $budgetModel;
    private $expenseModel;

    public function __construct() {
        $this->customerModel = new Customer();
        $this->userModel = new User();
        $this->noticeModel = new Notice();
        $this->authModel = new CustomerAuth();
        $this->householdModel = new Household();
        $this->settingsModel = new CustomerSettings();
        $this->budgetModel = new Budget();
        $this->expenseModel = new Expense();
    }

    private function requireCustomer() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'customer') {
            header('Location: ' . URLROOT . '/customer/login');
            exit();
        }
        // topbar photo (also for sessions started before the photo was added)
        if (!isset($_SESSION['user_photo'])) {
            $_SESSION['user_photo'] = $this->photoUrl($this->settingsModel->getProfile($_SESSION['user_id'])['profile_picture_url'] ?? '');
        }
    }

    private function setFlash($message, $type = 'success') {
        $_SESSION['customer_flash'] = $message;
        $_SESSION['customer_flash_type'] = $type;
    }

    public function index() {
        $this->view('Customer/index');
    }

    // ===================== LOGIN =====================
    // Tables: users (read + last_login_at), login_history (insert)
    public function login() {
        // Already logged in -> go straight to the dashboard
        if (isset($_SESSION['user_id']) && ($_SESSION['user_role'] ?? '') == 'customer') {
            header('Location: ' . URLROOT . '/customer/dashboard');
            exit();
        }

        $data = ['email' => $_COOKIE['customer_remember'] ?? '', 'error' => ''];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email    = strtolower(trim($_POST['email'] ?? ''));
            $password = $_POST['password'] ?? '';
            $data['email'] = $email;

            if ($email == '' || $password == '') {
                $data['error'] = 'Please enter your email and password.';
            } else {
                $user = $this->authModel->login($email, $password);
                (new SecurityAudit())->record($email, 'customer', is_array($user), is_array($user) ? $user['id'] : null);

                if (is_array($user)) {
                    session_regenerate_id(true);
                    $_SESSION['user_id']        = $user['id'];
                    $_SESSION['user_role']      = 'customer';
                    $_SESSION['user_name']      = $user['name'];
                    $_SESSION['household_id']   = $user['household_id'];
                    $_SESSION['household_role'] = $user['household_role'];
                    $_SESSION['user_photo']     = $this->photoUrl($this->settingsModel->getProfile($user['id'])['profile_picture_url']);

                    // "Remember me" only remembers the email (30 days)
                    if (!empty($_POST['remember'])) {
                        setcookie('customer_remember', $email, time() + 30 * 24 * 3600, '/');
                    } else {
                        setcookie('customer_remember', '', time() - 3600, '/');
                    }

                    header('Location: ' . URLROOT . '/customer/dashboard');
                    exit();
                }
                $data['error'] = $user; // the error message from the model
            }
        }

        $this->view('Customer/customer-login', $data);
    }

    // ===================== CREATE ACCOUNT =====================
    // Tables: households, users, user_profiles (all insert)
    // Family members are added later on the Household page.
    public function register() {
        $data = ['error' => '', 'old' => $_POST];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $owner = [
                'name'     => trim($_POST['name'] ?? ''),
                'email'    => strtolower(trim($_POST['email'] ?? '')),
                'password' => $_POST['password'] ?? '',
                'nic'      => strtoupper(trim($_POST['nic'] ?? '')),
                'gender'   => ($_POST['gender'] ?? '') != '' ? $_POST['gender'] : null,
                'age'      => ($_POST['age'] ?? '') != '' ? (int) $_POST['age'] : null,
                'income'   => (float) ($_POST['income'] ?? 0),
                'savings'  => (float) ($_POST['savings'] ?? 0),
                'photo'    => null
            ];

            $error = $this->checkSignup($owner);

            if ($error == '') {
                $owner['photo'] = $this->savePhoto($_FILES['photo'] ?? null); // optional
                if ($this->authModel->register($owner)) {
                    header('Location: ' . URLROOT . '/customer/login?registered=1');
                    exit();
                }
                $error = 'Could not create the account. Please try again.';
            }
            $data['error'] = $error;
        }

        $this->view('Customer/create-account', $data);
    }

    // Returns an error message, or '' when everything is fine.
    private function checkSignup($owner) {
        if ($owner['name'] == '' || $owner['email'] == '' || $owner['password'] == '' || $owner['nic'] == '') {
            return 'Please fill your name, email, password and NIC.';
        }
        if (!filter_var($owner['email'], FILTER_VALIDATE_EMAIL)) return 'Please enter a valid email address.';
        if (strlen($owner['password']) < 8) return 'Password must be at least 8 characters.';
        if ($owner['age'] !== null && ($owner['age'] < 13 || $owner['age'] > 120)) return 'Age must be between 13 and 120.';
        if ($owner['gender'] !== null && !in_array($owner['gender'], ['male', 'female'])) return 'Please select a gender.';
        if ($owner['income'] < 0 || $owner['savings'] < 0) return 'Income and savings cannot be negative.';
        if (empty($_POST['terms'])) return 'Please agree to the Terms of Service and Privacy Policy.';
        if ($this->authModel->emailExists($owner['email'])) return 'This email is already registered.';
        return '';
    }

    // Saves an uploaded photo in public/assets/img/avatars and returns the file name
    // (stored in user_profiles.profile_picture_url). Returns null when there is no photo.
    private function savePhoto($file) {
        if (!$file || $file['error'] != UPLOAD_ERR_OK) return null;
        if ($file['size'] > 2 * 1024 * 1024 || !getimagesize($file['tmp_name'])) return null;

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) return null;

        $fileName = 'u_' . time() . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
        $folder = APPROOT . '/../public/assets/img/avatars/';
        return move_uploaded_file($file['tmp_name'], $folder . $fileName) ? $fileName : null;
    }

    // Full link to a profile photo (for the topbar). Returns '' when there is no photo.
    private function photoUrl($file) {
        if (empty($file)) return '';
        if (strpos($file, '/') !== false) return URLROOT . '/public/' . $file; // old photos keep a path
        return URLROOT . '/public/assets/img/avatars/' . $file;
    }

    // ===================== HOUSEHOLD =====================
    // Tables: users, user_profiles, budgets, transactions
    // Only the account holder (head) can add, edit or remove family members.
    public function household() {
        $this->requireCustomer();
        $householdId = $_SESSION['household_id'];
        $isHead = ($_SESSION['household_role'] == 'head');
        $data = ['form' => '', 'old' => [], 'error' => ''];

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $isHead) {
            $id = (int) ($_POST['id'] ?? 0);

            if (($_POST['action'] ?? '') == 'remove') {
                $this->householdModel->removeMember($id, $householdId);
                $this->setFlash('Family member removed.');
                header('Location: ' . URLROOT . '/customer/household');
                exit();
            }

            // action = save (add when id is 0, otherwise edit)
            $name     = trim($_POST['name'] ?? '');
            $email    = strtolower(trim($_POST['email'] ?? ''));
            $password = $_POST['password'] ?? '';
            $error    = $this->checkMember($id, $name, $email, $password);

            if ($error == '') {
                if ($id == 0) {
                    $this->householdModel->addMember($householdId, $name, $email, $password);
                    $this->setFlash('Family member added. They can now log in with this email.');
                } else {
                    $this->householdModel->updateMember($id, $householdId, $name, $email, $password);
                    $this->setFlash('Family member updated.');
                }
                header('Location: ' . URLROOT . '/customer/household');
                exit();
            }
            $data['form']  = ($id == 0) ? 'add' : 'edit';
            $data['old']   = ['id' => $id, 'name' => $name, 'email' => $email];
            $data['error'] = $error;
        }

        // ?add=1 opens the empty form, ?edit=ID opens it filled
        if ($isHead && isset($_GET['add'])) {
            $data['form'] = 'add';
        }
        if ($isHead && isset($_GET['edit'])) {
            $member = $this->householdModel->getMember((int) $_GET['edit'], $householdId);
            if ($member) {
                $data['form'] = 'edit';
                $data['old']  = $member;
            }
        }

        $data['isHead']  = $isHead;
        $data['members'] = $this->householdModel->getMembers($householdId);
        $data['summary'] = $this->householdModel->getSummary($householdId);
        $data['flash']   = $_SESSION['customer_flash'] ?? '';
        unset($_SESSION['customer_flash'], $_SESSION['customer_flash_type']);

        $this->view('Customer/household', $data);
    }

    // Returns an error message, or '' when the member form is fine.
    private function checkMember($id, $name, $email, $password) {
        if ($name == '' || $email == '') return 'Please enter the name and email.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return 'Please enter a valid email address.';
        if ($id == 0 && $password == '') return 'Please enter a password for the new member.';
        if ($password != '' && strlen($password) < 8) return 'Password must be at least 8 characters.';
        if ($this->householdModel->emailUsed($email, $id)) return 'This email is already registered.';
        return '';
    }

    // ===================== SETTINGS =====================
    // Tables: users, user_profiles
    public function settings() {
        $this->requireCustomer();
        $userId = $_SESSION['user_id'];
        $data = ['error' => '', 'pwError' => '', 'tfaError' => ''];
        $user = $this->settingsModel->getProfile($userId);

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && ($_POST['form'] ?? '') == 'profile') {
            $p = [
                'name'     => trim($_POST['name'] ?? ''),
                'email'    => strtolower(trim($_POST['email'] ?? '')),
                'nic'      => strtoupper(trim($_POST['nic'] ?? '')),
                'age'      => ($_POST['age'] ?? '') != '' ? (int) $_POST['age'] : null,
                'gender'   => ($_POST['gender'] ?? '') != '' ? $_POST['gender'] : null,
                'income'   => (float) ($_POST['income'] ?? 0),
                'savings'  => (float) ($_POST['savings'] ?? 0),
                'currency' => $_POST['currency'] ?? 'USD',
                'photo'    => null
            ];
            $error = $this->checkProfile($userId, $p);

            if ($error == '') {
                $p['photo'] = $this->savePhoto($_FILES['photo'] ?? null);
                $this->settingsModel->updateProfile($userId, $p);
                $_SESSION['user_name'] = $p['name']; // topbar shows the new name
                if ($p['photo']) $_SESSION['user_photo'] = $this->photoUrl($p['photo']);
                if ($_SESSION['household_role'] == 'head') {
                    $this->budgetModel->setSavingsGoal($_SESSION['household_id'], $p['savings']); // savings_goals table
                }
                if (!$p['photo'] && !empty($_FILES['photo']['name'])) {
                    $this->setFlash('Profile saved, but the photo was not: use a JPG, PNG or WEBP image under 2 MB.');
                } else {
                    $this->setFlash('Your profile has been saved.');
                }
                header('Location: ' . URLROOT . '/customer/settings');
                exit();
            }
            $data['error'] = $error;
            // show what the user typed, not the old saved values
            $user = array_merge($user, $p, ['monthly_income' => $p['income'], 'financial_goals' => $p['savings'],
                                            'preferred_currency' => $p['currency']]);
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && ($_POST['form'] ?? '') == 'password') {
            $current = $_POST['current'] ?? '';
            $new     = $_POST['new'] ?? '';

            if (!password_verify($current, $user['password_hash'])) {
                $data['pwError'] = 'Current password is incorrect.';
            } elseif (strlen($new) < 8) {
                $data['pwError'] = 'New password must be at least 8 characters.';
            } elseif ($new != ($_POST['confirm'] ?? '')) {
                $data['pwError'] = 'New passwords do not match.';
            } else {
                $this->settingsModel->updatePassword($userId, $new);
                $this->setFlash('Your password has been changed.');
                header('Location: ' . URLROOT . '/customer/settings');
                exit();
            }
        }

        // Two-factor ON: 6-digit PIN + confirm + current password
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && ($_POST['form'] ?? '') == 'tfa_on') {
            $pin = $_POST['pin'] ?? '';

            if (!password_verify($_POST['password'] ?? '', $user['password_hash'])) {
                $data['tfaError'] = 'Current password is incorrect.';
            } elseif (!preg_match('/^[0-9]{6}$/', $pin)) {
                $data['tfaError'] = 'The PIN must be exactly 6 digits.';
            } elseif (in_array($pin, ['123456', '654321']) || preg_match('/^(\d)\1{5}$/', $pin)) {
                $data['tfaError'] = 'This PIN is too easy to guess. Choose another one.';
            } elseif ($pin != ($_POST['pin_confirm'] ?? '')) {
                $data['tfaError'] = 'The two PINs do not match.';
            } else {
                $this->settingsModel->enableTwoFactor($userId, $pin);
                $this->setFlash('Two-factor authentication is on. Forgot Password will now ask for your PIN.');
                header('Location: ' . URLROOT . '/customer/settings');
                exit();
            }
        }

        // Two-factor OFF: current password only
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && ($_POST['form'] ?? '') == 'tfa_off') {
            if (!password_verify($_POST['password'] ?? '', $user['password_hash'])) {
                $data['tfaError'] = 'Current password is incorrect.';
            } else {
                $this->settingsModel->disableTwoFactor($userId);
                $this->setFlash('Two-factor authentication is off.');
                header('Location: ' . URLROOT . '/customer/settings');
                exit();
            }
        }

        $data['user']   = $user;
        $data['people'] = $this->householdModel->getMembers($_SESSION['household_id']);
        $data['flash']  = $_SESSION['customer_flash'] ?? '';
        unset($_SESSION['customer_flash'], $_SESSION['customer_flash_type']);

        $this->view('Customer/settings', $data);
    }

    // Returns an error message, or '' when the profile form is fine.
    private function checkProfile($userId, $p) {
        if ($p['name'] == '' || $p['email'] == '') return 'Name and email cannot be empty.';
        if (!filter_var($p['email'], FILTER_VALIDATE_EMAIL)) return 'Please enter a valid email address.';
        if ($p['age'] !== null && ($p['age'] < 13 || $p['age'] > 120)) return 'Age must be between 13 and 120.';
        if ($p['gender'] !== null && !in_array($p['gender'], ['male', 'female', 'other', 'prefer_not_to_say'])) return 'Please select a gender.';
        if ($p['income'] < 0 || $p['savings'] < 0) return 'Income and savings goal cannot be negative.';
        if (!in_array($p['currency'], ['USD', 'EUR', 'GBP', 'LKR', 'INR', 'AUD'])) return 'Please select a currency.';
        if ($this->householdModel->emailUsed($p['email'], $userId)) return 'This email is already registered.';
        return '';
    }

    // ===================== FORGOT PASSWORD =====================
    // Tables: users (read, password_hash update), password_resets
    // Step 1: email.  Step 2: PIN (only if two-factor is on).  Step 3: new password.
    // 5 wrong PINs = blocked for 15 minutes.
    public function forgot() {
        $data = ['step' => 'email', 'email' => '', 'error' => ''];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $step = $_POST['step'] ?? 'email';

            if ($step == 'email' || $step == 'pin') {
                $email = strtolower(trim($_POST['email'] ?? ''));
                $data['email'] = $email;
                $user = $this->authModel->findByEmail($email);

                if (!$user) {
                    $data['error'] = 'No account was found with this email.';
                } elseif ($this->authModel->isResetBlocked($user['id'])) {
                    $data['error'] = 'Too many wrong PINs. Please try again in 15 minutes.';
                } elseif ($user['two_factor_enabled'] && $step == 'email') {
                    $data['step'] = 'pin'; // two-factor is on -> ask for the PIN
                } elseif ($user['two_factor_enabled'] && !password_verify($_POST['pin'] ?? '', $user['two_factor_pin_hash'])) {
                    $this->authModel->addWrongPin($user['id']);
                    $data['step']  = 'pin';
                    $data['error'] = 'The security PIN is incorrect.';
                } else {
                    $_SESSION['reset_user_id'] = $user['id']; // checked -> allow a new password
                    $data['step'] = 'password';
                }
            }

            if ($step == 'password' && isset($_SESSION['reset_user_id'])) {
                $new = $_POST['new'] ?? '';
                if (strlen($new) < 8) {
                    $data['error'] = 'Password must be at least 8 characters.';
                } elseif ($new != ($_POST['confirm'] ?? '')) {
                    $data['error'] = 'The passwords do not match.';
                } else {
                    $this->settingsModel->updatePassword($_SESSION['reset_user_id'], $new);
                    unset($_SESSION['reset_user_id']);
                    header('Location: ' . URLROOT . '/customer/login?reset=1');
                    exit();
                }
                $data['step'] = 'password';
            }
        }

        $this->view('Customer/forgot-password', $data);
    }

    // ===================== BUDGETS =====================
    // Tables: user_profiles, users, transactions, transaction_categories, budgets
    // Everyone can see the plan. Only the account holder can change it.
    public function budgets() {
        $this->requireCustomer();
        $householdId = $_SESSION['household_id'];
        $isHead = ($_SESSION['household_role'] == 'head');
        $categories = $this->budgetModel->getCategories(); // id, name, suggested_percent (from the DB)
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $isHead) {
            $action = $_POST['action'] ?? '';

            if ($action == 'incomes') {                 // 1 + 2: main and family incomes
                foreach ($_POST['income'] ?? [] as $userId => $amount) {
                    $this->budgetModel->updateIncome((int) $userId, $householdId, max(0, (float) $amount));
                }
                $this->setFlash('Income saved.');
            }
            if ($action == 'add_income') {              // 3: additional income
                $name   = trim($_POST['name'] ?? '');
                $amount = (float) ($_POST['amount'] ?? 0);
                if ($name == '' || $amount <= 0) {
                    $error = 'Enter a name and an amount above 0 for the additional income.';
                } else {
                    $this->budgetModel->addAdditionalIncome($householdId, $_SESSION['user_id'], $name, $amount);
                    $this->setFlash('Additional income added.');
                }
            }
            if ($action == 'remove_income') {
                $this->budgetModel->removeAdditionalIncome((int) ($_POST['id'] ?? 0), $householdId);
                $this->setFlash('Additional income removed.');
            }
            if ($action == 'save_plan') {               // monthly plan limits
                $limits = [];
                foreach ($categories as $c) {
                    $limits[$c['id']] = max(0, (float) ($_POST['limit'][$c['id']] ?? 0));
                }
                $this->budgetModel->savePlan($householdId, $limits);
                $this->setFlash('Monthly plan saved.');
            }

            if ($error == '') {
                header('Location: ' . URLROOT . '/customer/budgets');
                exit();
            }
        }

        // ---- Income totals ----
        $incomes = $this->budgetModel->getIncomes($householdId);
        $extras  = $this->budgetModel->getAdditionalIncomes($householdId);
        $mainIncome = 0; $familyIncome = 0; $extraIncome = 0;
        foreach ($incomes as $i) {
            if ($i['household_role'] == 'head') $mainIncome += $i['monthly_income'];
            else $familyIncome += $i['monthly_income'];
        }
        foreach ($extras as $e) $extraIncome += $e['amount'];
        $totalIncome = $mainIncome + $familyIncome + $extraIncome;

        // ---- Money for the plan = income - savings goal ----
        $goal      = $this->budgetModel->getSavingsGoal($householdId); // savings_goals row
        $savings   = (float) $goal['target_amount'];
        $available = max(0, $totalIncome - $savings);

        // ---- One row per category: suggested amount and the limit to show ----
        $saved = $this->budgetModel->getPlan($householdId);   // [category_id => amount]
        $useSuggestion = isset($_GET['suggest']);            // "Auto-Suggest" button
        $rows = [];
        $totalPlanned = 0;
        $totalPercent = 0;
        foreach ($categories as $c) {
            $percent   = (float) $c['suggested_percent'];
            $suggested = round($available * $percent / 100, 2);
            $limit     = (!$useSuggestion && isset($saved[$c['id']])) ? (float) $saved[$c['id']] : $suggested;
            $rows[] = ['id' => $c['id'], 'name' => $c['name'], 'percent' => $percent, 'suggested' => $suggested, 'limit' => $limit];
            $totalPlanned += $limit;
            $totalPercent += $percent;
        }

        $data = [
            'isHead'       => $isHead,
            'error'        => $error,
            'incomes'      => $incomes,
            'extras'       => $extras,
            'mainIncome'   => $mainIncome,
            'familyIncome' => $familyIncome,
            'extraIncome'  => $extraIncome,
            'totalIncome'  => $totalIncome,
            'savings'      => $savings,
            'goal'         => $goal,
            'available'    => $available,
            'rows'         => $rows,
            'totalPlanned' => $totalPlanned,
            'totalPercent' => $totalPercent,
            'planSaved'    => !empty($saved) && !$useSuggestion,
            'flash'        => $_SESSION['customer_flash'] ?? ''
        ];
        unset($_SESSION['customer_flash'], $_SESSION['customer_flash_type']);

        $this->view('Customer/budgets', $data);
    }

    // ===================== DASHBOARD =====================
    // Tables: transactions, transaction_categories, users, user_profiles, budgets (+ notices)
    public function dashboard() {
        $this->requireCustomer();
        $householdId = $_SESSION['household_id'];

        // income = everyone's monthly income + additional income this month
        $income = 0;
        foreach ($this->budgetModel->getIncomes($householdId) as $i) $income += $i['monthly_income'];
        foreach ($this->budgetModel->getAdditionalIncomes($householdId) as $e) $income += $e['amount'];

        // spending this month per category (for the pie chart)
        $spending = $this->expenseModel->getSpendingByCategory($householdId);
        $spent = 0;
        foreach ($spending as $c) $spent += $c['total'];

        $budget = array_sum($this->budgetModel->getPlan($householdId)); // saved monthly plan

        $data = [
            'notices'      => $this->noticeModel->getActiveForAudience('customer'),
            'income'       => $income,
            'spent'        => $spent,
            'remaining'    => $budget - $spent,
            'spending'     => $spending,
            'transactions' => $this->expenseModel->getTransactions($householdId),
            'flash'        => $_SESSION['customer_flash'] ?? ''
        ];
        unset($_SESSION['customer_flash'], $_SESSION['customer_flash_type']);
        $this->view('Customer/dashboard', $data);
    }

    // ===================== ADD EXPENSE =====================
    // Tables: transactions (insert), transaction_categories, budgets
    // Every person in the household can add expenses.
    public function addExpense() {
        $this->requireCustomer();
        $householdId = $_SESSION['household_id'];
        $categories  = $this->budgetModel->getCategories();
        $data = ['error' => '', 'old' => $_POST];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $amount      = (float) ($_POST['amount'] ?? 0);
            $categoryId  = (int) ($_POST['category'] ?? 0);
            $date        = $_POST['expense_date'] ?? '';
            $description = trim($_POST['description'] ?? '');

            if ($amount <= 0) {
                $data['error'] = 'Please enter an amount above 0.';
            } elseif (!in_array($categoryId, array_column($categories, 'id'))) {
                $data['error'] = 'Please choose a category.';
            } elseif (!strtotime($date)) {
                $data['error'] = 'Please choose a date.';
            } else {
                $this->expenseModel->addExpense($householdId, $_SESSION['user_id'], $categoryId, $amount, $date, $description);
                $this->setFlash('Expense saved.');
                header('Location: ' . URLROOT . '/customer/addExpense');
                exit();
            }
        }

        $data['categories'] = $categories;
        $data['spending']   = $this->expenseModel->getSpendingByCategory($householdId); // this month
        $data['plan']       = $this->budgetModel->getPlan($householdId);                // [category_id => limit]
        $data['flash']      = $_SESSION['customer_flash'] ?? '';
        unset($_SESSION['customer_flash'], $_SESSION['customer_flash_type']);
        $this->view('Customer/addExpense', $data);
    }

    public function logout() {
        unset($_SESSION['user_id'], $_SESSION['user_email'], $_SESSION['user_name'], $_SESSION['user_role']);
        session_destroy();
        header('Location: ' . URLROOT . '/customer/login');
        exit();
    }

    public function grocery()       { $this->requireCustomer(); $this->view('Customer/grocery'); }
    public function cart()          { $this->requireCustomer(); $this->view('Customer/cart'); }
    public function analytics()     { $this->requireCustomer(); $this->view('Customer/analytics'); }
    public function notifications() { $this->requireCustomer(); $this->view('Customer/notifications'); }
    public function product()       { $this->requireCustomer(); $this->view('Customer/product'); }
    public function privacy()       { $this->view('Customer/privacy'); }
    public function terms()         { $this->view('Customer/terms'); }
    public function product_detail() {
        $this->requireCustomer();
        $this->view('customer/product_detail');
    }
}


?>
