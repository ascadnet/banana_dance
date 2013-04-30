<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Ajax search results.
	
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

require "../config.php";

// ----------------------------------------
//	Private Site?

$public_site = $manual->check_public();
if ($public_site == '0' && empty($user)) {
	$text = lg_private_site;
	$db->show_error($text);
	exit;
}

$length = strlen($_POST['q']);
if ($length < 3) {
	echo "0+++no";
	exit;
}

$all_results = '';
$check_q = urldecode($_POST['q']);
if (strlen(ltrim($check_q,'#')) < strlen($check_q)) {
	$tags = explode(',',$check_q);
	foreach ($tags as $tag) {
		$clean_tag = ltrim($tag,'#');
		$where_add .= " OR `tag`='" . $db->mysql_clean($clean_tag) . "'";
	}
	$where_add = ltrim($where_add,' OR ');
	$result_unique = array();
	$q = "SELECT `page_id` FROM `" . TABLE_PREFIX . "article_tags` WHERE $where_add AND `page_id`!='0' LIMIT 10";
	$found = $db->run_query($q);
	while ($row = mysql_fetch_array($found)) {
		if (! in_array($row['page_id'],$result_unique)) {
			$page_link = $manual->prepare_link($row['page_id'],'','');
			$all_results .= "<li id=\"#" . $row['tag'] . "\" onclick=\"window.location='" . $page_link . "';\"><a href=\"$page_link\">" . $row['tag'] . "</a></li>";
		}
	}
}

else {
	// Search article text and name
	$q = "
		SELECT `id`,`category`,`name`
		FROM `" . TABLE_PREFIX . "articles`
		WHERE `name` LIKE '%" . $db->mysql_clean($_POST['q']) . "%' AND `public`='1'
		ORDER BY `name` DESC
		LIMIT 10
	";
	$results = $db->run_query($q);
	while ($row = mysql_fetch_array($results)) {
		$page_link = $manual->prepare_link($row['id'],$row['category'],$row['name']);
		$all_results .= "<li id=\"" . $row['name'] . "\" onclick=\"window.location='" . $page_link . "';\"><a href=\"$page_link\">" . $row['name'] . "</a></li>";
	}
}

if (empty($all_results)) {
	echo "0+++no";
	exit;
} else {
	$all_results = "<li style=\"display:none;\"></li>" . $all_results;
	echo "1+++$all_results";
	exit;
}

?>