<?php
class AdminProductModeration {
    private PDO $db;
    public function __construct() { $this->db=(new Database())->getConnection(); }
    private function conditions(string $search,string $status,int $category,bool $incomplete): array {
        $where=[];$params=[];
        if ($search!=='') { $where[]="(p.name LIKE :search OR EXISTS (SELECT 1 FROM product_variants v WHERE v.product_id=p.id AND v.sku LIKE :sku))"; $params[':search']='%'.$search.'%';$params[':sku']='%'.$search.'%'; }
        if (in_array($status,['Pending','Approved','Rejected'],true)) {$where[]="p.admin_verification=:status";$params[':status']=$status;}
        if ($category>0) {$where[]="p.category_id=:category";$params[':category']=$category;}
        if ($incomplete) {$where[]="(TRIM(COALESCE(p.name,''))='' OR TRIM(COALESCE(p.description,''))='' OR NOT EXISTS (SELECT 1 FROM product_variants v WHERE v.product_id=p.id AND v.price > 0 AND TRIM(v.sku)<>''))";}
        return [$where ? ' WHERE '.implode(' AND ',$where):'',$params];
    }
    private function execute(string $sql,array $params=[]): PDOStatement {
        $stmt=$this->db->prepare($sql);
        foreach($params as $key=>$value) $stmt->bindValue($key,$value,is_int($value)?PDO::PARAM_INT:PDO::PARAM_STR);
        $stmt->execute();return $stmt;
    }
    public function stats(): array {
        $row=$this->db->query("SELECT COUNT(*) total, COALESCE(SUM(admin_verification='Pending'),0) pending, COALESCE(SUM(admin_verification='Approved'),0) approved, COALESCE(SUM(admin_verification='Rejected'),0) rejected FROM products")->fetch(PDO::FETCH_ASSOC);
        $row['incomplete']=(int)$this->db->query("SELECT COUNT(*) FROM products p WHERE TRIM(COALESCE(p.name,''))='' OR TRIM(COALESCE(p.description,''))='' OR NOT EXISTS (SELECT 1 FROM product_variants v WHERE v.product_id=p.id AND v.price > 0 AND TRIM(v.sku)<>'')")->fetchColumn();
        return $row;
    }
    public function categories():array {return $this->db->query("SELECT id,name FROM product_categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);}
    public function listing(string $search,string $status,int $category,bool $incomplete,string $sort,int $page,int $limit=10):array {
        [$where,$params]=$this->conditions($search,$status,$category,$incomplete);
        $total=(int)$this->execute("SELECT COUNT(*) FROM products p".$where,$params)->fetchColumn();
        $pages=max(1,(int)ceil($total/$limit));$page=min(max(1,$page),$pages);
        $order=$sort==='oldest'?'p.created_at ASC,p.id ASC':'p.created_at DESC,p.id DESC';
        $sql="SELECT p.id,p.name,p.description,p.ingredients,p.nutritional_value,p.document_url,p.admin_verification,p.status,p.created_at,p.category_id, c.name category_name,u.name supplier_name,u.email supplier_email, (SELECT GROUP_CONCAT(v.sku ORDER BY v.id SEPARATOR ', ') FROM product_variants v WHERE v.product_id=p.id) skus, (SELECT COUNT(*) FROM product_variants v WHERE v.product_id=p.id AND v.price > 0 AND TRIM(v.sku)<>'') valid_variants FROM products p LEFT JOIN product_categories c ON c.id=p.category_id LEFT JOIN users u ON u.id=p.supplier_id".$where." ORDER BY ".$order." LIMIT :limit OFFSET :offset";
        $params[':limit']=$limit;$params[':offset']=($page-1)*$limit;
        return ['rows'=>$this->execute($sql,$params)->fetchAll(PDO::FETCH_ASSOC),'total'=>$total,'pages'=>$pages,'page'=>$page];
    }
    public function moderate(int $id,string $action):string {
        $this->db->beginTransaction();
        try {
            $stmt=$this->execute("SELECT id,name,description,admin_verification FROM products WHERE id=:id FOR UPDATE",[':id'=>$id]);
            $p=$stmt->fetch(PDO::FETCH_ASSOC);
            if (!$p) throw new RuntimeException('Product not found.');
            if ($p['admin_verification']!=='Pending') throw new RuntimeException('Only pending products can be moderated.');
            if ($action==='approve') {
                $valid=(int)$this->execute("SELECT COUNT(*) FROM product_variants WHERE product_id=:id AND price>0 AND TRIM(sku)<>''",[':id'=>$id])->fetchColumn();
                if (trim((string)$p['name'])==='' || trim((string)$p['description'])==='' || !$valid) throw new RuntimeException('Product needs a name, description and a valid variant with SKU and price before approval.');
            }
            $status=$action==='approve'?'Approved':'Rejected';
            $this->execute("UPDATE products SET admin_verification=:status WHERE id=:id",[':status'=>$status,':id'=>$id]);
            $this->db->commit();return 'Product '.$status.' successfully.';
        } catch(Throwable $e) {$this->db->rollBack();throw $e;}
    }
}
