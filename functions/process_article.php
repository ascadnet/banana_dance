<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Re-processes an article post edit.
	
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

if (! empty($_POST['id'])) {

	if ($_POST['skipArticle'] != '1') {
		// Article Details
		$article_info = $manual->get_article($_POST['id'],'1');
		// Define
		define('BD_ARTICLE_VIEWING',$_POST['id']);
		define('BD_CATEGORY_VIEWING',$article_info['category']);
		define('BD_ARTICLE_COMMENT_TYPE',$article_info['default_comment_type_show']);
		// Format the article
		$article = $manual->format_article($article_info,$article_info['content'],$user,'','1');
	}
	
	if ($_POST['skipComments'] != '1') {
		// Commenting allowed?
		if ($article_info['allow_comments'] == "1") {
			$comments = $manual->get_comments($_POST['id'],$user);
		}
	}
	
	// Sidebar
	if ($_POST['sidebar'] == '1') {
		if (empty($article_info['category'])) {
			$check_cate = $_POST['category'];
		} else {
			$check_cate = $article_info['category'];
		}
		$cache_category_list = $db->get_option('cache_category_list');
		$sidebar_categories = $manual->category_tree($check_cate,'1',$cache_category_list,'0');
	}
	
	echo $article . "|||||" . $comments . "|||||" . $sidebar_categories;
	exit;
}

?>