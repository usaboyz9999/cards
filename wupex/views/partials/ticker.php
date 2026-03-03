<?php
$S    = Setting::all();
$lang = Lang::current();
if(empty($S['ticker_enabled'])) return;
$tickerText = $S["ticker_text_$lang"] ?? $S['ticker_text_ar'] ?? '';
if(!$tickerText) return;
$speed = max(10, intval($S['ticker_speed'] ?? 25));
$bg    = htmlspecialchars($S['ticker_bg'] ?? '#7c3aed');
$color = htmlspecialchars($S['ticker_color'] ?? '#ffffff');
$isRtl = Lang::isRtl();
?>
<div class="ticker-bar" style="background:<?= $bg ?>;--spd:<?= $speed ?>s;--dir:<?= $isRtl?'reverse':'normal' ?>">
  <div class="ticker-inner">
    <span style="color:<?= $color ?>"><?= htmlspecialchars($tickerText) ?></span>
  </div>
</div>
