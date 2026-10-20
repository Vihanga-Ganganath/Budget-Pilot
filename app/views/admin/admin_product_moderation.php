<?php require APPROOT . '/Views/shared/header.php';
$esc=fn($v)=>htmlspecialchars((string)($v??''),ENT_QUOTES,'UTF-8');
$f=$data['filters'];$r=$data['results'];$stats=$data['stats'];
$query=function($overrides=[]) use($f){return http_build_query(array_merge(['q'=>$f['q'],'status'=>$f['status'],'category'=>$f['category'],'sort'=>$f['sort'],'incomplete'=>$f['incomplete']?'1':'0'],$overrides));};
?>
<style>
.pm-head{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:22px}
.pm-grid{display:grid;grid-template-columns:repeat(5,minmax(125px,1fr));gap:12px;margin-bottom:20px}
.pm-card,.pm-panel{background:#fff;border:1px solid #dbe2ea;border-radius:12px;padding:18px}
.pm-card strong{display:block;font-size:28px;color:#182d4a;margin-top:8px}
.pm-card span{color:#536579;font-size:13px}
.pm-filters{display:flex;gap:12px;flex-wrap:wrap;align-items:end}
.pm-filters label{display:flex;flex-direction:column;gap:5px;font-size:13px;color:#475569}
.pm-filters input,.pm-filters select{padding:10px;border:1px solid #cbd5e1;border-radius:7px;max-width:100%}
.pm-btn{border:1px solid #1c3557;background:#1c3557;color:white;border-radius:7px;padding:9px 13px;cursor:pointer;text-decoration:none;display:inline-block;font-size:13px}
.pm-btn.light{background:white;color:#1c3557}.pm-btn.danger{background:#a83232;border-color:#a83232}
.pm-table{overflow-x:auto;margin-top:16px}.pm-table table{width:100%;border-collapse:collapse;min-width:790px}
.pm-table th,.pm-table td{padding:13px;text-align:left;border-bottom:1px solid #e2e8f0;vertical-align:top;font-size:13px}
.pm-table th{background:#f4f7fb;color:#1c3557}
.pm-actions{display:flex;gap:7px;flex-wrap:wrap}.pm-actions form{display:inline}
.pm-muted{color:#64748b;font-size:12px}.pm-flash{padding:12px;border-radius:8px;margin-bottom:15px;background:#edf7ef;border:1px solid #b8dcc2}
.pm-flash.error{background:#fff0f0;border-color:#e9b4b4}
.pm-pages{display:flex;gap:8px;align-items:center;margin-top:16px;flex-wrap:wrap}
@media(max-width:900px){.pm-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.pm-head{align-items:flex-start}}
@media(max-width:500px){.pm-grid{grid-template-columns:1fr 1fr}.pm-card{padding:12px}.pm-card strong{font-size:22px}.pm-filters>*{width:100%}.pm-filters input,.pm-filters select{width:100%}}
</style>
<main class="main-content">
<div class="pm-head">
<div><h2 class="page-title">Product Moderation</h2><p class="page-subtitle">Review real supplier product submissions.</p></div>
<a class="pm-btn light" href="<?= $esc(URLROOT.'/admin/products?'.$query(['export'=>'csv'])) ?>">Export filtered list (CSV)</a>
</div>
<?php if($data['flash']): ?><div class="pm-flash <?= $data['flash_type']==='error'?'error':'' ?>"><?= $esc($data['flash']) ?></div><?php endif; ?>
<div class="pm-grid">
<?php foreach(['Pending Reviews'=>'pending','Incomplete Data'=>'incomplete','Approved'=>'approved','Rejected'=>'rejected','All Products'=>'total'] as $label=>$key): ?>
<div class="pm-card"><span><?= $esc($label) ?></span><strong><?= number_format((int)$stats[$key]) ?></strong>
<?php if($key==='incomplete'): ?><a href="<?= $esc(URLROOT.'/admin/products?'.$query(['incomplete'=>1,'status'=>'all','page'=>1])) ?>" class="pm-muted">View incomplete products</a><?php endif; ?></div>
<?php endforeach; ?>
</div>
<section class="pm-panel">
<form method="get" action="<?= $esc(URLROOT.'/admin/products') ?>" class="pm-filters">
<label>Search product or SKU<input name="q" maxlength="100" value="<?= $esc($f['q']) ?>" placeholder="Product name or SKU"></label>
<label>Verification status<select name="status"><?php foreach(['all'=>'All statuses','Pending'=>'Pending','Approved'=>'Approved','Rejected'=>'Rejected'] as $value=>$label): ?><option value="<?= $esc($value) ?>" <?= $f['status']===$value?'selected':'' ?>><?= $esc($label) ?></option><?php endforeach; ?></select></label>
<label>Category<select name="category"><option value="0">All categories</option><?php foreach($data['categories'] as $cat): ?><option value="<?= (int)$cat['id'] ?>" <?= (int)$f['category']===(int)$cat['id']?'selected':'' ?>><?= $esc($cat['name']) ?></option><?php endforeach; ?></select></label>
<label>Sort<select name="sort"><option value="newest" <?= $f['sort']==='newest'?'selected':'' ?>>Newest first</option><option value="oldest" <?= $f['sort']==='oldest'?'selected':'' ?>>Oldest first</option></select></label>
<label><span>Data quality</span><select name="incomplete"><option value="0" <?= !$f['incomplete']?'selected':'' ?>>All products</option><option value="1" <?= $f['incomplete']?'selected':'' ?>>Incomplete only</option></select></label>
<button class="pm-btn" type="submit">Apply filters</button><a class="pm-btn light" href="<?= $esc(URLROOT.'/admin/products') ?>">Reset</a>
</form>
<p class="pm-muted" style="margin-top:15px">Showing <?= count($r['rows']) ?> of <?= number_format($r['total']) ?> matching products. Only pending products can be approved or rejected. Approval requires a name, description and valid SKU/price variant.</p>
<div class="pm-table"><table><thead><tr><th>Product / SKU</th><th>Category / Supplier</th><th>Data check</th><th>Verification</th><th>Submitted</th><th>Actions</th></tr></thead><tbody>
<?php foreach($r['rows'] as $product):
$complete=trim((string)$product['name'])!=='' && trim((string)$product['description'])!=='' && (int)$product['valid_variants']>0;
?>
<tr><td><strong><?= $esc($product['name']) ?></strong><div class="pm-muted">SKU: <?= $esc($product['skus']?:'Not assigned') ?></div><div class="pm-muted">ID: <?= (int)$product['id'] ?></div></td>
<td><?= $esc($product['category_name']?:'Uncategorized') ?><div class="pm-muted"><?= $esc($product['supplier_name']?:'Unknown supplier') ?></div><div class="pm-muted"><?= $esc($product['supplier_email']) ?></div></td>
<td><?= $complete?'Complete':'Incomplete' ?></td><td><strong><?= $esc($product['admin_verification']) ?></strong></td><td><?= $esc($product['created_at']) ?></td>
<td><?php if($product['admin_verification']==='Pending'): ?><div class="pm-actions">
<form method="post" action="<?= $esc(URLROOT.'/admin/moderateProduct') ?>" onsubmit="return confirm('Approve this product?')"><input type="hidden" name="csrf" value="<?= $esc($data['csrf']) ?>"><input type="hidden" name="id" value="<?= (int)$product['id'] ?>"><input type="hidden" name="action" value="approve"><button class="pm-btn" type="submit" <?= !$complete?'disabled title="Incomplete product"':'' ?>>Approve</button></form>
<form method="post" action="<?= $esc(URLROOT.'/admin/moderateProduct') ?>" onsubmit="return confirm('Reject this product?')"><input type="hidden" name="csrf" value="<?= $esc($data['csrf']) ?>"><input type="hidden" name="id" value="<?= (int)$product['id'] ?>"><input type="hidden" name="action" value="reject"><button class="pm-btn danger" type="submit">Reject</button></form>
</div><?php else: ?><span class="pm-muted">Already reviewed</span><?php endif; ?></td></tr>
<?php endforeach; ?>
<?php if(!$r['rows']): ?><tr><td colspan="6" style="text-align:center;padding:28px">No products match these filters.</td></tr><?php endif; ?>
</tbody></table></div>
<div class="pm-pages">
<?php if($r['page']>1): ?><a class="pm-btn light" href="<?= $esc(URLROOT.'/admin/products?'.$query(['page'=>$r['page']-1])) ?>">Previous</a><?php endif; ?>
<span class="pm-muted">Page <?= $r['page'] ?> of <?= $r['pages'] ?></span>
<?php if($r['page']<$r['pages']): ?><a class="pm-btn light" href="<?= $esc(URLROOT.'/admin/products?'.$query(['page'=>$r['page']+1])) ?>">Next</a><?php endif; ?>
</div></section></main>
<?php require APPROOT . '/Views/shared/footer.php'; ?>
