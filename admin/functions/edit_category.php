<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Edit Categories.
	
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
	$admin->check_permission('can_create_categories',$user,$privileges);
}
// Editing an article
else {
	$admin->check_permission('can_alter_categories',$user,$privileges);
}

// ----------------------------------------------------------------------------------
//	Continue

if (empty($_POST['name']) && $_POST['id'] != "base") {
	echo "0+++Title" . lg_something_required;
	exit;
}

$update_q = "";
// Loop through submitted fields.
// Only fields within the form named "edit"
// will be sent from admin.js.
foreach ($_POST as $name => $value) {
	// The ID of this article
	if ($name == "id") {
		if ($value == "new") {
			$creating = "1";
		}
		else if ($value == "base") {
			$where = "`base`='1'";
		}
		else {
			$where = "`id`='" . $db->mysql_clean($value) . "'";
		}
	}
	// Article re-ordering
	else if ($name == "article") {
		$updateArray = $_POST['article'];
	}
	// Sub-category re-ordering
	else if ($name == "category") {
		$updateCateArray = $_POST['category'];
	}
	else if ($name == "add_user") {
	
	}
	// All other fields
	else {
		$update_q .= ",`$name`='" . $db->mysql_clean($value) . "'";
		$insert .= ",`$name`";
		$insert1 .= ",'" . $db->mysql_clean($value) . "'";
	}
}

if ($creating == "1") {

	$options = array(
		'allow_article_creation' => $_POST['allow_article_creation'],
		'template' => $_POST['template'],
		'public' => $_POST['public'],
	);
	$add = $manual->create_category($_POST['name'],$_POST['subcat'],$options);

	/*
	// Create it
	$insert = substr($insert,1);
	$insert1 = substr($insert1,1);
	$q = "INSERT INTO `" . TABLE_PREFIX . "categories` ($insert) VALUES ($insert1)";	
	$insert = $db->insert($q);
	// Cache Category List
	if ($db->get_option('cache_category_list') == '1') {
		$cache_list = $manual->category_tree('','1','1');
	}
	// Create main page?
	if ($this->get_option('direct_link') == '1') {
		$page = $manual->no_homepage_article($insert);
	}
	// Complete the action
	$log = $db->complete_task('category_create',$user,'');
	// Reply
	echo "1+++$insert";
	exit;
	*/
} else {
	// Reorder articles
	$current_order = 0;
	if ($updateArray) {
		foreach ($updateArray as $article_id) {
			$current_order++;
			$q = "UPDATE `" . TABLE_PREFIX . "articles` SET `order`='$current_order' WHERE `id`='$article_id' LIMIT 1";
			$update = $db->update($q);
		}
	}
	// Reorder categories
	$current_order = 0;
	if ($updateCateArray) {
		foreach ($updateCateArray as $cate_id) {
			$current_order++;
			$q = "UPDATE `" . TABLE_PREFIX . "categories` SET `order`='$current_order' WHERE `id`='$cate_id' LIMIT 1";
			$update = $db->update($q);
		}
	}
	// Update it
	$update_q = substr($update_q,1);
	if ($_POST['id'] != "base") { 
		$q = "UPDATE `" . TABLE_PREFIX . "categories` SET $update_q WHERE $where LIMIT 1";	
		$update = $db->update($q);
	}
	// Cache Category List
	if ($db->get_option('cache_category_list') == '1') {
		$cache_list = $manual->category_tree('','1','1');
	}
	// Complete the action
	$log = $db->complete_task('category_edit',$user,'');
	// Reply
	echo "1+++" . lg_saved;
	exit;
}

?>