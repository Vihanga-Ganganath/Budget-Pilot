<?php
class SecurityAudit {
    private $db;
    public function __construct() { $this->db = (new Database())->getConnection(); }
    public function record($email, $role, $success, $userId = null) {
        try {
            $s = $this->db->prepare('INSERT INTO security_login_attempts (email, role, user_id, ip_address, user_agent, successful) VALUES (?,?,?,?,?,?)');
            $s->execute([substr($email,0,150), $role, $userId, substr($_SERVER['REMOTE_ADDR'] ?? 'unknown',0,45), substr($_SERVER['HTTP_USER_AGENT'] ?? '',0,255), (int)$success]);
        } catch (Throwable $e) { error_log('Security audit write failed: '.$e->getMessage()); }
    }
    public function overview() {
        $failed = (int)$this->db->query('SELECT COUNT(*) FROM security_login_attempts WHERE successful=0 AND attempted_at >= NOW()-INTERVAL 1 DAY')->fetchColumn();
        $resets = (int)$this->db->query('SELECT COUNT(*) FROM password_resets WHERE created_at >= NOW()-INTERVAL 7 DAY')->fetchColumn();
        $sessions = (int)$this->db->query('SELECT COUNT(*) FROM security_sessions WHERE last_seen >= NOW()-INTERVAL 30 MINUTE AND revoked=0')->fetchColumn();
        $sql = "SELECT MAX(id) id, email, role, ip_address, COUNT(*) attempts, MAX(attempted_at) last_attempt, MIN(attempted_at) first_attempt FROM security_login_attempts WHERE successful=0 AND attempted_at >= NOW()-INTERVAL 1 DAY GROUP BY email, role, ip_address HAVING COUNT(*) >= 3 ORDER BY last_attempt DESC LIMIT 100";
        return ['failed'=>$failed,'resets'=>$resets,'sessions'=>$sessions,'alerts'=>$this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC), 'recent'=>$this->db->query('SELECT email,role,ip_address,successful,attempted_at FROM security_login_attempts ORDER BY id DESC LIMIT 50')->fetchAll(PDO::FETCH_ASSOC)];
    }
    public function resolved($email,$role,$ip) {
        $s=$this->db->prepare('SELECT MAX(resolved_at) FROM security_resolutions WHERE email=? AND role=? AND ip_address=?');$s->execute([$email,$role,$ip]);return $s->fetchColumn();
    }
    public function resolve($email,$role,$ip,$adminId) {
        $s=$this->db->prepare('INSERT INTO security_resolutions (email,role,ip_address,resolved_by) VALUES (?,?,?,?)');return $s->execute([$email,$role,$ip,$adminId]);
    }
    public function touchSession($userId) {
        if (empty($_SESSION['security_token'])) $_SESSION['security_token']=bin2hex(random_bytes(32));
        $s=$this->db->prepare('INSERT INTO security_sessions (token_hash,user_id,last_seen) VALUES (?,?,NOW()) ON DUPLICATE KEY UPDATE last_seen=NOW()');
        $s->execute([hash('sha256',$_SESSION['security_token']),$userId]);
    }
    public function isRevoked() {
        if (empty($_SESSION['security_token'])) return false;
        $s=$this->db->prepare('SELECT revoked FROM security_sessions WHERE token_hash=?');$s->execute([hash('sha256',$_SESSION['security_token'])]);return (bool)$s->fetchColumn();
    }
    public function revokeOthers($adminId) {
        $s=$this->db->prepare('UPDATE security_sessions SET revoked=1 WHERE user_id<>?');$s->execute([$adminId]);return $s->rowCount();
    }
    public function exportRows() {return $this->db->query('SELECT attempted_at,email,role,ip_address,user_agent,successful FROM security_login_attempts ORDER BY id DESC LIMIT 10000')->fetchAll(PDO::FETCH_ASSOC);}
}
