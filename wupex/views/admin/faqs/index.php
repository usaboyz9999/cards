<?php $isAr=Lang::isRtl(); $lang=Lang::current();
$faqs=Database::fetchAll("SELECT * FROM ".DB_PREFIX."faqs ORDER BY sort_order ASC");
?>
<div style="display:flex;justify-content:flex-end;margin-bottom:14px">
  <button class="btn btn-primary" onclick="openModal('addFaqModal')">➕ <?= $isAr?'إضافة سؤال':'Add FAQ' ?></button>
</div>
<div class="tbl-wrap"><table><thead><tr>
  <th><?= $isAr?'السؤال':'Question' ?></th><th>Sort</th><th>Status</th><th></th>
</tr></thead><tbody>
<?php foreach($faqs as $f): ?>
<tr>
  <td style="font-size:13px;font-weight:700"><?= htmlspecialchars(mb_substr($lang==='ar'?$f['question_ar']:$f['question_en'],0,70)) ?></td>
  <td><?= $f['sort_order'] ?></td>
  <td><span class="bpill <?= $f['status']?'bp-active':'bp-inactive' ?>"><?= $f['status']?'Active':'Off' ?></span></td>
  <td>
    <div style="display:flex;gap:5px">
      <button class="btn btn-sm btn-info" onclick='editFaq(<?= json_encode($f,JSON_UNESCAPED_UNICODE) ?>)'>✏️</button>
      <form method="POST" style="display:inline" onsubmit="return confirmDel()"><input type="hidden" name="csrf" value="<?= Session::csrf() ?>"><input type="hidden" name="action" value="delete_faq"><input type="hidden" name="id" value="<?= $f['id'] ?>"><button type="submit" class="btn btn-sm btn-danger">🗑️</button></form>
    </div>
  </td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<div class="modal-ov" id="addFaqModal"><div class="modal-box"><div class="modal-hdr"><h3>➕ <?= $isAr?'إضافة سؤال':'Add FAQ' ?></h3><button class="modal-close" onclick="closeModal('addFaqModal')">×</button></div>
<form method="POST"><input type="hidden" name="csrf" value="<?= Session::csrf() ?>"><input type="hidden" name="action" value="add_faq">
<div class="modal-body"><div class="grid-2">
  <div class="fg"><label>Question AR</label><input type="text" name="question_ar" required></div>
  <div class="fg"><label>Question EN</label><input type="text" name="question_en"></div>
  <div class="fg full"><label>Answer AR</label><textarea name="answer_ar" required></textarea></div>
  <div class="fg full"><label>Answer EN</label><textarea name="answer_en"></textarea></div>
  <div class="fg"><label>Sort Order</label><input type="number" name="sort_order" value="99"></div>
</div>
<label class="chk-row" style="margin-top:10px"><input type="checkbox" name="status" value="1" checked><span>Active</span></label>
</div>
<div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="closeModal('addFaqModal')">Cancel</button><button type="submit" class="btn btn-primary">💾</button></div>
</form></div></div>
<div class="modal-ov" id="editFaqModal"><div class="modal-box"><div class="modal-hdr"><h3>✏️ Edit FAQ</h3><button class="modal-close" onclick="closeModal('editFaqModal')">×</button></div>
<form method="POST"><input type="hidden" name="csrf" value="<?= Session::csrf() ?>"><input type="hidden" name="action" value="edit_faq"><input type="hidden" name="id" id="ef_id">
<div class="modal-body"><div class="grid-2">
  <div class="fg"><label>Question AR</label><input type="text" name="question_ar" id="ef_qar"></div>
  <div class="fg"><label>Question EN</label><input type="text" name="question_en" id="ef_qen"></div>
  <div class="fg full"><label>Answer AR</label><textarea name="answer_ar" id="ef_aar"></textarea></div>
  <div class="fg full"><label>Answer EN</label><textarea name="answer_en" id="ef_aen"></textarea></div>
  <div class="fg"><label>Sort</label><input type="number" name="sort_order" id="ef_sort"></div>
</div>
<label class="chk-row" style="margin-top:10px"><input type="checkbox" name="status" id="ef_st" value="1"><span>Active</span></label>
</div>
<div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="closeModal('editFaqModal')">Cancel</button><button type="submit" class="btn btn-primary">💾</button></div>
</form></div></div>
<script>function editFaq(f){document.getElementById('ef_id').value=f.id;document.getElementById('ef_qar').value=f.question_ar||'';document.getElementById('ef_qen').value=f.question_en||'';document.getElementById('ef_aar').value=f.answer_ar||'';document.getElementById('ef_aen').value=f.answer_en||'';document.getElementById('ef_sort').value=f.sort_order||99;document.getElementById('ef_st').checked=f.status==1;openModal('editFaqModal');}</script>
