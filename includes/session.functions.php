<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: User and session functions.
	
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

class session extends db {

	var $captcha_show = "3"; // Failed attempts before requiring CAPTCHA
	var $max_updates = "5"; // Spam prevention: max failed attempts or rapid succession attempts
	var $lock_out_spam = "5"; // Minutes

	// -----------------------------------------------------------------------------
	// 	Check's for an active session
	//	return a username if active.
	
	function check_logged($skip_headers = "0") {
		$session_length = (3600 * $this->get_option('session_length'));
		if (! empty($_COOKIE['mn_session'])) {
			$q = "SELECT * FROM `" . TABLE_PREFIX . "sessions` WHERE `id`='" . $_COOKIE['mn_session'] . "' LIMIT 1";
			$session_info = $this->get_array($q);
			if (! empty($session_info['user']) && $session_info['ended'] == '0000-00-00 00:00:00') {
				$check_it = time() - strtotime($session_info['last_activity']);
				if ($check_it >= $session_length && $session_info['remember'] != '1') {
					$q1 = "UPDATE `" . TABLE_PREFIX . "sessions` SET `ended`='" . $this->current_date() . "' WHERE `id`='" . $_COOKIE['mn_session'] . "' LIMIT 1";
					$update = $this->update($q1);
					if ($skip_headers != '1') {
						$this->kill_session($_COOKIE['mn_session']);
					}
					return "0";
				}
				// Valid session!
				else {
					$update = $this->update_session($_COOKIE['mn_session']);
					return $session_info['user'];
				}
			} else {
   				if ($skip_headers != '1') {
   					$this->kill_session($_COOKIE['mn_session']);
   				}
				return "0";
			}
		} else {
			return "0";
		}
	}


	// -----------------------------------------------------------------------------
	// 	Get a user's privileges
	
	function get_user_privileges($username) {
		// Basic Information
		$q = "SELECT `type` FROM `" . TABLE_PREFIX . "users` WHERE `username`='$username' LIMIT 1";
		$user = $this->get_array($q);
		// User type settings
		$privs = $this->get_usertype_settings($user['type']);
		// Additional Privileges
		// This is used mainly for adminstrators on the control panel.
		/*
		$q1 = "SELECT `pemission` FROM `" . TABLE_PREFIX . "user_permissions` WHERE `user`='$username' LIMIT 1";
		$additional_permissions = $this->run_query($q1);
    		while ($row = mysql_fetch_array($additional_permissions)) {
    			$privs[$row['permission']] = "1";
    		}
    		*/
		return $privs;
	}
	
	
	// ---------------------------------------------------------------------------
	// 	Determine an account's "type"
	
	function get_user_type($username,$user_id = "") {
		if (! empty($user_id)) {
			$where = "`id`='$user_id'";
		} else {
			$where = "`username`='$username'";
		}
		// User type
		$q = "SELECT `type` FROM `" . TABLE_PREFIX . "users` WHERE $where LIMIT 1";
		$type = $this->get_array($q);
		// Type Settings
		$settings = $this->get_usertype_settings($type['type']);
		return $settings;
	}
	
	
	// ---------------------------------------------------------------------------
	// 	Get a user type settings
	
	function get_usertype_settings($status,$select = '*') {
		$q = "SELECT $select FROM `" . TABLE_PREFIX . "user_types` WHERE `id`='$status' LIMIT 1";
		$settings = $this->get_array($q);
		return $settings;
	}
	

	// -----------------------------------------------------------------------------
	// 	Create a spam session if necessary
	
	function start_spam_session() {
		$session_store = uniqid();
 		// Create the attempt
 		$q = "INSERT INTO `" . TABLE_PREFIX . "spam` (`id`,`update`,`ip`,`last_activity`,`started`) VALUES ('$session_store','1','" . $_SERVER['REMOTE_ADDR'] . "','" . time(). "','" . time() . "')";
 		$insert = $this->insert($q);
 		// Cookie
 		$this->create_cookie('session_temp',$session_store);
 		// Return
 		return $session_store;
	}
	
	// -----------------------------------------------------------------------------
	// 	Delete a spam session if necessary
	
	function delete_spam_session() {
		$q = "DELETE FROM `" . TABLE_PREFIX . "spam` WHERE `ip`='" . $_SERVER['REMOTE_ADDR'] . "'";
		$del = $this->delete($q);
		$cook_del = $this->delete_cookie('session_temp');
	}
	
	// -----------------------------------------------------------------------------
	// 	Update/check the status of a spam session if necessary
	//	Run this when an error occurs or a process is submitted
	
	function check_spam_session($source = "",$position = "") {
		// Session details
   		$q1 = "SELECT `update`,`id`,`out_until`,`proven_captcha`,`last_activity`,`captcha` FROM `" . TABLE_PREFIX . "spam` WHERE `ip`='" . $_SERVER['REMOTE_ADDR'] . "' LIMIT 1";
   		$found = $this->get_array($q1);
   		$difference = time() - strtotime($found['last_activity']);
		// Found a cookie or given ID
		if (! empty($found['id'])) {
			// Is the user locked out?
			if (! empty($found['out_until'])) {
				// Unlock the session
				if (time() >= $found['out_until']) {
					$q = "UPDATE `" . TABLE_PREFIX . "spam` SET `out_until`='',`update`='1' WHERE `ip`='" . $_SERVER['REMOTE_ADDR'] . "' LIMIT 1";
					$update = $this->update($q);
				}
				// Error out the request.
				else {
					$difference = $found['out_until'] - time();
					if ($difference > "60") {
						$show_dif = ceil($difference/60) . " " . lg_minutes;
					} else {
						$show_dif = $difference . " " . lg_seconds;
					}
					$message = str_replace('%difference%',$show_dif,lg_excessive_attempts_try_again);
					echo "0+++" . $message;
					exit;
				}
			}
			// Not locked out, update the session
			else {
				// Too quick on the key stroke?
				if ($difference <= "1") {
					echo "0+++" . lg_slow_down;
					exit;
				}
				else {
	   				$new_update = $found['update'] + 1;
	   				// Too many fails!
	   				if ($new_update >= $this->max_updates) {
	   					$locked_until = time()+($this->lock_out_spam*60);
	   					$q = "UPDATE `" . TABLE_PREFIX . "spam` SET `last_activity`='" . date('Y-m-d H:i:s') . "',`update`='0',`out_until`='$locked_until' WHERE `ip`='" . $_SERVER['REMOTE_ADDR'] . "' LIMIT 1";
	   					$update = $this->update($q);
	   					$dif = $this->lock_out_spam . " " . lg_minutes;
						$message = str_replace('%difference%',$dif,lg_excessive_attempts_try_again);
	   					echo "0+++" . $message;
	   					exit;
	   				}
	   				// Generate the CAPTCHA
    					else if ($new_update >= $this->captcha_show && $found['proven_captcha'] != '1') {
	   					require PATH . "/includes/captcha.functions.php";
	   					$captcha = new captcha;
	   					$gen_captcha = $captcha->generate_captcha('words');
	   					$q = "UPDATE `" . TABLE_PREFIX . "spam` SET `last_activity`='" . date('Y-m-d H:i:s') . "',`captcha`='$gen_captcha',`update`='$new_update' WHERE `ip`='" . $_SERVER['REMOTE_ADDR'] . "' LIMIT 1";
	   					$update = $this->update($q);
	   				}
	   				// Failed attempt, update the session's "fails" by 1
	   				else {
	   					$q = "UPDATE `" . TABLE_PREFIX . "spam` SET `last_activity`='" . time() . "',`update`='$new_update' WHERE `ip`='" . $_SERVER['REMOTE_ADDR'] . "' LIMIT 1";
	   					$update = $this->update($q);
	   				}
   				}
			}
		}
		// No session ID found based on cookie
		// or the user's IP. Create one.
		else {
			$spam_session = $this->start_spam_session();
		}
	}
	
	// -----------------------------------------------------------------------------
	// 	Check a spam session's current status
	
	function current_spam_session() {
		global $banned;
		// Partial Ban?
		if (! empty($banned)) {
			$difference = $banned - time();
			if ($difference > 0) {
				if ($difference > 60) {
					$show = ceil($difference/60) . " minute(s).";
				} else {
					$show = $difference . " second(s).";
				}
				$show = str_replace('%difference%',$show,lg_excessive_attempts_try_again);
				echo "0+++" . $show;
				exit;
			}
		}
		// Session details
   		$q1 = "SELECT `update`,`captcha`,`proven_captcha` FROM `" . TABLE_PREFIX . "spam` WHERE `ip`='" . $_SERVER['REMOTE_ADDR'] . "' LIMIT 1";
   		$found = $this->get_array($q1);
   		// CAPTCHA Required?
   		if (! empty($found['captcha']) && $found['proven_captcha'] != '1') {
   			echo "0+++" . lg_prove_you_are_human . "+++1";
   			exit;
   		}
   		return $found;
	}
	
	// -----------------------------------------------------------------------------
	// 	Get spam CAPTCHA
	function get_spam_captcha() {
		// Session details
   		$q1 = "SELECT `captcha`,`failed_captcha` FROM `" . TABLE_PREFIX . "spam` WHERE `ip`='" . $_SERVER['REMOTE_ADDR'] . "' LIMIT 1";
   		$found = $this->get_array($q1);
   		return $found;
	}

	// -----------------------------------------------------------------------------
	// 	Ban a user
	//	$type -> 1 = partial, 0 = full
	//	Partial means can't do actions, full means can't see site.
	function ban_user($reason,$username = "",$ip = "",$email = "",$until = "",$type = "0",$banned_username = "") {
		global $template;
		global $user;
		// Ban the user
		if (! empty($username)) {
			$q = "UPDATE `" . TABLE_PREFIX . "users` SET `status`='1' WHERE `username`='" . $db->mysql_clean($username) . "' LIMIT 1";
			$update = $db->update($q);
			// E-Mail the User
		   	$special_changes = array(
		   		'%username%' => $_POST['username'],
		   		'%reason%' => $reason
		   	);
		   	$sent = $template->send_template($_POST['username'],'banned',"",$special_changes);
		}
		// Ban the IP
		if (empty($user)) {
			$user = "system";
		}
		$q1 = "INSERT INTO `" . TABLE_PREFIX . "banned` (`ip`,`date`,`email`,`reason`,`banned_by`,`banned_until`,`ban_type`,`username`) VALUES ('" . $ip . "','" . $this->current_date() . "','" . $email . "','$reason','$user','$until','$type','$banned_username')";
		$insert = $this->insert($q1);
	   	// Complete the action
	   	$log = $this->complete_task('user_ban',$user,$username);
	   	return "1";
	}


	// -----------------------------------------------------------------------------
	// 	Get information on a banned ip or username
	
	function get_ban_data($ip = "", $id = "", $email = "", $username = "") {
		if (! empty($ip)) {
			$add_where = "`ip`='" . $this->mysql_clean($ip) . "'";
		}
		else if (! empty($username)) {
			$add_where = "`username`='" . $this->mysql_clean($username) . "'";
		}
		else if (! empty($email)) {
			$add_where = "`username`='" . $this->mysql_clean($username) . "'";
		}
		else {
			$add_where = "`id`='" . $this->mysql_clean($id) . "'";
		}
		$q = "SELECT * FROM `" . TABLE_PREFIX . "banned` WHERE $add_where LIMIT 1";
		$found = $this->get_array($q);
		return $found;
	}
	

	// -----------------------------------------------------------------------------
	// 	Check if an IP or email is banned
	
	function check_banned($email = "",$username = "",$return = "0") {
		$add_where = '';
		if (! empty($email)) {
			$add_where = "`email`='" . $this->mysql_clean($email) . "'";
		}
		else if (! empty($username)) {
			$add_where .= "`username`='" . $this->mysql_clean($username) . "'";
		}
		else {
			$add_where .= "`ip`='" . $_SERVER['REMOTE_ADDR'] . "'";
		}
		$q = "SELECT `id`,`date`,`reason`,`banned_until`,`ban_type` FROM `" . TABLE_PREFIX . "banned` WHERE $add_where LIMIT 1";
		$found = $this->get_array($q);
		if (! empty($found['date'])) {
			// Partial ban, can't do anything like
			// login, comment, register, etc.
			//if ($found['ban_type'] == "1") {
			//	return $found['banned_until'];
			//}
			// Full ban, quick the user off the site.
			//else {
				if (! empty($found['banned_until'])) {
					if (time() >= $found['banned_until']) {
						$qa = "DELETE FROM `" . TABLE_PREFIX . "banned` WHERE `id`='" . $found['id'] . "' LIMIT 1";
						$del = $this->delete($qa);
						return "";
					} else {
						if ($return == "1") {
							return $found['banned_until'];
						} else {
							$banned_date = $this->format_date(date('Y-m-d H:i:s',strtotime($found['banned_until'])));
							$error = "You are locked out from the website until $banned_date for the following reason:<blockquote>" . $found['reason'] . "</blockquote>";
							$this->show_error($error);
						}
					}
				} else {
					if ($return == "1") {
						return "banned";
					} else {
						$error = "You were banned from accessing this site on " . $this->format_date($found['date']) . " for the following reason:<blockquote>" . $found['reason'] . "</blockquote>";
						$this->show_error($error);
					}
				}
			//}
		}
	}
	

	// -----------------------------------------------------------------------------
	// 	Update a session
	
	function update_session($id) {
		$q = "UPDATE `" . TABLE_PREFIX . "sessions` SET `last_activity`='" . date('Y-m-d H:i:s') . "' WHERE `id`='$id' LIMIT 1";
		$run = $this->update($q);
		return "1";
	}
	

	// -----------------------------------------------------------------------------
	//	Start a session
	
	function start_session($username,$remember = '0') {
		$session_id = substr(md5(rand(100000,999999) . $username),0,25);
		$q = "INSERT INTO `" . TABLE_PREFIX . "sessions` (`id`,`user`,`started`,`last_activity`,`remember`) VALUES ('$session_id','" . $this->mysql_clean($username) . "','" . date('Y-m-d H:i:s') . "','" . date('Y-m-d H:i:s') . "','" . $this->mysql_clean($remember) . "')";
		$insert = $this->insert($q);
		if ($remember == '1') {
			$rem_time = time()+2629743; // One month
			$cookie = $this->create_cookie('mn_session',$session_id,$rem_time);
		} else {
			$cookie = $this->create_cookie('mn_session',$session_id,'none');
		}
		return "1";
	}
	

	// -----------------------------------------------------------------------------
	// 	Kill a session
	
	function kill_session($id = "") {
		if (! empty($id)) {
			$session_id = $id;
		} else {
			if (! empty($_COOKIE['mn_session'])) {
				$session_id = $_COOKIE['mn_session'];
			}
		}
		if (! empty($session_id)) {
			$q = "UPDATE `" . TABLE_PREFIX . "sessions` SET `ended`='" . date('Y-m-d H:i:s') . "' WHERE `id`='" . $this->mysql_clean($session_id) . "' LIMIT 1";
			$update = $this->update($q);
		}
		$killit = $this->delete_cookie('mn_session');
		// Facebook connect?
		$fb_cookie = "fbs_" . $this->get_option('fb_app_id');
		$killit = $this->delete_cookie($fb_cookie);
		return "1";
	}
	
	
	// -----------------------------------------------------------------------------
	// 	Updates a logged in user's options
	
	function update_user_options($adding) {
		global $user_data;
		// New options
		if (! empty($user_data['options'])) {
			$user_options_current = unserialize($user_data['options']);
			foreach ($user_options_current as $key => $value) {
				if (! array_key_exists($key,$adding)) {
					$adding[$key] = $value;
				}
			}
		}
		$final_options = serialize($adding);
		$q34 = "UPDATE `" . TABLE_PREFIX . "users` SET `options`='" . $this->mysql_clean($final_options) . "' WHERE `id`='" . $user_data['id'] . "' LIMIT 1";
		$update = $this->update($q34);
	}
	
	// -----------------------------------------------------------------------------
	// 	Get User Link
	
	function get_user_link($username) {
		if ($this->get_option('url_display_type') == 'Name') {
			$user_link = URL . "/user/" . $username;
		} else {
			$user_link = URL . "/index.php?v=user&id=" . $username;
		}
		return $user_link;
	}
	
	// -----------------------------------------------------------------------------
	// 	Get profile pic
	
	function get_profile_pic($user_id,$picture = '') {
		global $theme_folder;
		if (empty($picture)) {
			$picture = $this->get_eav('picture',$user_id);
		}
		if (empty($picture)) {
			$path = THEME_IMAGES . "/profile_none_lg.jpg";
			$final = $path;
		} else {
			// Check this for facebook profile usage.
			if (strpos($picture,'http') !== false) {
				$path = $picture;
			} else {
				$path = URL . "/generated/" . $picture;
			}
			$final = $path;
		}
		return $final;
	}
	
	// -----------------------------------------------------------------------------
	// 	Get thumbnail
	
	function get_profile_thumb($user_id,$picture = '') {
		global $theme_folder;
		if (empty($picture)) {
			$picture = $this->get_eav('thumbnail',$user_id);
		}
		if (empty($picture)) {
			$path = $theme_folder . "/imgs/profile_none_lg_tb.jpg";
			$final = $path;
		} else {
			// Check this for facebook profile usage.
			if (strpos($picture,'http') !== false) {
				$path = $picture;
			} else {
				$path = URL . "/generated/" . $picture;
			}
			$final = $path;
		}
		return $final;
	}
	
	
	// -----------------------------------------------------------------------------
	// 	Get a user's ID in the database
	
	function get_user_id($username) {
		$q = "SELECT `id` FROM `" . TABLE_PREFIX . "users` WHERE `username`='" . $this->mysql_clean($username) . "' LIMIT 1";
		$id = $this->get_array($q);
		return $id['id'];
	}
	
	// -----------------------------------------------------------------------------
	// 	Get a user's email from ID or username
	
	function get_user_email($id = '',$username = '') {
		if (! empty($id)) {
			$q = "SELECT `email` FROM `" . TABLE_PREFIX . "users` WHERE `id`='" . $this->mysql_clean($id) . "' LIMIT 1";
		} else {
			$q = "SELECT `email` FROM `" . TABLE_PREFIX . "users` WHERE `username`='" . $this->mysql_clean($username) . "' LIMIT 1";
		}
		$email = $this->get_array($q);
		return $email['email'];
	}
	
	
	// -----------------------------------------------------------------------------
	// 	Get a user's username in the database
	
	function get_username_from_id($id) {
		$q = "SELECT `username` FROM `" . TABLE_PREFIX . "users` WHERE `id`='" . $this->mysql_clean($id) . "' LIMIT 1";
		$username = $this->get_array($q);
		return $username['username'];
	}
	
	
	// -----------------------------------------------------------------------------
	// 	Get user's current points
	
	function get_user_points($username = "",$user_id = "") {
		// Basic Information
		if (empty($user_id)) {
			$user_id = $this->get_user_id($username);
		}
		// Get points
		$q = "SELECT `value` FROM `" . TABLE_PREFIX . "user_data` WHERE `key`='myScore' AND `user_id`='" . $this->mysql_clean($user_id) . "' LIMIT 1";
		$points = $this->get_array($q);
		return $points['value'];
	}
	
	// -----------------------------------------------------------------------------
	// 	Check if a user has a badge
	
	function check_user_badge($badge,$user_id) {
	   	$q1 = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "badge_history` WHERE `user_id`='" . $this->mysql_clean($user_id) . "' AND `badge`='" . $this->mysql_clean($badge) . "'";
	   	$found = $this->get_array($q1);
	   	return $found['0'];
	}
	
	// -----------------------------------------------------------------------------
	// 	Check if user has earned any new badges
	
	function check_new_badges($user_id,$username,$user_points,$task = 'score',$custom_data = '') {
		global $manual;
		$skip = '0';
		$user_data = $this->get_user_data($username,$user_id);
		if ($task == 'score') {
			$where = " WHERE `points_required`<='$user_points'";
		} else {
			if ($task == 'article_add') {
				$where = " WHERE `points_required`<='" . $user_data['articles'] . "'";
			}
			else if ($task == 'article_edit') {
				$where = " WHERE `points_required`<='" . $user_data['articles_edited'] . "'";
			}
			else if ($task == 'comment_post') {
				$where = " WHERE `points_required`<='" . $user_data['comments'] . "'";
			}
			else if ($task == 'comment_status_changed') {
				$type_check = "comments_status" . $custom_data['comment_type'];
				$where = " WHERE `points_required`<='" . $user_data[$type_check] . "' AND `act_id`='" . $this->mysql_clean($custom_data['comment_type']) . "'";
			}
			else {
				$skip = '1';
			}
		}
		$where .= " AND `act`='$task'";
		// Run query
   		if ($skip != '1') {
	   		$q = "SELECT `id` FROM `" . TABLE_PREFIX . "badges`" . $where;
	   		$badges = $this->run_query($q);
	   		while ($row = mysql_fetch_array($badges)) {
	   			$check = $this->check_user_badge($row['id'],$user_id);
	   			if ($check <= 0) {
	   				$badge_info = $manual->get_badge($row['id']);
	   				$add = $this->give_user_badge($row['id'],$user_id,$username,$badge_info,$user_points);
	   			}
	   		}
   		}
	}
	
	// -----------------------------------------------------------------------------
	// 	Grant user a badge
	
	function give_user_badge($badge,$user_id,$username = '',$badge_info,$user_points = '') {
	   	$q2 = "INSERT INTO `" . TABLE_PREFIX . "badge_history` (`badge`,`user_id`,`date`) VALUES ('" . $badge_info['id'] . "','" . $user_id . "','" . $this->current_date() . "')";
	   	$insert = $this->insert($q2);
	   	// E-Mail user
	   	global $template;
	   	$changes = array(
	   		'%badge_name%' => $badge_info['name'],
	   		'%points_required%' => $badge_info['points_required']
	   	);
	   	if (empty($username)) {
	   		$username = $this->get_username_from_id($user_id);
	   	}
   		$sent = $template->send_template($username,'badge_awarded',"",$changes);
   		// Task
		$log = $this->complete_task('givebadge',$user_id,$badge_info['id']);
   		// Add notice
   		$notice = $this->add_notice($user_id,'badge');
	}
	
	// --------------------------------------------------------------------
	// 	Check for Notice
	
	function check_for_notice($user_id,$type,$act_id = '') {
		$q1 = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "user_notices` WHERE `type`='$type' AND `user_id`='$user_id' AND `viewed`!='1' AND `act_id`='$act_id'";
		$found = $this->get_array($q1);
		if ($found['0'] > 0) {
			return '1';
		} else {
			return '0';
		}
	}
	
	// --------------------------------------------------------------------
	// 	Add a user notice
	
	function add_notice($user_id,$type,$act_id = '') {
		$found_already = '0';
		if ($type == 'article_edit') {
			$found_already = $this->check_for_notice($user_id,$type,$act_id);
		}
		if ($found_already == '0') {
			$q = "INSERT INTO `" . TABLE_PREFIX . "user_notices` (`date`,`type`,`user_id`,`act_id`) VALUES ('" . $this->current_date() . "','$type','$user_id','$act_id')";
			$insert = $this->insert($q);
		}
	}
	
	// --------------------------------------------------------------------
	// 	Get notices since last visit
	
	function get_total_notices($user_id) {
		$q = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "user_notices` WHERE `user_id`='$user_id' AND `viewed`!='1' ORDER BY `date` DESC";
		$notices = $this->get_array($q);
		return $notices['0'];
	}
	
	// -----------------------------------------------------------------------------
	// 	Get the user's encrypted password, salt, and ID.
	
	function get_password($username) {
		$q = "SELECT `password`,`salt`,`id` FROM `" . TABLE_PREFIX . "users` WHERE `username`='" . $this->mysql_clean($username) . "' LIMIT 1";
		$pass_info = $this->get_array($q);
		return $pass_info;
	}
	
	
	// -----------------------------------------------------------------------------
	// 	Get all information available on a user
	
	function get_user_data($username = "",$in_user_id = "",$extended = '0') {
		global $theme;
		global $user;
		// Basic Information
		if (! empty($in_user_id)) {
			$where = "`id`='" . $this->mysql_clean($in_user_id) . "'";
		} else {
			$where = "`username`='" . $this->mysql_clean($username) . "'";
		}
		$info = array();
		$q = "SELECT `id`,`username`,`email`,`options`,`name`,`type`,`upvoted`,`downvoted`,`joined` FROM `" . TABLE_PREFIX . "users` WHERE $where LIMIT 1";
		$userinfo = $this->get_array($q);
		if (empty($in_user_id)) {
			$in_user_id = $userinfo['id'];
		}
		// $info['secondary_data'] = $secondary_data;
		// Merge Arrays
		$info['id'] = $userinfo['id'];
		$info['username'] = $userinfo['username'];
		$info['email'] = $userinfo['email'];
		$info['name'] = $userinfo['name'];
		$info['options'] = $userinfo['options'];
		$info['type'] = $userinfo['type'];
		$info['joined'] = $userinfo['joined'];
		$info['joined_display'] = $this->format_date($userinfo['joined']);
		$info['upvoted'] = $userinfo['upvoted'];
		$info['downvoted'] = $userinfo['downvoted'];
		// Additional Information
		$query = "SELECT `key`,`value` FROM `" . TABLE_PREFIX . "user_data` WHERE `user_id`='" . $this->mysql_clean($in_user_id) . "'";
		$secondary_info = $this->get_assoc_array($query);
		$info = array_merge($info,$secondary_info);
		// User link
		$info['user_link'] = $this->get_user_link($userinfo['username']);
		// User panels
		//global $template;
		//$render_user_panel_sm = $template->render_template('user_panel_sm',$userinfo['username'],'','1');
		//$render_user_panel = $template->render_template('user_panel',$userinfo['username'],'','1');
		//$info['panel_sm'] = $render_user_panel_sm;
		//$info['panel'] = $render_user_panel;
		// Score
		$score = $this->get_user_score($userinfo['username'],$userinfo['upvoted'],$userinfo['downvoted']);
		$info['score'] = $score;
		// myScore = points
		// Score = ups-downs from comments
		if (empty($info['myScore'])) {
			$info['myScore'] = '0';
		}
		// Check for a ban
		$banned = $this->check_banned('',$userinfo['username'],'1');
		$info['banned'] = $banned;
		// Other stats
		if (empty($info['comments'])) { $info['comments'] = '0'; }
		if (empty($info['articles'])) { $info['articles'] = '0'; }
		if (empty($info['favorites'])) { $info['favorites'] = '0'; }
		if (empty($info['logins'])) { $info['logins'] = '0'; }
		if (empty($info['last_login'])) {
			$info['last_login'] = 'n/a';
			$info['last_login_display'] = 'n/a';
		} else {
			$info['last_login_display'] = $this->format_date($info['last_login']);
		}
		// Time a member
		$time_period = $this->get_age($userinfo['joined']);
		$info['time_member'] = $time_period;
   		// User panel
   		// This has to be called from a separate function.
   		//global $template;
   		//$user_panel = $template->render_template('user_panel',$userinfo['username'],'','1','1',$info);
   		// $info['user_panel'] = $user_panel;
		// User Type
		$user_type = $this->get_usertype_settings($userinfo['type']);
		$info['usertype_name'] = $user_type['name'];
		// Profile Pic
		$info['profile_pic'] = $this->get_profile_pic($info['id'],'');
		$info['profile_thumbnail'] = $this->get_profile_thumb($info['id'],$info['thumbnail']);
		// Extended information, like recent activity
		if ($extended == '1') {
			global $manual;
			// Loop fields and fill in the blanks
			$q = "SELECT `id` FROM `" . TABLE_PREFIX . "fields`";
			$fields = $this->run_query($q);
			while ($row = @mysql_fetch_array($fields)) {
				if (! array_key_exists($row['id'],$info)) {
					$info[$row['id']] = lg_not_available;
				}
				else if (empty($info[$row['id']])) {
					$info[$row['id']] = lg_not_available;
				}
			}
			// Recent Articles
			$recent_articles = '';
			$q = "`owner`='" . $userinfo['username'] . "' AND `public`='1'";
			$recent_articles = $manual->recent_articles($q,'5',$userinfo['username']);
			$info['recent_articles'] = $recent_articles;
			// Recent comments
			$recent_comments = '';
			$q1 = "`user`='" . $userinfo['username'] . "' AND `pending`!='1'";
			$recent_comments = $manual->recent_comments($q1,'5',$userinfo['username']);
			$info['recent_comments'] = $recent_comments;
			// Favorites
			$favorites = $this->get_favorites($userinfo['id']);
			$info['favorites'] = $favorites;
			// Notices
			if ($user == $userinfo['username']) {
				$notices_old = $this->get_notices($userinfo['id'],'1','1');
				$info['notices_old'] = $notices_old;
				$notices = $this->get_notices($userinfo['id'],'0','1');
				$info['notices'] = $notices;
			}
	   		// Badges
	   		$q = "
	   			SELECT " . TABLE_PREFIX . "badge_history.badge," . TABLE_PREFIX . "badges.desc," . TABLE_PREFIX . "badges.name," . TABLE_PREFIX . "badges.color," . TABLE_PREFIX . "badges.font_color
	   			FROM `" . TABLE_PREFIX . "badge_history`
	   			INNER JOIN `" . TABLE_PREFIX . "badges`
	   			ON " . TABLE_PREFIX . "badge_history.badge=" . TABLE_PREFIX . "badges.id
	   			WHERE " . TABLE_PREFIX . "badge_history.user_id='" . $userinfo['id'] . "'";
			$results = $this->run_query($q);
			$badges = '';
			$total_badges = 0;
			while ($row = @mysql_fetch_array($results)) {
				$style_found = '0';
				$style_put = "";
				$badges .= $this->format_badge($row);
				$total_badges++;
			}
			if ($total_badges <= 0) {
				$badges = "<div class=\"badge small\">" . lg_no_badges . "</div>";
			}
			$info['badges'] = $badges;
			$info['total_badges'] = $total_badges;
		}
		return $info;
	}
	
	// -----------------------------------------------------------------------------
	// 	Format a badge
	
	function format_badge($row) {
 		if (! empty($row['color'])) {
 			$style_found = '1';
 			$row['color'] = ltrim($row['color'],'#');
 			$style_put .= "background-color:#" . $row['color'] . ";";
 		}
 		if (! empty($row['font_color'])) {
 			$style_found = '1';
 			$row['font_color'] = ltrim($row['font_color'],'#');
 			$style_put .= "color:#" . $row['font_color'] . ";";
 		}
 		if ($style_found == '1') {
 			$style_put = "style=\"$style_put\"";
 		}
 		return "<div class=\"badge\"$style_put title=\"" . $row['desc'] . "\">" . $row['name'] . "</div>";
	}
	
	
	// -----------------------------------------------------------------------------
	// 	Get a user's notices
	
	function get_notices($user_id,$old = '0',$inclass = '0') {
		global $manual;
		global $template;
		global $user_data;
		$return_array = array();
		$total = 0;
		$total_old = 0;
		$all = "";
		$all_old = "";	
		// New notices
		if ($old == '1') {
   			$q = "SELECT * FROM `" . TABLE_PREFIX . "user_notices` WHERE `user_id`='$user_id' AND `viewed`='1' ORDER BY `date` DESC";
   		} else {
   			$q = "SELECT * FROM `" . TABLE_PREFIX . "user_notices` WHERE `user_id`='$user_id' AND `viewed`!='1' ORDER BY `date` DESC";
   		}
   		$results = $this->run_query($q);
   		while ($notices = mysql_fetch_array($results)) {
   			$details = '';
   			$final_type = '';
   			$final_link = '';
   			// Type
   			if ($notices['type'] == 'comment_post' || $notices['type'] == 'comment_reply') {
   				$comment_info = $manual->get_a_comment($notices['act_id'],'`id`,`article`,`comment`');
   				$format = $manual->format_comment($comment_info['comment']);
   				$article_link = $manual->prepare_link($comment_info['article']);
   				$final_link = $article_link . "#comment" . $notices['act_id'];
   				$details = $format;
   				if ($notices['type'] == 'comment_reply') {
   					$final_type = ucwords(lg_comment_reply);
   				} else {
   					$final_type = ucwords(lg_comment);
   				}
   			}
   			else if ($notices['type'] == 'comment_status_change') {
   				$final_type = ucwords(lg_comment_type);
   			}
   			else if ($notices['type'] == 'mention') {
   				$final_type = ucwords(lg_mention_type);
   				$exp_id = explode('-',$row['act_id']);
   				// Mention in page
   				if ($exp_id['0'] == 'p') {
	   				$final_link = $manual->prepare_link($exp_id['0']);
   				}
   				// Mention in comment
   				else {
	   				$comment_info = $manual->get_a_comment($notices['act_id'],'id,article,comment');
	   				$format = $manual->format_comment($comment_info['comment']);
   					$article_link = $manual->prepare_link($comment_info['article']);
   					$final_link = $article_link . "#comment" . $exp_id['1'];
   					$details = $format;
   				}
   			}
   			else if ($notices['type'] == 'article_edit') {
   				$final_link = $manual->prepare_link($notices['act_id']);
   				$final_type = ucwords(lg_article_edited);
   			}
   			else if ($notices['type'] == 'badge') {
   				$final_type = ucwords(lg_badge);
   			}
 			// Changes
 			$special_changes = array(
 				'%notice_type%' => $final_type,
 				'%notice_link%' => $final_link,
 				'%notice_details%' => $details,
 				'%notice_date%' => $this->format_date($notices['date'])
 			);
		   	// Render Page
		   	$render = $template->render_template('user_notice',$username,$special_changes,'1','0',$user_data);
		   	$all .= $render;
		   	$total++;
   		}
   		if (empty($total)) {
   			$total = '0';
   			$all = lg_no_notices_found;
   		}
   		$return_array['items'] = $all;
   		$return_array['total'] = $total;
   		return $return_array;
	}
	
	
	// -----------------------------------------------------------------------------
	// 	Mark a user's notices as "Read"
	
	function mark_notices_read($user_id) {
		$q = "UPDATE `" . TABLE_PREFIX . "user_notices` SET `viewed`='1' WHERE `user_id`='$user_id'";
		$update = $this->update($q);
	}
	
	
	// -----------------------------------------------------------------------------
	// 	Get a user's favorites
	
	function get_favorites($user_id) {
		global $manual;
		global $template;
		$return_array = array();
		$total = 0;
   		$q = "SELECT `article` FROM `" . TABLE_PREFIX . "favorites` WHERE `user_id`='$user_id' ORDER BY `date` DESC";
   		$results = $this->run_query($q);
   		while ($faves = mysql_fetch_array($results)) {
   			$row = $manual->get_article($faves['article']);
 			// Considerations
   			$score = $row['upvoted'] - $row['downvoted'];
   			$category_name = $manual->get_category_name_from_id($row['category']);
   			$article_link = $manual->prepare_link($row['id'],$row['category'],$row['name']);
   			// $article_link = "<a href=\"$article_link\">" . $row['name'] . "</a>";
   			$q = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "comments` WHERE `article`='" . $row['id'] . "' AND `pending`!='1'";
   			$count = $this->get_array($q);
   			$comments = $count['0'];
 			// Changes
 			$special_changes = array(
 				'%article_owner%' => $row['owner'],
 				'%article_title%' => $row['name'],
 				'%article_name%' => $row['name'],
 				'%article_link%' => $article_link,
 				'%article_description%' => $row['meta_desc'],
 				'%article_category%' => $row['category'],
 				'%category_name%' => $category_name,
 				'%article_created%' => $this->format_date($row['created']),
 				'%article_last_updated%' => $this->format_date($row['last_updated']),
 				'%article_score%' => $score,
 				'%article_comments%' => $comments
 			);
		   	// Render Page
		   	$put_user_data = $this->get_user_data($row['owner']);
		   	$render = $template->render_template('article_panel',$row['owner'],$special_changes,'1','0',$put_user_data);
		   	$all .= $render;
		   	$total++;
   		}
   		if ($total <= 0) {
   			$all = lg_no_favorites_found;
   		}
   		$return_array['items'] = $all;
   		$return_array['total'] = $total;
   		return $return_array;
	}
	
	
	// -----------------------------------------------------------------------------
	// 	Get a user's score
	function get_user_score($username,$ups = "0",$downs = "0") {
		if (! empty($ups) && ! empty($downs)) {
			return $ups - $downs;
		} else {
			$q = "SELECT SUM(`upvoted`-`downvoted`) FROM `" . TABLE_PREFIX . "users` WHERE `username`='$username' LIMIT 1";
			$sum = $this->get_array($q);
			return $sum['0'];
		}
	}
	
}

?>