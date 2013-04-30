<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Printer-friendly rendering fucntions.
	
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


class printing extends db {


	function get_categories($id,$subcategories='1') {
		$current_level[] = $id;
		// Categories
		$q = "SELECT `id`,`name` FROM `" . TABLE_PREFIX . "categories` WHERE `subcat`='$id' ORDER BY `order` ASC";
		$result = $this->run_query($q);
		while ($row = mysql_fetch_array($result)) {
			// Add to main UL
			if ($subcategories == '1') {
				$current_level[] = $this->get_categories($row['id'],$subcategories);
			}
		}
		return $current_level;
	}
	
	
	function format_categories($array,$level = '0') {
		if ($level == '0') {
			$margin_left = 0;
		} else {
			$margin_left = $level * 30 - 30;
		}
		foreach ($array as $element) {
			if (is_array($element)) {
				$level++;
				$this->format_categories($element,$level);
				$level = 0;
			} else {
				if ($element == '0') {
					$name['name'] = NAME;
				} else {
					$q = "SELECT `name` FROM `" . TABLE_PREFIX . "categories` WHERE `id`='" . $element . "' LIMIT 1";
					$name = $this->get_array($q);
				}
				echo "<li class=\"category_header\" style=\"margin-left:" . $margin_left . "px\"><a href=\"#category" . $element . "\">" . $name['name'] . "</a></li>";
				$articles = $this->get_articles($element);
				echo $articles;
			}
		}
	}



	function get_articles($category) {
		global $user;
		global $manual;
		$article_ul = "<ul class=\"articles\">";
		// Articles
		$q1 = "SELECT `id`,`name`,`public` FROM `" . TABLE_PREFIX . "articles` WHERE `category`='$category' ORDER BY `order` ASC";
		$result1 = $this->run_query($q1);
		while ($row = mysql_fetch_array($result1)) {
			// Add to main UL
			$go = 0;
   			if ($row['public'] == '1') { $go = 1; }
   			else if ($row['public'] == '2') {
   				if (empty($user)) {
   					$go = 0;
   				} else {
   					$hasAccess = $manual->user_permissions($row['id'],$user);
   					if ($hasAccess == '1') {
   						$go = 1;
   					} else {
   						$go = 0;
   					}
   				}
   			}
   			else  { $go = 0; }
   			if ($go == '1') {
				$article_ul .= "<li class=\"article\"><a href=\"#article" . $row['id'] . "\">" . $row['name'] . "</a></li>";
			}
		}
		$article_ul .= "</ul>";
		return $article_ul;
	}
	
	

	function get_formatted_articles($array) {
		global $manual;
		global $db;
		global $user;
		foreach ($array as $element) {
			if (is_array($element)) {
				$level++;
				$this->get_formatted_articles($element);
				$level = 0;
			} else {
				// Category
				$cate_name = $manual->get_category_name_from_id($element);
				echo "<div class=\"category_hr\"></div>";
				echo "<a name=\"category" . $element . "\"></a><h1 class=\"category_name\"><div class=\"highlight\">" . $cate_name . "<span class=\"toTop\"><a href=\"#top\">^ Top</a> - <a href=\"" . URL . "/print_all/" . $element . "\">Print this category</a> - <a href=\"" . URL . "/print_pdf/" . $element . "\">Save PDF</a></span></div></h1>";
				$category_articles = $this->get_articles($element);
				echo "<div class=\"sub_articles\">" . $category_articles . "</div>";
				// Articles
				$q1 = "SELECT `id`,`public` FROM `" . TABLE_PREFIX . "articles` WHERE `category`='" . $element . "' ORDER BY `order` ASC";
				$result1 = $this->run_query($q1);
				while ($row = mysql_fetch_array($result1)) {
					$go = 0;
					// Cache considerations
					if ($row['public'] == '1') { $go = 1; }
					else if ($row['public'] == '2') {
						if (empty($user)) {
							$go = 0;
						} else {			
							$hasAccess = $manual->user_permissions($row['id'],$user);
							if ($hasAccess == '1') {
								$go = 1;
							} else {
								$go = 0;
							}
						}
					}
					else  { $go = 0; }
					// Add article to output
					if ($go == '1') {
						$article = $manual->get_article($row['id'],'1');
						$article_crumbs = "<a href=\"" . URL . "\">" . NAME . "</a>" . $manual->breadcrumbs($article['category'],$article['id']);
						echo "<div class=\"article_hr\"/></div><div class=\"article_entry\">";
						echo "<a name=\"article" . $row['id'] . "\"></a><h1 class=\"article_name\">" . $article['name'] . "<br /><span class=\"breadcrumbs\">$article_crumbs</span><span class=\"toTop\"><a href=\"#top\">^ Top</a> - <a href=\"" . URL . "/print/" . $element . "/" . $article['id'] . "\">Print this article</a> - <a href=\"" . URL . "/print_pdf/article/" . $element . "/" . $article['id'] . "\">Save PDF</a></span></h1>";
						$article = $manual->get_article($row['id'],'1');
						if ($db->get_option('cache_articles') == '1') {
						   	$cache_file = PATH . "/generated/article-" . $article['id'] . ".php";
						   	if (! file_exists($cache_file)) {
								$manual->cache_article($article['id'],$article);
						   	}
					 		ob_start();
					 		include($cache_file);
					 		$return_file = ob_get_contents();
					 		ob_end_clean();
					 		echo $return_file;
						} else {
							echo $manual->format_article($article,$article['content'],$user,'','1','1');
						}
						echo "</div>";
					}
				} // while
			}
		}
	}
	
	
}

?>