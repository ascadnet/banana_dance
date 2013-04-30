<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: admin CP functions.
	
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


class admin extends db {

	// ---------------------------------------------------------------------------
	// 	General checks
	
	function __construct() {
		global $privileges;
		if ($privileges['is_admin'] != '1') {
			echo "You must be an administrator to use this class. If you are trying to log in, <a href=\"" . ADMIN_URL . "/static_login.php\">click here</a>.";
			exit;
		}
	}

	// ---------------------------------------------------------------------------
	// 	Get a list of all existing comment statuses
	//	Used on the edit comment page
	
	function get_comment_statuses($current,$type = 'list') {
	
		if ($type == 'list') {
			if ($current == "0") {
	 			$return .= "<li class=\"selected\">";
			} else {
				$return = "<li>";
			}
			$return .= "<input type=\"radio\" name=\"status\" value=\"0\"";
			if ($current == "0") {
				$return .= " checked=\"checked\"";
			}
			$return .= " /> <span class=\"stat_col_l\">Standard Comment</span></li>";
		} else {
			$return = "<option value=\"0\"";
			if ($current == "0") {
				$return .= " selected=\"selected\"";
			}
			$return .= ">Standard Comment</option>";
		}
		$q = "SELECT * FROM `" . TABLE_PREFIX . "comment_statuses` ORDER BY `title` ASC";
		$statuses = $this->run_query($q);
 		while ($row = mysql_fetch_array($statuses)) {
 		
 			if ($type == 'list') {
	 			if ($current == $row['id']) {
	 				$return .= "<li class=\"selected\">";
	 			} else {
	 				$return .= "<li>";
	 			}
	 			$return .= "<div class=\"r_options\"><a href=\"index.php?l=category_status_edit&id=" . $row['id'] . "\"><img src=\"imgs/icon-edit.png\" border=\"0\" alt=\"Edit\" title=\"Edit\" class=\"icon_l\" /></a><a href=\"#\" onClick=\"deleteID('" . TABLE_PREFIX . "comment_statuses','" . $row['id'] . "');\"><img src=\"imgs/icon-delete.png\" border=\"0\" alt=\"Delete\" title=\"Delete\" class=\"icon_l\" /></a></div><input type=\"radio\" name=\"status\" value=\"" . $row['id'] . "\"";
	 			if ($current == $row['id']) {
	 				$return .= " checked=\"checked\"";
	 			}
	 			$return .= " /> <span class=\"stat_col_l\">" . $row['title'] . "</span>";
	 			if (! empty($row['desc'])) {
	 				$return .= "<span class=\"stat_col_r\">" . $row['desc'] . "</span>";
	 			}
	 			$return .= "</li>";
 			} else {
 				$return .= "<option value=\"" . $row['id'] . "\"";
				if ($current == $row['id']) {
					$return .= " selected=\"selected\"";
				}
 				$return .= " />" . $row['title'] . "</option>";
 			}
 			
 		}
 		return $return;
	}
	
	// ---------------------------------------------------------------------------
	// 	Determine whether we need to
	//	get the latest BD news.
	
	function get_news_feed() {
		$allow_outside_connections = $this->get_option('allow_outside_connections');
		if ($allow_outside_connections == '1') {
			// Where is the file located.
			$path = PATH . "/generated/bd_news.xml";
			// Check if we need to update the file.
			$last_check = $this->get_option('last_news_update');
			if (time()-strtotime($last_check) > 172800 || ! file_exists($path)) {
				$this->update_feed();
				$q = "UPDATE `" . TABLE_PREFIX . "options` SET `value`='" . $this->current_date() . "' WHERE `key`='last_news_update' LIMIT 1";
				$this->update($q);
			}
			// Parse the feed
			$feed = $this->parse_rss_feed($path);
			return $feed;
		} else {
			return "<p>External connections have been turned off. <a href=\"http://www.doyoubananadance.com/community/article/Program+News\" target=\"_blank\">Click here</a> to view Banana Dance news.</p>";
		}
	}
	
	// ------------------------------------
	// 	Get Banana Dance News from
	//	external server
	function update_feed() {
		$allow_outside_connections = $this->get_option('allow_outside_connections');
		$file = file_get_contents("http://www.doyoubananadance.com/rss/category.php?id=1");
		$location = PATH . "/generated/bd_news.xml";
		$this->write_file($location,$file);
	}
	
	// --------------------------------------------------------------------
	// Update a comment and all subcomments for that comment

	/*
	function update_subcomments($id,$new_status = '',$article_id = '') {
		// Form the query
		$update = '';
		if (empty($new_status)) { $update .= ",`status`='0'"; }
		else { $update .= ",`status`='" . $this->mysql_clean($new_status) . "'"; }
		
		if (empty($article_id)) { $update .= ",`article`='" . $this->mysql_clean($article_id) . "'"; }
		else { $update .= ",`article`='" . $this->mysql_clean($article_id) . "'"; }
		$update = substr($update,1);
		// Continue to update
		if (! empty($update)) {
			// Update MySQL
			$q1 = "UPDATE `" . TABLE_PREFIX . "comments` SET $update WHERE `id`='" . $this->mysql_clean($id) . "' LIMIT 1";
			$run = $this->update($q1);
			// Scan for subcomments
		   	$q = "SELECT `subcomment`,`id` FROM `" . TABLE_PREFIX . "comments` WHERE `subcomment`='" . $this->mysql_clean($id) . "'";
		   	$get = $this->get_array($q);
		   	if (! empty($get['subcomment'])) {
		   		$this->update_subcomments($get['id'],$new_status,$article_id);
		   	}
		}
		
	}
	*/
	
	
	// --------------------------------------------------------------------
	// Get an A-Z list
	
	function alpha_list($get,$current_letter = "") {
		if (empty($current_letter)) {
			$az_list = "<span class=\"letter on\"><a href=\"index.php?$get&alpha=\">All</a></span>\n";
		} else {
			$az_list = "<span class=\"letter\"><a href=\"index.php?$get&alpha=\">All</a></span>\n";
		}
		$letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$total = 26;
		$up = 0;
		while ($total > 0) {
			if ($current_letter == $letters[$up]) {
				$az_list .= "<span class=\"letter on\"><a href=\"index.php?$get&alpha=" . $letters[$up] . "\">" . $letters[$up] . "</a></span>\n";
			} else {
				$az_list .= "<span class=\"letter\"><a href=\"index.php?$get&alpha=" . $letters[$up] . "\">" . $letters[$up] . "</a></span>\n";
			}
			$up++;
			$total--;
		}
		return $az_list;
	}
	
	

	// --------------------------------------------------------------------
	// 	Get the pagination information
	//	This is for admin functions
	
	function paginate($total,$display,$link,$cur_page = "1") {
   		$show_pages = "17";
		$actual_pages = ceil($total / $display);
		if ($actual_pages < 18) {
			$cur = 0;
	   		while ($actual_pages > 0) {
	   			$cur++;
	   			if ($cur == $cur_page) {
	   				$final_page .= "<span class=\"page on\"><a href=\"$link&p=$cur\">$cur</a></span>";
	   			} else {
	   				$final_page .= "<span class=\"page\"><a href=\"$link&p=$cur\">$cur</a></span>";
	   			}
	   			$actual_pages--;
	   		}
		} else {
			$cur = 0;
	   		$pages = "9";
	   		$both_sides = "8";
	   		$current = "0";
	   		$final_numbers = "";
	   		// Only display 9 pages at once
	   		$first_couple = $cur_page - $both_sides;
	   		$last_couple = $cur_page + $both_sides;
	   		// Consider anything with 4 of the last page
	   		if ($first_couple <= 0) {
	   			$end_start = $cur_page + 1;
	   			$begin_start = "1";
	   			$first_couple = $cur_page - 1;
	   			$last_couple = $show_pages - $first_couple - 1;
	   		}
	   		// Consider anything with 4 of the first page
	   		else if ($last_couple > $show_pages) {
	   			$first_couple = $both_sides;
	   			$begin_start = $cur_page - $both_sides;
	   			$end_start = $cur_page + 1;
	   			$last_couple = $show_pages - $cur_page;
	   			$reverse_add = $both_sides - $last_couple;
	   			$first_couple += $reverse_add;
	   			$begin_start -= $reverse_add;
	   		}
	   		// Consider all other options
	   		else {
	   			$first_couple = $both_sides;
	   			$last_couple = $both_sides;
	   			$begin_start = $cur_page - $both_sides;
	   			$end_start = $cur_page + 1;
	   		}
	   		// Do the first couple pages
	   		$i = 0;
	   		while ($i < $first_couple) {
	   			$final_page .= "<span class=\"page\"><a href=\"$link&p=$begin_start\">$begin_start</a></span>";
	   			$begin_start++;
	   			$i++;
	   		}
	   		// Do the current page
	   		$final_page .= "<span class=\"page on\"><a href=\"$link&p=$cur_page\">$cur_page</a></span>";
	   		// Do the last couple pages
	   		$i = 0;
	   		while ($i < $last_couple) {
	   			$final_page .= "<span class=\"page\"><a href=\"$link&p=$end_start\">$end_start</a></span>";
	   			$end_start++;
	   			$i++;
	   		}
   		}
		return $final_page;
	}
	
	
	// ------------------------------------
	// 	Read RSS Feed
	
	function parse_rss_feed($feed_url) {  
		if (class_exists('SimpleXmlElement')) {
			$final = "";
			$content = file_get_contents($feed_url);
			$x = new SimpleXmlElement($content);
			foreach ($x->channel->item as $entry) {
				$final .= "<div class=\"home_news\">\n";
				$final .= "<img src=\"imgs/icon-news.png\" width=\"32\" height=\"32\" border=\"0\" alt=\"New Version\" title=\"New Version\" style=\"float:left;margin-right: 20px;\" />\n";
				$final .= "<p class=\"home_comment_title\"><span class=\"home_count\"><a href=\"" . $entry->link . "\" target=\"_blank\">" . $entry->title . "</a></span><span class=\"home_name\">" . $this->format_date($entry->pubDate) . "</span></p>\n";
				if (! empty($entry->description)) {
					$final .= "<p class=\"home_comment\">" . $entry->description . "</p>\n";
				}
				$final .= "</div><div class=clear></div>\n";
			}  
			return $final;
		} else {
			return "<p>Could not read XML file, missing class \"SimpleXmlElement\". <a href=\"http://www.doyoubananadance.com/community/article/Program+News\" target=\"_blank\">Click here</a> to view Banana Dance news.</p>";
		}
	}
	
	
	// ---------------------------------------------------------------------------
	// 	Get details of a comment status
	
	function get_comment_status($status) {
		$q = "SELECT * FROM `" . TABLE_PREFIX . "comment_statuses` WHERE `id`='$status' LIMIT 1";
		$cs = $this->get_array($q);
		return $cs;
	}
	
	// ---------------------------------------------------------------------------
	// 	Get badges list
	
	function badge_list() {
		$return = '';
		$q = "SELECT `id`,`name`,`desc` FROM `" . TABLE_PREFIX . "badges` ORDER BY `name` ASC";
		$list = $this->run_query($q);
		while ($row = mysql_fetch_array($list)) {
			$return .= "<option value=\"" . $row['id'] . "\">" . $row['name'] . " (" . $row['desc'] . ")</option>";
		}
		return $return;
	}
	
	
	// ---------------------------------------------------------------------------
	// 	Get articles within a category.
	//	Used for sortable list on category edit page
	
	function article_li($category,$default_article = "") {
		$q = "SELECT * FROM `" . TABLE_PREFIX . "articles` WHERE `category`='$category' ORDER BY `order` ASC";
		$articles = $this->run_query($q);
 		while ($row = mysql_fetch_array($articles)) {
 			// List it
			$list .= "<li id=\"article_" . $row['id'] . "\"><a href=\"index.php?l=article_edit&id=" . $row['id'] . "\">" . $row['name'] . "</a> <div style=\"float:right;\" class=\"small\">";
			if ($default_article != $row['id']) {
				$list .= "<span class=\"default_article\" id=\"default" . $row['id'] . "\"><a href=\"#\" onClick=\"makeDefault('$category','" . $row['id'] . "');return false;\"><img src=\"imgs/icon-make_homepage.png\" border=\"0\" alt=\"Make Category Homepage\" title=\"Make Category Homepage\" class=\"icon_l\" /></a></span>";
			} else {
				$list .= "<span class=\"default_article\" id=\"default" . $row['id'] . "\"><b>Homepage</b></span>";
			}
			$list .= "<a href=\"#\" onclick=\"deleteID('" . TABLE_PREFIX . "articles','" . $row['id'] . "');return false;\"><img src=\"imgs/icon-delete.png\" border=\"0\" alt=\"Delete\" title=\"Delete\" class=\"icon_l\" /></a>
			</div></li>";
		}
		return $list;
	}
	
	
	
	// ---------------------------------------------------------------------------
	// 	Totla categories and pages
	
	function totals() {
		
		$q = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "categories` WHERE 1";
		$tot_categories = $this->get_array($q);
	
		$q = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "articles` WHERE 1";
		$tot_pages = $this->get_array($q);
		
		$total = $tot_pages + $tot_categories;
		
		$array = array(
			'categories' => $tot_categories['0'],
			'pages' => $tot_pages['0'],
			'all' => $total['0'],
		);
		
		return $array;
		
	}
	
	
	// ---------------------------------------------------------------------------
	// 	Random quote
	
	function random_quote() {
		$quotes = array(
			"<a href=\"http://en.wikipedia.org/wiki/Banana#Description\" target=\"_blank\">Technically, I'm a berry.</a>",
			"<a href=\"http://www.doyoubananadance.com/Pages/Widgets/widgets\" target=\"_blank\">Widgets? I know all about those.</a>",
			"<a href=\"http://www.doyoubananadance.com/Users/User-Login/facebook_connect#Facebook_Connect\" target=\"_blank\">I hear facebook is big nowadays...</a>",
			"<a href=\"http://www.youtube.com/watch?v=Z3ZAGBL6UBA\" target=\"_blank\">Dance off?</a>",
			"<a href=\"https://twitter.com/#!/jbelelieu\" target=\"_blank\">Twitter Much? #bananadance</a>",
			"<a href=\"http://www.youtube.com/watch?v=2pfwY2TNehw\" target=\"_blank\">You should watch this.</a>",
			"<a href=\"http://www.facebook.com/bananadance\" target=\"_blank\">Join me on the book of faces!</a>",
			"<a href=\"http://www.facebook.com/bananadance\" target=\"_blank\">First president of Zimbabwe you ask? <a href=\"http://en.wikipedia.org/wiki/President_of_Zimbabwe\" target=\"_blank\">President Banana, of course!</a></a>",
			"<a href=\"http://www.doyoubananadance.com/Download/Mobile-Theme-Library\" target=\"_blank\">I look goooooood on your phone!</a>",
		);
		$length = sizeof($quotes)-1;
		$random = rand(0,$length);
		$random_selection = $quotes[$random];
		return $random_selection;
	}
	
	
	// ---------------------------------------------------------------------------
	// 	Get full website tree
	
	function website_tree($category = '0',$level = '0') {
		
		if ($category == '0') {
			$q = "SELECT * FROM `" . TABLE_PREFIX . "categories` WHERE `base`='1' LIMIT 1";
			$base = $this->get_array($q);
			if ($base['public'] == '2') {
				$public = '<img src="imgs/icon-public-2.png" width="12" height="12" alt="Restricted Access" title="Restricted Access" border="0" class="icon_l" />';
			}
			else if ($base['public'] == '3') {
				$public = '<img src="imgs/icon-public-3.png" width="12" height="12" alt="Under Maintenance" title="Under Maintenance" border="0" class="icon_l" />';
			}
			else if ($base['public'] == '0') {
				$public = '<img src="imgs/icon-public-0.png" width="12" height="12" alt="Private" title="Private" border="0" class="icon_l" />';
			}
			else {
				$public = '';
			}
			$theList = "<ul class=\"left_map primary_top inner\" id=\"c-$category\">\n";
			$theList .= "<li class=\"category\" id=\"category_0\"><div><a href=\"#\" onclick=\"expandCate('0');return false;\"><img src=\"imgs/icon_contract.png\" border=\"0\" alt=\"Expand\" title=\"Contract\" class=\"expand\" id=\"cat_0_img\" /></a><a href=\"" . ADMIN_URL . "/index.php?l=category_edit&id=0\">Home</a>$public</div></li>\n";
			$theList .= "<li id=\"ex_0\" class=\"subcategory toplevel\" style=\"display:block;\">\n<ul class=\"inner\">\n";
		} else {
			// Base category
			$level++;
			$theList .= "<li id=\"ex_$category\" class=\"subcategory\">\n<ul id=\"c-$category\" class=\"inner\">\n";
		}
		
		// --------------------------
		// 	Subcategories
		$q = "SELECT * FROM `" . TABLE_PREFIX . "categories` WHERE `subcat`='$category' AND `base`!='1' ORDER BY `order` ASC";
		$categories = $this->run_query($q);
		while ($row = mysql_fetch_array($categories)) {
			
			if ($row['public'] == '2') {
				$public = '<img src="imgs/icon-public-2.png" width="12" height="12" alt="Restricted Access" title="Restricted Access" border="0" class="icon_l" />';
			}
			else if ($row['public'] == '3') {
				$public = '<img src="imgs/icon-public-3.png" width="12" height="12" alt="Under Maintenance" title="Under Maintenance" border="0" class="icon_l" />';
			}
			else if ($row['public'] == '0') {
				$public = '<img src="imgs/icon-public-0.png" width="12" height="12" alt="Private" title="Private" border="0" class="icon_l" />';
			}
			else {
				$public = '';
			}
			
			$name_len = strlen($row['name']);
			$max_length = 50 - $level * 10;
			if ($name_len > $max_length) {
				$show_name = substr($row['name'],0,$max_length) . '...';
			} else {
				$show_name = $row['name'];
			}
			// $theList .= "<li class=\"category\" id=\"category_" . $row['id'] . "\"><div><a href=\"#\" onclick=\"expandCate('" . $row['id'] . "');return false;\"><img src=\"imgs/icon_expand.png\" border=\"0\" alt=\"Expand\" title=\"Expand\" class=\"expand\" id=\"cat_" . $row['id'] . "_img\" /></a><a href=\"#\" onclick=\"loadPage('" . $row['id'] . "','category');return false;\">" . $row['name'] . "</a>$public</div></li>\n";
			$theList .= "<li class=\"category\" id=\"category_" . $row['id'] . "\"><div><a href=\"#\" onclick=\"expandCate('" . $row['id'] . "');return false;\"><img src=\"imgs/icon_expand.png\" border=\"0\" alt=\"Expand\" title=\"Expand\" class=\"expand\" id=\"cat_" . $row['id'] . "_img\" /></a><a href=\"" . ADMIN_URL . "/index.php?l=category_edit&id=" . $row['id'] . "\">" . $show_name . "</a>$public</div></li>\n";
			
			// --------------------------
			// 	Pages in category?
			$theList .= $this->website_tree($row['id'],$level);
			
		}
		
		// --------------------------
   		//   Pages
   		$found = 0;
   		
   		$q = "SELECT * FROM `" . TABLE_PREFIX . "articles` WHERE `category`='$category' ORDER BY `order` ASC";
   		$articles = $this->run_query($q);
   		while ($rowA = mysql_fetch_array($articles)) {
   			$found++;
   			
			if ($rowA['public'] == '2') {
				$public = '<img src="imgs/icon-public-2.png" width="12" height="12" alt="Restricted Access" title="Restricted Access" border="0" class="icon_l" />';
			}
			else if ($rowA['public'] == '3') {
				$public = '<img src="imgs/icon-public-3.png" width="12" height="12" alt="Under Maintenance" title="Under Maintenance" border="0" class="icon_l" />';
			}
			else if ($rowA['public'] == '0') {
				$public = '<img src="imgs/icon-public-0.png" width="12" height="12" alt="Private" title="Private" border="0" class="icon_l" />';
			}
			else {
				$public = '';
			}
   			
			$name_len = strlen($rowA['name']);
			$max_length = 50 - $level * 10;
			if ($name_len > $max_length) {
				$show_name = substr($rowA['name'],0,$max_length) . '...';
			} else {
				$show_name = $rowA['name'];
			}
   			// $theList .= "<li class=\"page\" onclick=\"loadPage('" . $rowA['id'] . "','page');return false;\" id=\"page_" . $rowA['id'] . "\"><div>" . $rowA['name'] . "$public</div></li>\n";
   			$theList .= "<li class=\"page\" onclick=\"window.location='" . ADMIN_URL . "/index.php?l=article_edit&id=" . $rowA['id'] . "';\" id=\"page_" . $rowA['id'] . "\"><div><a href=\"index.php?l=article_edit&id=" . $rowA['id'] . "\">" . $show_name . "</a>$public</div></li>\n";
   		}
   		if ($found == 0) {
   			$theList .= "<li class=\"none\"><div>No pages</div></li>\n";
   		}
		
		// Close the UL
		$theList .= "</ul></li>\n"; // ex_category
		
		// Close the overall UL
		if ($category == '0') {
			$theList .= "</ul>\n";
		}
		
		return $theList;
		
	}
	
	
	
	// ---------------------------------------------------------------------------
	// 	Get subcategories within a category.
	//	Used for sortable list on category edit page
	
	function field_set_li($exclude = '') {
		global $fields;
		$q = "SELECT `id` FROM `" . TABLE_PREFIX . "fields_sets` ORDER BY `order` ASC";
		$sets = $this->run_query($q);
 		while ($row = mysql_fetch_array($sets)) {
 			if (! empty($exclude)) {
 				$q1 = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "fields_sets_locations` WHERE `set_id`='" . $row['id'] . "' AND `location`='$exclude'";
 				$count = $this->get_array($q1);
 				if ($count['0'] <= "0") {
 					$set_info = $fields->field_set_data($row['id']);
 					$return .= "<option value=\"" . $row['id'] . "\">" . $set_info['name'] . "</option>\n";
 				}
 			} else {
   				$set_info = $fields->field_set_data($row['id']);
   				$return .= "<option value=\"" . $row['id'] . "\">" . $set_info['name'] . "</option>\n";
 			}
 		}
 		return $return;
	}
	
	
	// ---------------------------------------------------------------------------
	// 	Get subcategories within a category.
	//	Used for sortable list on category edit page
	
	function category_li($category,$type = 'li') {
		$q = "SELECT * FROM `" . TABLE_PREFIX . "categories` WHERE `subcat`='$category' ORDER BY `order` ASC";
		$articles = $this->run_query($q);
 		while ($row = mysql_fetch_array($articles)) {
 			// List it
 			if ($type == "select") {
				$list .= "<option value=\"" . $row['id'] . "\">" . $row['name'] . "</option>\n";
 			} else {
				$list .= "<li id=\"category_" . $row['id'] . "\"><a href=\"index.php?l=category_edit&id=" . $row['id'] . "\">" . $row['name'] . "</a> <div style=\"float:right;\" class=\"small\">";
				$list .= "<a href=\"#\" onClick=\"deleteCategory('" . $row['id'] . "');\"><img src=\"imgs/icon-delete.png\" border=\"0\" alt=\"Delete\" title=\"Delete\" class=\"icon_l\" /></a></div></li>\n";
			}
		}
		return $list;
	}
	
	
	// ---------------------------------------------------------------------------
	// 	Check if a user has permission to use
	//	various features of the control panel.
	
	function check_permission($perm,$user,$privileges) {
		if (! empty($user)) {
		   	if ($privileges[$perm] != "1" && $privileges['is_admin'] != "1") {
		   		$error = "1";
		   	}
		}
		// Not logged in
		else {
			$error = "1";
		}
		if ($error == "1") {
			echo "0+++You do not have the privilieges to perform this task.";
			exit;
		}
	}
	
	
	// ---------------------------------------------------------------------------
	// 	Create a select menu of user types
	
	function user_types_select($selected = "3",$type = "radio") {
		global $manual;
   		return $manual->user_types_select($selected,$type);
	}
	
	// ---------------------------------------------------------------------------
	// 	Get HTML templates
	
	function get_template_html_list($type = "array",$custom = '0') {
		if ($custom == '1') {
			$add_where = " WHERE `type`!='0'";
		}
		else if ($custom == 'all') {
			$add_where = "";
		}
		else {
			$add_where = " WHERE `type`='0'";
		}
 		$q = "SELECT * FROM `" . TABLE_PREFIX . "templates_html` $add_where ORDER BY `order` ASC";
		$results = $this->run_query($q);
 		if ($type == 'select') {
			$theList = '';
		} else {
			$theList = array();
		}
 		while ($row = mysql_fetch_array($results)) {
 			if ($type == 'select') {
 				$theList .= "<option value=\"" . $row['id'] . "\">" . $row['title'] . "</option>";
 			} else {
 				$theList[] = $row;
 			}
 		}
 		return $theList;
 	}
 	
	// ---------------------------------------------------------------------------
	// 	Scan for new files that can be edited.
 	
 	function scan_new_template_files() {
 		global $theme;
		// Make new files editable.
		$path = PATH . "/templates/html/" . $theme;
   		if ($handle = opendir($path)) {
   			/* This is the correct way to loop over the directory. */
   			while (false !== ($file = readdir($handle))) {
   				if ($file == '.' || $file == '..') {
   					continue;
   				} else {
   					if (! is_dir($file) && $file != '_author') {
   						$file_name = explode('.',$file);
   						
   						$q = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "templates_html` WHERE `template`='" . $this->mysql_clean($file_name['0']) . "'";
   						$count = $this->get_array($q);
   						
   						if ($count['0'] <= 0) {
   						
   							$q1 = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "templates_html` WHERE `path`='" . $this->mysql_clean($file) . "'";
   							$count1 = $this->get_array($q1);
   							if ($count1['0'] <= 0) {
   							
	   							// Can we determine more about it?
	   							if (stripos($file,'header') !== false) {
	   								$type = "1";
	   								$template = "header";
	   							}
	   							else if (stripos($file,'footer') !== false) {
	   								$type = "2";
	   								$template = "footer";
	   							}
	   							else if (stripos($file,'article') !== false) {
	   								$type = "3";
	   								$template = "article";
	   							}
	   							else {
	   								$type = "3";
	   								$template = "";
	   							}
	   							$q = "INSERT INTO `" . TABLE_PREFIX . "templates_html` (`template`,`path`,`theme`,`title`,`type`) VALUES ('$template','" . $this->mysql_clean($file) . "','$theme','$file','$type')";
	   							$insert = $this->insert($q);
	   							echo "<p class=\"highlight\">Imported template file: $file</p>";
   							
   							}
   						}
   					}
   				}
   			}
   		}
 	}
 	
	// ---------------------------------------------------------------------------
	// 	Get themes
	
	function get_theme_list($type = "array",$mobile = '0') {
		global $user;
		global $theme;
		// Install new plugins
		if ($mobile == '1') {
			$path = PATH . "/templates/mobile";
			$mobile = '1';
		} else {
			$path = PATH . "/templates/html";
			$mobile = '0';
		}
   		if ($handle = opendir($path)) {
   			/* This is the correct way to loop over the directory. */
   			while (false !== ($file = readdir($handle))) {
   				if ($file == '.' || $file == '..') {
   					continue;
   				} else {
   					$full_name = $path . "/" . $file;
   					if (is_dir($full_name)) {
   						// Find info file
   						$info_file = $path . "/" . $file . "/_author/info.xml";
   						if (file_exists($info_file)) {
							// Exists?
							$q = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "themes` WHERE `folder_name`='" . $file . "' AND `mobile`='$mobile'";
							$found = $this->get_array($q);
							if ($found['0'] <= '0') {
	   							$xml = file_get_contents($info_file);
	   							$folder_name = $this->get_xml_value('folder_name',$xml);
	   							$name = $this->get_xml_value('name',$xml);
	   							$desc = $this->get_xml_value('desc',$xml);
	   							$developer = $this->get_xml_value('developer',$xml);
	   							$developer_url = $this->get_xml_value('developer_url',$xml);
	   							if ($mobile == '1') {
	   								$type = 'mobile';
	   							} else {
	   								$type = strtolower($this->get_xml_value('type',$xml));
	   							}
	   							if ($theme == $name) {
	   								$row['on'] = '1';
	   							} else {
	   								$row['on'] = '0';
	   							}
	   							if (empty($type)) {
	   								$type = 'combo';
	   							}
	   							// Add DB entry
	   							$q1 = "INSERT INTO `" . TABLE_PREFIX . "themes` (`folder_name`,`name`,`description`,`author`,`author_url`,`selected`,`type`,`mobile`) VALUES ('" . $this->mysql_clean($folder_name) . "','" . $this->mysql_clean($name) . "','" . $this->mysql_clean($desc) . "','" . $this->mysql_clean($developer) . "','" . $this->mysql_clean($developer_url) . "','0','" . $this->mysql_clean($type) . "','$mobile')";
	   							$insert = $this->insert($q1);
   							}
   						} // Files exist?
   					} // Directory?
   				} // Not . or ..
   			} // Loop directory
   			closedir($handle);
   		}
		// Now get the existing themes
		if ($mobile == '1') {
 			$q = "SELECT * FROM `" . TABLE_PREFIX . "themes` WHERE `mobile`='1' ORDER BY `selected` DESC, `name` ASC";
 		} else {
 			$q = "SELECT * FROM `" . TABLE_PREFIX . "themes` WHERE `mobile`!='1' ORDER BY `selected` DESC, `name` ASC";
 		}
		$results = $this->run_query($q);
		$theList = array();
 		while ($row = @mysql_fetch_array($results)) {
 			$theList[] = $row;
 		}
 		return $theList;
 	}

		
	// ---------------------------------------------------------------------------
	// 	Get widget plugins
	
	function get_plugin_list($type = "array",$check_new = '1') {
		global $user;
		// Install new plugins
		$path = PATH . "/addons/widgets";
		if ($check_new = '1') {
			if ($handle = opendir($path)) {
				/* This is the correct way to loop over the directory. */
				while (false !== ($file = readdir($handle))) {
					if ($file == '.' || $file == '..') {
						continue;
					} else {
						$full_name = $path . "/" . $file;
						if (is_dir($full_name)) {
							// Find info file
							$info_file = $path . "/" . $file . "/info.xml";
							$options_file = $path . "/" . $file . "/options.xml";
							$main_info = $path . "/" . $file . "/index.php";
							if (file_exists($info_file) && file_exists($main_info)) {
								// Setup?
								// This file should have any MySQL commands
								// necessary for the plugin to function.
								$setup_info = $path . "/" . $file . "/setup.php";
								if (file_exists($setup_info)) {
									include $setup_info;
								}
								// Load settings
								$xml = file_get_contents($info_file);
								// Installed?
								$q = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "widgets` WHERE `type`='5' AND `filename`='" . $file . "'";
								$found = $this->get_array($q);
								if ($found['0'] <= '0') {
									// Get elements from XML file
									$name = $this->get_xml_value('name',$xml);
									$desc = $this->get_xml_value('desc',$xml);
									$version = $this->get_xml_value('version',$xml);
									$developer = $this->get_xml_value('developer',$xml);
									$developer_url = $this->get_xml_value('developer_url',$xml);
									$img_location = $this->get_xml_value('img_location',$xml);
									$plugin_page = $this->get_xml_value('plugin_page',$xml);
									$screen_shot = $this->get_xml_value('screen_shot',$xml);
									// Options
									$options = array();
									$options['desc'] = $desc;
									$options['version'] = $version;
									$options['developer'] = $developer;
									$options['developer_url'] = $developer_url;
									$options['img_location'] = $img_location;
									$options['plugin_page'] = $plugin_page;
									$options['screen_shot'] = $screen_shot;
									$options = serialize($options);
									// Add DB entry
									$q1 = "INSERT INTO `" . TABLE_PREFIX . "widgets` (`date`,`plugin_name`,`owner`,`name`,`type`,`filename`,`active`,`options`) VALUES ('" . $this->current_date() . "','" . $this->mysql_clean($file) . "','$user','" . $this->mysql_clean($name) . "','5','" . $this->mysql_clean($file) . "','0','$options')";
									$insert = $this->insert($q1);
									// Options File
									$final_field_type = '';
									if (file_exists($options_file)) {
										$options_xml = file_get_contents($options_file);
										$options_array = $this->xml_to_array($options_xml);
										foreach ($options_array as $final_val) {
	   										if ($final_val['field']['type'] == 'text') { $final_field_type = '2'; }
	   										else if ($final_val['field']['type'] == 'select') { $final_field_type = '3'; }
	   										else if ($final_val['field']['type'] == 'yes_no') { $final_field_type = '1'; }
	   										else { $final_field_type = '2'; }
	   										$final_val['field']['selections'] = str_replace(',','|',$final_val['field']['selections']);
	   										$q = "INSERT INTO `" . TABLE_PREFIX . "options` (`type`,`key`,`value`,`display_name`,`description`,`fixed_selections`,`field_type`,`field_width`,`plugin`,`field_order`) VALUES ('3','" . $this->mysql_clean($final_val['ref']) . "','" . $this->mysql_clean($final_val['default_value']) . "','" . $this->mysql_clean($final_val['display_name']) . "','" . $this->mysql_clean($final_val['desc']) . "','" . $this->mysql_clean($final_val['field']['selections']) . "','" . $final_field_type . "','" . $this->mysql_clean($final_val['field']['width']) . "','" . $this->mysql_clean($file) . "','" . $this->mysql_clean($final_val['order']) . "')";
	   										$insert_options = $this->insert($q);
										}
									} // Options file exists?
								}
							} // Files exist?
						} // Directory?
					} // Not . or ..
				} // Loop directory
				closedir($handle);
			}
		}
		// Now get the existing plugins
 		$q = "SELECT * FROM `" . TABLE_PREFIX . "widgets` WHERE `type`='5' ORDER BY `active` DESC, `name` ASC";
		$results = $this->run_query($q);
		$theList = array();
 		while ($row = mysql_fetch_array($results)) {
 			$theList[] = $row;
 		}
 		return $theList;
 	}


	// ---------------------------------------------------------------------------
	// 	Rewrite global options file.
	
	function rewrite_global_options() {
		$globals = '<?php' . "\n";
		$q = "SELECT `id`,`key`,`group`,`value` FROM `" . TABLE_PREFIX . "options` WHERE `type`='1'";
		$result = $this->run_query($q);
		while ($row = mysql_fetch_array($result)) {
			if ($row['group'] == $_POST['set']) {
				$q = "UPDATE `" . TABLE_PREFIX . "options` SET `value`='" . $this->mysql_clean($_POST[$row['id']]) . "' WHERE `id`='" . $row['id'] . "' LIMIT 1";
				$update = $this->update($q);
				$globals .= '$GLOBALS["' . $row['key'] . '"]="' . addslashes($_POST[$row['id']]) . '";' . "\n";
			} else {
				$globals .= '$GLOBALS["' . $row['key'] . '"]="' . addslashes($row['value']) . '";' . "\n";
			}
		}
		// Add the theme
		$q = "SELECT `folder_name`,`type` FROM `" . TABLE_PREFIX . "themes` WHERE `selected`='1' AND `mobile`!='1'";
		$theTheme = $this->get_array($q);
		$globals .= '$GLOBALS["theme"]="' . addslashes($theTheme['folder_name']) . '";' . "\n";
		$q1 = "SELECT `folder_name` FROM `" . TABLE_PREFIX . "themes` WHERE `selected`='1' AND `mobile`='1'";
		$theMobileTheme = $this->get_array($q1);
		$globals .= '$GLOBALS["mobile_theme"]="' . addslashes($theMobileTheme['folder_name']) . '";' . "\n";
		$globals .= '?>';
		// Write it!
		$path = PATH . "/generated/globals.php";
		$this->write_file($path,$globals);
	}


	// ---------------------------------------------------------------------------
	// 	Get widgets
	
	function get_widget_list($type = "array") {
 		$q = "SELECT * FROM `" . TABLE_PREFIX . "widgets` ORDER BY `name` ASC";
		$results = $this->run_query($q);
		$theList = array();
 		while ($row = mysql_fetch_array($results)) {
 			$theList[] = $row;
 		}
 		return $theList;
 	}
	
	// ---------------------------------------------------------------------------
	// 	Get all existing templates
	
	function get_template_list($type = "array",$custom = "0") {
		// Get custom templates?
		if ($custom == "1") {
 			$q = "SELECT * FROM `" . TABLE_PREFIX . "templates` WHERE `custom`='1' ORDER BY `template` ASC";
		}
		// Or standard templates
		else {
 			$q = "SELECT `title`,`template`,`desc`,`id`,`status`,`format` FROM `" . TABLE_PREFIX . "templates` WHERE `custom`='0' GROUP BY `template` ORDER BY `template` ASC";
		}
		$results = $this->run_query($q);
		$unique = array();
 		while ($row = mysql_fetch_array($results)) {
 			$unique[] = $row;
 		}
 		return $unique;
 	}
 	
 	
	// ---------------------------------------------------------------------------
	// 	Get an existing templates
	
	function get_template($id,$custom = "0") {
		//if ($custom == "1") {
 		//	$q = "SELECT * FROM `" . TABLE_PREFIX . "templates` WHERE `id`='$id' AND `custom`='1' LIMIT 1";
 		//} else {
 		$q = "SELECT * FROM `" . TABLE_PREFIX . "templates` WHERE `id`='$id' LIMIT 1";
 		//}
		$result = $this->get_array($q);
 		return $result;
 	}
 	
 	
	// ---------------------------------------------------------------------------
	// 	Creates update and insert commands for editing
	//	and creating various things around the control panel.
	
 	function prepare_commands($post,$ignore,$table) {
 		$return = array();
 		$ignore[] = 'action';
		foreach ($post as $name => $value) {
			if ($name == "id") {
				$where = "`id`='" . $this->mysql_clean($value) . "'";
			}
			else if (in_array($name,$ignore)) {
				continue;
			}
			else {
		   		$update_q .= ",`$name`='" . $this->mysql_clean($value) . "'";
		   		$insert .= ",`$name`";
		   		$insert1 .= ",'" . $this->mysql_clean($value) . "'";
	   		}
		}
		$update_q = substr($update_q,1);
		$insert = substr($insert,1);
		$insert1 = substr($insert1,1);
		$return['where'] = $where;
		$return['update'] = $update_q;
		$return['insert'] = $insert;
		$return['insert1'] = $insert1;
		return $return;
 	}
 	
 	
	// ---------------------------------------------------------------------------
	// 	Formats caller tags available for a table.
 	
 	function format_caller_tags($intags,$textarea = "content") {
 		$standard_tags = array(
 			'%company%' => 'Company name.',
 			'%company_url%' => 'Company URL.',
 			'%program_url%' => 'Program URL.',
 			'%date%' => 'Current date.',
 		);
 		$send_tags = "<table cellspacing=0 callpadding=0 border=0 class=\"callers\"><thead><tr>";
 		$send_tags .= "<th>Caller Tag</th>\n";
 		$send_tags .= "<th>Description</th>\n";
 		$send_tags .= "</tr></thead><tbody>\n";
 		foreach ($standard_tags as $atag => $desc) {
 			$send_tags .= "<tr>\n";
 			$send_tags .= "<td valign=\"top\"><a href=\"#\" onClick=\"addCaller('$textarea','" . $atag . "');return false;\">" .  $atag . "</td>\n";
 			$send_tags .= "<td valign=\"top\">" . $desc . "</td>\n";
 			$send_tags .="</tr>\n";
 		}
 		$intags = trim($intags);
 		if (! empty($intags)) {
	 		$tags = explode("\n",$intags);
	 		foreach ($tags as $theTag) {
	 			$components = explode("||",$theTag);
	 			if (empty($components['1'])) {
	 				$components['1'] = "<i>N/A</i>";
	 			}
	 			$send_tags .= "<tr>\n";
	 			$send_tags .= "<td valign=\"top\"><a href=\"#\" onClick=\"addCaller('$textarea','" . $components['0'] . "');return false;\">" .  $components['0'] . "</td>\n";
	 			$send_tags .= "<td valign=\"top\">" . $components['1'] . "</td>\n";
	 			$send_tags .="</tr>\n";
	 		}
 		}
 		$send_tags .= "</tbody></table>";
 		return $send_tags;
 	}
	
	
	// ---------------------------------------------------------------------------
	// 	List users on the admin CP
	
	function list_results($query) {
		$result = $this->run_query($query);
		// Get users information
		$final_results = array();
 		while ($row = mysql_fetch_array($result)) {
    			$final_results[] = $row['id'];
    			$results++;
    		}
    		return $final_results;
	}
	
}

?>