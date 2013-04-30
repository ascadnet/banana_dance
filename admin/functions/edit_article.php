<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Edit articles/pages.
	
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


// ----------------------------------------------------------------------------------
//	Load the basics

require "../../config.php";
require "../../includes/admin.functions.php";
$admin = new admin;

// ----------------------------------------------------------------------------------
//	Logged in and has privileges?

// Creating an article
if ($_POST['id'] == "new") {
	$admin->check_permission('can_create_articles',$user,$privileges);
}
// Editing an article
else {
	$admin->check_permission('can_alter_articles',$user,$privileges);
}

// ----------------------------------------------------------------------------------
//	Continue

// If user doesn't have the correct privilieges,
// always make new articles private.
if ($_POST['id'] == "new" && $privileges['new_articles_public'] != "1") {
	$_POST['public'] = "0";
}

$update_q = "";
// Loop through submitted fields.
// Only fields within the form named "edit"
// will be sent from admin.js.
foreach ($_POST as $name => $value) {
	// The ID of this article
	if ($name == "id") {
		$where = "`id`='" . $db->mysql_clean($value) . "'";
	}
	// Finalize Editing Field
	else if ($name == "finalize") {
		// Nothing
	}
	// Finalize Editing Field
	else if ($name == "add_user") {
		// Nothing
	}
	// Category information
	else if ($name == "category_default") {
		// Here we make this the default article for this category.
		if ($value == "1") {
			$make_default = "1";
		}
		// Here we check if we are removing this article
		// as the category's default article.
		else {
			$category = $manual->get_category($_POST['category']);
			if ($category['home_article'] == $_POST['id']) {
				$q1 = "UPDATE `" . TABLE_PREFIX . "categories` SET `home_article`='' WHERE `id`='" . $db->mysql_clean($_POST['category']) . "' LIMIT 1";
				$update = $db->update($q1);
			}
		}
	}
	// All other fields
	else {
		if ($name == 'name') {
			$value = str_replace('+','&#43;',$value);
		}
		$update_q .= ",`$name`='" . $db->mysql_clean($value) . "'";
		$insert .= ",`$name`";
		$insert1 .= ",'" . $db->mysql_clean($value) . "'";
	}
}


	// Get current data
	$current_article_data = $manual->get_article($_POST['id'],'1');
	// Update it
	$update_q = substr($update_q,1);
	$q = "UPDATE `" . TABLE_PREFIX . "articles` SET $update_q,`last_updated`='" . $db->current_date() . "' WHERE $where LIMIT 1";
	$update = $db->update($q);
	// Make category default article?
	if ($make_default == "1") {
		$q1 = "UPDATE `" . TABLE_PREFIX . "categories` SET `home_article`='" . $db->mysql_clean($_POST['id']) . "' WHERE `id`='" . $db->mysql_clean($_POST['category']) . "' LIMIT 1";
		$update = $db->update($q1);
	}
	// Add to history log?
	if ($_POST['finalize'] == '1') {
		$return_text = $manual->prepare_link($_POST['id'],$_POST['category'],$_POST['name']);
	} else {
		$return_text = "Article updated!";
	}
	// Cache Category List
	if ($db->get_option('cache_category_list') == '1' && $_POST['display_on_sidebar'] == '1') {
		$cache_list = $manual->category_tree('','1','1');
	}
	// Complete the action
	$log = $db->complete_task('article_edit',$user,'');
	// Reply
	echo "1+++$return_text";
	exit;

?>