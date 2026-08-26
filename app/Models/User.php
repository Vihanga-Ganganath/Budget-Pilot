<?php
class User {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    // ─── Login ───────────────────────────────────────────────────────────────

    /**
     * Authenticate a user by email, password, and role.
     * Returns the user row array on success, or an array with 'error' key on failure.
     */
    public function login($email, $password, $role) {
        $query = "SELECT * FROM users WHERE email = :email AND role = :role";
        $stmt  = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':role',  $role);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Block pending accounts (supplier registration awaiting approval)
            if ($row['account_status'] === 'pending') {
                return ['error' => 'pending'];
            }
            // Block suspended accounts
            if ($row['account_status'] === 'suspended') {
                return ['error' => 'suspended'];
            }
            if (password_verify($password, $row['password_hash'])) {
                return $row;
            }
        }

        return false;
    }

    // ─── Admin: user list ─────────────────────────────────────────────────────

    public function getUsers($limit = 6, $offset = 0) {
        $query = "SELECT * FROM users ORDER BY id DESC LIMIT :limit OFFSET :offset";
        $stmt  = $this->db->prepare($query);
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getUserStats() {
        $query = "SELECT
                    COUNT(*) as total_users,
                    SUM(CASE WHEN account_status = 'active'    THEN 1 ELSE 0 END) as active_users,
                    SUM(CASE WHEN account_status = 'suspended' THEN 1 ELSE 0 END) as suspended_users,
                    SUM(CASE WHEN account_status = 'locked'    THEN 1 ELSE 0 END) as locked_users
                  FROM users";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // ─── Supplier registration ────────────────────────────────────────────────

    /**
     * Register a new supplier. Inserts into `users` (pending) and `brands_companies`.
     * Returns true on success, 'duplicate' if email+role already exists, false on error.
     */
    public function registerSupplier($data) {
        try {
            $this->db->beginTransaction();

            // 1. Insert into users
            $query = "INSERT INTO users
                        (name, email, password_hash, role, phone_number, account_status, created_at)
                      VALUES
                        (:name, :email, :password_hash, 'supplier', :phone, 'pending', NOW())";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':name',          $data['name']);
            $stmt->bindParam(':email',         $data['email']);
            $stmt->bindParam(':password_hash', $data['password_hash']);
            $stmt->bindParam(':phone',         $data['phone']);
            $stmt->execute();

            $userId = $this->db->lastInsertId();

            // 2. Insert into brands_companies
            $query2 = "INSERT INTO brands_companies
                         (user_id, company_name, business_information, verification_status)
                       VALUES
                         (:user_id, :company_name, :description, 'pending')";

            $stmt2 = $this->db->prepare($query2);
            $stmt2->bindParam(':user_id',      $userId);
            $stmt2->bindParam(':company_name', $data['company_name']);
            $stmt2->bindParam(':description',  $data['description']);
            $stmt2->execute();

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            // Duplicate email+role unique key violation
            if ($e->getCode() == 23000) {
                return 'duplicate';
            }
            return false;
        }
    }

    /**
     * Check if an email is already registered for a given role.
     */
    public function emailExistsForRole($email, $role) {
        $query = "SELECT id FROM users WHERE email = :email AND role = :role LIMIT 1";
        $stmt  = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':role',  $role);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    // ─── Admin: supplier approval ─────────────────────────────────────────────

    /**
     * Return all suppliers with account_status = 'pending', joined with brand info.
     */
    public function getPendingSuppliers() {
        $query = "SELECT u.id, u.name, u.email, u.phone_number, u.created_at,
                         b.company_name, b.business_information
                  FROM users u
                  LEFT JOIN brands_companies b ON b.user_id = u.id
                  WHERE u.role = 'supplier' AND u.account_status = 'pending'
                  ORDER BY u.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Return recently decided (approved/rejected) supplier accounts.
     */
    public function getRecentSupplierDecisions($limit = 10) {
        $query = "SELECT u.id, u.name, u.email, u.account_status, u.updated_at,
                         b.company_name
                  FROM users u
                  LEFT JOIN brands_companies b ON b.user_id = u.id
                  WHERE u.role = 'supplier'
                    AND u.account_status IN ('active', 'suspended')
                  ORDER BY u.updated_at DESC
                  LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Count pending supplier registrations.
     */
    public function countPendingSuppliers() {
        $query = "SELECT COUNT(*) FROM users WHERE role = 'supplier' AND account_status = 'pending'";
        $stmt  = $this->db->prepare($query);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    /**
     * Approve or reject a supplier by updating account_status.
     * $status = 'active' (approve) or 'suspended' (reject)
     */
    public function updateSupplierStatus($id, $status) {
        // Also sync brands_companies.verification_status
        $brandStatus = ($status === 'active') ? 'approved' : 'rejected';

        $query = "UPDATE users SET account_status = :status WHERE id = :id AND role = 'supplier'";
        $stmt  = $this->db->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id',     $id, PDO::PARAM_INT);
        $stmt->execute();

        $query2 = "UPDATE brands_companies SET verification_status = :vs WHERE user_id = :id";
        $stmt2  = $this->db->prepare($query2);
        $stmt2->bindParam(':vs',  $brandStatus);
        $stmt2->bindParam(':id',  $id, PDO::PARAM_INT);
        $stmt2->execute();

        return $stmt->rowCount() > 0;
    }
}
?>