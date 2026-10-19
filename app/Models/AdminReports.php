<?php
class AdminReports {
    private $db;
    public function __construct() { $this->db = (new Database())->getConnection(); }
    public function get($days) {
        $days = in_array((int)$days, [7,30,90], true) ? (int)$days : 30;
        $start = date('Y-m-d', strtotime('-'.($days-1).' days'));
        $end = date('Y-m-d', strtotime('+1 day'));
        $previous = date('Y-m-d', strtotime('-'.($days*2-1).' days'));
        $q = $this->db->prepare("SELECT COUNT(*) transactions, COALESCE(SUM(amount),0) volume, COALESCE(AVG(amount),0) average, COALESCE(SUM(CASE WHEN type='expense' THEN amount ELSE 0 END),0) expenses, COALESCE(SUM(CASE WHEN type='income' THEN amount ELSE 0 END),0) income FROM transactions WHERE transaction_date >= ? AND transaction_date < ?");
        $q->execute([$start,$end]); $stats = $q->fetch(PDO::FETCH_ASSOC);
        $q->execute([$previous,$start]); $prior = $q->fetch(PDO::FETCH_ASSOC);
        $q = $this->db->prepare("SELECT COALESCE(SUM(amount),0) FROM budgets WHERE budget_month >= DATE_FORMAT(?, '%Y-%m-01') AND budget_month < ?");
        $q->execute([$start,$end]); $budget=(float)$q->fetchColumn();
        $q=$this->db->prepare("SELECT c.name, COUNT(t.id) transaction_count, COALESCE(SUM(t.amount),0) total FROM transactions t JOIN transaction_categories c ON c.id=t.category_id WHERE t.type='expense' AND t.transaction_date >= ? AND t.transaction_date < ? GROUP BY c.id,c.name ORDER BY total DESC LIMIT 10");
        $q->execute([$start,$end]);$categories=$q->fetchAll(PDO::FETCH_ASSOC);
        $q=$this->db->prepare("SELECT DATE(transaction_date) day, COALESCE(SUM(CASE WHEN type='income' THEN amount ELSE 0 END),0) income, COALESCE(SUM(CASE WHEN type='expense' THEN amount ELSE 0 END),0) expenses FROM transactions WHERE transaction_date >= ? AND transaction_date < ? GROUP BY DATE(transaction_date) ORDER BY day");
        $q->execute([$start,$end]);$daily=$q->fetchAll(PDO::FETCH_ASSOC);
        $q=$this->db->query("SELECT u.id,u.name,u.email,u.account_status,COUNT(p.id) product_count, SUM(CASE WHEN p.admin_verification='Approved' THEN 1 ELSE 0 END) approved_products FROM users u LEFT JOIN products p ON p.supplier_id=u.id WHERE u.role='supplier' GROUP BY u.id,u.name,u.email,u.account_status ORDER BY product_count DESC,u.name ASC");
        $suppliers=$q->fetchAll(PDO::FETCH_ASSOC);
        $active=0;foreach($suppliers as $supplier){if($supplier['account_status']==='active')$active++;}
        return compact('days','start','stats','prior','budget','categories','daily','suppliers','active');
    }
}
