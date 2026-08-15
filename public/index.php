<?php
// 1. Load the configuration file (This is where URLROOT is!)
require_once '../app/Config/config.php';

// 2. Load the database configuration
require_once '../app/Config/Database.php';

// 3. Load the core MVC libraries
require_once '../app/Libraries/Core.php';
require_once '../app/Libraries/Controller.php';

// 4. Initialize the Router (This starts the whole system)
$init = new Core();
?>