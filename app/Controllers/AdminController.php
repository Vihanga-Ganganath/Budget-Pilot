<?php
class AdminController extends Controller {
    
    // Default method (If someone just types /admin)
    public function index() {
        $this->view('admin/dashboard');
    }

    public function dashboard() {
        $this->view('admin/dashboard');
    }

    public function users() {
        $this->view('admin/admin_user_management'); // Make sure this file exists in Views/admin/
    }

    public function suppliers() {
        $this->view('admin/admin_supplier_approvals');
    }

    public function products() {
        $this->view('admin/admin_product_moderation');
    }

    public function security() {
        $this->view('admin/admin_security_audit');
    }

    public function reports() {
        $this->view('admin/admin_reports');
    }

    public function settings() {
        $this->view('admin/admin_settings');
    }

    public function login() {
        $this->view('admin/login');
    }
}
?>