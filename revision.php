<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: All pages are rendered through this page.
	
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


if (file_exists('config.php')) {
	require "config.php";
} else {
	header('Location: setup/index.php');
	exit;
}


if (PERFORMANCE_TESTS == '1') {
	$start = microtime(true);
}


// ----------------------------------------
//	Private Site?

$public_site = $manual->check_public();
if ($public_site == '0' && empty($user)) {
   		$text = lg_private_site;
   		$db->show_error($text);
   		exit;
}


// ----------------------------------------
// 	Revision Details

$revision = $manual->get_revision($_GET['id']);


// ----------------------------------------
// 	Article Basics

$article = $manual->get_article($revision['article_id']);

$article_crumbs = $manual->breadcrumbs($revision['category'],$revision['article_id'],'0','0',$delimiter,$article,'0');
$breadcrumbs = "<a href=\"" . URL . "\">" . NAME . "</a>" . $article_crumbs;
$stats = $manual->increase_views($article['id']);


// ----------------------------------------
// 	Access granted?
//	Can this user view this article?

if (($category['public'] == '1' && $article['public'] == "1") || $article['owner'] == $user || $privileges['can_view_private'] == "1") {
	$show_article = "1";
	$article_text = "";
	$hasAccess = '1';
} else {
	// Check category privacy
	if ($category['public'] == '3') {
   		$text = lg_category_maintenance;
   		$db->show_error($text);
   		exit;
	}
	else if ($category['public'] == '0') {
		if ($privileges['is_admin'] == '1' || $privileges['can_view_private'] == "1") {
			$show_article = "1";
			$article_text = "";
		} else {
			$show_article = "0";
			$text = lg_category_private;	
			$db->show_error($text);
			exit;
		}
	}
	else if ($category['public'] != '1' && ! empty($category['public'])) {
		$hasAccess = $manual->user_permissions($article['id'],$user_data['id'],$user_data['type'],'0',$article['category']);
		if ($hasAccess == "1") {
			$show_article = "1";
			$article_text = "";
		} else {
			$show_article = "0";
			$text = lg_category_private;	
			$db->show_error($text);
			exit;
		}
	}
	else {
		$show_article = "1";
		$article_text = "";
	}
	// Check page privacy
	if ($article['public'] == '3') {
   		$text = lg_article_maintenance;
   		$db->show_error($text);
   		exit;
	}
	else if ($article['public'] == '0') {
		if ($privileges['is_admin'] == '1' || $article['owner'] == $user || $privileges['can_view_private'] == "1") {
			$show_article = "1";
			$article_text = "";
		} else {
			$show_article = "0";
			$text = lg_article_private;	
			$db->show_error($text);
			exit;
		}
	}
	else if ($article['public'] != '1' && ! empty($article['public'])) {
		$hasAccess = $manual->user_permissions($article['id'],$user_data['id'],$user_data['type']);
		if ($hasAccess == "1") {
			$show_article = "1";
			$article_text = "";
		} else {
			$show_article = "0";
			$text = lg_article_private;	
			$db->show_error($text);
			exit;
		}
	}
	else {
		$show_article = "1";
		$article_text = "";
	}
}

// ----------------------------------------
// 	Define some globals we need for
//	generating headers.

define('BD_ARTICLE_VIEWING',$article['id']);
define('BD_CATEGORY_VIEWING',$article['category']);
define('BD_ARTICLE_COMMENT_TYPE',$article['default_comment_type_show']);


// Last viewed article
if (! empty($user) && $article['id'] != '1') {
	// Create the location memory
	$array_loc = array(
		'last_page_viewed' => $article['id']
	);
	$update_opts = $session->update_user_options($array_loc);
}


// ----------------------------------------
// 	Get the required components
//	to render the page
	
// User sidebar
if (! empty($user)) {
	$disabled = "";
	$box_text = "";
	$user_sidebar = $template->render_template('logged_in_sidebar',$user,'','1');
} else {
	$disabled = " disabled=\"disabled\"";
	$box_text = "Login to comment.";
	$user_sidebar = $template->render_template('logged_out_sidebar','','','1');
}

// Category Tree
$cache_category_list = $db->get_option('cache_category_list');
$category_tree = $manual->category_tree($revision['category'],'',$cache_category_list);
$this_cate_tree = $manual->category_tree($revision['category'],'0','0','1','0',' AND `display_on_sidebar`="1" ORDER BY `order` ASC','1');
$category_link = $manual->prepare_link('',$revision['category']);
	
// Discussion box
if ($article['allow_comments'] == "1" && $show_article == "1") {
	// Total comments
	$q1 = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "comments` WHERE `article`='" . $article['id'] . "' AND `pending`!='1'";
	$comments = $manual->get_array($q1);
	// Render Comments
	$render_comments = $manual->get_comments($article['id'],$user,$article);
	$special_changes = array(
		'%discussion%' => $render_comments,
		'%total_comments%' => $comments['0'],
		'%disabled%' => $disabled,
		'%box_text%' => $box_text,
		'%article_id%' => $article['id']
	);
	$render_discussion = $template->render_template('discussion',$user,$special_changes,'1');
} else {
	$render_discussion = $template->render_template('discussion_closed',$user,'','1');
}


// Management Bar
$manage_bar = $manual->article_sidebar($article,'1','');

// Format the article
if (! empty($_GET['h'])) {
	$highlight = $_GET['h'];
}

// Login required?
if ($article['login_to_view'] == '1') {
	if (! empty($user)) {
		$proceed_art = '1';
	} else {
		$format_article = array();
		$format_article['article'] = "<p class=\"bd_small\"><a href=\"#\" onClick=\"showLogin();return false;\">Login</a> or <a href=\"#\" onClick=\"showRegister();return false;\">register</a> to view this article.</p>";
		$format_article['sublinks'] = "";
		$format_article['options'] = "";
		$format_article['stats'] = "";
	}
} else {
	$proceed_art = '1';
}


// Proceed to format article?
if ($proceed_art == '1') {
	$format_article = $manual->format_article($article,$revision['content'],$user,$highlight,'0','0',$user_data);
}

// Cache code
if ($cache_category_list == '1') {
	$cache_code = "$(window).load(function () { closeCategory('0');expandCategory('" . $article['category'] . "'); });";
}

// Meta Deta
if (empty($article['meta_title'])) {
	$page_title = $manual->get_page_title($article_crumbs,$delimiter);
	$article['meta_title'] = $page_title;
}
if (empty($article['meta_desc'])) {
	$cate_meta_desc = $db->get_an_item_option('meta_desc',$article['category'],'category');
	if (empty($cate_meta_desc)) {
		$article['meta_desc'] = $db->get_an_item_option('meta_desc','d');
	} else {
		$article['meta_desc'] = $cate_meta_desc;
	}
}
if (empty($article['meta_keywords'])) {
	$cate_meta_keys = $db->get_an_item_option('meta_desc',$article['category'],'category');
	if (empty($cate_meta_keys)) {
		$article['meta_keywords'] = $db->get_an_item_option('meta_keywords','d');
	} else {
		$article['meta_keywords'] = $cate_meta_keys;
	}
}

$article['meta_title'] = $article['meta_title'];

// Callers on title and meta
$format_created = $db->format_date($article['created']);
$article['name'] = str_replace('%created%',$format_created,$article['name']);
$article['name'] = str_replace('%last_updated%',$db->format_date($article['last_updated']),$article['name']);
$article['meta_title'] = str_replace('%name%',$article['name'],$article['meta_title']);
$article['meta_title'] = str_replace('%created%',$format_created,$article['meta_title']);

// Favorite?
$links = article_links($article['id'],$revision['category'],$article['favorited'],$article['following']);

// RSS Link
$rss_link = URL . '/rss/category.php?id=' . $revision['category'];

// Prepare the final template
$special_changes = array(
	'%discussion%' => $render_discussion,
	'%total_comments%' => $comments['0'],
	'%breadcrumbs%' => $breadcrumbs,
	'%category_tree%' => $category_tree,
	'%category_sublinks%' => $this_cate_tree,
	'%links%' => $links,
	'%category%' => $_GET['category'],
	'%created%' => $db->format_date($article['created']),
	'%last_updated%' => $db->format_date($article['last_updated']),
	'%views%' => $article['views'],
	'%formatted_article%' => $format_article['article'],
	'%article_sublinks%' => $format_article['sublinks'],
	'%manage_bar%' => $manage_bar,
	'%favorites%' => $article['favorited'],
	'%follows%' => $article['following'],
	'%article_stats%' => $format_article['stats'],
	'%id%' => $article['id'],
	'%name%' => $article['name'],
	'%category%' => $article['category'],
	'%article_id%' => $article['id'],
	'%article_name%' => $article['name'],
	'%article_category%' => $article['category'],
	'%category_id%' => $article['category'],
	'%creator%' => $article['owner'],
	'%user_sidebar%' => $user_sidebar,
	'%rss_link%' => $rss_link,
	'%default_comment_status%' => $article['default_comment_type_show'],
	'%meta_title%' => $article['meta_title'],
	'%meta_desc%' => $article['meta_desc'],
	'%meta_keywords%' => $article['meta_keywords'],
	'%creator_panel%' => $article['creator_panel'],
	'%creator_picture%' => $article['creator_picture'],
	'%category_name%' => $article['category_name'],
	'%category_link%' => $category_link,
);

// Printing?
if ($_GET['print'] == '1') {
	$display_everything = $template->render_template('article_print',$user,$special_changes,'1','1','0','','1');
} else {
	// Use the correct template!
	if (empty($article['template'])) {
		if (! empty($category['template'])) {
			$template_custom = $category['template'];
		} else {
			$template_custom = '0';
		}
	} else {
		$template_custom = $article['template'];
	}
	/*
	$display_everything = $template->render_template('article',$user,$special_changes,'0','0','',$template_custom,'1');
	$display_everything = $manual->create_article('article',$user,$special_changes,'0','0','',$template_custom,'1');
	*/
	$display_everything = $manual->create_page($article,$category,$special_changes,$template_custom);
}

// Rendering a PDF?
// Must appear before the performance testing.
if (isset($_GET['pdf'])) {
	if ($_GET['pdf'] == '1') { $pdfing = '1'; }
	else { $pdfing = '0'; }
}
if ($pdfing == '1') {
	$filename = str_replace(' ','_',$article['name']);
	require PATH . "/includes/dompdf/dompdf_config.inc.php";
	$dompdf = new DOMPDF();
	$dompdf->load_html($display_everything);
	$dompdf->render();
	$dompdf->stream($filename);
	exit;
}

if (PERFORMANCE_TESTS == '1') {
	$end = microtime(true);
	$dif = $end - $start;
	echo "<div class=\"bd_system\" style=\"z-index:9999999;position:fixed;bottom:0;right:0;padding:8px;background-color:#000;color:#fff;\"><b>Load time = $dif seconds.</b></div>";
}

echo $display_everything;
exit;

?>