<?php
class User {
    private $db;

    public function __construct() {
        // අර අපි කලින් හදපු Database.php එක හරහා Connection එක ලබාගැනීම
        $this->db = (new Database())->getConnection();
    }

    // Login Function එක
    // දැන් Function එක බලාපොරොත්තු වෙනවා Email, Password සහ Role එකක්
    public function login($email, $password, $role) {
        
        // Hard code කරපු 'admin' අයින් කරලා :role කියලා දැම්මා
        $query = "SELECT * FROM users WHERE email = :email AND role = :role";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':role', $role); // ආපු role එක bind කරනවා
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $hashed_password = $row['password_hash'];
            if(password_verify($password, $hashed_password)) {
                return $row;
            }
        }
        
        return false;
    }
}
?>