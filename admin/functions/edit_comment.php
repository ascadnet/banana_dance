<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Edit comments.
	
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

$admin->check_permission('edit_comment_status',$user,$privileges);

// ----------------------------------------------------------------------------------
//	Continue

if (empty($_POST['id'])) {
	echo "0+++Comment ID" . lg_something_required;
	exit;
} else {
	$this_comment = $manual->get_a_comment($_POST['id']);
}

// ---------------------------
// 	Prepare update
$update_q = "";
foreach ($_POST as $name => $value) {
	if ($name != 'id') {
		if ($name == 'article_name') {
			// nothing...
		} else {
			$update_q .= ",`$name`='" . $db->mysql_clean($value) . "'";
			$insert .= ",`$name`";
			$insert1 .= ",'" . $db->mysql_clean($value) . "'";
		}
	}
	else {
		$where = "`id`='" . $db->mysql_clean($value) . "'";
	}
}
$update_q = substr($update_q,1);
			
// ---------------------------
// 	Update the DB

$q = "UPDATE `" . TABLE_PREFIX . "comments` SET $update_q WHERE $where LIMIT 1";
$update = $db->update($q);

// We need to make sure subcomments are moved
// to the correct status type for accurate
// comment counts by type. Also applies to
// new article selection for the comment.

if ($_POST['status'] != $this_comment['status'] || $_POST['article'] != $this_comment['article']) {
	$commands = $admin->update_subcomments($_POST['id'],$_POST['status'],$_POST['article']);
	// New Status?
	if ($_POST['status'] != $this_comment['status']) {
		$custom_data = array(
			'old_status' => $this_comment['status'],
			'new_status' => $_POST['status']
		);
 		if (! empty($_POST['status'])) {
			$name_ct = "comments_status" . $_POST['status'];
	 		$update = $db->update_eav($name_ct,"add",$user,'username');
 		}
 		if (! empty($this_comment['status'])) {
			$name_ct_old = "comments_status" . $this_comment['status'];
	 		$update = $db->update_eav($name_ct_old,"subtract",$user,'username');
 		}
		$custom_data = array('comment_type' => $_POST['status']);
		$log = $db->complete_task('comment_status_changed',$user,$_POST['id'],$custom_data);
	}
}

// ---------------------------
//	Caching?
if ($db->get_option('cache_comments') == '1') {
	$manual->get_comments($this_comment['article'],'','','1');
}

// ---------------------------
// Complete the action
$log = $db->complete_task('comment_edit',$user,$_POST['id']);

echo "1+++" . lg_saved;
exit;

?>