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


    // පිටුවට අදාළව යූසර්ස්ලා ලබාගැනීම (Pagination සමග)
    public function getUsers($limit = 6, $offset = 0) {
        // LIMIT සහ OFFSET පාවිච්චි කරලා අදාළ ටික විතරක් ගන්නවා
        $query = "SELECT * FROM users ORDER BY id DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($query);
        // PDO වලදී Limit සහ Offset අනිවාර්යයෙන්ම Integer (ඉලක්කම්) විදිහට Bind කරන්න ඕනේ
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getUserStats() {
        $query = "SELECT 
                    COUNT(*) as total_users,
                    SUM(CASE WHEN account_status = 'active' THEN 1 ELSE 0 END) as active_users,
                    SUM(CASE WHEN account_status = 'suspended' THEN 1 ELSE 0 END) as suspended_users,
                    SUM(CASE WHEN account_status = 'locked' THEN 1 ELSE 0 END) as locked_users
                  FROM users";
                  
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

}
?>