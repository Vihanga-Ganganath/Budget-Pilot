<?php
class Core {
    // Default controller if none is specified (e.g., loading the homepage)
    protected $currentController = 'HomeController'; 
    protected $currentMethod = 'index';
    protected $params = [];

    public function __construct() {
        $url = $this->getUrl();

        // 1. Look in the Controllers folder for the first part of the URL (e.g., 'admin')
        if (isset($url[0])) {
            $controllerName = ucwords($url[0]) . 'Controller';
            
            if (file_exists('../app/Controllers/' . $controllerName . '.php')) {
                $this->currentController = $controllerName;
                unset($url[0]);
            }
        }

        // Require the chosen controller and instantiate it
        require_once '../app/Controllers/' . $this->currentController . '.php';
        $this->currentController = new $this->currentController;

        // 2. Check for the second part of the URL (e.g., 'dashboard' or 'login')
        if (isset($url[1])) {
            if (method_exists($this->currentController, $url[1])) {
                $this->currentMethod = $url[1];
                unset($url[1]);
            }
        }

        // 3. Get any remaining URL parameters (e.g., an ID like /product/edit/5)
        $this->params = $url ? array_values($url) : [];

        // 4. Call the method inside the controller, passing the parameters
        call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
    }

    public function getUrl() {
        if (isset($_GET['url'])) {
            // Strip ending slashes and sanitize the URL to prevent hacking
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
        return [];
    }
}
?>