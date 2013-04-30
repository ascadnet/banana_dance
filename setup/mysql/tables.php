<?php


/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	http://www.ascadnetworks.com/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: MySQL Tables.
	
	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 2 of the License, or
	(at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.

====================================================== */


//	utf8_general_ci is faster, good for english sites.
//	utf8_unicode_ci is slower but better for non-english, latin-language sites.

// English
if ($_POST['language'] == 'english') {
	$charset = 'utf8_general_ci';
}
else if ($_POST['language'] == 'various') {
	$charset = 'utf8_unicode_ci';
}
// czech, danish, esperanto, estonian, hungarian, icelandic, latvian, lithuanian, persian, 
// polish, roman, romanian, slovak, slovenian, spanish, spanish2, swedish, turkish
else {
	$charset = 'utf8_' . $_POST['language'] . '_ci';
}

// Clean this up...
$_POST['db_prefix'] = addslashes($_POST['db_prefix']);


$q = mysql_query("
CREATE TABLE IF NOT EXISTS `" . $_POST['db_prefix'] . "articles` (
  `id` int(7) NOT NULL auto_increment,
  `category` mediumint(6) NOT NULL,
  `owner` varchar(65) NOT NULL,
  `name` varchar(150) NOT NULL,
  `content` mediumtext NOT NULL,
  `created` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  `last_updated_by` varchar(65) NOT NULL,
  `meta_title` varchar(50) NOT NULL,
  `meta_desc` varchar(255) NOT NULL,
  `meta_keywords` varchar(150) NOT NULL,
  `order` smallint(6) NOT NULL,
  `views` int(8) NOT NULL,
  `upvoted` int(8) NOT NULL,
  `downvoted` int(8) NOT NULL,
  `allow_comments` tinyint(4) NOT NULL,
  `allow_ratings` tinyint(1) NOT NULL,
  `show_stats` tinyint(1) NOT NULL,
  `login_to_view` tinyint(1) NOT NULL,
  `public` tinyint(1) NOT NULL,
  `display_on_sidebar` tinyint(1) NOT NULL,
  `email_comment_posted` tinyint(1) NOT NULL,
  `sharing_options` tinyint(1) NOT NULL,
  `max_threading` tinyint(2) NOT NULL,
  `login_to_comment` tinyint(1) NOT NULL,
  `in_widgets` tinyint(1) NOT NULL,
  `comment_hide_threshold` varchar(3) NOT NULL,
  `allow_comment_edits` int(1) NOT NULL,
  `default_comment_type_show` smallint(3) NOT NULL,
  `template` mediumint(5) NOT NULL,
  `comment_thread_style` enum('Forum','Tree') NOT NULL,
  `redirect` varchar(150) NOT NULL,
  `format_type` tinyint(1) NOT NULL default '1' COMMENT '1 = Wiki standard / 2 = CMS',
  `locked` int(12) NOT NULL,
  `locked_to` int(8) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `category` (`category`),
  KEY `owner` (`owner`),
  FULLTEXT KEY `name` (`name`,`content`),
  FULLTEXT KEY `name_2` (`name`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM  CHARACTER SET utf8 COLLATE $charset;
");

$q = mysql_query("
CREATE TABLE IF NOT EXISTS `" . $_POST['db_prefix'] . "activity` (
  `id` int(9) NOT NULL auto_increment,
  `user` int(9) NOT NULL,
  `type` enum('newpages','mentions','editpages','badges','newuser','comment','profilepost') NOT NULL,
  `act_id` int(9) NOT NULL,
  `sup_id` int(7) NOT NULL,
  `act_name` varchar(150) NOT NULL,
  `category_id` int(7) NOT NULL,
  `date` datetime NOT NULL,
  `post` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user` (`user`,`type`,`act_id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM  CHARACTER SET utf8 COLLATE $charset;
");


$q = mysql_query("
  CREATE TABLE `" . $_POST['db_prefix'] . "articles_history` (
  `id` mediumint(5) NOT NULL auto_increment,
  `article_id` int(8) NOT NULL,
  `user` varchar(65) NOT NULL,
  `ip` VARCHAR( 39 ) NOT NULL,
  `magnitude` TINYINT( 1 ) NOT NULL COMMENT '1 = Significant edit / 2 = Minor Edit',
  `category` mediumint(6) NOT NULL,
  `name` varchar(150) NOT NULL,
  `content` MEDIUMTEXT NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `category` (`category`),
  KEY `article_id` (`article_id`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM  CHARACTER SET utf8 COLLATE $charset;
");


$q = mysql_query("
  CREATE TABLE `" . $_POST['db_prefix'] . "attachments` (
  `id` VARCHAR( 15 ) NOT NULL ,
  `path` VARCHAR( 150 ) NOT NULL ,
  `server_path` VARCHAR( 150 ) NOT NULL,
  `filename` VARCHAR( 65 ) NOT NULL,
  `downloads` INT( 8 ) NOT NULL ,
  `owner` VARCHAR( 100 ) NOT NULL ,
  `login` TINYINT( 1 ) NOT NULL ,
  `limit` TINYINT( 1 ) NOT NULL ,
  PRIMARY KEY (  `id` ) ,
  INDEX (  `path` )
) ENGINE=MyISAM  CHARACTER SET utf8 COLLATE $charset;
");


$q = mysql_query("
  CREATE TABLE  `" . $_POST['db_prefix'] . "article_redirects` (
  `id` INT( 8 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `old_category` VARCHAR( 100 ) NOT NULL,
  `old_article` VARCHAR( 100 ) NOT NULL,
  `new_article_id` INT( 8 ) NOT NULL,
  `date` TIMESTAMP NOT NULL,
   INDEX (`old_category`,`old_article`)
  ) ENGINE = MYISAM  CHARACTER SET utf8 COLLATE $charset;
");


$q = mysql_query("
CREATE TABLE `" . $_POST['db_prefix'] . "article_tags` (
  `id` int(11) NOT NULL auto_increment,
  `page_id` int(8) NOT NULL,
  `tag` varchar(25) NOT NULL,
  `date` datetime NOT NULL,
  `category` mediumint(6) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `page_id` (`page_id`,`tag`),
  KEY `category` (`category`)
  ) ENGINE = MYISAM  CHARACTER SET utf8 COLLATE $charset;
");


$q = mysql_query("
  CREATE TABLE `" . $_POST['db_prefix'] . "attachments_dls` (
  `id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  `date` DATETIME NOT NULL ,
  `ip` VARCHAR( 20 ) NOT NULL ,
  `host` VARCHAR( 85 ) NOT NULL ,
  `user` VARCHAR( 100 ) NOT NULL ,
  `dl` VARCHAR( 14 ) NOT NULL ,
  INDEX (  `ip` ,  `user`, `dl` )
) ENGINE=MyISAM  CHARACTER SET utf8 COLLATE $charset;
");


$q = mysql_query("
  CREATE TABLE `" . $_POST['db_prefix'] . "banned` (
  `id` mediumint(6) NOT NULL auto_increment,
  `date` datetime NOT NULL,
  `ip` varchar(30) NOT NULL,
  `username` varchar(65) NOT NULL,
  `email` varchar(150) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `banned_by` varchar(65) NOT NULL,
  `banned_until` varchar(12) NOT NULL,
  `ban_type` tinyint(1) NOT NULL COMMENT '0 = full / 1 = partial',
  PRIMARY KEY  (`id`),
  KEY `ip` (`ip`),
  KEY `email` (`email`),
  KEY `username` (`username`)
) ENGINE=MyISAM  CHARACTER SET utf8 COLLATE $charset;
");


$q = mysql_query("
  CREATE TABLE `" . $_POST['db_prefix'] . "badges` (
  `id` MEDIUMINT( 4 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  `name` VARCHAR( 35 ) NOT NULL ,
  `desc` VARCHAR( 255 ) NOT NULL,
  `color` VARCHAR( 6 ) NOT NULL ,
  `font_color` VARCHAR( 6 ) NOT NULL ,
  `points_required` INT( 6 ) NOT NULL,
  `act` ENUM(  'score',  'article_edit',  'article_add',  'comment_post',  'comment_status_changed' ) NOT NULL ,
  `act_id` VARCHAR( 20 ) NOT NULL
) ENGINE=MyISAM  CHARACTER SET utf8 COLLATE $charset;
");

$q = mysql_query("
  CREATE TABLE `" . $_POST['db_prefix'] . "badge_history` (
  `id` INT( 9 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  `user_id` INT( 8 ) NOT NULL ,
  `badge` MEDIUMINT( 5 ) NOT NULL,
  `date` DATETIME NOT NULL ,
  INDEX (  `user_id` )
) ENGINE=MyISAM  CHARACTER SET utf8 COLLATE $charset;
");

$q = mysql_query("
  CREATE TABLE `" . $_POST['db_prefix'] . "categories` (
  `id` mediumint(5) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  `subcat` mediumint(4) NOT NULL,
  `order` mediumint(4) NOT NULL,
  `allow_article_creation` tinyint(1) NOT NULL COMMENT '1 = Users with the \"can_create_articles\" privilege can create an article.',
  `home_article` mediumint(5) NOT NULL,
  `template` MEDIUMINT( 5 ) NOT NULL,
  `public` TINYINT( 1 ) NOT NULL DEFAULT '1',
  `base` TINYINT( 1) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`id`),
  KEY `subcat` (`subcat`)
) ENGINE=MyISAM  CHARACTER SET utf8 COLLATE $charset;
");


$q = mysql_query("
  CREATE TABLE `" . $_POST['db_prefix'] . "comments` (
  `id` int(8) NOT NULL auto_increment,
  `date` datetime NOT NULL,
  `user` varchar(100) NOT NULL,
  `comment` text NOT NULL,
  `article` mediumint(6) NOT NULL,
  `up` mediumint(5) NOT NULL,
  `down` mediumint(5) NOT NULL,
  `pending` tinyint(1) NOT NULL,
  `status` tinyint(2) NOT NULL COMMENT 'Corresponds to " . $_POST['db_prefix'] . "comment_statuses',
  `subcomment` mediumint(6) NOT NULL,
  `last_edited` datetime NOT NULL,
  `deleted` datetime NOT NULL,
  `deleted_by` varchar(65) NOT NULL,
  `edits` smallint(3) NOT NULL,
  `edited_by` varchar(65) NOT NULL,
  `contract_subcomments` tinyint(1) NOT NULL,
  `ip` VARCHAR( 25 ) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `article` (`article`),
  KEY `user` (`user`),
  KEY `ip` (`ip`),
  KEY `subcomment` (`subcomment`)
) ENGINE=MyISAM  CHARACTER SET utf8 COLLATE $charset;
");


$q = mysql_query("
  CREATE TABLE `" . $_POST['db_prefix'] . "comment_ratings` (
  `id` int(8) NOT NULL auto_increment,
  `comment` int(8) NOT NULL,
  `user` varchar(150) NOT NULL,
  `ip` varchar(35) NOT NULL,
  `rating` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `comment` (`comment`),
  KEY `user` (`user`)
) ENGINE=MyISAM  CHARACTER SET utf8 COLLATE $charset;
");

$q = mysql_query("
  CREATE TABLE `" . $_POST['db_prefix'] . "comment_statuses` (
  `id` tinyint(2) NOT NULL auto_increment,
  `title` varchar(50) NOT NULL,
  `desc` varchar(255) NOT NULL,
  `contract_subcomments` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  CHARACTER SET utf8 COLLATE $charset;
");


$q = mysql_query("
  CREATE TABLE `" . $_POST['db_prefix'] . "custom_actions` (
  `id` mediumint(4) NOT NULL auto_increment,
  `action` varchar(35) NOT NULL,
  `criteria` text NOT NULL COMMENT 'Serialized array of field and required value in data array.',
  `mysql_command` text NOT NULL,
  `code` text NOT NULL,
  `run_order` mediumint(4) NOT NULL,
  `include` VARCHAR( 150 ) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `action` (`action`)
) ENGINE=MyISAM  CHARACTER SET utf8 COLLATE $charset;
");


$q = mysql_query("
  CREATE TABLE IF NOT EXISTS `" . $_POST['db_prefix'] . "custom_callers` (
	  `id` int(8) NOT NULL auto_increment,
	  `caller` varchar(65) NOT NULL,
	  `replacement` varchar(255) NOT NULL,
	  `category` tinyint(1) NOT NULL COMMENT 'If set to 1, the user will be linked to category.',
	  `type` enum('caller','bubble','link') NOT NULL,
	  `limit_to_category` mediumint(6) NOT NULL COMMENT 'Category ID if we are limiting the scope of the caller.',
	  PRIMARY KEY  (`id`),
	  KEY `limit_to_category` (`limit_to_category`)
  ) ENGINE=MyISAM  CHARACTER SET utf8 COLLATE $charset;
");


$q = mysql_query("
  CREATE TABLE `" . $_POST['db_prefix'] . "errors` (
  `id` INT( 8 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  `file` VARCHAR( 150 ) NOT NULL ,
  `referrer` VARCHAR( 200 ) NOT NULL ,
  `date` DATETIME NOT NULL ,
  `ip` VARCHAR( 35 ) NOT NULL ,
  `type` VARCHAR( 3 ) NOT NULL ,
  INDEX (  `file` )
  ) ENGINE = MYISAM CHARACTER SET utf8 COLLATE $charset;
");


$q = mysql_query("
  CREATE TABLE  `" . $_POST['db_prefix'] . "favorites` (
  `id` INT( 9 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  `article` INT( 9 ) NOT NULL ,
  `user_id` INT( 8 ) NOT NULL ,
  `date` DATETIME NOT NULL ,
  INDEX (  `article` ,  `user_id` )
  ) ENGINE = MYISAM CHARACTER SET utf8 COLLATE $charset;
");

$q = mysql_query("
  CREATE TABLE  `" . $_POST['db_prefix'] . "fbconnect` (
  `fb_id` INT( 10 ) NOT NULL ,
  `user_id` INT( 8 ) NOT NULL ,
  PRIMARY KEY (  `fb_id` ) ,
  INDEX (  `user_id` )
  ) ENGINE = MYISAM CHARACTER SET utf8 COLLATE $charset;
");

$q = mysql_query("
  CREATE TABLE `" . $_POST['db_prefix'] . "fields` (
  `id` varchar(40) NOT NULL,
  `display_name` varchar(80) NOT NULL,
  `description` text NOT NULL,
  `styling` text NOT NULL,
  `type` tinyint(2) NOT NULL COMMENT '1 = text, 2 = textarea, 3 = select, 4 = multi-select, 5 = radio, 6 = checkbox, 7 = multi-checkbox, 8 = hidden, 9 = date, 10 = linkert, 11 = file_upload, 12 = terms',
  `secondary_type` tinyint(1) NOT NULL COMMENT 'Limited to \"text\" type fields: 1 = random_id, 2 = url, 3 = email, 4 = phone, 5 = state, 6 = country',
  `encrypted` tinyint(1) NOT NULL,
  `options` text NOT NULL,
  `draw_options_from_field` smallint(5) NOT NULL,
  `default_value` varchar(150) NOT NULL,
  `maxlength` mediumint(5) NOT NULL,
  `style_hide` tinyint(1) NOT NULL COMMENT 'Used when the field needs to be invoked through JS conditional logic.',
  `js_conditional_logic` text NOT NULL,
  `js_required_format` varchar(100) NOT NULL,
  `js_character_limits` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE $charset;
");


$q = mysql_query("
  CREATE TABLE `" . $_POST['db_prefix'] . "fields_sets` (
  `id` mediumint(5) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `order` mediumint(4) NOT NULL,
  `cols` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  CHARACTER SET utf8 COLLATE $charset;
");

$q = mysql_query("
  CREATE TABLE `" . $_POST['db_prefix'] . "fields_sets_comps` (
  `id` mediumint(6) NOT NULL auto_increment,
  `set_id` mediumint(5) NOT NULL,
  `field_id` varchar(40) NOT NULL,
  `col` tinyint(1) NOT NULL default '1',
  `req` tinyint(1) NOT NULL,
  `tabindex` mediumint(4) NOT NULL,
  `order` mediumint(5) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `set_id` (`set_id`)
) ENGINE=MyISAM  CHARACTER SET utf8 COLLATE $charset;
");


$q = mysql_query("
  CREATE TABLE `" . $_POST['db_prefix'] . "fields_sets_locations` (
  `id` mediumint(6) NOT NULL auto_increment,
  `set_id` mediumint(6) NOT NULL,
  `location` mediumint(5) NOT NULL COMMENT '10001 = User Edit / 10002 = Admin Edit / 10003 = Forced Updates',
  `order` mediumint(4) NOT NULL,
  `page` tinyint(2) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `set_id` (`set_id`,`location`)
) ENGINE=MyISAM  CHARACTER SET utf8 COLLATE $charset;
");

$q = mysql_query("  
  CREATE TABLE `" . $_POST['db_prefix'] . "following` (
    `id` int(9) NOT NULL auto_increment,
    `article` int(8) NOT NULL,
    `user_id` int(8) NOT NULL,
    `date` datetime NOT NULL,
    `type` TINYINT( 1 ) NOT NULL COMMENT  '1 = email, 2 = notice',
    PRIMARY KEY  (`id`),
    KEY `article` (`article`,`user_id`)
) ENGINE=MyISAM  CHARACTER SET utf8 COLLATE $charset;
");

$q = mysql_query("
  CREATE TABLE  `" . $_POST['db_prefix'] . "item_options` (
  `id` INT( 9 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  `key` VARCHAR( 25 ) NOT NULL ,
  `value` TEXT NOT NULL ,
  `act_id` VARCHAR( 20 ) NOT NULL ,
  `type` ENUM(  'article',  'category',  'comment_type',  'default',  'user' ) NOT NULL ,
  INDEX (  `key` ,  `act_id`, `type` )
) ENGINE=MyISAM  CHARACTER SET utf8 COLLATE $charset;
");


$q = mysql_query("
CREATE TABLE IF NOT EXISTS `" . $_POST['db_prefix'] . "media` (
  `id` int(9) NOT NULL auto_increment,
  `location` varchar(255) NOT NULL,
  `filename` varchar(100) NOT NULL,
  `title` varchar(150) NOT NULL,
  `caption` varchar(255) NOT NULL,
  `owner` int(9) NOT NULL,
  `date` datetime NOT NULL,
  `public` tinyint(1) NOT NULL COMMENT 'Refers to editing and whether it appears in all user media galleries.',
  `folder` VARCHAR( 50 ) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `location` (`location`),
  KEY `owner` (`owner`),
  KEY `date` (`date`),
  KEY `folder` (`folder`)
) ENGINE=MyISAM  CHARACTER SET utf8 COLLATE $charset;
");


$q = mysql_query("
CREATE TABLE IF NOT EXISTS `" . $_POST['db_prefix'] . "media_tags` (
  `id` int(9) NOT NULL auto_increment,
  `img_id` int(9) NOT NULL,
  `tag` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `img_id` (`img_id`),
  KEY `tag` (`tag`)
) ENGINE=MyISAM  CHARACTER SET utf8 COLLATE $charset;
");


$q = mysql_query("
  CREATE TABLE `" . $_POST['db_prefix'] . "options` (
  `id` int(8) NOT NULL auto_increment,
  `plugin` VARCHAR( 65 ) NOT NULL,
  `type` tinyint(1) NOT NULL COMMENT '1 = Option / 2 = Option Group Title / 3 = Static Option / 4 = plugin',
  `key` varchar(35) NOT NULL COMMENT 'Option or template name',
  `value` varchar(150) NOT NULL,
  `group` tinyint(2) NOT NULL,
  `display_name` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `fixed_selections` varchar(255) NOT NULL,
  `field_type` tinyint(1) NOT NULL COMMENT '1 = yes/no / 2 = text / 3 = select / 4 = file upload',
  `field_width` smallint(3) NOT NULL,
  `left_padding` smallint(3) NOT NULL,
  `field_order` mediumint(4) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `key` (`key`),
  KEY `plugin` (`plugin`)
) ENGINE=MyISAM  CHARACTER SET utf8 COLLATE $charset;
");


$q = mysql_query("
  CREATE TABLE `" . $_POST['db_prefix'] . "page_ratings` (
    `id` int(8) NOT NULL auto_increment,
    `page` int(8) NOT NULL,
    `user` varchar(150) NOT NULL,
    `ip` varchar(35) NOT NULL,
    `rating` tinyint(1) NOT NULL,
    PRIMARY KEY  (`id`),
    KEY `page` (`page`),
    KEY `user` (`user`)
  ) ENGINE=MyISAM  CHARACTER SET utf8 COLLATE $charset;
");

$q = mysql_query("
  CREATE TABLE  `" . $_POST['db_prefix'] . "point_log` (
  `id` INT( 9 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  `date` DATETIME NOT NULL ,
  `user_credited` INT( 9 ) NOT NULL ,
  `points` MEDIUMINT( 5 ) NOT NULL ,
  `task` VARCHAR( 25 ) NOT NULL ,
  `acted_on` VARCHAR( 65 ) NOT NULL ,
  `ip` VARCHAR( 32 ) NOT NULL ,
  INDEX (  `user_credited` )
) ENGINE=MyISAM  CHARACTER SET utf8 COLLATE $charset;
");


$q = mysql_query("
CREATE TABLE IF NOT EXISTS `" . $_POST['db_prefix'] . "point_values` (
  `id` int(9) NOT NULL auto_increment,
  `task` varchar(25) NOT NULL,
  `points` mediumint(5) NOT NULL,
  `required` mediumint(5) NOT NULL,
  `act_on` enum('user','act_on') NOT NULL,
  `act_on_id` varchar(20) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `task` (`task`)
) ENGINE=MyISAM  CHARACTER SET utf8 COLLATE $charset;
");


$q = mysql_query("
  CREATE TABLE `" . $_POST['db_prefix'] . "rating` (
  `id` int(8) NOT NULL auto_increment,
  `article` mediumint(6) NOT NULL,
  `rating` tinyint(1) NOT NULL,
  `ip` varchar(35) NOT NULL,
  `user` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `article` (`article`),
  KEY `user` (`user`)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE $charset;
");


$q = mysql_query("
  CREATE TABLE `" . $_POST['db_prefix'] . "sent_emails` (
  `id` varchar(20) NOT NULL,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `template` varchar(35) NOT NULL,
  `headers` text NOT NULL,
  `content` text NOT NULL,
  `format` tinyint(1) NOT NULL,
  `username` varchar(65) NOT NULL,
  `opened` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `template` (`template`)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE $charset;
");


$q = mysql_query("
  CREATE TABLE `" . $_POST['db_prefix'] . "sessions` (
  `id` varchar(25) NOT NULL,
  `user` varchar(150) NOT NULL,
  `started` datetime NOT NULL,
  `last_activity` datetime NOT NULL,
  `ended` datetime NOT NULL,
  `remember` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user` (`user`)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE $charset;
");


$q = mysql_query("
  CREATE TABLE `" . $_POST['db_prefix'] . "spam` (
  `id` varchar(12) NOT NULL,
  `captcha` varchar(20) NOT NULL,
  `failed_captcha` tinyint(2) NOT NULL,
  `proven_captcha` tinyint(1) NOT NULL,
  `update` tinyint(2) NOT NULL,
  `ip` varchar(25) NOT NULL,
  `out_until` varchar(12) NOT NULL,
  `started` varchar(12) NOT NULL,
  `last_activity` varchar(12) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `ip` (`ip`),
  KEY `captcha` (`captcha`)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE $charset;
");


$q = mysql_query("
  CREATE TABLE IF NOT EXISTS `" . $_POST['db_prefix'] . "stripped_privs` (
  `id` int(9) NOT NULL auto_increment,
  `category` mediumint(5) NOT NULL,
  `privilege` varchar(60) NOT NULL,
  `group` mediumint(5) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `category` (`category`,`privilege`,`group`)
  ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE $charset;
");

$q = mysql_query("
  CREATE TABLE `" . $_POST['db_prefix'] . "tasks` (
  `id` int(8) NOT NULL auto_increment,
  `date` datetime NOT NULL,
  `action` varchar(25) NOT NULL,
  `user` varchar(65) NOT NULL,
  `performed_on` varchar(65) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `action` (`action`,`user`)
) ENGINE=MyISAM  CHARACTER SET utf8 COLLATE $charset;
");

$q = mysql_query("
  CREATE TABLE `" . $_POST['db_prefix'] . "templates` (
  `id` mediumint(4) NOT NULL auto_increment,
  `template` varchar(35) NOT NULL,
  `title` varchar(100) NOT NULL,
  `desc` varchar(255) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `to` varchar(125) NOT NULL,
  `from` varchar(80) NOT NULL,
  `cc` varchar(255) NOT NULL,
  `bcc` varchar(255) NOT NULL,
  `override_content` text NOT NULL,
  `format` tinyint(1) NOT NULL COMMENT '1 = html / 0 = plain text',
  `status` tinyint(1) NOT NULL,
  `save` tinyint(1) NOT NULL,
  `caller_tags` text NOT NULL,
  `custom` tinyint(1) NOT NULL COMMENT 'If this was custom created by the user.',
  `created_by` varchar(100) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `template` (`template`)
) ENGINE=MyISAM  CHARACTER SET utf8 COLLATE $charset;
");


$q = mysql_query("
  CREATE TABLE `" . $_POST['db_prefix'] . "templates_html` (
  `id` mediumint(5) NOT NULL auto_increment,
  `template` varchar(65) NOT NULL,
  `path` VARCHAR( 150 ) NOT NULL,
  `theme` VARCHAR( 25 ) NOT NULL,
  `subtemplate` varchar(65) NOT NULL,
  `title` varchar(100) NOT NULL,
  `desc` varchar(255) NOT NULL,
  `caller_tags` text NOT NULL,
  `order` smallint(3) NOT NULL,
  `custom_header` MEDIUMINT( 5 ) NOT NULL,
  `custom_footer` MEDIUMINT( 5 ) NOT NULL,
  `type` TINYINT( 1 ) NOT NULL COMMENT '1 = Header, 2 = Footer, 3 = Custom template, 0 = Template',
  PRIMARY KEY  (`id`),
  KEY `template` (`template`,`subtemplate`)
) ENGINE=MyISAM  CHARACTER SET utf8 COLLATE $charset;
");


$q = mysql_query("
  CREATE TABLE `" . $_POST['db_prefix'] . "themes` (
  `id` mediumint(4) NOT NULL auto_increment,
  `folder_name` varchar(30) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `author` varchar(60) NOT NULL,
  `author_url` varchar(150) NOT NULL,
  `selected` tinyint(1) NOT NULL,
  `type` ENUM(  'wiki',  'combo',  'cms' ) NOT NULL,
  `mobile` TINYINT( 1 ) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `selected` (`selected`),
  KEY `folder_name` (`folder_name`)
) ENGINE=MyISAM  CHARACTER SET utf8 COLLATE $charset;
");


$q = mysql_query("
  CREATE TABLE `" . $_POST['db_prefix'] . "users` (
  `id` int(8) NOT NULL auto_increment,
  `username` varchar(65) NOT NULL,
  `password` varchar(255) NOT NULL,
  `salt` varchar(255) NOT NULL,
  `email` varchar(75) NOT NULL,
  `name` varchar(75) NOT NULL,
  `options` text NOT NULL,
  `type` tinyint(1) NOT NULL,
  `ip` varchar(25) NOT NULL,
  `joined` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  `upvoted` mediumint(6) NOT NULL,
  `downvoted` mediumint(6) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `username` (`username`),
  KEY `email` (`email`)
) ENGINE=MyISAM  CHARACTER SET utf8 COLLATE $charset;
");


$q = mysql_query("
  CREATE TABLE `" . $_POST['db_prefix'] . "user_data` (
  `id` int(8) NOT NULL auto_increment,
  `user_id` int(8) NOT NULL,
  `key` varchar(50) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `key` (`key`)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE $charset;
");


/*
$q = mysql_query("
CREATE TABLE IF NOT EXISTS `" . $_POST['db_prefix'] . "user_favorites` (
  `id` int(9) NOT NULL auto_increment,
  `article` int(9) NOT NULL,
  `user_id` int(9) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `article` (`article`,`user_id`)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE $charset;
");
*/

$q = mysql_query("
  CREATE TABLE `" . $_POST['db_prefix'] . "user_notices` (
  `id` INT( 8 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  `date` DATETIME NOT NULL ,
  `type` ENUM(  'comment_post','comment_reply','badge','comment_status_change','article_edit','mention' ) NOT NULL ,
  `act_id` VARCHAR( 20 ) NOT NULL ,
  `user_id` INT( 8 ) NOT NULL,
  `viewed` TINYINT( 1 ) NOT NULL,
  INDEX (  `act_id`,`user_id`,`type` )
  ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE $charset;
") or die('Error 1: ' . mysql_error());


$q = mysql_query("
CREATE TABLE IF NOT EXISTS `" . $_POST['db_prefix'] . "user_permissions` (
  `id` int(8) NOT NULL auto_increment,
  `user_id` int(8) NOT NULL,
  `user_type` mediumint(4) NOT NULL,
  `permission` int(8) NOT NULL,
  `category` MEDIUMINT( 6 ) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user` (`user_id`),
  KEY `user_type` (`user_type`),
  KEY `permission` (`permission`),
  KEY `category` (`category`)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE $charset;
");



$q = mysql_query("
  CREATE TABLE `" . $_POST['db_prefix'] . "user_types` (
  `id` tinyint(2) NOT NULL auto_increment,
  `comments_pending_review` tinyint(1) NOT NULL,
  `name` varchar(25) NOT NULL,
  `short_form` varchar(12) NOT NULL COMMENT 'Displayed in the comments next to the username.',
  `color` varchar(6) NOT NULL COMMENT 'Highlighted color in comments.',
  `font_color` varchar(6) NOT NULL,
  `track_tasks` tinyint(1) NOT NULL,
  `is_admin` tinyint(1) NOT NULL,
  `can_alter_comments` tinyint(1) NOT NULL,
  `edit_comment_status` tinyint(1) NOT NULL,
  `can_create_articles` tinyint(1) NOT NULL,
  `new_articles_public` tinyint(1) NOT NULL,
  `can_alter_articles` tinyint(1) NOT NULL,
  `can_delete_articles` tinyint(1) NOT NULL,
  `can_ban` tinyint(1) NOT NULL,
  `can_view_private` tinyint(1) NOT NULL,
  `new_comments_approved` tinyint(1) NOT NULL,
  `cp_access` tinyint(1) NOT NULL,
  `can_alter_categories` tinyint(1) NOT NULL,
  `can_create_categories` tinyint(1) NOT NULL,
  `can_delete_categories` tinyint(1) NOT NULL,
  `can_alter_article_options` tinyint(1) NOT NULL,
  `upload_files` TINYINT( 1 ) NOT NULL,
  `upload_images` TINYINT( 1 ) NOT NULL,
  `post_code` TINYINT( 1 ) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  CHARACTER SET utf8 COLLATE $charset;
");


$q = mysql_query("
  CREATE TABLE `" . $_POST['db_prefix'] . "widgets` (
  `id` INT( 8 ) NOT NULL AUTO_INCREMENT,
  `date` DATETIME NOT NULL,
  `plugin_name` VARCHAR( 65 ) NOT NULL,
  `name` VARCHAR( 100 ) NOT NULL,
  `owner` VARCHAR( 100 ) NOT NULL,
  `type` TINYINT( 2 ) NOT NULL,
  `category` MEDIUMINT( 6 ) NOT NULL,
  `html` TEXT NOT NULL,
  `html_insert` TEXT NOT NULL,
  `filename` VARCHAR( 150 ) NOT NULL,
  `active` TINYINT( 1 ) NOT NULL,
  `options` TEXT NOT NULL COMMENT  'Serialized array.',
  `trigger` VARCHAR( 25 ) NOT NULL COMMENT  'For custom actions',
  `when_to_run` TINYINT( 1 ) NOT NULL COMMENT  '1 = after trigger, 0 = before trigger',
  PRIMARY KEY  (`id`),
  KEY `filename` (`filename`),
  KEY `type` ( `type` )
  ) ENGINE = MYISAM  CHARACTER SET utf8 COLLATE $charset;
");


$q = mysql_query("
  CREATE TABLE `" . $_POST['db_prefix'] . "widgets_todo` (
    `id` int(8) NOT NULL auto_increment,
    `list_id` int(8) NOT NULL,
    `name` varchar(255) NOT NULL,
    `complete` tinyint(1) NOT NULL,
    `date_complete` datetime NOT NULL,
    `date` datetime NOT NULL,
    PRIMARY KEY  (`id`),
    KEY `list_id` (`list_id`)
  ) ENGINE = MYISAM  CHARACTER SET utf8 COLLATE $charset;
");

?>