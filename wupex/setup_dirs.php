<?php
// يُشغَّل مرة واحدة لإنشاء المجلدات اللازمة
$dirs = [
    'uploads/products',
    'uploads/categories',
    'uploads/banners',
    'uploads/users',
    'storage/logs',
    'storage/cache',
    'storage/sessions',
    'storage/backups',
    'images/gift-cards',
];
foreach ($dirs as $dir) {
    $path = __DIR__ . '/' . $dir;
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
        file_put_contents($path . '/.gitkeep', '');
    }
}
// .htaccess للحماية
file_put_contents(__DIR__.'/storage/.htaccess', "Deny from all\n");
file_put_contents(__DIR__.'/uploads/.htaccess', "<FilesMatch \"\\.php$\">\nDeny from all\n</FilesMatch>\n");
echo "✅ Directories created successfully!";
