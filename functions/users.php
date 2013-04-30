<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: User account management.
	
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

if (empty($user)) {
	echo "0+++" . lg_login_to_use_feature;
	exit;
} else {
	
	// ---------------------------------------------------------------------------------
	// 	Edit the account
	if ($_POST['action'] == "edit_account") {
		
		// ------------------------------------------
		// 	Check the current password
		require PATH . "/includes/password.functions.php";
		$password = new password;
		
		// Is the user's current password correct?
		if (empty($_POST['current_password'])) {
			echo "0+++" . lg_incorrect_password;
			exit;
		} else {
			$q = "SELECT `id`,`salt`,`password` FROM `" . TABLE_PREFIX . "users` WHERE `username`='$user' LIMIT 1";
			$salt = $db->get_array($q);
	   		$check_pass = $password->encode_password($_POST['current_password'],$salt['salt']);
	   		if ($check_pass != $salt['password']) {
				echo "0+++" . lg_incorrect_password;
				exit;
	   		}
		}
		
		$primary_update = '';
		
		// ------------------------------------------
		// 	New password?
		if (! empty($_POST['pass'])) {
			$password_checks = '1';
		} else {
			$password_checks = '0';
		}
		
		// ------------------------------------------
		// 	Email checks?
		if ($_POST['email'] != $user_data['email']) {
			$email_checks = '1';
		} else {
			$email_checks = '0';
		}
	
		// ------------------------------------------
		// 	Now run the standard account update checks.
		$password->update_account_checks($password_checks,$email_checks);
	
	
		// ------------------------------------------
		//	Encode and update the new password
		if ($password_checks == '1') {
			$saltgen = $password->generate_salt($user_data['username']);
			$encoded_pass = $password->encode_password($_POST['pass'],$saltgen);
			$primary_update .= ",`password`='$encoded_pass',`salt`='$saltgen'";
		}
	
		// ------------------------------------------
		// 	Strip Tags?
		if ($privileges['post_code'] != '1') {
			foreach ($_POST as $name => $value) {
				if ($name == 'current_password' || $name == 'pass' || $name == 'pass1' ||  $name == 'email' || $name == 'action') {
					// Don't mess with these...
				} else {
					$_POST[$name] = strip_tags($value);
				}
			}
		}
	
		// ------------------------------------------
		// 	Additional Information
		$add_on = "";
		$ignore = array('username','current_password','pass','pass1','name','email','action');
		foreach ($_POST as $name => $value) {
			if (! in_array($name,$ignore)) {
				// Create the field in the DB
				$q1 = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "fields` WHERE `id`='" . $db->mysql_clean($name) . "'";
				$found = $db->get_array($q1);
				if ($found['0'] <= 0) {
					$leng = strlen($value);
					if ($leng > 255) {
						$type = '2';
					} else {
						$type = '1';
					}
					$display_name = str_replace('_',' ',$name);
					$display_name = ucwords($display_name);
					// Add to fields
					$q = "INSERT INTO `" . TABLE_PREFIX . "fields` (`id`,`display_name`,`type`) VALUES ('" . $db->mysql_clean($name) . "','" . $db->mysql_clean($display_name) . "','$type')";
					$add = $db->insert($q);
					// Add to main field set
					$q1 = "INSERT INTO `" . TABLE_PREFIX . "fields_sets_comps` (`set_id`,`field_id`,`col`) VALUES ('1','" . $db->mysql_clean($name) . "','1')";
					$add = $db->insert($q1);
				}
			}
		}
		
		// ------------------------------------------
		// 	Update existing information
		// 	We use isset rather than empty
		// 	because required field checks
		// 	are handled in "update_account_checks"
		// 	above. Therefore if they make it this
		// 	far and are empty, the update stands.
		if (isset($_POST['name'])) {
			$primary_update .= ",`name`='" . $db->mysql_clean($_POST['name']) . "'";
		}
		if (isset($_POST['email'])) {
			$primary_update .= ",`email`='" . $db->mysql_clean($_POST['email']) . "'";
		}
		
		if (! empty($primary_update)) {
			$primary_update = ltrim($primary_update,',');
			$q = "UPDATE `" . TABLE_PREFIX . "users` SET $primary_update WHERE `id`='" . $salt['id'] . "' LIMIT 1";
			$update = $db->update($q);
		}
		
		// ------------------------------------------
		// 	Update secondary information
		$q1 = "SELECT `id` FROM `" . TABLE_PREFIX . "fields`";
		$returned = $db->run_query($q1);
		while ($row = mysql_fetch_array($returned)) {
			if (isset($_POST[$row['id']])) {
				$update = $db->update_eav($row['id'],$_POST[$row['id']],$salt['id'],'user_id');
			}
		}
		
		// ------------------------------------------
		//	Reply
		echo "1+++" . lg_saved;
		exit;
	}
	
	// ---------------------------------------------------------------------------------
	// 	Remove a profile pic
	else if ($_POST['action'] == 'remove_pic') {
	
		if (! empty($user_data['picture'])) {
			$file = PATH . "/generated/" . $user_data['picture'];
			$del = @unlink($file);
			$file1 = PATH . "/generated/" . $user_data['thumbnail'];
			$del = @unlink($file1);
			$q = "DELETE FROM `" . TABLE_PREFIX . "user_data` WHERE `user_id`='" . $user_data['id'] . "' AND `key`='picture' LIMIT 1";
			$del = $db->delete($q);
			$q = "DELETE FROM `" . TABLE_PREFIX . "user_data` WHERE `user_id`='" . $user_data['id'] . "' AND `key`='thumbnail' LIMIT 1";
			$del = $db->delete($q);
			$path = URL . "/templates/html/$theme/imgs/profile_none_lg.jpg";
			echo "1+++" . $path;
			exit;
		} else {
			echo "0+++" . lg_error;
		}
		exit;
	
	}

}


?>