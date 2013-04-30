<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Handles most ajax calls used on the admin CP.
	
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
//	Get a page for display on the admin CP

if ($_POST['action'] == "get_page") {

	$path = PATH . "/admin/includes/";

	ob_start();
	include($path);
	$content = ob_get_contents();
	ob_end_clean();
	
	echo $content;
	exit;

}

// ----------------------------------------------------------------------------------
//	Clear a page's content.

if ($_POST['action'] == "clear_page") {

	// Permissions
	$admin->check_permission('is_admin',$user,$privileges);
	$log = $db->begin_task('clear_page_content',$user,$_GET['id']);
	
	$q = "UPDATE `" . TABLE_PREFIX . "articles` SET `content`='' WHERE `id`='" . $db->mysql_clean($_POST['id']) . "' LIMIT 1";
	$update = $db->update($q);
	
	$log = $db->complete_task('clear_page_content',$user,$_GET['id']);
	echo "1+++Cleared...";
	exit;

}

// ----------------------------------------------------------------------------------
//	Create a redirect rule

else if ($_POST['action'] == "redirectRule") {
	// First we get the old link
	$url = "/article/" . $_POST['category'] . "/" . $_POST['name'];
	// Prepare new link
	$new_link = $manual->prepare_link($_POST['article'],'','','');
	// Database consideration
	$q = "INSERT INTO `" . TABLE_PREFIX . "article_redirects` (`old_category`,`old_article`,`new_article_id`) VALUES ('" . $_POST['category'] . "','" . $_POST['name'] . "','" . $_POST['article'] . "')";
	$insert = $db->insert($q);
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
		$write = $db->write_file($path,$htaccess);
		// Reply
		echo "1+++" . lg_saved;
		exit;
	} else {
		// Reply
		$rule = nl2br($rule);
		$write = str_replace('%rule%',$rule,lg_admin_no_write);
		echo "0+++" . $write;
		exit;
	}
}


// ----------------------------------------------------------------------------------
//	Revert a page to previous version

else if ($_POST['action'] == 'revert_page') {

	// Permissions
	$admin->check_permission('is_admin',$user,$privileges);

	// ----------------------------------------------------------------------------------
	//	Continue
	if (empty($_POST['id'])) {
		echo "0+++Page version ID" . lg_something_required;
		exit;
	}
	
	$log = $db->begin_task('revert_page',$user,'');
		
	// Revision Info
	$revision = $manual->get_revision($_POST['id']);
	
	// Update it
	$q = "UPDATE `" . TABLE_PREFIX . "articles` SET `name`='" . $db->mysql_clean($revision['name']) . "',`category`='" . $revision['category'] . "',`content`='" . $db->mysql_clean($revision['content']) . "',`last_updated`='" . $db->current_date() . "' WHERE `id`='" . $article['id'] . "' LIMIT 1";
	$update = $db->update($q);
	
   	// Save history?
   	if ($db->get_option('save_article_history') == '1') {
   		$q = "INSERT INTO `" . TABLE_PREFIX . "articles_history` (`user`,`ip`,`article_id`,`category`,`name`,`content`,`date`) VALUE ('$user','" . $db->mysql_clean($_SERVER['REMOTE_ADDR']) . "','" . $revision['article_id'] . "','" . $revision['category'] . "','" . $db->mysql_clean($revision['name']) . "','" . $db->mysql_clean($revision['content']) . "','" . $db->current_date() . "')";
   		$insertA = $db->insert($q);
   	}
   	
	$log = $db->complete_task('revert_page',$user,'');
	
   	// Reply
   	echo "1+++" . lg_saved;
   	exit;

}



// ----------------------------------------------------------------------------------
//	Create or edit a user type

else if ($_POST['action'] == "add_user_type") {

	// Permissions
	$admin->check_permission('is_admin',$user,$privileges);

	// ----------------------------------------------------------------------------------
	//	Continue
	if (empty($_POST['name'])) {
		echo "0+++Name" . lg_something_required;
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
			} else {
				$where = "`id`='" . $db->mysql_clean($value) . "'";
			}
		}
		// Action
		else if ($name == "action") {
			// Nothing
		}
		// All other fields
		else {
			$update_q .= ",`$name`='" . $db->mysql_clean($value) . "'";
			$insert .= ",`$name`";
			$insert1 .= ",'" . $db->mysql_clean($value) . "'";
		}
	}
	
	if ($creating == "1") {
		// Create it
		$insert = substr($insert,1);
		$insert1 = substr($insert1,1);
		$q = "INSERT INTO `" . TABLE_PREFIX . "user_types` ($insert) VALUES ($insert1)";
		$insert = $db->insert($q);
		// Complete the action
		$log = $db->complete_task('user_type_create',$user,'');
		// Reply
		echo "1+++$insert+++user_types_edit";
		exit;
	} else {
		// Update it
		$update_q = substr($update_q,1);
		$q = "UPDATE `" . TABLE_PREFIX . "user_types` SET $update_q WHERE $where LIMIT 1";
		$update = $db->update($q);
		// Complete the action
		$log = $db->complete_task('user_type_edit',$user,'');
		// Reply
		echo "1+++" . lg_saved;
		exit;
	}

}


// ----------------------------------------------------------------------------------
//	Create or edit a user

else if ($_POST['action'] == "add_user") {

	// ----------------------------------------------------------------------------------
	//	Permissions
	
	$admin->check_permission('is_admin',$user,$privileges);

	// ----------------------------------------------------------------------------------
	//	Continue

	require PATH . "/includes/password.functions.php";
	$password = new password;
	
	require PATH . "/includes/field.functions.php";
	$fields = new fields;
	
	// Password Considerations
	//	New Account
	if ($_POST['id'] == "new") {
		if (empty($_POST['pass'])) {
			$_POST['pass'] = $password->strong_password();
			$_POST['pass1'] = $_POST['pass'];
			$skip_password_checks = '1';
		} else {
			$skip_password_checks = '0';
		}
		$update_pass = '1';
	}
	// 	Existing Account
	else {
		$user_data = $session->get_user_data('',$_POST['id']);
		// Username
		if ($_POST['username'] != $user_data['username']) {
			$skip_username_checks = '0';
		} else {
			$_POST['username'] = $user_data['username'];
			$skip_username_checks = '1';
		}
		// Username
		if ($_POST['email'] != $user_data['email']) {
			$skip_email_checks = '0';
		} else {
			$skip_email_checks = '1';
		}
		// Password?
		if (empty($_POST['pass'])) {
			$skip_password_checks = '1';
			$update_pass = '0';
		} else {
			$skip_password_checks = '0';
			$update_pass = '1';
		}
	}
	
	// General registration checks
	$password->registration_checks('1',$skip_password_checks,$skip_username_checks,$skip_email_checks);
	
	// Encode the password
	if ($update_pass == '1') {
		$salt = $password->generate_salt($_POST['username']);
		$encoded_pass = $password->encode_password($_POST['pass'],$salt);
	}
	
	// User type
	if (empty($_POST['type'])) {
		$_POST['type'] = "3";
	}
	
	$primary_fields = array('username','email','name','type','pass','pass1','id','action');
	$main_table = array('username','email','name','type');
	$secondary_mysql = array();
	$update_q = "";
	// Loop through submitted fields.
	// Only fields within the form named "edit"
	// will be sent from admin.js.
	foreach ($_POST as $name => $value) {
		// The ID of this article
		if ($name == "id") {
			if ($value == "new") {
				$creating = "1";
			} else {
				$where = "`id`='" . $db->mysql_clean($value) . "'";
			}
		}
		// Action
		else if ($name == "pass" || $name == "pass1") {
			// Nothing
		}
		// Action
		else if ($name == "action") {
			// Nothing
		}
		// All other fields
		else {
			if (in_array($name,$main_table)) {
				$update_q .= ",`$name`='" . $db->mysql_clean($value) . "'";
				$insert .= ",`$name`";
				$insert1 .= ",'" . $db->mysql_clean($value) . "'";
			} else {
				$secondary_mysql[] = $name;
			}
		}
	}
	
	if ($creating == "1") {
		// Create it
		$insert = substr($insert,1);
		$insert1 = substr($insert1,1);
		$q = "INSERT INTO `" . TABLE_PREFIX . "users` ($insert,`password`,`salt`,`joined`,`last_updated`) VALUES ($insert1,'$encoded_pass','$salt','" . $db->current_date() . "','" . $db->current_date() . "')";
		$insert = $db->insert($q);
		// Secondary Information
		foreach ($_POST as $name => $value) {
			if (! in_array($name,$primary_fields)) {
				$update = $db->update_eav($name,$db->mysql_clean($value),$insert,'user_id','');
			}
		}
		// Complete the action
		$log = $db->complete_task('user_add',$user,'');
		// E-Mail the user
		// Send Template
	   	$special_changes = array(
	   		'%password%' => $_POST['pass']
	   	);
	   	$sent = $template->send_template($_POST['username'],'account_created',"",$special_changes);
		// Reply
		echo "1+++$insert+++users_edit";
		exit;
	} else {
		// You can update a username simply because the
		// DB stores everything as User ID, which won't
		// change.
	
		// Update it
		$update_q .= ",`last_updated`='" . $db->current_date() . "'";
		if ($skip_password_checks != '1') {
			$update_q .= ",`password`='" . $encoded_pass . "',`salt`='$salt'";
		}
		$update_q = substr($update_q,1);
		$q = "UPDATE `" . TABLE_PREFIX . "users` SET $update_q WHERE $where LIMIT 1";
		$update = $db->update($q);
		// Secondary Information
		foreach ($_POST as $name => $value) {
			if (! in_array($name,$primary_fields)) {
				// Encrypted?
				$field_info = $fields->get_field($name);
				if ($field_info['encrypted'] == "1") {
					$value = $db->encode_data($value);
				}
				$update = $db->update_eav($name,$db->mysql_clean($value),$db->mysql_clean($_POST['id']),'user_id','');
			}
		}
		// E-Mail the user
		// Send Template
	   	$special_changes = array(
	   		'%password%' => $_POST['pass']
	   	);
	   	$sent = $template->send_template($_POST['username'],'account_updated_admin',"",$special_changes);
		// Complete the action
		$log = $db->complete_task('user_edit',$user,'');
		// Reply
		echo "1+++" . lg_saved;
		exit;
	}

}

// ----------------------------------------------------------------------------------
//	Create or edit a widget
//	Not for plugins

else if ($_POST['action'] == "add_widget") {

	// Permissions
	$admin->check_permission('is_admin',$user,$privileges);

	// ----------------------------------------------------------------------------------
	//	Continue
	if (empty($_POST['type'])) {
		echo "0+++Widget type" . lg_something_required;
		exit;
	}
	// Get widget names
	include "../includes/widget_names.php";
	
	// Give it a name if none was provided.
	if (empty($_POST['name'])) {
		$_POST['name'] = "Unnamed " . $widget_names[$_POST['type']];
	}
	$update_q = "";
	$insert = "";
	$insert1 = "";
	// Loop through submitted fields.
	// Only fields within the form named "edit"
	// will be sent from admin.js.
	if ($_POST['id'] == 'new') {
		$creating = '1';
		$insert = "`date`,`name`,`owner`,`type`,`active`";
		$insert1 = "'" . $db->current_date() . "','" . $db->mysql_clean($_POST['name']) . "','$user','" . $db->mysql_clean($_POST['type']) . "','1'";
	} else {
		$creating = '0';
		$where = "`id`='" . $db->mysql_clean($_POST['id']) . "'";
		$update_q = "`name`='" . $db->mysql_clean($_POST['name']) . "'";
	}
	
	// Special considerations
	// Widget options
	$insert .= ",`html`,`html_insert`";
	if ($_POST['type'] == '1') {
		if (empty($_POST['category_1'])) {
			$_POST['category_1'] = '0';
		}
		$category = $_POST['category_1'];
		$options_array = array(
			$_POST['limit_1'],
			$_POST['sub_categories_1'],
			$_POST['order_1'],
			$_POST['dir_1'],
			$_POST['columns_1']
		);
		$insert1 .= ",'',''";
	}
	else if ($_POST['type'] == '2') {
		if (empty($_POST['article_2'])) {
			echo "0+++Page ID" . lg_something_required;
			exit;
		} else {
			$q = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "articles` WHERE `id`='" . $db->mysql_clean($_POST['article_2']) . "'";
			$found = $db->get_array($q);
			if ($found['0'] <= 0) {
				echo "0+++" . lg_admin_article_not_found;
				exit;
			}
		}
		$category = '';
		$options_array = array(
			$_POST['limit_2'],
			$_POST['article_2'],
			$_POST['order_2'],
			$_POST['dir_2'],
			$_POST['trim_2']
		);
		$insert1 .= ",'" . $db->mysql_clean($_POST['html_2']) . "','" . $db->mysql_clean($_POST['html_insert_2']) . "'";
		$update_q .= ",`html`='" . $db->mysql_clean($_POST['html_2']) . "',`html_insert`='" . $db->mysql_clean($_POST['html_insert_2']) . "'";
	}
	else if ($_POST['type'] == '3') {
		$category = '';
		$options_array = array(
			$_POST['format_3']
		);
		$insert1 .= ",'" . $db->mysql_clean($_POST['html_3']) . "','" . $db->mysql_clean($_POST['html_insert_3']) . "'";
		$update_q .= ",`html`='" . $db->mysql_clean($_POST['html_3']) . "',`html_insert`='" . $db->mysql_clean($_POST['html_insert_3']) . "'";
	}
	else if ($_POST['type'] == '4') {
		$category = $_POST['category_4'];
		if (empty($_POST['category_4'])) {
			$_POST['category_4'] = '0';
		}
		$options_array = array(
			$_POST['limit_4'],
			'',
			$_POST['order_4'],
			$_POST['dir_4']
		);
		$insert1 .= ",'" . $db->mysql_clean($_POST['html_4']) . "','" . $db->mysql_clean($_POST['html_insert_4']) . "'";
		$update_q .= ",`html`='" . $db->mysql_clean($_POST['html_4']) . "',`html_insert`='" . $db->mysql_clean($_POST['html_insert_4']) . "'";
	}
	else if ($_POST['type'] == '6') {
		$category = '';
		$options_array = array(
			$_POST['limit_6'],
			$_POST['user_type_6'],
			$_POST['order_6'],
			$_POST['dir_6']
		);
		$insert1 .= ",'" . $db->mysql_clean($_POST['html_6']) . "','" . $db->mysql_clean($_POST['html_insert_6']) . "'";
		$update_q .= ",`html`='" . $db->mysql_clean($_POST['html_6']) . "',`html_insert`='" . $db->mysql_clean($_POST['html_insert_6']) . "'";
	}
	else if ($_POST['type'] == '7') {
		$category = '';
		$options_array = array(
			'tags' => $_POST['tags_7'],
			'strict' => $_POST['strict_7'],
			'thumb_width' => $_POST['thumb_width_7'],
			'columns' => $_POST['cols_7'],
		);
		if ($_POST['lock_list_7'] == '1' || $_POST['refresh_list_7'] == '1') {
			$options_array['not_after'] = $db->current_date();
		}
		$insert1 .= ",'" . $db->mysql_clean($_POST['html_7']) . "','" . $db->mysql_clean($_POST['html_insert_7']) . "'";
		$update_q .= ",`html`='" . $db->mysql_clean($_POST['html_7']) . "',`html_insert`='" . $db->mysql_clean($_POST['html_insert_7']) . "'";
	}
	$ser_options = serialize($options_array);
	$insert .= ",`options`,`category`";
	$insert1 .= ",'$ser_options','$category'";
	$update_q .= ",`options`='$ser_options',`category`='$category'";
	
	// Now create the widget or make the changes
	if ($creating == "1") {
		// Create it
		$q = "INSERT INTO `" . TABLE_PREFIX . "widgets` ($insert) VALUES ($insert1)";
		$insert = $db->insert($q);
		// Complete the action
		$log = $db->complete_task('widgets_add',$user,'');
		// Reply
		echo "1+++$insert+++widgets";
		exit;
	} else {
		// Update it
		$q = "UPDATE `" . TABLE_PREFIX . "widgets` SET $update_q WHERE $where LIMIT 1";
		$update = $db->update($q);
		// Complete the action
		$log = $db->complete_task('widgets_edit',$user,'');
		// Reply
		echo "1+++" . lg_saved;
		exit;
	}

}

// ----------------------------------------------------------------------------------
//	Switch status

else if ($_POST['action'] == "switchStatus") {

	// Permissions
	$admin->check_permission('is_admin',$user,$privileges);
	
	// Current Switch
	$q = "SELECT `" . $db->mysql_clean($_POST['field']) . "` FROM `" . $db->mysql_clean($_POST['table']) . "` WHERE `id`='" . $db->mysql_clean($_POST['id']) . "' LIMIT 1";
	$status = $db->get_array($q);
	
	if ($status[$_POST['field']] == "1") {
		$new_status = '0';
		$image = "imgs/status-off.png";
	} else {
		$new_status = '1';
		$image = "imgs/status-on.png";
	}
	
	// Comments? Pending is opposite
	if ($_POST['table'] == TABLE_PREFIX . 'comments') {
		if ($status[$_POST['field']] == "1") {
			$image = "imgs/status-on.png";
		} else {
			$image = "imgs/status-off.png";
		}
	}
	
	// Make the switch
	$q1 = "UPDATE `" . $db->mysql_clean($_POST['table']) . "` SET `" . $db->mysql_clean($_POST['field']) . "`='$new_status' WHERE `id`='" . $db->mysql_clean($_POST['id']) . "' LIMIT 1";
	$update = $db->update($q1);
	
	//// Switch status on all subcomments
	//$all_subs = $manual->update_subcomments($_POST['id'],$new_status);

	// ---------------------------
	//	Caching?
	$comment_table = TABLE_PREFIX . "comments";
	if ($_POST['table'] == $comment_table && $db->get_option('cache_comments') == '1') {
		$this_comment = $manual->get_a_comment($_POST['id']);
		$manual->get_comments($this_comment['article'],'','','1');
	}

	// Reply
	echo "1+++$image";
	exit;

}


// ----------------------------------------------------------------------------------
//	Create or edit a comment type

else if ($_POST['action'] == "add_comment_type") {

	// Permissions
	$admin->check_permission('is_admin',$user,$privileges);

	// ----------------------------------------------------------------------------------
	//	Continue
	if (empty($_POST['title'])) {
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
			} else {
				$where = "`id`='" . $db->mysql_clean($value) . "'";
			}
		}
		// Action
		else if ($name == "action") {
			// Nothing
		}
		// All other fields
		else {
			$update_q .= ",`$name`='" . $db->mysql_clean($value) . "'";
			$insert .= ",`$name`";
			$insert1 .= ",'" . $db->mysql_clean($value) . "'";
		}
	}
	
	if ($creating == "1") {
		// Create it
		$insert = substr($insert,1);
		$insert1 = substr($insert1,1);
		$q = "INSERT INTO `" . TABLE_PREFIX . "comment_statuses` ($insert) VALUES ($insert1)";
		$insert = $db->insert($q);
		// Complete the action
		$log = $db->complete_task('comment_status_create',$user,'');
		// Reply
		echo "1+++$insert+++comment_types";
		exit;
	} else {
		// Update it
		$update_q = substr($update_q,1);
		$q = "UPDATE `" . TABLE_PREFIX . "comment_statuses` SET $update_q WHERE $where LIMIT 1";
		$update = $db->update($q);
		// Complete the action
		$log = $db->complete_task('comment_status_edit',$user,'');
		// Reply
		echo "1+++" . lg_saved;
		exit;
	}

}


// ----------------------------------------------------------------------------------
//	Edit Option

else if ($_POST['action'] == "edit_dl") {

	// Permissions
	$admin->check_permission('is_admin',$user,$privileges);

	$file_info = $db->get_file_info('','',$_POST['id']);
		
	// File?
	if (! empty($_FILES['file']['tmp_name'])) {
		$temp_ext = explode('.',$_FILES['file']['name']);
		$size = sizeof($temp_ext) - 1;
		$ext = $temp_ext[$size];
		if ($ext == $file_info['ext']) {
			$target = $file_info['path'];
	 		move_uploaded_file($_FILES['file']['tmp_name'],$target);
		}
	}
	
	// Update
	$q = "UPDATE `" . TABLE_PREFIX . "attachments` SET `login`='" . $db->mysql_clean($_POST['login']) . "',`limit`='" . $db->mysql_clean($_POST['limit']) . "' WHERE `id`='" . $db->mysql_clean($_POST['id']) . "' LIMIT 1";
	$update = $db->update($q);
	
	header('Location: ' . ADMIN_URL . '/index.php?l=downloads&saved=1');
	exit;

}


// ----------------------------------------------------------------------------------
//	Edit Plugin Option

else if ($_POST['action'] == "edit_plugin_options") {

	if (empty($_POST['plugin'])) {
		echo "0+++A plugin is required.";
		exit;
	}

	foreach ($_POST as $name => $value) {
		if ($name == 'action' || $name == 'plugin') {
		
		}
		else {
			$q = "UPDATE `" . TABLE_PREFIX . "options` SET `value`='" . $db->mysql_clean($value) . "' WHERE `id`='" . $db->mysql_clean($name) . "' AND `plugin`='" . $db->mysql_clean($_POST['plugin']) . "' LIMIT 1";
			$update = $db->update($q);
		}
	}
	echo "1+++Saved";
	exit;

}

// ----------------------------------------------------------------------------------
//	Edit Option

else if ($_POST['action'] == "edit_options") {

	// Permissions
	$admin->check_permission('is_admin',$user,$privileges);

	// ----------------------------------------------------------------------------------
	//	Continue
	if (empty($_POST['set'])) {
		echo "0+++Option set" . lg_something_required;
		exit;
	}
	
	// ----------------------------------------------------------------------------------
	// Loop through options set and update
	/*
	$q = "SELECT id FROM `" . TABLE_PREFIX . "options` WHERE `type`='1' AND `group`='" . $db->mysql_clean($_POST['set']) . "'";
	$result = $db->run_query($q);
	while ($row = mysql_fetch_array($result)) {
		$q = "UPDATE `" . TABLE_PREFIX . "options` SET `value`='" . $db->mysql_clean($_POST[$row['id']]) . "' WHERE `id`='" . $row['id'] . "' LIMIT 1";
		$update = $db->update($q);
	}
	*/
	
	// ----------------------------------------------------------------------------------
	//	Write globals file.
	
	$file = $admin->rewrite_global_options();
	
	// ----------------------------------------------------------------------------------
	//	Complete task.
	
	$log = $db->complete_task('options_update',$user,'');
	echo "1+++" . lg_saved;
	exit;	

}


// ----------------------------------------------------------------------------------
//	Custom Callers

else if ($_POST['action'] == 'add_caller') {

	$act_on_id = '';

	// Permissions
	$admin->check_permission('is_admin',$user,$privileges);
	
	if ($_POST['id'] == 'new') {
		if ($_POST['type'] == 'link') {
			$_POST['caller'] = $_POST['caller_2'];
			$_POST['replacement'] = $_POST['replacement_2'];
		}
		else if ($_POST['type'] == 'bubble') {
			$_POST['caller'] = $_POST['caller_3'];
			$_POST['replacement'] = $_POST['replacement_3'];
		}
		else {
			$_POST['caller'] = $_POST['caller_1'];
			$_POST['replacement'] = $_POST['replacement_1'];
		}
	}
	
	// Requirements?
	if (empty($_POST['caller'])) {
		echo "0+++Callert &amp; Replacement" . lg_something_required;
		exit;
	}
	
	if (empty($_POST['replacement'])) {
		if ($_POST['type'] == 'link') {
			if (! empty($_POST['article_name'])) {
				if (filter_var($_POST['article_name'], FILTER_VALIDATE_URL) === false) {
					echo "0+++Replacement must be a valid URL.";
					exit;
				} else {
					$_POST['replacement'] = $_POST['article_name'];
				}
			}
		} else {
			echo "0+++Replacement" . lg_something_required;
			exit;
		}
	}
	
	// Creating or editing?
	if ($_POST['id'] == 'new') {
		$log = $db->begin_task('point_values_add',$user,'');
	
		$q = "INSERT INTO `" . TABLE_PREFIX . "custom_callers` (`caller`,`replacement`,`type`) VALUES ('" . $db->mysql_clean($_POST['caller']) . "','" . $db->mysql_clean($_POST['replacement']) . "','" . $db->mysql_clean($_POST['type']) . "')";
		$insert = $db->insert($q);
	
		$log = $db->complete_task('custom_caller_add',$user,'');
			
		echo "1+++$insert+++replacements";
		exit;	
		
	} else {
		$log = $db->begin_task('custom_caller_edit',$user,'');
	
		$q = "UPDATE `" . TABLE_PREFIX . "custom_callers` SET `caller`='" . $db->mysql_clean($_POST['caller']) . "',`replacement`='" . $db->mysql_clean($_POST['replacement']) . "',`type`='" . $db->mysql_clean($_POST['type']) . "' WHERE `id`='" . $db->mysql_clean($_POST['id']) . "' LIMIT 1";
		$update = $db->update($q);
		
		$log = $db->complete_task('custom_caller_edit',$user,'');
		
		echo "1+++" . lg_saved;
		exit;	
	}
	
}


// ----------------------------------------------------------------------------------
//	Point Values

else if ($_POST['action'] == 'add_point_values') {

	$act_on_id = '';

	// Permissions
	$admin->check_permission('is_admin',$user,$privileges);
	
	// Requirements?
	if (empty($_POST['task'])) {
		echo "0+++Task" . lg_something_required;
		exit;
	} else {
		if ($_POST['task'] == 'comment_status_changed') {
			if (empty($_POST['comment_status'])) {
				echo "0+++Comment type" . lg_something_required;
				exit;
			} else {
				$act_on_id = $_POST['comment_status'];
			}
		}
	}
	
	// Type?
	if ($_POST['type'] == 'required') {
		$final_req = $_POST['points'];
		$final_points = '';
	} else {
		$final_req = '';
		$final_points = $_POST['points'];
	}
	
	// Comment status types
   	if ($_POST['task'] == 'comment_status_changed') {
   		if (empty($_POST['comment_status'])) {
   			echo "0+++Select a comment type.";
   			exit;
   		} else {
   			$add_where = " AND `act_on_id`='" . $_POST['comment_status'] . "'";
   		}
   	}
   	
   	// Exist already?
   	if ($_POST['type'] == 'required') {
   		$q1 = "SELECT `id` FROM `" . TABLE_PREFIX . "point_values` WHERE `task`='" . $db->mysql_clean($_POST['task']) . "' AND `required`!=''$add_where LIMIT 1";
   	} else {
   		$q1 = "SELECT `id` FROM `" . TABLE_PREFIX . "point_values` WHERE `task`='" . $db->mysql_clean($_POST['task']) . "' AND `points`!=''$add_where LIMIT 1";
   	}
   	
   	$found = $db->get_array($q1);
   	if (! empty($found['id'])) {
   		$link = "index.php?l=points_edit&id=" . $found['id'];
   		$msg = str_replace('%link%',$link,lg_admin_task_exists);
   		echo "0+++This task already exists. <a href=\"index.php?l=points_edit&id=" . $found['id'] . "\">Click here to edit it.</a>";
   		exit;
   	}
	
	// Creating or editing?
	if ($_POST['id'] == 'new') {
		$log = $db->begin_task('point_values_add',$user,'');
	
		$q = "INSERT INTO `" . TABLE_PREFIX . "point_values` (`task`,`points`,`required`,`act_on`,`act_on_id`) VALUES ('" . $db->mysql_clean($_POST['task']) . "','" . $db->mysql_clean($final_points) . "','" . $db->mysql_clean($final_req) . "','" . $db->mysql_clean($_POST['act_on']) . "','" . $db->mysql_clean($act_on_id) . "')";
		$insert = $db->insert($q);
	
		$log = $db->complete_task('point_values_add',$user,'');
			
		echo "1+++$insert+++points";
		exit;	
		
	} else {
		$log = $db->begin_task('point_values_edit',$user,'');
	
		$q = "UPDATE `" . TABLE_PREFIX . "point_values` SET `task`='" . $db->mysql_clean($_POST['task']) . "',`points`='" . $db->mysql_clean($final_points) . "',`required`='" . $db->mysql_clean($final_req) . "',`act_on`='" . $db->mysql_clean($_POST['act_on']) . "',`act_on_id`='" . $db->mysql_clean($act_on_id) . "' WHERE `id`='" . $db->mysql_clean($_POST['id']) . "' LIMIT 1";
		$update = $db->update($q);
		
		$log = $db->complete_task('point_values_edit',$user,'');
		
		echo "1+++" . lg_saved;
		exit;	
	}
	
}


// ----------------------------------------------------------------------------------
//	Badges

else if ($_POST['action'] == 'add_badge') {

	$act_on_id = '';

	// Permissions
	$admin->check_permission('is_admin',$user,$privileges);
	
	if (empty($_POST['name'])) {
		echo "0+++Name" . lg_something_required;
		exit;
	}
	
	if (empty($_POST['points_required'])) {
		echo "0+++Points required" . lg_something_required;
		exit;
	}

	if ($_POST['id'] == 'new') {
		$log = $db->begin_task('badge_add',$user,'');
	
		$q = "INSERT INTO `" . TABLE_PREFIX . "badges` (`name`,`desc`,`color`,`font_color`,`points_required`,`act`,`act_id`) VALUES ('" . $db->mysql_clean($_POST['name']) . "','" . $db->mysql_clean($_POST['desc']) . "','" . $db->mysql_clean($_POST['color']) . "','" . $db->mysql_clean($_POST['font_color']) . "','" . $db->mysql_clean($_POST['points_required']) . "','" . $db->mysql_clean($_POST['act']) . "','" . $db->mysql_clean($_POST['act_id']) . "')";
		$insert = $db->insert($q);
	
		$log = $db->complete_task('badge_add',$user,'');
		
		echo "1+++$insert+++badges";
		exit;	
	} else {
		$log = $db->begin_task('badge_edit',$user,'');
	
		$q = "UPDATE `" . TABLE_PREFIX . "badges` SET `name`='" . $db->mysql_clean($_POST['name']) . "',`desc`='" . $db->mysql_clean($_POST['desc']) . "',`color`='" . $db->mysql_clean($_POST['color']) . "',`font_color`='" . $db->mysql_clean($_POST['font_color']) . "',`points_required`='" . $db->mysql_clean($_POST['points_required']) . "',`act`='" . $db->mysql_clean($_POST['act']) . "',`act_id`='" . $db->mysql_clean($_POST['act_id']) . "' WHERE `id`='" . $db->mysql_clean($_POST['id']) . "' LIMIT 1";
		$update = $db->update($q);
	
		$log = $db->complete_task('badge_edit',$user,'');
		echo "1+++" . lg_saved;
		exit;	
	}

}

// ----------------------------------------------------------------------------------
//	Badges

else if ($_POST['action'] == 'give_badge') {

	$act_on_id = '';

	// Permissions
	$admin->check_permission('is_admin',$user,$privileges);
	
	if (empty($_POST['id'])) {
		echo "0+++Badge" . lg_something_required;
		exit;
	}
	
	if (empty($_POST['user'])) {
		echo "0+++User" . lg_something_required;
		exit;
	}
	
	// Already have it?
	$q1 = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "badge_history` WHERE `user_id`='" . $db->mysql_clean($_POST['user']) . "' AND `badge`='" . $db->mysql_clean($_POST['id']) . "'";
	$find = $db->get_array($q1);
	if ($find['0'] > 0) {
		echo "0+++User already has this badge.";
		exit;
	}
		
	// Add it
	$badge_info = $manual->get_badge($_POST['id']);
	$give = $manual->give_user_badge($_POST['id'],$_POST['user'],'',$badge_info,'');
	
	// Render it
	$formatted = $session->format_badge($badge_info);
	
	echo "1+++$formatted";
	exit;

}

// ----------------------------------------------------------------------------------
//	Delete a category

else if ($_POST['action'] == "del_category") {
	// Permissions
	$admin->check_permission('can_delete_categories',$user,$privileges);
	// Continue
	if (empty($_POST['id'])) {
		echo "0+++Category ID" . lg_something_required;
		exit;
	}
	// Base category?
	$q1 = "SELECT `base` FROM `" . TABLE_PREFIX . "categories` WHERE `id`='" . $db->mysql_clean($_POST['id']) . "' LIMIT 1";
	$del_1 = $db->get_array($q1);
	if ($del_1['base'] == '1') {
		echo "0+++" . lg_no_del_base;
		exit;
	}
	// Delete it
	$q = "DELETE FROM `" . TABLE_PREFIX . "categories` WHERE `id`='" . $db->mysql_clean($_POST['id']) . "' AND `base`!='1' LIMIT 1";
	$delete = $db->delete($q);
	// Cache Category List
	if ($db->get_option('cache_category_list') == '1') {
		$cache_list = $manual->category_tree('','1','1');
	}
	// Complete the action
	$log = $db->complete_task('category_del',$user,'');
	echo "1+++" . lg_deleted;
	exit;
}


// ----------------------------------------------------------------------------------
//	Delete Something for anything
//	requiring the user to be an administrator

else if ($_POST['action'] == "delete") {

	// ------------------------------------------
	// Permissions
	$admin->check_permission('is_admin',$user,$privileges);
	
	// ------------------------------------------
	// Table Prefix?
	if (strpos($_POST['table'], TABLE_PREFIX) === false) {
		$_POST['table'] = TABLE_PREFIX . $_POST['table'];
	}
	
	// ------------------------------------------
	// 	Users?
	// 	If so, only the primary admin
	if ($_POST['table'] == TABLE_PREFIX . "users") {
		// Only the primary admin can delete
		// other administrators.
		$deleting_type = $session->get_user_type('',$_POST['id']);
		$myId = $session->get_user_id($user);
		// Administrator trying to delete another admin?
		if ($privileges['is_admin'] == "1" && $deleting_type['id'] == '1' && $myId != '1') {
   			echo "0+++" . lg_admin_nodel_admin;
   			exit;
		}
		// Deleting yourself?
		else if ($_POST['id'] == $myId) {
   			echo "0+++" . lg_admin_nodel_yourself;
   			exit;
		}
		// You can never delete the primary
		// admin account, ID '1'.
		else if ($_POST['id'] == '1') {
			echo "0+++" . lg_admin_nodel_primary_admin;
			exit;
		}
	}
	
	
	// ------------------------------------------
	// 	Special Considerations: pre-delete
	
	$skip_query = 0;
	if ($_POST['table'] == TABLE_PREFIX . "comments" && $db->get_option('cache_comments') == '1') {
		$del = $manual->delete_comment($_POST['id']);
		$skip_query = '1';
	}
	
	else if ($_POST['table'] == TABLE_PREFIX . "categories") {
		$del = $manual->delete_category($_POST['id']);
		$skip_query = '1';
	}

	else if ($_POST['table'] == TABLE_PREFIX . "attachments") {
		$file = $db->get_file_info('','',$_POST['id']);
		$unlink = @unlink($file['path']);
	}
	
	else if ($_POST['table'] == TABLE_PREFIX . "articles") {
		$manual->delete_page($_POST['id']);
		$skip_query = '1';
	}
	
	else if ($_POST['table'] == TABLE_PREFIX . "templates_html") {
		$path = PATH . "/generated/template-" . $_POST['id'] . ".php";
		$unlink = @unlink($path);
	}
			
	// ------------------------------------------
	// Run the query
	if ($skip_query != '1') {
		$q = "DELETE FROM `" . $db->mysql_clean($_POST['table']) . "` WHERE `id`='" . $db->mysql_clean($_POST['id']) . "' LIMIT 1";
		$delete = $db->delete($q);
	}
	
	// ------------------------------------------
	// 	Special Considerations: post-delete
	
	if ($_POST['table'] == TABLE_PREFIX . "users") {
		$username = $session->get_username_from_id($_POST['id']);
		$q1 = "DELETE FROM `" . TABLE_PREFIX . "users` WHERE `id`='" . $db->mysql_clean($_POST['id']) . "'";
		$delete = $db->delete($q1);
		$q2 = "DELETE FROM `" . TABLE_PREFIX . "user_data` WHERE `user_id`='" . $db->mysql_clean($_POST['id']) . "'";
		$delete = $db->delete($q2);
		$q3 = "DELETE FROM `" . TABLE_PREFIX . "fbconnect` WHERE `user_id`='" . $db->mysql_clean($_POST['id']) . "'";
		$delete = $db->delete($q3);
		$q4 = "DELETE FROM `" . TABLE_PREFIX . "favorites` WHERE `user_id`='" . $db->mysql_clean($_POST['id']) . "'";
		$delete = $db->delete($q4);
		$q5 = "DELETE FROM `" . TABLE_PREFIX . "user_notices` WHERE `username`='$username'";
		$delete = $db->delete($q5);
		$q6 = "DELETE FROM `" . TABLE_PREFIX . "user_permissions` WHERE `user_id`='" . $db->mysql_clean($_POST['id']) . "'";
		$delete = $db->delete($q6);
		$q7 = "DELETE FROM `" . TABLE_PREFIX . "following` WHERE `user_id`='" . $db->mysql_clean($_POST['id']) . "'";
		$delete = $db->delete($q7);
		$q8 = "DELETE FROM `" . TABLE_PREFIX . "username` WHERE `user_id`='$username'";
		$delete = $db->delete($q8);
		$q9 = "DELETE FROM `" . TABLE_PREFIX . "badge_history` WHERE `user_id`='" . $db->mysql_clean($_POST['id']) . "'";
		$delete = $db->delete($q9);
	}

	else if ($_POST['table'] == TABLE_PREFIX . "fields") {
		$q1 = "DELETE FROM `" . TABLE_PREFIX . "fields_sets_comps` WHERE `field_id`='" . $db->mysql_clean($_POST['id']) . "'";
		$delete = $db->delete($q1);
	}

	else if ($_POST['table'] == TABLE_PREFIX . "attachments_dls") {
		$q1 = "UPDATE `" . TABLE_PREFIX . "attachments` SET `downloads`=(`downloads`-1) WHERE `id`='" . $db->mysql_clean($_POST['id']) . "' LIMIT 1";
		$update = $db->update($q1);
	}
	
	// ------------------------------------------
	// Complete the action
	$log = $db->complete_task('delete',$user,$_POST['id']);

	// ------------------------------------------
	// Reply
	echo "1+++" . lg_deleted;
	exit;
	
}


// ----------------------------------------------------------------------------------
//	Add a template

else if ($_POST['action'] == "add_html_template") {

	// Permissions
	$admin->check_permission('is_admin',$user,$privileges);
   	$log = $db->begin_task('template_html_add',$user,'');

	// Content submitted?
	if (empty($_POST['content'])) {
		echo "0+++Content" . lg_something_required;
		exit;
	}
	
	$folder = PATH . "/generated";
   	if (! is_writable($folder)) {
   		$msg = str_replace('%directory%','generated',lg_upload_notwritable);
   		echo "0+++" . $msg;
   		exit;
   	}
	
	// Current template info
	$temp_into = $template->template_data('',$_POST['id']);
	
	$custom_header = '';
	$custom_footer = '';
	if ($temp_into['template'] == 'header') {
		$type = '1';
	}
	else if ($temp_into['template'] == 'footer') {
		$type = '2';
	}
	else {
		$type = '3';
		if (! empty($_POST['custom_header'])) {
			$custom_header = $_POST['custom_header'];
		}
		if (! empty($_POST['custom_footer'])) {
			$custom_footer = $_POST['custom_footer'];
		}
	}
	
	// DB entry
	$q = "INSERT INTO `" . TABLE_PREFIX . "templates_html` (`template`,`title`,`desc`,`caller_tags`,`custom_header`,`custom_footer`,`type`) VALUES ('" . $temp_into['template'] . "','" . $_POST['title'] . "','" . $_POST['desc'] . "','" . $temp_into['caller_tags'] . "','" . $db->mysql_clean($custom_header) . "','" . $db->mysql_clean($custom_footer) . "','$type')";
	$final_id = $db->insert($q);
	
	// Write the file
	$path = $folder . "/template-" . $final_id . ".php";
   	$fh = fopen($path, 'w');
   	fwrite($fh, $_POST['content']);
   	fclose($fh);
   	
   	// Complete the action
   	$log = $db->complete_task('template_html_add',$user,'');
   	echo "1+++$final_id+++templates_html";
   	exit;
}


// ----------------------------------------------------------------------------------
//	Edit a template

else if ($_POST['action'] == "edit_html_template") {

	// Permissions
	$admin->check_permission('is_admin',$user,$privileges);
   	$log = $db->begin_task('template_html_edit',$user,'');
   	
	// Content submitted?
	if (empty($_POST['content'])) {
		echo "0+++Content" . lg_something_required;
		exit;
	}
	
	// Path
	$ext = '.php';
	
	// Begin the MySQL statement
	$set_statement = "`title`='" . $db->mysql_clean($_POST['title']) . "',`desc`='" . $db->mysql_clean($_POST['desc']) . "'";
	// Add whatever else needs to be updated
	// and get the file write path
		
	if ($_POST['type'] == '1' || $_POST['type'] == '2' || $_POST['type'] == '3') {
	
   		if (! empty($_POST['path'])) {
   			$path = PATH . "/templates/html/" . $theme . "/" . $_POST['path'];
   		} else {
   			$path = PATH . "/generated/template-" . $_POST['id'] . $ext;
   		}
	
	} else {
	
		$path = PATH . "/templates/html/" . $theme . "/" . $_POST['template'] . $ext;
	
	}
	
   	if ($_POST['type'] == '3' || $_POST['type'] == '0') {
   		if (isset($_POST['custom_header'])) {
   			$set_statement .= ",`custom_header`='" . $db->mysql_clean($_POST['custom_header']) . "'";
   		}
   		if (isset($_POST['custom_footer'])) {
   			$set_statement .= ",`custom_footer`='" . $db->mysql_clean($_POST['custom_footer']) . "'";
   		}
   	}
   	
	// Basic DB updating
	$q = "UPDATE `" . TABLE_PREFIX . "templates_html` SET $set_statement WHERE `id`='" . $_POST['id'] . "' LIMIT 1";
	$update = $db->update($q);
	
	// Write the file
   	if (! is_writable($path)) {
   		$msg = str_replace('%directory%',$path,lg_upload_notwritable);
   		echo "0+++" . $msg;
   		exit;
   	} else {
   		$fh = fopen($path, 'w');
   		fwrite($fh, $_POST['content']);
   		fclose($fh);
   	}
   	// Complete the action
   	$log = $db->complete_task('template_html_edit',$user,'');
   	echo "1+++" . lg_saved;
   	exit;
}

// ----------------------------------------------------------------------------------
//	Edit a template

else if ($_POST['action'] == "edit_template") {
	// Permissions
	$admin->check_permission('is_admin',$user,$privileges);
	// Requirements
	if (empty($_POST['to'])) {
		echo "0+++To" . lg_something_required;
		exit;
	}
	if (empty($_POST['subject'])) {
		echo "0+++Subject" . lg_something_required;
		exit;
	}
	if (empty($_POST['content'])) {
		echo "0+++Content" . lg_something_required;
		exit;
	}
   	// Custom templates are stored in the DB
   	if ($_POST['custom'] == "1") {
   		if ($_POST['id'] == "new") {
      		$insert = ",`override_content`,`created`,`created_by`";
      		$insert1 = ",'" . $db->mysql_clean($_POST['content']) . "','" . $db->current_date() . "','$user'";
   		} else {
      		$update_q = ",`override_content`='" . $db->mysql_clean($_POST['content']) . "'";
   		}
   	}
   	// Standard templates are physical files
   	else {
   		$file = PATH . "/templates/email/" . $_POST['template'] . ".html";
   		if (is_writable($file)) {
   			$fh = fopen($file, 'w');
   			fwrite($fh, $_POST['content']);
   			fclose($fh);
   		}
   	}
	// Get template
	$template_info = $admin->get_template($_POST['clone_id']);
   	$insert .= ",`caller_tags`";
   	$insert1 .= ",'" . $template_info['caller_tags'] . "'";
	// Prepare the MySQL commands
	$ignore = array('content','clone_id');
	$commands = $admin->prepare_commands($_POST,$ignore,TABLE_PREFIX . 'templates');
	// Update DB
	if ($_POST['id'] == "new") {
		// Create it
		$q = "INSERT INTO `" . TABLE_PREFIX . "templates` (" . $commands['insert'] . "$insert) VALUES (" . $commands['insert1'] . "$insert1)";
		$insert = $db->insert($q);
		// Complete the action
		$log = $db->complete_task('template_create',$user,'');
		// Reply
		echo "1+++$insert+++templates_email_edit";
		exit;
	} else {
		$q = "UPDATE `" . TABLE_PREFIX . "templates` SET " . $commands['update'] . "$update_q WHERE " . $commands['where'] . " LIMIT 1";	
		$update = $db->update($q);
		// Complete the action
		$log = $db->complete_task('template_edit',$user,'');
		echo "1+++" . lg_saved;
		exit;
	}
}


// ----------------------------------------------------------------------------------
//	Set a theme

else if ($_POST['action'] == "set_theme") {
	// Permissions
	$admin->check_permission('is_admin',$user,$privileges);
	// Checks
	if (empty($_POST['theme'])) {
		echo "0+++Theme" . lg_something_required;
		exit;
	}
	// Type
	if ($_POST['type'] == '1') {
		$where = " AND `mobile`!='1'";
	} else {
		$where = " AND `mobile`='1'";
	}
	// Database update.
	$q = "UPDATE `" . TABLE_PREFIX . "themes` SET `selected`='0' WHERE `selected`='1' $where";
	$update = $db->update($q);
	$q1 = "UPDATE `" . TABLE_PREFIX . "themes` SET `selected`='1' WHERE `folder_name`='" . $db->mysql_clean($_POST['theme']) . "'$where LIMIT 1";
	$update1 = $db->update($q1);
	// ----------------------------------------------------------------------------------
	//	Write globals file.
	$file = $admin->rewrite_global_options();
	// Reply
	echo "1+++" . lg_saved;
	exit;
}


// ----------------------------------------------------------------------------------
//	Make an article the default "Homepage" for it's
//	category.

else if ($_POST['action'] == "make_default") {
	// Permissions
	$admin->check_permission('can_alter_categories',$user,$privileges);
	// Continue
	if (empty($_POST['id']) && $_POST['id'] != '0') {
		echo "0+++Category ID" . lg_something_required;
		exit;
	}
	if (empty($_POST['article'])) {
		echo "0+++Article ID" . lg_something_required;
		exit;
	}
	// Current default
	$category = $manual->get_category($_POST['id']);
	// Check if it's a default on a category
	$q1 = "UPDATE `" . TABLE_PREFIX . "categories` SET `home_article`='" . $db->mysql_clean($_POST['article']) . "' WHERE `id`='" . $db->mysql_clean($_POST['id']) . "' LIMIT 1";
	$update = $db->update($q1);
	// Complete the action
	$log = $db->complete_task('article_make_homepage',$user,'');
	echo "1+++" . $_POST['article'] . "+++" . $category['home_article'] . "+++" . $_POST['id'];
	exit;
}

?>