<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Primary article/page-related functions.
	
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


class manual extends template {

	// ---------------------------------------------------------------------------
	// 	Get an article's details
	
	function get_article($id,$skip_comments = "0",$select = '*',$get_tags = '0',$get_related = '0',$get_user_data = '1') {
		// Primary
		if (empty($select)) {
			$select = '*';
		}
		// Clean the input
		$clean_id = $this->mysql_clean($id);
		// Get details
		$q = "SELECT $select FROM `" . TABLE_PREFIX . "articles` WHERE `id`='" . $clean_id . "' LIMIT 1";
		$article = $this->get_array($q);
		// Follows + Favorites
		$q2 = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "following` WHERE `article`='" . $clean_id . "'";
		$follows = $this->get_array($q2);
		$q3 = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "favorites` WHERE `article`='" . $clean_id . "'";
		$favs = $this->get_array($q3);
		$article['favorited'] = $favs['0'];
		$article['following'] = $follows['0'];
		// Last Updated
		if (empty($article['last_updated_by'])) {
			$article['last_updated_by'] = $article['owner'];
		}
		// Category name
		$category_info = $this->get_category($article['category'],'0','name');
		if (empty($category_info['name'])) {
			$category_info['name'] = lg_home;
		}
		$article['category_name'] = $category_info['name'];
		// Creator details
		if ($get_user_data == '1') {
			$creator_id = $this->get_user_id($article['owner']);
			$article['creator_picture'] = $this->get_profile_pic($creator_id);
			$article['creator_thumbnail'] = $this->get_profile_thumb($creator_id);
			$article['creator_panel'] = $this->render_template('user_panel',$article['owner'],'','1','1','');
		}
		// Options
		//$article_options = $this->get_item_options($id,'article',$article['category']);
		//$query = "SELECT `key`,`value` FROM `" . TABLE_PREFIX . "item_options` WHERE `act_id`='" . $this->mysql_clean($id) . "' AND `type`='article'";
		//$info = $this->get_assoc_array($query);
		//$article = @array_merge($article,$article_options);
		// Count comments?
		if ($skip_comments != "1") {
			$q1 = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "comments` WHERE `article`='" . $clean_id . "' AND `pending`!='1' LIMIT 1";
			$count = $this->get_array($q1);
			$article['comments'] = $count['0'];
		}
		// Tags
		if ($get_tags == '1') {
			$tags = $this->get_article_tags($clean_id,'list');
			$tag_list = "<ul id=\"page_tags\">" . $tags . "</ul>";
			$article['tags'] = $tag_list;
		}
		// Related
		// Based on tags
		if ($get_related == '1') {
			$related = $this->get_related_pages($clean_id,'list');
			$related_list = "<ul id=\"related_pages\">" . $related . "</ul>";
			$article['related'] = $related_list;
		}
		
		return $article;
	}
	
	// ---------------------------------------------------------------------------
	// 	Get a page's tags
	//	ID should be pre-sanitized
	//	array or list
	
	function get_article_tags($id,$type = 'array') {
		$send_tags = array();
		$list_tags = '';
		$q = "SELECT `tag` FROM `" . TABLE_PREFIX . "article_tags` WHERE `page_id`='" . $id . "' ORDER BY `tag` ASC";
		$results = $this->run_query($q);
		while ($row = mysql_fetch_array($results)) {
			if ($type == 'array') {
				$send_tags[] = $row['tag'];
			}
			else if ($type == 'list') {
				$search_tag = urlencode('#' . $row['tag']);
				$list_tags .= "<li><a href=\"" . URL . "/search.php?q=" . $search_tag . "\">" . $row['tag'] . "</a></li>";
			}
		}
		if ($type == 'array') {
			return $send_tags;
		} else {
			if (empty($list_tags)) {
				$list_tags = "<li class=\"none\">" . lg_no_tags_found . "</li>";
			}
			return $list_tags;
		}
	}
	
	// ---------------------------------------------------------------------------
	// 	Related Pages
	//	Gets pages related to a page
	//	based on that page's tags.
	
	function get_related_pages($id,$type = 'array') {
		$add_where = '';
		$send = array();
		$list = '';
		$tags = $this->get_article_tags($id);
		if (! empty($tags)) {
			foreach ($tags as $tag) {
				$add_where .= " OR `tag`='$tag'";
			}
			$add_where = ltrim($add_where,' OR ');
			$q = "SELECT `page_id` FROM `" . TABLE_PREFIX . "article_tags` WHERE `page_id`!='$id' AND ($add_where) GROUP BY `page_id` LIMIT 8";
			$results = $this->run_query($q);
			while ($row = mysql_fetch_array($results)) {
				if ($type == 'array') {
					$send[] = $row['page_id'];
				}
				else if ($type == 'list') {
					$page_info = $this->get_article($row['page_id'],'1','name,category','0','0','0');
					$page_link = $this->prepare_link($row['page_id'],$page_info['category'],$page_info['name']);
					if (! empty($page_info['name'])) {
						$list .= "<li><a href=\"$page_link\">" . $page_info['name'] . "</a></li>";
					}
				}
			}
		}
		if ($type == 'array') {
			return $send;
		} else {
			if (empty($list)) {
				$list = "<li class=\"none\">" . lg_no_related_found . "</li>";
			}
			return $list;
		}
	}
	
	
	// ---------------------------------------------------------------------------
	// 	Get a category's details
	
	function get_category($id,$count_articles = '0',$select = '*') {
		if ($id == 'base' || empty($id)) {
   			$q = "SELECT $select FROM `" . TABLE_PREFIX . "categories` WHERE `base`='1' LIMIT 1";
		} else {
   			$q = "SELECT $select FROM `" . TABLE_PREFIX . "categories` WHERE `id`='" . $this->mysql_clean($id) . "' LIMIT 1";
		}
   		// $q = "SELECT $select FROM `" . TABLE_PREFIX . "categories` WHERE `id`='" . $this->mysql_clean($id) . "' LIMIT 1";
   		$category = $this->get_array($q);
   		if ($count_articles == "1") {
   			$count = $this->pages_in_category($category['id']);
   			$category['articles'] = $count['total'];
   		}
		return $category;
	}
	
	// ---------------------------------------------------------------------------
	// 	Total pages in a category
	
	function pages_in_category($id) {
		// Total
 		if ($id == 'base') {
 			$final_id = '0';
 		} else {
 			$final_id = $id;
   		}
   		$q1 = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "articles` WHERE `category`='" . $this->mysql_clean($final_id) . "' LIMIT 1";
   		$count = $this->get_array($q1);
   		// Order
   		$q2 = "SELECT `order` FROM `" . TABLE_PREFIX . "articles` WHERE `category`='" . $this->mysql_clean($final_id) . "' ORDER BY `order` DESC LIMIT 1";
   		$order = $this->get_array($q2);
   		// Reply
   		$array = array();
   		$array['total'] = $count['0'];
   		$array['order'] = $order['order'];
   		$array['next_order'] = $order['order']+1;
   		return $array;
	}
	


	// ---------------------------------------------------------------------------
	//   Is this website public?
	//   Determined by checking the base categories
	//   privacy settings
	
	function check_public() {
		$q = "SELECT `public` FROM `" . TABLE_PREFIX . "categories` WHERE `base`='1' LIMIT 1";
		$public = $this->get_array($q);
		return $public['public'];
	}
	

	// ---------------------------------------------------------------------------
	//   Get the ID of the base category
	
	function base_category_id() {
		$q = "SELECT `id` FROM `" . TABLE_PREFIX . "categories` WHERE `base`='1' LIMIT 1";
		$bcate = $this->get_array($q);
		return $bcate['id'];
	}
	
	// ---------------------------------------------------------------------------
	// 	Create a category tree for the sidebar.
	//	This will loop through the categories,
	//	while the list_articles function gets
	//	the articles and sub-categories.
	
	function category_tree($active,$override_list = '0',$cache_category_list = '0',$widget = '0',$widget_list_categories = '1',$widget_clause = '`order` ASC',$widget_cols = '1') {
		global $privileges;
		global $user;
		global $user_data;
		$file = PATH . "/generated/categories.php";
		// Cache category list
		if ($cache_category_list == '1' && $override_list != '1' && $widget != '1') {
   			if (file_exists($file)) {
				ob_start();
				include($file);
				$contents = ob_get_contents();
				ob_end_clean();
				return $contents;
   			} else {
   				$tree = $this->category_tree($active,'1');
   				$this->write_file($file,$tree);
   				return $tree;
   			}
		}
		// Generate list from DB
		else {
			// Direct linking?
			$direct_linking = $this->get_option('direct_link');
			// Public site?
			$public_site = $this->check_public();
			if ($public_site == '0' && empty($user)) {
				return '';
			}
			// Continue
			$list = '';
			$articles_hidden = '';
			$kill_widget_cols = '';
			$url_display_type = $this->get_option('url_display_type');
			if ($widget == '1') {
				$class = "bd_widget_ul";
				$wid_class = "bd_widget_ul_primary";
				$final_class_primary_ul = " class=\"bd_widget_ul bd_widget_ul_primary\"";
				$subcat = $active;
				// Does this widget have columns?
				if ($widget_cols > 1) {
					$kill_widget_cols = '1';
					$col_width = floor((100) / $widget_cols);
					$col_width -= 1;
					$list .= "<style type=\"text/css\"><!--\n";
					$list .= ".bd_widget_ul {\n";
					$list .= "	float: left;\n";
					$list .= "	width: " . $col_width . "%;\n";
					$list .= "}\n";
					$list .= ".bd_widget_ul_primary {\n";
					$list .= "	float: none;\n";
					$list .= "}\n";
					$list .= "--></style>\n";
				}
			} else {
				$subcat = '0';
				$class = "";
				$final_class_primary_ul = "";
				$wid_class = "";
			}
			$q = "SELECT * FROM `" . TABLE_PREFIX . "categories` WHERE `subcat`='$subcat' AND `base`!='1' ORDER BY `order` ASC";
			$result = $this->run_query($q);
			$list .= "<ul$final_class_primary_ul id=\"articles0\"";
			if ($active != "0" && $widget != '1') {
				$list .= " style=\"display:none;\"";
			}
			$list .= ">\n";
			$current_widget_loop = 1;
		 	$found_categories = '0';
		 	$theCates = '';
		 	$show = '0';
		 	$private = '';
			// Loop through the primary "base" categories
			if ($widget_list_categories == '1') {
		 		while ($row = mysql_fetch_array($result)) {
		 			$found_categories = '1';
		 			// Here we determine whether there are multiple
		 			// articles in a category or just one. If there
		 			// is only one, we link directly to it. Otherwise
		 			// we display the options.
		 			if ($widget != '1') {
			 			// Private?
						if ($row['public'] == '0' || $row['public'] == '2' || $row['public'] == '3') {
				 			if ($privileges['is_admin'] == '1' || $privileges['can_view_private'] == '1') {
								$private = "<img src=\"" . URL . "/templates/html/_imgs/private.png\" width=\"16\" height=\"16\" alt=\"This category is private.\" title=\"This category is private.\" border=\"0\" style=\"vertical-align:middle;padding-left:4px;\" />";
								$show = '1';
							} else {
								if (empty($user)) {
									$show = '0';
								} else {
									$hasAccess = $this->user_permissions('',$user_data['id'],$user_data['type'],'0',$row['id']);
									if ($hasAccess == '1') {
										$show = '1';
									} else {
										$show = '0';
									}
								}
							}
						} else {
							$show = '1';
							$private = '';
						}
						// Create the link
						if ($show == '1') {
							if ($direct_linking == '1') {
				   				if (! empty($row['home_article'])) {
					   				$q1 = "SELECT `id`,`name` FROM `" . TABLE_PREFIX . "articles` WHERE `id`='" . $row['home_article'] . "'";
					   				$single_article_details = $this->get_array($q1);
					   				$link = $this->prepare_link($single_article_details['id'],$row['id'],$single_article_details['name']);
				   				} else {
					   				$link = $this->prepare_link('',$row['id'],'');
				   				}
								//$q1 = "SELECT `id`,`name` FROM `" . TABLE_PREFIX . "articles` WHERE `category`='" . $row['id'] . "'";
								//$single_article_details = $this->get_array($q1);
								//$link = $this->prepare_link($single_article_details['id'],$row['id'],$single_article_details['name']);
				 				$theCates .= "<li class=\"category_entry\"><a href=\"$link\">" . $row['name'] . "</a>$private</li>\n";
							} else {
								if (! empty($row['home_article'])) {
									$q1 = "SELECT `id`,`name` FROM `" . TABLE_PREFIX . "articles` WHERE `id`='" . $row['home_article'] . "' LIMIT 1";
									$single_article_details = $this->get_array($q1);
									$link = $this->prepare_link($single_article_details['id'],$row['id'],$single_article_details['name']);
					 				$theCates .= "<li class=\"category_entry\"><a href=\"$link\">" . $row['name'] . "</a>$private</li>";
								} else {
				 					$theCates .= "<li class=\"category_entry\"><a href=\"#\" onclick=\"return expandCategory('" . $row['id'] . "');\">" . $row['name'] . "</a>$private</li>\n";
								}
							}
							/*
				 			if (empty($row['home_article']) && $direct_linking == '1') {
								$q1 = "SELECT `id`,`name` FROM `" . TABLE_PREFIX . "articles` WHERE `category`='" . $row['id'] . "'";
								$single_article_details = $this->get_array($q1);
								$link = $this->prepare_link($single_article_details['id'],$row['id'],$single_article_details['name']);
				 				$theCates .= "<li class=\"category_entry\"><a href=\"$link\">" . $row['name'] . "</a>$private</li>\n";
				 			} else {
				 				if (! empty($row['home_article']) && $direct_linking == '1') {
									$q1 = "SELECT `id`,`name` FROM `" . TABLE_PREFIX . "articles` WHERE `id`='" . $row['home_article'] . "' LIMIT 1";
									$single_article_details = $this->get_array($q1);
									$link = $this->prepare_link($single_article_details['id'],$row['id'],$single_article_details['name']);
					 				$theCates .= "<li class=\"category_entry\"><a href=\"$link\">" . $row['name'] . "</a>$private</li>";
				 				} else {
				 					$theCates .= "<li class=\"category_entry\"><a href=\"#\" onclick=\"expandCategory('" . $row['id'] . "');return false;\">" . $row['name'] . "</a>$private</li>\n";
				 				}
				 			}
				 			*/
			 			}
		 			}
		 			// If the active category isn't "base",
		 			// display that category rather than the
		 			// "base" category.
		 			if ($row['id'] == $active && $active != "0") {
		 				// First get sub-categories within
		 				// this category.
		 				/*
						$q1 = "SELECT id,name FROM `" . TABLE_PREFIX . "categories` WHERE `subcat`='" . $row['id'] . "' ORDER BY `order` ASC";
						$subcategories = $this->run_query($q1);
						while ($rowA = mysql_fetch_array($subcategories)) {
		 					$list .= "<li class=\"sub_link\"><a href=\"#\" onclick=\"originalMenu();return false;\">" . $rowA['name'] . "</a></li>";
			 				$get_articles = $this->list_articles($rowA['id']);
			 				$articles_hidden .= $get_articles;
		 				}
		 				*/
		 				// Append the article titles for this category
		 				$get_articles = $this->list_articles($row['id'],'1','0',$active,$url_display_type,$widget,$widget_list_categories,$widget_clause,$widget_cols);
		 			}
		 			// Here we get other category data without
		 			// displaying it.
		 			else {
		 				$get_articles = $this->list_articles($row['id'],'','',$active,$url_display_type,$widget,$widget_list_categories,$widget_clause,$widget_cols);
		 			}
		 			$articles_hidden .= $get_articles;
		 			
		 			// For widgets with columns.
		 			if ($kill_widget_cols == '1' && $current_widget_loop == 3) {
		 				$articles_hidden .= "<div style=\"clear:both;\"></div>";
		 				$current_widget_loop = 0;
		 			}
		 			$current_widget_loop++;
		 		}
	 		}
			$theCates .= "<li class=\"category_separator\"></li>\n";
	 		if ($kill_widget_cols == '1' && $current_widget_loop != '1') {
	 			$articles_hidden .= "<div style=\"clear:both;\"></div>";
	 		}
	 		// Categories found?
	 		if ($found_categories > 0) {
				$list .= "<li class=\"categories_heading\">" . lg_subcates . "</li>\n";
				$list .= $theCates;
	 		}
	 		// Add base articles to the list
			// Articles
			$add_where = '';
			if ($privileges['can_view_private'] != '1' && $privileges['is_admin'] != '1') {
				$add_where .= " AND `public`='1' AND `display_on_sidebar`='1'";
			} else {
				$add_where .= "";
			}
			if (! empty($widget_clause) && $widget == '1') {
				$q = "SELECT `id`,`name`,`category`,`public` FROM `" . TABLE_PREFIX . "articles` WHERE `category`='$active'$add_where $widget_clause";
			} else {
				$q = "SELECT `id`,`name`,`category`,`public` FROM `" . TABLE_PREFIX . "articles` WHERE `category`='0'$add_where ORDER BY `order` ASC";
			}
			//echo $q;
			
			$result = $this->run_query($q);
	 		$found_sublink = '0';
			$sublinks = '';
	 		while ($row = @mysql_fetch_array($result)) {
	 			$found_sublink = '1';
				$private = '';
				$link = $this->prepare_link($row['id'],$row['category'],$row['name']);
				if ($row['public'] != '1') {
					$private = "<img src=\"" . URL . "/templates/html/_imgs/private.png\" width=\"16\" height=\"16\" alt=\"This page is not public.\" title=\"This page is not public.\" border=\"0\" style=\"vertical-align:middle;padding-left:4px;\" />";
				} else {
					$private = '';
				}
				if (BD_ARTICLE_VIEWING == $row['id']) {
					$sublinks .= "<li class=\"sub_link on\"><a href=\"$link\">" . $row['name'] . "</a>$private</li>\n";
				} else {
					$sublinks .= "<li class=\"sub_link\"><a href=\"$link\">" . $row['name'] . "</a>$private</li>\n";
				}
	 		}
	 		if ($found_sublink > 0) {
				$list .= "<li class=\"articles_heading\">" . lg_sublinks . "</li>\n";
				$list .= $sublinks;
	 		} else {
	 			// $list = '';
	 		}
	 		$list .= "</ul>\n";
	 		$list .= $articles_hidden;
	 		// Cache category list
	 		// We can only get to this spot in the code
	 		// if caching is on but the file doesn't
	 		// exist.
	 		if ($cache_category_list == '1') {
				$fp = fopen($file, 'w');
				fwrite($fp, $list);
				fclose($fp);
	 		}
			return $list;
		}
	}
	
	
	// ---------------------------------------------------------------------------
	// 	Lists articles and sub-categories within a
	//	category on the sidebar navigation.
	
	function list_articles($category,$show = "0",$previous_cate = "0",$active = "",$url_display_type = "id",$widget = '0',$level = '0',$widget_crap = '',$widget_cols = '0') {
		global $privileges;
		$direct_linking = $this->get_option('direct_link');
		$category_info = $this->get_category($category);
   		$total_nest = $this->category_nest_level($category);
		// Widget considerations
		if ($widget == '1') {
			$level_pad = (25) * $total_nest;
			$level_pad .= "px";
				$class = " class=\"bd_widget_ul\"";
			if ($widget_cols <= 0) {
				$class .= " style=\"margin-left:$level_pad;\"";
			}
		}
		// Subcategories?
		$subcategories = '';
		$subcate_ul = '';
   		$q1 = "SELECT `id`,`name`,`subcat`,`home_article` FROM `" . TABLE_PREFIX . "categories` WHERE `subcat`='" . $category . "' AND `base`!='1'  ORDER BY `order` ASC";
   		$subcatefind = $this->run_query($q1);
   		while ($rowA = mysql_fetch_array($subcatefind)) {
			if ($category_info['subcat'] != $previous_cate) {
   				$level++;
   			}
   			$subcate_ul .= $this->list_articles($rowA['id'],'',$category,$active,$url_display_type,$widget,$level);
    			// Here we determine whether there are multiple
    			// articles in a category or just one. If there
    			// is only one, we link directly to it. Otherwise
    			// we display the options.
   			if ($direct_linking == '1') {
   				if (! empty($rowA['home_article'])) {
	   				$q1 = "SELECT `id`,`name` FROM `" . TABLE_PREFIX . "articles` WHERE `id`='" . $rowA['home_article'] . "'";
	   				$single_article_details = $this->get_array($q1);
	   				$link = $this->prepare_link($single_article_details['id'],$rowA['id'],$single_article_details['name']);
   				} else {
	   				$link = $this->prepare_link('',$rowA['id'],'');
   				}
   				//$q1 = "SELECT `id`,`name` FROM `" . TABLE_PREFIX . "articles` WHERE `category`='" . $rowA['id'] . "'";
   				//$single_article_details = $this->get_array($q1);
   				//$link = $this->prepare_link($single_article_details['id'],$rowA['id'],$single_article_details['name']);
    				$subcategories .= "<li class=\"category_entry\"><a href=\"$link\">" . $rowA['name'] . "</a></li>\n";
   			} else {
   				if (! empty($rowA['home_article'])) {
   					$q1 = "SELECT `id`,`name` FROM `" . TABLE_PREFIX . "articles` WHERE `id`='" . $rowA['home_article'] . "' LIMIT 1";
   					$single_article_details = $this->get_array($q1);
   					$link = $this->prepare_link($single_article_details['id'],$rowA['id'],$single_article_details['name']);
   	 				$subcategories .= "<li class=\"category_entry\"><a href=\"$link\">" . $rowA['name'] . "</a></li>\n";
   				} else {
    					$subcategories .= "<li class=\"category_entry\"><a href=\"#\" onclick=\"return expandCategory('" . $rowA['id'] . "');\">" . $rowA['name'] . "</a></li>\n";
   				}
   			}
		}
		// Begin the list
		if ($active == $category) {
			$show = '1';
		}
		if ($widget == '1') {
			$list = "\n<ul id=\"articles" . $category . "\" $class>\n";
		} else {
			$list = "\n<ul id=\"articles" . $category . "\" style=\"";
			if ($show == "1") {
				$list .= "display:block;\"";
			} else {
				$list .= "display:none;\"";
			}
			$list .= ">\n";
		}
		// This category li
		if (! empty($category_info['home_article'])) {
			$link = $this->prepare_link('',$category_info['id'],'');
			$list .= "<li class=\"category_name\"><a href=\"$link\">" . $category_info['name'] . "</a></li>";
		} else {
			$list .= "<li class=\"category_name\">" . $category_info['name'] . "</li>";
		}
		$list .= "<li class=\"category_separator\"></li>\n";
		if (! empty($subcategories)) {
			$list .= $subcategories;
			$list .= "<li class=\"category_separator\"></li>\n";
		}
		// Articles
		$add_where = '';
		if ($privileges['can_view_private'] != '1') {
			$add_where .= " AND `public`='1'";
		}
   		if (! empty($widget_clause) && $widget == '1') {
   			$add_where .= " AND " . $where_clause;
   		}
		$q = "SELECT `id`,`name`,`category`,`public` FROM `" . TABLE_PREFIX . "articles` WHERE `category`='$category' AND `display_on_sidebar`='1'$add_where ORDER BY `order` ASC";
		$result = $this->run_query($q);
 		while ($row = mysql_fetch_array($result)) {
			$private = '';
			$link = $this->prepare_link($row['id'],$row['category'],$row['name']);
			if ($row['public'] != '1') {
				$private = "<img src=\"" . URL . "/templates/html/_imgs/private.png\" width=\"16\" height=\"16\" alt=\"This page is not public.\" title=\"This page is not public.\" border=\"0\" style=\"vertical-align:middle;padding-left:4px;\" />";
			} else {
				$private = '';
			}
			if (BD_ARTICLE_VIEWING == $row['id']) {
 				$list .= "<li class=\"sub_link on\"><a href=\"$link\">" . $row['name'] . "</a>$private</li>\n";
			} else {
 				$list .= "<li class=\"sub_link\"><a href=\"$link\">" . $row['name'] . "</a>$private</li>\n";
 			}
 		}
		if ($widget != '1') {
   			if ($direct_linking == '1') {
				$link = $this->prepare_link('',$previous_cate,'');
				$list .= "<li><a href=\"$link\">&laquo; " . lg_back . "</a></li>\n";
   			} else {
				$list .= "<li><a href=\"#\" onclick=\"return originalMenu('$previous_cate');\">&laquo; " . lg_back . "</a></li>\n";
			}
		}
 		$list .= "</ul>\n";
		$list .= $subcate_ul;
		return $list;
	}
	
	
	// ---------------------------------------------------------------------------
	// 	Get a page's title and makes it
	//	SEO-friendly
	
	function get_page_title($crumbs,$delimiter = ' / ') {
		$crumbs = strip_tags($crumbs);
		$site_name = $this->get_option('site_name');
		if (! empty($site_name)) {
			$page_title = $site_name . $delimiter;
		}
		
		$page_title .= $crumbs . " | " . $this->get_option('company_name');
		$len = strlen($page_title);
		if ($len > 70) {
			$need_to_remove = $len - 70 + strlen($delimiter);
			$temp_pt = strlen($crumbs);
			// $crumbs = ltrim($crumbs,$delimiter);
			$exp_crumbs = @explode($delimiter,$crumbs);
			/*
			$done = '0';
			while ($done != '1') {
				$cut_off_first = @array_shift($exp_crumbs);
				$final_output = @implode($delimiter,$exp_crumbs);
				$new_len = strlen($final_output);
				$dif = $temp_pt - $new_len;
				if ($dif > $need_to_remove) {
					if (! empty($site_name)) {
						$page_title = $site_name . $delimiter;
					}
					$page_title .= $final_output . " | " . $this->get_option('company_name');
					$done = '1';
				}
			}
			*/
			$sizeof = sizeof($exp_crumbs)-1;
			$page_title = $exp_crumbs[$sizeof] . " | " . $this->get_option('company_name');
			
		}
		// Remove start junk
		//global $divider;
		//if (substr($page_title, 0, strlen($divider)) == $divider) {
		//	$page_title = substr($page_title, strlen($divider), strlen($page_title));
		//} 
		return $page_title;
	}
	
	
	// ---------------------------------------------------------------------------
	// 	Creates a breadcrumb trail for articles
	//	$id = category ID
	//	$article = article ID
	//	$skip_article = boolean
	
	function breadcrumbs($id,$article = '',$article_info_in = '',$skip_article = '0',$additional = '') {
		global $divider;
		// Add the home
		$cate_home = $this->get_category('base','0','name');
		$add_to_crumbs = "<a href=\"" . URL . "\">" . $cate_home['name'] . "</a>";
		// Base category?
		if ($id != "0") {
			$found_it = "0";
			$next_category = $id;
			$add_safety = 0;
			$theCates = array();
			while ($found_it == "0" && $add_safety < 50) {
				$add_safety++;
				$category_info = $this->get_category($next_category,'0','subcat,home_article,name,id');
				if ($category_info['subcat'] != "0") {
					$next_category = $category_info['subcat'];
					if (! empty($category_info['home_article'])) {
						$link = $this->prepare_link('',$category_info['id'],'');
						$theCates[] .= $divider . "<a href=\"$link\">" . $category_info['name'] . "</a>";
					} else {
						$theCates[] .= $divider . "<a href=\"#\">" . $category_info['name'] . "</a>";
					}
				} else {
					if (! empty($category_info['home_article'])) {
						$link = $this->prepare_link('',$category_info['id'],'');
						$theCates[] .= $divider . "<a href=\"$link\">" . $category_info['name'] . "</a>";
					} else {
						$theCates[] .= $divider . "<a href=\"#\">" . $category_info['name'] . "</a>";
					}
					$found_it = "1";
					break;
				}
			}
			$theCates = array_reverse($theCates);
			foreach ($theCates as $cate) {
				$add_to_crumbs .= $cate;
			}
		}
		// Adding page to the list?
   		if ($skip_article != '1' && ! empty($article)) {
   			// Do we have the page's information?
			if (! empty($article_info_in)) {
				$article_info = $article_info_in;
			} else {
				if ($skip_article != '1') {
					$article_info = $this->get_article($article,'0','name,category,id','0','0','0');
				}
			}
   			// Create the page's link
   			if (! empty($article_info['name'])) {
	   			$link = $this->prepare_link($article_info['id'],$article_info['category'],$article_info['name']);
				$add_to_crumbs .= $divider . "<a href=\"$link\">" . $article_info['name'] . "</a>";
			}
   		}
   		// Array of additional items to add
   		// onto the end of the trail. Used
   		// on user pages.
   		if (! empty($additional)) {
   			foreach ($additional as $item) {
   				$add_to_crumbs .= $divider;
   				$add_to_crumbs .= $item;
   			}
   		}
   		// Remove trailing junk
   		// $add_to_crumbs = substr($add_to_crumbs, 0, strrpos($add_to_crumbs, $divider)+1);
   		return $add_to_crumbs;
	}
	
	// ---------------------------------------------------------------------------
	// 	Prepares an article's link
	
	function prepare_link($article_id,$category_id = "",$article_name = "",$highlight_text = "",$article_info = '') {
		$url_display_type = $this->get_option('url_display_type');
		// Article but no category?
		// Or no article name?
		if (! empty($article_id)) {
   			if (empty($category_id) && $category_id != '0') {
   				if (empty($article_info)) {
   					$article_info = $this->get_article($article_id,'1','name,category','0','0','0');
   				}
   				if (empty($category_id)) {
   					$category_id = $article_info['category'];
   				}
   				if (empty($article_name)) {
   					$article_name = $article_info['name'];
   				}
   			}
		}
		// Now prepare the link
		if ($url_display_type == "Name") {
	 		$cate_name = urlencode($this->get_category_name_from_id($category_id));
			if (empty($category_id)) {
				$cate_name = "Home";
			}
			if (empty($article_id)) {
				// $link = URL . "/page/" . $cate_name . "/";
				$link = URL . "/" . $this->urlencodeclean($cate_name) . "/";
			} else {
				$article_name = urlencode($article_name);
				// $link = URL . "/page/" . $cate_name . "/" . urlencode($article_name);
				$link = URL . "/" . $this->urlencodeclean($cate_name) . "/" . $this->urlencodeclean($article_name);
			}
		} else {
			$link = URL . "/index.php?category=" . $category_id . "&id=" . $article_id;
			if (! empty($highlight_text)) {
				$link .= "&highlight=" . $highlight_text;
			}
			/*
			if (empty($category_id)) {
				$category_id = "0";
			}
			if (empty($article_id)) {
				// $link = URL . "/page/" . $category_id . "/";
				$link = URL . "/" . $category_id . "/";
			} else {
				$article_name = urlencode($article_name);
				// $link = URL . "/page/" . $category_id . "/" . $article_id;
				$link = URL . "/" . $category_id . "/" . $article_id;
			}
			*/
		}
		if (! empty($highlight_text)) {
			$link .= "/" . $this->urlencodeclean($highlight_text);
		}
		return $link;
	}
	
	
	// ---------------------------------------------------------------------------
	// 	Sanitize a name to make it
	//	work with the standard structure
	
	function sanitize_name($name) {
		$name = str_replace('-','&#8211;',$name);
		$name = trim($name);
		return $name;
	}
	
	
	// ---------------------------------------------------------------------------
	// 	Get revision
	
	function get_revision($id) {
		$q = "SELECT * FROM `" . TABLE_PREFIX . "articles_history` WHERE `id`='" . $this->mysql_clean($id) . "' LIMIT 1";
		$revision = $this->get_array($q);
		return $revision;
	}
	
	// ---------------------------------------------------------------------------
	// 	Get last revision of a page
	
	function get_last_revision($id) {
		$q = "SELECT * FROM `" . TABLE_PREFIX . "articles_history` WHERE `article_id`='" . $this->mysql_clean($id) . "' ORDER BY `date` DESC LIMIT 1";
		$revision = $this->get_array($q);
		return $revision;
	}
	
	
	// ---------------------------------------------------------------------------
	// 	Create a page
	
	function create_page($article,$category,$special_changes,$template_custom = '') {
		global $user;
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
		$display_everything = $this->render_template('article',$user,$special_changes,'0','0','',$template_custom,'1');
		return $display_everything;
	}
	
	
	// ---------------------------------------------------------------------------
	// 	Renders and formats an article
	//	$article is an array of the database row.
	
	function format_article($article,$input,$user = "",$highlight_text = "",$just_article = '0',$widget = '0',$force_preview = '0',$skip_widgets = '0',$incoming = '') {
		global $privileges;
		global $theme;
		// Image Functions
		require_once 'image.functions.php';
		$image = new image;
		// User
		if (empty($user)) {
			global $user;
		}
		// Category
		$category = $this->get_category($article['category']);
	    	$format_created = $this->format_date($article['created']);
	    	$format_last_updated = $this->format_date($article['last_updated']);
	    	$theme_folder = URL . "/templates/html/" . $theme;
	    	// Establish the input text
	   	$final_lines = '';
		if (empty($input)) {
			$input = $article['content'];
		}
		// -----------------------------------------------------------------------------------
		//	Full HTML formatting
		
		$headers = array();
		$footnotes_array = array();
			
		if ($article['format_type'] == '2') {
			
			$input = $this->get_php_code($input);
			
	   		$temp_lines = explode("\n",$input);
	   		// Text align considerations
	   		foreach ($temp_lines as $line) {
				// ------------------------------
				// 	Widgets
	   	    		$line = $this->find_widgets($line,$skip_widgets);
   		    		// ------------------------------
   		    		// 	Attachments
   		    		$line = $this->find_attachments($line);
   		    		
				$line = $this->clickable_tags($line,'2');
	   			$final_lines .= $line;
   	    		}
			
			$final_article = $final_lines;

			// Headers

      		// ------------------------------------------------------------------------
   	   		// 	h1 headers
   	   		preg_match_all('/<h1>(.*?)<\/h1>/', $line, $matches);
   	   		foreach ($matches['0'] as $match) {
   	   			$no_paragraph = "1";
   	   			$match_temp = str_replace('<h1>','',$match);
   	   			$match_temp = str_replace('</h1>','',$match_temp);
   	   			$match_temp_heading = $match_temp;
   	   			$headers[] = '1+++' . $match_temp_heading;
   	   		}
   	   		
   	    		// ------------------------------
   	   		// 	h2 headers
   	   		preg_match_all('/\<h2\>(.*?)\<\/h2\>/', $line, $matches);
   	   		foreach ($matches['0'] as $match) {
   	   			$no_paragraph = "1";
   	   			$match_temp = str_replace('<h2>','',$match);
   	   			$match_temp = str_replace('</h2>','',$match_temp);
   	   			$match_temp_heading = $match_temp;
   	   			$headers[] = '2+++' . $match_temp_heading;
   	   		}
   	   		
   	    		// ------------------------------
   	   		// 	h3 headers
   	   		preg_match_all('/\<h3\>(.*?)\<\/h3\>/', $line, $matches);
   	   		foreach ($matches['0'] as $match) {
   	   			$no_paragraph = "1";
   	   			$match_temp = str_replace('<h3>','',$match);
   	   			$match_temp = str_replace('</h3>','',$match_temp);
   	   			$match_temp_heading = $match_temp;
   	   			$headers[] = '3+++' . $match_temp_heading;
   	   		}
			
		  //  	$final_article = str_replace('&#43;&#43;&#43;','+++',$value);
		    		
			// Used for full CMS mode previewing
			if ($force_preview == '1') {
				$special_changes = array(
					'%formatted_article%' => $final_article,
				);
				$final_article = $this->create_page($article,$category,$special_changes);
			}
			
		}
		// -----------------------------------------------------------------------------------
		//	Wiki-formatting
		else {
	    		// Lists & Other
	    		$find = array(
	    			'[left]',
	    			'[/left]',
	    			'[right]',
	    			'[/right]',
	    			'[big]',
	    			'[/big]',
	    			'[sup]',
	    			'[/sup]',
	    			'[sub]',
	    			'[/sub]',
	    			'[small]',
	    			'[/small]',
	    			'[quote]',
	    			'[/quote]',
	    			'[highlight]',
	    			'[/highlight]',
	    			'[strike]',
	    			'[/strike]',
	    			'====',
	    			//'^^^',
	    			//'^^',
	    			//'|||',
	    			//'||',
	    			'%created%',
	    			'%last_updated%',
	    			'%views%',
	    			'%creator%',
	    			'\\\\'
	    		);
	    		$replace = array(
	    			'<div class="bd_left_menu"><div class="bd_left_menu_pad">',
	    			'</div></div>',
	    			'<div class="bd_right_menu"><div class="bd_right_menu_pad">',
	    			'</div></div>',
	    			'<span style="font-size:120%;">',
	    			'</span>',
	    			'<sup>',
	    			'</sup>',
	    			'<sub>',
	    			'</sub>',
	    			'<span style="font-size:80%;">',
	    			'</span>',
	    			'<div class="bd_quoted_text">',
	    			'</div>',
	    			'<div class="bd_attention">',
	    			'</div>',
	    			'<strike>',
	    			'</strike>',
	    			'<div class="bd_divider"></div>',
	    			//'<th>',
	    			//'</th><th>',
	    			//'</th></tr><tr><td>',
	    			//'</td><td>',
	    			$this->format_date($article['created']),
	    			$this->format_date($article['last_updated']),
	    			$article['views'],
	    			$article['owner'],
	    			'<br />'
	    		);
	    		// Update user information
	    		if (! empty($user)) {
	    			global $user_data;
	    			$input = $this->process_user($input,$user,$user_data);
	    		} else {
	    			$replacements = array('%name%','%username%');
	    			$replacement_values = array('Guest','Guest');
	    			$input = str_replace($replacements,$replacement_values,$input);
	    		}
	    		// Get ready...
			$code_type = '';
	   		$code_open = "0";
	   		$final_article = '';
	   		$list_ul_open = '';
			$total_h1_headers = 0;
			$total_h2_headers = 0;
			$total_h3_headers = 0;
			$current_footnote = 0;
	   		$php_tag_open = '0';
			$list_ol_open = '';
			$list_ul_open = '';
	   		$stats_shows = '';
	   		$included_tablesort_js = '';
			$hiding = '';
			$options = '';
	   		$input = str_replace('<->','|-|center|-|',$input);
	   		$temp_lines = explode("\n",$input);
	   		// Text align considerations
	   		foreach ($temp_lines as $line) {
	   			$line = rtrim($line);
		   		$no_paragraph = "1";
	   			$wiki_format = '1';
	   			$style_add = '';
	   			$widget_found = '0';
	   			$tl_start = '';
	   			$skip_code_clear = '0';
	   			$code_close_found = false;
	   			
	   			// ------------------------------------------------------------------------
	   			//	Special considerations for multi-line tags
	   			
	   			if ($code_open == "1") {
		   			$no_paragraph = "1";
		   			// Preformatted Code
		   			if ($code_type == "pre") {
		   				if ($keep_formatting != '1') {
		   					$wiki_format = '0';
		   				}
			   			$code_close_found = strpos($line,'[/code]');
			   			$line = str_replace('[/code]','',$line);
		   				if (empty($code_language) || $code_language == 'formatted') {
			   				$line = str_replace('<','&lt;',$line);
			   				$line = str_replace('[','&#091;',$line);
			   			}
			   			$all_code .= $line . "\n";
			   			$line = '';
		   			}
		   			else if ($code_type == "table") {
			   			$no_paragraph = "1"; // Leave this!
			   			$table_close_found = strpos($line,'[/table]');
			   			if ($table_close_found !== false) {
	   		   				$code_open = "0";
	   		   				$code_type = "";
	   		   				$line = '</tbody></table>';
	   		   				$skip_code_clear = '1';
			   			} else {
			   				$current_line_th = strpos($line,'^');
			   				if ($current_line_th !== false) {
				   				// Multiple colspan for thead th
					   			if (strpos($line,'^^') == 0) {
					   				$mutli_head = 1;
					   			} else {
					   				$mutli_head = 0;
					   				$line = substr($line,1);
					   			}
					   			
					   			// $line = preg_replace('/\^\+\+/(\d+)','</th><th colspan=\"{1}\">',$line);
					   			
				   				$line = str_replace("^++10","</th><th colspan=\"10\">",$line);
				   				$line = str_replace("^++9","</th><th colspan=\"9\">",$line);
				   				$line = str_replace("^++8","</th><th colspan=\"8\">",$line);
				   				$line = str_replace("^++7","</th><th colspan=\"7\">",$line);
				   				$line = str_replace("^++6","</th><th colspan=\"6\">",$line);
				   				$line = str_replace("^++5","</th><th colspan=\"5\">",$line);
				   				$line = str_replace("^++4","</th><th colspan=\"4\">",$line);
				   				$line = str_replace("^++3","</th><th colspan=\"3\">",$line);
				   				$line = str_replace("^++2","</th><th colspan=\"2\">",$line);
				   				
				   				if ($mutli_head == 1) {
				   					$line = str_replace("^","</th><th>",$line) . "</th></tr></thead><tbody>";
				   				} else {
				   					$line = "<th>" . str_replace("^","</th><th>",$line) . "</th></tr></thead><tbody>";
				   				}
			   				} else {
			   				
					   	   		$line = $this->do_images($line);
					   	   		$line = $this->do_links($line);
					   	    		$line = $this->do_custom_classes($line);
					   	    		$line = $this->find_attachments($line);
					   	    		
					   	    		
					   	    		$tr_class = '';
					   	    		$td_class = '';
					   	    		$td_style = '';
					   	    		$number = 1;
					   	    		$check_td_class = '';
					   	    		$check_tr_class = '';
					   	    		
					   	    			$check_tr_class = strpos($line,'*');
						   	    		if (strpos($line,'!') === 0) {
	   							   		preg_match('/\!(.*?)\!/', $line, $coloring);
	   							   		//print_r($coloring);
	   							   		$stylesare = explode(':',$coloring['1']);
	   							   		$tr_class = " class=\"" . $coloring['1'] . "\"";
	   							   		$line = str_replace($coloring['0'],'',$line);
						   	    		} else {
						   	    			$tr_class = '';
						   	    		}
						   	    		
						   	    		$temp_line = "<tr$tr_class>";
						   	    		
						   	    		$cells = explode('|',$line);
						   	    		foreach ($cells as $aCell) {
						   	    		
						   	    			$td_class = '';
						   	    			$td_style = '';
						   	    			$number = 1;
						   	    			$rowspan = 1;
						   	    		
						   	    			// Colspan?
						   	    			$check_multi = strpos($aCell,'++');
					   	    				if ($check_multi !== false) {
							   	    			$number = substr($aCell,2,1);
					   	    					$aCell = substr($aCell,3);
					   	    				}
					   	    				
						   	    			// Rowspan?
						   	    			$check_multi = strpos($aCell,'~~');
					   	    				if ($check_multi !== false) {
							   	    			$rowspan = substr($aCell,2,1);
					   	    					$aCell = substr($aCell,3);
					   	    				}
					   	    				
					   	    				// Class?
					   	    				$makesure_not_bold = strpos($aCell,'**');
					   	    				$check_td_class = strpos($aCell,'*');
						   	    			if (strpos($aCell,'*') === 0 && $makesure_not_bold !== 0) {
		   							   		preg_match('/\*(.*?)\*/', $aCell, $coloring);
		   							   		//print_r($coloring);
		   							   		$stylesare = explode(':',$coloring['1']);
		   							   		$td_class = " class=\"" . $coloring['1'] . "\"";
		   							   		$aCell = str_replace($coloring['0'],'',$aCell);
						   	    			}
					   	    				
						   	    			if (strpos($aCell,'|-|center|-|') !== false) {
					   						$td_style = " style=\"text-align:center;\"";
					   						$aCell = str_replace('|-|center|-|','',$aCell);
					   					}
						   	    			else if (strpos($aCell,'<->') !== false) {
					   						$td_style = " style=\"text-align:center;\"";
					   						$aCell = str_replace('<->','',$aCell);
						   	    			}
						   	    			else if (strpos($aCell,'-->') !== false) {
					   						$td_style = " style=\"text-align:right;\"";
					   						$aCell = str_replace('-->','',$aCell);
						   	    			}
						   	    			
					   	    				$temp_line .= "<td$td_class$td_style";
					   	    				if ($number > 1) {
					   	    					$temp_line .= " colspan=\"$number\"";
					   	    				}
					   	    				if ($rowspan > 1) {
					   	    					$temp_line .= " rowspan=\"$rowspan\"";
					   	    				}
					   	    				$temp_line .= ">";
					   	    				$temp_line .= $aCell;
					   	    				$temp_line .= "</td>";
					   	    				
						   	    		}
						   	    		
						   	    		$temp_line .= "</tr>";
						   	    		
						   	    		$line = $temp_line;
						   	    		
						   	    		//echo "$temp_line\n\n";
						   	    		
					   	    		//}
					   	    		
					   	    		
					   	    		
					   	    		/*
				   				$line = str_replace("||||||||","<td colspan=\"8\">",$line);
				   				$line = str_replace("|||||||","<td colspan=\"7\">",$line);
				   				$line = str_replace("||||||","<td colspan=\"6\">",$line);
				   				$line = str_replace("|||||","<td colspan=\"5\">",$line);
				   				$line = str_replace("||||","<td colspan=\"4\">",$line);
				   				$line = str_replace("|||","<td colspan=\"3\">",$line);
				   				$line = str_replace("||","<td colspan=\"2\">",$line);
					   	    		$check_multi = strpos($line,'|++');
					   	    		// Multi-colspan
					   	    		if ($check_multi !== false) {
					   	    			$check_multi = $check_multi + 3;
					   	    			$number = substr($line,$check_multi,1);
				   					$line = str_replace("|++$number","",$line);
					   	    			if (strpos($line,'|-|center|-|') !== false) {
				   						$line = str_replace('|-|center|-|','',$line);
					   					$line = "<tr$final_coloring><td colspan=\"$number\" style=\"text-align:center;\">" . $line . "</td></tr>";
				   					}
					   	    			else if (strpos($line,'-->') !== false) {
				   						$line = str_replace('-->','',$line);
					   					$line = "<tr$final_coloring><td colspan=\"$number\" style=\"text-align:right;\">" . $line . "</td></tr>";
					   	    			} else {
					   					$line = "<tr$final_coloring><td colspan=\"$number\">" . $line . "</td></tr>";
					   	    			}
					   	    		}
					   	    		// Standard colspan
					   	    		else {
					   				$line = ltrim($line,'|');
					   				$line = str_replace("|","</td><td>",$line);
					   				$tl_start = strpos($line,'<td');
					   				if ($tl_start == '0') {
					   					$line = "<tr$final_coloring>" . $line . "</td></tr>";
					   				} else {
					   					$line = "<tr$final_coloring><td>" . $line . "</td></tr>";
					   				}
					   	    		}
					   	    		*/
					   	    		
					   	    		
			   				}
			   			}
		   			}
		   			else if ($code_type == "def_list") {
		   				$exp_dl_line = explode(':',$line);
		   				if (! empty($exp_dl_line['0']) && ! empty($exp_dl_line['1'])) {
			   				$theLine = "<dt>" . $exp_dl_line['0'] . "</dt><dd>" . $exp_dl_line['1'] . "</dd>";
				   			$line = $theLine;
			   			}
		   			}
		   			else if ($code_type == "literal") {
		   				$wiki_format = '0';
		   			}
	   			} else {
	   				$no_paragraph = "0";
			    		$line = $this->clickable_tags($line);
	   			}
	   			
	   			// Remove code
	   			if ($code_type != 'html' && $code_type != 'table' && $code_type != 'def_list' && $skip_code_clear != '1') {
	   				// Check if there is literal PHP code
	   				// to take into account.
	   				if (strpos($line,'<?php') !== false) {
	   					$no_paragraph = '1';
	   					$php_tag_open = '1';
	   				}
	   				else if ($php_tag_open == '1') {
	   					$no_paragraph = '1';
	   					if (strpos($line,'?>') !== false) {
	   						$php_tag_open = '0';
	   					}
	   				}
	   				else {
	   					$line = str_replace('<','&lt;',$line);
	   				}
	   			}
	   			
	   			// --------------------------------------------
	   			// 	Preformatted code
	   			
	   			if ($code_type != 'html') {
		   			if ($code_type != "pre") {
						// $tab_line = strpos($line, "    ");
						$tab_line = strpos($line, "\t");
						if ($tab_line !== false) {
							$no_paragraph = '1';
							if ($tab_line == '0') {
								$wiki_format = 0;
								// $line = ltrim($line,"    ");
								$line = ltrim($line,"\t");
								$line = '<pre class="bd_code_plain">' . $line . '</pre>';
							} else {
								$wiki_format = 1;
								$full_length = strlen($line);
								$first_part = substr($line,0,$tab_line);
								$second_part = substr($line,$tab_line,$full_length);
								//$second_part = ltrim($second_part,"    ");
								$second_part = ltrim($second_part,"\t");
								$line = $first_part . '<pre class="bd_code_plain">' . $second_part . '</pre>';
							}
						}
					}
		   			
		   			$code_found = strpos($line,'[code');
		   			// $code_found1 = strpos($line,'[code:formatted]');
		   			// $code_close_found = strpos($line,'[/code]');
		   			if ($code_found !== false) {
		   				// Language specific?
				   		$check_language = explode(':',$line);
				   		if (! empty($check_language['1'])) {
				   			$code_language = rtrim($check_language['1'],']');
				   		}
				   		if (! empty($check_language['2'])) {
				   			$code_line_numbers = '1';
				   		}
			   			$no_paragraph = "1";
		   				$code_open = "1";
		   				$code_type = "pre";
		   				if ($code_language == 'formatted') {
		   					$keep_formatting = '1';
		   				} else {
		   					$keep_formatting = '0';
		   				}
				   		$code_close_found = strpos($line,'[/code]');
		   				// $line = "<div class=\"bd_code\"><pre>" . $line;
		   				$line = preg_replace('/\[(\w+):(\w+)\]/', '', $line);
		   				$line = preg_replace('/\[(\w+):(\w+):(\w+)\]/', '', $line);
				   		$line = str_replace('[code]','',$line);
				   		$line = str_replace('[/code]','',$line);
				   		//$line = str_replace('[code:formatted]','',$line);
				   		$all_code .= $line . "\n";
				   		$line = '';
		   			}
		   			if ($code_close_found !== false) {
		   				$no_paragraph = "1"; // Leave this!
		   				$code_open = "0";
		   				// $line = $line . "</pre></div>";
				   		$all_code = str_replace('[/code]','',$all_code);
				   		$all_code = trim($all_code,"\n");
				   		// Process code?
				   		if (! empty($code_language) && $code_language != 'formatted') {
				   			include_once PATH . '/includes/geshi/geshi.php';
				   			$geshi = new GeSHi($all_code, $code_language);
				   			if ($code_line_numbers == '1') {
				   				$geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
				   			}
				   			$line = "<div class=\"bd_code\"><pre>\n" . $geshi->parse_code() . "\n</pre></div>";
				   		} else {
				   			$line = "<div class=\"bd_code\"><pre>\n" . $all_code . "\n</pre></div>";
				   		}
				   		$all_code = '';
		   				$code_type = '';
				   		$code_language = '';
				   		$code_line_numbers = '';
				   		$keep_formatting = '';
		   			}
	   			
	   			}
	
				if ($wiki_format == '1') {
				
			    		// ------------------------------	   		
			   		// 	Replace some basics.
			   		
			   		// General Replacements
		   	   		$line = $this->do_images($line);
		   	   		$line = $this->do_links($line);
		   	    		$line = $this->do_custom_classes($line);
		   	    		$line = $this->find_attachments($line);
			    		$line = $this->find_widgets($line,$skip_widgets);
		
		   			// --------------------------------------------
		   			// 	Generic list?
		   			
		   			$ul_found = strpos($line,'  -');
		   			$ol_found = strpos($line,'  #');
		   			$check_last_ul = strpos($line,'  -');
		   			$check_last_ol = strpos($line,'  #');
		   			if ($list_ul_open == '1' && $check_last_ul === false) {
		   				$line = "</ul>" . $line;
		   				$list_ul_open = '0';
		   			}
		   			if ($list_ol_open == '1' && $check_last_ol === false) {
		   				$line = "</ol>" . $line;
		   				$list_ol_open = '0';
		   			}
   		   			if ($ul_found !== false && $list_ul_open != '1') {
   		   				$list_ul_open = '1';
						$line = $this->clean_list_item($list_ul_open,$list_ol_open,$line,'1',$check_last_ul,$check_last_ol);
   		   				//$line = "<ul class=\"bd_normal_text\">" . $line;
   		   			}
   		   			else if ($ol_found !== false && $list_ol_open != '1') {
   		   				$list_ol_open = '1';
						$line = $this->clean_list_item($list_ul_open,$list_ol_open,$line,'1',$check_last_ul,$check_last_ol);
   		   				//$line = "<ol class=\"bd_normal_text\">" . $line;
   		   			}
		   			else if ($list_ul_open == '1' || $list_ol_open == '1') {
						$line = $this->clean_list_item($list_ul_open,$list_ol_open,$line,'0',$check_last_ul,$check_last_ol);
		   			}
		   			
		   			// --------------------------------------------
		   			// 	HTML code
		   			$code_found = strpos($line,'[html]');
		   			$code_close_found = strpos($line,'[/html]');
		   			if ($code_found !== false) {
			   			$no_paragraph = "1";
		   				$code_open = "1";
		   				$code_type = "html";
				   		$line = str_replace('[html]','',$line);
		   			}
		   			if ($code_close_found !== false) {
		   				$no_paragraph = "1"; // Leave this!
		   				$code_open = "0";
		   				$code_type = "";
				   		$line = str_replace('[/html]','',$line);
		   			}
		
		   			// --------------------------------------------
		   			// 	Table Found
		   			$code_found = strpos($line,'[table');
		   			if ($code_found !== false) {
			   			$no_paragraph = "1";
		   				$code_open = "1";
		   				$code_type = "table";
		   				$exp_table = explode(':',$line);
		   				if (! empty($exp_table['1']) || ! empty($exp_table['2']) || ! empty($exp_table['3'])) {
		   					$exp_table['3'] = rtrim($exp_table['3'],']');
		   					$exp_table['2'] = rtrim($exp_table['2'],']');
		   					$exp_table['1'] = rtrim($exp_table['1'],']');
		   					if (! empty($exp_table['2']) && $included_tablesort_js != '1') {
		   						$id = rand(1000,999999);
		   						$tablesorter = '<script type="text/javascript" src="' . URL . '/js/jquery.tablesorter.min.js"></script><script>$(document).ready(function() { $("#' . $id . '").tablesorter();  } );</script>';
		   						$included_tablesort_js = '1';
		   						$class = 'tablesorter';
		   					} else {
		   						$tablesorter = '';
		   						$class = 'tablesorter';
		   						$id = rand(1000,999999);
		   					}
		   					if (! empty($exp_table['1'])) {
		   						$class .= ' ' . $exp_table['1'];
		   					} else {
		   						$class .= ' bd_table';
		   					}
		   					if (! empty($exp_table['3'])) {
		   						$width = " style=\"width:" . $exp_table['3'] . "\";";
		   					} else {
		   						$width = "";
		   					}
		   					$line = $tablesorter . '<table cellspacing="0" cellpadding="0" border="0" id="' . $id . '" class="' . $class . '"' . $width . '><thead><tr>';
		   				} else {
		   					$line = '<table cellspacing="0" cellpadding="0" border="0" class="bd_table" id="' . $id . '"><thead><tr>';
		   				}
		   			}
		   			
		   			
		   			// --------------------------------------------
		   			// 	Definition list
		   			$code_found = strpos($line,'[def]');
		   			$code_close_found = strpos($line,'[/def]');
		   			if ($code_found !== false) {
			   			$no_paragraph = "1";
		   				$code_open = "1";
		   				$code_type = "def_list";
				   		$line = str_replace('[def]','<dl class="in_page">',$line);
		   			}
		   			if ($code_close_found !== false) {
		   				$no_paragraph = "1"; // Leave this!
		   				$code_open = "0";
		   				$code_type = "";
				   		$line = str_replace('[/def]','</dl>',$line);
		   			}
		   			
		   			// ------------------------------------------------------------------------
			   		// 	h1 headers
			   		preg_match_all('/----(.*?)----/', $line, $matches);
			   		foreach ($matches['0'] as $match) {
			   			$no_paragraph = "1";
			   			$match_temp = str_replace('----','',$match);
			   			$match_temp_clean = str_replace('"','',$match_temp);
			   			$match_temp_clean = str_replace('\'','',$match_temp_clean);
			   			$match_temp_clean = str_replace(' ','_',$match_temp_clean);
			   			$match_temp_heading = $match_temp;
			   			$match_temp = "<a name=\"" . $match_temp_clean . "\"></a><h1 class=\"bd_h1\">" . $match_temp . "</h1>";
			   			$line = str_replace($match,$match_temp,$line);
			   			$headers[] = '1+++' . $match_temp_heading;
			   			$total_h1_headers++;
			   		}
			   		
			    		// ------------------------------
			   		// 	h2 headers
			   		preg_match_all('/---(.*?)---/', $line, $matches);
			   		foreach ($matches['0'] as $match) {
			   			$no_paragraph = "1";
			   			$match_temp = str_replace('---','',$match);
			   			$match_temp_clean = $this->clean_name($match_temp);
			   			$match_temp_heading = $match_temp;
			   			$match_temp = "<a name=\"" . $match_temp_clean . "\"></a><h2 class=\"bd_h2\">" . $match_temp . "</h2>";
			   			$line = str_replace($match,$match_temp,$line);
			   			$headers[] = '2+++' . $match_temp_heading;
			   			$total_h2_headers++;
			   		}
			   		
			    		// ------------------------------
			   		// 	h3 headers
			   		preg_match_all('/--(.*?)--/', $line, $matches);
			   		foreach ($matches['0'] as $match) {
			   			$no_paragraph = "1";
			   			$match_temp = str_replace('--','',$match);
			   			$match_temp_clean = $this->clean_name($match_temp);
			   			$match_temp_heading = $match_temp;
			   			$match_temp = "<a name=\"" . $match_temp_clean . "\"></a><h3 class=\"bd_h3\">" . $match_temp . "</h3>";
			   			$line = str_replace($match,$match_temp,$line);
			   			$headers[] = '3+++' . $match_temp_heading;
			   			$total_h3_headers++;
			   		}
			   		
			    		// ------------------------------
			   		// 	help bubbles
			   		preg_match_all('/\[\?\](.*?)\[\/\?\]/', $line, $bubbles);
			   		foreach ($bubbles['0'] as $bubble) {
			   			$match_temp = str_replace('[?]','',$bubble);
			   			$match_temp = str_replace('[/?]','',$match_temp);
			   			$match_temp = $this->generate_help_bubble($match_temp);
			   			$line = str_replace($bubble,$match_temp,$line);
			   		}
			   			
			    		// ------------------------------
			   		//   Footnotes
			   		preg_match_all('/\[fn\](.*?)\[\/fn\]/', $line, $footnotes);
			   		foreach ($footnotes['0'] as $aFootnote) {
			   			$current_footnote++;
			   			$match_temp = str_replace('[fn]','',$aFootnote);
			   			$match_temp = str_replace('[/fn]','',$match_temp);
			   			$replace_with = "<sup><a href=\"#footnotes\">[$current_footnote]</a></sup>";
			   			$line = str_replace($aFootnote,$replace_with,$line);
			   			$footnotes_array[] = $match_temp;
			   		}
			
			    		// ------------------------------	   		
			   		// 	Quoted Text
					if (strpos($line, ">") === 0) {
						$no_paragraph = '1';
						$line = ltrim($line,">");
						$line = '<p class="bd_quoted_text">' . $line . '</p>';
					}
			   		
			    		// ------------------------------
			   		// 	Generic Replacements
			   		$line = str_replace($find,$replace,$line);
			   		
			    		// We need to make sure that italics and
			    		// external links don't get confused.
				   	$line = str_replace('http://','http:++',$line);
				   	$line = str_replace('https://','https:++',$line);
			   		
			    		// ------------------------------
			   		// Font style replacements
			   		if ($widget_found != '1') {
					   	$line = $this->run_replace('/__(.*?)__/',$line,'<u>','</u>','__');
					   	$line = $this->run_replace('/\*\*(.*?)\*\*/',$line,'<b>','</b>','**');
					   	$line = $this->run_replace('/\/\/(.*?)\/\//',$line,'<i>','</i>','//');
				   	}
			    		// Now return URLs to their correct
			    		// formatting.
				   	$line = str_replace('http:++','http://',$line);
				   	$line = str_replace('https:++','https://',$line);
		   		} // $wiki_format == '1'
		    		// ------------------------------
	   			//	 Paragraphs
	   			$length = strlen($line);
	   			$pos1 = strpos($line,'<ul');
	   			$pos2 = strpos($line,'<ol');
	   			$pos3 = strpos($line,'/ul>');
	   			$pos4 = strpos($line,'/ol>');
	   			$pos5 = strpos($line,'<li');
	   			$pos6 = strpos($line,'<div');
	   			$pos7 = strpos($line,'/div>');
	   			$pos8 = strpos($line,'<form');
	   			$pos9 = strpos($line,'/form>');
	   			$pos10 = strpos($line,'<iframe');
	   			$line_no_tags = strlen(strip_tags($line));
	   			if ($no_paragraph != "1" && $line_no_tags != 0 && $length > 0 && $pos1 === false && $pos2 === false && $pos3 === false && $pos4 === false && $pos5 === false && $pos6 === false && $pos7 === false && $pos8 === false && $pos9 === false && $pos10 === false) {
		   	    		// Higlighted text?
	   				$link_found = strpos($line,'<a href');
	   				if ($link_found === false) {
	    	    				$line = str_ireplace($highlight_text,'<span class="bd_highlighted">' . $highlight_text . '</span>',$line);
	    	    			}
	      	    		// Check for text alignment
	      	    		// Right align
	     			$right_align = strpos($line,'-->');
	     			if ($right_align !== false) {
	      	    			$line = str_replace('-->','',$line);
	      	    			$style_add = " style=\"text-align:right;\"";
	      	    		}
	         	    		// Centered
	        			$center_align = strpos($line,'|-|center|-|');
	        			if ($center_align !== false) {
	         	    			$line = str_replace('|-|center|-|','',$line);
	         	    			$style_add = " style=\"text-align:center;\"";
	         	    		}
		   	    		// Complete the line
	   				$final_lines .= "<p$style_add>" . $line . "</p>\n";
	   			} else {
	   				$line = str_replace('|-|center|-|','<->',$line);
	   				$final_lines .= $line . "\n";
	   			}
	   		} // Loop lines
	   		$input = $final_lines;
			$input = $this->get_php_code($input);
		   	// Smilies
		   	// $input = $this->process_smilies($input);
	   		// Prepare HTML
	   		//$final_article = "<div class=\"normal_text\">";
	   		
	   		/*
	   		if ($article['public'] != "1" && $widget != '1') {
	   			if ($article['public'] == '2') {
	   				$final_article .= "<div class=\"bd_attention bd_normal_text\"><p>This article is only visible to a specific set of users.</p></div>";
	   			}
	   			else if ($article['public'] == '3') {
	   				$final_article .= "<div class=\"bd_attention bd_normal_text\"><p>This article is marked marked for maintenance, meaning that it is not visible to the public. You are seeing it because you either own it or have the privileges.</p></div>";
	   			}
	   			else {
	   				$final_article .= "<div class=\"bd_attention bd_normal_text\"><p>This article is marked as private, meaning that it is not visible to the public. You are seeing it because you either own it or have the privileges.</p></div>";
	   			}
	   		}
	   		*/
	   		
		    	// $final_article = str_replace('&#43;&#43;&#43;','+++',$value);
	   		$final_article .= $input;
	   		// $final_article .= "\n\n<!-- End Article -->\n\n";
	   		// Stats
	   		/*
	   		if ($article['show_stats'] == '1') {
	   			$stats_shows = "<ul id=\"bd_article_stats\">";
	   			$stats_shows .= "<li>Created by " . $article['owner'] . ".</li>";
	   			if ($article['views'] == 0 || $article['views'] > 1) { $plural = "s"; } else { $plural = ""; }
	   			$stats_shows .= "<li>Viewed " . $article['views'] . " time$plural.</li>";
	   			$stats_shows .= "<li>Created on " . $this->format_date($article['created']) . ".</li>";
	   			$stats_shows .= "<li>Last updated on " . $this->format_date($article['last_updated']) . " times.</li>";
	   			$stats_shows .= "</ul>";
	   		} else {
	   			$stats_shows = '';
	   		}
	   		*/
	   		// Sidebar
	   		//if ($widget != '1' && ! empty($user)) {
	   			// $options = $this->article_sidebar($article,$just_article,$category);
	   		//}
	   		
   		}
   		
   		// Footnotes
   		$addupfoot = 0;
    		$total_footnotes = sizeof($footnotes_array);
	    	if ($total_footnotes > 0) {
	 	   	$footnotes_put = "<a name=\"footnotes\"></a><ol id=\"bd_footnotes\">";
    			$addupfoot++;
 	   		foreach ($footnotes_array as $aFoot) {
 	   			$footnotes_put .= "   <li>$aFoot</li>\n";
 	   		}
 	  	 	$footnotes_put .= "</ol>";
 	   	}

    		// Headers
    		$total_headers = sizeof($headers);
 	   	$headers_put = "<ul class=\"bd_headers\">";
    		if ($total_headers > 0) {
 	   		$nav_current = 0;
 	   		foreach ($headers as $aHeader) {
 	   			$class = '';
 	   			$cutHeader = explode('+++',$aHeader);
 	   			$nav_current++;
 		   		$header_clean = $this->clean_name($cutHeader['1']);
 	   			if ($cutHeader['0'] == '2') {
 	   				$class .= "h2";
 	   			}
 	   			else if ($cutHeader['0'] == '3') {
 	   				$class .= "h3";
 	   			}
 	   			else {
 	   				$class .= "h1";
 	   			}
 	   			if ($nav_current == '1') { $class .= " first"; }
 	   			// Hide lesser headers if there
 	   			// are too many on the page.
 	   			$total_big_headers = $total_h1_headers + $total_h2_headers;
 	   			if ($total_big_headers > 15) {
 	   				if ($cutHeader['0'] == '3' || $cutHeader['0'] == '2') {
 	   					$class .= " hide";
 	   					$hiding = '1';
 	   				}
 	   			}
 	   			else if ($total_big_headers > 10 && $total_big_headers < 15) {
 	   				if ($cutHeader['0'] == '3') {
 	   					$class .= " hide";
 	   					$hiding = '1';
 	   				}
 	   			}
 	   			$headers_put .= "          <li class=\"$class\"><a href=\"#$header_clean\">" . $cutHeader['1'] . "</a></li>\n";
 	   		}
 	   		if ($hiding == '1') {
 	   			$headers_put .= "          <li class=\"last small\"><a href=\"#\" onclick=\"return showHiddenHeaders();\">Show all headers</a></li>\n";
 	   		} else {
 	   			$headers_put .= "          <li class=\"last\"></li>\n";
 	   		}
    		} else {
 	   		$headers_put .= "          <li class=\"none\">" . lg_no_internal_links . "</li>\n";
 	   	}
 	   	$headers_put .= "</ul>";
   		
   		
   		// Find @username stuff
		$final_article = $this->find_at_username($final_article);
		//$final_article = $this->clickable_tags($final_article);
		$final_article = $this->custom_replacements($final_article);
   		
   		$final_article .= $footnotes_put;
   		
   		// $just_article is used when dynamically
   		// refreshing an article without a reload.
   		if ($just_article == '1') {
   			$combine = $final_article;
			return $combine;
		} else {
	   		$the_array = array();
	   		$the_array['article'] = $final_article;
	   		$the_array['sublinks'] = $headers_put;
	   		$the_array['options'] = $options;
	   		$the_array['stats'] = $stats_shows;
			return $the_array;
		}
	}


	// ---------------------------------------------------------------------------
   	// 	Generate a help bubble
   	//	$no_id is used for custom replacements. Since the same
   	//	replacement could be on the page multiple times,
   	//	we need to make sure each has a unique ID.
   	
	function generate_help_bubble($data,$no_id = '1') {
		global $theme_folder;
		if ($no_id == '1') {
			$this_id = 'XXbd_gen_idXX';
		} else { 
			$this_id = "bd" . uniqid();
		}
		$data = "<img src=\"$theme_folder/imgs/icon-help.png\" width=\"12\" height=\"12\" border=\"0\" alt=\"Additional Information\" class=\"bd_help_icon\" id=\"$this_id\" onmouseover=\"openHelpBubble('$this_id');\" onmouseout=\"closeHelpBubble('$this_id');\" /><span class=\"bd_help_bubble\" id=\"help_$this_id\">" . $data . "</span>";
		return $data;
	}


	// ---------------------------------------------------------------------------
   	// 	Take CLEditor's input and make it...
   	//	"normaler"... or at least try to?

	function cleditor_to_html($input) {
	
		preg_match_all('/\<div\>(.*?)\<\/div\>/', $input, $matches);
		foreach ($matches['0'] as $match) {
		   $match_temp = str_replace('<div>','',$match);
		   $match_temp = str_replace('</div>','',$match_temp);
		   if ($match_temp == '<br>') {
		   	$match_temp = "</p><p>";
		   } else {
		   	$match_temp = "<br />" . $match_temp;
		   }
		   $input = str_replace($match,$match_temp,$input);
		}
		// $input = str_replace('<p><br />','<p>',$input);
		$input = str_replace('<table','</p><table class="bd_table"',$input);
		$input = str_replace('</table>','</table><p>',$input);
		$input = "<p>" . $input . "</p>";
		$input = str_replace('<p></p>','',$input);
		$input = str_replace('</p></p>','</p>',$input);
		$input = str_replace('<p><p','<p',$input);
		$input = str_replace('<b><br></b>','',$input);
		$input = str_replace('<h1><br>','<h1>',$input);
		$input = str_replace('<h2><br>','<h2>',$input);
		$input = str_replace('<h3><br>','<h3>',$input);
		$input = str_replace('<u><br></u>','',$input);
		$input = str_replace('<i><br></i>','',$input);
		$input = str_replace('{-','</p>{-',$input);
		$input = str_replace('-}','-}<p>',$input);
		$input = str_replace('<ul>','<ul class="in_page">',$input);
		$input = str_replace('<ol>','<ol class="in_page">',$input);
		$input = str_replace('<strike><br></strike>','',$input);
		// $input = ltrim($input,'<p></p>');
		
		return $input;
	}


	// ---------------------------------------------------------------------------
   	// 	Custom classes
   	
	function do_custom_classes($line) {
  		preg_match_all('/\{\{(.*?)\}\}/', $line, $custom_divs);
  		foreach ($custom_divs['0'] as $custom_div) {
  			$no_paragraph = "1";
  			$custom_div_temp = str_replace('{{','',$custom_div);
  			$custom_div_temp = str_replace('}}','',$custom_div_temp);
  			$exp_custom_div = explode('|',$custom_div_temp);
  			if ($exp_custom_div['0'] == "end") {
      			$line = str_replace($custom_div,'</div>',$line);
  			} else {
  				// Style
  				if (! empty($exp_custom_div['1'])) {
  					$cc_style = " style=\"" . $exp_custom_div['1'] . "\"";
  				} else {
  					$cc_style = "";
  				}
      			// Get article
      			$replace_custom_div = "<div class=\"" . $exp_custom_div['0'] . "\"$cc_style>";
      			$line = str_replace($custom_div,$replace_custom_div,$line);
  			}
  		}
  		return $line;
	}

	// ---------------------------------------------------------------------------
   	// 	Special characters for page names
   	
	function convert_link_characters($link_name) {
		$link_name = str_replace('/','&#47;',$link_name);
		$link_name = str_replace('+','&#43;',$link_name);
		$link_name = str_replace('-','&ndash;',$link_name);
		return $link_name;
	}

	// ---------------------------------------------------------------------------
   	// 	Links
   	
   	function do_links($line) {
		// Links and other
	   	preg_match_all('/\[\[(.*?)\]\]/i', $line, $links);
	   	foreach ($links['0'] as $link) {
	   	
	   		$link_temp = str_replace('[[','',$link);
	   		$link_temp = str_replace(']]','',$link_temp);
	   		
	   		$link_temp = $this->convert_link_characters($link_temp);
	   		
	   		$exp_link = explode('|',$link_temp);
			
	   		$user_profile = strpos($exp_link['0'],'user:');
	   		if ($user_profile !== false) {
	   		
	   			$username_put = explode(':',$exp_link['0']);
	   			$get_link = URL . "/user/" . $username_put['1'];
		   		$replace_link = "<a href=\"$get_link\">" . $username_put['1'] . "</a>";
	   		
	   		} else {
	   		
		   		$find_article_id = $this->get_article_id_from_name($exp_link['0']);
		   		// Internal link, found ID
		   		if (! empty($find_article_id)) {
		   			$article_dets = $this->get_article($find_article_id,'1','category,name','0','0','0');
		   			$get_link = $this->prepare_link($find_article_id,$article_dets['category'],$article_dets['name']);
		  			// Get article
		  			if (empty($exp_link['1'])) {
		  				$exp_link['1'] = $article_dets['name'];
		  			}
		   			$replace_link = "<a href=\"$get_link\">" . $exp_link['1'] . "</a>";
		   		}
		   		// External link or didn't find ID.
		   		else {
		   			$pos = strpos($exp_link['0'],'http://');
		   			$pos1 = strpos($exp_link['0'],'https://');
		   			$pos3 = strpos($exp_link['0'],'ftp://');
		   			$pos2 = strpos($exp_link['0'],'#');
		   			// Internal page link
		   			if ($pos2 !== false) {
		      			if (empty($exp_link['1'])) {
		      				$exp_link['1'] =  $exp_link['0'];
		      			}
		   				$replace_link = "<a href=\"" . $exp_link['0'] . "\">" . $exp_link['1'] . "</a>";
		   			}
		   			// External Link
		   			else if ($pos !== false || $pos1 !== false || $pos3 !== false) {
		   				if ($exp_link['2'] == 'same') {
		   					$target = "";
		   				} else {
		   					$target = " target=\"_blank\"";
		   				}
		      			if (empty($exp_link['1'])) {
		      				$exp_link['1'] =  $exp_link['0'];
		      			}
		   				$replace_link = "<a href=\"" . $exp_link['0'] . "\"$target>" . $exp_link['1'] . "</a>";
		   			}
		   			// Internal Link
		   			else {
		      			// Title changes?
		      			if (empty($find_article_id)) {
		      				$exception = $this->find_redirect_rule('',$exp_link['0']);
		      				if (! empty($exception)) {
		   		   			if (empty($exp_link['1'])) {
		   		   				$exp_link['1'] = $get_link;
		   		   			}
		   	   				$get_link = $this->prepare_link($exception,'','');
		   	   				$replace_link = "<a href=\"$get_link\">" . $exp_link['1'] . "</a>";
		      				} else {
		   						$replace_link = "<span class=\"bd_no_link\">" . $exp_link['1'] . "</span>";
		      				}
		      			}
		   			}
		   		}
		   	}
		   	
		   	$line = str_replace($link,$replace_link,$line);
	   	}
	   	
	   	return $line;
   	}


	// ---------------------------------------------------------------------------
	// 	Find @username syntax
	
	function find_at_username($input) {
		$input = preg_replace('/(?<=^|\s|>)@([a-z0-9_]+)/i','<a href="%SL_base%/member_cp/' . URL . '/user/$1">@$1</a>',$input);
		return $input;
	}
	

	// ---------------------------------------------------------------------------
	// 	Images

	function do_images($line) {
	
		global $privileges;
		global $theme;
		
   		// ------------------------------
  		// 	Images
  		preg_match_all('/\(\((.*?)\)\)/', $line, $images);
  		foreach ($images['0'] as $image) {
  			$image_temp = str_replace('((','',$image);
  			$image_temp = str_replace('))','',$image_temp);
  			$exp_image = explode('|',$image_temp);
  			// Path and file exists?
  			// Start by removing this URL if possible.
  			$path = $exp_image['0'];
  			$path = str_replace(URL,'',$path);
  			$clean_path = $path;
  			$clean_path = str_replace('//','/',$clean_path);
   			$pos = strpos($path,'http://');
   			$pos1 = strpos($path,'https://');
  			// Outside source?
   			if ($pos !== false || $pos1 !== false) {
   				$outside_source = '1';
   			} else {
   				$outside_source = '0';
  				// $path = PATH . "/" . ltrim($exp_image['0'],'/');
  				$path = PATH . "/" . ltrim($path,'/');
   			}
   			// Try to see if the file exists
   			// On server!
  			if (file_exists($path)) {
  				// Width but no height
  				if (empty($exp_image['2']) && ! empty($exp_image['1']) && $exp_image['1'] != 'true') {
  					$dimensions = $this->proportion_image($path,$exp_image['1'],'');
  					$exp_image['2'] = $dimensions['y'];
  				}
  				// Height but no width
  				else if (empty($exp_image['1']) && ! empty($exp_image['2'])) {
  					$dimensions = $this->proportion_image($path,'',$exp_image['2']);
  					$exp_image['1'] = $dimensions['x'];
  				}
  				// Neither height nor width
  				else if (empty($exp_image['2']) && empty($exp_image['1'])) {
  					$dimensions = $this->proportion_image($path,'','');
  					$exp_image['1'] = $dimensions['x'];
  					$exp_image['2'] = $dimensions['y'];
  				}
  				// Title/alt?
  				if (empty($exp_image['3'])) {
  					if (! empty($exp_image['4'])) {
  						$exp_image['3'] = $exp_image['4'];
  					} else {
      					$file_comps = explode('/',$exp_image['0']);
      					$last = sizeof($file_comps) - 1;
      					$exp_image['3'] = $file_comps[$last];
  					}
  				}
  				$exp_image['3'] = str_replace('"','',$exp_image['3']);
  				$exp_image['3'] = str_replace('\'','',$exp_image['3']);
				list($original_width, $original_height, $o_type, $o_attr) = getimagesize($path);
				// No caption or title? Add them...
				// If we can't find the file in the media gallery,
				// we add it.
				if (empty($exp_image['4']) || empty($exp_image['3'])) {
					$q1 = "SELECT `id`,`title`,`caption` FROM `" . TABLE_PREFIX . "media` WHERE `location`='" . $this->mysql_clean($clean_path) . "' LIMIT 1";
					$theData = $this->get_array($q1);
					if (! empty($theData['id'])) {
						if (empty($exp_image['3'])) {
							$exp_image['3'] = $theData['title'];
						}
						if (empty($exp_image['4'])) {
							$exp_image['4'] = $theData['caption'];
						}
					} else {
						if ($privileges['is_admin'] == '1' || $privileges['can_create_articles'] == '1' || $privileges['can_alter_articles'] == '1') {
							$filenamear = explode('/',$clean_path);
							$last_fn = sizeof($filenamear)-1;
							$filename = $filenamear[$last_fn];
							$q2 = "INSERT INTO `" . TABLE_PREFIX . "media` (`location`,`filename`,`date`,`public`) VALUES ('" . $this->mysql_clean($clean_path) . "','" . $this->mysql_clean($filename) . "','" . $this->current_date() . "','1')";
							$insert = $this->insert($q2);
						}
					}
				}
  				// The actual outputs
  				
  				// echo "$exp_image[0]";
  				if ($exp_image['1'] == 'true') {
   					$replace_image = "<img src=\"" . $exp_image['0'] . "\" border=\"0\" />";
  				} else {
  					$replace_image = $this->img_html_thumbnail($exp_image['0'],$exp_image['1'],$exp_image['2'],$original_width,$original_height,$exp_image['3'],$exp_image['4']);
  				}
  			}
  			// Not on server currently
  			else {
  				$theme_path = URL . "/templates/html/$theme/imgs/icon-broken_img.png";
  				if ($outside_source == '1') {
   					$replace_image = "<img src=\"$path\" border=\"0\" />";
  				} else {
   					$replace_image = "<a href=\"#\" onclick=\"showUpload('2','" . $path . "','" . $article['id'] . "','');\"><img src=\"$theme_path\" width=\"34\" height=\"29\" border=\"0\" alt=\"Image not found, click to upload.\" title=\"Image not found, click to upload.\" /></a>";
   				}
  			}
  			$line = str_replace($image,$replace_image,$line);
  		}
  		
  		return $line;
	}


	// ---------------------------------------------------------------------------
	// 	Image HTML Thumbnail
	
	function img_html_thumbnail($url,$width,$height,$original_width,$original_height,$title = '',$caption = '',$thumbnail = '') {
		if (! empty($thumbnail)) {
			$t1 = $thumbnail;
			$t2 = $url;
		} else {
			$t1 = $url;
			$t2 = $url;
		}
		// Mobile Browser
		if (BD_MOBILE == '1') {
	   		$replace_image .= "<img src=\"" . $t1 . "\" data-fullsrc=\"" . $t2 . "\" title=\"" . addslashes(htmlspecialchars($title)) . "\" alt=\"" . addslashes(htmlspecialchars($title)) . "\" border=\"0\" class=\"bd_image\" />";
		}
		// Standard Browser
		/*
		else {
	   		$replace_image = "<div style=\"width:" . $width . "px;\">";
	   		$replace_image .= "<a href=\"#\" onclick=\"viewFullImage('" . $t2 . "','" . addslashes(htmlspecialchars($caption)) . "','" . addslashes(htmlspecialchars($title)) . "','" . $original_width . "','" . $original_height . "');return false;\"><img src=\"" . $t1 . "\" width=\"" . $width . "\" height=\"" . $height . "\" title=\"" . addslashes(htmlspecialchars($title)) . "\" alt=\"" . addslashes(htmlspecialchars($title)) . "\" border=\"0\" class=\"bd_image\" /></a>";
	   		if (! empty($caption)) {
	   			$replace_image .= "<p class=\"bd_image_caption\">" . $caption . "</p>";
	   		}
	   		$replace_image .= "</div>";
		}
		*/
		else {
	   		$replace_image = "<div class=\"bd_image_container\" style=\"width:" . $width . "px;\"><div style=\"cursor:pointer;width:100%;height:100%;background:url('$t1') top left no-repeat;width:" . $width . "px;height:$height" . "px;\" onclick=\"return viewFullImage('" . $t2 . "','" . addslashes(htmlspecialchars($caption)) . "','" . addslashes(htmlspecialchars($title)) . "','" . $original_width . "','" . $original_height . "');\" class=\"bd_image\"></div>";
	   		if (! empty($caption)) {
	   			$replace_image .= "<p class=\"bd_image_caption\">" . $caption . "</p>";
	   		}
	   		$replace_image .= "</div>";
		}
   		return $replace_image;
	}


	// ---------------------------------------------------------------------------
	// 	Check a page for hashtags and adds
	//	them to the database if found.

	function add_tags($input,$page,$category) {
		// Clear page's tags
		$q1 = "DELETE FROM `" . TABLE_PREFIX . "article_tags` WHERE `page_id`='$page'";
		$delete = $this->delete($q1);
		// Remove stuff where we don't want tags...
		$input = str_replace("\n", '', $input);
		$input = preg_replace('#\[code\].*?\[\/code\]#', '$1$2', $input);
		$input = preg_replace('#\[html\].*?\[\/html\]#', '$1$2', $input);
		$input = preg_replace('#\<style.*?\<\/style\>#', '$1$2', $input);
		$input = preg_replace('#\<script.*?\<\/script\>#', '$1$2', $input);
		// Redo tags
		$date = $this->current_date();
		$tag_list = '';
   		preg_match_all('/(?<=^|\s|>)#([a-z0-9_]+)/i', $input, $found_tags);
   		foreach ($found_tags['0'] as $aTag) {
   			// Make change
   			$clean_tag = strtolower(ltrim($aTag,'#'));
   			$tag_list .= ", ('$page','" . $this->mysql_clean($clean_tag) . "','$date','$category')";
   		}
   		// Here tags were found, return an empty line.
    		if (! empty($tag_list)) {
    			$tag_list = ltrim($tag_list,', ');
    			$q = "INSERT INTO `" . TABLE_PREFIX . "article_tags` (`page_id`,`tag`,`date`,`category`) VALUES $tag_list";
    			$insert = $this->insert($q);
    		}
	}
	
	
	// ---------------------------------------------------------------------------
	// 	Make hashtags clickable
	

	function clickable_tags($input,$format = '1') {
		if ($this->get_option('use_hastags') == '1') {
			// Remove stuff where we don't want tags...
			$temp_use = preg_replace('#\[code\].*?\[\/code\]#', '$1$2', $input);
			$temp_use = preg_replace('#\[html\].*?\[\/html\]#', '$1$2', $temp_use);
			$temp_use = preg_replace('#\<style.*?\<\/style\>#', '$1$2', $temp_use);
			$temp_use = preg_replace('#\<script.*?\<\/script\>#', '$1$2', $temp_use);
	   		// Make clickable
	   		preg_match_all('/(?<=^|\s|>)#([a-z0-9_]+)/i', $temp_use, $found_tags);
	   		foreach ($found_tags['0'] as $aTag) {
	   			if ($format == '1') {
		   			$new_line = "[[" . URL . "/search.php?q=" . urlencode($aTag) . "|" . $aTag . "|same]]";
			   	   	$input = preg_replace("/$aTag\s/i",$new_line . " ",$input);
			   	   	$input = preg_replace("/$aTag\[/i",$new_line . "[",$input);
		   		} else {
		   			$new_line = "<a href=\"" . URL . "/search.php?q=" . urlencode($aTag) . "\">$aTag</a>";
		   			$input = str_replace($aTag,$new_line,$input);
		   		}
		   	}
		   	return $input;
	   	} else {
	   		return $input;
	   	}
	}
	
	
	// ---------------------------------------------------------------------------
	// 	Clean a list item
	//	Used when a comment is posted, edited,
	//	or when a page is created or edited.

	function check_mentions($input,$page='',$comment='',$page_array = '',$comment_array = '') {
		global $user;
		global $user_data;
   		preg_match_all('/(?<=^|\s|>)@([a-z0-9_]+)/i', $input, $found_users);
   		foreach ($found_users['0'] as $aUser) {
   			// Make change
   			$clean_user = ltrim($aUser,'@');
   			//$line = "<a href=\"" . URL . "/user/$clean_user/public\">" . $aUser . "</a>";
   			//$input = str_replace($found_users['0'],$line,$input);
   			// Notified about this?
		   	if (empty($page_array)) {
		   		$page_array = $this->get_article($page,'1','*','0','0','0');
		   	}
		   	$special_changes = $this->replace_article_tags($page_array);
   			if (! empty($comment)) {
	   			$final_id = 'c-' . $comment;
	   			$template = 'user_mentionned_comment';
		   		$special_changesA = $this->replace_comment_tags($comment_array);
		   		$special_changes = @array_merge($special_changesA,$special_changes);
   			} else {
	   			$final_id = 'p-' . $page;
	   			$template = 'user_mentionned_page';
   			}
   			// Get user ID
   			$user_id = $this->get_user_id($clean_user);
			$already_sent = $this->check_for_notice($user_id,'mention',$final_id);
			
			
			// Send notification + email
			if ($already_sent != '1') {
				// Notice
				$notice = $this->complete_task('mention',$user_id,$page_array['id'],'',$user_data['id']);
				$notice = $this->add_notice($user_id,'mention',$final_id);
		   		$special_changes['%mentionned_by%'] = $clean_user;
		   		$sent = $this->send_template($clean_user,$template,"",$special_changes);
			}
   		}
	}


	// ---------------------------------------------------------------------------
	// 	Clean a list item
	
	function clean_list_item($list_ul_open,$list_ol_open,$line,$first = '0',$check_last_ul,$check_last_ol) {
	      	if (! empty($list_ul_open)) {
	      		$space_left = $check_last_ul * 10;
	      	}
	      	else if (! empty($list_ol_open)) {
	      		$space_left = $check_last_ol * 10;
	      	}
	      	else {
	      		$space_left = 0;
	      	}
	      	$line = str_replace('  -','',$line);
	      	$line = str_replace('  #','',$line);
	      	$line = ltrim($line,' ');
	      	$line = "<li style=\"margin-left:" . $space_left . "px;\">" . $line . "</li>";
	      	if ($first == '1') {
	      		if ($list_ul_open == '1') {
	      			$line = "<ul class=\"in_page\">" . $line;
	      		}
	      		else if ($list_ol_open == '1') {
	      			$line = "<ol class=\"in_page\">" . $line;
	      		}
	      	}
	      	return $line;
	}

	// ---------------------------------------------------------------------------
	// 	Clean a name for IDs, etc.
	
	function clean_name($name) {
		$name = preg_replace('/[^\w\d_ -]/si', '', $name);
		$name = str_replace(' ', '_', $name);
		return $name;
	}


	// ---------------------------------------------------------------------------
	// 	Run PHP Code
	
	function get_php_code($input) {
		// Only run this if the user deems it
		// safe to run.
		if ($this->get_option('allow_php') == '1') {
	   		preg_match_all('/<\?[php]*([^\?>]*)\?>/', $input, $codeF);
	   		foreach ($codeF['0'] as $PHP_CODE) {
	   			// Clean the information
	   			$PHP_CODE_ORI = $PHP_CODE;
	   			$PHP_CODE = str_replace('<?php','',$PHP_CODE);
	   			$PHP_CODE = str_replace('?>','',$PHP_CODE);
	   			// Start buffering
	   			ob_start();
				eval($PHP_CODE);
				$the_code = ob_get_contents();
				ob_end_clean();
				// Replace information in page
	   			$input = str_replace($PHP_CODE_ORI,$the_code,$input);
	   		}
   		}
   		return $input;
	}


	// ---------------------------------------------------------------------------
	// 	Find widgets on line.
	//	Takes a line from a page and finds widgets.
	
	function find_widgets($line,$skip_widgets = '0') {
		$current = 0;
   		preg_match_all('/\{\-(.*?)\-\}/', $line, $widgets);
   		foreach ($widgets['0'] as $aWidget) {
   			$no_paragraph = "1";
   			$widget_found = '1';
   			$widgets_temp = str_replace('{-','',$aWidget);
   			$widgets_temp = str_replace('-}','',$widgets_temp);
   			if ($skip_widgets == '1') {
   				$widget_data = '';
   			} else {
   				if (ctype_digit($widgets_temp) === true) {
   					$widget_data = $this->get_widget($widgets_temp);
   				} else {
   					$widget_data = $this->get_widget('0',$widgets_temp);
   				}
   			}
	   		$line = str_replace($aWidget,$widget_data,$line);
   		}
   		return $line;
	}
	
	
	// ---------------------------------------------------------------------------
	// 	Create a select menu of user types
	
	function user_types_select($selected = "3",$type = "radio",$add_select = '0') {
		if ($add_select == '1') {
			$final = "<select name=\"options[user_type]\" style=\"width:300px;\">";
			$final .= "<option value=\"\"";
			if (! empty($selected)) {
				$final .= " selected=\"selected\"";
			}
			$final .= ">All User Types</option>";
		}
   		$q = "SELECT `id`,`name` FROM `" . TABLE_PREFIX . "user_types` ORDER BY `name` ASC";
   		$results = $this->run_query($q);
   		while ($row = mysql_fetch_array($results)) {
   			if ($type == "radio") {
   				$final .= "<option value=\"" . $row['id'] . "\"";
   				if ($selected == $row['id']) {
   					$final .= " selected=\"selected\"";
   				}
   				$final .= "> " . $row['name'] . "</option>";
   			}
   		}
		if ($add_select == '1') {
			$final .= "</select>";
		}
   		return $final;
	}
	

	// ---------------------------------------------------------------------------
	// 	Check if a user type's privileges have been
	//	stripped in a specific category.
	
	function check_usertype_stripped($category,$user_type,$priv = 'all') {
		$q = "SELECT COUNT(*) FROM `bd_stripped_privs` WHERE `category`='" . $this->mysql_clean($category) . "' AND `group`='" . $this->mysql_clean($user_type) . "' AND `privilege`='" . $this->mysql_clean($priv) . "'";
		$found = $this->get_array($q);
		return $found['0'];
	}
	
	// ---------------------------------------------------------------------------
	// 	Find downloads on line.
	//	Takes a line from a page and finds downloads.
	
	function find_attachments($line) {
		global $user;
		global $privileges;
   		preg_match_all('/\{\|(.*?)\|\}/', $line, $attachment);
   		foreach ($attachment['0'] as $aFile) {
   			$no_paragraph = "1";
   			$file_temp = str_replace('{|','',$aFile);
   			$file_temp = str_replace('|}','',$file_temp);
   			$exp_file = $file_temp;
   			// Base Domain
   			// Filename
   			/*
	   		$ThisServer = strpos($exp_file,$_SERVER['HTTP_HOST']);
	   		if ($ThisServer === false) {
	   			$exp_file1 = ltrim($exp_file,'/');
	   			$exp_server = PATH . "/" . $exp_file1;
	   			$exp_file = URL . "/" . $exp_file1;
	   		} else {
	   			$exp_file1 = ltrim($exp_file,'http:// https:// ftp://');
	   			$exp_server = str_replace($_SERVER['HTTP_HOST'],$_SERVER['DOCUMENT_ROOT'],$exp_file1);
	   		}
	   		// File information
	   		$file_info = $this->get_file_info($exp_file,'','');
   		
   			// DB entry?
   			if (empty($file_info['id'])) {
   	   		$exp_options = explode('/',$exp_file);
   	   		$total_options = sizeof($exp_options) - 1;
   	   		$actual_filename = $exp_options[$total_options];
   				$id = substr(uniqid(),0,14);
   				$q = "
   				INSERT INTO `" . TABLE_PREFIX . "attachments` (`id`,`path`,`server_path`,`filename`,`downloads`,`owner`,`login`,`limit`)
   				VALUES ('$id','" . $this->mysql_clean($exp_file) . "','" . $exp_server . "','" . $this->mysql_clean($actual_filename) . "','0','$user','0','0')
   				";
   				$insert = $this->insert($q);
   				$final_id = $id;
   			} else {
   				$final_id = $file_info['id'];
   			}
   			*/
	   		$file_info = $this->get_file_info($exp_file);
			if (! empty($file_info['id'])) {
	   			$dl_path = URL . "/functions/dl.php?file=" . urlencode($exp_file);
	   			$special_changes = array(
	   				'%dl_size%' => $file_info['size'],
	   				'%dl_path%' => $dl_path,
	   				'%dl_name%' => $file_info['name'],
	   				'%dl_ext%' => $file_info['ext'],
	   				'%dl_downloads%' => $file_info['downloads']
	   			);
	   			$final_put_file = $this->render_template('download_entry',$user,$special_changes,'1');
   			} else {
	   			$final_put_file = '';
   			}
   			
	   		//print_r($file_info);
	   		// On server or external?
	   		//$ThisServer1 = strpos($exp_file,URL);
   			// File on server
   			/*
   			if (file_exists($file_info['path'])) {
   				$dl_path = URL . "/functions/dl.php?file=" . urlencode($final_id);
   				$special_changes = array(
   					'%dl_size%' => $file_info['size'],
   					'%dl_path%' => $dl_path,
   					'%dl_name%' => $file_info['name'],
   					'%dl_ext%' => $file_info['ext'],
   					'%dl_downloads%' => $file_info['downloads']
   				);
   				$final_put_file = $this->render_template('download_entry',$user,$special_changes,'1');
   			}
   			// File not on server: upload box display
   			else {
   				if (! empty($user) && $privileges['upload_files'] == '1') {
   					$final_put_file = "<div class=\"bd_attachment_internal bd_dl_missing\"><a href=\"#\" onclick=\"showUpload('1','" . $exp_file . "','" . $article['id'] . "','$final_id');\">" . $file_info['name'] . "</a></div>";
   				} else {
   					$final_put_file = '';
   				}
   			}
   			*/
	   		$line = str_replace($aFile,$final_put_file,$line);
   		}
   		return $line;
	}


	// ---------------------------------------------------------------------------
	// 	Create a rewrite rule

	function create_rewrite_rule($category,$name,$id) {
		// First we get the old link
		$url = "/article/" . $category . "/" . $name;
		// Prepare new link
		$new_link = $this->prepare_link($id,'','','');
		// Database consideration
		$q = "INSERT INTO `" . TABLE_PREFIX . "article_redirects` (`old_category`,`old_article`,`new_article_id`) VALUES ('" . $this->mysql_clean($category) . "','" . $this->mysql_clean($name) . "','" . $id . "')";
		$insert = $this->insert($q);
		// Rule
		$url = str_replace(' ','\+',$url);
		$new_link = str_replace(' ','\+',$new_link);
		$rule = "RewriteRule ^" . $url . "(/)?$ " . $new_link . " [NC,L]\n";
		$rule .= "# next rule here";
		// Write to mod_rewrite file
		$path = PATH . "/.htaccess";
		if (is_writable($path)) {
			$htaccess = file_get_contents($path);
			$htaccess = str_replace('# next rule here',$rule,$htaccess);
			$write = $this->write_file($path,$htaccess);
		} else {
			// Reply
			$rule = nl2br($rule);
			$write = str_replace('%rule%',$rule,lg_admin_no_write);
		}
	}


	// ---------------------------------------------------------------------------
	// 	Alter an image's size to get correct
	//	proportions based on either a given
	//	width or height.
	//	Presumes the file exists on the server.
	//	Pre-screen if needed.
	
	function proportion_image($path,$width = '',$height = '') {
		$dim = array();
		$direction = '';
		$ratio = '';
		list($cur_width, $cur_height, $cur_type, $cur_attr) = getimagesize($path);
		// X or y bigger?
		/*
		if ($cur_width > $cur_height) {
			$direction = "x";
			$reduction = (1 - ($width / $cur_width));
		} else {
			$direction = "y";
			$reduction = (1 - ($height / $cur_height));
		}
		*/
		
		if (! empty($width)) {
			$ratio = $width / $cur_width;
			$new_x = $width;
			$new_y = ceil($cur_height * $ratio);
		}
		else if (! empty($height)) {
			$new_y = $height;
			$new_x = ceil($cur_width * $ratio);
		}
		else {
			$new_x = $cur_width;
			$new_y = $cur_height;
		}

		$dim['x'] = $new_x;
		$dim['y'] = $new_y;
		return $dim;
	}

	// ---------------------------------------------------------------------------
	// 	Get a widget's details
	
	function widget_info($id = '',$plugin_name = '') {
		if (! empty($id)) {
			$q = "SELECT * FROM `" . TABLE_PREFIX . "widgets` WHERE `id`='$id' LIMIT 1";
			$widget = $this->get_array($q);
		}
		else if (! empty($plugin_name)) {
			$q = "SELECT * FROM `" . TABLE_PREFIX . "widgets` WHERE `filename`='$plugin_name' LIMIT 1";
			$widget = $this->get_array($q);
		}
		else {
			$widget = '';
		}
		
		$options = unserialize($widget['options']);
		$widget['opts'] = $options;
		
		return $widget;
	}


	// ---------------------------------------------------------------------------
	// 	Gets a widgets and adds it to a template
	//	or article.
	
	function get_widget($id,$plugin_name = '') {	
		$where_clause = '';
		$order_clause = '';
		$limit_clause = '';
		if (! empty($plugin_name)) {
			$widget = $this->widget_info('',$plugin_name);
		} else {
			$widget = $this->widget_info($id);
			$widoptions = unserialize($widget['options']);
		}
		
		// Exist?
		if (empty($widget)) {
		
			return "<! Widget or plugin does not exists! ($id / $plugin_name) >";
			
		}
		
		// Is the widget active?
		else if ($widget['active'] == '1') {
		
			// ---------------------------------------------------------------------------
			// 	Category article index
			
			if ($widget['type'] == '1') {
			
				if (empty($widoptions['order'])) {
					$widoptions['order'] = "order";
				}
	   			$where_clause = " AND `in_widgets`='1' ORDER BY `" . $widoptions['order'] . "`";
	   			if (! empty($widoptions['dir'])) {
	   				$where_clause .= " " . $widoptions['dir'];
	   			} else {
	   				$where_clause .= " ASC";
	   			}
	   			if (! empty($widoptions['limit'])) {
	   				$where_clause .= " LIMIT 0," . $widoptions['limit'];
	   			}
	   			// Columns
	   			if (empty($widoptions['columns'])) {
	   				$widoptions['columns'] = '1';
	   			}
	   			// No.5 = List subcategories?
				$widget_content = $this->category_tree($widget['category'],'0','0','1','1',$where_clause,$widoptions['columns']);
				
			}
			
			// ---------------------------------------------------------------------------
			// 	Site Activity
			
			if ($widget['type'] == '19') {
			
				$widget_content = $this->generate_feed($widoptions);
			
			}
			
			// ---------------------------------------------------------------------------
			// 	Recent comments in category
			
			else if ($widget['type'] == '2') {
			
				// Work with the options to create
				// a WHERE clause.
				if (empty($widoptions['order'])) {
					$widoptions['order'] = "date";
				}
	   			if (empty($widoptions['dir'])) {
	   				$widoptions['dir'] .= " DESC";
	   			}
				if ($widoptions['order'] == "score") {
	   				$order_clause = " ORDER BY `up`-`down` " . $widoptions['dir'];
				} else {
	   				$order_clause = " ORDER BY `" . $widoptions['order'] . "` " . $widoptions['dir'];
	   			}
	   			if (empty($widoptions['limit'])) {
	   				$widoptions['limit'] = "10";
	   			}
	   			$limit_clause = " LIMIT 0," . $widoptions['limit'];
	   			// Specific Article?
	   			$where_clause = " WHERE `pending`!='1'";
	   			if (! empty($widoptions['page'])) {
	   				$where_clause = " AND `article`='" . $widoptions['page'] . "'";
	   			}
	   			// Cut length
	   			if (empty($widoptions['trim'])) {
	   				$widoptions['trim'] = "50";
	   			}
				// Prepare HTML
				if (empty($widget['html'])) {
					$widget['html'] = "<ul class=\"bd_widget_ul\">%entries%</ul>";
				}
				if (empty($widget['html_insert'])) {
					$widget['html_insert'] = "<li>\n<span class=\"bg_widget_list_title\"><a href=\"%article_link%\">%article_name%</a></span>\n<span class=\"bd_widget_list_sub\">%article_created% by <a href=\"/user/%username%\">%username%</a></span>\n</li>";
				}
				// Combine the WHERE statement
				$secondary_list = "";
				$q = "SELECT * FROM `" . TABLE_PREFIX . "comments` " . $where_clause . $order_clause . $limit_clause;
				$user_results = $this->run_query($q);
				while ($row = mysql_fetch_array($user_results)) {
					// Snippet
					$comment_length = strlen($row['comment']);
					if ($comment_length > $widoptions['trim']) {
						$final_comment = $row['comment'] . "...";
					} else {
						$final_comment = $row['comment'];
					}
					// Article details
					$article_name = $this->get_article_name_from_id($row['article']);
					$article_link = $this->prepare_link($row['article'],'',$article_name);
					$article_link = "<a href=\"$article_link\">" . $article_name . "</a>";
					// Other
					$score = $row['up'] - $row['down'];
					if ($row['status'] == '0') {
						$status_title = '';
					} else {
						$final_comment_type = $this->get_status_settings($row['status']);
						$status_title = $final_comment_type['title'];
					}
					// Format
					$comments = '';
					$temp_hold = $widget['html_insert'];
					$temp_hold = str_replace('%article_title%',$article_name,$temp_hold);
					$temp_hold = str_replace('%article_link%',$article_link,$temp_hold);
					$temp_hold = str_replace('%comment_date%',$this->format_date($row['date']),$temp_hold);
					$temp_hold = str_replace('%comment_score%',$score,$temp_hold);
					$temp_hold = str_replace('%comment%',$final_comment,$temp_hold);
					$temp_hold = str_replace('%comment_type%',$status_title,$temp_hold);
					$temp_hold = $this->process_user($temp_hold,$row['user']);
					$secondary_list .= $temp_hold;
				}
				// Combine it all
				$widget_content = $widget['html'];
				$widget_content = str_replace('%entries%',$secondary_list,$widget_content);
			}
			
			// ---------------------------------------------------------------------------
			// 	Custom HTML
			//	3 = custom html
			//	12 = Video
			//	13 = Map
			//	14 = Calendar
			//	16 = Spreadsheet
			
			else if ($widget['type'] == '3' || $widget['type'] == '12' || $widget['type'] == '13' || $widget['type'] == '14' || $widget['type'] == '16') {
			
				$temp_hold = $widget['html'];
			
				if ($widget['type'] == '12') {
					if (filter_var($temp_hold, FILTER_VALIDATE_URL) !== false) {
						// Youtube
						if (strpos($temp_hold,'youtube') !== false) {
							$blow_url = explode('?',$temp_hold);
							$blow_query = explode('&',$blow_url['1']);
							foreach ($blow_query as $qs) {
								$blow_qs = explode('=',$qs);
								if ($blow_qs['0'] == 'v') {
									$vid_id = $blow_qs['1'];
								}
							}
						}
						$temp_hold = "<iframe width=\"420\" height=\"315\" src=\"http://www.youtube.com/embed/$vid_id?wmode=opaque\" frameborder=\"0\" allowfullscreen></iframe>";
					}
				}
			
				if ($widoptions['format'] != '1') {
					$temp_hold = $this->format_article('',$temp_hold,'','','1','1','','','Widget');
				}
				global $user_data;
				global $user;
				$temp_hold = $this->process_standard($temp_hold,'0',$user_data,'1');
				$temp_hold = $this->process_user($temp_hold,$user,$user_data);
				$widget_content = $temp_hold;
				
				//if (BD_ARTICLE_VIEWING_FORMAT == '1') {
				//	$widget_content = "[html]" . $widget_content . "[/html]";
				//}
			}
			
			// ---------------------------------------------------------------------------
			// 	Most recent articles
			
			else if ($widget['type'] == '4') {
				// Work with the options to create
				// a WHERE clause.
				if (empty($widoptions['order'])) {
					$widoptions['order'] = "created";
				}
	   			if (empty($widoptions['dir'])) {
	   				$widoptions['dir'] .= " DESC";
	   			}
				if ($widoptions['order'] == "score") {
	   				$order_clause = " ORDER BY `upvoted`-`downvoted` " . $widoptions['dir'];
				} else {
	   				$order_clause = " ORDER BY `" . $widoptions['order'] . "` " . $widoptions['dir'];
	   			}
	   			if (empty($widoptions['limit'])) {
	   				$widoptions['limit'] = "10";
	   			}
	   			$limit_clause = " LIMIT 0," . $widoptions['limit'];
	   			// Specific Category
	   			if (! empty($widget['category'])) {
	   				$where_clause = " WHERE `public`='1' AND `category`='" . $widget['category'] . "'";
	   			}
	   			else if (! empty($widoptions['category'])) {
	   				$where_clause = " WHERE `public`='1' AND `category`='" . $widoptions['category'] . "'";
	   			}
	   			$where_clause .= " AND `in_widgets`='1'";
				// Prepare HTML
				if (empty($widget['html'])) {
					$widget['html'] = "<ul class=\"bd_widget_ul\">%entries%</ul>";
				}
				if (empty($widget['html_insert'])) {
					$widget['html_insert'] = "<li>\n<span class=\"bg_widget_list_title\"><a href=\"%article_link%\">%article_name%</a></span>\n<span class=\"bd_widget_list_sub\">%article_created% by <a href=\"/user/%username%\">%username%</a></span>\n</li>";
				}
				// Combine the WHERE statement
				$secondary_list = "";
				$q = "SELECT `id` FROM `" . TABLE_PREFIX . "articles` " . $where_clause . $order_clause . $limit_clause;
				$user_results = $this->run_query($q);
				while ($rowA = @mysql_fetch_array($user_results)) {
					// Article data
					$row = $this->get_article($rowA['id'],'1','*','1','0','0');
					/*
					// Continue...
					$score = $row['upvoted'] - $row['downvoted'];
					$category_name = $this->get_category_name_from_id($row['category']);
					$category_link = "<a href=\"" . $this->prepare_link('',$row['category']) . "\">" . $category_name . "</a>";
					$article_link = $this->prepare_link($row['id'],$row['category'],$row['name']);
					$article_link = "<a href=\"$article_link\">" . $row['name'] . "</a>";
					// Snippet
					$article_snippet = $this->get_snippet($row,$row['content']);
					// Comments
					$q = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "comments` WHERE `article`='" . $row['id'] . "' AND `pending`!='1'";
					$count = $this->get_array($q);
					$comments = $count['0'];
					*/
					// Widget
					$temp_hold = $this->replace_article_tags($row,'1',$widget['html_insert']);
					/*
					$temp_hold = str_replace('%article_snippet%',$article_snippet,$temp_hold);
					$temp_hold = str_replace('%article_title%',$row['name'],$temp_hold);
					$temp_hold = str_replace('%article_link%',$article_link,$temp_hold);
					$temp_hold = str_replace('%article_description%',$row['meta_desc'],$temp_hold);
					$temp_hold = str_replace('%article_category%',$row['category'],$temp_hold);
					$temp_hold = str_replace('%category_name%',$category_name,$temp_hold);
					$temp_hold = str_replace('%category_link%',$category_link,$temp_hold);
					$temp_hold = str_replace('%article_created%',$this->format_date($row['created']),$temp_hold);
					$temp_hold = str_replace('%article_date%',$this->format_date($row['created']),$temp_hold);
					$temp_hold = str_replace('%article_last_updated%',$this->format_date($row['last_updated']),$temp_hold);
					$temp_hold = str_replace('%article_score%',$score,$temp_hold);
					$temp_hold = str_replace('%article_comments%',$comments,$temp_hold);
					*/
					$temp_hold = $this->process_user($temp_hold,$row['owner']);
					$secondary_list .= $temp_hold;
				}
				// Combine it all
				$widget_content = $widget['html'];
				$widget_content = str_replace('%entries%',$secondary_list,$widget_content);
			}
			
			// ---------------------------------------------------------------------------
			// 	Plugin
			
			else if ($widget['type'] == '5') {
				global $manual;
				global $db;
				global $session;
				global $template;
				$path = PATH . "/addons/widgets/" . $widget['filename'] . "/index.php";
				if (file_exists($path)) {
	   				ob_start();
	   				include($path);
	   				$widget_content = ob_get_contents();
	   				ob_end_clean();
	   			} else {
	   				$widget_content = '';
	   			}
			}
			
			// ---------------------------------------------------------------------------
			// 	Most recent users
			
			else if ($widget['type'] == '6') {
				// Work with the options to create
				// a WHERE clause.
				if (empty($widoptions['order'])) {
					$widoptions['order'] = "joined";
				}
	   			if (empty($widoptions['dir'])) {
	   				$widoptions['dir'] .= " DESC";
	   			}
	   			$order_clause = " ORDER BY `" . $widoptions['order'] . "` " . $widoptions['dir'];
	   			if (empty($widoptions['limit'])) {
	   				$widoptions['limit'] = "10";
	   			}
	   			$limit_clause = " LIMIT 0," . $widoptions['limit'];
	   			// Specific User Type?
	   			if (! empty($widoptions['user_type'])) {
	   				$where_clause = " WHERE `type`='" . $widoptions['user_type'] . "'";
	   			}
				// Prepare HTML
				if (empty($widget['html'])) {
					$widget['html'] = "<ul class=\"bd_widget_ul\">%entries%</ul>";
				}
				if (empty($widget['html_insert'])) {
					$widget['html_insert'] = "<li><span class=\"bg_widget_list_title\"><a href=\"%user_link%\">%username%</a></span><span class=\"bd_widget_list_sub\">%joined%</span></li>";
				}
				// Combine the WHERE statement
				$secondary_list = "";
				$q = "SELECT username FROM `" . TABLE_PREFIX . "users` " . $where_clause . $order_clause . $limit_clause;
				$user_results = $this->run_query($q);
				while ($row = @mysql_fetch_array($user_results)) {
					$temp_hold = $this->process_user($widget['html_insert'],$row['username']);
					$secondary_list .= $temp_hold;
				}
				// Combine it all
				$widget_content = $widget['html'];
				$widget_content = str_replace('%entries%',$secondary_list,$widget_content);
			}
			
			
			// ---------------------------------------------------------------------------
			// 	Image Gallery
			
			else if ($widget['type'] == '7') {
				
				//print_r($widoptions);
				
				require_once PATH . "/includes/image.functions.php";
				$image = new image;
				$gallery = $image->get_images_by_tag($widoptions['tags'],$widoptions['strict'],$widoptions['not_after']);
				
				if (empty($widoptions['thumb_width'])) {
					$widoptions['thumb_width'] = '200';
				}
				
				$current = 0;
				foreach ($gallery as $photo) {
					$current++;
					$photo_info = $image->get_image($photo);
		   			
		   			$path = $photo_info['thumbnail'];
		   			$thumb_sizes = @$image->get_resize($path,$widoptions['thumb_width']);
		   			
	  				$replace_image = $this->img_html_thumbnail($photo_info['location'],$thumb_sizes['0'],$thumb_sizes['1'],$photo_info['width'],$photo_info['height'],$photo_info['title'],$photo_info['caption'],$photo_info['thumbnail_url']);
	  				
	  				if (BD_MOBILE == '1') {
		   				$temp_hold = "<li>%image%<p class=\"bd_caption\">%caption%</p></li>";
		   			} else {
		   				$temp_hold = $widget['html_insert'];
		   			}
		   			
		   			$temp_hold = str_replace('%image%',$replace_image,$temp_hold);
		   			$temp_hold = str_replace('%caption%',$photo_info['caption'],$temp_hold);
		   			$final_list .= $temp_hold;
		   			
	   	   			if ($current == $widoptions['columns'] && $widoptions['columns'] > 1 && BD_MOBILE != '1') {
	   	   				$final_list .= "<li style=\"background:none !important;border:0px !important;clear:left;\"></li>";
	   	   				$current = 0;
	   	   			}
				}
				
				$final_list .= "<div class=\"clear\"></div>";
				
				$widget_content = $widget['html'];
				$widget_content = str_replace('%entries%',$final_list,$widget_content);
				
			}
			
			// ---------------------------------------------------------------------------
			// 	Most commented pages
			
			else if ($widget['type'] == '8') {
			
				
			
			}
			
			// ---------------------------------------------------------------------------
			// 	Tag Cloud
			//	$widoptions['0'] = category_id
			//	$widoptions['1'] = max_tags
			
			else if ($widget['type'] == '9') {
				// Work with the options to create
				// a WHERE clause.
				$category = '';
				if (! empty($widoptions['category'])) {
					$subcats = $this->get_subcategories_of($widoptions['category'],'1');
					foreach ($subcats as $aCate) {
						$categories .= " OR `category`='$aCate'";
					}
					$categories = ltrim($categories,' OR ');
				}
	   			if (empty($widoptions['max_tags'])) {
	   				$widoptions['max_tags'] = "99999";
	   			}
	   			
	   			// MySQL Query
	   			$q = "SELECT `tag`,COUNT(*) FROM `" . TABLE_PREFIX . "article_tags` ";
				if (! empty($categories)) {
					$q .= " WHERE " . $categories;
				}
				$q .= "GROUP BY `tag`";
	   			$limit = "LIMIT 0," . $widoptions['max_tags'];
	   			
	   			// Highest
	   			$highest = $q . "ORDER BY COUNT(*) DESC LIMIT 1";
	   			// Lowest
	   			// $lowest = $q . "ORDER BY COUNT(*) ASC LIMIT 1";
	   			$highCount = $this->get_array($highest);
	   			//$lowCount = $this->get_array($lowest);
	   			
	   			//$lowest_number = $lowCount['1'];
	   			
	   			$divisions = $highCount['1'] / 4;
	   			$set_1 = $divisions;
	   			$set_2 = $divisions * 2;
	   			$set_3 = $divisions * 3;
	   			$set_4 = $divisions * 4;
	   			
	   			//print_r($highCount);
	   			//echo "<li>$highest /// $highest_number || $divisions -- $set_1 / $set_2 / $set_3 / $set_4";
	   			
				// Prepare HTML
				if (empty($widget['html'])) {
					$widget['html'] = "<ul class=\"bd_tags\">%entries%</ul>";
				}
				if (empty($widget['html_insert'])) {
					$widget['html_insert'] = "<li%style%>%tag%</li>";
				}
				
				// Combine the WHERE statement
				$tag_results = $this->run_query($q);
				while ($row = mysql_fetch_array($tag_results)) {
					if ($row['1'] <= $set_1) {
						$style = "";
					}
					else if ($row['1'] > $set_1 && $row['1'] <= $set_2) {
						$style = " style=\"font-size:120%;\"";
					}
					else if ($row['1'] > $set_2 && $row['1'] <= $set_3) {
						$style = " style=\"font-size:150%;\"";
					}
					else if ($row['1'] > $set_3 && $row['1'] <= $set_4) {
						$style = " style=\"font-size:180%;\"";
					}
					else {
						$style = " style=\"font-size:220%;\"";
					}
					$secondary_list .= $widget['html_insert'];
					$secondary_list = str_replace('%tag%',"<a href=\"" . URL . "/search.php?q=%23" . $row['tag'] . "\">" . $row['tag'] . "</a>",$secondary_list);
					$secondary_list = str_replace('%style%',$style,$secondary_list);
				}
				
				// Combine it all
				$widget_content = $widget['html'];
				$widget_content = str_replace('%entries%',$secondary_list,$widget_content);
			}
			
			// ---------------------------------------------------------------------------
			// 	To Do List
			
			else if ($widget['type'] == '11') {
				
				global $user;
				
				// Can the user edit this list?
				if ($widoptions['privacy'] == 'private' && $widget['owner'] == $user) {
					$editable = '1';
				}
				else if ($widoptions['privacy'] == 'public' && ! empty($user)) {
					$editable = '1';
				}
				else {
					$editable = '0';
				}
				
				// Get list items
				$widget_content = '<ul id="widget' . $widget['id'] . '" class="bd_todo">';
				$q = "SELECT `id`,`name`,`complete`,`date`,`date_complete` FROM `" . TABLE_PREFIX . "widgets_todo` WHERE `list_id`='" . $widget['id'] . "' ORDER BY `complete` ASC";
				$items = $this->run_query($q);
				while ($row = mysql_fetch_array($items)) {
					
					$widget_content .= $this->format_todo_item($row['id'],$widget['id'],$row['name'],$row['complete'],$editable,$row['date'],$row['date_complete']);
					
				}
				
				// Can the user add items?
				if ($editable == '1') {
					$widget_content .= "<li style=\"text-align:center;\"><a id=\"" . $widget['id'] . "shownewa\" href=\"#\" onclick=\"return crossfade('" . $widget['id'] . "shownewa','" . $widget['id'] . "shownew');\">Add New Item [+]</a><div id=\"" . $widget['id'] . "shownew\" style=\"display:none;\"><input type=\"text\" id=\"" . $widget['id'] . "newitem\" style=\"width:250px;\" /> <input type=\"button\" value=\"Add Item\" onclick=\"toDoAdd('" . $widget['id'] . "');\" /> <input type=\"button\" value=\"Cancel\" onclick=\"return crossfade('" . $widget['id'] . "shownew','" . $widget['id'] . "shownewa');\" /></div></li>";
				}
				$widget_content .= "</ul>";
				
			}
			
			// ---------------------------------------------------------------------------
			// 	Page date list
			
			else if ($widget['type'] == '18') {
			
				$diff = abs(strtotime($widoptions['end_date']) - strtotime($widoptions['start_date']));
				$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
				
				$entries = '';
				$current_month = strtotime($widoptions['end_date']);
				while ($months >= 0) {
					$show_date = date('F Y',$current_month);
					
					$entries .= "<li class=\"date_title\" id=\"" . $widget['id'] . "$current_month\"><a href=\"#\" onclick=\"return getPageRange('$current_month','" . $widget['id'] . "');\">$show_date</a></li>";
					
					$current_month = strtotime('-1 month',$current_month);
					$months--;
				}
				
				$widget_content = str_replace('%entries%',$entries,$widget['html']);
			
			}
			
			// Return the widget data
			
			$widget_content .= "<div class=\"clear\"></div>";
			return $widget_content;
			
			
		} // Widget active?
		
		
		else {
		
			return $widget['name'] . ' is disabled...';
		
		}
		
	}


	// --------------------------------------------------
	//	Generate an activity feed
	
	function generate_feed($widoptions,$user_id = '') {
	
		global $session;
		global $user_data;
	
   		$category_where = '';
   		if ($widoptions['category'] != '0') {
   			$category_whereA = '';
   			$subcategories = $this->get_subcategories_of($widoptions['category']);
   			foreach ($subcategories as $aCate) {
   				$category_whereA .= " OR `category_id`='" . $this->mysql_clean($aCate) . "'";
   			}
   			$category_whereA = substr($category_whereA,4);
   			$category_where .= "(`category_id`='" . $this->mysql_clean($widoptions['category']) . "'" . $category_whereA . ")";
   		}
   		
   		$where = '';
   		$type_where = '';
   		if ($widoptions['newpages'] == '1') {
   			$type_where .= " OR `type`='newpages'";
   		}
   		if ($widoptions['editpages'] == '1') {
   			$type_where .= " OR `type`='editpages'";
   		}
   		if ($widoptions['badges'] == '1') {
   			$type_where .= " OR `type`='badges'";
   		}
   		if ($widoptions['newuser'] == '1') {
   			$type_where .= " OR `type`='newuser'";
   		}
   		if ($widoptions['comment'] == '1') {
   			$type_where .= " OR `type`='comment'";
   		}
   		if ($widoptions['mentions'] == '1') {
   			$type_where .= " OR `type`='mentions'";
   		}
   		if ($widoptions['profilepost'] != '1') {
   			// Nothing here
   		} else {
	   		if (! empty($user_id)) {
	   			$type_where .= " OR `type`='profilepost'";
	   		} else {
	   			$type_where .= " OR `type`!='profilepost'";
	   		}
   		}
   		
   		if (! empty($type_where)) {
   			$type_where = substr($type_where,4);
   			$where .= " OR (" . $type_where . ")";
   		}
   		
   		if (! empty($widoptions['limit'])) {
   			$limit = " LIMIT 0," . $widoptions['limit'];
   		} else {
   			$limit = "";
   		}
   		
   		$last_type = '';
   		$last_act = '';
   		
   		$where = substr($where,4);
   		if (! empty($category_where)) {
   			$where = $category_where . ' AND ' . $where;
   		}
   		if (! empty($user_id)) {
   			$where .= " AND `user`='$user_id'";
   		}
   		$widget_content = '';
   		$q = "
   			SELECT * FROM `" . TABLE_PREFIX . "activity`
   			WHERE $where
   			ORDER BY `date` DESC
   			$limit
   		";
   		$results = $this->run_query($q);
   		
   		while ($row = mysql_fetch_array($results)) {
   			
   			$special_changes = array();
   			if ($last_type == $row['type'] && $last_type != 'comment' && $last_type != 'profilepost' && $last_act == $row['act_id']) {
   				// Skip to avoid duplicate tasks reappearing
   				// over and over again.
   			} else {
   			
   				$last_type = $row['type'];
   				$last_act = $row['act_id'];
   			
   				$username_send = $this->get_username_from_id($row['user']);
  	   			$user_link = $this->get_user_link($row['user']);
   				$user_pic = $this->get_profile_thumb($row['user']);
  					$special_changes = array(
						'%posting_id%' => $row['id'],
  						'%username_by%' => $username_send,
  						'%user_link%' => $user_link,
  						'%user_pic%' => $user_pic,
  						'%date%' => $this->format_date($row['date']),
  						'%age%' => $this->get_age($row['date']),
  					);
   				
   				// Get Page
   				if ($row['type'] == 'newpages' || $row['type'] == 'editpages') {
   					$article_link = $this->prepare_link($row['act_id'],$row['category_id'],$row['act_name']);
  					$special_changes['%article_id%'] = $row['act_id'];
  					$special_changes['%article_name%'] = $row['act_name'];
  					$special_changes['%article_link%'] = $article_link;
   				}
   				else if ($row['type'] == 'mentions') {
					$article_link = $this->prepare_link($row['act_id'],$row['category_id'],$row['act_name']);
    					$special_changes['%article_id%'] = $row['act_id'];
    					$special_changes['%article_name%'] = $row['act_name'];
    					$special_changes['%article_link%'] = $article_link;
 					$mub = $this->get_username_from_id($row['sup_id']);
	   				$user_link = $this->get_user_link($row['sup_id']);
    					$special_changes['%mentionned_by%'] = $mub;
    					$special_changes['%mentionned_link%'] = $mub;
   				}
   				else if ($row['type'] == 'profilepost') {
	   				if ($row['user'] == $user_data['id'] || $row['sup_id'] == $user_data['id']) {
	   					$final_options = "<p class=\"feed_options\"><a href=\"#\" onclick=\"return delPosting('" . $row['id'] . "');\">Delete Posting</a></p>";
	   				} else {
	   					$final_options = '';
	   				}
    					$user_info = $session->get_user_data('',$row['sup_id']);
	   				$poster_link = $this->get_user_link($user_info['username']);
    					$special_changes['%poster_link%'] = $poster_link;
    					$special_changes['%poster_username%'] = $user_info['username'];
    					$special_changes['%poster_pic%'] = $user_info['profile_thumbnail'];
    					$special_changes['%post%'] = $this->format_comment($row['post']);
    					$special_changes['%feedpost_options%'] = $final_options;
   				}
   				else if ($row['type'] == 'badges') {
    					$badge_info = $this->get_badge($row['act_id']);
    					$special_changes['%badge_id%'] = $badge_info['id'];
    					$special_changes['%badge_name%'] = $badge_info['name'];
    					$special_changes['%badge_desc%'] = $badge_info['desc'];
   				}
   				else if ($row['type'] == 'comment') {
    					$comment_info = $this->get_a_comment($row['sup_id'],'comment');
					$article_link = $this->prepare_link($row['act_id'],$row['category_id'],$row['act_name']);
    					$special_changes['%article_id%'] = $row['act_id'];
    					$special_changes['%article_name%'] = $row['act_name'];
    					$special_changes['%article_link%'] = $article_link;
    					$special_changes['%comment%'] = $this->format_comment($comment_info['comment']);
   				}
   				
   				$widget_content .= $this->render_template('feed_' . $row['type'],$username_send,$special_changes,'1','1');
   			
   			}
   			
   		}
   		
   		return $widget_content;
	
	}


	// ---------------------------------------------------------------------------
	// 	Get activity
	
	function get_activity($id) {
		$q = "SELECT * FROM `" . TABLE_PREFIX . "activity` WHERE `id`='" . $this->mysql_clean($id) . "' LIMIT 1";
		$dets = $this->get_array($q);
		return $dets;
	}


	// ---------------------------------------------------------------------------
	// 	Get the status of a todo list item.
	//	Swap item is requested.
	
	function get_todo_status($item_id,$swap = '0') {
		$theId = $this->mysql_clean($item_id);
   		$q = "SELECT `complete` FROM `" . TABLE_PREFIX . "widgets_todo` WHERE `id`='" . $theId . "'";
   		$status = $this->get_array($q);
   		if ($swap == '1') {
   			if ($status['complete'] == '1') {
   				$q = "UPDATE `" . TABLE_PREFIX . "widgets_todo` SET `complete`='0',`date_complete`='' WHERE `id`='" . $theId . "'";
   				$new_status = '0';
   			} else {
   				$q = "UPDATE `" . TABLE_PREFIX . "widgets_todo` SET `complete`='1',`date_complete`='" . $this->current_date() . "' WHERE `id`='" . $theId . "'";
   				$new_status = '1';
   			}
   			$update = $this->update($q);
			return $new_status;
   		} else {
			return $status['complete'];
   		}
	}
	
	
	// ---------------------------------------------------------------------------
	// 	Delete a widget's item.
	
	function delete_widget_item($item_id) {
		
		$q = "DELETE FROM `" . TABLE_PREFIX . "widgets_todo` WHERE `id`='" . $this->mysql_clean($item_id) . "' LIMIT 1";
		$del = $this->delete($q);
		echo "1";
		exit;
		
	}
	
	
	// ---------------------------------------------------------------------------
	// 	Add to do item
	
	function add_todo_item($widget,$name) {
		
		global $user;
		
		$q = "INSERT INTO `" . TABLE_PREFIX . "widgets_todo` (`list_id`,`name`,`complete`,`date`) VALUES ('" . $this->mysql_clean($widget) . "','" . $this->mysql_clean($name) . "','0','" . $this->current_date() . "')";
		$insert = $this->insert($q);
		
		$todoItem = $this->format_todo_item($insert,$widget,$_POST['name'],'0','1');
						
		return "$todoItem";
		
	}
	

	// ---------------------------------------------------------------------------
	// 	Format a to-do item

	function format_todo_item($id,$widget,$name,$complete,$editable = '0',$date_added = '',$date_completed = '') {
		if ($complete == '1') {
			$img = 'check_on.png';
			$class = 'complete';
			$tag = "Mark incomplete.";
			$date_difference = "Completed in " . $this->get_age($date_added,$date_completed) . "";
		} else {
			$img = 'check_off.png';
			$class = 'incomplete';
			$date_difference = '';
			$tag = "Mark complete.";
		}
		if ($editable == '1') {
			return "<li class=\"$class\" id=\"todoItem" . $id . "\" title=\"$date_difference\"><a href=\"#\" onclick=\"return markToDo('" . $id . "','" . $widget . "');\"><img src=\"" . THEME_IMAGES . "/$img\" style=\"float:left;\" id=\"todoImg" . $id . "\" border=\"0\" alt=\"$tag\" title=\"$tag\" /></a><div style=\"margin-left:24px\"><a href=\"#\" onclick=\"return delToDo('" . $id . "','" . $widget . "');\"><img src=\"" . THEME_IMAGES . "/close.png\" border=\"0\" style=\"float:right;\" title=\"Remove Item\" alt=\"Remove Item\" /></a>" . $name . "</div></li>";
		} else {
			return "<li class=\"$class\" id=\"todoItem" . $id . "\"><img src=\"" . THEME_IMAGES . "/$img\" style=\"float:left;\" border=\"0\" alt=\"$tag\" title=\"$tag\" id=\"todoImg" . $id . "\" /><div style=\"margin-left:24px\">" . $name . "$date_difference</div></li>";
		}
	}
	

	// ---------------------------------------------------------------------------
	// 	Check to do privileges

	function check_todo_privs($widget_id) {

		global $user;
		
		if (empty($user)) {
	   		echo "0+++" . lg_privilieges_req;
	   		exit;
		}
	
		$widget = $this->widget_info($widget_id);
		$widoptions = unserialize($widget['options']);
		
	   	// Can the user edit this list?
	   	if ($widoptions['privacy'] == 'private' && $widget['owner'] == $user) {
	   		$editable = '1';
	   	}
	   	else if ($widoptions['privacy'] == 'public') {
	   		$editable = '1';
	   	}
	   	else {
	   		$editable = '0';	
	   	}
	   	
	   	return $editable;
	   	
	}


	// ---------------------------------------------------------------------------
	//	Get article image
	
	function get_article_image($article_data,$content) {
  		preg_match_all('/\(\((.*?)\)\)/', $content, $images);
  		if (! empty($images['0'])) {
			$image = $images['0']['0'];
	  		$image = str_replace('((','',$image);
	  		$image = str_replace('))','',$image);
	  		$exp_image = explode('|',$image);
	  		$image = $exp_image['0'];
	  		if (strpos($image,'http://') === false && strpos($image,'https://') === false) {
	  			$image = ltrim($image,'/');
	  			$image = URL . "/" . $image;
	  		}
  		}
  		return $image;
	}


	// ---------------------------------------------------------------------------
	// 	Get a snippet of an article.
	
	function get_snippet($article_data,$content = '',$cut_off = '250',$no_line_breaks = '0') {
		global $user;
  		$content = preg_replace('/\(\((.*?)\)\)/', '', $content);
		$content = trim(strip_tags($content));
		$article_snippet = $this->format_article($article_data,$content,$user,'','1','0','0','1','Snippet');
		$holdsnip = $article_snippet;
		$article_snippet = strip_tags($article_snippet);
		$slen = strlen($article_snippet);
		if ($slen > $cut_off) {
			$article_snippet = trim(substr($article_snippet,0,$cut_off));
			$exp_snippet = explode('.',$article_snippet);
			if (sizeof($exp_snippet) > 1) {
				$popped = array_pop($exp_snippet);
				if (sizeof($exp_snippet) <= 0) {
					$article_snippet = $article_snippet . "...";
				} else {
					$article_snippet = implode('.',$exp_snippet) . ".";
				}
			}
		}
		if ($no_line_breaks == '1') {
			$exp_snippet = explode("\n",$article_snippet);
			$article_snippet = $exp_snippet['0'];
		}
		$article_snippet = preg_replace('/\%(.*?)\%/', '', $article_snippet);
		//$article_snippet = $this->replace_article_tags($article_data,'1',$article_snippet,'1');
		return $article_snippet;
	}
	

	// --------------------------------------------------------------------
	// 	Custom Callers
	
	function get_custom_caller($id) {
		$q = "SELECT * FROM `" . TABLE_PREFIX . "custom_callers` WHERE `id`='" . $this->mysql_clean($id) . "'";
		return $this->get_array($q);		
	}
	

	// --------------------------------------------------------------------
	// 	Do custom caller replacements
	
	function custom_replacements($data) {
		$manual = new manual;
		if (BD_CATEGORY_VIEWING) {
			$add_where = " OR `category`='" . BD_CATEGORY_VIEWING . "'";
		}
		$q = "SELECT * FROM `" . TABLE_PREFIX . "custom_callers` WHERE `category`='0' $add_where";
		$results = $this->run_query($q);
		while ($row = mysql_fetch_array($results)) {
			// Link to page
			if ($row['type'] == 'link') {
				if (BD_ARTICLE_VIEWING != $row['replacement']) {
					// Full Link
					if (strpos($row['replacement'],'http') !== false) {
						$link = $row['replacement'];
						$external = ' target="_blank"';
					}
					// Article Links
					else {
						$external = '';
						if ($row['category'] == '1') {
							$link = $manual->prepare_link('',$row['replacement']);
						} else {
							$link = $manual->prepare_link($row['replacement']);
						}
					}
					$full_link = "<a href=\"$link\"$external>" . $row['caller'] . "</a>";
					$data = str_replace($row['caller'],$full_link,$data);
					$finding = array(
						'</a>s ',
						'</a>s-',
						'</a>s.',
						'</a>s,',
						'</a>s:',
						'</a>s<',
						'</a>s/',
						'</a>s)',
						'</a>s]',
						'</a>s}',
						'</a>es ',
						'</a>es-',
						'</a>es.',
						'</a>es,',
						'</a>es:',
						'</a>es<',
						'</a>es/',
						'</a>es)',
						'</a>es]',
						'</a>es}',
					);
					$replacing = array(
						's</a> ',
						's</a>-',
						's</a>.',
						's</a>,',
						's</a>:',
						's</a><',
						's</a>/',
						's</a>)',
						's</a>]',
						's</a>}',
						'es</a> ',
						'es</a>-',
						'es</a>.',
						'es</a>,',
						'es</a>:',
						'es</a><',
						'es</a>/',
						'es</a>)',
						'es</a>]',
						'es</a>}',
					);
					$data = str_replace($finding,$replacing,$data);
				}
			}
			// Help Bubble
			else if ($row['type'] == 'bubble') {
				$get_bubble = $manual->generate_help_bubble($row['replacement'],'1');
				//$get_bubble = str_replace('%bd_gen_id%',$this_id,$get_bubble);
				$full_back = $row['caller'] . $get_bubble;
				$data = str_replace($row['caller'],$full_back,$data);
				$data = $this->multi_str_replace('XXbd_gen_idXX',$data);
			}
			// Replacement
			else {
				$data = str_replace($row['caller'],$row['replacement'],$data);
			}
		}
		return $data;
	}

	// ---------------------------------------------------------------------------
	// 	Check permissions for a private article.
	
	function user_permissions($article_id = '',$user_id,$user_type,$from_admin_cp = '0',$category_id = '') {
		global $privileges;
		global $user;
		if (empty($user)) {
			return "0";
		}
		else if ($privileges['is_admin'] == '1' && $from_admin_cp != '1') {
			return "1";
		}
		else {
			if (! empty($article_id)) {
				$add_where = "`permission`='" . $this->mysql_clean($article_id) . "'";
			} else {
				$add_where = "`category`='" . $this->mysql_clean($category_id) . "'";
			}
			if (! intval($user_id)) {
				// global $session;
				$user_id = $this->get_user_id($user_id);
			}
			if (! empty($user_type) && ! empty($user_id)) {
				$add_where .= " AND (`user_type`='" . $this->mysql_clean($user_type) . "' OR `user_id`='" . $this->mysql_clean($user_id) . "')";
			}
			else if (! empty($user_id) && empty($user_type)) {
				$add_where .= " AND `user_id`='" . $this->mysql_clean($user_id) . "'";
			}
			else if (empty($user_id) && ! empty($user_type)) {
				$add_where .= " AND `user_type`='" . $this->mysql_clean($user_type) . "'";
			}
			// Now check
			$q = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "user_permissions` WHERE $add_where";
			$count = $this->get_array($q);
			if ($count['0'] > 0) {
				return "1";
			} else {
				return "0";
			}
		}
	}


	// ---------------------------------------------------------------------------
	// 	Get recent comments matching criteria
	
	function recent_comments($where,$limit = '5',$user = '') {
		// global $template;
		$q = "SELECT * FROM `" . TABLE_PREFIX . "comments` WHERE $where ORDER BY `date` DESC LIMIT $limit";
		$result = $this->run_query($q);
		$all = '';
		$found_comments = 0;
 		while ($row = mysql_fetch_array($result)) {
 			// Considerations
   			// Snippet
   			$found_comments++;
   			$final_comment = $this->format_comment($row['comment']);
   			// Article details
   			// $article_name = $this->get_article_name_from_id($row['article']);
   			// $article_link = $this->prepare_link($row['article'],'',$article_name);
   			//$article_link = "<a href=\"$article_link\">" . $article_name . "</a>";
   			// Other
   			$score = $row['up'] - $row['down'];
   			if ($row['status'] == '0') {
   				$status_title = '';
   			} else {
   				$final_comment_type = $this->get_status_settings($row['status']);
   				$status_title = $final_comment_type['title'];
   			}
 			// Changes
 			$special_changes = $this->replace_comment_tags($row);
		   	// Render Page
		   	$render = $this->render_template('comment_panel',$user,$special_changes,'1');
		   	$all .= $render;
 		}
   		// Anything?
   		if ($found_comments <= 0) {
		   	$all = lg_no_comments_found;
   		}
 		return $all;
	}
	
	// ---------------------------------------------------------------------------
	// 	Get recent articles matching criteria
	
	function recent_articles($where,$limit = '5',$user = '') {
		// global $template;
		// global $session;
		$q = "SELECT * FROM `" . TABLE_PREFIX . "articles` WHERE $where ORDER BY `created` DESC LIMIT $limit";
		$result = $this->run_query($q);
		$all = '';
		$found_pages = 0;
 		while ($row = mysql_fetch_array($result)) {
 			$found_pages++;
 			// Considerations
   			$score = $row['upvoted'] - $row['downvoted'];
   			$category_name = $this->get_category_name_from_id($row['category']);
   			$article_link = $this->prepare_link($row['id'],$row['category'],$row['name']);
   			$article_link = "<a href=\"$article_link\">" . $row['name'] . "</a>";
   			$q = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "comments` WHERE `article`='" . $row['id'] . "' AND `pending`!='1'";
   			$count = $this->get_array($q);
   			$comments = $count['0'];
 			// Changes
 			$special_changes = $this->replace_article_tags($row);
		   	// Render Page
		   	$put_user_data = $this->get_user_data($row['owner']);
		   	$render = $this->render_template('article_panel',$row['owner'],$special_changes,'1','0',$put_user_data);
		   	$all .= $render;
 		}
   		// Anything?
   		if ($found_pages <= 0) {
		   	$all = lg_no_articles_found;
   		}
 		return $all;
	}


	// ---------------------------------------------------------------------------
	// 	Create a list of possible tasks
	
	function task_list($selected = "",$type = "select") {
		$set1 = array(
			'article_add' => 'Create a new page.',
			'article_edit' => 'Edit an page.',
			'article_generate_new' => 'Generate page creation screen.',
			'article_generate_edit' => 'Generate page edit screen.'
		);
		$set2 = array(
			'comment_post' => 'Comment posted.',
			'comment_edit' => 'Comment edited.',
			'comment_delete' => 'Comment deleted.',
			'comment_approve' => 'Comment approved by mod/admin.',
			'comment_status_changed' => 'Comment status changed.',
			'comment_vote' => 'Comment voted (either up or down).',
			'comment_vote_up' => 'Comment up-voted.',
			'comment_vote_down' => 'Comment down-voted.',
			'comment_vote_change' => 'Comment vote changed (either up or down).',
			'comment_vote_changed_down' => 'Comment vote changed down.',
			'comment_vote_changed_up' => 'Comment vote changed up.',
		);
		$set3 = array(
			'ban_user' => 'User banned.',
			'login' => 'User logs in.',
			'logout' => 'User logs out.',
			'lost_password_recovery' => 'User recovers password.',
			'register' => 'User registers.'
		);
	
		// Create the list
		$list = '';
		if ($type == 'select') {
			$list .= "<optgroup label=\"Articles\">";
			foreach ($set1 as $name => $value) {
				if ($selected == $name) {
					$list .= "<option value=\"$name\" selected=\"selected\">$value</option>";
				} else {
					$list .= "<option value=\"$name\">$value</option>";
				}
			}
			$list .= "</optgroup>";
			$list .= "<optgroup label=\"Commenting\">";
			foreach ($set2 as $name => $value) {
				if ($selected == $name) {
					$list .= "<option value=\"$name\" selected=\"selected\">$value</option>";
				} else {
					$list .= "<option value=\"$name\">$value</option>";
				}
			}
			$list .= "</optgroup>";
			$list .= "<optgroup label=\"Accounts\">";
			foreach ($set3 as $name => $value) {
				if ($selected == $name) {
					$list .= "<option value=\"$name\" selected=\"selected\">$value</option>";
				} else {
					$list .= "<option value=\"$name\">$value</option>";
				}
			}
			$list .= "</optgroup>";
		}
		return $list;
	}


	// ---------------------------------------------------------------------------
	// 	Lock a page
	//	type 1 = lock
	//	type 2 = unlock

	function lock_page($page_id,$type,$user_id) {
		// Lock it
		if ($type == '1') {
			$q = "UPDATE `" . TABLE_PREFIX . "articles` SET `locked`='" . time() . "',`locked_to`='" . $this->mysql_clean($user_id) . "' WHERE `id`='" . $this->mysql_clean($page_id) . "' LIMIT 1";
		}
		// Unlock it
		else {
			$q = "UPDATE `" . TABLE_PREFIX . "articles` SET `locked`='0',`locked_to`='0' WHERE `id`='" . $this->mysql_clean($page_id) . "' LIMIT 1";
		}
		$update = $this->update($q);
	}
	
	
	// ---------------------------------------------------------------------------
	// 	Check if page is locked
	//	Return 1 = locked

	function check_lock($page_id,$user_id,$article_lock,$article_user_lock) {
		if (empty($article_lock)) {
 			return '0';
		}
		else if (time()-$article_lock >= 300) {
 			return '0';
		}
		else if ($article_lock != '0') {
			if ($user_id == $article_user_lock) {
				return '0';
			} else {
 				return '1';
 			}
		}
	}


	// ---------------------------------------------------------------------------
	// 	Create a select menu of all categories
	
	function category_select($selected = "0",$category = "0",$pad_left = "0",$selectName = '',$selectId = '') {
 		// Add base category
 		// if $pad_left is above 0 don't do it
 		// because it means we are getting subcategories.
 		$list = '';
 		$put_id = '';
 		if (! empty($selectName)) {
 			if (! empty($selectId)) {
 				$put_id = " id=\"$selectId\"";
 			}
 			$list .= "<select name=\"options[" . $selectName . "]\"$put_id tabindex=\"2\">";
 		}
 		// Padding
 		if ($pad_left == "0") {
	 		if ($selected == "0") {
	 			$list .= "<option value=\"0\" selected=\"selected\">Base Category</option>\n";
	 		} else {
	 			$list .= "<option value=\"0\">Base Category</option>\n";
	 		}
 		}
		// Get other categories
		$q = "SELECT * FROM `" . TABLE_PREFIX . "categories` WHERE `subcat`='$category' AND `base`!='1' ORDER BY `order` ASC";
		$result = $this->run_query($q);
		// Loop through the category and get sub-categories if possible
 		while ($row = mysql_fetch_array($result)) {
 		
 			$temp_hold_pad = $pad_left;
 			$dashes = "";
 			while ($temp_hold_pad > 0) {
 				$dashes .= "--";
 				$temp_hold_pad--;
 			}
 			if ($row['id'] == $selected) {
 				$list .= "<option value=\"" . $row['id'] . "\" selected=\"selected\">" . $dashes . " " . $row['name'] . "</option>\n";
 			} else {
 				$list .= "<option value=\"" . $row['id'] . "\">" . $dashes . " " . $row['name'] . "</option>\n";
 			}
 			
			// Subcategories...
			$q1 = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "categories` WHERE `subcat`='" . $row['id'] . "' AND `base`!='1'";
			$subcategories = $this->get_array($q1);
			if ($subcategories['0'] > 0) {
				$new_pad_left = $pad_left + 1;
				$list .= $this->category_select($selected,$row['id'],$new_pad_left);
			}
 		}
 		if (! empty($selectName)) {
 			$list .= "</select>";
 		}
		return $list;
	}
	

	// ---------------------------------------------------------------------------
	// 	Creates an space-delimited list of all sub-categories in a
	//	category. Used for searching mainly.
	
	function get_subcategories_of($id,$array = '0') {
		$return = array();
	 	// Subcategories...
	 	$return = $id;
	 	$q1 = "SELECT `id` FROM `" . TABLE_PREFIX . "categories` WHERE `subcat`='" . $this->mysql_clean($id) . "' AND `base`!='1'";
	 	$subcategories = $this->run_query($q1);
	 	while ($row = mysql_fetch_array($subcategories)) {
			$return .= "," . $this->get_subcategories_of($row['id']);
	 	}
	 	if ($array == '1') {
	 		$return = explode(',',$array);
	 	}
	 	return $return;
	}

	// ---------------------------------------------------------------------------
	// 	Nest level of a category

	function category_nest_level($id) {
	 	$nest_level = 0;
	 	while ($id != 0) {
		 	$nest_level++;
		 	$q1 = "SELECT `base`,`subcat` FROM `" . TABLE_PREFIX . "categories` WHERE `id`='" . $this->mysql_clean($id) . "'";
		 	$checkit = $this->get_array($q1);
		 	if ($checkit['base'] == '1') {
		 		break;
		 	} else if ($checkit['subcat'] == 0) {
		 		break;
		 	} else {
		 		$id = $checkit['subcat'];
		 	}
	 	}
	 	return $nest_level;
	}

	// ---------------------------------------------------------------------------
	// 	Get an ordered list of all primary categories
	//	in which a sub-category lives.
	
	function get_primary_categories($subcate) {
		$full_list = array();
		while ($subcate != '0') {
			$this_cate = $this->get_category($subcate,'0','subcat');
	 		$full_list[] = $this_cate['subcat'];
	 		$subcate = $this_cate['subcat'];
	 	}
	 	return $full_list;
	}


	// ------------------------------------
	// 	Get a plugin
	
	function get_plugin($name,$getoptions = '1') {
		// Basics
 		$q = "SELECT * FROM `" . TABLE_PREFIX . "widgets` WHERE `filename`='" . $this->mysql_clean($name) . "' AND `type`='5'";
		$plugin_data = $this->get_array($q);
		$plugin_data['details'] = $plugin_data['options'];
		// Options?
		if ($getoptions == '1') {
			$options = $this->get_plugin_options($name);
			$plugin_data['options'] = $options;
		}
		return $plugin_data;
	}
	
	// ------------------------------------
	// 	Get a plugin's options
	
	function get_plugin_options($name) {
 		$q = "SELECT * FROM `" . TABLE_PREFIX . "options` WHERE `plugin`='" . $this->mysql_clean($name) . "' AND `type`='3'";
		$results = $this->run_query($q);
		$options_array = array();
		while ($row = mysql_fetch_array($results)) {
			$options_array[] = $row;
		}
		return $options_array;
	}
	
	// ------------------------------------
	// 	Get a plugin's options
	
	function plugin_option($id,$plugin = '') {
		if (! empty($plugin)) {
			$add_where = "AND `plugin`='" . $this->mysql_clean($plugin) . "'";
		}
 		$q = "SELECT `value` FROM `" . TABLE_PREFIX . "options` WHERE `key`='" . $this->mysql_clean($id) . "' $add_where";
		$results = $this->get_array($q);
		return $results['value'];
	}
	
	
	// ---------------------------------------------------------------------------
	// 	Get the article sidebar.
	//	$article is an array of the database row.
	//	Only called if the user is logged in and
	//	we aren't formatting a widget.
	
	function article_sidebar($article = '',$just_article = '0',$category = '',$cp = '0') {
		global $privileges;
		global $user;
		global $user_data;
		// global $session;
		if ($user) {
	   		$right_menu = "";
	   		$found_options = '';
	   		$options = '';
	   		
	   		$icon_style = "vertical-align:middle;padding: 0 8px 0 0;";
	   		$img_folder = URL . "/templates/html/_imgs/manage_bar";
	   		$a_style = "margin-right:8px;";
	   		
	   		// Check for stripped privileges
	   		if ($privileges['is_admin'] != '1') {
				$check_group_stripped = $this->check_usertype_stripped(BD_CATEGORY_VIEWING,$user_data['type'],'all');
				if ($check_group_stripped == '1') {
					$privileges = array();
				}
			}
			
	   		// User privileges?
			// $privileges = $this->get_user_privileges($user);
			if ($privileges['is_admin'] == "1") {
				$found_options = "1";
				if ($cp == '1') {
					$options .= "<li id=\"bd_manage_admin_cp\" style=\"background-color:#000;\"><a href=\"" . ADMIN_URL . "/functions/return_to_last_viewed.php\"><img src=\"$img_folder/control_panel.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . lg_cp . "\" title=\"" . lg_cp . "\" style=\"$icon_style\" />" . lg_cp . "</a></li>";
				} else {
					$options .= "<li id=\"bd_manage_admin_cp\"><a href=\"" . ADMIN_URL . "\"><img src=\"$img_folder/control_panel.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . lg_cp . "\" title=\"" . lg_cp . "\" style=\"$icon_style\" />" . lg_cp . "</a></li>";
				}
			}
			if (! empty($article)) {
		   	//	if (($category['allow_article_creation'] == "1" && $privileges['can_create_articles'] == "1") || $privileges['is_admin'] == "1") {

				$src1 = URL . '/templates/html/_imgs/manage_bar/expand.png';
				$src2 = URL . '/templates/html/_imgs/manage_bar/contract.png';
				

		   			$found_options = "1";
		   			$add_options_to_page = '';
		   			if ($privileges['can_create_articles'] == "1" || $privileges['is_admin'] == "1") {
		   				$add_options_to_page .= "<a href=\"#\" onclick=\"return editArticle('new');\"><img src=\"$img_folder/add.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . lg_new . " " . lg_article . "\" title=\"" . lg_new . " " . lg_article . "\" style=\"$icon_style\" /></a>";
		   			}
		   			if ($privileges['can_alter_articles'] == "1" || $user == $article['owner'] || $privileges['is_admin'] == "1") {
		   				$add_options_to_page .= "<a href=\"#\" onclick=\"return editArticle('" . $article['id'] . "');\"><img src=\"$img_folder/edit.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . lg_edit . " " . lg_article . "\" title=\"" . lg_edit . " " . lg_article . "\" style=\"$icon_style\" /></a>";
		   			}
		   			if (! empty($add_options_to_page)) {
			   			$options .= "<li id=\"bd_manage_article_new\">";
			   			$options .= "<span style=\"$a_style\">" . lg_article . "</span>";
		   				$options .= $add_options_to_page;
		   			}
		   			
		   			if ($privileges['is_admin'] == "1") {
			   			//if (BD_CATEGORY_VIEWING != '0') {
				   		$options .= "<a href=\"#\" onclick=\"return makeCategoryHomepage('" . $article['id'] . "');\"><img src=\"$img_folder/make_homepage.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"Make this page the category homepage\" title=\"Make this page the category homepage\" style=\"$icon_style\" /></a>";
			   			//}
			   			$options .= "<a href=\"#\" onclick=\"return delete_cate_or_page('page','" . $article['id'] . "');\"><img src=\"$img_folder/delete.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . lg_delete . " " . lg_article . "\" title=\"" . lg_delete . " " . lg_article . "\" style=\"$icon_style\" /></a></li>";
		   			}
		   			
		   		/*
		   		if ($privileges['can_alter_articles'] == "1" || $user == $article['owner'] || $privileges['is_admin'] == "1") {
		   			$found_options = "1";
		   			$options .= "<li id=\"bd_manage_article_edit\"><a href=\"#\" onclick=\"editArticle('" . $article['id'] . "');\">" . lg_edit . " " . lg_article . "</a></li>";
		   			// <a href=\"" . ADMIN_URL . "/index.php?l=article_edit&id=" . $article['id'] . "\">Edit " . lg_article . "</a>
		   		}
		   		*/
		   		
		   		if ($privileges['can_alter_categories'] == "1" || $privileges['is_admin'] == "1") {
					$fsr = '1';
		   			$options .= "<li id=\"bd_manage_category_new\"><span style=\"$a_style\">" . lg_category . "</span><a href=\"#\" onclick=\"addCategory('" . $article['category'] . "');\"><img src=\"$img_folder/add.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . lg_new . " " . lg_category . "\" title=\"" . lg_new . " " . lg_category . "\" style=\"$icon_style\" /></a><a href=\"" . ADMIN_URL . "/index.php?l=category_edit&id=" . $article['category'] . "\"><img src=\"$img_folder/edit.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . lg_edit . " " . lg_category . "\" title=\"" . lg_edit . " " . lg_category . "\" style=\"$icon_style\" /></a><a href=\"" . ADMIN_URL . "/index.php?l=home&help=site_map_area&category=" . $article['category'] . "\"><img src=\"$img_folder/hierarchy.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"Organize Category\" title=\"Organize Category\" style=\"$icon_style\" /></a><a href=\"" . ADMIN_URL . "/index.php?l=defaults&category=" . $article['category'] . "\"><img src=\"$img_folder/settings.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"Edit category's default page settings\" title=\"Edit category's default page settings\" style=\"$icon_style\" /></a><a href=\"#\" onclick=\"return delete_cate_or_page('category','" . $article['category'] . "');\"><img src=\"$img_folder/delete.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . lg_delete . " " . lg_category . "\" title=\"" . lg_delete . " " . lg_category . "\" style=\"$icon_style\" /></a>
		   			</li>
		   			";
		   		}
		   		
		   		// MEDIA LIBRARY
		   		if ($privileges['upload_files'] == "1" || $privileges['upload_images'] == "1") {
			   		$options .= "<li id=\"bd_manage_category_media\"><span style=\"$a_style\">" . lg_media_gallery . "</span>";
			   		if ($privileges['upload_files'] == "1") {
			   			$options .= "<a href=\"#\" onclick=\"return uploadFile('image','cms');\"><img src=\"$img_folder/gallery.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"Images\" title=\"Images\" style=\"$icon_style\" /></a>";
			   		}
			   		if ($privileges['upload_images'] == "1") {
			   			$options .= "<a href=\"#\" onclick=\"return uploadFile('file','cms');\"><img src=\"$img_folder/downloads.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"Downloadable Files\" title=\"Downloadable Files\" style=\"$icon_style\" /></a>";
			   		}
			   		$options .= "</li>";
			   	}
		   		
	   		}
	   		// Should it be displayed?
	   		if ($this->get_option('display_userbar_to_all') == '1') {
	   			$display = '1';
	   		}
	   		else if ($privileges['is_admin'] == "1") {
	   			$display = '1';
	   		}
	   		else {
	   			$display = '0';
	   		}
	   		if ($display == '1') {
		   		if ($just_article == '1') {
					$right_menu .= "\n\n<ul id=\"bd_manage_bar\" class=\"bd_options\" style=\"font-family:tahoma, arial;font-size:12px;\">";
		   		}
		   		$user_link = $this->get_user_link($user);
	   			$right_menu .= "<li class=\"first\">" . lg_welcome . " <a href=\"$user_link\">$user</a> (<a href=\"#\" onclick=\"return logout();\">Logout</a>)</li>";
	   			// (<a href=\"#\" onclick=\"logout();return false;\">" . lg_logout . "</a>)
	   			$right_menu .= $options;
		   		if ($just_article == '1') {
					$right_menu .= "</ul>\n\n";
		   		}
				// Notices for this user
				$notices = $this->get_total_notices($user_data['id']);
				$right_menu .= "<div id=\"bd_manage_notices\"><div id=\"bd_notice_number\"><a href=\"" . $user_data['user_link'] . "/notices\">$notices</a></div></div>";
	   		}
   		}
   		return $right_menu;
	}
	
	
	
	// ---------------------------------------------------------------------------
	// 	Create a category
	
	function create_category($name,$subcategory,$options = '') {
		global $privileges;
		global $user;
		
		// User logged in?
		if (empty($user)) {
			echo "0+++" . lg_login_to_use_feature;
			exit;
		}
		
		// Privileges?
		if ($privileges['can_create_categories'] != '1' && $privileges['is_admin'] != '1') {
			echo "0+++" . lg_privilieges_req;
			exit;
		}
		
		// Name?
		if (empty($name)) {
			echo "0+++Name is required.";
			exit;
		}
		
		// Options
		$insert = '';
		$values = '';
		if (empty($options)) {
			$options = $this->get_category($subcategory,'0');
		}
		$insert = ",`allow_article_creation`,`template`,`public`,`base`";
		$values = ",'" . $options['allow_article_creation'] . "','" . $options['template'] . "','" . $options['public'] . "','0'";
		
		// Create the category
		$q = "INSERT INTO `" . TABLE_PREFIX . "categories` (`name`,`subcat`$insert) VALUES ('" . $this->mysql_clean($name) . "','" . $this->mysql_clean($subcategory) . "'$values)";
		$insert_id = $this->insert($q);
		
		// Create new homepage?
		if ($this->get_option('direct_link') == '1') {
			$categoryDets = array(
				'id' => $insert_id,
				'name' => $name,
				'subcat' => $subcategory,
			);
			$page = $this->no_homepage_article($categoryDets);
		}
		
		// Cache Category List
		if ($this->get_option('cache_category_list') == '1') {
			$cache_list = $this->category_tree('','1','1');
		}
		
		// Reply
		echo "1+++Saved!";
		exit;
	}
	
	
	// ---------------------------------------------------------------------------
	// 	Generate a category list if
	// 	no homepage selected for category
	//	and direct linking is on.
	//	$category = category array
	
	function no_homepage_article($category) {
		if ($this->get_option('direct_link') == '1') {
			// Main Admin
			$main_admin = $this->get_username_from_id('1');
			// Create Widget
			
			$array_opts = array(
				'category' => $category,
				'type' => 'standard',
				'limit' => '10000',
				'columns' => '1',
				'order' => 'order',
				'dir' => 'ASC'
			);
			
			$q1 = "
				INSERT INTO `" . TABLE_PREFIX . "widgets` (`date`,`name`,`owner`,`type`,`category`,`active`,`options`)
				VALUES ('" . $this->current_date() . "','Category List for " . $this->mysql_clean($category['name']) . "','" . $main_admin . "','1','" . $category['id'] . "','1','" . serialize($array_opts) . "')
			";
			$insert = $this->insert($q1);
			// Create Page
			// Get category defaults
			$item_options = $this->get_item_options($category['id'],'category');
			// Insert page
			$q = "
				INSERT INTO `" . TABLE_PREFIX . "articles` (`category`,`owner`,`name`,`content`,`created`,`last_updated`,`allow_comments`,`allow_ratings`,`show_stats`,`login_to_view`,`public`,`display_on_sidebar`,`email_comment_posted`,`sharing_options`,`max_threading`,`login_to_comment`,`comment_hide_threshold`,`allow_comment_edits`,`default_comment_type_show`,`template`,`comment_thread_style`,`format_type`)
				VALUES ('" . $category['id'] . "','" . $main_admin . "','" . $category['name'] . lg_directory . "','{-$insert-}','" . $this->current_date() . "','" . $this->current_date() . "','0','0','0','" . $item_options['login_to_view'] . "','" . $item_options['public'] . "','" . $item_options['display_on_sidebar'] . "','" . $item_options['email_comment_posted'] . "','" . $item_options['sharing_options'] . "','" . $item_options['max_threading'] . "','" . $item_options['login_to_comment'] . "','" . $item_options['comment_hide_threshold'] . "','" . $item_options['allow_comment_edits'] . "','" . $item_options['default_comment_type_show'] . "','" . $item_options['template'] . "','" . $item_options['comment_thread_style'] . "','" . $item_options['format_type'] . "')
			";
			$insert1 = $this->insert($q);
			// Set as homepage
			$q = "UPDATE `" . TABLE_PREFIX . "categories` SET `home_article`='$insert1' WHERE `id`='" . $category['id'] . "' LIMIT 1";
			$update = $this->update($q);
			// Return Page 1
			return $insert1;
		} else {
			return '';
		}
	}
	
	
	// ---------------------------------------------------------------------------
	// 	Find duplicate article names
	
	function find_duplicates($name,$ignore_id,$category) {
		$q = "SELECT id FROM `" . TABLE_PREFIX . "articles` WHERE `name`='" . $this->mysql_clean($name) . "' AND `id`!='" . $this->mysql_clean($ignore_id) . "' AND `category`='" . $this->mysql_clean($category) . "' LIMIT 1";
		$dup_name = $this->get_array($q);
		if (! empty($dup_name['id'])) {
			echo "0+++" . lg_article_dup_name;
			exit;
		}
	}
	
	
	// ---------------------------------------------------------------------------
	// 	Get text between 2 strings

	function text_between($string, $start, $end){
		$string = " " . $string;
		$ini = strpos($string,$start);
		if ($ini == 0) return "";
		$ini += strlen($start);
		$len = strpos($string, $end, $ini) - $ini;
		return substr($string, $ini, $len);
	}
	

	// ---------------------------------------------------------------------------
	// 	Get a comment status settings
	
	function get_status_settings($status,$select = '*') {
		$q = "SELECT $select FROM `" . TABLE_PREFIX . "comment_statuses` WHERE `id`='" . $this->mysql_clean($status) . "' LIMIT 1";
		$status = $this->get_array($q);
		return $status;
	}
	
	
	// ---------------------------------------------------------------------------
	// 	Get point value information
	
	function get_point_value($id,$select = '*') {
		$q = "SELECT $select FROM `" . TABLE_PREFIX . "point_values` WHERE `id`='" . $this->mysql_clean($id) . "' LIMIT 1";
		$pv = $this->get_array($q);
		return $pv;
	}
	
	// ---------------------------------------------------------------------------
	// 	Get badge information
	
	function get_badge($id,$select = '*') {
		$q = "SELECT $select FROM `" . TABLE_PREFIX . "badges` WHERE `id`='" . $this->mysql_clean($id) . "' LIMIT 1";
		$badge = $this->get_array($q);
		return $badge;
	}
	
	// ---------------------------------------------------------------------------
	// 	Generate a list of all comment statuses
	
	function get_comment_statuses($selected) {
 		// Add base category
 		// if $pad_left is above 0 don't do it
 		// because it means we are getting subcategories.
    		if ($selected == "0") {
    			$list .= "<option value=\"0\" selected=\"selected\">Standard Comments</option>\n";
    		} else {
    			$list .= "<option value=\"0\">Standard Comments</option>\n";
    		}
		// Get other categories
		$q = "SELECT `id`,`title` FROM `" . TABLE_PREFIX . "comment_statuses`";
		$result = $this->run_query($q);
		// Loop through the category and get sub-categories if possible
 		while ($row = mysql_fetch_array($result)) {
 			$temp_hold_pad = $pad_left;
 			$dashes = "";
 			if ($row['id'] == $selected) {
 				$list .= "<option value=\"" . $row['id'] . "\" selected=\"selected\">" . $row['title'] . "</option>\n";
 			} else {
 				$list .= "<option value=\"" . $row['id'] . "\">" . $row['title'] . "</option>\n";
 			}
 		}
		return $list;
	}
	
	
	// -----------------------------------------------------------------------------
	// 	Get total comments for a status type
	
	function get_total_comments($status_type,$article) {
		$q = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "comments` WHERE `status`='" . $this->mysql_clean($status_type) . "' AND `article`='" . $this->mysql_clean($article) . "' LIMIT 1";
		$count = $this->get_array($q);
		return $count['0'];
	}
	
	// -----------------------------------------------------------------------------
	// 	Get total articles in a category
	
	function get_total_articles($category) {
		$q = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "articles` WHERE `public`='1' AND `display_on_sidebar`='1' AND `category`='" . $category . "'";
		$count = $this->get_array($q);
		return $count['0'];
	}
	
	
	// -----------------------------------------------------------------------------
	// 	Get a comment's information
	
	function get_a_comment($id,$select = '*') {
		if (empty($select)) {
			$select = '*';
		}
		$q = "SELECT $select FROM `" . TABLE_PREFIX . "comments` WHERE `id`='" . $this->mysql_clean($id) . "' LIMIT 1";
		$comment = $this->get_array($q);
		// Score
		$score = $comment['up'] - $comment['down'];
		$comment['score'] = $score;
		return $comment;
	}

	
	// -----------------------------------------------------------------------------
	// 	E-Mail owner of an article about a comment
	//	being posted or approved.
	
	function comment_email_article_owner($comment_id,$comment_info = "",$article = "",$email_user = "",$reply = '0') {
 		// global $template;
 		if (empty($comment_info)) {
 			$comment_info = $this->get_a_comment($comment_id);
 		}
 		if (empty($article)) {
 			$article = $this->get_article($comment_info['article'],'1','*','0','0','0');
 		}
 		// E-mail owner and user who posted it?
 		if ($article['email_comment_posted'] == "1") {
			if ($comment_info['pending'] != "1") {
				$comment_status = "Live";
			} else {
				$comment_status = "Pending";
			}
 			$link = $this->prepare_link($article['id'],$article['category'],$article['name']);
 			$special_changes = array(
 				'%comment%' => $this->format_comment($comment_info['comment']),
 				'%status%' => $comment_status,
 				'%article%' => $article['name'],
 				'%article_link%' => $link,
 				'%posted_by%' => $comment_info['user']
 			);
 			if ($reply == '1') {
 		    		$sent1 = $this->send_template($email_user,'comment_posted','',$special_changes);
 			} else {
    				// Article owner?
    				// Only if the comment isn't pending, otherwise
    				// the article owner will be notified once
    				// the comment is approved.
    				if (! empty($article['owner']) && $comment_info['pending'] != '1') {
    					$sent = $this->send_template($article['owner'],'comment_posted_to_article','',$special_changes);
    				}
 			}
 		}
	}
	
	
	
	// -----------------------------------------------------------------------------
	// 	Get all comments for an article
	
	function get_comments($article,$user,$article_data = '',$write_cache = '0') {
   		// $user = $this->check_logged();
		// $privileges = $this->get_user_privileges($user);
		// Thread Style?
		if (BD_MOBILE == '1') {
			$thread_style = 'tree';
		} else {
			if (! empty($article_data['comment_thread_style'])) {
				$thread_style = $article_data['comment_thread_style'];
			} else {
				$thread_style = $this->get_option('thread_style');
			}
		}
		global $privileges;
		$proceed = '1';
		$cache_file = PATH . "/generated/comments-" . $article . ".php";
		if ($write_cache != '1') {
			if ($this->get_option('cache_comments') == '1') {
				$caching = '1';
			} else {
				$caching = '0';
			}
			// Cached comments?
	 		if ($caching == '1' && $force_cache != '1') {
				if (file_exists($cache_file)) {
					$proceed = '0';
					$force_cache = '0';
					ob_start();
					include($cache_file);
					$comments = ob_get_contents();
					ob_end_clean();
					return $comments;
				} else {
					$proceed = '1';
					$force_cache = '1';
				}
			}
		} else {
			$caching = '1';
		}
		
   		// --------------------------------------------------
   		// 	Render Comments and Comment Types
		
		if ($proceed == '1') {
			// Article data?
			if (empty($article_data)) {
				$article_data = $this->get_article($article,'1','*','0','0','0');
			}
	   		// Return to main thread
	   		if ($thread_style == 'Forum') {
	   			$return_comments = "<div id=\"bd_subcom_return\" style=\"display:none;\"><a href=\"#\" onclick=\"return hideSubcomments();\">" . lg_comment_return . "</a></div>";
    				$order_put = " `date` DESC";
	   		} else {
	   			$return_comments = "";
    				$order_put = " `date` ASC";
	   		}
	   		// Status types
	 		if (empty($article_data['default_comment_type_show'])) {
	 			 $article_data['default_comment_type_show'] = '0';
	 		} else {
		 		// Comments for main type?
				$q = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "comments` WHERE `status`='" . $article_data['default_comment_type_show'] . "' AND `article`='" . $this->mysql_clean($article) . "'";
				$found_type = $this->get_array($q);
				if ($found_type['0'] <= 0) {
					$article_data['default_comment_type_show'] = '0';
				}
			}
			
			// --------------------------------------------------
			// 	Status Types
			
			$types = array();
			$q1 = "SELECT `status` FROM `" . TABLE_PREFIX . "comments` WHERE `article`='" . $this->mysql_clean($article) . "' ORDER BY `status` ASC";
			$result1 = $this->run_query($q1);
	 		while ($row1 = mysql_fetch_array($result1)) {
	 			$total_comments = 0;
	   			if (! in_array($row1['status'],$types)) {
	   				$total_comments = $this->get_total_comments($row1['status'],$article);
	   				// $total_comments = $this->comment_total_replies($row['id'],'',$row1['status']);
	   				$status_details = $this->get_status_settings($row1['status']);
	   				$types[] = $row1['status'];
	   				if ($row1['status'] == "0") {
	   					$status_name = lg_comment . " ($total_comments)";
	   				} else {
	   					$status_name = $status_details['title'] . " ($total_comments)";
	   				}
	 				$status_ul .= "<li id=\"statusActive" . $row1['status'] . "\"";
	 				if ($row1['status'] == $article_data['default_comment_type_show']) {
	 					$status_ul .= " class=\"on\"";
	 				}
	 				$status_ul .= "><a href=\"#\" onclick=\"return show_status('" . $row1['status'] . "');\">$status_name</a></li>";
	   			}
	 		}
	 		$found_a_comment = '';
	 		if (! empty($status_ul)) {
	 			$return_comments .= "<ul id=\"bd_statusTypes\">";
	 			$return_comments .= $status_ul;
	 			$return_comments .= "</ul>";
	 		}
	 		
			// --------------------------------------------------
			// 	Start Comments
	 		// 	Get the comments belonging to each
	 		//	comment type.
	 		
	 		$comments_as_types = "<div id=\"bd_all_comments\">";
	   		
	 		foreach ($types as $aType) {
	 			$comments_as_types .= "<div id=\"status" . $aType . "\"";
	 			if ($aType == $article_data['default_comment_type_show']) {
	 				$comments_as_types .= " style=\"display:block;\"";
	 			} else {
	 				$comments_as_types .= " style=\"display:none;\"";
	 			}
	 			$comments_as_types .= ">";
				// Process comments
				$q = "SELECT * FROM `" . TABLE_PREFIX . "comments` WHERE `status`='$aType' AND `article`='" . $this->mysql_clean($article) . "' AND `subcomment`='0' ORDER BY (`up`-`down`) DESC, $order_put";
				$result = $this->run_query($q);
		 		while ($row = mysql_fetch_array($result)) {
		 			if (($row['pending'] == "1" && $user == $row['user']) || $privileges['is_admin'] == "1" || $privileges['can_alter_comments'] == "1" || $row['pending'] != '1') {
		 				$found_a_comment = "1";
		 				$comments_as_types .= $this->render_comment($row['id'],$row,'',$user,'0',$article_data,$caching);
		 			}
				}
	 			$comments_as_types .= "</div>";
	 		}
	 		
	 		// No comments found...
	 		if ($found_a_comment != "1") {
	 			$comments_as_types .= "<div id=\"bd_no_comments\" class=\"bd_comment_none\">" . lg_no_comments . "</div>";
	 		}
	 		
 			$comments_as_types .= "</div>";
	 		
 		} // $proceed = '1';
 		
		// --------------------------------------------------
 		// 	Add comments to the final output.
 		
 		$return_comments .= $comments_as_types;
 		
		// --------------------------------------------------
 		// 	Cache?
 		
 		if ($caching == '1' && $write_cache = '1') {
			$this->write_file($cache_file,$return_comments);
   			ob_start();
   			include($cache_file);
   			$comments = ob_get_contents();
   			ob_end_clean();
			return $comments;
		}
		
		// --------------------------------------------------
 		// 	No cache... return results.
 		
		else {
			return $return_comments;
		}
		
	}
	
	
	// -----------------------------------------------------------------------------
	// 	Render a specific comment and that comment's subcomments
	
	function render_comment($id,$row,$force_show = "0",$user = "",$padding_left = "0",$article_data = '',$force_cache = '0') {

		global $privileges;
		global $theme;
		global $user;
		
		$show_comment = '0';
		
		// Some options
		$comment_hide_threshold = $article_data['comment_hide_threshold'];
		$login_to_comment = $article_data['login_to_comment'];
		$allow_comment_edits = $article_data['allow_comment_edits'];
		$max_threading = $article_data['max_threading'];
		
		// Comment Style
		if (BD_MOBILE == '1') {
			$thread_style = 'tree';
		} else {
			if (! empty($article_data['comment_thread_style'])) {
				$thread_style = $article_data['comment_thread_style'];
			} else {
				$thread_style = $this->get_option('thread_style');
			}
		}
		
		// To avoid issue detecting thread style
		$hold_thread_style = $thread_style;
		$thread_style = strtolower($thread_style);
		if ($thread_style == 'tree') {
			$template_name = 'comment_entry_tree';
		} else {
			$template_name = 'comment_entry_forum';
		}
		
		// Pending?
	    	if ($row['pending'] != "1") {
	    		$show_comment = 1;
	    	}
	    	else if ($row['pending'] == '1' && ($privileges['is_admin'] == '1' || $privilieges['can_alter_comments'] == '1')) {
	    		$show_comment = 1;
	    	}

	    	if ($show_comment == '1') {
	    	
	    		// Deleted?
	    		if ($row['deleted'] != '0000-00-00 00:00:00') {
	    			$deleted = '1';
	    		} else {
	    			$deleted = '0';
	    		}
	    	
	   		// Has this user voted?
	   		$total = $row['up'] - $row['down'];
	   		$cast_vote = $this->check_user_vote($row['id'],$_SERVER['REMOTE_ADDR'],$user);
			
			// Hidden?
			if ($total <= $comment_hide_threshold && $force_show != "1") {
				$return_comment .= "<div id=\"hiddenText" . $row['id'] . "\" class=\"small\"><i>Comment hidden (<a href=\"#\" onclick=\"return showComment('" . $row['id'] . "');\">show</a>)</i></div>";
				$return_comment .= "<div id=\"hidden" . $row['id'] . "\" style=\"display:none;\">" . $this->format_comment($row['comment']) . "</div>";
			} else {
				$return_comment = $this->format_comment($row['comment']);
		    		// This works because only pending comments
		    		// belonging to this user will be displayed.
		    		// Other users cannot see pending comments.
		    		if ($row['pending'] == "1") {
		    			$return_comment .= "<p id=\"pending" . $row['id'] . "\"><b>This comment is pending review!</b></p>";
		    		}
	    		}
	    		$row['comment'] = $return_comment;
			
			// Primary callers
			$special_changes = $this->replace_comment_tags($row);
	   		
	   		// Additional special callers
	   		if ($padding_left == '0') {
	   			$addClass = 'bd_a_main_comment';
		   		$style = " style=\"border:0 !important;padding: 0 !important;margin: 0 !important;\"";
	   		} else {
	   			$addClass = 'bd_a_subcomment';
	   			$style = '';
	   		}
	   		// Hidden?
	   		$total = $row['up'] - $row['down'];
	   		if ($total <= $comment_hide_threshold && $force_show != "1") {
	   			$addClass .= ' bd_comment_hidden';
	   		}
			$special_changes['%add_class%'] = $addClass;
			$special_changes['%com_style%'] = $style;
			
			// Voted?
	 		if ($cast_vote == "1") {
				$special_changes['%voted_up%'] = 'voted';
				$special_changes['%voted_down%'] = '';
	 		}
	 		else if ($cast_vote == "-1") {
				$special_changes['%voted_up%'] = '';
				$special_changes['%voted_down%'] = 'voted';
	 		}
	 		else {
				$special_changes['%voted_up%'] = '';
				$special_changes['%voted_down%'] = '';
	 		}
	 				
	 		// Comment Options
			if ($force_cache == '1') {
	    			$comment_options = "\n" . '<?php' . "\n" . 'echo show_comment_options("' . $row['user'] . '","' . $row['id'] . '","' . $row['article'] . '","' . addslashes($row['comment']) . '","' . $deleted . '","' . $row['pending'] . '","' . $max_threading . '","' . $padding_left . '","' . $hold_thread_style . '");' . "\n" . "?>\n";
	    		} else {
	    			$comment_options = show_comment_options($row['user'],$row['id'],$row['article'],$row['comment'],$deleted,$row['pending'],$max_threading,$padding_left,$hold_thread_style);
	    		}
	    		
			$special_changes['%comment_options%'] = $comment_options;
	    		
	    		// --------------------------
	   		// Found subcomments?
	   		$subcoms = 0;
	   		$total_subcomments = 0;
	   		if ($thread_style == 'forum') {
	   			if ($padding_left == '0') {
					$total_subcomments = $this->comment_total_replies($row['id']);
					// $force_show is mainly used when
					// a new comment is posted.
					if ($force_show != '1') {
	   					$expand_code = "<div class=\"expandCommentThread\" onclick=\"showSubcomments('" . $row['id'] . "');\">
								<span class=\"bd_sc_replies_text\">" . lg_replies . "</span>
								<span id=\"currentReplies" . $row['id'] . "\" class=\"bd_sc_replies\">" . $total_subcomments . "</span>
							</div>";
					} else {
	   					$expand_code = '';
					}
	   			} else {
	   				$expand_code = '';
	   			}
				$special_changes['%expand_code%'] = $expand_code;
	   			$subcoms = '1';
	   		} else {
				$special_changes['%expand_code%'] = '';
		   		// Subcomments for this comment?
		   		$q9 = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "comments` WHERE `article`='" . $row['article'] . "' AND `subcomment`='" . $row['id'] . "' AND `pending`!='1'";
		   		$count = $this->get_array($q9);
	   			if ($count['0'] > 0 && $padding_left < $max_threading) {
	   				$subcoms = '1';
	   			}
	   		}
	   		
	   		if ($subcoms == '1') {
	   			$padding_left++;
		   		$return_subcomments = $this->get_sub_comments($row['article'],$row['id'],$padding_left,$user,$article_data,$force_cache); 
		    		if (! empty($return_subcomments) || $thread_style == 'forum') {
		    			if ($thread_style == 'forum') {
		    				if ($padding_left > 0) {
		    					$display = 'none';
		    				} else {
		    					$display = 'block';
		    				}
			    			$return_subcomments = "<div id=\"subcomments" . $row['id'] . "\" style=\"display:$display;\" class=\"bd_discussion_bubble\">$return_subcomments</div>";
		    			} else {
		   				$status_details = $this->get_status_settings($row['status']);
			    			if ($row['contract_subcomments'] == "1" || $status_details['contract_subcomments'] == "1") {
			    				$return_subcomments = "<div id=\"subcommentsTop" . $row['id'] . "\" class=\"bd_display_discussion\"><a href=\"#\" onclick=\"return expandDiscussion('" . $row['id'] . "');\">" . lg_expand_discussion . "</a></div><div id=\"subcomments" . $row['id'] . "\" style=\"display:none;\" class=\"bd_discussion_bubble\">$return_subcomments</div>";
			    			} else {
			    				$return_subcomments = "<div id=\"subcomments" . $row['id'] . "\" class=\"bd_discussion_bubble\">$return_subcomments</div>";
			    			}
		    			}
		    		}
				$special_changes['%subcomments%'] = $return_subcomments;
	    		} else {
				$special_changes['%subcomments%'] = '';
	    		}
	    		
	    		// --------------------------
	    		
			$rendered_template = $this->render_template($template_name,$user,$special_changes,'1');

	    	} else {
	    		$rendered_template = '';
	    	}
	    	
		return $rendered_template;
	    	
	}
	
	
	// -----------------------------------------------------------------------------
	// 	Check user vote
	
	function check_user_vote($comment_id,$ip,$user) {
		$qA = "SELECT `rating` FROM `" . TABLE_PREFIX . "comment_ratings` WHERE `comment`='" . $comment_id . "' AND (`ip`='" . $ip . "' OR `user`='" . $user . "') LIMIT 1";
		$cast_vote = $this->get_array($qA);
		return $case_vote['rating'];
	}
	
	
	// -----------------------------------------------------------------------------
	// 	Get all subcomments for a comment
	
	function get_sub_comments($article,$comment,$padding_left,$user,$article_data = '',$force_cache = '0') {
   		// Subcomments for this comment
   		$return_subcomments = "";
		// Thread Style?
		if (BD_MOBILE == '1') {
			$thread_style = 'tree';
		} else {
			if (! empty($article_data['comment_thread_style'])) {
				$thread_style = $article_data['comment_thread_style'];
			} else {
				$thread_style = $this->get_option('thread_style');
			}
		}
   		// Threading style?
		if ($thread_style == 'Forum') {
			$q = "SELECT * FROM `" . TABLE_PREFIX . "comments` WHERE `article`='" . $this->mysql_clean($article) . "' AND `subcomment`='" . $this->mysql_clean($comment) . "' AND `pending`!='1' ORDER BY `date` ASC";
		} else {
			$q = "SELECT * FROM `" . TABLE_PREFIX . "comments` WHERE `article`='" . $this->mysql_clean($article) . "' AND `subcomment`='" . $this->mysql_clean($comment) . "' AND `pending`!='1' ORDER BY (`up`-`down`) DESC, `date` ASC";
		}		
		$subcomment = $this->run_query($q);
    		while ($rowA = mysql_fetch_array($subcomment)) {
    			$found_subcomments++;
    			$return_subcomments .= $this->render_comment($rowA['id'],$rowA,'',$user,$padding_left,$article_data,$force_cache);
    		}
    		return $return_subcomments;
	}
	

	// --------------------------------------------------------------------
	// 	Get total replies and subreplies for a primary comment

	function comment_total_replies($id,$current_total = '0',$status = '') {
		// Status?
		if (! empty($status)) {
			$where_add = " AND `status`='$status'";
		}
    		// Add to total
    		$q = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "comments` WHERE `subcomment`='$id'$where_add LIMIT 1";
    		$count_it = $this->get_array($q);
    		$current_total += $count_it['0'];
    		// Keep going?
   	   	$q = "SELECT `subcomment`,`id` FROM `" . TABLE_PREFIX . "comments` WHERE `subcomment`='" . $this->mysql_clean($id) . "'$where_add";
   	   	$get = $this->get_array($q);
    		if (! empty($get['subcomment'])) {
    			$additional = $this->comment_total_replies($get['id'],$current_total);
    			$current_total = $additional;
    		}
    		return $current_total;
	}
	

	// --------------------------------------------------------------------
	// Update a comment and all subcomments for that comment

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
	
	// --------------------------------------------------------------------
	// 	Update sub-comments for a primary
	//	comment.
	/*
	function update_subcomments($id,$status = '',$article = '') {
	
    		$q1 = "UPDATE `comments` SET `status`='$status' WHERE `subcomment`='" . $this->mysql_clean($id) . "'";
    		$update = $this->update($q1);
    		// Keep going?
   	   	$q = "SELECT `subcomment`,`id` FROM `" . TABLE_PREFIX . "comments` WHERE `subcomment`='" . $this->mysql_clean($id) . "'";
   	   	$get = $this->get_array($q);
    		if (! empty($get['subcomment'])) {
    			$additional = $this->update_subcomments($get['id'],$current_total);
    		}
	}
	*/
	
	// -----------------------------------------------------------------------------
	// 	Cache an article
	
	function cache_article($id,$article = '') {
		if ($id) {
			// Get and format article
			if (empty($article)) {
				$article = $this->get_article($id);
			}
			$formatted = $this->format_article($article,'','','','1','','','','Cache Article');
			// Write the file
			$location = PATH . "/generated/article-" . $id . ".php";
			$this->write_file($location,$formatted);
		}
	}
	
	// -----------------------------------------------------------------------------
	// 	Format a comment
	
	function format_comment($input) {
		// Comment comment
		$code_open = "0";
   		$temp_lines = explode("\n",$input);
   		foreach ($temp_lines as $line) {
   			$line = rtrim($line);
   			// Code?
   			if ($code_open == "1") {
	   			$line = str_replace('<','&lt;',$line);
   			} else {
	   			$line = strip_tags($line);
   			}
			$tab_line = strpos($line, "    ");
			if ($tab_line == '0' && $tab_line !== false) {
				$wiki_format = 0;
				$line = ltrim($line,"\t");
				$line = '<pre class="bd_code_plain">' . $line . '</pre>';
			}
   			// Start/Finish?
   			$code_found = strpos($line,'[code]');
   			$code_close_found = strpos($line,'[/code]');
   			if ($code_found !== false) {
   				$code_open = "1";
   				$line = "<div class=\"bd_code\"><pre>" . $line;
		   		$line = str_replace('[code]','',$line);
   			}
   			if ($code_close_found !== false) {
   				$code_open = "0";
   				$line = $line . "</pre></div>";
		   		$line = str_replace('[/code]','',$line);
   			}
	   		$line = $this->run_replace('/\/\/(.*?)\/\//',$line,'<i>','</i>','//');
   			$line = $this->make_url_clickable($line);
			// Links
			$line = $this->do_links($line);
   			// Code?
   			if ($code_open == "1") {
   				$line .= "\n";
   			} else {
   				$line .= "<br />";
   			}
   			$final_lines .= $line;
   		}
   		// Twitter style user replacements
   		$final_lines = $this->find_at_username($final_lines);
   		$final_lines = $this->clickable_tags($final_lines);
	   	$final_lines = $this->run_replace('/__(.*?)__/',$final_lines,'<u>','</u>','__');
	   	$final_lines = $this->run_replace('/\*\*(.*?)\*\*/',$final_lines,'<b>','</b>','**');
   		$final_lines = $this->process_smilies($final_lines);
		return $final_lines;
	}
	
	
	// -----------------------------------------------------------------------------
	//   Replace Smilies
	
	function process_smilies($text) {
		$image_path = THEME_IMAGES . "/smilies/";
		$in = array(
			':)',
			':-)',
			':D',
			':(',
			':|',
			':S',
			'B)',
			":'(",
			'T_T',
			'>:)',
			'=/',
			':O',
			':o',
			':p',
			':P',
			';)'
		);
		$out = array(
			'<img src="' . $image_path . 'smile.png' . '" border="0" class="smilie" alt="Smile" />',
			'<img src="' . $image_path . 'smile.png' . '" border="0" class="smilie" alt="Smile" />',
			'<img src="' . $image_path . 'happy.png' . '" border="0" class="smilie" alt="Big Smile" />',
			'<img src="' . $image_path . 'unhappy.png' . '" border="0" class="smilie" alt="Sad" />',
			'<img src="' . $image_path . 'neutral.png' . '" border="0" class="smilie" alt="Neutral" />',
			'<img src="' . $image_path . 'confused.png' . '" border="0" class="smilie" alt="Confused" />',
			'<img src="' . $image_path . 'cool.png' . '" border="0" class="smilie" alt="Cool" />',
			'<img src="' . $image_path . 'cry.png' . '" border="0" class="smilie" alt="Crying" />',
			'<img src="' . $image_path . 'cry.png' . '" border="0" class="smilie" alt="Crying" />',
			'<img src="' . $image_path . 'evil.png' . '" border="0" class="smilie" alt="Evil" />',
			'<img src="' . $image_path . 'mad.png' . '" border="0" class="smilie" alt="Mad" />',
			'<img src="' . $image_path . 'surprised.png' . '" border="0" class="smilie" alt="Surprised" />',
			'<img src="' . $image_path . 'surprised.png' . '" border="0" class="smilie" alt="Surprised" />',
			'<img src="' . $image_path . 'tongue.png' . '" border="0" class="smilie" alt="Tongue" />',
			'<img src="' . $image_path . 'tongue.png' . '" border="0" class="smilie" alt="Tongue" />',
			'<img src="' . $image_path . 'wink.png' . '" border="0" class="smilie" alt="Wink" />'
		);
		$text = str_replace($in,$out,$text);
		return $text;
	}
	
	
	// -----------------------------------------------------------------------------
	// 	Runs a generic replacement of a pattern
	
	function run_replace($pattern,$input,$start_string,$end_string,$delimiter) {
    		preg_match_all($pattern, $input, $matches);
    		foreach ($matches['0'] as $match) {
    			$no_paragraph = "1";
    			$match_temp = str_replace($delimiter,'',$match);
    			$match_temp = $start_string . $match_temp . $end_string;
    			$input = str_replace($match,$match_temp,$input);
    		}
    		return $input;
	}

	
	// -----------------------------------------------------------------------------
	// Check point requirements
	
	function check_point_reqs($task,$user_running,$acted_on,$custom_data) {
		if (! empty($user_running)) {
			$q = "SELECT `required`,`act_on`,`act_on_id` FROM `" . TABLE_PREFIX . "point_values` WHERE `task`='$task' AND `required`>'0' LIMIT 1";
			$action = $this->get_array($q);
			if (! empty($action['required'])) {
				// global $session;
				$user_id = $this->get_user_id($user_running);
				$user_points = $this->get_user_points('',$user_id);
				if ($user_points < $action['required']) {
					$message = str_replace('%required%',$action['required'],lg_not_enough_points);
					$message = str_replace('%user_points%',$user_points,$message);
					echo "0+++" . $message;
					exit;
				}
			}
		}
	}
	
	
	// -----------------------------------------------------------------------------
	// 	Adds points to a user's score
	
	function add_points($task,$user_running,$acted_on,$custom_data) {
		// First we add points if possible
		// global $session;
		$logged_user_id = $this->get_user_id($user_running);
		$skip = 0;
		$q = "SELECT `points`,`act_on`,`act_on_id` FROM `" . TABLE_PREFIX . "point_values` WHERE `task`='$task' AND `required`='' LIMIT 1";
		$action = $this->get_array($q);
		if (! empty($action['points'])) {
			// Grant points to the user running it.
			if ($action['act_on'] == 'user') {
				$final_user_id = $logged_user_id;
			}
			// Grant points to to owner of $acted_on,
			// which could be an article or comment ID.
			else {
				// Article-related
				if (strpos($task, 'article') !== false) {
					$article = $this->get_article($acted_on,'1','owner','0','0','0');
					$user_lookup = $article['owner'];
				}
				// Comment-related
				else if (strpos($task, 'comment') !== false) {
					$comment = $this->get_a_comment($acted_on);
					$user_lookup = $comment['user'];
					// Status change?
					if ($task == 'comment_status_changed' && ! empty($action['act_on_id'])) {
						if ($custom_data['new_status'] != $action['act_on_id']) {
							$skip = '1';
						}
					}
				}
				// User ID
				if ($skip != '1') {
					$final_user_id = $this->get_user_id($user_lookup);
				}
			}
			// Points
		   	$action_do = "add=" . $action['points'];
			// Now add the points!
			if (! empty($final_user_id) && $skip != '1') {
				// Update user
				$this->update_eav('myScore',$action_do,$final_user_id,'user_id');
				// Point log
				$q1 = "INSERT INTO `" . TABLE_PREFIX . "point_log` (`date`,`user_credited`,`points`,`task`,`acted_on`,`ip`) VALUES ('" . $this->current_date() . "','$final_user_id','" . $action['points'] . "','$task','$acted_on','" . $this->mysql_clean($_SERVER['REMOTE_ADDR']) . "')";
				$insert = $this->insert($q1);
				// New Badge?
				$user_points = $this->get_user_points('',$final_user_id);
				// Check if any score-based badges need to be issued
				$check_badges = $this->check_new_badges($final_user_id,$user_running,$user_points,'score');
			}
		}
		// Next we check if any badges need to be issued
		// that aren't based on score.
		$check_badges_again = $this->check_new_badges($logged_user_id,$user_running,$user_points,$task,$custom_data);
	}
	
	
	// -----------------------------------------------------------------------------
	// 	For linking, will that an article's name
	//	and return the ID.
	
	function get_article_id_from_name($name,$category_id = "") {
		if (strpos($name,'::')) {
			$exp_items = explode('::',$name);
			// $final_name = strtolower(str_replace('_','+',$exp_items['0']));
			$final_name = strtolower($exp_items['0']);
			if (! empty($exp_items['1']) && empty($category_id)) {
				// Get category
				$category_id = $this->get_category_id_from_name($exp_items['1']);
			}
		} else {
			$final_name = $name;
		}
		if (! empty($category_id) || $category_id == '0') {
			$q = "SELECT id FROM `" . TABLE_PREFIX . "articles` WHERE LOWER(name)='" . $this->mysql_clean($final_name) . "' AND `category`='" . $this->mysql_clean($category_id) . "' LIMIT 1";
		} else {
			$q = "SELECT id FROM `" . TABLE_PREFIX . "articles` WHERE LOWER(name)='" . $this->mysql_clean($final_name) . "' LIMIT 1";
		}
		$found_id = $this->get_array($q);
		return $found_id['id'];
	}
	
	// -----------------------------------------------------------------------------
	// 	For linking, will that a category's name
	//	and return the ID.
	
	function get_category_id_from_name($name) {
		$final_name = strtolower(str_replace('_',' ',$name));
		$q = "SELECT id FROM `" . TABLE_PREFIX . "categories` WHERE LOWER(name)='" . $this->mysql_clean($final_name) . "' LIMIT 1";
		$found_id = $this->get_array($q);
		if (! empty($found_id['id'])) {
			return $found_id['id'];
		} else {
			return "";
		}
	}
	
	// -----------------------------------------------------------------------------
	// 	Used when "url_display_type" option is set to "Name"
	
	function get_category_name_from_id($id) {
		if (empty($id)) {
			$fname = "Home";
		} else {
			$q = "SELECT name FROM `" . TABLE_PREFIX . "categories` WHERE `id`='" . $this->mysql_clean($id) . "' LIMIT 1";
			$found_id = $this->get_array($q);
			$fname = $found_id['name'];
		}
		return $fname;
	}
	
	// -----------------------------------------------------------------------------
	// Is there a redirect rule?
	
	function find_redirect_rule($category,$id) {
		$id = strtolower($id);
		$category = strtolower($category);
		if (! empty($category)) {
			$q = "SELECT `new_article_id` FROM `" . TABLE_PREFIX . "article_redirects` WHERE LOWER(`old_article`)='" . $this->mysql_clean($id) . "' AND LOWER(`old_category`)='" . $this->mysql_clean($category) . "' ORDER BY `date` DESC LIMIT 1";
		} else {
			$q = "SELECT `new_article_id` FROM `" . TABLE_PREFIX . "article_redirects` WHERE LOWER(`old_article`)='" . $this->mysql_clean($id) . "' ORDER BY `date` DESC LIMIT 1";
		}
		$redirect = $this->get_array($q);
		if (! empty($redirect['new_article_id'])) {
			return $redirect['new_article_id'];
		} else {
			return '0';
		}
	}
	
	
	// -----------------------------------------------------------------------------
	// 	Used for widgets and templates.
	
	function get_article_name_from_id($id) {
		$q = "SELECT `name` FROM `" . TABLE_PREFIX . "articles` WHERE `id`='" . $this->mysql_clean($id) . "' LIMIT 1";
		$found_id = $this->get_array($q);
		return $found_id['name'];
	}
	
	
	// -----------------------------------------------------------------------------
	// 	Increase a article's view count
	
	function increase_views($id) {
		$q = "UPDATE `" . TABLE_PREFIX . "articles` SET `views`=(`views`+1) WHERE `id`='" . $this->mysql_clean($id) . "' LIMIT 1";
		$this->update($q);
	}
	
	
	// -----------------------------------------------------------------------------
	// 	Add a vote for an article rating.
	
	function add_vote($rating,$user,$article) {
		if (! empty($user)) {
			$q = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "rating` WHERE `user`='$user' AND `article`='$article' LIMIT 1";
			$found = $this->get_array($q);
			if ($found['0'] > 0) {
				$q1 = "UPDATE `" . TABLE_PREFIX . "rating` SET `rating`='$rating' WHERE `user`='$user' AND `article`='$article' LIMIT 1";
				$update = $this->update($q1);
				$calc_rating = $this->update_rating($article);
				return "1|Vote updated.";
			} else {
				$q1 = "INSERT INTO `" . TABLE_PREFIX . "rating` (`article`,`rating`,`ip`,`user`) VALUES ('$article','$rating','" . $_SERVER['REMOTE_ADDR'] . "','$user')";
				$insert = $this->insert($q1);
				$q2 = "UPDATE `" . TABLE_PREFIX . "articles` SET `votes`=(`votes`+1) WHERE `id`='$article' LIMIT 1";
				$update = $this->update($q2);
				$calc_rating = $this->update_rating($article);
				return "1|Vote recorded.";
			}
		} else {
			if ($this->allow_vote_not_logged == "1") {
				$q = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "rating` WHERE `ip`='" . $_SERVER['REMOTE_ADDR'] . "' AND `article`='$article' LIMIT 1";
				$found = $this->get_array($q);
				if ($found['0'] > 0) {
					$q1 = "UPDATE `" . TABLE_PREFIX . "rating` SET `rating`='$rating' WHERE `ip`='" . $_SERVER['REMOTE_ADDR'] . "' AND `article`='$article' LIMIT 1";
					$update = $this->update($q1);
					$calc_rating = $this->update_rating($article);
					return "1|Vote updated.";
				} else {
					$q1 = "INSERT INTO `" . TABLE_PREFIX . "rating` (`article`,`rating`,`ip`) VALUES ('$article','$rating','" . $_SERVER['REMOTE_ADDR'] . "')";
					$insert = $this->insert($q1);
					$q2 = "UPDATE `" . TABLE_PREFIX . "articles` SET `votes`=(`votes`+1) WHERE `id`='$article' LIMIT 1";
					$update = $this->update($q2);
					$calc_rating = $this->update_rating($article);
					return "1|Vote recorded.";
				}
				return "1";
			} else {
				return "0|<a href=\"#\" onclick=\"return showLogin();\">Login</a> to vote.";
			}
		}
	}
	
	// -----------------------------------------------------------------------------
	// 	Update a rating
	
	function update_rating($article) {
		$q = "SELECT SUM(rating) FROM `" . TABLE_PREFIX . "rating` WHERE `article`='$article'";
		$math = $this->get_array($q);
		$q1 = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "rating` WHERE `article`='$article'";
		$count = $this->get_array($q1);
		$rating = round($math['0'] / $count['0'],2);
		$q2 = "UPDATE `" . TABLE_PREFIX . "articles` SET `rating`='$rating' WHERE `id`='$article' LIMIT 1";
		$update = $this->update($q2);
	}

	// -----------------------------------------------------------------------------
	// 	Check if article is favorited.

	function check_favorite($article,$user_id) {
		$q = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "favorites` WHERE `user_id`='" . $user_id . "' AND `article`='" . $article . "'";
		$found = $this->get_array($q);
		if ($found['0'] > 0) {
			return '1';
		} else {
			return '0';
		}
	}

	// -----------------------------------------------------------------------------
	// 	Check if article is favorited.

	function check_follow($article,$user_id) {
		$q = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "following` WHERE `user_id`='" . $user_id . "' AND `article`='" . $article . "'";
		$found = $this->get_array($q);
		if ($found['0'] > 0) {
			return '1';
		} else {
			return '0';
		}
	}
	

	// -----------------------------------------------------------------------------
	// 	E-mail notify users following a page or category
	//	$type = 'comment','article_edit','article_created'
	
	function notify_followers($article_data,$category_id = '',$type,$comment_data = '') {
		if (! empty($category_id) || $category_id == '0') {
			$q = "SELECT `user_id`,`type` FROM `" . TABLE_PREFIX . "following` WHERE `category`='" . $category_id . "'";
		} else {
			$q = "SELECT `user_id`,`type` FROM `" . TABLE_PREFIX . "following` WHERE `article`='" . $article_data['id'] . "'";
		}
		$results = $this->run_query($q);
		while ($row = @mysql_fetch_array($results)) {
				/*
				// Page created
				if ($type == 'article_edit') {
					// Changes
					$special_changes['notify_type'] = 'Page Edited';
				}
				else if ($type == 'article_created') {
					$category_data = $this->get_category($article_data['category']);
					// Additional changes
					$special_changes['category'] = $category_data['name'];
					$special_changes['notify_type'] = 'Page Created';
				}
				else if ($type == 'comment') {
					$special_changes['notify_type'] = 'Comment Posted to Page';
				}
				*/
				// E-Mail user
				$special_changes = $this->replace_article_tags($article_data);
		   		$username = $this->get_username_from_id($row['user_id']);
		   		$email = $this->get_user_email($row['user_id']);
				if ($type == 'article_edit') {
					// Notice
					$addit = $this->add_notice($row['user_id'],'article_edit',$article_data['id']);
					// E-mail
		   			$sent = $this->send_template($username,'follow_page_notice',$email,$special_changes);
				}
				else if ($type == 'comment') {
					// Notice
					$addit = $this->add_notice($row['user_id'],'comment_post',$article_data['id']);
					// Email
					$more_changes = $this->replace_comment_tags($comment_data);
					$special_changesA = @array_merge($special_changes,$more_changes);
		   			$sent = $this->send_template($username,'follow_comment_notice',$email,$special_changesA);
				}
		}
	}
	
	
	// -----------------------------------------------------------------------------
	// 	Replaces article tags on any inputted
	//	data. Returns an array with the info.
	
	function replace_article_tags($article_data,$direct_change = '0',$direct_change_content = '',$snippet = '0') {
		// Required information
   		$article_link = $this->prepare_link($article_data['id'],$article_data['category'],$article_data['name'],'',$article_data);
   		$article_breadcrumbs = $this->breadcrumbs($article_data['category'],$article_data['id'],$article_data,'0','');
   		if ($snippet != '1') {
 			$article_snippet = $this->get_snippet($article_data,$article_data['content']);
 		} else {
 			$article_snippet = $direct_change_content;
 		}
 		$article_image = $this->get_article_image($article_data,$article_data['content']);
 		if (! empty($article_image)) {
 			$image_div = '<div style="background-image:url(\'' . $article_image . '\');" class="bd_article_image" onclick="window.location=\'' . $article_link . '\';"></div>';
 		}
 		
 		$score = $article_data['upvoted'] - $article_data['downvoted'];
 		$category_data = $this->get_category($article_data['category'],'0','name');
 		
 		// Creator details
		// $user_id = $this->get_user_id($article_data['owner']);
		// $user_pic = $this->get_profile_pic($user_id);
		// $user_thumb = $this->get_profile_thumb($user_id);

		// Meta Deta
		if (empty($article_data['meta_title'])) {
			$page_title = $this->get_page_title($article_breadcrumbs);
			$article_data['meta_title'] = $page_title;
		}
		if (empty($article_data['meta_desc'])) {
			$cate_meta_desc = $this->get_an_item_option('meta_desc',$article_data['category'],'category');
			if (empty($cate_meta_desc)) {
				$article_data['meta_desc'] = $this->get_an_item_option('meta_desc','d');
			} else {
				$article_data['meta_desc'] = $cate_meta_desc;
			}
			if (empty($article_data['meta_desc'])) {
				$article_data['meta_desc'] = $this->get_snippet($article_data,$article_data['content'],'150','1');
			}
		}
		if (empty($article_data['meta_keywords'])) {
			$cate_meta_keys = $this->get_an_item_option('meta_desc',$article_data['category'],'category');
			if (empty($cate_meta_keys)) {
				$article_data['meta_keywords'] = $this->get_an_item_option('meta_keywords','d');
			} else {
				$article_data['meta_keywords'] = $cate_meta_keys;
			}
			if (empty($article_data['meta_keywords'])) {
				$plain_tags = $this->get_article_tags($article_data['id']);
				if (! empty($plain_tags)) {
					$article_data['meta_keywords'] = implode(',',$plain_tags);
				}
			}
		}
		
		// Meta data?
		$page_title = $this->get_page_title($article_breadcrumbs,'');
		$article['meta_title'] = $page_title;
		
		$category_link = $this->prepare_link('',$article_data['category']);
		$rss_link = URL . '/rss/category.php?id=' . $article_data['category'];

		$user_link = $this->get_user_link($article_data['owner']);
		
    		$theChanges = array(
    			'%article_id%' => $article_data['id'],
    			'%article_title%' => $article_data['name'],
    			'%article_name%' => $article_data['name'],
    			'%name%' => $article_data['name'],
    			'%article_related%' => $article_data['related'],
    			'%article_tags%' => $article_data['tags'],
    			'%article_link%' => $article_link,
    			'%breadcrumbs%' => $article_breadcrumbs,
    			'%created%' => $this->format_date($article_data['created']),
    			'%article_date%' => $this->format_date($article_data['created']),
			'%last_updated%' => $this->format_date($article_data['last_updated']),
			'%last_updated_by%' => $article_data['last_updated_by'],
    			'%article_snippet%' => $article_snippet,
    			'%article_breadcrumbs%' => $article_breadcrumbs,
    			'%article_description%' => $article_data['meta_desc'],
    			'%article_image%' => $article_image,
    			'%article_image_div%' => $image_div,
    			'%default_comment_status%' => $article_data['default_comment_type_show'],
    			'%article_category%' => $article_data['category'],
    			'%article_category_name%' => $category_data['name'],
    			'%views%' => $article_data['views'],
    			'%article_ups%' => $article_data['upvoted'],
    			'%article_downs%' => $article_data['downvoted'],
    			'%comments%' => $article_data['comments'],
    			'%creator%' => $article_data['owner'],
			'%creator_picture%' => $article_data['creator_picture'],
			'%creator_thumbnail%' => $article_data['creator_thumbnail'],
			'%creator_panel%' => $article_data['creator_panel'],
			'%category_link%' => $category_link,
    			'%category_name%' => $category_data['name'],
    			'%category%' => $article_data['category'],
    			'%category_id%' => $category_data['name'],
    			'%rss_link%' => $rss_link,
			'%favorites%' => $article_data['favorited'],
			'%follows%' => $article_data['following'],
    			'%meta_title%' => $article_data['meta_title'],
    			'%meta_desc%' => $article_data['meta_desc'],
    			'%meta_keywords%' => $article_data['meta_keywords'],
    			'%article_score%' => $score,
    			'%article_owner%' => $article_data['owner'],
 			'%article_user_link%' => $user_link,
    			'%article_last_updated%' => $this->format_date($article_data['last_updated']),
    			'%article_created%' => $this->format_date($article_data['created']),
    		);
    		
    		if ($direct_change == '1' && ! empty($direct_change_content)) {
    			foreach ($theChanges as $name => $value) {
    				$direct_change_content = str_replace($name,$value,$direct_change_content);
    			}
    			return $direct_change_content;
    		} else {
    			return $theChanges;
    		}
	}

	
	// -----------------------------------------------------------------------------
	// 	Replaces comments
	
	function replace_comment_tags($comment_data) {
		$user_id = $this->get_user_id($comment_data['user']);
		$user_pic = $this->get_profile_pic($user_id);
		$user_link = $this->get_user_link($comment_data['user']);
		$user_thumb = $this->get_profile_thumb($user_id);
		$score = $comment_data['up']-$comment_data['down'];
		// $total_subcomments = $this->comment_total_replies($comment_data['id']);
		// Comment deleted?
		if ($comment_data['deleted'] != '0000-00-00 00:00:00') {
			$put_comment = "<span class=\"bd_deleted_comment\">Deleted</span>";
		}
		// The comment should arrive to this function
		// formatted. Important for a number of reasons
		// that I'm too lazy to explain here but will
		// probably regret not doing later. Later self,
		// I am sorry, sincerly, self from Nov 29th 2011.
		else {
			// $put_comment = $this->format_comment($comment_data['comment']);
		}
		
		// Article Link
		$article = $this->get_article($comment_data['article'],'0','id,name,category','0','0','0');
		$article_link = $this->prepare_link($article['id'],$article['category'],$article['name'],'',$article);
		$article_link = "<a href=\"$article_link\">" . $article['name'] . "</a>";
		// User score
		$user_score = $this->get_user_score($comment_data['user']);
    		// Changes
    		$theChanges = array(
 			'%comment_id%' => $comment_data['id'],
 			'%comment_score%' => $score,
 			'%comment_upvotes%' => $comment_data['up'],
 			'%comment_downvotes%' => $comment_data['down'],
 			// '%comment_replies%' => $total_subcomments,
 			'%comment%' => $comment_data['comment'],
 			'%comment_date%' => $this->format_date($comment_data['date']),
 			'%comment_user%' => $comment_data['user'],
 			'%comment_username%' => $comment_data['user'],
 			'%comment_user_score%' => $user_score,
 			'%comment_user_picture%' => $user_pic,
 			'%comment_user_thumbnail%' => $user_thumb,
 			'%comment_user_link%' => $user_link,
 			'%article_link%' => $article_link,
 			'%article_name%' => $article['name']
    		);
    		return $theChanges;
	}
	
	
	// -----------------------------------------------------------------------------
	// 	Delete a page
	
	function delete_page($id) {
		// Delete Homepage?
		if ($id == '1') {
			echo "0+++" . lg_no_del_homepage;
			exit;
		}
		// Cache articles?
		if ($this->get_option('cache_articles') == '1') {
		   	$cache_file = PATH . "/generated/article-" . $id . ".php";
		   	$unlink = @unlink($cache_file);
		}
		// Cache Category List
		if ($this->get_option('cache_category_list') == '1') {
			$cache_list = $this->category_tree('','1','1');
		}
		// Page info
		$q2 = "DELETE FROM `" . TABLE_PREFIX . "articles` WHERE `id`='" . $this->mysql_clean($id) . "' LIMIT 1";
		$delete = $this->delete($q2);
		// Page history
		$q4 = "DELETE FROM `" . TABLE_PREFIX . "article_tags` WHERE `page_id`='" . $this->mysql_clean($id) . "'";
		$delete = $this->delete($q4);
		// Page history
		$q3 = "DELETE FROM `" . TABLE_PREFIX . "articles_history` WHERE `article_id`='" . $this->mysql_clean($id) . "'";
		$delete = $this->delete($q3);
		// Following
		$q1 = "DELETE FROM `" . TABLE_PREFIX . "following` WHERE `article`='" . $this->mysql_clean($id) . "'";
		$delete = $this->delete($q1);
		// Delete options
		$q = "DELETE FROM `" . TABLE_PREFIX . "item_options` WHERE `act_id`='" . $this->mysql_clean($id) . "' AND `type`='article'";
		$delete = $this->delete($q);
	}
	
	// -----------------------------------------------------------------------------
	// 	Delete a category
	
	function delete_category($id) {
		// Base category?
		$q1 = "SELECT `base` FROM `" . TABLE_PREFIX . "categories` WHERE `id`='" . $this->mysql_clean($id) . "' LIMIT 1";
		$del_1 = $this->get_array($q1);
		if ($del_1['base'] == '1' || empty($id)) {
			echo "0+++" . lg_no_del_base;
			exit;
		}
		// Cache Category List
		if ($this->get_option('cache_category_list') == '1') {
			$cache_list = $this->category_tree('','1','1');
		}
		// Page info
		$q3 = "DELETE FROM `" . TABLE_PREFIX . "categories` WHERE `id`='" . $this->mysql_clean($id) . "'";
		$delete = $this->delete($q3);
		// Page info
		$q2 = "DELETE FROM `" . TABLE_PREFIX . "articles` WHERE `category`='" . $this->mysql_clean($id) . "'";
		$delete = $this->delete($q2);
		// Widgets
		$q1 = "DELETE FROM `" . TABLE_PREFIX . "widgets` WHERE `category`='" . $this->mysql_clean($id) . "'";
		$delete = $this->delete($q1);
		// Delete options
		$q = "DELETE FROM `" . TABLE_PREFIX . "item_options` WHERE `act_id`='" . $this->mysql_clean($id) . "' AND `type`='category'";
		$delete = $this->delete($q);
	}
	
	
	// -----------------------------------------------------------------------------
	// 	Delete a comment
	
	function delete_comment($id) {
		// Get the comment
		$this_comment = $this->get_a_comment($id);
		// Subcomments?
		$q1 = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "comments` WHERE `subcomment`='" . $this->mysql_clean($id) . "'";
		$subcomments = $this->get_array($q1);
		// If pending or no sub-comments, just delete it.
		if ($comment['pending'] == "1" || $subcomments['0'] <= "0") {
			$q = "DELETE FROM `" . TABLE_PREFIX . "comments` WHERE `id`='" . $this->mysql_clean($id) . "' LIMIT 1";
			$delete = $this->delete($q);
			$reply = "2";
		}
		else {
			$q = "UPDATE `" . TABLE_PREFIX . "comments` SET `deleted`='" . $this->current_date() . "',`deleted_by`='$user' WHERE `id`='" . $this->mysql_clean($id) . "' LIMIT 1";
			$update = $this->update($q);
			$reply = "1";
		}
		// Remove from his/her total.
		$update = $this->update_eav('comments',"subtract",$this_comment['user'],'username','');
	 	// ---------------------------
	 	//	Caching?
	 	if ($this->get_option('cache_comments') == '1') {
	 		$recache = $this->get_comments($comment['article'],'','','1');
	 	}
	}
	
	
}

?>