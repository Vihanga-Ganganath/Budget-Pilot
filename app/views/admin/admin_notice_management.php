
<?php require APPROOT . '/Views/shared/header.php'; ?>
<header class="content-header"><div class="header-left"><h2 class="page-title">Notice Management</h2><p class="page-subtitle">Create and manage notices shown to customers and suppliers.</p></div></header>
<?php if(!empty($data['flash'])): ?><div class="flash-notice <?php echo ($data['flash_type']??'')==='error'?'error':''; ?>"><?php echo htmlspecialchars($data['flash']); ?></div><?php endif; ?>
<section class="stat-grid cols-3">
<div class="stat-card"><div class="stat-card-label">Total Notices</div><div class="stat-card-value"><?php echo (int)($data['stats']->total??0); ?></div></div>
<div class="stat-card"><div class="stat-card-label">Active Notices</div><div class="stat-card-value"><?php echo (int)($data['stats']->active??0); ?></div></div>
<div class="stat-card"><div class="stat-card-label">Audience Types</div><div class="stat-card-value">3</div></div>
</section>
<div class="notice-toolbar"><div><h3 style="margin:0">All Notices</h3><p style="margin:4px 0 0;color:#667085;font-size:.88rem">Customers and suppliers only receive notices intended for them.</p></div><button class="notice-btn" id="newNoticeBtn">+ Create Notice</button></div>
<div class="notice-card"><table class="notice-table"><thead><tr><th>Notice</th><th>Audience</th><th>Status</th><th>Expires</th><th>Created</th><th>Actions</th></tr></thead><tbody>
<?php if(empty($data['notices'])): ?><tr><td colspan="6"><div class="empty-notices"><strong>No notices yet.</strong><br>Create your first notice using the button above.</div></td></tr>
<?php else: foreach($data['notices'] as $n): ?>
<tr><td><div class="notice-title"><?php echo htmlspecialchars($n->title); ?></div><div class="notice-message"><?php echo htmlspecialchars(mb_strimwidth($n->message,0,115,'')); ?></div></td>
<td><span class="pill pill-<?php echo htmlspecialchars($n->audience); ?>"><?php echo ucfirst(htmlspecialchars($n->audience)); ?></span></td>
<td><span class="pill pill-<?php echo htmlspecialchars($n->status); ?>"><?php echo ucfirst(htmlspecialchars($n->status)); ?></span></td>
<td><?php echo $n->expires_at ? date('d M Y',strtotime($n->expires_at)) : 'No expiry'; ?></td><td><?php echo date('d M Y',strtotime($n->created_at)); ?></td>
<td><div class="row-actions"><button type="button" class="notice-btn secondary editNotice" data-id="<?php echo (int)$n->id; ?>" data-title="<?php echo htmlspecialchars($n->title,ENT_QUOTES); ?>" data-message="<?php echo htmlspecialchars($n->message,ENT_QUOTES); ?>" data-audience="<?php echo htmlspecialchars($n->audience); ?>" data-status="<?php echo htmlspecialchars($n->status); ?>" data-expires="<?php echo $n->expires_at?date('Y-m-d\TH:i',strtotime($n->expires_at)):''; ?>">Edit</button>
<form method="POST" action="<?php echo URLROOT; ?>/admin/deleteNotice" onsubmit="return confirm('Delete this notice permanently?');"><input type="hidden" name="notice_id" value="<?php echo (int)$n->id; ?>"><button class="notice-btn danger" type="submit">Delete</button></form></div></td></tr>
<?php endforeach; endif; ?></tbody></table></div>
</main></div>
<div class="modal-backdrop" id="noticeModal"><div class="notice-modal"><div class="modal-head"><div><h3 id="modalTitle" style="margin:0">Create Notice</h3><p style="margin:4px 0 0;color:#667085;font-size:.86rem">Choose who should see this announcement.</p></div><button type="button" class="modal-close" id="closeModal">&times;</button></div>
<form method="POST" id="noticeForm" action="<?php echo URLROOT; ?>/admin/createNotice"><input type="hidden" name="notice_id" id="noticeId">
<div class="form-grid"><div class="form-field full"><label>Notice Title *</label><input name="title" id="noticeTitle" maxlength="150" required placeholder="e.g. Scheduled System Maintenance"></div><div class="form-field full"><label>Message *</label><textarea name="message" id="noticeMessage" required placeholder="Write the notice message..."></textarea></div><div class="form-field"><label>Audience *</label><select name="audience" id="noticeAudience"><option value="all">Everyone</option><option value="customer">Customers only</option><option value="supplier">Suppliers only</option></select></div><div class="form-field"><label>Status *</label><select name="status" id="noticeStatus"><option value="active">Active</option><option value="inactive">Inactive</option></select></div><div class="form-field full"><label>Expiry Date (optional)</label><input type="datetime-local" name="expires_at" id="noticeExpires"><div class="modal-actions"><button type="button" class="notice-btn secondary" id="cancelModal">Cancel</button><button type="submit" class="notice-btn" id="saveBtn">Create Notice</button></div></form></div>
</div>
<script src="<?php echo URLROOT; ?>/public/js/admin.js?v=<?php echo filemtime(APPROOT . '/../public/js/admin.js'); ?>"></script>
<script>
const modal=document.getElementById('noticeModal'),form=document.getElementById('noticeForm');
function closeNoticeModal(){modal.classList.remove('open')}
document.getElementById('newNoticeBtn').onclick=()=>{form.reset();form.action='<?php echo URLROOT; ?>/admin/createNotice';document.getElementById('noticeId').value='';document.getElementById('modalTitle').textContent='Create Notice';document.getElementById('saveBtn').textContent='Create Notice';modal.classList.add('open')};
document.querySelectorAll('.editNotice').forEach(b=>b.onclick=()=>{form.action='<?php echo URLROOT; ?>/admin/updateNotice';document.getElementById('noticeId').value=b.dataset.id;document.getElementById('noticeTitle').value=b.dataset.title;document.getElementById('noticeMessage').value=b.dataset.message;document.getElementById('noticeAudience').value=b.dataset.audience;document.getElementById('noticeStatus').value=b.dataset.status;document.getElementById('noticeExpires').value=b.dataset.expires;document.getElementById('modalTitle').textContent='Edit Notice';document.getElementById('saveBtn').textContent='Save Changes';modal.classList.add('open')});
document.getElementById('closeModal').onclick=closeNoticeModal;document.getElementById('cancelModal').onclick=closeNoticeModal;modal.addEventListener('click',e=>{if(e.target===modal)closeNoticeModal()});
</script>
<?php require APPROOT . '/Views/shared/footer.php'; ?>







