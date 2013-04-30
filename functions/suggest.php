<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Suggestion box functions.
	
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

// ----------------------------------
// 	Permissions

if ($_POST['function'] == "article_permissions" || $_POST['function'] == "category_permissions") {
	if ($privileges['is_admin'] != "1") {
		echo "0+++" . lg_privilieges_req;
		exit;
	}
}

	
// ----------------------------------
// 	Act on the suggestion
if ($_POST['action'] == "complete") {

	// ----------------------------------
	// 	Grant user access to article
	if ($_POST['function'] == "article_permissions") {
		$exp = explode(':',$_POST['id']);
		if ($exp['0'] == 'user') {
			$hasPerm = $manual->user_permissions($_POST['additional'],$exp['1'],'','1');
		} else {
			$hasPerm = $manual->user_permissions($_POST['additional'],'',$exp['1'],'1');
		}
		if ($hasPerm == '1') {
			echo "0+++" . lg_user_has_access;
			exit;
		} else {
			// Create entry
			$exp = explode(':',$_POST['id']);
			if ($exp['0'] == 'user') {
				$q = "INSERT INTO `" . TABLE_PREFIX . "user_permissions` (`user_id`,`permission`) VALUES ('" . $db->mysql_clean($exp['1']) . "','" . $db->mysql_clean($_POST['additional']) . "')";
				$insert = $db->insert($q);
				// List element
				$mysql_table = TABLE_PREFIX . "user_permissions";
				// Get details
			   	$username = $session->get_username_from_id($exp['1']);
				$list = "<li id=\"" . $insert . "\"><a href=\"index.php?l=users_edit&id=" . $exp['1'] . "\"><img src=\"imgs/icon-user.png\" width=16 height=16 border=0 alt=\"User\" title=\"User\" class=\"icon\" />" . $username . "</a><div class=\"icon_float_right\"><a href=\"#\" onClick=\"deleteID('$mysql_table','" . $insert . "');return false;\"> <img src=\"imgs/icon-delete.png\" border=\"0\" alt=\"Delete\" title=\"Delete\" class=\"icon_nopad\" /></a></div></li>";
			} else {
				$q = "INSERT INTO `" . TABLE_PREFIX . "user_permissions` (`user_type`,`permission`) VALUES ('" . $db->mysql_clean($exp['1']) . "','" . $db->mysql_clean($_POST['additional']) . "')";
				$insert = $db->insert($q);
				// List element
				$mysql_table = TABLE_PREFIX . "user_permissions";
				// Get details
			   	$typename = $session->get_usertype_settings($exp['1'],'name');
				$list = "<li id=\"" . $insert . "\"><a href=\"index.php?l=user_types_edit&id=" . $exp['1'] . "\"><img src=\"imgs/icon-usergroup.png\" width=16 height=16 border=0 alt=\"User Type\" title=\"User Type\" class=\"icon\" />" . $typename['name'] . "</a><div class=\"icon_float_right\"><a href=\"#\" onClick=\"deleteID('$mysql_table','" . $insert . "');return false;\"><img src=\"imgs/icon-delete.png\" border=\"0\" alt=\"Delete\" title=\"Delete\" class=\"icon_nopad\" /></a></div></li>";
			}
			// Reply
			echo "1+++$list";
			exit;
		}
	}
	

	// ----------------------------------
	// 	Grant user access to categories
	
	else if ($_POST['function'] == "category_permissions") {
		$exp = explode(':',$_POST['id']);
		if ($exp['0'] == 'user') {
			$hasPerm = $manual->user_permissions('',$exp['1'],'','1',$_POST['additional']);
		} else {
			$hasPerm = $manual->user_permissions('','',$exp['1'],'1',$_POST['additional']);
		}
		if ($hasPerm == '1') {
			echo "0+++" . lg_user_has_access;
			exit;
		} else {
			// Create entry
			$exp = explode(':',$_POST['id']);
			if ($exp['0'] == 'user') {
				$q = "INSERT INTO `" . TABLE_PREFIX . "user_permissions` (`user_id`,`category`) VALUES ('" . $db->mysql_clean($exp['1']) . "','" . $db->mysql_clean($_POST['additional']) . "')";
				$insert = $db->insert($q);
				// List element
				$mysql_table = TABLE_PREFIX . "user_permissions";
				// Get details
			   	$username = $session->get_username_from_id($exp['1']);
				$list = "<li id=\"" . $insert . "\"><a href=\"index.php?l=users_edit&id=" . $exp['1'] . "\"><img src=\"imgs/icon-user.png\" width=16 height=16 border=0 alt=\"User\" title=\"User\" class=\"icon\" />" . $username . "</a><div class=\"icon_float_right\"><a href=\"#\" onClick=\"deleteID('$mysql_table','" . $insert . "');return false;\"> <img src=\"imgs/icon-delete.png\" border=\"0\" alt=\"Delete\" title=\"Delete\" class=\"icon_nopad\" /></a></div></li>";
			} else {
				$q = "INSERT INTO `" . TABLE_PREFIX . "user_permissions` (`user_type`,`category`) VALUES ('" . $db->mysql_clean($exp['1']) . "','" . $db->mysql_clean($_POST['additional']) . "')";
				$insert = $db->insert($q);
				// List element
				$mysql_table = TABLE_PREFIX . "user_permissions";
				// Get details
			   	$typename = $session->get_usertype_settings($exp['1'],'name');
				$list = "<li id=\"" . $insert . "\"><a href=\"index.php?l=user_types_edit&id=" . $exp['1'] . "\"><img src=\"imgs/icon-usergroup.png\" width=16 height=16 border=0 alt=\"User Type\" title=\"User Type\" class=\"icon\" />" . $typename['name'] . "</a><div class=\"icon_float_right\"><a href=\"#\" onClick=\"deleteID('$mysql_table','" . $insert . "');return false;\"><img src=\"imgs/icon-delete.png\" border=\"0\" alt=\"Delete\" title=\"Delete\" class=\"icon_nopad\" /></a></div></li>";
			}
			// Reply
			echo "1+++$list";
			exit;
		}
	}	
	

}

// ----------------------------------
// 	Search
else {

	// Creating links in articles.
	if ($_POST['function'] == "article_editing") {
		
		$q = "SELECT `category`,`name`,`id` FROM `" . TABLE_PREFIX . "articles` WHERE `name` LIKE '%" . $db->mysql_clean($_POST['value']) . "%' LIMIT 10";
		$results = $db->run_query($q);
		while ($row = mysql_fetch_array($results)) {
			if (! empty($_POST['custom_link_name'])) {
				$link_name = $_POST['custom_link_name'];
			} else {
				$link_name = $row['name'];
			}
			$category_name = $manual->get_category_name_from_id($row['category']);
			// Link format
			if ($_POST['ed_type'] == 'cms') {
				$linkA = $manual->prepare_link($row['id'],$row['category'],$row['name']);
				$caller_format = '<a href=\\"' . $linkA . '\\">' . $link_name . '</a>';
			} else {
				$caller_format = "[[" . addslashes($row['name']) . "::" . addslashes($category_name) . "|" . addslashes($link_name) . "]]";
			}
			$data .= "<li onclick='addCaller(\"content\",\"" . $caller_format . "\");complete_suggest();closeCaptcha();return false;'>" . $category_name . " &raquo; " . $row['name'] . "</li>";
		}
		echo "1+++$data";
		exit;
		
	}
	
	else if ($_POST['function'] == "article_permissions" || $_POST['function'] == "category_permissions") {
	
		// User Types
		$q1 = "SELECT `id`,`name` FROM `" . TABLE_PREFIX . "user_types` WHERE `name` LIKE '%" . $db->mysql_clean($_POST['value']) . "%' LIMIT 25";
		$results1 = $db->run_query($q1);
		while ($row = mysql_fetch_array($results1)) {
			$data .= "<li onclick=\"selectSuggest('type:" . $row['id'] . "');\">User Type: " . $row['name'] . "</li>";
		}
		
		// Users
		$q = "SELECT `id`,`username` FROM `" . TABLE_PREFIX . "users` WHERE `username` LIKE '%" . $db->mysql_clean($_POST['value']) . "%' LIMIT 25";
		$results = $db->run_query($q);
		while ($row = mysql_fetch_array($results)) {
			$data .= "<li onclick=\"selectSuggest('user:" . $row['id'] . "');\">" . $row['username'] . "</li>";
		}
		
		echo "1+++$data";
		exit;
	
	}
	
	else {
	
		$t1 = TABLE_PREFIX . '_users';
		$t2 = TABLE_PREFIX . '_user_data';
		if ($_POST['table'] == $t1 || $_POST['table'] == $t2) {
			exit;
		}
	
		$q = "SELECT `" . $db->mysql_clean($_POST['return']) . "`,`" . $db->mysql_clean($_POST['display']) . "` FROM `" . $db->mysql_clean($_POST['table']) . "` WHERE `" . $db->mysql_clean($_POST['search']) . "` LIKE '%" . $db->mysql_clean($_POST['value']) . "%' LIMIT 25";
		$results = $db->run_query($q);
		while ($row = mysql_fetch_array($results)) {
			$data .= "<li onclick=\"selectSuggest('" . $row[$_POST['return']] . "','" . $row[$_POST['display']] . "');\">" . $row[$_POST['display']] . "</li>";
		}
		echo "1+++$data";
		exit;
		
	}

}

	
?>