DROP TABLE IF EXISTS `pre__lapi_config`;
CREATE TABLE `pre__lapi_config` (
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '配置名称',
  `value` text COMMENT '配置值',
  `description` text COMMENT '配置说明',
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='API配置';

DROP TABLE IF EXISTS `pre__lapi_app`;
CREATE TABLE `pre__lapi_app` (
  `id` varchar(32) NOT NULL DEFAULT '' COMMENT 'id',
  `name` varchar(255) NOT NULL,
  `app_id` varchar(32) NOT NULL,
  `app_secret` varchar(32) NOT NULL,
  `description` varchar(255) NOT NULL COMMENT '授权描述',
  `allow_origin` tinyint(1) NOT NULL DEFAULT '0' COMMENT '允许跨域，1-允许',
  `is_check` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1-需要验证',
  `check_type` char(10) NOT NULL DEFAULT 'MD5' COMMENT '检测类型',
  `sign_postion` char(10) NOT NULL DEFAULT 'param' COMMENT '签名位置',
  `listorder` smallint(5) NULL DEFAULT '100' COMMENT '排序ID',
  `last_active` int(10) DEFAULT '0' COMMENT '上次活动时间',
  `last_ip` varchar(50) DEFAULT '' COMMENT '上次活动IP',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态，1-开启',
  `add_time` int(10) DEFAULT NULL COMMENT '添加时间',
  `add_ip` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `app_id` (`app_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `pre__lapi_app_log`;
CREATE TABLE `pre__lapi_app_log` (
  `id` varchar(32) NOT NULL DEFAULT '',
  `app_id` varchar(32) NOT NULL,
  `api` text NOT NULL COMMENT '请求API',
  `url` text NOT NULL COMMENT '请求完整链接',
  `method` varchar(10) NOT NULL DEFAULT '' COMMENT '请求类型',
  `useragent` text COMMENT '请求来源',
  `header` text COMMENT '请求头信息',
  `payload` longtext COMMENT '请求内容',
  `content` longtext COMMENT '请求原始内容',
  `cookie` longtext COMMENT '请求cookie',
  `add_time` int(10) DEFAULT '0' COMMENT '添加时间',
  `add_ip` varchar(50) CHARACTER SET utf8mb4 DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `app_id` (`app_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `pre__lapi_url`;
CREATE TABLE `pre__lapi_url` (
  `id` varchar(32) NOT NULL DEFAULT '' COMMENT 'id',
  `parentid` varchar(32) DEFAULT '0' COMMENT '上级ID',
  `title` varchar(200) NOT NULL DEFAULT '' COMMENT '中文名称',
  `slug` varchar(50) COMMENT '地址标识',
  `url` varchar(500) COMMENT '请求地址',
  `method` varchar(10) NOT NULL DEFAULT '' COMMENT '请求类型',
  `request` text COMMENT '请求字段，推荐JSON格式',
  `response` text COMMENT '响应字段，推荐JSON格式',
  `description` text COMMENT '描述',
  `listorder` smallint(5) NULL DEFAULT '100' COMMENT '排序ID',
  `status` tinyint(2) NULL DEFAULT '1' COMMENT '状态',
  `edit_time` int(10) DEFAULT '0' COMMENT '编辑时间',
  `edit_ip` varchar(50) CHARACTER SET utf8mb4 DEFAULT '',
  `add_time` int(10) DEFAULT '0' COMMENT '添加时间',
  `add_ip` varchar(50) CHARACTER SET utf8mb4 DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `url` (`url`),
  KEY `method` (`method`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='请求URL表';

DROP TABLE IF EXISTS `pre__lapi_url_access`;
CREATE TABLE `pre__lapi_url_access` (
  `id` varchar(32) NOT NULL DEFAULT '' COMMENT 'id',
  `app_id` varchar(32) NOT NULL DEFAULT '0',
  `url_id` varchar(32) NOT NULL DEFAULT '0',
  `max_request` int(10) DEFAULT '100' COMMENT '每秒最大请求数',
  UNIQUE KEY `app_id` (`app_id`,`url_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='请求URL与APP的关联表';

REPLACE INTO `pre__lapi_config` VALUES 
('api_close','0',NULL),
('api_close_tip','API系统维护中',NULL),
('api_app_pre','API','app的appid前缀'),
('open_putlog','1','是否启用记录日志');
