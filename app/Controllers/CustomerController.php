<?php
require_once '../app/Models/Customer.php';
require_once '../app/Models/Notice.php';

class CustomerController extends Controller {

    private $customerModel;

    public function __construct() {
        $this->customerModel = new Customer();
    }

    // ═════════════════════════════════════════════════════════════════════
    //  Helpers
    // ═════════════════════════════════════════════════════════════════════

    /**
     * Signed in as a customer whose account still exists. If the account was
     * deleted (from another browser, by the head, or in phpMyAdmin) the old
     * session is ended here, so a deleted person cannot keep using the site.
     */
    private function isLoggedIn() {
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'customer') {
            return false;
        }
        if (!$this->customerModel->userExists((int) $_SESSION['user_id'])) {
            $_SESSION = [];
            session_destroy();
            return false;
        }
        return true;
    }

    /** 🔒 Sends anyone who is not a signed-in customer to the login page. */
    private function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: ' . URLROOT . '/customer/login?signin=required');
            exit();
        }
    }

    /** Reads a JSON request body into an array. */
    private function jsonInput() {
        $data = json_decode(file_get_contents('php://input'), true);
        return is_array($data) ? $data : [];
    }

    /** Sends a JSON response and stops. */
    private function json($payload, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($payload);
        exit();
    }

    /** Adds the real database error to a message while APP_DEBUG is on. */
    private function withDetail($message) {
        if (defined('APP_DEBUG') && APP_DEBUG && $this->customerModel->lastError) {
            return $message . ' (' . $this->customerModel->lastError . ')';
        }
        return $message;
    }

    private function requirePost() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['ok' => false, 'message' => 'Method not allowed.'], 405);
        }
    }

    /** A photo is accepted only as a data URL ("data:image/...;base64,..."). */
    private function photoData($value) {
        return (is_string($value) && strpos($value, 'data:image/') === 0) ? $value : null;
    }

    /** Same rules as the sign-up page: 8+ chars, upper, lower, number, symbol. */
    private function strongPassword($pw) {
        return strlen($pw) >= 8
            && preg_match('/[A-Z]/', $pw)
            && preg_match('/[a-z]/', $pw)
            && preg_match('/[0-9]/', $pw)
            && preg_match('/[^A-Za-z0-9]/', $pw);
    }

    /**
     * Validates member cards. Adds problems to $errors and returns the
     * cleaned list. $seen holds emails already used in this request.
     */
    private function validateMembers($rawMembers, &$errors, &$seen) {
        $members = [];
        foreach ($rawMembers as $i => $m) {
            $name  = trim($m['name'] ?? '');
            $email = strtolower(trim($m['email'] ?? ''));
            $pw    = $m['password'] ?? '';

            if (strlen($name) < 2) {
                $errors['members'][$i] = 'Enter this member’s full name.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['members'][$i] = 'Enter a valid email address.';
            } elseif (in_array($email, $seen, true)) {
                $errors['members'][$i] = 'Each person needs a different email.';
            } elseif ($this->customerModel->emailExists($email)) {
                $errors['members'][$i] = 'That email already has an account.';
            } elseif (!$this->strongPassword($pw)) {
                $errors['members'][$i] = 'Set a password with 8+ characters, upper and lower case, a number and a symbol.';
            }

            $seen[] = $email;
            $members[] = ['name' => $name, 'email' => $email, 'password' => $pw,
                          'avatar' => $this->photoData($m['avatar'] ?? null)];
        }
        return $members;
    }

    // ═════════════════════════════════════════════════════════════════════
    //  Public pages (no login needed)
    // ═════════════════════════════════════════════════════════════════════

    public function index() {
        $this->view('Customer/index');
    }

    public function login() {
        // Every household in the database, for the profile picker.
        $this->view('Customer/customer-login', [
            'households' => $this->customerModel->getLoginDirectory()
        ]);
    }

    public function register() {
        $this->view('Customer/create-account');
    }

    public function privacy() {
        $this->view('Customer/privacy');
    }

    public function terms() {
        $this->view('Customer/terms');
    }

    // ═════════════════════════════════════════════════════════════════════
    //  Private pages (🔒 signed-in customers only)
    // ═════════════════════════════════════════════════════════════════════

    public function dashboard() {
    $this->requireLogin();
    $noticeModel = new Notice();
    $data = ['notices' => $noticeModel->getActiveForAudience('customer')];
    $this->view('Customer/dashboard', $data);
}
    public function budgets()       { $this->requireLogin(); $this->view('Customer/budgets'); }
    public function expense()       { $this->requireLogin(); $this->view('Customer/expense'); }
    public function grocery()       { $this->requireLogin(); $this->view('Customer/grocery'); }
    public function cart()          { $this->requireLogin(); $this->view('Customer/cart'); }
    public function analytics()     { $this->requireLogin(); $this->view('Customer/analytics'); }
    public function household()     { $this->requireLogin(); $this->view('Customer/household'); }
    public function notifications() { $this->requireLogin(); $this->view('Customer/notifications'); }
    public function settings()      { $this->requireLogin(); $this->view('Customer/settings'); }
    public function product()       { $this->requireLogin(); $this->view('Customer/product'); }

    // ═════════════════════════════════════════════════════════════════════
    //  Auth API (JSON) — called by create-account.js and auth.js
    // ═════════════════════════════════════════════════════════════════════

    /** POST /customer/apiRegister — create a household (main holder + members). */
    public function apiRegister() {
        $this->requirePost();
        $in = $this->jsonInput();
        $o  = $in['owner'] ?? [];

        $owner = [
            'name'     => trim($o['name'] ?? ''),
            'email'    => strtolower(trim($o['email'] ?? '')),
            'password' => $o['password'] ?? '',
            'nic'      => preg_replace('/\s+/', '', $o['nic'] ?? ''),
            'gender'   => $o['gender'] ?? '',
            'age'      => $o['age'] ?? '',
            'income'   => $o['income'] ?? '',
            'savings'  => $o['savings'] ?? '',
            'avatar'   => $this->photoData($o['avatar'] ?? null),
        ];
        $errors = [];

        // ── Main holder ──
        if (strlen($owner['name']) < 2) {
            $errors['fullName'] = 'Enter your full name.';
        }
        if (!filter_var($owner['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Enter a valid email address.';
        } elseif ($this->customerModel->emailExists($owner['email'])) {
            $errors['email'] = 'That email already has an account. Sign in instead.';
        }
        if (!$this->strongPassword($owner['password'])) {
            $errors['password'] = 'Your password is missing one of the requirements.';
        }
        if (!preg_match('/^(\d{9}[VvXx]|\d{12})$/', $owner['nic'])) {
            $errors['nic'] = 'Use 12 digits, or 9 digits followed by V.';
        }
        if (!in_array($owner['gender'], ['male', 'female', 'other', 'prefer_not_to_say'], true)) {
            $errors['gender'] = 'Select an option.';
        }
        $age = (int) $owner['age'];
        if ($age < 13 || $age > 120) {
            $errors['age'] = 'Enter an age between 13 and 120.';
        }
        $owner['age'] = $age;
        if ($owner['savings'] !== '' && (!is_numeric($owner['savings']) || $owner['savings'] < 0)) {
            $errors['savings'] = 'Enter a positive amount.';
        }
        if ($owner['income'] !== '' && (!is_numeric($owner['income']) || $owner['income'] < 0)) {
            $errors['income'] = 'Enter a positive amount.';
        }
        if (empty($in['terms'])) {
            $errors['terms'] = 'Accept the terms to continue.';
        }

        // ── Members ──
        $seen    = [$owner['email']];
        $members = $this->validateMembers($in['members'] ?? [], $errors, $seen);

        if ($errors) {
            $this->json(['ok' => false, 'errors' => $errors,
                         'message' => 'Check the highlighted fields and try again.'], 422);
        }

        // ── Save ──
        $householdId = $this->customerModel->registerHousehold($owner, $members);

        if ($householdId === 'duplicate') {
            $this->json(['ok' => false, 'errors' => ['email' => 'That email already has an account.'],
                         'message' => 'That email already has an account.'], 409);
        }
        if (!$householdId) {
            $this->json(['ok' => false, 'message' => 'Something went wrong. Please try again.'], 500);
        }

        $this->json([
            'ok'      => true,
            'members' => $this->customerModel->getHouseholdMembers($householdId),
        ]);
    }

    /** POST /customer/apiAddMembers — main holder adds family members. */
    public function apiAddMembers() {
        $this->requirePost();

        if (!$this->isLoggedIn()) {
            $this->json(['ok' => false,
                         'message' => 'Sign in as the main account holder first, then add members from Settings.'], 401);
        }
        if (($_SESSION['household_role'] ?? '') !== 'head') {
            $this->json(['ok' => false,
                         'message' => 'Only the main account holder can add members.'], 403);
        }

        $in     = $this->jsonInput();
        $errors = [];
        $seen   = [];
        $members = $this->validateMembers($in['members'] ?? [], $errors, $seen);

        if (!$members) {
            $this->json(['ok' => false, 'message' => 'Add at least one member first.'], 422);
        }
        if ($errors) {
            $this->json(['ok' => false, 'errors' => $errors,
                         'message' => 'Check the highlighted fields and try again.'], 422);
        }

        $result = $this->customerModel->addMembers($_SESSION['household_id'], $members);

        if ($result === 'duplicate') {
            $this->json(['ok' => false, 'message' => 'One of those emails already has an account.'], 409);
        }
        if (!$result) {
            $this->json(['ok' => false, 'message' => 'Something went wrong. Please try again.'], 500);
        }

        $this->json([
            'ok'      => true,
            'members' => $this->customerModel->getHouseholdMembers($_SESSION['household_id']),
        ]);
    }

    /** POST /customer/apiLogin — check email + password, start the session. */
    public function apiLogin() {
        $this->requirePost();
        $in = $this->jsonInput();

        $email    = trim($in['email'] ?? '');
        $password = $in['password'] ?? '';

        if ($email === '' || $password === '') {
            $this->json(['ok' => false, 'message' => 'Enter your email and password.'], 422);
        }

        $user = $this->customerModel->login($email, $password);

        if (is_array($user) && isset($user['error'])) {
            $this->json(['ok' => false, 'reason' => $user['error'],
                         'message' => 'This account is ' . $user['error'] . '. Please contact support.'], 403);
        }
        if (!$user) {
            $this->json(['ok' => false, 'reason' => 'credentials',
                         'message' => 'Incorrect email or password.'], 401);
        }

        session_regenerate_id(true);   // stops session-fixation attacks
        $_SESSION['user_id']        = (int) $user['id'];
        $_SESSION['user_email']     = $user['email'];
        $_SESSION['user_name']      = $user['name'];
        $_SESSION['user_role']      = 'customer';
        $_SESSION['household_id']   = (int) $user['household_id'];
        $_SESSION['household_role'] = $user['household_role'];

        $this->json([
            'ok'      => true,
            'userId'  => (int) $user['id'],
            'members' => $this->customerModel->getHouseholdMembers($user['household_id']),
        ]);
    }

    /** POST /customer/apiUpdateProfile — the signed-in customer edits their details. */
    public function apiUpdateProfile() {
        $this->requirePost();
        if (!$this->isLoggedIn()) {
            $this->json(['ok' => false, 'message' => 'Your session has ended. Sign in again.'], 401);
        }

        $in = $this->jsonInput();
        $d = [
            'name'   => trim($in['name'] ?? ''),
            'email'  => strtolower(trim($in['email'] ?? '')),
            'nic'    => preg_replace('/\s+/', '', $in['nic'] ?? ''),
            'gender' => $in['gender'] ?? '',
            'age'         => trim((string) ($in['age'] ?? '')),
            'currency'    => strtoupper(trim((string) ($in['currency'] ?? 'LKR'))),
            'savingsGoal' => trim((string) ($in['savingsGoal'] ?? '')),
            'avatar'      => $this->photoData($in['avatar'] ?? null),   // only sent when changed
        ];
        if ($d['gender'] === 'undisclosed') $d['gender'] = 'prefer_not_to_say';   // settings page value

        $errors = [];
        if (strlen($d['name']) < 2) {
            $errors['fName'] = 'Enter your full name.';
        }
        if (!filter_var($d['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['fEmail'] = 'Enter a valid email address.';
        } elseif ($this->customerModel->emailTakenByOther($d['email'], $_SESSION['user_id'])) {
            $errors['fEmail'] = 'Another account already uses that email.';
        }
        if ($d['nic'] !== '' && !preg_match('/^(\d{9}[VvXx]|\d{12})$/', $d['nic'])) {
            $errors['fNic'] = 'Use 12 digits, or 9 digits followed by V.';
        }
        if ($d['nic'] === '' && $this->customerModel->twoFactorStatus($_SESSION['user_id'])['enabled']) {
            $errors['fNic'] = 'Your NIC is needed to reset your password while two-factor is on. Turn two-factor off before removing it.';
        }
        if (!in_array($d['gender'], ['', 'male', 'female', 'other', 'prefer_not_to_say'], true)) {
            $d['gender'] = '';
        }
        if ($d['age'] !== '' && (!ctype_digit($d['age']) || (int) $d['age'] < 13 || (int) $d['age'] > 120)) {
            $errors['fAge'] = 'Enter an age between 13 and 120.';
        }
        if (!in_array($d['currency'], Customer::CURRENCIES, true)) {
            $errors['fCurrency'] = 'Choose a currency from the list.';
        }
        if ($d['savingsGoal'] !== '' && (!is_numeric($d['savingsGoal']) || $d['savingsGoal'] < 0)) {
            $errors['fSavings'] = 'Enter a positive amount.';
        }

        if ($errors) {
            $this->json(['ok' => false, 'errors' => $errors,
                         'message' => 'Check the highlighted fields and try again.'], 422);
        }

        $result = $this->customerModel->updateAccount($_SESSION['user_id'], $d);

        if ($result === 'duplicate') {
            $this->json(['ok' => false, 'errors' => ['fEmail' => 'Another account already uses that email.'],
                         'message' => 'Another account already uses that email.'], 409);
        }
        if ($result === 'badimage') {
            $this->json(['ok' => false, 'message' => 'That photo could not be saved. Choose a JPG or PNG image.'], 422);
        }
        if (!$result) {
            $this->json(['ok' => false, 'message' => $this->withDetail('Could not save. Please try again.')], 500);
        }

        $_SESSION['user_name']  = $d['name'];
        $_SESSION['user_email'] = $d['email'];
        $this->json(['ok' => true, 'avatarUrl' => $result['avatarUrl']]);
    }

    /** POST /customer/apiChangePassword — needs the current password. */
    public function apiChangePassword() {
        $this->requirePost();
        if (!$this->isLoggedIn()) {
            $this->json(['ok' => false, 'message' => 'Your session has ended. Sign in again.'], 401);
        }

        $in      = $this->jsonInput();
        $current = $in['current'] ?? '';
        $new     = $in['new'] ?? '';
        $confirm = $in['confirm'] ?? '';
        $errors  = [];

        if ($current === '') {
            $errors['pwCurrent'] = 'Enter your current password.';
        }
        if (!$this->strongPassword($new)) {
            $errors['pwNew'] = 'Use 8+ characters with upper and lower case, a number and a symbol.';
        } elseif ($new === $current) {
            $errors['pwNew'] = 'Choose a password different from your current one.';
        }
        if ($new !== $confirm) {
            $errors['pwConfirm'] = 'The passwords do not match.';
        }
        if ($errors) {
            $this->json(['ok' => false, 'errors' => $errors], 422);
        }

        $result = $this->customerModel->changePassword($_SESSION['user_id'], $current, $new);

        if ($result === 'wrong') {
            $this->json(['ok' => false, 'errors' => ['pwCurrent' => 'Your current password is incorrect.']], 401);
        }
        if (!$result) {
            $this->json(['ok' => false, 'message' => 'Could not change the password. Please try again.'], 500);
        }
        $this->json(['ok' => true]);
    }

    // ═════════════════════════════════════════════════════════════════════
    //  Forgot password (JSON) — called by forgot-password.js on the login page
    //  No email is sent; the account's own details are checked instead:
    //    1. apiForgotStart   email         → which account is being reset
    //    2. apiForgotNic     NIC number    → every customer
    //    3. apiForgotPin     security PIN  → only if users.two_factor_enabled
    //    4. apiForgotReset   new password
    //  Which request (and which person) is in progress is kept in the PHP
    //  session, never trusted from the browser.
    // ═════════════════════════════════════════════════════════════════════

    /** The request in progress for this browser, or stops with 'restart'. */
    private function resetInProgress() {
        $r = $_SESSION['pw_reset'] ?? null;
        if (!is_array($r) || empty($r['started']) || time() - $r['started'] > Customer::RESET_MINUTES * 60) {
            unset($_SESSION['pw_reset']);
            $this->json(['ok' => false, 'reason' => 'restart',
                         'message' => 'This reset has expired. Enter your email to start again.'], 410);
        }
        return $r;
    }

    /** Shared reply for a wrong NIC / PIN, or a cancelled request. */
    private function resetFailed($result, $field, $wrongText) {
        if (is_array($result) && isset($result['wrong'])) {
            $left = $result['wrong'];
            $this->json(['ok' => false, 'errors' => [$field =>
                $wrongText . ' ' . $left . ' attempt' . ($left === 1 ? '' : 's') . ' left.']], 401);
        }
        unset($_SESSION['pw_reset']);
        $this->json(['ok' => false, 'reason' => 'restart',
                     'message' => 'Too many wrong tries, or the reset expired. Enter your email to start again.'], 410);
    }

    /** POST /customer/apiForgotStart — { email } */
    public function apiForgotStart() {
        $this->requirePost();
        $email = strtolower(trim($this->jsonInput()['email'] ?? ''));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json(['ok' => false, 'errors' => ['fpEmail' => 'Enter a valid email address.']], 422);
        }

        $user   = $this->customerModel->findForReset($email);
        $result = $user ? $this->customerModel->startReset((int) $user['id']) : null;

        if ($result === 'limit') {
            $this->json(['ok' => false, 'message' => 'Too many reset attempts for this account. Try again in an hour.'], 429);
        }
        if ($result === false) {
            $this->json(['ok' => false, 'message' => $this->withDetail('Could not start the reset. Please try again.')], 500);
        }

        // The reply is the same whether or not the email has an account, so
        // this page can't be used to find out who is registered. An unknown
        // email gets an empty request (rid 0) that no NIC can pass.
        $_SESSION['pw_reset'] = [
            'rid'     => is_int($result) ? $result : 0,
            'uid'     => is_int($result) ? (int) $user['id'] : 0,
            'started' => time(),
        ];
        $this->json(['ok' => true, 'next' => 'nic']);
    }

    /** POST /customer/apiForgotNic — { nic } */
    public function apiForgotNic() {
        $this->requirePost();
        $r   = $this->resetInProgress();
        $nic = preg_replace('/\s+/', '', (string) ($this->jsonInput()['nic'] ?? ''));

        if (!preg_match('/^(\d{9}[VvXx]|\d{12})$/', $nic)) {
            $this->json(['ok' => false, 'errors' => ['fpNic' => 'Use 12 digits, or 9 digits followed by V.']], 422);
        }

        if ($r['rid']) {
            $result = $this->customerModel->checkResetNic($r['rid'], $r['uid'], $nic);
        } else {
            // Unknown email: every NIC is wrong, counted down like a real one.
            $_SESSION['pw_reset']['fails'] = ($r['fails'] ?? 0) + 1;
            $left   = Customer::RESET_MAX_ATTEMPTS - $_SESSION['pw_reset']['fails'];
            $result = $left > 0 ? ['wrong' => $left] : 'expired';
        }

        if ($result === 'pin' || $result === 'ready') {
            $this->json(['ok' => true, 'next' => $result === 'pin' ? 'pin' : 'password']);
        }
        $this->resetFailed($result, 'fpNic', 'That NIC doesn’t match this account.');
    }

    /** POST /customer/apiForgotPin — { pin } (two-factor accounts only). */
    public function apiForgotPin() {
        $this->requirePost();
        $r   = $this->resetInProgress();
        $pin = preg_replace('/\s+/', '', (string) ($this->jsonInput()['pin'] ?? ''));

        if (!preg_match('/^\d{6}$/', $pin)) {
            $this->json(['ok' => false, 'errors' => ['fpPin' => 'Enter your 6-digit security PIN.']], 422);
        }

        $result = $this->customerModel->checkResetPin($r['rid'], $r['uid'], $pin);
        if ($result === 'ready') {
            $this->json(['ok' => true, 'next' => 'password']);
        }
        $this->resetFailed($result, 'fpPin', 'That PIN is not right.');
    }

    /** POST /customer/apiForgotReset — { password, confirm } */
    public function apiForgotReset() {
        $this->requirePost();
        $r  = $this->resetInProgress();
        $in = $this->jsonInput();
        $pw = $in['password'] ?? '';

        $errors = [];
        if (!$this->strongPassword($pw)) {
            $errors['fpNew'] = 'Use 8+ characters with upper and lower case, a number and a symbol.';
        }
        if ($pw !== ($in['confirm'] ?? '')) {
            $errors['fpConfirm'] = 'The passwords do not match.';
        }
        if ($errors) {
            $this->json(['ok' => false, 'errors' => $errors], 422);
        }

        $result = $this->customerModel->finishReset($r['rid'], $r['uid'], $pw);

        if ($result === 'same') {
            $this->json(['ok' => false, 'errors' => ['fpNew' => 'Choose a password different from your old one.']], 422);
        }
        if ($result === 'expired') {
            unset($_SESSION['pw_reset']);
            $this->json(['ok' => false, 'reason' => 'restart',
                         'message' => 'This reset has expired. Enter your email to start again.'], 410);
        }
        if (!$result) {
            $this->json(['ok' => false, 'message' => $this->withDetail('Could not save the new password. Please try again.')], 500);
        }

        unset($_SESSION['pw_reset']);
        $this->json(['ok' => true]);
    }

    // ═════════════════════════════════════════════════════════════════════
    //  Two-factor authentication (JSON) — Settings page, two-factor.js
    //  Sets users.two_factor_enabled and the security PIN
    //  (users.two_factor_pin_hash), which the forgot-password flow checks.
    // ═════════════════════════════════════════════════════════════════════

    private function requireLoginJson() {
        if (!$this->isLoggedIn()) {
            $this->json(['ok' => false, 'message' => 'Your session has ended. Sign in again.'], 401);
        }
    }

    /** A PIN that is too easy to guess: 111111, 123456, 987654 … */
    private function weakPin($pin) {
        return preg_match('/^(\d)\1{5}$/', $pin)
            || strpos('0123456789', $pin) !== false
            || strpos('9876543210', $pin) !== false;
    }

    /** POST /customer/apiTwoFactorStatus — { enabled, hasNic } */
    public function apiTwoFactorStatus() {
        $this->requireLoginJson();
        $this->json(['ok' => true] + $this->customerModel->twoFactorStatus($_SESSION['user_id']));
    }

    /**
     * POST /customer/apiTwoFactorSet
     *   turn on / change PIN: { enabled: true, pin, pinConfirm, password }
     *   turn off:             { enabled: false, password }
     */
    public function apiTwoFactorSet() {
        $this->requirePost();
        $this->requireLoginJson();

        $in       = $this->jsonInput();
        $on       = !empty($in['enabled']);
        $password = $in['password'] ?? '';
        $pin      = preg_replace('/\s+/', '', (string) ($in['pin'] ?? ''));

        $errors = [];
        if ($on) {
            if (!preg_match('/^\d{6}$/', $pin)) {
                $errors['tfaPin'] = 'The PIN must be exactly 6 digits.';
            } elseif ($this->weakPin($pin)) {
                $errors['tfaPin'] = 'That PIN is too easy to guess. Avoid repeated or 123456-style digits.';
            }
            if ($pin !== preg_replace('/\s+/', '', (string) ($in['pinConfirm'] ?? ''))) {
                $errors['tfaPinConfirm'] = 'The PINs do not match.';
            }
        }
        if ($password === '') {
            $errors['tfaPassword'] = 'Enter your password to confirm.';
        }
        if ($errors) {
            $this->json(['ok' => false, 'errors' => $errors], 422);
        }

        $result = $this->customerModel->setTwoFactor($_SESSION['user_id'], $on, $password, $on ? $pin : null);

        if ($result === 'wrong') {
            $this->json(['ok' => false, 'errors' => ['tfaPassword' => 'That password is incorrect.']], 401);
        }
        if ($result === 'nonic') {
            $this->json(['ok' => false, 'message' => 'Add your NIC under Account Details and save first — password reset checks it.'], 422);
        }
        if (!$result) {
            $this->json(['ok' => false, 'message' => $this->withDetail('Could not change two-factor. Please try again.')], 500);
        }
        $this->json(['ok' => true, 'enabled' => $on]);
    }

    // ═════════════════════════════════════════════════════════════════════
    //  Household members CRUD (JSON) — called by household.js
    //    Create → apiAddMembers   (above)
    //    Read   → apiMembers
    //    Update → apiUpdateMember
    //    Delete → apiDeleteMember
    // ═════════════════════════════════════════════════════════════════════

    /** Stops the request unless a main account holder (head) is signed in. */
    private function requireHeadJson() {
        if (!$this->isLoggedIn()) {
            $this->json(['ok' => false, 'message' => 'Your session has ended. Sign in again.'], 401);
        }
        if (($_SESSION['household_role'] ?? '') !== 'head') {
            $this->json(['ok' => false, 'message' => 'Only the main account holder can manage members.'], 403);
        }
    }

    /** READ — POST|GET /customer/apiMembers — everyone in my household, from MySQL. */
    public function apiMembers() {
        if (!$this->isLoggedIn()) {
            $this->json(['ok' => false, 'message' => 'Your session has ended. Sign in again.'], 401);
        }
        $this->json([
            'ok'      => true,
            'userId'  => (int) $_SESSION['user_id'],   // who PHP says is signed in
            'members' => $this->customerModel->getHouseholdMembers($_SESSION['household_id']),
        ]);
    }

    /** UPDATE — POST /customer/apiUpdateMember — head edits one member. */
    public function apiUpdateMember() {
        $this->requirePost();
        $this->requireHeadJson();

        $in       = $this->jsonInput();
        $memberId = (int) ($in['id'] ?? 0);
        $d = [
            'name'     => trim($in['name'] ?? ''),
            'email'    => strtolower(trim($in['email'] ?? '')),
            'gender'   => $in['gender'] ?? '',
            'password' => $in['password'] ?? '',
        ];

        $errors = [];
        if (strlen($d['name']) < 2) {
            $errors['name'] = 'Enter this member’s full name.';
        }
        if (!filter_var($d['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Enter a valid email address.';
        } elseif ($this->customerModel->emailTakenByOther($d['email'], $memberId)) {
            $errors['email'] = 'Another account already uses that email.';
        }
        if (!in_array($d['gender'], ['', 'male', 'female', 'other', 'prefer_not_to_say'], true)) {
            $errors['gender'] = 'Select an option.';
        }
        if ($d['password'] !== '' && !$this->strongPassword($d['password'])) {
            $errors['password'] = 'Use 8+ characters with upper and lower case, a number and a symbol.';
        }
        if ($errors) {
            $this->json(['ok' => false, 'errors' => $errors,
                         'message' => 'Check the highlighted fields and try again.'], 422);
        }

        $result = $this->customerModel->updateMember($_SESSION['household_id'], $memberId, $d);

        if ($result === 'notfound') {
            $this->json(['ok' => false, 'message' => 'That member is not on your account.'], 404);
        }
        if ($result === 'duplicate') {
            $this->json(['ok' => false, 'errors' => ['email' => 'Another account already uses that email.'],
                         'message' => 'Another account already uses that email.'], 409);
        }
        if (!$result) {
            $this->json(['ok' => false, 'message' => 'Could not save. Please try again.'], 500);
        }

        $this->json([
            'ok'      => true,
            'members' => $this->customerModel->getHouseholdMembers($_SESSION['household_id']),
        ]);
    }

    /** DELETE — POST /customer/apiDeleteMember — head removes one member. */
    public function apiDeleteMember() {
        $this->requirePost();
        $this->requireHeadJson();

        $in       = $this->jsonInput();
        $memberId = (int) ($in['id'] ?? 0);

        if ($memberId === (int) $_SESSION['user_id']) {
            $this->json(['ok' => false, 'message' => 'You can’t remove yourself here.'], 422);
        }

        $result = $this->customerModel->deleteMember($_SESSION['household_id'], $memberId);

        if ($result === 'notfound') {
            $this->json(['ok' => false, 'message' => 'That member is not on your account.'], 404);
        }
        if (!$result) {
            $this->json(['ok' => false, 'message' => $this->withDetail('Could not remove the member. Please try again.')], 500);
        }

        $this->json([
            'ok'      => true,
            'members' => $this->customerModel->getHouseholdMembers($_SESSION['household_id']),
        ]);
    }

    /**
     * DELETE own account — POST /customer/apiDeleteAccount
     * Called by the "Deactivate Account" button on the Settings page.
     * Works for both the head and a family member (see Customer::deleteAccount).
     */
    public function apiDeleteAccount() {
        $this->requirePost();
        if (!$this->isLoggedIn()) {
            $this->json(['ok' => false, 'message' => 'Your session has ended. Sign in again.'], 401);
        }

        $in       = $this->jsonInput();
        $password = $in['password'] ?? '';
        $whole    = !empty($in['wholeHousehold']);

        // The page says whose account it is deleting. It must be the person PHP
        // has signed in, otherwise we would delete someone other than the one
        // on screen.
        if (isset($in['id']) && (int) $in['id'] !== (int) $_SESSION['user_id']) {
            $this->json(['ok' => false, 'reason' => 'mismatch',
                         'message' => 'This page is showing a different person from the one signed in. Sign in again, then try once more.'], 409);
        }

        if ($password === '') {
            $this->json(['ok' => false, 'errors' => ['password' => 'Enter your password to confirm.']], 422);
        }

        $result = $whole
            ? $this->customerModel->deleteHousehold($_SESSION['user_id'], $password)
            : $this->customerModel->deleteAccount($_SESSION['user_id'], $password);

        if ($result === 'wrong') {
            $this->json(['ok' => false, 'errors' => ['password' => 'That password is incorrect.']], 401);
        }
        if ($result === 'nothead') {
            $this->json(['ok' => false, 'message' => 'Only the main account holder can delete the whole household.'], 403);
        }
        if ($result === 'notfound') {
            $this->json(['ok' => false, 'message' => 'This account no longer exists.'], 404);
        }
        if (!$result) {
            $this->json(['ok' => false, 'message' => $this->withDetail('Could not delete the account. Please try again.')], 500);
        }

        // The account is gone, so end the session straight away.
        $_SESSION = [];
        session_destroy();

        $this->json([
            'ok'       => true,
            'whole'    => $whole,
            'promoted' => $result['promoted'] ?? null,
            'deleted'  => $result['deleted'] ?? 1,
        ]);
    }

    /** GET /customer/logout — end the session and go back to the login page. */
    public function logout() {
        unset($_SESSION['user_id'], $_SESSION['user_email'], $_SESSION['user_name'],
              $_SESSION['user_role'], $_SESSION['household_id'], $_SESSION['household_role']);
        session_destroy();

        $notice = isset($_GET['deactivated']) ? '?deactivated=1' : '';
        header('Location: ' . URLROOT . '/customer/login' . $notice);
        exit();
    }
}
?>
