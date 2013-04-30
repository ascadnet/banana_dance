<?php

/*	====================================================

	"Horizontal Navigation Bar" by Jon Belelieu
	For Banana Dance (Plugin)
	http://www.doyoubananadance.com/
	http://www.ascadnetworks.com/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Creates a horizontal navigation menu
	based on your base category sub-categories and pages.
	
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

// ----------------------------
// 	Require some stuff and
//	set up some paths.

require_once PATH . '/addons/widgets/horizontal_nav/functions.php';
$theme_path = URL . "/templates/html/" . $theme . "/imgs";

// ----------------------------
// 	Start the menu

$primary_ul = "<ul id=\"hnav_primary\">";

// ----------------------------
// 	Add the Base Category

if ($manual->plugin_option('display_base','horizontal_nav') == '1') {
	$primary_ul .= "<li id=\"hnav_m_0\" class=\"main_li nav_home\"><a href=\"" . URL . "\">Home</a>";
	$primary_ul .= hnav_articles('0');
	$primary_ul .= "</li>";
}

// -----------------------------------------------------
// 	Get all categories within the base-category
//	and create a menu for each.

$use_images = $manual->plugin_option('use_images','horizontal_nav');

$q = "SELECT `name`,`id`,`subcat`,`home_article` FROM `" . TABLE_PREFIX . "categories` WHERE `base`!='1' ORDER BY `order` ASC";
$categories = $this->run_query($q);
while ($row = mysql_fetch_array($categories)) {
	// If it is in the base category we add
	// it to the primary list.
	if (empty($row['subcat'])) {
		$lower = strtolower(str_replace(' ','_',$row['name']));
		if (! empty($row['home_article'])) {
			$article = $manual->get_article($row['home_article'],'id,name');
			$final_link = $manual->prepare_link($article['id'],$row['id'],$article['name']);
			if ($use_images == '1') {
				$show = "<a href=\"$final_link\"><img src=\"$theme_path/nav-$lower.png\" border=\"0\" alt=\"" . $row['name'] . "\" title=\"" . $row['name'] . "\" /></a>";
			} else {
				$show = "<a href=\"$final_link\">" . $row['name'] . "</a>";
			}
		} else {
			$final_link = '';
			if ($use_images == '1') {
				$show = "<img src=\"$theme_path/nav-$lower.png\" border=\"0\" alt=\"" . $row['name'] . "\" title=\"" . $row['name'] . "\" />";
			} else {
				$show = $row['name'];
			}
		}
		$primary_ul .= "<li id=\"hnav_m_" . $row['id'] . "\" class=\"main_li nav_" . strtolower($row['name']) . "\" $final_link>" . $show;
		$primary_ul .= hnav_articles($row['id']);
		$primary_ul .= "</li>";
	}
}

// ----------------------------
// 	Close the menu

$primary_ul .= "</ul>";

// ----------------------------
// 	Combine everything
$path = URL . "/addons/widgets/horizontal_nav/nav.js";
echo "<script type=\"text/javascript\" src=\"$path\"></script>";
echo $primary_ul;

?>