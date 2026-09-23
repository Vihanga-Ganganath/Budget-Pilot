<?php
class Notice {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function getAll() {
        $stmt = $this->db->prepare("SELECT n.*, u.name AS admin_name FROM notices n LEFT JOIN users u ON u.id = n.created_by ORDER BY n.created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM notices WHERE id = :id LIMIT 1");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function create($data) {
        $sql = "INSERT INTO notices (title, message, audience, status, expires_at, created_by) VALUES (:title, :message, :audience, :status, :expires_at, :created_by)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':title' => $data['title'], ':message' => $data['message'], ':audience' => $data['audience'],
            ':status' => $data['status'], ':expires_at' => $data['expires_at'] ?: null, ':created_by' => $data['created_by']
        ]);
    }

    public function update($id, $data) {
        $sql = "UPDATE notices SET title=:title, message=:message, audience=:audience, status=:status, expires_at=:expires_at WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':title'=>$data['title'], ':message'=>$data['message'], ':audience'=>$data['audience'],
            ':status'=>$data['status'], ':expires_at'=>$data['expires_at'] ?: null, ':id'=>$id
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM notices WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getActiveForAudience($audience, $limit = 6) {
        $allowed = ['customer', 'supplier'];
        if (!in_array($audience, $allowed, true)) {
            return [];
        }

        $limit = max(1, min((int)$limit, 20));
        $sql = "SELECT id, title, message, audience, status, expires_at, created_at
                FROM notices
                WHERE status = 'active'
                  AND audience IN (:audience, 'all')
                  AND (expires_at IS NULL OR expires_at >= CURDATE())
                ORDER BY created_at DESC
                LIMIT " . $limit;
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':audience', $audience, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getStats() {
        $stmt = $this->db->query("SELECT COUNT(*) total, SUM(status='active') active, SUM(audience='customer') customers, SUM(audience='supplier') suppliers FROM notices");
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}
