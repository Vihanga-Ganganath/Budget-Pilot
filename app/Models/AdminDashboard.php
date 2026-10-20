<?php
class AdminDashboard {
    private $db;
    public function __construct() { $this->db=(new Database())->getConnection(); }
    public function getData() {
        $totals=$this->db->query("SELECT COUNT(*) total_users, COALESCE(SUM(role='supplier' AND account_status='active'),0) active_suppliers, COALESCE(SUM(role='supplier' AND account_status='pending'),0) pending_suppliers, COALESCE(SUM(role='customer'),0) customers, COALESCE(SUM(role='supplier'),0) suppliers, COALESCE(SUM(role='admin'),0) admins FROM users")->fetch(PDO::FETCH_ASSOC);
        $pendingProducts=(int)$this->db->query("SELECT COUNT(*) FROM products WHERE admin_verification='Pending'")->fetchColumn();
        $totals['pending_approvals']=(int)$totals['pending_suppliers']+$pendingProducts;
        $monthly=$this->db->query("SELECT DATE_FORMAT(created_at,'%Y-%m') month, role, COUNT(*) total FROM users WHERE created_at >= DATE_FORMAT(DATE_SUB(CURDATE(),INTERVAL 11 MONTH),'%Y-%m-01') GROUP BY DATE_FORMAT(created_at,'%Y-%m'),role ORDER BY month")->fetchAll(PDO::FETCH_ASSOC);
        $recent=$this->db->query("SELECT name,role,account_status,created_at FROM users ORDER BY created_at DESC,id DESC LIMIT 8")->fetchAll(PDO::FETCH_ASSOC);
        $alerts=[];
        try { $alerts=$this->db->query("SELECT email,role,ip_address,COUNT(*) attempts,MAX(attempted_at) last_attempt FROM security_login_attempts WHERE successful=0 AND attempted_at >= NOW()-INTERVAL 1 DAY GROUP BY email,role,ip_address HAVING COUNT(*)>=3 ORDER BY last_attempt DESC LIMIT 100")->fetchAll(PDO::FETCH_ASSOC); }
        catch (PDOException $e) { error_log('Dashboard audit unavailable: '.$e->getMessage()); }
        $totals['security_alerts']=count($alerts);
        return ['totals'=>$totals,'monthly'=>$monthly,'recent'=>$recent,'alerts'=>$alerts];
    }
}
