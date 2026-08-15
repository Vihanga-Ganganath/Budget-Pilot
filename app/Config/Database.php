<?php
class Database {
    // XAMPP Default Credentials
    private $host = "localhost";
    private $db_name = "budget_pilot_db"; // අපි කලින් හදපු Database එකේ නම
    private $username = "root";           // XAMPP වල default username එක
    private $password = "";               // XAMPP වල password එකක් නැහැ (හිස්ව තියන්න)
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            // Create a new PDO connection
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            
            // Set error mode to throw exceptions
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
        } catch(PDOException $exception) {
            echo "Database Connection Error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>