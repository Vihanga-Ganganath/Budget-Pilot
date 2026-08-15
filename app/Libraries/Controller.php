<?php
class Controller {
    
    // Load a Database Model
    public function model($model) {
        require_once '../app/Models/' . $model . '.php';
        return new $model();
    }

    // Load an HTML View and pass data to it
    public function view($view, $data = []) {
        // Look for the view file (e.g., 'admin/dashboard')
        if (file_exists('../app/Views/' . $view . '.php')) {
            require_once '../app/Views/' . $view . '.php';
        } else {
            // If the view doesn't exist, stop the script
            die("Error: The view '" . $view . "' does not exist.");
        }
    }
}
?>