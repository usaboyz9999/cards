<?php
/**
 * ووبيكس - ملف الإعدادات الرئيسي
 */
if (!defined('WUPEX')) die('Access denied.');

define('DB_HOST',    '{{DB_HOST}}');
define('DB_NAME',    '{{DB_NAME}}');
define('DB_USER',    '{{DB_USER}}');
define('DB_PASS',    '{{DB_PASS}}');
define('DB_CHARSET', 'utf8mb4');
define('DB_PREFIX',  'wx_');

define('SITE_URL',   '{{SITE_URL}}');
define('SITE_PATH',  __DIR__);
define('ADMIN_PATH', '{{ADMIN_PATH}}');

define('VIEWS_PATH',   SITE_PATH . '/views');
define('UPLOADS_PATH', SITE_PATH . '/uploads');
define('STORAGE_PATH', SITE_PATH . '/storage');
define('LANG_PATH',    SITE_PATH . '/lang');
define('IMAGES_PATH',  SITE_PATH . '/images');

define('SESSION_NAME',     'wupex_session');
define('SESSION_LIFETIME',  86400 * 7);
define('CSRF_TOKEN_NAME',  'wupex_csrf');

define('APP_KEY',     '{{APP_KEY}}');
define('HASH_COST',   12);
define('APP_DEBUG',   false);
define('APP_VERSION', '1.0.0');
define('MAX_UPLOAD',  5 * 1024 * 1024);
define('ALLOWED_IMG', ['jpg','jpeg','png','gif','webp','svg']);
define('CACHE_ENABLED', true);
define('CACHE_TTL',     3600);

if (!file_exists(SITE_PATH . '/install/.installed') && 
    strpos($_SERVER['REQUEST_URI'] ?? '', '/install') === false &&
    strpos($_SERVER['REQUEST_URI'] ?? '', 'index.php') === false) {
    header('Location: ' . SITE_URL . '/install/');
    exit;
}
