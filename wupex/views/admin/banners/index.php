<?php $isAr=Lang::isRtl(); $banners=Banner::all(); ?>
<div style="display:flex;justify-content:flex-end;margin-bottom:14px">
  <button class="btn btn-primary" onclick="openModal('addBannerModal')">➕ <?= $isAr?'إضافة بانر':'Add Banner' ?></button>
</div>
<div class="tbl-wrap"><table><thead><tr>
  <th><?= $isAr?'العنوان':'Title' ?></th><th><?= $isAr?'الموضع':'Position' ?></th>
  <th><?= $isAr?'الرابط':'Link' ?></th><th><?= $isAr?'يبدأ':'Start' ?></th>
  <th><?= $isAr?'ينتهي':'End' ?></th><th><?= $isAr?'الحالة':'Status' ?></th><th></th>
</tr></thead><tbody>
<?php foreach($banners as $b): ?>
<tr>
  <td><strong><?= htmlspecialchars($isAr?$b['title_ar']:$b['title_en']) ?></strong></td>
  <td><span class="bpill bp-processing"><?= $b['position'] ?></span></td>
  <td style="font-size:11px;color:var(--muted)"><?= htmlspecialchars(mb_substr($b['link_url']??'',0,35)) ?></td>
  <td style="font-size:11px;color:var(--muted)"><?= $b['start_date']?date('Y-m-d',strtotime($b['start_date'])):'∞' ?></td>
  <td style="font-size:11px;color:var(--muted)"><?= $b['end_date']?date('Y-m-d',strtotime($b['end_date'])):'∞' ?></td>
  <td><span class="bpill <?= $b['status']?'bp-active':'bp-inactive' ?>"><?= $b['status']?'Active':'Off' ?></span></td>
  <td>
    <div style="display:flex;gap:5px">
      <form method="POST" style="display:inline"><input type="hidden" name="csrf" value="<?= Session::csrf() ?>"><input type="hidden" name="action" value="toggle_banner"><input type="hidden" name="id" value="<?= $b['id'] ?>"><button type="submit" class="btn btn-sm btn-secondary"><?= $b['status']?'❌':'✅' ?></button></form>
      <form method="POST" style="display:inline" onsubmit="return confirmDel()"><input type="hidden" name="csrf" value="<?= Session::csrf() ?>"><input type="hidden" name="action" value="delete_banner"><input type="hidden" name="id" value="<?= $b['id'] ?>"><button type="submit" class="btn btn-sm btn-danger">🗑️</button></form>
    </div>
  </td>
</tr>
<?php endforeach; ?>
</tbody></table></div>

<div class="modal-ov" id="addBannerModal">
  <div class="modal-box"><div class="modal-hdr"><h3>➕ <?= $isAr?'إضافة بانر':'Add Banner' ?></h3><button class="modal-close" onclick="closeModal('addBannerModal')">×</button></div>
  <form method="POST" enctype="multipart/form-data"><input type="hidden" name="csrf" value="<?= Session::csrf() ?>"><input type="hidden" name="action" value="add_banner">
  <div class="modal-body">
    <div class="grid-2">
      <div class="fg"><label>Title AR</label><input type="text" name="title_ar" required></div>
      <div class="fg"><label>Title EN</label><input type="text" name="title_en"></div>
      <div class="fg"><label><?= $isAr?'الموضع':'Position' ?></label>
        <select name="position">
          <option value="hero">Hero</option>
          <option value="sidebar">Sidebar</option>
          <option value="popup">Popup</option>
          <option value="footer">Footer</option>
        </select>
      </div>
      <div class="fg"><label>Link URL</label><input type="url" name="link_url" placeholder="https://..."></div>
      <div class="fg"><label>Start Date</label><input type="date" name="start_date"></div>
      <div class="fg"><label>End Date</label><input type="date" name="end_date"></div>
      <div class="fg full"><label><?= $isAr?'الصورة':'Image' ?></label><input type="file" name="image" accept="image/*"></div>
    </div>
    <label class="chk-row" style="margin-top:10px"><input type="checkbox" name="status" value="1" checked><span>Active</span></label>
  </div>
  <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="closeModal('addBannerModal')"><?= $isAr?'إلغاء':'Cancel' ?></button><button type="submit" class="btn btn-primary">💾</button></div>
  </form></div>
</div>
