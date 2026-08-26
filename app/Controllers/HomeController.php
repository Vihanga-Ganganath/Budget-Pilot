<?php
class HomeController extends Controller {

    public function index() {
        // Loads the main landing page — the entry point for all public visitors
        $this->view('home');
    }
}
?>