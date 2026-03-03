<?php
$S    = Setting::all();
$lang = Lang::current();
$isAr = Lang::isRtl();
$fDir = $S['footer_direction']??'auto';
if($fDir==='auto') $fDir = $isAr?'rtl':'ltr';

$fbg    = htmlspecialchars($S['footer_bg']   ?? '#0f0f1a');
$fcolor = htmlspecialchars($S['footer_color'] ?? '#a1a1aa');
$fsize  = intval($S['footer_font_size']  ?? 13);
$fpad   = htmlspecialchars($S['footer_padding'] ?? '20px 28px');
$storeName = htmlspecialchars($S["store_name_$lang"] ?? 'ووبيكس');
$copyright = htmlspecialchars($S['footer_copyright'] ?? '© '.date('Y').' '.$storeName);
$cpPos  = $S['footer_copyright_position'] ?? 'center';
$logoPos= $S['footer_logo_position']     ?? 'col1';

// Parse footer links
$rawLinks = json_decode($S['footer_links']??'[]', true) ?: [
  ['col'=>1,'label_ar'=>'الرئيسية','label_en'=>'Home','url'=>'/'],
  ['col'=>1,'label_ar'=>'المنتجات','label_en'=>'Products','url'=>'?page=shop'],
  ['col'=>2,'label_ar'=>'الأسئلة الشائعة','label_en'=>'FAQ','url'=>'?page=faq'],
  ['col'=>2,'label_ar'=>'تواصل معنا','label_en'=>'Contact','url'=>'?page=contact'],
  ['col'=>3,'label_ar'=>'من نحن','label_en'=>'About','url'=>'?page=about'],
  ['col'=>3,'label_ar'=>'سياسة الخصوصية','label_en'=>'Privacy','url'=>'?page=privacy'],
  ['col'=>4,'label_ar'=>'سياسة الإرجاع','label_en'=>'Returns','url'=>'?page=returns'],
];

// Group links by column
$cols = [1=>[],2=>[],3=>[],4=>[]];
foreach($rawLinks as $lnk) {
  $col = max(1,min(4,(int)($lnk['col']??1)));
  $cols[$col][] = $lnk;
}

// Social
$socials = ['whatsapp'=>'💬','telegram'=>'📨','instagram'=>'📸','twitter'=>'🐦','facebook'=>'👤','tiktok'=>'🎵','youtube'=>'▶️'];
$hasSocial = false;
foreach($socials as $k=>$ic) { if(!empty($S[$k])) { $hasSocial=true; break; } }

function renderFooterCol($links, $lang, $isAr) {
  if(empty($links)) return '';
  $out = '<ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:7px">';
  foreach($links as $lnk) {
    $lbl = htmlspecialchars($isAr ? ($lnk['label_ar']??$lnk['label_en']??'') : ($lnk['label_en']??$lnk['label_ar']??''));
    $url = htmlspecialchars($lnk['url']??'#');
    if(!$lbl) continue;
    $out .= "<li><a href=\"$url\" style=\"text-decoration:none;color:inherit;transition:.15s\" onmouseover=\"this.style.color='var(--primary)'\" onmouseout=\"this.style.color=''\">$lbl</a></li>";
  }
  $out .= '</ul>';
  return $out;
}
?>
<style>
.footer-v8{background:<?= $fbg ?>;color:<?= $fcolor ?>;font-size:<?= $fsize ?>px;direction:<?= $fDir ?>;padding:<?= $fpad ?>}
.footer-v8 .fgrid{display:grid;grid-template-columns:repeat(4,1fr);gap:24px;margin-bottom:16px}
.footer-v8 .fcol-title{font-weight:700;font-size:<?= $fsize+1 ?>px;color:rgba(255,255,255,.8);margin-bottom:10px}
.footer-v8 .fbrand-name{font-weight:800;font-size:<?= $fsize+3 ?>px;color:#fff;margin-bottom:6px}
.footer-v8 .fdesc{font-size:<?= $fsize-1 ?>px;line-height:1.6;opacity:.7}
.footer-v8 .fsocials{display:flex;gap:10px;flex-wrap:wrap;margin-top:10px}
.footer-v8 .fsoc-btn{width:32px;height:32px;border-radius:8px;background:rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;text-decoration:none;font-size:15px;transition:.2s}
.footer-v8 .fsoc-btn:hover{background:var(--primary)}
.footer-v8 .fbottom{border-top:1px solid rgba(255,255,255,.07);padding-top:12px;font-size:<?= $fsize-1 ?>px;opacity:.5;text-align:<?= $cpPos ?>}
@media(max-width:700px){.footer-v8 .fgrid{grid-template-columns:1fr 1fr}}
@media(max-width:440px){.footer-v8 .fgrid{grid-template-columns:1fr}}
</style>

<footer class="footer-v8">
  <div class="fgrid">
    <?php
    $logoHtml = '<div class="fcol-title"><div class="fbrand-name"><span>'.$storeName.'</span></div></div>';
    $footerDesc = htmlspecialchars($S["footer_text_$lang"] ?? $S['footer_text_ar'] ?? '');
    $logoColContent = $logoHtml . ($footerDesc?'<p class="fdesc">'.$footerDesc.'</p>':'');
    // Social
    if($hasSocial) {
      $logoColContent .= '<div class="fsocials">';
      foreach($socials as $k=>$ic) {
        if(!empty($S[$k])) $logoColContent .= '<a href="'.htmlspecialchars($S[$k]).'" class="fsoc-btn" target="_blank">'.$ic.'</a>';
      }
      $logoColContent .= '</div>';
    }

    // Build 4 columns, place logo in specified position
    for($ci=1;$ci<=4;$ci++):
      $isLogoCol = ($logoPos==='col'.$ci);
      $colLinks = $cols[$ci];
      if(!$isLogoCol && empty($colLinks)) continue;
    ?>
    <div>
      <?php if($isLogoCol): ?>
        <?php if(!empty($S['logo_url'])): ?>
        <div style="margin-bottom:8px"><img src="<?= htmlspecialchars($S['logo_url']) ?>" alt="logo" style="height:32px;object-fit:contain;border-radius:6px"></div>
        <?php endif; ?>
        <?= $logoColContent ?>
      <?php else: ?>
        <?= renderFooterCol($colLinks,$lang,$isAr) ?>
      <?php endif; ?>
    </div>
    <?php endfor; ?>
  </div>
  <div class="fbottom"><?= $copyright ?></div>
</footer>
