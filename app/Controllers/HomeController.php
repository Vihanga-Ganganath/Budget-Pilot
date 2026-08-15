<?php
class HomeController extends Controller {
    
    public function index() {
        // Loads the landing page view from app/Views/home.php
        $this->view('home');
    }
}
?>