<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	http://www.ascadnetworks.com/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: MySQL Table Data.
	
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


// -----------------------------------------------------------
//	Build options based on what the user's
//	purpose for the program is.

$time_offset = '';
$hours = '';

// Public Site?
if ($_POST['public'] == '1') {
	$allow_reg = '1';
} else {
	$allow_reg = '0';
}

// CMS Style
// Wiki features disabled.
if ($_POST['purpose'] == 'website') {
	$select1 = '0';
	$select2 = '0';
	$select3 = '1';
	$allow_comments = '0';
	$email_comment = '0';
	$format_type = '2';
	// Some program options
	$save_history = '0';
	$direct_linking = '1';
	$allow_reg = '0';
	$display_user_bar = '0';
	$use_point_system = '0';
	// Default Page
	$def_article = '<div style="font-family:arial;">Welcome to your Banana Dance temporary homepage!\n<br />\n<br />You have chosen to use Banana Dance as a full CMS without the wiki features. This means that you will start with a blank slate and build your website from the ground up using full HTML.<br />\n<br />\n<h1>Quick Start Guide</h1>\n\nGetting started is as simple as 1-2-3!<br />\n<br />\n<ol>\n<li>Log into the administrative account that you created during the setup.</li>\n<li>Once logged in, you will notice that the <b>Manage Bar</b> will appear at the bottom of your screen. Use the manage bar to access the administrative control panel, create and edit articles, and create and edit categories.</li>\n<li>When you feel comfortable with the program, consider downloading a new <a href="http://www.bananadance.org/Download/Theme-Library">theme</a> or <a href="http://www.bananadance.org/Download/Plugin-Library">plugin</a>.</li>\n</ol>\n<br />\n<br />\n<h1>Additional Questions?</h1>\n\nFor additional questions, please visit the online <a href="http://www.bananadance.org/Product-Manual">product manual</a>. <a href="http://www.bananadance.org/Product-Manual/Intro-to-Banana-Dance">Click here</a> for a useful article on the basics of template editing and page creation.<br />\n<br />\nHappy dancing!<br />\n<br />\nPS: Friend Banana Dance on <a href="http://www.facebook.com/bananadance" target="_blank">Facebook</a> or follow me on <a href="http://twitter.com/#!/jbelelieu" target="_blank">Twitter</a> for program updates!<br />\n<br />\n<h1>Login Here to Start Creating!</h1>\n\n<div id="bd_logged_session">%user_sidebar%</div></div>';
}

// Wiki Style
// Wiki features available.
else {
	if ($_POST['purpose'] == 'starter') {
		$save_history = '0';
		$select1 = '0';
		$select2 = '1';
		$direct_linking = '1';
		$type_explain = "You have chosen to use Banana Dance as a combo CMS/wiki. This means that your theme looks like a website but that the wiki features are activated by default.";
	} else {
		$save_history = '1';
		$select1 = '1';
		$select2 = '0';
		$direct_linking = '0';
		$type_explain = "You have chosen to use Banana Dance as a wiki. This means that the wiki features are activated by default and your site looks and feels like a more traditional wiki!";
	}
	$select3 = '0';
	$allow_comments = '1';
	$email_comment = '1';
	$format_type = '1';
	// Some program options
	$display_user_bar = '1';
	$use_point_system = '1';
	// Default Page
	$def_article = 'Welcome to your Banana Dance temporary homepage!\n\n' . $type_explain . '\n\n---Quick Start Guide---\n\nGetting started is as simple as 1-2-3!\n\n  # Log into the administrative account that you created during the setup.\n  # Once logged in, the **Manage Bar** will appear at the bottom of your screen. Use the manage bar to access the administrative control panel, create and edit articles, and create and edit categories.\n  # When you feel comfortable with the program, consider downloading a new [[http://www.bananadance.org/Download/Theme-Library|theme]] or [[http://www.bananadance.org/Download/Plugin-Library|plugin]].\n\n---Additional Questions?---\n\nFor additional questions, please visit the online [[http://www.bananadance.org/Product+Manual/Common-URLs|product manual]]. [[http://www.bananadance.org/Product+Manual/Intro-to-Banana-Dance|Click here]] for a useful article on the basics of template editing and page creation.\n\nHappy dancing!\n\nPS: Friend Banana Dance on [[http://www.facebook.com/bananadance|Facebook]] or follow me on [[http://twitter.com/#!/jbelelieu|Twitter]] for program updates!';
}


$offset = ltrim($_POST['time_offset'],'+');
$time_offset = $offset * 3600;
$addtime = time()+$hours;
$date = date('Y-m-d H:i:s',$addtime);

// -----------------------------------------------------------
//	Default article

$q1 = mysql_query("
INSERT INTO `" . $_POST['db_prefix'] . "articles` (`id`, `category`, `owner`, `name`, `content`, `created`, `last_updated`, `meta_title`, `meta_desc`, `meta_keywords`, `order`, `views`, `upvoted`, `downvoted`,`allow_comments`, `allow_ratings`, `show_stats`, `login_to_view`, `public`, `display_on_sidebar`, `email_comment_posted`, `sharing_options`, `max_threading`, `login_to_comment`, `comment_hide_threshold`, `allow_comment_edits`, `format_type`) VALUES
(1, 0, 'admin', 'Welcome', '$def_article', '" . $date . "', '" . $date . "', '', '', '', 1, 0, 0, 0, 1, 0, 1, 0, 1, 0, 1, 1, 4, 1, '-3', 1, $format_type);
");

$q1A = mysql_query("
INSERT INTO `" . $_POST['db_prefix'] . "categories` (`name`,`subcat`,`order`,`allow_article_creation`,`home_article`,`template`,`public`,`base`) VALUES
('Home','0','0','1','1','0','" . $_POST['public'] . "','1')
");

// -----------------------------------------------------------
//	Field-tables

$q2 = mysql_query("
INSERT INTO `" . $_POST['db_prefix'] . "fields_sets` (`id`, `name`, `description`, `order`, `cols`) VALUES
(1, 'Registration Fields', '', 1, 2);
");

$q3 = mysql_query("
INSERT INTO `" . $_POST['db_prefix'] . "fields_sets_locations` (`id`, `set_id`, `location`, `order`, `page`) VALUES
(1, 1, 10002, 1, 0);
");


// -----------------------------------------------------------
//	Options
// 	Primary options
//	Sets, as represented below:
//	2 = Articles and Comments
//	3 = User Accounts
//	1 = General
//	4 = Performance

$domain = $_SERVER["HTTP_HOST"];
$domain = str_replace('www.','',$domain);
$check_domain = explode(":",$domain);
if (! empty($check_domain['1'])) {
	$domain = $check_domain['0'];
}

$q4 = mysql_query("
INSERT INTO `" . $_POST['db_prefix'] . "options` (`type`, `key`, `value`, `group`, `display_name`, `description`, `fixed_selections`, `field_type`, `field_width`, `left_padding`, `field_order`) VALUES
(1, 'url_display_type', 'Name', 2, 'Article URL Display', 'Controls whether to display article/category IDs or names in the web browser address bar.', 'Name|ID', 3, 0, 0, 0),
(1, 'direct_link',  '$direct_linking',  '1',  'Direct Linking',  'If set to \"Yes\", direct links will be added for categories in the primary navigation menu that have a homepage set or a single article in them.',  '',  '1',  '',  '',  '1'),
(1, 'save_article_history', '$save_history', 2, 'Save Article Changes', 'If set to \"Yes\", every change made to an article will be logged in the database.', '', 1, 0, 0, 2),
(1, 'addthis_profile_id', '', 2, 'AddThis Profile ID', 'If you have an AddThis profile, input your Profile ID to activate social bookmarking.', '', 2, 200, 0, 3),
(1, 'google_analytics_id', '', 2, 'Google Analytics ID', 'If you have a Google Analytics account, input your Web Property ID to activate statistical tracking.', '', 2, 200, 0, 4),
(1, 'editor_type', 'Plain Text', '2', 'Page Editor Type', 'What type of page editor would you like to use? Note that users can override these settings from their account.',  'Plain Text|WYSIWYG',  '3',  '300',  '',  '5'),
(1, 'use_hastags', '1', 2, 'Use Hashtagging?', 'If set to yes, the program will automatically detect hashtags in pages and ', '', 1, 0, 0, 0),

(1, 'session_length', '2', 3, 'Session Length', 'Idle time before a session expires (in hours).', '', 2, 50, 0, 0),
(1, 'required_reg_fields', 'email,username,pass', '3', 'Required Registration Fields', 'Enter the exact field names that should be required as registration, separated by commas.', '', '2', '400', '', '1'),
(1, 'required_update_fields',  'email,current_password',  '3',  'Required Account Update Fields',  'Which fields should be required for a user to update their account?',  '',  '2',  '400',  '',  '1'),
(1, 'fb_app_id',  '',  '3',  'Facebook App ID',  'Found on your facebook app settings page.',  '',  '2',  '200',  '',  '2'),
(1, 'fb_app_secret',  '',  '3',  'Facebook App Secret',  'Found on your facebook app settings page.',  '',  '2',  '200',  '',  '3'),
(1, 'one_email_max', '0', 3, 'Duplicate E-Mails', 'Controls whether you wish to allow the same e-mail address to register multiple times.', '', 1, 0, 0, 4),
(1, 'one_ip_max', '1', 3, 'Duplicate Accounts', 'Controls whether you wish to allow the same IP address to register multiple accounts.', '', 1, 0, 0, 5),
(1, 'required_pass_strength', '2', 3, 'Password Strength Require', 'Controls how strong a user''s password needs to be. Higher numbers reflect a stronger password, 5 being the strongest.', '5|4|3|2|1', 3, 50, 0, 6),
(1, 'allow_registration', '$allow_reg', 3, 'Allow Registration?', 'Controls whether visitors can register or not.', '', 1, 0, 0, 7),
(1, 'popup_registration', 'Pop Up', '3', 'Registration Form Type', 'What type of registration form should be used on your website.', 'Pop Up|Inline', '3', '100', '', '8'),
(1, 'popup_login', 'Pop Up', '3', 'Login Form Type', 'What type of login form should be used on your website.', 'Pop Up|Inline', '3', '100', '', '9'),
(1, 'display_userbar_to_all', '$display_user_bar', '3', 'Display Logged In Menu to Everyone?', 'If set to yes, all users will be shown the logged in menu, otherwise only administrators will see it.', '', '1', '', '', '10'),
(1, 'use_point_system',  '$use_point_system',  '3',  'Use Point System?',  'If set to yes, points will be granted to users for specific tasks.',  '',  '1',  '',  '',  '11'),
(1, 'max_profile_pic_size',  '524288',  '3',  'Profile Picture Maximum Size',  'In bytes, what should the maximum size of a profile picture be?',  '',  '2',  '100',  '',  '12'),
(1, 'profile_pic_max_width',  '190',  '3',  'Profile Picture Crop Width',  'What width should profile pictures be cropped to? If this is inputted and the height is left blank, all images will be cropped to this width, with the height being auto-calculated.',  '',  '2',  '50',  '',  '13'),
(1, 'profile_pic_max_height',  '',  '3',  'Profile Picture Crop Height',  'What height should profile pictures be cropped to? If this is inputted and the width is left blank, all images will be cropped to this height, with the width being auto-calculated.',  '',  '2',  '50',  '',  '14'),

(3, 'logo', '', 1, 'Logo File', 'If you have a company logo, upload it here.', '', 4, 125, 0, 0),
(1, 'date_format', 'Y/m/d', 1, 'Date Format', 'Using PHP date standards, input your desired date format. Common formats:<br />j F Y: 13 May 2011<br />F j, Y g:ia: May 13, 2011 1:25pm<br />F jS, Y: May 13th, 2011<br />Y/m/d: 2011/05/13<br />m/d/y: 05/13/11', '', 2, 125, 0, 1),
(1, 'time_offset', '" . addslashes($_POST['time_offset']) . "', 1, 'Time Offset', 'Controls by how many hours the time should be adjusted.', '+11|+10|+9|+8|+7|+6|+5|+4|+3|+2|+1|0|-1|-2|-3|-4|-5|-6|-7|-8|-9|-10|-11', 3, 100, 1, 2),
(1, 'site_name', '" . addslashes($_POST['site_name']) . "', 1, 'Site Name', 'The name of your website. Used in webpage titles for SEO-friendliness. It should be short (3-6 words) and identify what your website is.', '', 2, 250, 0, 3),
(1, 'company_name', '" . addslashes($_POST['company_name']) . "', 1, 'Company Name', 'This is mainly used on outgoing e-mails generated by the program.', '', 2, 250, 0, 4),
(1, 'default_email', '" . addslashes($_POST['site_name']) . " <noreply@$domain>', 1, 'Default From E-Mail', 'What should the from line on outgoing e-mails be?', '', 2, 250, 0, 5),
(1, 'allow_outside_connections', '1', 1, 'Allow Outside Connections?', 'Determines whether the program can communicate with outside servers, like the Banana Dance server.', '', 1, 0, 0, 7),
(1, 'curl_proxy', '', 1, 'cURL Proxy', 'Only required if your server uses a cURL proxy.', '', 2, 150, 1, 8),

(1, 'cache_category_list', '0', 4, 'Cache Category List', 'If you do not update your categories often, set this to \"Yes\" to reduce stress on the database.', '', 1, 0, 0, 2),
(1, 'cache_articles', '0', '4', 'Cache Articles', 'For improved performance on high-access websites, set this option to \"Yes\"', '', '1', '0', '0', '3'),
(1, 'cache_comments', '0', '4', 'Cache Comments', 'For improved performance on high-access websites, set this option to \"Yes\"', '', '1', '0', '0', '4'),
(1, 'permission_type', 'Basic', 4, 'Permission Type', 'Controls whether you wish to use basic permissions or advanced permissions on the control panel for administrators.', 'Basic|Advanced', 3, 0, 0, 6),
(1, 'allow_php', 'Basic', 4, 'Run PHP Code on Pages?', 'If set to yes, the program will run PHP code within your pages. Otherwise the text will be outputted in its literal form.', '0', 1, 0, 0, 5),
(1, 'minify_code',  '0',  '4',  'Minify HTML?',  'Would you like to minify the HTML outputted to the browser to increase speed?',  '',  '1',  '',  '',  '7');
");

// 	Sidebar option headings
$q5 = mysql_query("
INSERT INTO `" . $_POST['db_prefix'] . "options` (`type`, `key`, `value`, `group`, `display_name`, `description`, `fixed_selections`, `field_type`, `field_width`, `left_padding`, `field_order`) VALUES
(2, '1', 'General', 0, '', '', '', 0, 0, 0, 0),
(2, '2', 'Pages and Comments', 0, '', '', '', 0, 0, 0, 0),
(2, '3', 'User Accounts', 0, '', '', '', 0, 0, 0, 0),
(2, '4', 'Performance', 0, '', '', '', 0, 0, 0, 0);
");

// 	Static options, not updatabled from the control panel.
$q6 = mysql_query("
INSERT INTO `" . $_POST['db_prefix'] . "options` (`type`, `key`, `value`, `group`, `display_name`, `description`, `fixed_selections`, `field_type`, `field_width`, `left_padding`, `field_order`) VALUES
(3, 'version', '" . $version . "', 0, '', '', '', 0, 0, 0, 0),
(3, 'last_news_update', '" . $date . "', 0, '', '', '', 0, 0, 0, 0),
(3, 'last_updated', '" . $date . "', 0, '', '', '', 0, 0, 0, 0);
");


// -----------------------------------------------------------
//	Templates: Email

$q7 = mysql_query("
INSERT INTO `" . $_POST['db_prefix'] . "templates` (`template`, `title`, `desc`, `subject`, `to`, `from`, `cc`, `bcc`, `override_content`, `format`, `status`, `save`, `caller_tags`, `custom`, `created_by`, `created`) VALUES
('password_reset', 'Lost Password Reset', 'Sent when a user recovers a lost password.', 'Your new password at %company%', '%user%', '', '', '', '', 0, 1, 0, '', 0, '', '0000-00-00 00:00:00'),
('banned', 'User Banned', 'Sent when a user has been banned.', 'Account Banned at %company%', '%user%', '', '', '', '', 0, 1, 1, '%reason%||Reason the user was banned.', 0, '', '0000-00-00 00:00:00'),
('registration_complete', 'Registration Complete', 'Sent when a user completes registration.', 'Registration Complete at %company%', '%user%', '', '', '', '', 0, 1, 1, '', 0, '', '0000-00-00 00:00:00'),
('comment_posted', 'Comment Reply Posted', 'Sent to the owner of a comment that has received a reply.', 'Someone has replied to your comment!', '%user%', '', '', '', '', 0, 1, 0, '%article%||Page name to which the comment was posted.\r\n%article_link%||Link to the article on which the comment was posted.\r\n%status%||Whether the comment is pending or active.\r\n%comment%||The comment that was posted.\r\n%posted_by%||Username of the user who posted the comment.', 0, '', '0000-00-00 00:00:00'),
('comment_posted_to_article', 'Comment Posted (Notify Owner)', 'Notifies the owner of an article that a comment has posted.', 'Someone commented on \"%article%\"', '%user%', '', '', '', '', 0, 1, 0, '%article%||Page name to which the comment was posted.\r\n%article_link%||Link to the article on which the comment was posted.\r\n%status%||Whether the comment is pending or active.\r\n%comment%||The comment that was posted.\r\n%posted_by%||Username of the user who posted the comment.', 0, '', '0000-00-00 00:00:00'),
('account_created', 'Account Created By Admin', 'Sent when an administrator creates an account for a user.', 'Account Created at %company%', '%user%', '', '', '', '', 0, 1, 0, '', 0, '', '0000-00-00 00:00:00'),
('account_updated_admin', 'Account Updated by Admin', 'Informs a user that an administrator has edited his/her account.', 'Your account has been updated!', '%user%', '', '', '', '', 0, 1, 0, '', 0, '', '0000-00-00 00:00:00'),
('file_downloaded', 'File Downloaded', 'Sent to admin when a file has been downloaded.', 'File %filename% downloaded!', '', '', '', '', '', '0', '0', '0', '%filename%||Name of the file downloaded.\r\n%path%||Path to the file downloaded.\r\n%url%||URL to the file downloaded.', '1', 'system', '$date'),
('badge_awarded',  'Badge Awarded',  'Sent when a user receives a new badge.',  'You have earned the following badge: %badge_name%!',  '%user%',  '',  '',  '',  '',  '0',  '1',  '0',  '%badge_name%||Badge name\r\n%points_required%||Points required to get this badge.\r\n%myScore%||Points the user currently has.',  '',  '',  ''),
('email_article',  'E-Mail Article',  'Suggest article to a friend.',  '%name% has recommended an article to you!',  '%friend_email%',  '%email%',  '',  '',  '',  '0',  '1',  '0',  '%email%||Sender email.\r\n%name%||Sender name.\r\n%message%||Message to friends.\r\n%link%||Link to the article.\r\n%article_name%||Article name.',  '',  '',  ''),
('follow_comment_notice',  'Following: Comment Posted Notice',  'Sent to users following a page when a comment is posted to that page.',  '%user%',  '',  '',  '',  '',  '',  '0',  '1',  '0',  '',  '',  '',  ''),
('follow_page_notice',  'Following: Page Edited Notice',  'Sent to users following a page when that page is edited.',  'Notice: Page %article_title% update',  '%user%',  '',  '',  '',  '',  '0',  '1',  '0',  '',  '',  '',  ''),
('user_mentionned_page',  'User Notice: Mentioned in Page',  'Sent when a user has been mentioned using the @username syntax on a page.',  'You''ve been mentioned on page %article_title%!',  '%user%',  '',  '',  '',  '',  '0',  '1',  '0',  '',  '',  '',  ''),
('user_mentionned_comment',  'User Notice: Mentioned in Comment',  'Sent when a user has been mentioned using the @username syntax in a comment.',  'Someone mentioned you!',  '%user%',  '',  '',  '',  '',  '0',  '1',  '0',  '',  '',  '',  ''),
('fb_connect_account',  'Facebook Connect: Account Created',  'Sent to a user when their account has been created through Facebook Connect.',  'Account Created at %company%',  '%user%',  '',  '',  '',  '',  '0',  '1',  '1',  '',  '',  '',  '');
");


// -----------------------------------------------------------
//	Templates: HTML

$q8 = mysql_query("
INSERT INTO `" . $_POST['db_prefix'] . "templates_html` (`template`, `path`, `subtemplate`, `title`, `desc`, `caller_tags`, `order`, `custom_header`, `custom_footer`, `type`) VALUES
('article', '', '', 'Article Page', 'The primary article display page.', '%discussion%||Comments box.\r\n%total_comments%||Total comments in the discussion.\r\n%breadcrumbs%||Breadcrumb trail.\r\n%category%||Category name.\r\n%rss_link%||Link to the RSS feed for the category.\r\n%meta_title%||Meta title.\r\n%meta_desc%||Meta description.\r\n%meta_keywords%||Meta keywords\r\n%created%||Date created.\r\n%last_updated%||Date of last update.\r\n%views%||Total views.\r\n%formatted_article%||The fully formatted article content.\r\n%article_sublinks%||Inner-page links.\r\n%article_options%||Logged in options.\r\n%article_stats%||Articles stats, including views, creation date, last update date, and creator username.\r\n%article_id%||Article ID.\r\n%article_name%||Article Name.\r\n%article_category%||Article Category ID.\r\n%creator%||Username of creator.\r\n%sharing_code%||AddThis social bookmarking code.\r\n%user_sidebar%||User sidebar widget that displays login and registration forms if not logged in or user details if logged in.\r\n%allow_registration%||A required javascript flag which determines if registration is enabled.', 4, '', '', 0),
('article_print', '',  'article',  'Print Article',  'Printer friendly version of an article.',  '',  '6',  '',  '',  '0'),
('article_print_all', '',  'article',  'Print Category',  'Prints an entire category of articles.',  '',  '6',  '',  '',  ''),
('email_article', '',  'article',  'E-Mail Article',  'Suggest an article to a friend.',  '',  '6',  '',  '',  ''),
('discussion', '', 'article', 'Discussion Box', 'Discussion box containing comments for an article.', '%discussion%||All comments.\r\n%article_id%||Article ID.\r\n%disabled%||Whether or not the comment box is disabled. Generated by the program.\r\n%box_text%||Text displayed within the comment box. Generated by the program.\r\n%total_comments%||Total comments in the discussion.', 5, '', '', 0),
('discussion_closed', '', 'article', 'Discussion Closed', 'When commenting is turned off for an article this appears.', '', 6, '', '', 0),
('comment_entry_forum', '', 'article', 'Forum-Style Comment Entry', 'Renders a comment and all of its subcomments for forum-style commenting system.', '%comment%||Formatted comment.\r\n%comment_id%||Comment ID.\r\n%comment_score%||Comment score.\r\n%comment_date%||Date comment was posted.\r\n%comment_username%||Username of user who posted the comment.\r\n%comment_options%||Comment option links.\r\n%subcomments%||Comment subcomments.\r\n%voted_up%||Class if user has voted up this comment.\r\n%voted_down%||Class if user has voted down this comment.\r\n%upvote_link%||Link to upvote.\r\n%downvote_link%||Link to downvote.\r\n%add_class%||Adds required classes to bd_comment\r\n%comment_replies%||Total replies to this comment.', '7', '0', '0', '0'),
('comment_entry_tree', '', 'article', 'Tree-Style Comment Entry', 'Renders a comment and all of its subcomments for tree-style commenting system.', '%comment%||Formatted comment.\r\n%comment_id%||Comment ID.\r\n%comment_score%||Comment score.\r\n%comment_date%||Date comment was posted.\r\n%comment_username%||Username of user who posted the comment.\r\n%comment_options%||Comment option links.\r\n%subcomments%||Comment subcomments.\r\n%voted_up%||Class if user has voted up this comment.\r\n%voted_down%||Class if user has voted down this comment.\r\n%upvote_link%||Link to upvote.\r\n%downvote_link%||Link to downvote.\r\n%add_class%||Adds required classes to bd_comment\r\n%comment_replies%||Total replies to this comment.', '8', '0', '0', '0'),
('header', '', '', 'Header', 'The universal header for the website.', '%user_sidebar%||User sidebar widget that displays login and registration forms if not logged in or user details if logged in.\r\n%category_tree%||Sidebar widget with categories and articles.\r\n%allow_registration%||A required javascript flag which determines if registration is enabled.\r\n%article_id%||Article ID.\r\n%article_category%||Article Category ID.', 1, '', '', 0),
('footer', '', '', 'Footer', 'The universal footer for the website.', '', 2, '', '', 0),
('error', '', '', 'Error Page', 'General error page for the program.', '%error%||Error message generated by the program.', 3, '', '', 0),
('logged_in_sidebar', '', 'article', 'Sidebar: Logged In', 'Replaces the %user_sidebar% caller tag if a user is logged in.', '%username%||Username of the logged in user.', 7, '', '', 0),
('logged_out_sidebar', '', 'article', 'Sidebar: Logged Out', 'Replaces the %user_sidebar% caller tag if a user is not logged in.', '', 8, '', '', 0),
('login_sidebar', '', 'logged_out_sidebar', 'Sidebar: Login Form', 'Login form on the sidebar.', '', 9, '', '', 0),
('register_sidebar', '', 'logged_out_sidebar', 'Sidebar: Registration Form', 'Registration form on the sidebar. All fields added ', '', 10, '', '', 0),
('download_entry', '',  'article',  'Downloadable File',  'Displayed for downloadable files.',  '%name%||Name of the file.\r\n%size%||Size of the file.\r\n%path%||Full link to download the file.\r\n%ext%||Extension.\r\n%downloads%||Total downloads.',  '7',  '',  '',  '0'),
('article_inline_add', '',  'article',  'Inline Add',  'Basic inline addition of an article.',  '%article_name%\r\n%article_content%\r\n%formatting_guide%\r\n%options_menu%\r\n%meta_menu%',  '11', '', '', 0),
('article_inline_edit', '',  'article',  'Inline Edit',  'Basic inline editing of an article.',  '%article_name%\r\n%article_content%\r\n%formatting_guide%\r\n%options_menu%\r\n%meta_menu%',  '11', '', '', 0),
('search', '', '', 'Search Results', 'Page display all search results.', '%total_results%||Total results.\r\n%search_results%||List of results. Replaced by content of the \"Search Results: Nothing Found\" or \"Search Results: Entry\" templates.', 12, '', '', 0),
('search_no_results', '', 'search', 'Search Results: Nothing Found', 'No search results were returned.', '', 14, '', '', 0),
('search_result', '', 'search', 'Search Result: Entry', 'Each search result will be rendered through this template.', '%article_link%||Link to the article.\r\n%article_name%||Article name.\r\n%article_breadcrumbs%||Breadcrumb trail.\r\n%article_snippet%||Brief snippet taken from the article''s content.', 13, '', '', 0),

('css_style', '', '', 'CSS: Website', 'Style sheets for this theme.', '', 0, '', '', 0),
('css_article', '', '', 'CSS: Wiki-syntax', 'Style sheet for wiki-syntax formatting.', '', 0, 0, 0, 0),
('css_definitions', '', '', 'CSS: Basic Definitions', 'Fonts, links, and some basics.', '', 0, 0, 0, 0),
('css_theme_style', '', '', 'CSS: Custom Theme Styles', 'Custom style sheet for this theme.', '', 0, '', '', 0),
('css_print', '', '', 'CSS: Print', 'Printer-friendly screens.', '', 0, 0, 0, 0),

('lost_password_sidebar', '', 'logged_out_sidebar', 'Panel: Lost Password Recovery', 'Displays the lost password recovery form.', '', 11, '', '', 0),
('article_panel', '',  '',  'Article Panel',  'A small description panel for an article.',  '',  '20',  '',  '',  ''),
('comment_panel', '',  '',  'Comment Panel',  'A small description panel for a comment.',  '',  '21',  '',  '',  ''),

('user_manage_home', '', '', 'Users: Manage Home', 'User management homepage.', '%total_comments%\r\n%total_articles%\r\n%total_logins%', 15, 0, 0, 0),
('user_manage_edit', '', 'user_manage_home', 'Users: Edit Account', 'User management edit account', '', 16, 0, 0, 0),
('user_manage_picture', '',  'user_manage_home',  'Users: Profile Picture',  'User management profile picture upload.',  '',  '18',  '',  '',  ''),
('user_manage_comments', '', 'user_manage_home', 'Users: Comments', 'List of comment this user has posted.', '', 17, 0, 0, 0),
('user_manage_articles', '', 'user_manage_home', 'Users: Pages', 'List of pages this user has created.', '', 18, 0, 0, 0),
('user_manage_favorites', '',  'user_manage_home',  'Users: Favorites',  'List of favorite articles for a user.',  '',  '19',  '',  '',  '0'),
('user_manage_notices', '',  'user_manage_home',  'Users: Notices',  'Page listing user notices.',  '%total_notices%||Total unread notices.\r\n%notice_list%||List of unread notices.\r\n%total_notices_old%||Total read notices\r\n%notice_list_old%||List of read notices.',  '19',  '',  '',  '0'),
('user_notice', '',  'user_manage_notices',  'Users: Notice Entry',  'A notice entry for comments to articles, replies, badges, and more.',  '%notice_type%||Type of notice.\r\n%notice_link%||Link to relevant content, if any.\r\n%notice_date%||Date action took place.\r\n%notice_details%||Information on the notice.',  '19',  '',  '',  '0'),
('user_public_profile', '',  '',  'Users: Public Profile',  'A public page with information on a member.',  '',  '22',  '',  '',  ''),
('user_panel', '',  'user_manage_home',  'Users: User Panel',  'A small panel used to display a basic overview of an account.',  '',  '19',  '',  '',  '0');
");


// -----------------------------------------------------------
//	Themes

$q9 = mysql_query("
INSERT INTO `" . $_POST['db_prefix'] . "themes` (`id`, `folder_name`, `name`, `description`, `author`, `author_url`, `selected`, `type`) VALUES
(1, 'subtle_touch', 'Subtle Touch', 'The default wiki theme for Banana Dance.', 'Jon Belelieu', 'http://www.bananadance.org/', $select1, 'wiki'),
(2, 'site_starter', 'Starter Site', 'A good starter theme for a basic website layout.', 'Jon Belelieu', 'http://www.bananadance.org/', $select2, 'combo'),
(3, 'blank_slate', 'Blank Slate', 'For advanced web designers, this theme comes with the bare minimum styling elements. Good for creating a website from the ground up.', 'Jon Belelieu', 'http://www.bananadance.org/', $select3, 'cms');
");


// -----------------------------------------------------------
// 	Item Options

$q6A = mysql_query("
INSERT INTO `" . $_POST['db_prefix'] . "item_options` (`id`, `key`, `value`, `act_id`, `type`) VALUES
(1, 'category_default', '0', 'd', 'article'),
(2, 'public', '1', 'd', 'article'),
(3, 'allow_comments', '$allow_comments', 'd', 'article'),
(4, 'show_stats', '0', 'd', 'article'),
(5, 'login_to_view', '0', 'd', 'article'),
(6, 'display_on_sidebar', '1', 'd', 'article'),
(7, 'email_comment_posted', '$email_comment', 'd', 'article'),
(8, 'sharing_options', '0', 'd', 'article'),
(9, 'login_to_comment', '1', 'd', 'article'),
(10, 'allow_comment_edits', '1', 'd', 'article'),
(11, 'max_threading', '8', 'd', 'article'),
(12, 'comment_hide_threshold', '-5', 'd', 'article'),
(14, 'default_comment_type_show', '0', 'd', 'article'),
(15, 'format_type', '$format_type', 'd', 'article'),
(16, 'template', '0', 'd', 'article'),
(17, 'comment_thread_style', 'Tree', 'd', 'article'),
(18, 'meta_desc', '" . addslashes($_POST['meta_desc']) . "', '', 'article'),
(19, 'meta_keywords', '" . addslashes($_POST['meta_keywords']) . "', '', 'article'),
(20, 'meta_title', '', '', 'article');
");


// -----------------------------------------------------------
//	Widgets

$q10 = mysql_query("
INSERT INTO `" . $_POST['db_prefix'] . "widgets` (`id`, `date`, `plugin_name`, `name`, `owner`, `type`, `category`, `html`, `html_insert`, `filename`, `active`, `options`, `trigger`, `when_to_run`) VALUES
(1, '" . $date . "', 'ascadnet', 'Sample Plugin', 'admin', 5, 0, '', '', 'ascadnet', 0, 'a:4:{s:4:\"desc\";s:31:\"Sample plugin for Banana Dance.\";s:7:\"version\";s:4:\"v1.0\";s:9:\"developer\";s:14:\"Ascad Networks\";s:13:\"developer_url\";s:32:\"http://www.bananadance.org/\";}', '', 0),
(2, '" . $date . "', 'horizontal_nav', 'Horizontal Navigation', 'admin', 5, 0, '', '', 'horizontal_nav', 1, 'a:4:{s:4:\"desc\";s:76:\"Creates a horizontal navigation bar using your base categories and articles.\";s:7:\"version\";s:4:\"v1.0\";s:9:\"developer\";s:14:\"Ascad Networks\";s:13:\"developer_url\";s:32:\"http://www.bananadance.org/\";}', '', 0);
");


// -----------------------------------------------------------
//	Templates: Users and User types

$q11 = mysql_query("
INSERT INTO `" . $_POST['db_prefix'] . "users` (`id`, `username`, `password`, `salt`, `email`, `name`, `options`, `type`, `ip`, `joined`, `last_updated`, `upvoted`, `downvoted`) VALUES
(1, '" . addslashes($_POST['username']) . "', '" . $encode_pass . "', '" . $salt . "', '" . addslashes($_POST['email']) . "', 'Master Administrator', '', 1, '" . $_SERVER['REMOTE_ADDR'] . "', '" . $date . "', '" . $date . "', 0, 0);
");

$q11 = mysql_query("
INSERT INTO `" . $_POST['db_prefix'] . "user_types` (`comments_pending_review`, `name`, `short_form`, `color`, `font_color`, `track_tasks`, `is_admin`, `can_alter_comments`, `edit_comment_status`, `can_create_articles`, `new_articles_public`, `can_alter_articles`, `can_delete_articles`, `can_ban`, `can_view_private`, `new_comments_approved`, `cp_access`, `can_alter_categories`, `can_create_categories`, `can_delete_categories`,`can_alter_article_options`,`upload_files`,`upload_images`,`post_code`) VALUES
(0, 'Administrator', 'admin', 'FF5252', 'FFFFFF', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(0, 'Moderator', 'mod', 'FFE190', '', 1, 0, 1, 0, 0, 0, 0, 0, 1, 1, 1, 1, 0, 0, 0, 1, 1, 1, 1),
(0, 'Standard User', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0);
");

?>