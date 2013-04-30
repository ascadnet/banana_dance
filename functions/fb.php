<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Facebook connect integration.
	
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

// ------------------------------------------------------------------
// 	Only need to do this if the the user
//	doesn't already have a session in
//	Banana Dance.

if (empty($user)) {

	$app_id = $db->get_option('fb_app_id');
	$app_secret = $db->get_option('fb_app_secret');
	
	$fb_cookie = get_facebook_cookie($app_id, $app_secret);
	
	$user_fb_data = json_decode(file_get_contents( 'https://graph.facebook.com/me?access_token=' . $fb_cookie['access_token']));
		
		// ------------------------------------------------------------------
		// 	User already in DB?
		
		// Check for a facebook ID
		$found_a_user = '0';
		$q = "SELECT `username` FROM `" . TABLE_PREFIX . "users` WHERE `email`='" . $user_fb_data->email . "' LIMIT 1";
		$found = $db->get_array($q);
		if (empty($found['username'])) {
			$q1 = "SELECT `user_id` FROM `" . TABLE_PREFIX . "fbconnect` WHERE `fb_id`='" . $user_fb_data->id . "' LIMIT 1";
			$found1 = $db->get_array($q1);
			if (! empty($found1['user_id'])) {
				$found_a_user = '1';
				$final_username = $session->get_username_from_id($found1['user_id']);
			}
		} else {
			$found_a_user = '1';
			$final_username = $found['username'];
		}
		
		// ------------------------------------------------------------------
		// 	Did we find an existing user?
		
		if ($found_a_user != '1') {
		
			require PATH . "/includes/password.functions.php";
			$password = new password;
			
			// Get a username for this user
			$prefix = strtolower(substr($user_fb_data->first_name,0,1)) . strtolower(substr($user_fb_data->last_name,0,1));
			$final_username = $password->generate_username($prefix);
		
			// Generate a password
			$rand_pass = uniqid();
			$salt = $password->generate_salt($final_username);
			$encoded_pass = $password->encode_password($rand_pass,$salt);   		
		
			// Create the user in BD
			$fb_profile_url = "http://graph.facebook.com/" . $user_fb_data->id . "/picture?type=large";
			$more_data = array(
				'picture' => $fb_profile_url
			);
			$insert = $password->create_user($final_username,$encoded_pass,$salt,$user_fb_data->email,$user_fb_data->name,$more_data);
		
			// Add the facebook connect reference
			$q = "INSERT INTO `" . TABLE_PREFIX . "fbconnect` (`fb_id`,`user_id`) VALUES ('" . $user_fb_data->id . "','" . $insert . "')";
			$go = $db->insert($q);
			
			// E-Mail the user
		   	$special_changes = array(
		   		'%username%' => $final_username,
		   		'%name%' => $user_fb_data->name,
		   		'%email%' => $user_fb_data->email,
		   		'%password%' => $rand_pass
		   	);
		   	$sent = $template->send_template($final_username,'fb_connect_account',"",$special_changes);
		
		}
		
		$start_session = $session->start_session($final_username,'0');
	
		echo $final_username;
		exit;

}

?>