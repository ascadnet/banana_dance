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


require "config.php";

$total_results = 0;

// Performance Testing
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

// Query submitted?
if (empty($_GET['q'])) {
	$db->show_error('Enter a search query!');
	exit;
}


// Get all subcategories of the category
// being searched.
$inner_where = '';
if (! empty($_GET['category'])) {
	$add_where = "AND (";
	$subcates = $manual->get_subcategories_of($_GET['category']);
	$explode = explode(',',$subcates);
	foreach ($explode as $category) {
		$inner_where .= " OR `category`='" . $category . "'";
	}
	$inner_where = ltrim($inner_where,' OR ');
	$add_where .= $inner_where;
	$add_where .= ")";
}

// Hashtag Search?
$check_q = urldecode($_GET['q']);
if (strlen(ltrim($check_q,'#')) < strlen($check_q)) {
	$tags = explode(',',$check_q);
	foreach ($tags as $tag) {
		$clean_tag = ltrim($tag,'#');
		$where_add .= " OR `tag`='" . $db->mysql_clean($clean_tag) . "'";
	}
	$where_add = ltrim($where_add,' OR ');
	$result_unique = array();
	$q = "SELECT `page_id` FROM `" . TABLE_PREFIX . "article_tags` WHERE $where_add AND `page_id`!='0'";
	$found = $db->run_query($q);
	while ($row = mysql_fetch_array($found)) {
		if (! in_array($row['page_id'],$result_unique)) {
			$total_results++;
			$result_unique[] = $row['page_id'];
			$article_data = $manual->get_article($row['page_id'],'0');
			$this_results = get_search_result($article_data);
			$all_results .= $this_results;
		}
	}
}

// Standard Search
else {
	// Search article text and name
	$q = "
		SELECT *,
			MATCH (name) AGAINST ('+\"" . $db->mysql_clean($_GET['q']) . "\"+' IN BOOLEAN MODE) AS score1,
			MATCH (content) AGAINST ('+\"" . $db->mysql_clean($_GET['q']) . "\"+' IN BOOLEAN MODE) AS score2
		FROM `" . TABLE_PREFIX . "articles`
		WHERE MATCH (name,content) AGAINST ('+\"" . $db->mysql_clean($_GET['q']) . "\"+' IN BOOLEAN MODE)" . $add_where . " AND `public`='1'
		ORDER BY (score1*1.5)+(score2) DESC
		LIMIT 50
	";
	
	//echo $q;
	
	$results = $db->run_query($q);
	while ($row = mysql_fetch_array($results)) {
		$this_results = get_search_result($row,$row['score']);
		$all_results .= $this_results;
		$total_results++;
	}
}

// User sidebar
if (! empty($user)) {
	$user_sidebar = $template->render_template('logged_in_sidebar',$user,'','1');
} else {
	$user_sidebar = $template->render_template('logged_out_sidebar','','','1');
}

// Category Tree
$cache_category_list = $db->get_option('cache_category_list');
$category_tree = $manual->category_tree('0','',$cache_category_list);

// No results
if ($total_results <= 0) {
	$total_results = '0';
	$all_results = $template->render_template('search_no_results',$user,$special_changes,'1');
}

// Cache code
if ($cache_category_list == '1') {
	$cache_code = "$(window).load(function () { closeCategory('0');expandCategory('" . htmlentities($_GET['category']) . "'); });";
}

$title = NAME . " &raquo; Search Results ($total_results)";

// Management Bar
// $manage_bar = $manual->article_sidebar('','1','');

// Breadcrumbs
$add_crumbs = array(
	"<a href=\"#\">" . lg_search_results . "</a>"
);
$breadcrumbs = $manual->breadcrumbs('0','','','',$add_crumbs);
$title = $manual->get_page_title($breadcrumbs);

// Prepare the final template
$special_changes = array(
	'%breadcrumbs%' => $breadcrumbs,
	'%search_results%' => $all_results,
	'%total_results%' => $total_results,
	'%category_tree%' => $category_tree,
	'%user_sidebar%' => $user_sidebar,
	'%category%' => htmlentities($_GET['category']),
	'%meta_title%' => $title,
	'%manage_bar%' => $manage_bar
);
$display_everything = $template->render_template('search',$user,$special_changes,'0');

// Performance Testing
if (PERFORMANCE_TESTS == '1') {
	$end = microtime(true);
	$dif = $end - $start;
	echo "<div class=\"bd_system\"><b>Performance Testing: $dif</b></div>";
}

echo $display_everything;
exit;



function get_search_result($row,$score = '') {
	global $manual;
	global $db;
	global $template;
	$article_crumbs = $manual->breadcrumbs($row['category'],$row['id'],' / ','','0');
	// $snippet = substr($row['content'],0,150) . "...";
	$article_link = $manual->prepare_link($row['id'],$row['category'],$row['name'],htmlentities($_GET['q']));
	$special_changes = $manual->replace_article_tags($row);
	if (! empty($score)) {
		$special_changes['search_score'] = round($score,2);
	} else {
		$special_changes['search_score'] = '1';
	}
	$this_results = $template->render_template('search_result',$user,$special_changes,'1');
	return $this_results;
}

?>