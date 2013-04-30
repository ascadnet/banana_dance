<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: User profile and management features.
	
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


if (PERFORMANCE_TESTS == '1') {
	$start = microtime(true);
}

// ----------------------------------------
// 	Username or User ID submitted?

if (! intval($_GET['id'])) {
	// Get ID from username
	$user_id = $session->get_user_id($_GET['id']);
	$username = $_GET['id'];
} else {
	$user_id = $_GET['id'];
	$username = $session->get_username_from_id($_GET['id']);
}

if (empty($user_id)) {
	$text = lg_user_does_not_exist;	
	$db->show_error($text);
	exit;
}

// ----------------------------------------
// 	Some additional template considerations

$cache_category_list = $db->get_option('cache_category_list');
$category_tree = $manual->category_tree('0','',$cache_category_list);

if (! empty($user)) {
	$logged_in = '1';
	$user_sidebar = $template->render_template('logged_in_sidebar',$user,'','1');
} else {
	$logged_in = '0';
	$user_sidebar = $template->render_template('logged_out_sidebar','','','1');
}

// ----------------------------------------
// Need extended data...

$user_get_data = $session->get_user_data('',$user_id,'1');
$user_panel = $template->render_template('user_panel',$username,'','1','1',$user_get_data);

// ----------------------------------------
//	Breadcrumbs

$url_display_type = $db->get_option('url_display_type');
if ($url_display_type == 'Name') {
	$user_link = URL . "/user/$username";
} else {
	$user_link = URL . "/index.php?v=user&id=$username";
}
$add_crumbs = array(
	"<a href=\"$user_link\">$username</a>",
);

// ----------------------------------------
// 	User's profile management
//	$privileges already loaded

$special_changes = array(
	'%category_tree%' => $category_tree,
	'%user_sidebar%' => $user_sidebar,
	'%user_panel%' => $user_panel,
	'%total_favorites%' => $user_get_data['favorites']['total'],
);


// ----------------------------------------
// 	Privacy Concerns

$menu_items = "<ul class=\"bd_headers\">";
// This is the logged in user's profile
if ($user == $username) {
	$show_profile = '1';
	// User-only items
	if ($url_display_type == 'Name') {
		//$menu_items .= "<li><a href=\"$user_link/public\">" . lg_profile_public . "</a></li>";
		$menu_items .= "<li><a href=\"$user_link/edit\">" . lg_profile_edit . "</a></li>";
		$menu_items .= "<li><a href=\"$user_link/notices\">" . lg_notices . " (" . $user_get_data['notices']['total'] . ")</a></li>";
		$menu_items .= "<li><a href=\"$user_link/profile_pic\">" . lg_profile_pic . "</a></li>";
	} else {
		//$menu_items .= "<li><a href=\"$user_link&public=1\">" . lg_profile_public . "</a></li>";
		$menu_items .= "<li><a href=\"$user_link&p=edit\">" . lg_profile_edit . "</a></li>";
		$menu_items .= "<li><a href=\"$user_link&p=notices\">" . lg_notices . " (" . $user_get_data['notices']['total'] . ")</a></li>";
		$menu_items .= "<li><a href=\"$user_link&p=profile_pic\">" . lg_profile_pic . "</a></li>";
	}
}
// Optional privacy setting
else if ($user_get_data['privacy_hide_profile'] == '1') {
	$show_profile = '0';
}
// Show profile to logged in users
else if ($user_get_data['privacy_hide_profile'] == '2' && $logged_in != '1') {
	$show_profile = '0';
}
// Always show profile
else {
	$show_profile = '1';
}
// Complete menu options
if ($url_display_type == 'Name') {
	$menu_items .= "<li><a href=\"$user_link/comments\">" . lg_profile_comments . "</a></li>";
	$menu_items .= "<li><a href=\"$user_link/articles\">" . lg_profile_pages . "</a></li>";
	$menu_items .= "<li><a href=\"$user_link/favorites\">" . lg_profile_favorites . "</a></li>";
} else {
	$menu_items .= "<li><a href=\"$user_link&p=comments\">" . lg_profile_comments . "</a></li>";
	$menu_items .= "<li><a href=\"$user_link&p=articles\">" . lg_profile_pages . "</a></li>";
	$menu_items .= "<li><a href=\"$user_link&p=favorites\">" . lg_profile_favorites . "</a></li>";
}
$menu_items .= "</ul>";
$special_changes['%user_menu%'] = $menu_items;

// -------------------------
//	Public page or home page

if (empty($_GET['p']) && $show_profile == '1') {
	$feed_options = array(
		'category' => '0',
		'limit' => '50',
		'newpages' => '1',
		'editpages' => '1',
		'badges' => '1',
		'comment' => '1',
		'mentions' => '1',
		'profilepost' => '1',
		'page' => '1',
	);
	$special_changes['%feed%'] = $manual->generate_feed($feed_options,$user_id);
	$special_changes['%recent_comments%'] = $user_get_data['recent_comments'];
	$special_changes['%recent_articles%'] = $user_get_data['recent_articles'];
	$special_changes['%my_thumbnail%'] = $user_data['profile_thumbnail'];
	$special_changes['%meta_title%'] = NAME . ' / ' . $username . ' / ' . lg_profile;
	
	$add_crumbs[] = "<a href=\"$user_link\">" . lg_profile . "</a>";
	$breadcrumbs = $manual->breadcrumbs('0','','','',$add_crumbs);
	$special_changes['%breadcrumbs%'] = $breadcrumbs;	
	// Render Page
	$temp = "user_public_profile";
	$render_page = $template->render_template($temp,$username,$special_changes,'0','0',$user_get_data);
	// Remove post?
	if (empty($user)) {
		$render_page = str_replace('<!--start:post-->','<!--start:post',$render_page);
		$render_page = str_replace('<!--end:post-->','end:post-->',$render_page);
	}
}

// -------------------------
//	Only available to user
//	on own profile

else if ($_GET['p'] == 'notices' && $user == $username) {
	$mark_read = $session->mark_notices_read($user_get_data['id']);
	$special_changes['%notice_list%'] = $user_get_data['notices']['items'];
	$special_changes['%total_notices%'] = $user_get_data['notices']['total'];
	$special_changes['%notice_list_old%'] = $user_get_data['notices_old']['items'];
	$special_changes['%total_notices_old%'] = $user_get_data['notices_old']['total'];
	$special_changes['%meta_title%'] = NAME . ' / ' . $username . ' / ' . lg_notices;
	
	$add_crumbs[] = "<a href=\"$user_link/notices\">" . lg_notices . "</a>";
	$breadcrumbs = $manual->breadcrumbs('0','','','',$add_crumbs);
	
	$special_changes['%breadcrumbs%'] = $breadcrumbs;
	// Render Page
	$render_page = $template->render_template('user_manage_notices',$user,$special_changes,'0','0',$user_get_data);
}

else if ($_GET['p'] == 'profile_pic' && $user == $username) {
	if (! empty($user_get_data['picture'])) {
		$special_changes['%remove_link%'] = "<a href=\"#\" onclick=\"removeProfilePic();return false;\">Remove</a>";
	} else {
		$special_changes['%remove_link%'] = "";
	}
	
	$add_crumbs[] = "<a href=\"$user_link/profile_pic\">" . lg_profile_pic . "</a>";
	$breadcrumbs = $manual->breadcrumbs('0','','','',$add_crumbs);
	
	$special_changes['%meta_title%'] = NAME . ' / ' . $username . ' / ' . lg_profile_pic;
	$special_changes['%breadcrumbs%'] = $breadcrumbs;
	// Render Page
	$render_page = $template->render_template('user_manage_picture',$user,$special_changes,'0','0',$user_get_data);
}

else if ($_GET['p'] == 'edit' && $user == $username) {

	// Options and privacy
	$hide_profile = "<p><input type=\"radio\" name=\"privacy_hide_profile\" value=\"0\"";
	if ($user_get_data['privacy_hide_profile'] == '0' || empty($user_get_data['privacy_hide_profile']) || $user_get_data['privacy_hide_profile'] == 'n/a') {
		$hide_profile .= " checked=\"checked\"";
	}
	$hide_profile .= " />" . lg_visible . "<br />";
	$hide_profile .= "<input type=\"radio\" name=\"privacy_hide_profile\" value=\"2\"";
	if ($user_get_data['privacy_hide_profile'] == '2') {
		$hide_profile .= " checked=\"checked\"";
	}
	$hide_profile .= " />" . lg_visible_to_users . "<br />";
	$hide_profile .= "<input type=\"radio\" name=\"privacy_hide_profile\" value=\"1\"";
	if ($user_get_data['privacy_hide_profile'] == '1') {
		$hide_profile .= " checked=\"checked\"";
	}
	$hide_profile .= " />" . lg_not_visible . "</p>";
	
	// Editor
	if (empty($user_get_data['option_editor'])) {
		$user_get_data['option_editor'] = $db->get_option('editor_type');
	}
	$editor = "<p><input type=\"radio\" name=\"option_editor\" value=\"Plain Text\"";
	if ($user_get_data['option_editor'] != 'WYSIWYG') {
		$editor .= " checked=\"checked\"";
	}
	$editor .= " /> " . lg_editor_standard . "<br />";
	$editor .= " <input type=\"radio\" name=\"option_editor\" value=\"WYSIWYG\"";
	if ($user_get_data['option_editor'] == 'WYSIWYG') {
		$editor .= " checked=\"checked\"";
	}
	$editor .= " /> " . lg_editor_wysiwyg . "</p>";
	
	// E-Mail Format
	$email_format = "<p><input type=\"radio\" name=\"option_email_format\" value=\"text\"";
	if ($user_get_data['option_email_format'] == 'text') {
		$email_format .= " checked=\"checked\"";
	}
	$email_format .= " /> " . lg_email_format_text. "<br />";
	$email_format .= " <input type=\"radio\" name=\"option_email_format\" value=\"html\"";
	if ($user_get_data['option_email_format'] != 'text' || empty($user_get_data['option_email_format'])) {
		$email_format .= " checked=\"checked\"";
	}
	$email_format .= " /> " . lg_email_format_html . "</p>";

	// Render Page
	$special_changes['%meta_title%'] = NAME . ' / ' . $username . ' / ' . lg_profile_edit;
	$special_changes['%field_hide_profile%'] = $hide_profile;
	$special_changes['%field_default_editor%'] = $editor;
	$special_changes['%field_email_format%'] = $email_format;
	
	$add_crumbs[] = "<a href=\"$user_link/edit\">" . lg_profile_edit . "</a>";
	$breadcrumbs = $manual->breadcrumbs('0','','','',$add_crumbs);
	
	$special_changes['%breadcrumbs%'] = $breadcrumbs;
	$render_page = $template->render_template('user_manage_edit',$user,$special_changes,'0','0',$user_get_data);
}

// -------------------------

else if ($_GET['p'] == 'comments' && $show_profile == '1') {

	$feed_options = array(
		'category' => '0',
		'limit' => '50',
		'newpages' => '0',
		'editpages' => '0',
		'badges' => '0',
		'comment' => '1',
		'profilepost' => '0',
		'mentions' => '0',
		'page' => '0',
	);
	$special_changes['%feed%'] = $manual->generate_feed($feed_options,$user_id);

	$where = " `user`='$username' AND `pending`!='1'";
	$user_comments = $manual->recent_comments($where,'100',$username);
	$special_changes['%put_comments%'] = $user_comments;
	$special_changes['%meta_title%'] = NAME . ' / ' . $username . ' / ' . lg_profile_comments;
	
	$add_crumbs[] = "<a href=\"$user_link/comments\">" . lg_profile_comments . "</a>";
	$breadcrumbs = $manual->breadcrumbs('0','','','',$add_crumbs);
	
	$special_changes['%breadcrumbs%'] = $breadcrumbs;
	// Render Page
	$render_page = $template->render_template('user_manage_comments',$username,$special_changes,'0','0',$user_get_data);
}
else if ($_GET['p'] == 'articles' && $show_profile == '1') {

	$feed_options = array(
		'category' => '0',
		'limit' => '50',
		'newpages' => '1',
		'editpages' => '1',
		'badges' => '0',
		'comment' => '0',
		'mentions' => '0',
		'profilepost' => '0',
		'mentions' => '0',
		'page' => '0',
	);
	$special_changes['%feed%'] = $manual->generate_feed($feed_options,$user_id);

	$where = " `owner`='$username'";
	$user_articles = $manual->recent_articles($where,'100',$username);
	$special_changes['%put_articles%'] = $user_articles;
	$special_changes['%meta_title%'] = NAME . ' / ' . $username . ' / ' . lg_profile_pages;
	
	$add_crumbs[] = "<a href=\"$user_link/articles\">" . lg_profile_pages . "</a>";
	$breadcrumbs = $manual->breadcrumbs('0','','','',$add_crumbs);
	
	$special_changes['%breadcrumbs%'] = $breadcrumbs;
	// Render Page
	$render_page = $template->render_template('user_manage_articles',$username,$special_changes,'0','0',$user_get_data);
}
else if ($_GET['p'] == 'favorites' && $show_profile == '1') {
	$special_changes['%favorites_list%'] = $user_get_data['favorites']['items'];
	$special_changes['%meta_title%'] = NAME . ' / ' . $username . ' / ' . lg_profile_favorites;
	
	$add_crumbs[] = "<a href=\"$user_link/favorites\">" . lg_profile_favorites . "</a>";
	$breadcrumbs = $manual->breadcrumbs('0','','','',$add_crumbs);
	
	$special_changes['%breadcrumbs%'] = $breadcrumbs;
	// Render Page
	$render_page = $template->render_template('user_manage_favorites',$user,$special_changes,'0','0',$user_get_data);
}
else {
	$offlimits = str_replace('%username%',$username,lg_off_limits);
	$db->show_error($offlimits);
	exit;
}


// ----------------------------------------
//  Display the final rendered page

if (PERFORMANCE_TESTS == '1') {
	$end = microtime(true);
	$dif = $end - $start;
	echo "<div class=\"bd_system\"><b>Performance Testing: $dif</b></div>";
}

echo $render_page;
exit;

?>