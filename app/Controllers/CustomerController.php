<?php
class CustomerController extends Controller {

    public function index() {
        $this->view('Customer/index');
    }

    public function login() {
        $this->view('Customer/customer-login');
    }

    public function register() {
        $this->view('Customer/create-account');
    }

    public function supplierLogin() {
        $this->view('Customer/supplier-login');
    }

    public function dashboard() {
        $this->view('Customer/dashboard');
    }

    public function budgets() {
        $this->view('Customer/budgets');
    }

    public function expense() {
        $this->view('Customer/expense');
    }

    public function grocery() {
        $this->view('Customer/grocery');
    }

    public function cart() {
        $this->view('Customer/cart');
    }

    public function analytics() {
        $this->view('Customer/analytics');
    }

    public function household() {
        $this->view('Customer/household');
    }

    public function notifications() {
        $this->view('Customer/notifications');
    }

    public function settings() {
        $this->view('Customer/settings');
    }

    public function product() {
        $this->view('Customer/product');
    }

    public function privacy() {
        $this->view('Customer/privacy');
    }

    public function terms() {
        $this->view('Customer/terms');
    }
}
?>
