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

$delimiter = ' / ';
$skip_cate = '';
$cache_code = '';
$sharing_code = '';
$highlight = '';
$original_id = '';
$get_id = '';
$original_category = '';
$printing_pdf = '0';

// ----------------------------------------
//	Private Site?

$public_site = $manual->check_public();
if ($public_site == '0' && empty($user)) {
   		$text = lg_private_site;
   		$db->show_error($text,'',lg_private_site_title);
   		exit;
}

// --------------------------------------------------------------------------------
//	The following section is devoted solely
//	to making sure that we have a valid
//	category and article ID

// $explode_url = explode('/',$_GET['v']);
// $main_input = $explode_url['0'];

$_GET['v'] = ltrim($_GET['v'],'/');
$url_display_type = $db->get_option('url_display_type');
if ($url_display_type == 'Name') {
	// The basics
	$explode_url = explode('/',$_GET['v']);
	$main_input = $explode_url['0'];
	$item0 = $explode_url['0'];
	$item1 = $explode_url['1'];
	$item2 = urldecode($explode_url['2']);
} else {
	$main_input = $_GET['v'];
	$item0 = $_GET['category'];
	$item1 = $_GET['id'];
	$item2 = $_GET['highlight'];
}


// print_r($explode_url);
//	print_r($_GET);
//	echo "<li>$_GET[v]<li>$main_input<li>$item0<li>$item1";

if (strtolower($main_input) == 'print') {
	$printing = '1';
	//$input_category = $explode_url['1'];
	//$input_id = $explode_url['2'];
	$input_category = $item1;
	$input_id = $item2;
	$get_id = $item2;
}

else if (strtolower($main_input) == 'print_all') {
   	header('Location: ' . URL . '/functions/print.php?pdf=0&category=' . $item1 . '&subcategories=' . $item2);
   	exit;
}

else if (strtolower($main_input) == 'user') {

//	echo "Asdsa";

	if ($url_display_type == 'Name') {
		$item1 = trim(str_replace('/user/','',$item1),'/');
		$_GET['id'] = $item1;
		if ($item2 == 'public') {
			$_GET['public'] = '1';
			$_GET['p'] = '';
		} else {
			$_GET['p'] = $item2;
		}
	}
	include "user.php";
   	exit;
}

else if (strtolower($main_input) == 'print_pdf') {
	if ($item1 == 'article') {
		$printing_pdf = '1';
	}
	else {
		header('Location: ' . URL . '/functions/print.php?pdf=1&category=' . $item1 . '&subcategories=' . $item2);
		exit;
	}
}

else if (strtolower($main_input) == 'revision') {
   	header('Location: ' . URL . '/revision.php?id=' . $item1);
   	exit;
}

else if (strtolower($main_input) == 'category') {
	$input_category = $item1;
	$input_id = '';
}

else {
	// The basics
	$input_category = $item0;
	$input_id = $item1;
	$input_highlight = urldecode($item2);
   	// Auto-edit page?
   	if ($input_highlight == 'de95b43bceeb4b998aed4aed5cef1ae7') {
   		$_GET['edit'] = '1';
   		$input_highlight = '';
   	}
   	// Auto-add page?
   	if ($input_id == '76ea0bebb3c22822b4f0dd9c9fd021c5') {
   		$input_id = '';
   		$_GET['create'] = '1';
   		$input_highlight = '';
   	}
}

// Some housekeeping

$input_id = $db->urldecodeclean($input_id);
$input_category = $db->urldecodeclean($input_category);
$original_id = $input_id;
$original_category = $input_category;
$url_display_type = $db->get_option('url_display_type');

// ----------------------------------------
//	Category ID

//	Let's first figure out the category ID
// 	Here no category was submitted, so
//	presume this is the home article
if (empty($input_category)) {
	if (empty($input_id)) {
		//$input_id = "1";
	   	$category = $manual->get_category('0','','home_article');
	   	if (! empty($category['home_article'])) {
	   		$input_id = $category['home_article'];
			$get_id = $input_id;
	   	} else {
			$get_id = '1';
		}
	} else {
		$get_id = $input_id;
	}
	$input_category = "0";
}

// Here a category was submitted, so check
// to make sure it was a numeric ID.
else {
	if ($url_display_type == 'Name') {
		if ($input_category == "Home") {
			$input_category = "0";
		} else {
			$cate_id = $manual->get_category_id_from_name($input_category);
			$input_category = $cate_id;
		}
	}
}

// ----------------------------------------
// 	Default article

//	If no article ID is submitted, determine
//	what article to display.

// Here we are viewing a category,
// no page ID submitted
if (empty($input_id)) {
	// Category submitted but no ID,
	// so get the category's default
	// article, if any...
	// If we can't find one there will
	// be an error screen.
   	$category = $manual->get_category($input_category,'','home_article');
   	if (! empty($category['home_article'])) {
   		$input_id = $category['home_article'];
   	} else {
		$input_id = $manual->no_homepage_article($category);
   	}
}

// ----------------------------------------
//	Article ID was submitted
//	Make sure it is a numeric ID
//	if SEO-friendly links are
//	being used.

else {
	// Try to find based on article name
	if (! empty($get_id)) {
		$input_id = $get_id;
	} else {
		if ($url_display_type == 'Name') {
			$get_id = $manual->get_article_id_from_name($input_id,$input_category);
			$input_id = $get_id;
		}
	}
}

// ----------------------------------------
// 	Article Details
//	Get article details if possible.

if (! empty($input_id)) {
	$article = $manual->get_article($input_id,'0','*','1','1');
}

if (empty($article['id'])) {
	// Search for exceptions based on redirect rules.
	$exception = $manual->find_redirect_rule($original_category,$original_id);
	if (empty($exception)) {
		// Create it link?
		$create_link = "<br /><br /><a href=\"#\" onclick=\"return editArticle('new','$original_id','$input_category');\">" . lg_create_page . "</a>";
		// Still nothing?
		$db->show_error(lg_page_not_found . $create_link);
		exit;
	} else {
		$article = $manual->get_article($exception,'0','*','1','1');
   		$category = $manual->get_category($article['category']);
	}
}

// ----------------------------------------
//	Article found
//	Get the remaining information that
//	we need to continue.
else {
	// Article Details
	if ($skip_cate != "1") {
		$category = $manual->get_category($article['category']);
	}
}

// ----------------------------------------
//	Custom Redirection Setup?

if (! empty($article['redirect'])) {
	header('Location: ' . $article['redirect']);
	exit;
}

// $article_crumbs = $manual->breadcrumbs($article['category'],$input_id,'0','0',$delimiter,$article,'0');
// $breadcrumbs = "<a href=\"" . URL . "\">" . NAME . "</a>" . $article_crumbs;
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
	else if ($category['id'] == '1') {
		$show_article = "1";
		$article_text = "";
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
// define('BD_ARTICLE_VIEWING_FORMAT',$article['format_type']); // 1 = wiki, 2 = html
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
$category_tree = $manual->category_tree($input_category,'',$cache_category_list);
$this_cate_tree = $manual->category_tree($input_category,'0','0','1','0',' AND `display_on_sidebar`="1" ORDER BY `order` ASC','1');
// $category_link = $manual->prepare_link('',$article['category']);
	
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
if (! empty($input_highlight)) {
	$highlight = $input_highlight;
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
	if ($db->get_option('cache_articles') == '1') {
	   	$cache_file = PATH . "/generated/article-" . $article['id'] . ".php";
	   	if (! file_exists($cache_file)) {
			$manual->cache_article($article['id'],$article);
	   	}
 		ob_start();
 		include($cache_file);
 		$return_file = ob_get_contents();
 		ob_end_clean();
 		// Create the array
		$format_article = array();
		$format_article['article'] = $return_file;
		$format_article['sublinks'] = "";
		$format_article['options'] = "";
		$format_article['stats'] = "";
	} else {
		$format_article = $manual->format_article($article,$article['content'],$user,$highlight,'0','0',$user_data);
	}
}

// Cache code
if ($cache_category_list == '1') {
	$cache_code = "$(window).load(function () { closeCategory('0');expandCategory('" . $article['category'] . "'); });";
}


$format_created = $db->format_date($article['created']);

/*
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
$article['name'] = str_replace('%created%',$format_created,$article['name']);
$article['name'] = str_replace('%last_updated%',$db->format_date($article['last_updated']),$article['name']);
$article['meta_title'] = str_replace('%name%',$article['name'],$article['meta_title']);
$article['meta_title'] = str_replace('%created%',$format_created,$article['meta_title']);
*/

// Favorite?
$links = article_links($article['id'],$article['category'],$article['favorited'],$article['following'],$article['comments']);

// RSS Link
// $rss_link = URL . '/rss/category.php?id=' . $article['category'];

// Prepare the final template
$special_changesB = $manual->replace_article_tags($article);
$special_changesA = array(
	'%discussion%' => $render_discussion,
	'%total_comments%' => $comments['0'],
	'%category_tree%' => $category_tree,
	'%category_sublinks%' => $this_cate_tree,
	'%links%' => $links,
	'%formatted_article%' => $format_article['article'],
	'%article_sublinks%' => $format_article['sublinks'],
	'%manage_bar%' => $manage_bar,
//	'%article_stats%' => $format_article['stats'],
	'%user_sidebar%' => $user_sidebar
);
$special_changes = array_merge($special_changesA,$special_changesB);

//	'%rss_link%' => $rss_link,
//	'%id%' => $article['id'],
//	'%name%' => $article['name'],
//	'%category%' => $article['category'],
//	'%article_id%' => $article['id'],
//	'%article_name%' => $article['name'],
//	'%article_category%' => $article['category'],
//	'%category_id%' => $article['category'],
//	'%creator%' => $article['owner'],
//	'%favorites%' => $article['favorited'],
//	'%follows%' => $article['following'],
//	'%breadcrumbs%' => $breadcrumbs,
//	'%category%' => $input_category,
//	'%created%' => $db->format_date($article['created']),
//	'%last_updated%' => $db->format_date($article['last_updated']),
//	'%views%' => $article['views'],
//	'%meta_title%' => $article['meta_title'],
//	'%meta_desc%' => $article['meta_desc'],
//	'%meta_keywords%' => $article['meta_keywords'],
//	'%creator_picture%' => $article['creator_picture'],
//	'%category_name%' => $article['category_name'],
//	'%category_link%' => $category_link,
//	'%default_comment_status%' => $article['default_comment_type_show'],

// Printing?
if ($printing == '1') {
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
if ($printing_pdf == '1') {
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