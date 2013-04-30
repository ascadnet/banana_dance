<?php

/* ------------------------------------------------

	Banana Dance Plugin
	"Horizontal Navigation Bar"
	by Ascad Networks
	http://www.ascadnetworks.com/
	
	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.

------------------------------------------------ */



// -----------------------------------------------------
//	Create a list of all articles
//	in a category that are displayed
//	in the primary navigation.

function hnav_articles($id) {
	global $db;
	global $manual;
	$found_article = '0';
	$q = "SELECT `name`,`id` FROM `" . TABLE_PREFIX . "articles` WHERE `category`='$id' AND `display_on_sidebar`='1' ORDER BY `order` ASC";
	$articles = $db->run_query($q);
	$final_list = "<ul id=\"hnav_category$id\" class=\"hnav_articles\" style=\"display:none;\">";
	while ($row = mysql_fetch_array($articles)) {
		$found_article = '1';
		$link = $manual->prepare_link($row['id'],$id,$row['name']);
		$final_list .= "<li><a href=\"$link\">" . $row['name'] . "</a></li>";
	}
	$final_list .= "</ul>";
	if ($found_article != '1') {
		$final_list = "";
	}
	return $final_list;
}

?>