<?php
/**
 * ووبيكس - إنشاء جداول قاعدة البيانات تلقائياً
 */
class Schema {
    private static PDO $db;

    public static function install(): array {
        self::$db = Database::pdo();
        $log = [];
        $tables = self::getTables();
        foreach ($tables as $name => $sql) {
            try {
                self::$db->exec($sql);
                $log[] = "✅ جدول $name";
            } catch (PDOException $e) {
                $log[] = "⚠️ $name: " . $e->getMessage();
            }
        }
        // بيانات أولية
        self::seedDefaults();
        $log[] = "✅ البيانات الأولية";
        return $log;
    }

    private static function getTables(): array {
        $p = DB_PREFIX;
        return [
            'settings' => "CREATE TABLE IF NOT EXISTS `{$p}settings` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `key` VARCHAR(100) NOT NULL UNIQUE,
                `value` LONGTEXT,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            'users' => "CREATE TABLE IF NOT EXISTS `{$p}users` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(100) NOT NULL,
                `username` VARCHAR(50) UNIQUE,
                `email` VARCHAR(150) NOT NULL UNIQUE,
                `password` VARCHAR(255) NOT NULL,
                `phone` VARCHAR(20),
                `avatar` VARCHAR(255),
                `role` ENUM('user','admin','moderator') DEFAULT 'user',
                `status` ENUM('active','banned','pending') DEFAULT 'active',
                `wallet_balance` DECIMAL(12,2) DEFAULT 0.00,
                `points` INT DEFAULT 0,
                `referral_code` VARCHAR(20) UNIQUE,
                `referred_by` INT NULL,
                `email_verified` TINYINT(1) DEFAULT 0,
                `email_token` VARCHAR(100),
                `reset_token` VARCHAR(100),
                `reset_expires` DATETIME,
                `last_login` DATETIME,
                `last_ip` VARCHAR(45),
                `two_fa_secret` VARCHAR(100),
                `two_fa_enabled` TINYINT(1) DEFAULT 0,
                `locale` VARCHAR(10) DEFAULT 'ar',
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX `idx_email` (`email`),
                INDEX `idx_role` (`role`),
                INDEX `idx_status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            'categories' => "CREATE TABLE IF NOT EXISTS `{$p}categories` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `name_ar` VARCHAR(100) NOT NULL,
                `name_en` VARCHAR(100) NOT NULL,
                `slug` VARCHAR(120) UNIQUE NOT NULL,
                `icon` VARCHAR(50) DEFAULT '📦',
                `image` VARCHAR(255),
                `color1` VARCHAR(20) DEFAULT '#1a1a2e',
                `color2` VARCHAR(20) DEFAULT '#16213e',
                `description_ar` TEXT,
                `description_en` TEXT,
                `parent_id` INT DEFAULT 0,
                `featured` TINYINT(1) DEFAULT 0,
                `status` TINYINT(1) DEFAULT 1,
                `sort_order` INT DEFAULT 99,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            'products' => "CREATE TABLE IF NOT EXISTS `{$p}products` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `category_id` INT NOT NULL,
                `name_ar` VARCHAR(200) NOT NULL,
                `name_en` VARCHAR(200) NOT NULL,
                `slug` VARCHAR(220) UNIQUE NOT NULL,
                `icon` VARCHAR(50) DEFAULT '🎮',
                `image` VARCHAR(255),
                `color1` VARCHAR(20) DEFAULT '#1a1a2e',
                `color2` VARCHAR(20) DEFAULT '#16213e',
                `description_ar` TEXT,
                `description_en` TEXT,
                `price` DECIMAL(12,2) NOT NULL DEFAULT 0,
                `price_max` DECIMAL(12,2) DEFAULT 0,
                `original_price` DECIMAL(12,2) DEFAULT 0,
                `delivery_type` ENUM('instant','manual') DEFAULT 'instant',
                `countries` TEXT,
                `badge` VARCHAR(20) DEFAULT '',
                `featured` TINYINT(1) DEFAULT 0,
                `stock` TINYINT(1) DEFAULT 1,
                `stock_count` INT DEFAULT -1,
                `views_count` INT DEFAULT 0,
                `sales_count` INT DEFAULT 0,
                `rating` DECIMAL(3,2) DEFAULT 0,
                `reviews_count` INT DEFAULT 0,
                `sort_order` INT DEFAULT 99,
                `status` TINYINT(1) DEFAULT 1,
                `meta_title` VARCHAR(200),
                `meta_desc` TEXT,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX `idx_category` (`category_id`),
                INDEX `idx_status` (`status`),
                INDEX `idx_featured` (`featured`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            'product_prices' => "CREATE TABLE IF NOT EXISTS `{$p}product_prices` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `product_id` INT NOT NULL,
                `label_ar` VARCHAR(100),
                `label_en` VARCHAR(100),
                `price` DECIMAL(12,2) NOT NULL,
                `original_price` DECIMAL(12,2) DEFAULT 0,
                `sort_order` INT DEFAULT 0,
                INDEX `idx_product` (`product_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            'codes' => "CREATE TABLE IF NOT EXISTS `{$p}codes` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `product_id` INT NOT NULL,
                `price_id` INT,
                `code` TEXT NOT NULL,
                `note` TEXT,
                `status` ENUM('available','sold','reserved') DEFAULT 'available',
                `order_id` INT,
                `sold_at` DATETIME,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX `idx_product` (`product_id`),
                INDEX `idx_status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            'orders' => "CREATE TABLE IF NOT EXISTS `{$p}orders` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `order_number` VARCHAR(30) UNIQUE NOT NULL,
                `user_id` INT,
                `guest_email` VARCHAR(150),
                `status` ENUM('pending','processing','completed','cancelled','refunded') DEFAULT 'pending',
                `payment_method` VARCHAR(50),
                `payment_status` ENUM('unpaid','paid','refunded') DEFAULT 'unpaid',
                `subtotal` DECIMAL(12,2) DEFAULT 0,
                `discount` DECIMAL(12,2) DEFAULT 0,
                `tax` DECIMAL(12,2) DEFAULT 0,
                `shipping` DECIMAL(12,2) DEFAULT 0,
                `total` DECIMAL(12,2) NOT NULL,
                `coupon_code` VARCHAR(50),
                `coupon_discount` DECIMAL(12,2) DEFAULT 0,
                `points_used` INT DEFAULT 0,
                `points_earned` INT DEFAULT 0,
                `notes` TEXT,
                `admin_notes` TEXT,
                `ip_address` VARCHAR(45),
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX `idx_user` (`user_id`),
                INDEX `idx_status` (`status`),
                INDEX `idx_number` (`order_number`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            'order_items' => "CREATE TABLE IF NOT EXISTS `{$p}order_items` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `order_id` INT NOT NULL,
                `product_id` INT NOT NULL,
                `price_id` INT,
                `name` VARCHAR(200) NOT NULL,
                `quantity` INT DEFAULT 1,
                `price` DECIMAL(12,2) NOT NULL,
                `code` TEXT,
                INDEX `idx_order` (`order_id`),
                INDEX `idx_product` (`product_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            'carts' => "CREATE TABLE IF NOT EXISTS `{$p}carts` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `session_id` VARCHAR(100) NOT NULL,
                `user_id` INT,
                `product_id` INT NOT NULL,
                `price_id` INT,
                `quantity` INT DEFAULT 1,
                `price` DECIMAL(12,2) NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX `idx_session` (`session_id`),
                INDEX `idx_user` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            'wallet_transactions' => "CREATE TABLE IF NOT EXISTS `{$p}wallet_transactions` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT NOT NULL,
                `type` ENUM('deposit','withdrawal','purchase','refund','bonus','transfer','admin') NOT NULL,
                `amount` DECIMAL(12,2) NOT NULL,
                `balance_before` DECIMAL(12,2) DEFAULT 0,
                `balance_after` DECIMAL(12,2) DEFAULT 0,
                `reference` VARCHAR(100),
                `description_ar` TEXT,
                `description_en` TEXT,
                `status` ENUM('pending','completed','failed','cancelled') DEFAULT 'completed',
                `admin_id` INT,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX `idx_user` (`user_id`),
                INDEX `idx_type` (`type`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            'coupons' => "CREATE TABLE IF NOT EXISTS `{$p}coupons` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `code` VARCHAR(50) UNIQUE NOT NULL,
                `type` ENUM('percent','fixed','free_shipping') DEFAULT 'percent',
                `value` DECIMAL(10,2) NOT NULL,
                `min_order` DECIMAL(10,2) DEFAULT 0,
                `max_discount` DECIMAL(10,2) DEFAULT 0,
                `max_uses` INT DEFAULT 0,
                `used_count` INT DEFAULT 0,
                `per_user` INT DEFAULT 1,
                `products` TEXT,
                `categories` TEXT,
                `status` TINYINT(1) DEFAULT 1,
                `starts_at` DATETIME,
                `expires_at` DATETIME,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            'reviews' => "CREATE TABLE IF NOT EXISTS `{$p}reviews` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `product_id` INT NOT NULL,
                `user_id` INT NOT NULL,
                `order_id` INT,
                `rating` TINYINT NOT NULL,
                `title` VARCHAR(200),
                `comment` TEXT,
                `status` ENUM('pending','approved','rejected') DEFAULT 'pending',
                `admin_reply` TEXT,
                `helpful_count` INT DEFAULT 0,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY `uq_review` (`product_id`, `user_id`),
                INDEX `idx_product` (`product_id`),
                INDEX `idx_status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            'tickets' => "CREATE TABLE IF NOT EXISTS `{$p}tickets` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `ticket_number` VARCHAR(20) UNIQUE NOT NULL,
                `user_id` INT NOT NULL,
                `order_id` INT,
                `subject` VARCHAR(200) NOT NULL,
                `category` VARCHAR(50) DEFAULT 'general',
                `priority` ENUM('low','medium','high','urgent') DEFAULT 'medium',
                `status` ENUM('open','in_progress','waiting','resolved','closed') DEFAULT 'open',
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX `idx_user` (`user_id`),
                INDEX `idx_status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            'ticket_replies' => "CREATE TABLE IF NOT EXISTS `{$p}ticket_replies` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `ticket_id` INT NOT NULL,
                `user_id` INT NOT NULL,
                `message` TEXT NOT NULL,
                `is_admin` TINYINT(1) DEFAULT 0,
                `attachments` TEXT,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX `idx_ticket` (`ticket_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            'notifications' => "CREATE TABLE IF NOT EXISTS `{$p}notifications` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT,
                `type` VARCHAR(50) NOT NULL,
                `title_ar` VARCHAR(200),
                `title_en` VARCHAR(200),
                `message_ar` TEXT,
                `message_en` TEXT,
                `icon` VARCHAR(50) DEFAULT '🔔',
                `color` VARCHAR(20) DEFAULT '#7c3aed',
                `link` VARCHAR(255),
                `is_read` TINYINT(1) DEFAULT 0,
                `is_broadcast` TINYINT(1) DEFAULT 0,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX `idx_user` (`user_id`),
                INDEX `idx_read` (`is_read`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            'wishlists' => "CREATE TABLE IF NOT EXISTS `{$p}wishlists` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT NOT NULL,
                `product_id` INT NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY `uq_wish` (`user_id`, `product_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            'referrals' => "CREATE TABLE IF NOT EXISTS `{$p}referrals` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `referrer_id` INT NOT NULL,
                `referred_id` INT NOT NULL,
                `commission` DECIMAL(10,2) DEFAULT 0,
                `status` ENUM('pending','paid') DEFAULT 'pending',
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX `idx_referrer` (`referrer_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            'points_transactions' => "CREATE TABLE IF NOT EXISTS `{$p}points_transactions` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT NOT NULL,
                `type` ENUM('earn','redeem','expire','admin') NOT NULL,
                `points` INT NOT NULL,
                `reference` VARCHAR(100),
                `description_ar` VARCHAR(200),
                `description_en` VARCHAR(200),
                `expires_at` DATE,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX `idx_user` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            'banners' => "CREATE TABLE IF NOT EXISTS `{$p}banners` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `title_ar` VARCHAR(200),
                `title_en` VARCHAR(200),
                `subtitle_ar` TEXT,
                `subtitle_en` TEXT,
                `image` VARCHAR(255),
                `link` VARCHAR(255),
                `position` VARCHAR(50) DEFAULT 'hero',
                `btn_text_ar` VARCHAR(100),
                `btn_text_en` VARCHAR(100),
                `color1` VARCHAR(20) DEFAULT '#7c3aed',
                `color2` VARCHAR(20) DEFAULT '#ec4899',
                `status` TINYINT(1) DEFAULT 1,
                `sort_order` INT DEFAULT 99,
                `starts_at` DATETIME,
                `ends_at` DATETIME,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            'pages' => "CREATE TABLE IF NOT EXISTS `{$p}pages` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `slug` VARCHAR(120) UNIQUE NOT NULL,
                `title_ar` VARCHAR(200) NOT NULL,
                `title_en` VARCHAR(200) NOT NULL,
                `content_ar` LONGTEXT,
                `content_en` LONGTEXT,
                `meta_title` VARCHAR(200),
                `meta_desc` TEXT,
                `status` TINYINT(1) DEFAULT 1,
                `in_menu` TINYINT(1) DEFAULT 0,
                `sort_order` INT DEFAULT 99,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            'activity_logs' => "CREATE TABLE IF NOT EXISTS `{$p}activity_logs` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT,
                `action` VARCHAR(100) NOT NULL,
                `model` VARCHAR(50),
                `model_id` INT,
                `description` TEXT,
                `ip_address` VARCHAR(45),
                `user_agent` TEXT,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX `idx_user` (`user_id`),
                INDEX `idx_action` (`action`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            'visitors' => "CREATE TABLE IF NOT EXISTS `{$p}visitors` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `ip_address` VARCHAR(45) NOT NULL,
                `country` VARCHAR(50),
                `city` VARCHAR(100),
                `page` VARCHAR(255),
                `referer` VARCHAR(255),
                `user_agent` TEXT,
                `user_id` INT,
                `visited_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX `idx_ip` (`ip_address`),
                INDEX `idx_date` (`visited_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            'seo_metas' => "CREATE TABLE IF NOT EXISTS `{$p}seo_metas` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `page` VARCHAR(100) UNIQUE NOT NULL,
                `title_ar` VARCHAR(200),
                `title_en` VARCHAR(200),
                `description_ar` TEXT,
                `description_en` TEXT,
                `keywords` TEXT,
                `og_image` VARCHAR(255),
                `canonical` VARCHAR(255),
                `robots` VARCHAR(50) DEFAULT 'index,follow',
                `schema_json` TEXT,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            'login_attempts' => "CREATE TABLE IF NOT EXISTS `{$p}login_attempts` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `email` VARCHAR(150),
                `ip_address` VARCHAR(45) NOT NULL,
                `success` TINYINT(1) DEFAULT 0,
                `attempted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX `idx_ip` (`ip_address`),
                INDEX `idx_email` (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            'blocked_ips' => "CREATE TABLE IF NOT EXISTS `{$p}blocked_ips` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `ip_address` VARCHAR(45) NOT NULL UNIQUE,
                `reason` VARCHAR(200),
                `blocked_until` DATETIME,
                `permanent` TINYINT(1) DEFAULT 0,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            'deposit_requests' => "CREATE TABLE IF NOT EXISTS `{$p}deposit_requests` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `request_number` VARCHAR(30) UNIQUE NOT NULL,
                `user_id` INT NOT NULL,
                `amount` DECIMAL(12,2) NOT NULL,
                `bonus` DECIMAL(12,2) DEFAULT 0,
                `method` VARCHAR(50) NOT NULL,
                `status` ENUM('pending','approved','rejected') DEFAULT 'pending',
                `proof_image` VARCHAR(255),
                `notes` TEXT,
                `admin_notes` TEXT,
                `admin_id` INT,
                `processed_at` DATETIME,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX `idx_user` (`user_id`),
                INDEX `idx_status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            'faqs' => "CREATE TABLE IF NOT EXISTS `{$p}faqs` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `question_ar` TEXT NOT NULL,
                `question_en` TEXT NOT NULL,
                `answer_ar` LONGTEXT NOT NULL,
                `answer_en` LONGTEXT NOT NULL,
                `category` VARCHAR(50) DEFAULT 'general',
                `status` TINYINT(1) DEFAULT 1,
                `sort_order` INT DEFAULT 99,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        ];
    }

    private static function seedDefaults(): void {
        $p = DB_PREFIX;
        $db = self::$db;

        // الإعدادات الافتراضية
        $defaults = [
            'store_name_ar'       => 'ووبيكس',
            'store_name_en'       => 'Wupex',
            'store_tagline_ar'    => 'متجرك الرقمي المتكامل',
            'store_tagline_en'    => 'Your Complete Digital Store',
            'currency'            => 'SAR',
            'currency_symbol'     => 'ر.س',
            'primary_color'       => '#7c3aed',
            'secondary_color'     => '#f97316',
            'accent_color'        => '#ec4899',
            'bg_dark'             => '#0d0a1a',
            'bg_sidebar'          => '#110e22',
            'bg_card'             => '#1a1530',
            'hero_text_left'      => 'أسرع',
            'hero_text_right'     => 'سلس',
            'hero_subtext_ar'     => 'أفضل البطاقات الرقمية وشحن الألعاب',
            'hero_subtext_en'     => 'Best Digital Cards & Game Credits',
            'hero_character'      => '🧑‍💻',
            'hero_bg_start'       => '#4c1d95',
            'hero_bg_mid'         => '#7c3aed',
            'hero_bg_end'         => '#ec4899',
            'ticker_enabled'      => '1',
            'ticker_text_ar'      => '🔥 منتجات جديدة! | ⚡ تسليم فوري | 🎮 بطاقات ألعاب | 💳 بطاقات هدايا | 🌟 موثوق به',
            'ticker_text_en'      => '🔥 New Products! | ⚡ Instant Delivery | 🎮 Game Cards | 💳 Gift Cards | 🌟 Trusted',
            'ticker_speed'        => '30',
            'ticker_bg'           => '#7c3aed',
            'ticker_direction'    => 'right',
            'ticker_pause_hover'  => '1',
            'popup_enabled'       => '1',
            'popup_title_ar'      => 'مرحباً بك في ووبيكس! 🎉',
            'popup_title_en'      => 'Welcome to Wupex! 🎉',
            'popup_message_ar'    => 'احصل على أفضل البطاقات الرقمية بأسعار لا تُقاوم. سريع وآمن وموثوق!',
            'popup_message_en'    => 'Get the best digital cards at unbeatable prices. Fast, secure & trusted!',
            'popup_btn_ar'        => 'ابدأ التسوق',
            'popup_btn_en'        => 'Start Shopping',
            'popup_delay'         => '2',
            'popup_emoji'         => '🎉',
            'popup_show_once'     => '1',
            'products_per_row'    => '6',
            'show_prices'         => '1',
            'show_flags'          => '1',
            'maintenance_mode'    => '0',
            'maintenance_msg_ar'  => 'نحن نعمل على تحسين المتجر. سنعود قريباً!',
            'maintenance_msg_en'  => 'We are improving the store. Coming back soon!',
            'wallet_enabled'      => '1',
            'wallet_min_deposit'  => '10',
            'wallet_max_deposit'  => '5000',
            'wallet_bonus'        => '5',
            'coupons_enabled'     => '1',
            'tax_enabled'         => '0',
            'tax_percent'         => '15',
            'reviews_enabled'     => '1',
            'points_enabled'      => '1',
            'points_per_sar'      => '1',
            'points_redeem_rate'  => '0.01',
            'referral_enabled'    => '1',
            'referral_commission' => '5',
            'shipping_enabled'    => '1',
            'shipping_cost'       => '15',
            'shipping_free_above' => '200',
            'payment_wallet'      => '1',
            'payment_bank'        => '0',
            'payment_card'        => '0',
            'footer_text_ar'      => '© 2025 ووبيكس. جميع الحقوق محفوظة.',
            'footer_text_en'      => '© 2025 Wupex. All rights reserved.',
            'default_lang'        => 'ar',
            'registration_enabled'=> '1',
            'guest_checkout'      => '1',
        ];

        $stmt = $db->prepare("INSERT IGNORE INTO `{$p}settings` (`key`, `value`) VALUES (?, ?)");
        foreach ($defaults as $k => $v) {
            $stmt->execute([$k, $v]);
        }

        // التصنيفات الافتراضية
        $cats = [
            ['بطاقات الألعاب','Gaming Cards','gaming-cards','🎮','#1a1a2e','#7c3aed',1],
            ['بطاقات الهدايا','Gift Cards','gift-cards','🎁','#1a2e1a','#10b981',2],
            ['خدمات البث','Streaming','streaming','📺','#1a1a2e','#0ea5e9',3],
            ['التواصل الاجتماعي','Social Media','social-media','📱','#2e1a2e','#ec4899',4],
            ['البرمجيات','Software','software','💻','#1a2a2e','#06b6d4',5],
            ['VPN والأمان','VPN & Security','vpn-security','🔒','#1a2e2a','#10b981',6],
            ['التسوق الإلكتروني','E-Commerce','ecommerce','🛍️','#2e1a1a','#f97316',7],
            ['شحن الجوال','Mobile Topup','mobile-topup','📶','#2e2a1a','#f59e0b',8],
            ['الاشتراكات','Subscriptions','subscriptions','✨','#1a1a2e','#a855f7',9],
        ];

        $stmt2 = $db->prepare("INSERT IGNORE INTO `{$p}categories` (name_ar,name_en,slug,icon,color1,color2,sort_order,status) VALUES (?,?,?,?,?,?,?,1)");
        foreach ($cats as $i => $c) {
            $stmt2->execute([$c[0],$c[1],$c[2],$c[3],$c[4],$c[5],$i+1]);
        }

        // الأسئلة الشائعة
        $faqs = [
            ['كيف أشتري؟','How to buy?','اختر المنتج ثم اضغط "اشتري الآن" وأكمل الدفع','Choose the product then click "Buy Now" and complete payment'],
            ['كيف أتلقى الكود؟','How do I receive the code?','تصل الأكواد فورياً بعد إتمام الدفع','Codes arrive instantly after payment'],
            ['هل الدفع آمن؟','Is payment secure?','نعم جميع المعاملات مشفرة وآمنة','Yes all transactions are encrypted and secure'],
            ['ما طرق الدفع المتاحة؟','What payment methods?','المحفظة الإلكترونية والتحويل البنكي','E-wallet and bank transfer'],
        ];
        $stmt3 = $db->prepare("INSERT IGNORE INTO `{$p}faqs` (question_ar,question_en,answer_ar,answer_en,status) VALUES (?,?,?,?,1)");
        foreach ($faqs as $f) {
            $stmt3->execute($f);
        }
    }
}
