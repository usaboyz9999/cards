<?php
/**
 * ووبيكس - محمل التطبيق الرئيسي
 */
define('WUPEX', true);

// تحميل الإعدادات
require_once __DIR__ . '/../config.php';

// تحميل الكلاسات الأساسية
$coreFiles = ['Database','Schema','Session','Cache','Lang','Helpers','Security','ActivityLog','Notification','Auth'];
foreach ($coreFiles as $cls) {
    require_once __DIR__ . "/{$cls}.php";
}

// تحميل النماذج
$modelFiles = ['Setting','User','Category','Product','Order','Cart','Wallet','Coupon','Review','Ticket','Banner','Page','Code','Wishlist','Visitor'];
foreach ($modelFiles as $cls) {
    $file = __DIR__ . "/../models/{$cls}.php";
    if (file_exists($file)) require_once $file;
}

// بدء الجلسة
Session::start();

// ── تحويل الأرقام العربية إلى غربية في جميع المخرجات ──
ob_start(function($buffer) {
    $ar = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];
    $en = ['0','1','2','3','4','5','6','7','8','9'];
    return str_replace($ar, $en, $buffer);
});



// تحميل اللغة
$lang = Session::getLang();
if (isset($_GET['lang'])) {
    $lang = in_array($_GET['lang'],['ar','en']) ? $_GET['lang'] : $lang;
    Session::setLang($lang);
}
Lang::load($lang);

// Cache init
Cache::init();

// تسجيل الزائر
if (class_exists('Visitor')) {
    Visitor::track();
}
