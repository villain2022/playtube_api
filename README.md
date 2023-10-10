
## Playtube_api，特意针对playtube开发的采集youtube程序。


## 运行环境
* Linux + Nginx + Mysql5.7 + PHP7.4



### v1.0.0 --20230619
* 采集youtube的api接口
* playtube主程序搭建好后搭建playtube_api采集程序，

* 进入playtube数据库，手工删除替换playtube原始表项：
~~~
//一、创建、替换表


CREATE TABLE `tp_api_key` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `api_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `limit` int(10) unsigned NOT NULL DEFAULT '0',
  `use_times` int(10) unsigned NOT NULL DEFAULT '0',
  `comment` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `error_times` int(10) unsigned NOT NULL DEFAULT '0',
  `today_status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `today_status` (`today_status`),
  KEY `status` (`status`),
  KEY `limit` (`limit`),
  KEY `use_times` (`use_times`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `channel_title` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `ip_address` varchar(150) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `password` varchar(70) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `first_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `last_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `gender` varchar(10) CHARACTER SET latin1 NOT NULL DEFAULT 'male',
  `email_code` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `device_id` varchar(200) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `language` varchar(22) CHARACTER SET latin1 NOT NULL DEFAULT 'english',
  `avatar` varchar(100) CHARACTER SET latin1 NOT NULL DEFAULT 'upload/photos/d-avatar.jpg',
  `cover` varchar(100) CHARACTER SET latin1 NOT NULL DEFAULT 'upload/photos/d-cover.jpg',
  `src` varchar(22) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `country_id` int(11) NOT NULL DEFAULT '0',
  `age` int(11) NOT NULL DEFAULT '0',
  `about` text COLLATE utf8_unicode_ci,
  `google` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `facebook` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `twitter` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `instagram` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `active` int(11) NOT NULL DEFAULT '0',
  `admin` int(11) NOT NULL DEFAULT '0',
  `verified` int(11) NOT NULL DEFAULT '0',
  `last_active` int(11) NOT NULL DEFAULT '0',
  `registered` varchar(40) CHARACTER SET latin1 NOT NULL DEFAULT '0000/00',
  `time` int(11) NOT NULL DEFAULT '0',
  `is_pro` int(11) NOT NULL DEFAULT '0',
  `pro_type` int(2) NOT NULL DEFAULT '0',
  `imports` int(11) NOT NULL DEFAULT '0',
  `uploads` int(11) NOT NULL DEFAULT '0',
  `wallet` float NOT NULL DEFAULT '0',
  `balance` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '0',
  `video_mon` int(10) NOT NULL DEFAULT '0',
  `age_changed` int(11) NOT NULL DEFAULT '0',
  `donation_paypal_email` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `user_upload_limit` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '0',
  `two_factor` int(11) NOT NULL DEFAULT '0',
  `google_secret` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `authy_id` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `two_factor_method` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `last_month` text CHARACTER SET utf8,
  `active_time` int(50) NOT NULL DEFAULT '0',
  `active_expire` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '0',
  `phone_number` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `subscriber_price` varchar(11) CHARACTER SET utf8 NOT NULL DEFAULT '0',
  `monetization` int(11) NOT NULL DEFAULT '0',
  `new_email` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `fav_category` varchar(400) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `total_ads` float NOT NULL DEFAULT '0',
  `suspend_upload` int(11) NOT NULL DEFAULT '0',
  `suspend_import` int(11) NOT NULL DEFAULT '0',
  `paystack_ref` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `ConversationId` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `point_day_expire` int(50) NOT NULL DEFAULT '0',
  `points` float unsigned NOT NULL DEFAULT '0',
  `daily_points` int(11) NOT NULL DEFAULT '0',
  `converted_points` float NOT NULL DEFAULT '0',
  `info_file` varchar(300) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `google_tracking_code` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `newsletters` int(11) NOT NULL DEFAULT '0',
  `vk` varchar(18) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `qq` varchar(18) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `wechat` varchar(18) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `discord` varchar(18) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `mailru` varchar(18) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `linkedIn` varchar(18) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `pause_history` int(2) NOT NULL DEFAULT '0',
  `tv_code` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `permission` varchar(3000) CHARACTER SET utf8 DEFAULT NULL,
  `referrer` int(11) NOT NULL DEFAULT '0',
  `ref_user_id` int(11) NOT NULL DEFAULT '0',
  `ref_type` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `privacy` varchar(500) CHARACTER SET utf8 NOT NULL DEFAULT '{"show_subscriptions_count":"yes","who_can_message_me":"all","who_can_watch_my_videos":"all"}',
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `email` (`email`),
  KEY `password` (`password`),
  KEY `last_active` (`last_active`),
  KEY `admin` (`admin`),
  KEY `active` (`active`),
  KEY `registered` (`registered`),
  KEY `is_pro` (`is_pro`),
  KEY `wallet` (`wallet`),
  KEY `balance` (`balance`),
  KEY `video_mon` (`video_mon`),
  KEY `active_time` (`active_time`),
  KEY `pause_history` (`pause_history`),
  KEY `tv_code` (`tv_code`),
  KEY `permission` (`permission`(1024)),
  KEY `converted_points` (`converted_points`),
  KEY `referrer` (`referrer`),
  KEY `ref_user_id` (`ref_user_id`),
  KEY `pro_type` (`pro_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

CREATE TABLE `videos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `video_id` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `channel_title` varchar(200) NOT NULL DEFAULT '',
  `short_id` varchar(10) NOT NULL DEFAULT '',
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `thumbnail` varchar(500) CHARACTER SET latin1 NOT NULL DEFAULT 'upload/photos/thumbnail.jpg',
  `video_location` varchar(3000) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `youtube` varchar(32) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `vimeo` varchar(32) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `daily` varchar(32) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `facebook` varchar(100) NOT NULL DEFAULT '',
  `instagram` varchar(100) NOT NULL DEFAULT '',
  `ok` varchar(100) NOT NULL DEFAULT '',
  `twitch` varchar(100) NOT NULL DEFAULT '',
  `twitch_type` varchar(50) NOT NULL DEFAULT '',
  `embed` int(2) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL DEFAULT '0',
  `time_date` varchar(50) NOT NULL DEFAULT '',
  `active` int(11) NOT NULL DEFAULT '0',
  `tags` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `duration` varchar(33) CHARACTER SET latin1 NOT NULL DEFAULT '00:00',
  `size` bigint(20) NOT NULL DEFAULT '0',
  `converted` int(5) NOT NULL DEFAULT '1',
  `category_id` int(11) NOT NULL DEFAULT '0',
  `views` int(11) NOT NULL DEFAULT '0',
  `featured` int(11) NOT NULL DEFAULT '0',
  `registered` varchar(30) CHARACTER SET latin1 NOT NULL DEFAULT '0000/00',
  `privacy` int(11) NOT NULL DEFAULT '0',
  `age_restriction` int(11) NOT NULL DEFAULT '1',
  `type` varchar(100) NOT NULL DEFAULT '',
  `approved` int(11) NOT NULL DEFAULT '1',
  `240p` int(11) NOT NULL DEFAULT '0',
  `360p` int(11) NOT NULL DEFAULT '0',
  `480p` int(11) NOT NULL DEFAULT '0',
  `720p` int(11) NOT NULL DEFAULT '0',
  `1080p` int(11) NOT NULL DEFAULT '0',
  `2048p` int(11) NOT NULL DEFAULT '0',
  `4096p` int(11) NOT NULL DEFAULT '0',
  `sell_video` float unsigned NOT NULL DEFAULT '0',
  `sub_category` varchar(100) NOT NULL DEFAULT '',
  `geo_blocking` varchar(200) NOT NULL DEFAULT '',
  `demo` varchar(3000) NOT NULL DEFAULT '',
  `gif` varchar(3000) NOT NULL DEFAULT '',
  `is_movie` int(11) NOT NULL DEFAULT '0',
  `stars` varchar(200) NOT NULL DEFAULT '',
  `producer` varchar(200) NOT NULL DEFAULT '',
  `country` varchar(50) NOT NULL DEFAULT '',
  `movie_release` varchar(4) NOT NULL DEFAULT '',
  `quality` varchar(11) NOT NULL DEFAULT '',
  `rating` varchar(11) NOT NULL DEFAULT '',
  `monetization` int(11) NOT NULL DEFAULT '1',
  `rent_price` int(11) NOT NULL DEFAULT '0',
  `stream_name` varchar(150) NOT NULL DEFAULT '',
  `live_time` int(50) NOT NULL DEFAULT '0',
  `live_ended` int(11) NOT NULL DEFAULT '0',
  `agora_resource_id` text,
  `agora_sid` varchar(500) NOT NULL DEFAULT '',
  `agora_token` text,
  `license` varchar(100) NOT NULL DEFAULT '',
  `is_stock` int(11) NOT NULL DEFAULT '0',
  `trailer` varchar(3000) NOT NULL DEFAULT '',
  `embedding` int(11) NOT NULL DEFAULT '0',
  `live_chating` varchar(11) NOT NULL DEFAULT 'on',
  `publication_date` int(50) NOT NULL DEFAULT '0',
  `is_short` int(11) NOT NULL DEFAULT '0',
  `featured_movie` int(2) NOT NULL DEFAULT '0',
  `from_url` varchar(100) NOT NULL DEFAULT '',
  `url_error` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `video_id_2` (`video_id`),
  KEY `youtube_id` (`youtube`),
  KEY `vimeo` (`vimeo`),
  KEY `daily` (`daily`),
  KEY `user_id` (`user_id`),
  KEY `category_id` (`category_id`),
  KEY `featured` (`featured`),
  KEY `registered` (`registered`),
  KEY `views` (`views`),
  KEY `time` (`time`),
  KEY `order1` (`category_id`,`id`),
  KEY `order2` (`id`,`views`),
  KEY `240p` (`240p`),
  KEY `360p` (`360p`),
  KEY `480p` (`480p`),
  KEY `720p` (`720p`),
  KEY `1080p` (`1080p`),
  KEY `4096p` (`4096p`),
  KEY `2048` (`2048p`),
  KEY `privacy` (`privacy`),
  KEY `short_id` (`short_id`),
  KEY `age_restriction` (`age_restriction`),
  KEY `approved` (`approved`),
  KEY `twitch` (`twitch`),
  KEY `sub_category` (`sub_category`),
  KEY `geo_blocking` (`geo_blocking`),
  KEY `sell_video` (`sell_video`),
  KEY `is_movie` (`is_movie`),
  KEY `ok` (`ok`),
  KEY `is_short` (`is_short`),
  KEY `is_stock` (`is_stock`),
  KEY `time_date` (`time_date`),
  KEY `publication_date` (`publication_date`),
  KEY `live_time` (`live_time`),
  KEY `embed` (`embed`),
  KEY `category_id_2` (`category_id`,`id`),
  KEY `featured_movie` (`featured_movie`),
  KEY `from_url` (`from_url`),
  FULLTEXT KEY `description` (`description`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `tags` (`tags`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

~~~

* 去google官方申请youtube的接口api，将申请好的api添加到playtube_api后台
* 切换到playtube_api目录运行：php think youtube_search ，程序会自动根据关键词搜索入库youtube视频
* keywords文件：/playtube_api/extend/keyword/keywords.txt  关键词一行一个


## 配置相关

* ./config/app.php
* ./config/database.php
* 后台： https://your_domain.com/admin.php/login/login  admin admin888
















































