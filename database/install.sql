SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `admin_users`;
CREATE TABLE `admin_users`
(
    `id`             bigint(20) UNSIGNED                                           NOT NULL AUTO_INCREMENT,
    `username`       varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `password`       varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci  NOT NULL,
    `name`           varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `avatar`         varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
    `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
    `created_at`     timestamp                                                     NULL DEFAULT NULL,
    `updated_at`     timestamp                                                     NULL DEFAULT NULL,
    PRIMARY KEY (`id`) USING BTREE,
    UNIQUE INDEX `admin_users_username_unique` (`username`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 2
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  ROW_FORMAT = Dynamic;

INSERT INTO `admin_users`
VALUES (1, 'admin', '$2y$10$ztaax0ywzf1Itoy22IBlOuCZSP.6oW1yREe6WN3KYzhJ5lGLUxJMS', 'Administrator', NULL, NULL,
        '2022-02-11 17:36:56', '2022-02-14 15:47:04');

DROP TABLE IF EXISTS `admin_extension_histories`;
CREATE TABLE `admin_extension_histories`
(
    `id`         bigint(20) UNSIGNED                                           NOT NULL AUTO_INCREMENT,
    `name`       varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `type`       tinyint(4)                                                    NOT NULL DEFAULT 1,
    `version`    varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci  NOT NULL DEFAULT '0',
    `detail`     text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci         NULL,
    `created_at` timestamp                                                     NULL     DEFAULT NULL,
    `updated_at` timestamp                                                     NULL     DEFAULT NULL,
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `admin_extension_histories_name_index` (`name`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `admin_extensions`;
CREATE TABLE `admin_extensions`
(
    `id`         int(10) UNSIGNED                                              NOT NULL AUTO_INCREMENT,
    `name`       varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `version`    varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci  NOT NULL DEFAULT '',
    `is_enabled` tinyint(4)                                                    NOT NULL DEFAULT 0,
    `options`    text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci         NULL,
    `created_at` timestamp                                                     NULL     DEFAULT NULL,
    `updated_at` timestamp                                                     NULL     DEFAULT NULL,
    PRIMARY KEY (`id`) USING BTREE,
    UNIQUE INDEX `admin_extensions_name_unique` (`name`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `admin_menu`;
CREATE TABLE `admin_menu`
(
    `id`         bigint(20) UNSIGNED                                          NOT NULL AUTO_INCREMENT,
    `parent_id`  bigint(20)                                                   NOT NULL DEFAULT 0,
    `order`      int(11)                                                      NOT NULL DEFAULT 0,
    `title`      varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `icon`       varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL     DEFAULT NULL,
    `uri`        varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL     DEFAULT NULL,
    `extension`  varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
    `show`       tinyint(4)                                                   NOT NULL DEFAULT 1,
    `created_at` timestamp                                                    NULL     DEFAULT NULL,
    `updated_at` timestamp                                                    NULL     DEFAULT NULL,
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 17
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  ROW_FORMAT = Dynamic;

INSERT INTO `admin_menu`(`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `extension`, `show`, `created_at`,
                         `updated_at`)
VALUES (1, 0, 1, 'Index', 'feather icon-bar-chart-2', '/', '', 1, '2022-02-11 17:36:56', NULL);
INSERT INTO `admin_menu`(`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `extension`, `show`, `created_at`,
                         `updated_at`)
VALUES (2, 0, 2, 'Admin', 'feather icon-settings', '', '', 1, '2022-02-11 17:36:56', NULL);
INSERT INTO `admin_menu`(`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `extension`, `show`, `created_at`,
                         `updated_at`)
VALUES (3, 2, 3, 'Users', '', 'auth/users', '', 1, '2022-02-11 17:36:56', NULL);
INSERT INTO `admin_menu`(`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `extension`, `show`, `created_at`,
                         `updated_at`)
VALUES (4, 2, 4, 'Roles', '', 'auth/roles', '', 1, '2022-02-11 17:36:56', NULL);
INSERT INTO `admin_menu`(`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `extension`, `show`, `created_at`,
                         `updated_at`)
VALUES (5, 2, 5, 'Permission', '', 'auth/permissions', '', 1, '2022-02-11 17:36:56', NULL);
INSERT INTO `admin_menu`(`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `extension`, `show`, `created_at`,
                         `updated_at`)
VALUES (6, 2, 6, 'Menu', '', 'auth/menu', '', 1, '2022-02-11 17:36:56', NULL);
INSERT INTO `admin_menu`(`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `extension`, `show`, `created_at`,
                         `updated_at`)
VALUES (7, 2, 7, 'Extensions', '', 'auth/extensions', '', 1, '2022-02-11 17:36:56', NULL);
INSERT INTO `admin_menu`(`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `extension`, `show`, `created_at`,
                         `updated_at`)
VALUES (8, 20, 13, '商户列表', 'fa-user-circle', '/user', '', 1, '2022-02-14 08:50:32', '2023-04-18 23:29:22');
INSERT INTO `admin_menu`(`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `extension`, `show`, `created_at`,
                         `updated_at`)
VALUES (10, 0, 16, '订单管理', 'fa-reorder', '/order', '', 1, '2022-02-14 08:51:05', '2023-04-18 23:44:49');
INSERT INTO `admin_menu`(`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `extension`, `show`, `created_at`,
                         `updated_at`)
VALUES (12, 23, 9, '系统配置', 'fa-cog', '/config', '', 1, '2022-04-13 22:53:28', '2023-04-18 23:29:22');
INSERT INTO `admin_menu`(`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `extension`, `show`, `created_at`,
                         `updated_at`)
VALUES (13, 23, 10, '公告管理', 'fa-bell-o', '/announcement', '', 1, '2022-08-16 20:09:46', '2023-04-18 23:29:22');
INSERT INTO `admin_menu`(`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `extension`, `show`, `created_at`,
                         `updated_at`)
VALUES (14, 22, 19, '签约套餐', 'fa-codepen', '/shop', '', 1, '2022-08-16 20:40:47', '2023-04-18 23:29:22');
INSERT INTO `admin_menu`(`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `extension`, `show`, `created_at`,
                         `updated_at`)
VALUES (15, 23, 11, '使用教程', 'fa-book', '/tutorial', '', 1, '2022-08-25 10:52:21', '2023-04-18 23:29:22');
INSERT INTO `admin_menu`(`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `extension`, `show`, `created_at`,
                         `updated_at`)
VALUES (16, 20, 14, '签约记录', 'fa-gg', '/contract', '', 1, '2022-08-26 17:28:05', '2023-04-18 23:29:22');
INSERT INTO `admin_menu`(`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `extension`, `show`, `created_at`,
                         `updated_at`)
VALUES (17, 0, 17, '结算管理', 'fa-money', '/settlement', '', 1, '2022-09-03 17:28:05', '2023-04-18 23:44:57');
INSERT INTO `admin_menu`(`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `extension`, `show`, `created_at`,
                         `updated_at`)
VALUES (18, 22, 20, '支付插件', 'fa-credit-card', '/payment', '', 1, '2022-09-15 11:50:09', '2023-04-18 23:29:22');
INSERT INTO `admin_menu`(`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `extension`, `show`, `created_at`,
                         `updated_at`)
VALUES (19, 20, 15, '域名审核', 'fa-link', '/domain', '', 1, '2022-11-07 15:33:52', '2023-04-18 23:29:22');
INSERT INTO `admin_menu`(`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `extension`, `show`, `created_at`,
                         `updated_at`)
VALUES (20, 0, 12, '商户管理', 'fa-users', '/merchant', '', 1, '2023-04-18 23:20:14', '2023-04-18 23:29:22');
INSERT INTO `admin_menu`(`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `extension`, `show`, `created_at`,
                         `updated_at`)
VALUES (22, 0, 18, '支付管理', 'fa-paypal', '/pay', '', 1, '2023-04-18 23:26:16', '2023-04-18 23:29:22');
INSERT INTO `admin_menu`(`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `extension`, `show`, `created_at`,
                         `updated_at`)
VALUES (23, 0, 8, '系统管理', 'fa-tachometer', '/system', '', 1, '2023-04-18 23:29:04', '2023-04-18 23:29:22');
INSERT INTO `admin_menu`(`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `extension`, `show`, `created_at`,
                         `updated_at`)
VALUES (24, 0, 21, '其它功能', 'fa-th', '/other', '', 1, '2023-04-19 16:21:50', '2023-04-19 16:21:50');
INSERT INTO `admin_menu`(`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `extension`, `show`, `created_at`,
                         `updated_at`)
VALUES (25, 24, 22, '数据清理', 'fa-database', '/dataclear', '', 1, '2023-04-19 16:23:25', '2023-04-19 16:23:25');


DROP TABLE IF EXISTS `admin_permission_menu`;
CREATE TABLE `admin_permission_menu`
(
    `permission_id` bigint(20) NOT NULL,
    `menu_id`       bigint(20) NOT NULL,
    `created_at`    timestamp  NULL DEFAULT NULL,
    `updated_at`    timestamp  NULL DEFAULT NULL,
    UNIQUE INDEX `admin_permission_menu_permission_id_menu_id_unique` (`permission_id`, `menu_id`) USING BTREE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `admin_permissions`;
CREATE TABLE `admin_permissions`
(
    `id`          bigint(20) UNSIGNED                                           NOT NULL AUTO_INCREMENT,
    `name`        varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci  NOT NULL,
    `slug`        varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci  NOT NULL,
    `http_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL     DEFAULT NULL,
    `http_path`   text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci         NULL,
    `order`       int(11)                                                       NOT NULL DEFAULT 0,
    `parent_id`   bigint(20)                                                    NOT NULL DEFAULT 0,
    `created_at`  timestamp                                                     NULL     DEFAULT NULL,
    `updated_at`  timestamp                                                     NULL     DEFAULT NULL,
    PRIMARY KEY (`id`) USING BTREE,
    UNIQUE INDEX `admin_permissions_slug_unique` (`slug`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 7
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  ROW_FORMAT = Dynamic;

INSERT INTO `admin_permissions`
VALUES (1, 'Auth management', 'auth-management', '', '', 1, 0, '2022-02-11 17:36:56', NULL);
INSERT INTO `admin_permissions`
VALUES (2, 'Users', 'users', '', '/auth/users*', 2, 1, '2022-02-11 17:36:56', NULL);
INSERT INTO `admin_permissions`
VALUES (3, 'Roles', 'roles', '', '/auth/roles*', 3, 1, '2022-02-11 17:36:56', NULL);
INSERT INTO `admin_permissions`
VALUES (4, 'Permissions', 'permissions', '', '/auth/permissions*', 4, 1, '2022-02-11 17:36:56', NULL);
INSERT INTO `admin_permissions`
VALUES (5, 'Menu', 'menu', '', '/auth/menu*', 5, 1, '2022-02-11 17:36:56', NULL);
INSERT INTO `admin_permissions`
VALUES (6, 'Extension', 'extension', '', '/auth/extensions*', 6, 1, '2022-02-11 17:36:56', NULL);

DROP TABLE IF EXISTS `admin_role_menu`;
CREATE TABLE `admin_role_menu`
(
    `role_id`    bigint(20) NOT NULL,
    `menu_id`    bigint(20) NOT NULL,
    `created_at` timestamp  NULL DEFAULT NULL,
    `updated_at` timestamp  NULL DEFAULT NULL,
    UNIQUE INDEX `admin_role_menu_role_id_menu_id_unique` (`role_id`, `menu_id`) USING BTREE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `admin_role_permissions`;
CREATE TABLE `admin_role_permissions`
(
    `role_id`       bigint(20) NOT NULL,
    `permission_id` bigint(20) NOT NULL,
    `created_at`    timestamp  NULL DEFAULT NULL,
    `updated_at`    timestamp  NULL DEFAULT NULL,
    UNIQUE INDEX `admin_role_permissions_role_id_permission_id_unique` (`role_id`, `permission_id`) USING BTREE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `admin_role_users`;
CREATE TABLE `admin_role_users`
(
    `role_id`    bigint(20) NOT NULL,
    `user_id`    bigint(20) NOT NULL,
    `created_at` timestamp  NULL DEFAULT NULL,
    `updated_at` timestamp  NULL DEFAULT NULL,
    UNIQUE INDEX `admin_role_users_role_id_user_id_unique` (`role_id`, `user_id`) USING BTREE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  ROW_FORMAT = Dynamic;

INSERT INTO `admin_role_users`
VALUES (1, 1, '2022-02-11 17:36:56', '2022-02-11 17:36:56');

DROP TABLE IF EXISTS `admin_roles`;
CREATE TABLE `admin_roles`
(
    `id`         bigint(20) UNSIGNED                                          NOT NULL AUTO_INCREMENT,
    `name`       varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `slug`       varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` timestamp                                                    NULL DEFAULT NULL,
    `updated_at` timestamp                                                    NULL DEFAULT NULL,
    PRIMARY KEY (`id`) USING BTREE,
    UNIQUE INDEX `admin_roles_slug_unique` (`slug`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 2
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  ROW_FORMAT = Dynamic;

INSERT INTO `admin_roles`
VALUES (1, 'Administrator', 'administrator', '2022-02-11 17:36:56', '2022-02-11 17:36:56');

DROP TABLE IF EXISTS `admin_settings`;
CREATE TABLE `admin_settings`
(
    `slug`       varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `value`      longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci     NOT NULL,
    `created_at` timestamp                                                     NULL DEFAULT NULL,
    `updated_at` timestamp                                                     NULL DEFAULT NULL,
    PRIMARY KEY (`slug`) USING BTREE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `announcement`;
CREATE TABLE `announcement`
(
    `id`         int(10) UNSIGNED                                              NOT NULL AUTO_INCREMENT,
    `title`      varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标题',
    `content`    text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci         NOT NULL COMMENT '内容',
    `alert`      int(11)                                                       NOT NULL DEFAULT 0 COMMENT '是否弹窗',
    `status`     tinyint(3)                                                    NOT NULL DEFAULT 1,
    `created_at` timestamp                                                     NULL     DEFAULT NULL,
    `updated_at` timestamp                                                     NULL     DEFAULT NULL,
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 2
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  ROW_FORMAT = Dynamic;

INSERT INTO `announcement`
VALUES (1, '费率须知',
        '收款费率：签约时签约的费率<br>\r\n结算费率：USDT(3%)<br>\r\n结算手续费：2RMB<br>\r\n结算汇率：当日币安交易所卖价价差正负0.05以内<br>',
        0, 1, '2022-08-16 20:11:33', '2022-08-21 00:01:26');

DROP TABLE IF EXISTS `config`;
CREATE TABLE `config`
(
    `id`         int(10) unsigned                        NOT NULL AUTO_INCREMENT,
    `name`       varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '配置名',
    `key`        varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '配置key',
    `value`      text COLLATE utf8mb4_unicode_ci COMMENT '内容',
    `type`       varchar(100) COLLATE utf8mb4_unicode_ci          DEFAULT NULL COMMENT '类型',
    `field`      varchar(100) COLLATE utf8mb4_unicode_ci          DEFAULT NULL COMMENT '字段',
    `help`       varchar(191) COLLATE utf8mb4_unicode_ci          DEFAULT NULL COMMENT '提示框',
    `created_at` timestamp                               NULL     DEFAULT NULL,
    `updated_at` timestamp                               NULL     DEFAULT NULL,
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

INSERT INTO `config`
VALUES (1, '开启注册', 'open_register', 'true', '基础配置', 'switch', NULL, '2022-08-28 18:13:20',
        '2022-11-07 00:23:54');
INSERT INTO `config`
VALUES (2, '登录页背景图', 'login_background',
        'https://preview.keenthemes.com/metronic8/demo8/assets/media/auth/bg8.jpg', '基础配置', 'text', NULL,
        '2022-08-18 15:49:47', '2022-11-07 00:23:54');
INSERT INTO `config`
VALUES (3, 'Etherscan密钥', 'etherscan_key', '', '基础配置', 'text', NULL, '2022-08-28 18:13:20',
        '2022-11-07 00:23:54');
INSERT INTO `config`
VALUES (4, 'TronGrid密钥', 'trongrid_token', '', '基础配置', 'text', NULL, '2022-08-28 18:13:20',
        '2022-11-07 00:23:54');
INSERT INTO `config`
VALUES (5, 'Telegram机器人-名称', 'telegram_bot_name', '', 'Telegram配置', 'text', NULL, '2022-08-28 18:12:53',
        '2022-11-07 00:23:54');
INSERT INTO `config`
VALUES (6, 'Telegram机器人-Token', 'telegram_bot_token', '', 'Telegram配置', 'text', NULL, '2022-08-28 18:13:20',
        '2022-11-07 00:23:54');
INSERT INTO `config`
VALUES (7, '接口文档', 'api_wiki', '', '支付配置', 'text', NULL, '2022-11-16 15:40:22', '2022-11-16 15:47:40');
INSERT INTO `config`
VALUES (8, '接口地址', 'api_host', '', '支付配置', 'text', '最后不要带/', '2022-08-24 00:07:10', '2022-11-07 00:23:54');
INSERT INTO `config`
VALUES (9, '支付地址', 'pay_host', '', '支付配置', 'text', '最后不要带/', '2022-11-20 20:14:32', '2022-11-20 20:14:32');
INSERT INTO `config`
VALUES (10, '最低结算金额', 'settlement_money', '100', '结算配置', 'text', NULL, '2022-09-14 16:45:22',
        '2022-11-07 00:23:54');
INSERT INTO `config`
VALUES (11, '结算周期', 'settlement_day', '1', '结算配置', 'text', '单位为天，1代表D1(第二天)生成结算单',
        '2022-09-03 18:13:20', '2022-11-07 00:23:54');
INSERT INTO `config`
VALUES (12, '结算USDT汇率', 'settlement_usdt_price', '7', '结算配置', 'text', '当日修改之后，第二天结算单才会生效',
        '2022-09-03 18:13:20', '2022-11-07 00:23:54');
INSERT INTO `config`
VALUES (13, '结算USDT地址', 'settlement_usdt_address', '', '结算配置', 'text', NULL, '2022-09-03 18:13:20',
        '2022-11-07 00:23:54');
INSERT INTO `config`
VALUES (14, '结算USDT私钥', 'settlement_usdt_key', '', '结算配置', 'text', NULL, '2022-09-03 18:13:20',
        '2022-11-07 00:23:54');
INSERT INTO `config`
VALUES (15, '订单商品名称', 'order_goods_name',
        '睡眠遮光透气发热护眼眼罩\r\nLORDE里兜纯豆腐砂经典款猫砂豆腐猫砂\r\n士(LUX)洗护套装 大白瓶 水润丝滑洗发乳750mlx2\r\n舒耐(REXONA)爽身香体止汗喷雾 净纯无香150ml\r\n苏泊尔supor 锅具套装居家不粘炒锅煎锅汤锅三件套装锅\r\n塑料抽屉式收纳柜 卧室床头柜置物柜 儿童衣柜\r\n塑料衣架宽肩无痕晾衣架子加厚防滑晾晒不鼓包西服大衣挂架\r\n春节对联春联大礼包超值18件套\r\n电动车头盔男全盔冬季双镜片揭面盔女\r\n无火香薰精油家用室内香氛空气清新剂',
        '支付配置', 'textarea', NULL, '2022-09-15 11:13:18', '2022-11-07 00:23:54');
INSERT INTO `config`
VALUES (16, '支付模式', 'pay_model_setting', '1', '支付配置', 'select', NULL, '2022-11-16 15:40:22',
        '2022-11-16 15:47:40');
INSERT INTO `config`
VALUES (17, '最小支付金额', 'pay_min_amount', '1', '支付配置', 'text', NULL, '2022-11-07 00:29:26',
        '2022-11-07 00:29:28');
INSERT INTO `config`
VALUES (18, '最大支付金额', 'pay_max_amount', '500', '支付配置', 'text', NULL, '2022-11-07 00:28:37',
        '2022-11-07 00:28:39');
INSERT INTO `config`
VALUES (19, '开启授权支付域名审核', 'pay_domain_open', 'true', '支付配置', 'switch', NULL, '2022-11-07 00:31:10',
        '2022-11-07 00:31:13');
INSERT INTO `config`
VALUES (20, '未授权域名禁止支付', 'pay_domain_forbid', 'true', '支付配置', 'switch', NULL, '2022-11-07 00:34:40',
        '2022-11-07 00:34:40');
INSERT INTO `config`
VALUES (21, '开启退款', 'open_refund', 'false', '支付配置', 'switch', NULL, '2022-11-07 00:11:47',
        '2022-11-07 00:23:54');
INSERT INTO `config`
VALUES (22, '是否开启管理员推送', 'telegram_admin_push', 'false', 'Telegram配置', 'switch', NULL, '2022-08-28 18:13:20',
        '2022-11-07 00:23:54');
INSERT INTO `config`
VALUES (23, '管理员 Telegram ID', 'telegram_admin_id', '', 'Telegram配置', 'text', NULL, '2022-08-28 18:13:20',
        '2022-11-07 00:23:54');

DROP TABLE IF EXISTS `contract`;
CREATE TABLE `contract`
(
    `id`         int(10) UNSIGNED                                              NOT NULL AUTO_INCREMENT,
    `user_id`    varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户ID',
    `token`      varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '代币',
    `type`       tinyint(4)                                                    NOT NULL DEFAULT 1 COMMENT '1、数字货币 2、支付宝',
    `rate`       int(11)                                                       NOT NULL DEFAULT '0' COMMENT '费率',
    `cycle`      varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '周期',
    `expired_at` timestamp                                                     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '到期时间',
    `address`    varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL     DEFAULT NULL,
    `status`     tinyint(4)                                                    NOT NULL DEFAULT 1 COMMENT '状态',
    `created_at` timestamp                                                     NULL     DEFAULT NULL,
    `updated_at` timestamp                                                     NULL     DEFAULT NULL,
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `contract_user_id_token_status_index` (`user_id`, `token`, `status`) USING BTREE,
    INDEX `contract_user_id_index` (`user_id`) USING BTREE,
    INDEX `contract_token_index` (`token`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs`
(
    `id`         bigint(20) UNSIGNED                                       NOT NULL AUTO_INCREMENT,
    `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci     NOT NULL,
    `queue`      text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci     NOT NULL,
    `payload`    longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `exception`  longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `failed_at`  timestamp                                                 NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations`
(
    `id`        int(10) UNSIGNED                                              NOT NULL AUTO_INCREMENT,
    `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `batch`     int(11)                                                       NOT NULL,
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 29
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `oauth_access_tokens`;
CREATE TABLE `oauth_access_tokens`
(
    `id`         varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `user_id`    bigint(20) UNSIGNED                                           NULL DEFAULT NULL,
    `client_id`  bigint(20) UNSIGNED                                           NOT NULL,
    `name`       varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
    `scopes`     text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci         NULL,
    `revoked`    tinyint(1)                                                    NOT NULL,
    `created_at` timestamp                                                     NULL DEFAULT NULL,
    `updated_at` timestamp                                                     NULL DEFAULT NULL,
    `expires_at` datetime                                                      NULL DEFAULT NULL,
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `oauth_access_tokens_user_id_index` (`user_id`) USING BTREE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `oauth_auth_codes`;
CREATE TABLE `oauth_auth_codes`
(
    `id`         varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `user_id`    bigint(20) UNSIGNED                                           NOT NULL,
    `client_id`  bigint(20) UNSIGNED                                           NOT NULL,
    `scopes`     text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci         NULL,
    `revoked`    tinyint(1)                                                    NOT NULL,
    `expires_at` datetime                                                      NULL DEFAULT NULL,
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `oauth_auth_codes_user_id_index` (`user_id`) USING BTREE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `oauth_clients`;
CREATE TABLE `oauth_clients`
(
    `id`                     bigint(20) UNSIGNED                                           NOT NULL AUTO_INCREMENT,
    `user_id`                bigint(20) UNSIGNED                                           NULL DEFAULT NULL,
    `name`                   varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `secret`                 varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
    `provider`               varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
    `redirect`               text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci         NOT NULL,
    `personal_access_client` tinyint(1)                                                    NOT NULL,
    `password_client`        tinyint(1)                                                    NOT NULL,
    `revoked`                tinyint(1)                                                    NOT NULL,
    `created_at`             timestamp                                                     NULL DEFAULT NULL,
    `updated_at`             timestamp                                                     NULL DEFAULT NULL,
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `oauth_clients_user_id_index` (`user_id`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `oauth_personal_access_clients`;
CREATE TABLE `oauth_personal_access_clients`
(
    `id`         bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `client_id`  bigint(20) UNSIGNED NOT NULL,
    `created_at` timestamp           NULL DEFAULT NULL,
    `updated_at` timestamp           NULL DEFAULT NULL,
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `oauth_refresh_tokens`;
CREATE TABLE `oauth_refresh_tokens`
(
    `id`              varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `access_token_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `revoked`         tinyint(1)                                                    NOT NULL,
    `expires_at`      datetime                                                      NULL DEFAULT NULL,
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `oauth_refresh_tokens_access_token_id_index` (`access_token_id`) USING BTREE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders`
(
    `id`             int(10) UNSIGNED                                              NOT NULL AUTO_INCREMENT,
    `order_sn`       varchar(168) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '订单号',
    `user_id`        int(11)                                                       NOT NULL COMMENT '用户ID',
    `goods_price`    decimal(10, 2)                                                NOT NULL COMMENT '商品价格',
    `token_price`    decimal(10, 6)                                                         DEFAULT NULL COMMENT '代币金额',
    `final_amount`   decimal(10, 2)                                                         DEFAULT NULL COMMENT '净额',
    `notify_url`     varchar(168) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL     DEFAULT NULL COMMENT '回调地址',
    `return_url`     varchar(168) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL     DEFAULT NULL COMMENT '返回页面',
    `out_trade_no`   varchar(168) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL     DEFAULT NULL COMMENT '商家订单号',
    `address`        text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci         NULL COMMENT '支付地址',
    `transaction_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL     DEFAULT NULL COMMENT '交易哈希/支付方订单号',
    `status`         int(11)                                                       NOT NULL DEFAULT 0 COMMENT '状态 0:待支付 1:已过期 2:已支付 3:通知成功 4: 通知失败',
    `withdraw`       tinyint(3)                                                    NOT NULL DEFAULT 0 COMMENT '0:未结算 1:已结算',
    `type`           tinyint(4)                                                    NOT NULL DEFAULT 1 COMMENT '1:虚拟货币 2:支付宝 3:微信',
    `token`          varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL     DEFAULT NULL,
    `platform`       varchar(100) COLLATE utf8mb4_unicode_ci                                DEFAULT NULL COMMENT '支付场景',
    `settle_no`      varchar(50) COLLATE utf8mb4_unicode_ci                                 DEFAULT NULL COMMENT '结算单号',
    `payment_id`     int(11)                                                                DEFAULT NULL COMMENT '支付ID',
    `notified_at`    timestamp                                                     NULL     DEFAULT NULL,
    `created_at`     timestamp                                                     NULL     DEFAULT NULL,
    `updated_at`     timestamp                                                     NULL     DEFAULT NULL,
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `orders_user_id_status_index` (`user_id`, `status`) USING BTREE,
    INDEX `orders_order_sn_index` (`order_sn`) USING BTREE,
    INDEX `orders_user_id_index` (`user_id`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets`
(
    `email`      varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `token`      varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` timestamp                                                     NULL DEFAULT NULL,
    INDEX `password_resets_email_index` (`email`) USING BTREE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `shop`;
CREATE TABLE `shop`
(
    `id`               int(10) UNSIGNED                                              NOT NULL AUTO_INCREMENT,
    `name`             varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名称',
    `desc`             varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '介绍',
    `token`            varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '代币',
    `rate`             int(11)                                                       NOT NULL DEFAULT '0' COMMENT '费率',
    `image`            varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '图片',
    `type`             tinyint(3)                                                    NOT NULL DEFAULT 1,
    `status`           tinyint(3)                                                    NOT NULL DEFAULT 1,
    `sort`             int(3) UNSIGNED                                               NOT NULL DEFAULT 0,
    `month_price`      int(11)                                                       NULL     DEFAULT NULL COMMENT '月付',
    `quarter_price`    int(11)                                                       NULL     DEFAULT NULL COMMENT '季度',
    `half_year_price`  int(11)                                                       NULL     DEFAULT NULL COMMENT '半年',
    `year_price`       int(11)                                                       NULL     DEFAULT NULL COMMENT '年付',
    `three_year_price` int(11)                                                       NULL     DEFAULT NULL COMMENT '三年',
    `payment_ids`      json                                                                   DEFAULT NULL COMMENT '支付插件',
    `created_at`       timestamp                                                     NULL     DEFAULT NULL,
    `updated_at`       timestamp                                                     NULL     DEFAULT NULL,
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `shop_token_index` (`token`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 5
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  ROW_FORMAT = Dynamic;

INSERT INTO `shop`
VALUES (1, 'TRC20-USDT', '泰达币（USDT）是一种将加密货币与法定货币美元挂钩的虚拟货币。', 'USDT', 0.00, 'images/usdt.png',
        1, 1, 97, 10, 25, 45, 80, 200, null, '2022-08-16 20:45:26', '2022-08-16 20:53:15');
INSERT INTO `shop`
VALUES (2, 'ERC20-BTC', '比特币(BitCoin)是一种P2P形式的虚拟货币。', 'BTC', 0.00, 'images/btc.png', 1, 1, 96, 10, 25, 45,
        80, 200, null, '2022-08-16 20:45:26', '2022-08-16 20:53:15');
INSERT INTO `shop`
VALUES (3, 'ERC20-ETH', '以太坊（Ethereum）是一个去中心化的开源的有智能合约功能的公共区块链平台。', 'ETH', 0.00,
        'images/eth.png', 1, 1, 95, 10, 25, 45, 80, 200, null, '2022-08-16 20:45:26', '2022-08-26 14:55:04');
INSERT INTO `shop`
VALUES (4, '微信支付', '微信支付是腾讯集团旗下的第三方支付平台，致力于为用户和企业提供安全、便捷、专业的在线支付服务。',
        'wechat', 6.00, 'images/wechat.png', 3, 1, 100, 10, 25, 45, 80, 200, null, '2022-08-26 21:04:30',
        '2022-08-26 21:06:26');
INSERT INTO `shop`
VALUES (5, '微信支付(香港)',
        '微信支付(香港)是腾讯集团旗下的第三方支付平台，致力于为用户和企业提供安全、便捷、专业的在线支付服务。',
        'wechat_hk', 6.00, 'images/wechat.png', 5, 1, 100, 10, 25, 45, 80, 200, null, '2022-08-26 21:04:30',
        '2022-08-26 21:06:26');
INSERT INTO `shop`
VALUES (6, '支付宝', '支付宝，致力于为企业和个人提供“简单、安全、快速、便捷”的支付解决方案', 'alipay', 6.00,
        'images/alipay.png', 2, 1, 99, 10, 25, 45, 80, 200, null, '2022-08-26 21:04:30', '2022-08-26 21:06:26');
INSERT INTO `shop`
VALUES (7, '支付宝(香港)', '支付宝(香港)，致力于为企业和个人提供“简单、安全、快速、便捷”的支付解决方案', 'alipay_hk', 6.00,
        'images/alipay.png', 4, 1, 98, 10, 25, 45, 80, 200, null, '2022-08-26 21:04:30', '2022-08-26 21:06:26');

DROP TABLE IF EXISTS `tutorial`;
CREATE TABLE `tutorial`
(
    `id`          int(10) unsigned                        NOT NULL AUTO_INCREMENT,
    `name`        varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '名称',
    `description` text COLLATE utf8mb4_unicode_ci         NOT NULL COMMENT '描述',
    `download`    text COLLATE utf8mb4_unicode_ci COMMENT '下载地址',
    `status`      tinyint(4)                              NOT NULL DEFAULT '1' COMMENT '状态',
    `sort`        int(10) unsigned                        NOT NULL DEFAULT '0' COMMENT '排序',
    `created_at`  timestamp                               NULL     DEFAULT NULL,
    `updated_at`  timestamp                               NULL     DEFAULT NULL,
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 8
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  ROW_FORMAT = DYNAMIC;

INSERT INTO `tutorial`
VALUES (1, 'SSPanel-Metron对接教程',
        '### 1、将下面的代码复制到 config/.config.php\r\n\r\n```php\r\n// alpha\r\n$_ENV[\"alpha_api_url\"] = \"接口地址\";\r\n$_ENV[\"alpha_app_id\"] = \"您的AppID\";\r\n$_ENV[\"alpha_app_secret\"] = \"您的AppSecret\";\r\n```\r\n### 2、修改 .metron_setting.php 文件里面的支付选项\r\n```php\r\n$_MT[\"pay_alipay\"] = \"alpha\"; \r\n$_MT[\"pay_wxpay\"] = \"alpha\"; \r\n$_MT[\"pay_crypto\"] = \"alpha\"; \r\n```\r\n### 3、最后安装路径替换其他文件即可',
        'https://mega.nz/file/DldSzTSY#9xHvVeFugod-khmWf1zJ23O-__mXGNVW6etua1R7Q_c', 1, 0, '2022-08-25 10:56:28',
        '2022-09-05 16:11:15');
INSERT INTO `tutorial`
VALUES (2, 'SSPanel-Malio对接教程',
        '### 1、将下面的代码复制到 config/.config.php\r\n\r\n```php\r\n// alpha\r\n$_ENV[\'alpha_api_url\'] = \'接口地址\';\r\n$_ENV[\'alpha_app_id\'] = \'您的AppID\';\r\n$_ENV[\'alpha_app_secret\'] = \'您的AppSecret\';\r\n```\r\n\r\n### 2、修改 .malio_setting.php 文件里的支付选项\r\n```\r\n$Malio_Config[\'mups_alipay\'] = \'alpha\';\r\n$Malio_Config[\'mups_wechat\'] = \'alpha\';\r\n$Malio_Config[\'mups_crypto\'] = \'alpha\';\r\n```\r\n### 3、最后安装路径替换其他文件即可',
        'https://mega.nz/file/DldSzTSY#9xHvVeFugod-khmWf1zJ23O-__mXGNVW6etua1R7Q_c', 1, 0, '2022-08-25 11:41:58',
        '2022-09-05 15:44:56');
INSERT INTO `tutorial`
VALUES (3, 'SSPanel-UIM对接教程',
        '### 1、将其他文件按照路径替换或者添加进去\r\n### 2、导入根目录的【alpha.sql】文件到数据库\r\n### 3、项目根目录运行下面命令\r\n\r\ncomposer dumpautoload\r\n\r\n### 4、进入后台【设置中心】-【支付】-【alpha】 里面配置以下信息\r\n```\r\nApiHost 	= 	\'接口地址\';	\r\nAppID 		= 	\'您的AppID\';\r\nAppSecret 	= 	\'您的AppSecret\';\r\n```',
        'https://mega.nz/file/DldSzTSY#9xHvVeFugod-khmWf1zJ23O-__mXGNVW6etua1R7Q_c', 1, 0, '2022-08-25 11:58:44',
        '2022-09-05 16:19:07');
INSERT INTO `tutorial`
VALUES (4, 'V2board对接教程',
        '> 目前适配了v1.5.5和v1.6.0，其他版本请自行测试\r\n\r\n### 1、将其他文件按照路径替换或者添加进去\r\n\r\n### 2、进入后台【支付配置】-【添加支付方式】 里面配置以下信息\r\n\r\n```\r\nAPI地址 	= 	\'接口地址\';	\r\nAppID 	= 	\'您的AppID\';\r\nAppSecret 	= 	\'您的AppSecret\';\r\n```',
        'https://mega.nz/file/DldSzTSY#9xHvVeFugod-khmWf1zJ23O-__mXGNVW6etua1R7Q_c', 1, 0, '2022-08-25 12:00:46',
        '2022-09-05 16:32:11');
INSERT INTO `tutorial`
VALUES (5, 'WHCMS v8.13.对接教程',
        '### 1、按照路径添加文件\r\n\r\n\r\n### 2、打开后台\r\nSystem Setting -> Payment Gateways -> All Payment Gateways 启用扩展，并在 System Setting -> Payment Gateways -> Manage Existing Gateways 中配置相关信息。',
        'https://mega.nz/file/DldSzTSY#9xHvVeFugod-khmWf1zJ23O-__mXGNVW6etua1R7Q_c', 1, 0, '2022-08-25 12:02:00',
        '2022-08-25 12:02:00');
INSERT INTO `tutorial`
VALUES (6, '独角发卡对接教程',
        '### 1、打开独角发卡后台的支付配置页面，然后修改支付标识为coinbase的支付通道\r\n```\r\n商户 ID：您的AppID\r\n商户密钥：您的AppSecret\r\n支付方式：跳转\r\n```\r\n\r\n## 2、最后安装路径替换其他文件即可',
        'https://mega.nz/file/DldSzTSY#9xHvVeFugod-khmWf1zJ23O-__mXGNVW6etua1R7Q_c', 1, 0, '2022-08-25 12:02:58',
        '2022-08-25 12:02:58');
INSERT INTO `tutorial`
VALUES (7, '风铃发卡对接教程',
        '### 1、将【alpha】文件夹复制到 app\\Library\\Gateway\\Alpha 文件夹下面\r\n\r\n### 2、复制下面的代码\r\n```json\r\n{\r\n  \"api_url\": \"你的api地址\",\r\n  \"app_id\": \"你的app_id\",\r\n  \"app_secret\": \"你的app_secret\"\r\n}\r\n```\r\n\r\n\r\n### 3、打开发卡后台，支付渠道，点击添加子渠道\r\n![](https://c2.im5i.com/2022/09/05/51t24.png)\r\n\r\n### 4、修改前端支付选中\r\n![](https://c2.im5i.com/2022/09/05/51uaW.png)',
        'https://mega.nz/file/DldSzTSY#9xHvVeFugod-khmWf1zJ23O-__mXGNVW6etua1R7Q_c', 1, 0, '2022-08-25 12:06:09',
        '2022-09-05 16:37:51');
INSERT INTO `tutorial`
VALUES (8, '异次元发卡对接教程', '直接按照路径覆盖',
        'https://objectstorage.ap-tokyo-1.oraclecloud.com/n/nrfszetbihei/b/bucket-20220322-1910/o/yiciyuanfaka.zip', 1,
        0, '2022-08-25 12:06:09', '2022-09-05 16:37:51');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`
(
    `id`               bigint(20) UNSIGNED                                           NOT NULL AUTO_INCREMENT,
    `name`             varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL     DEFAULT NULL,
    `email`            varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL     DEFAULT NULL,
    `password`         varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL     DEFAULT NULL,
    `telegram_id`      bigint(20)                                                    NULL     DEFAULT NULL COMMENT 'Telegram_id',
    `telegram_account` varchar(168) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL     DEFAULT NULL COMMENT 'Telegram_name',
    `address`          varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL     DEFAULT NULL COMMENT '结算地址',
    `balance`          decimal(8, 2)                                                 NULL     DEFAULT 0.00,
    `usdt_rate`        double(8, 2)                                                  NOT NULL DEFAULT 6.30 COMMENT 'USDT费率',
    `status`           tinyint(3)                                                    NULL     DEFAULT 1,
    `last_ip`          varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL     DEFAULT NULL,
    `last_login`       timestamp                                                     NULL     DEFAULT NULL,
    `app_id`           varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL     DEFAULT NULL,
    `app_secret`       varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL     DEFAULT NULL,
    `expired_at`       int(11)                                                       NULL     DEFAULT NULL COMMENT '到期时间',
    `register_at`      timestamp                                                     NULL     DEFAULT NULL,
    `created_at`       timestamp                                                     NULL     DEFAULT NULL,
    `updated_at`       timestamp                                                     NULL     DEFAULT NULL,
    `deleted_at`       timestamp                                                     NULL     DEFAULT NULL,
    PRIMARY KEY (`id`) USING BTREE,
    UNIQUE INDEX `users_telegram_id_unique` (`telegram_id`) USING BTREE,
    UNIQUE INDEX `users_telegram_account_unique` (`telegram_account`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `verify_codes`;
CREATE TABLE `verify_codes`
(
    `id`         bigint(20) UNSIGNED                                           NOT NULL AUTO_INCREMENT,
    `address`    varchar(168) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '邮箱',
    `code`       varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci  NOT NULL COMMENT '验证码',
    `status`     varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '是否使用0: 未使用 1:已使用 2:已失效',
    `created_at` timestamp                                                     NULL     DEFAULT NULL,
    `updated_at` timestamp                                                     NULL     DEFAULT NULL,
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  ROW_FORMAT = Dynamic;

DROP TABLE IF EXISTS `settlement`;
CREATE TABLE `settlement`
(
    `id`              int(10) unsigned NOT NULL AUTO_INCREMENT,
    `settle_no`       varchar(50) COLLATE utf8mb4_unicode_ci  DEFAULT NULL COMMENT '结算单号',
    `user_id`         int(11)          NOT NULL COMMENT '用户id',
    `money`           decimal(8, 2)    NOT NULL COMMENT '结算金额',
    `usdt`            decimal(8, 3)    NOT NULL COMMENT 'USDT',
    `address`         varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '结算地址',
    `rate`            double(8, 2)                            DEFAULT NULL COMMENT '汇率',
    `settlement_time` timestamp        NULL                   DEFAULT NULL COMMENT '结算时间',
    `success_time`    timestamp        NULL                   DEFAULT NULL COMMENT '结算成功时间',
    `hash`            varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '交易哈希',
    `status`          tinyint(4)       NOT NULL               DEFAULT '0' COMMENT '状态 0:处理中 1:结算成功',
    `created_at`      timestamp        NULL                   DEFAULT NULL,
    `updated_at`      timestamp        NULL                   DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `settlement_user_id_status_index` (`user_id`, `status`),
    KEY `settlement_user_id_index` (`user_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments`
(
    `id`         int(11)      NOT NULL AUTO_INCREMENT,
    `name`       varchar(168) NOT NULL COMMENT '支付名称',
    `payment`    varchar(168) NOT NULL COMMENT '支付方式',
    `config`     json                  DEFAULT NULL COMMENT '支付配置',
    `period`     varchar(191)          DEFAULT NULL COMMENT '时间段',
    `price`      varchar(191)          DEFAULT NULL COMMENT '价格段',
    `sort`       int(11)      NOT NULL DEFAULT '0' COMMENT '排序',
    `created_at` timestamp    NULL     DEFAULT NULL,
    `updated_at` datetime              DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `index_payment` (`payment`) USING BTREE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `domain`;
CREATE TABLE `domain`
(
    `id`         int(11)      NOT NULL AUTO_INCREMENT,
    `user_id`    int(11)      NOT NULL COMMENT '用户ID',
    `domain`     varchar(191) NOT NULL COMMENT '域名',
    `status`     tinyint(4)   NOT NULL COMMENT '状态',
    `created_at` timestamp    NULL DEFAULT NULL,
    `updated_at` timestamp    NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
SET FOREIGN_KEY_CHECKS = 1;
