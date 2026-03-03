<?php
$S = Setting::all();
$lang = Lang::current();
if(empty($S['popup_enabled'])) return;
$title = htmlspecialchars($S["popup_title_$lang"] ?? $S['popup_title_ar'] ?? '');
$msg   = htmlspecialchars($S["popup_message_$lang"] ?? $S['popup_message_ar'] ?? '');
$btn   = htmlspecialchars($S["popup_btn_$lang"] ?? $S['popup_btn_ar'] ?? 'ابدأ');
$emoji = htmlspecialchars($S['popup_emoji']??'🎉');
$bg    = htmlspecialchars($S['popup_bg']??'#1a1530');
?>
<div class="popup-ov" id="welcomePopup" style="display:none">
  <div class="popup-box" style="background:<?= $bg ?>">
    <button class="popup-close" onclick="closePopup()">×</button>
    <span class="popup-emoji"><?= $emoji ?></span>
    <div class="popup-title"><?= $title ?></div>
    <div class="popup-msg"><?= $msg ?></div>
    <button class="popup-btn" onclick="closePopup()"><?= $btn ?></button>
  </div>
</div>
