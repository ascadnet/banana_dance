<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Password and registration functions.
	
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


class password extends db {

	// --------------------------------------------------------------------
	// 	Check strength
	// 	Strongest = 5
	// 	Strong = 4
	// 	Medium = 3
	// 	Weak = 2
	// 	Very Weak = 1 or lower
	function check_strength($password,$return_type = "word") {
		$power = "0";
		$found_upper = '0';
		$found_lower = '0';
		$found_symbol = '0';
		// Length
		$len = strlen($password);
		if ($len >= 8) { $power += "2"; }
		else	if ($len > 4 && $len < 8) { $power += "1"; }
		else	if ($len <= 4) { $power -= "-1"; }
		// Various characters?
		$components = preg_split('//', $password, -1, PREG_SPLIT_NO_EMPTY);
		$found_letter = 0;
		$found_number = 0;
		foreach ($components as $ele) {
			if (is_numeric($ele)) { $found_number = "1"; }
			if (preg_match('%^[A-Z]+$%', $ele)) { $found_letter = "1"; $found_upper = "1"; }
			if (preg_match('%^[a-z]+$%', $ele)) { $found_letter = "1"; $found_lower = "1"; }
			if (! preg_match('%^[a-zA-Z0-9]+$%', $ele)) { $found_symbol = "1"; }
		}
		$unique_found = $found_number + $found_upper + $found_lower + $found_symbol;
		if ($unique_found == "4") { $power += "3"; }
		else if ($unique_found == "3") { $power += "2"; }
		else if ($unique_found == "2") { $power += "1"; }
		else if ($unique_found == "1") { $power += "-1"; }
		// Rating
		if ($return_type == "word") {
			if ($power >= 5) { $return = "Strongest"; }
			else if ($power == 4) { $return = "Strong"; }
			else if ($power == 3) { $return = "Medium"; }
			else if ($power == 2) { $return = "Weak"; }
			else if ($power <= 1) { $return = "Weakest"; }
		} else {
			$return = $power;
		}
		return $return;
	}

	// --------------------------------------------------------------------
	// 	Properly encode a password
	function encode_password($password,$salt) {
		// Prepare the password
		$hash = $salt . $password;
		// Hash the salted password a bunch of times
		for ( $i = 0; $i < 100; $i ++ ) {
		    $hash = hash('sha256', $hash);
		}
		// Prefix the hash with the salt so we can find it back later
		$hash = $salt . $hash;
		return $hash;
	}
	
	// --------------------------------------------------------------------
	// 	Generate a strong salt for the user
	function generate_salt($username) {
		$salt = hash('sha256', uniqid(mt_rand(), true) . rand(10000,99999) . strtolower($username));
		return $salt;
	}

	// --------------------------------------------------------------------
	// 	Generate a strong password
	function strong_password() {
		$letters = "abcdefghijklmnopqrstuvwxyz";
		$letters_caps = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$symbols = "|*&^%$#@!)([]{}";
		$xletters = rand(3,5);
		$xletters_cap = rand(2,4);
		$xnumbers = rand(2,4);
		$xsymbols = rand(1,2);
		$password_array = array();
		while ($xletters > 0) {
			$password_array[] = $letters[mt_rand(0, strlen($letters)-1)];
			$xletters--;
		}
		while ($xletters_cap > 0) {
			$password_array[] = $letters_caps[mt_rand(0, strlen($letters_caps)-1)];
			$xletters_cap--;
		}
		while ($xnumbers > 0) {
			$password_array[] = rand(0,9);
			$xnumbers--;
		}
		while ($xsymbols > 0) {
			$password_array[] = $symbols[mt_rand(0, strlen($symbols)-1)];
			$xsymbols--;
		}
		shuffle($password_array);
		foreach ($password_array as $letter) {
			$password .= $letter;
		}
		return $password;
	}
	
	
	// --------------------------------------------------------------------
	// 	Create user in the DB
	
	function create_user($final_username,$encoded_pass,$salt,$email = '',$name = '',$more_data = '',$user_type = '3') {
		// Primary data
		$q1 = "INSERT INTO `" . TABLE_PREFIX . "users` (`username`,`password`,`salt`,`email`,`name`,`type`,`ip`,`joined`,`last_updated`) VALUES ('" . $this->mysql_clean($final_username) . "','$encoded_pass','$salt','" . $this->mysql_clean($email) . "','" . $this->mysql_clean($name) . "','$user_type','" . $_SERVER['REMOTE_ADDR'] . "','" .  $this->current_date() . "','" .  $this->current_date() . "')";
		$insert = $this->insert($q1);
		// Additional Data?
		$add_insert = '';
		if (! empty($more_data)) {
			foreach ($more_data as $name => $value) {
				$add_insert .= ",('" . $this->mysql_clean($name) . "','" . $this->mysql_clean($value) . "','$insert')";
			}
		}
		// User data
		$q2 = "
			INSERT INTO `" . TABLE_PREFIX . "user_data` (`key`,`value`,`user_id`) VALUES
			('agent','" . $_SERVER['HTTP_USER_AGENT'] . "','$insert'),
			('myScore','0','$insert')
			$add_insert
		";
		$go = $this->insert($q2);
		// Return
		return $insert;
	}
	
	
	// --------------------------------------------------------------------
	// 	Generate a username using a prefix
	function generate_username($prefix) {
		$found = 0;
		$current = 0;
		$try_username = $prefix;
		while ($found == 0) {
			$q = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "users` WHERE `username`='" . $this->mysql_clean($try_username) . "' LIMIT 1";
			$founduser = $this->get_array($q);
			if ($founduser['0'] > 0) {
				$current++;
				$try_username = $prefix . $current;
				continue;
			} else {
				$final_username = $try_username;
				$found = '1';
				break;
			}
		}
		return $final_username;
	}
	
	
	// --------------------------------------------------------------------
	// 	Run Registration Checks
	function registration_checks($admin = "0",$skip_password_checks = "0",$skip_username_checks = "0",$skip_email_checks = "0") {
		if ($admin != '1') {
			global $session;
		}
		// Allow registration?
		if ($admin != "1") {
			$allow_registration = $this->get_option('allow_registration');
			if ($allow_registration != "1") {
		   		echo "0+++" . lg_req_disabled;
				exit;
			}
		}
		// Required fields?
		// $req = array('username','email');
		$req	= explode(',',$this->get_option('required_reg_fields'));
		if ($skip_password_checks != '1' && ! in_array('pass',$req)) {
			$req[] = 'pass';
		}
		if ($skip_password_checks == '1' && in_array('pass',$req)) {
			$temp_array = array();
			foreach ($req as $element) {
				if ($element != 'pass') {
					$temp_array[] = $element;
				}
			}
			$req = $temp_array;
		}
		foreach ($req as $required) {
			$required = trim($required);
			if (empty($_POST[$required])) {
				$fields .= "--" . $required;
				$show_fields .= ", $required";
				$error = "1";
			}
		}
		if ($error == "1") {
			$fields = substr($fields,2);
			$show_fields = substr($show_fields,2);
			if ($admin != '1') {
				$update_ses = $session->check_spam_session('register','1');
			}
	   		echo "0+++" . lg_req_fields . " $show_fields.+++$fields";
	   		exit;
		}
		// Password matching
		if ($skip_password_checks != '1') {
			if ($_POST['pass'] != $_POST['pass1']) {
				if ($admin != '1') {
					$update_ses = $session->check_spam_session('register','2');
				}
				echo "0+++" . lg_password_no_match;
				exit;
			}
			// Password strength?
			$pass_strength = $this->check_strength($_POST['pass'],'1');
			if ($pass_strength < $this->get_option('required_pass_strength')) {
				if ($admin != '1') {
					$session = $session->check_spam_session('register','5');
				}
				echo "0+++" . lg_pass_strength;
				exit;
			}
		}
		// Username in use?
		if ($skip_username_checks != '1') {
			$q = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "users` WHERE `username`='" . $this->mysql_clean($_POST['username']) . "'";
			$found = $this->get_array($q);
			if ($found['0'] > 0) {
				if ($admin != '1') {
					$update_ses = $session->check_spam_session();
				}
				echo "0+++" . lg_user_taken;
				exit;
			}
		}
		// E-Mail Checks
		if ($skip_email_checks != '1') {
			// Correct E-Mail Format?
			$valid_email = $this->validate_email($_POST['email']);
			if ($valid_email != "1") {
	   			if ($admin != '1') {
	   				$update_ses = $session->check_spam_session('register','6');
	   			}
	   			echo "0+++" . lg_invalid_email;
	   			exit;
			}
			// Email in use?
			if ($this->get_option('one_email_max') == "1") {
				$q = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "users` WHERE `email`='" . $this->mysql_clean($_POST['email']) . "'";
				$found = $this->get_array($q);
				if ($found['0'] > 0) {
					if ($admin != '1') {
						$update_ses = $session->check_spam_session('register','3');
					}
					echo "0+++" . lg_email_taken;
					exit;
				}
			}
		}
		// IP exists?
		if ($admin != '1' && $this->get_option('one_ip_max') != "1") {
			$q = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "users` WHERE `ip`='" . $_SERVER['REMOTE_ADDR'] . "'";
			$found = $this->get_array($q);
			if ($found['0'] > 0) {
				$update_ses = $session->check_spam_session('register','4');
				echo "0+++" . lg_one_per_user;
				exit;
			}
		}
	}


	// --------------------------------------------------------------------
	// 	Run Registration Checks
	function update_account_checks($password_checks = "0",$email_checks = "0") {
		global $session;
		
		// Required fields?
		$req	= explode(',',$this->get_option('required_update_fields'));
		if ($password_checks == '1' && ! in_array('pass',$req)) {
			$req[] = 'pass';
		}
		foreach ($req as $required) {
			$required = trim($required);
			if (empty($_POST[$required])) {
				$fields .= "--" . $required;
				$show_fields .= ", $required";
				$error = "1";
			}
		}
		if ($error == "1") {
			$fields = substr($fields,2);
			$show_fields = substr($show_fields,2);
			if ($admin != '1') {
				$update_ses = $session->check_spam_session('register','1');
			}
	   		echo "0+++" . lg_req_fields . " $show_fields.+++$fields";
	   		exit;
		}
		// Password matching
		if ($password_checks == '1') {
			if ($_POST['pass'] != $_POST['pass1']) {
				if ($admin != '1') {
					$update_ses = $session->check_spam_session('register','2');
				}
				echo "0+++" . lg_password_no_match;
				exit;
			}
			// Password strength?
			$pass_strength = $this->check_strength($_POST['pass'],'1');
			if ($pass_strength < $this->get_option('required_pass_strength')) {
				if ($admin != '1') {
					$session = $session->check_spam_session('register','5');
				}
				echo "0+++" . lg_pass_strength;
				exit;
			}
		}
		// E-Mail Checks
		if ($email_checks == '1') {
			// Correct E-Mail Format?
			$valid_email = $this->validate_email($_POST['email']);
			if ($valid_email != "1") {
	   			if ($admin != '1') {
	   				$update_ses = $session->check_spam_session('register','6');
	   			}
	   			echo "0+++" . lg_invalid_email;
	   			exit;
			}
			// Email in use?
			if ($this->get_option('one_email_max') == "1") {
				$q = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "users` WHERE `email`='" . $this->mysql_clean($_POST['email']) . "'";
				$found = $this->get_array($q);
				if ($found['0'] > 0) {
					if ($admin != '1') {
						$update_ses = $session->check_spam_session('register','3');
					}
					echo "0+++" . lg_email_taken;
					exit;
				}
			}
		}
	}
	
	// --------------------------------------------------------------------
	// 	Check E-Mail Format
	//	Credit for this function is given to
	//	Douglas Lovell of the Linux Journal
	
	function validate_email($email) {
		$isValid = '1';
		$atIndex = strrpos($email, "@");
		if (is_bool($atIndex) && !$atIndex) {
			$isValid = '0';
		} else {
			$domain = substr($email, $atIndex+1);
			$local = substr($email, 0, $atIndex);
			$localLen = strlen($local);
			$domainLen = strlen($domain);
			if ($localLen < 1 || $localLen > 64) {
				// local part length exceeded
				$isValid = '0';
			}
			else if ($domainLen < 1 || $domainLen > 255) {
				// domain part length exceeded
				$isValid = '0';
			}
			else if ($local['0'] == '.' || $local[$localLen-1] == '.') {
				// local part starts or ends with '.'
				$isValid = '0';
			}
			else if (preg_match('/\\.\\./', $local)) {
				// local part has two consecutive dots
				$isValid = '0';
			}
			else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
				// character not valid in domain part
				$isValid = '0';
			}
			else if (preg_match('/\\.\\./', $domain)) {
				// domain part has two consecutive dots
				$isValid = '0';
			}
			else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',str_replace("\\\\","",$local))) {
				// character not valid in local part unless 
				// local part is quoted
				if (!preg_match('/^"(\\\\"|[^"])+"$/',str_replace("\\\\","",$local))) {
					$isValid = '0';
				}
			}
			
			if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))) {
				// domain not found in DNS
				$isValid = '0';
			}
		}
		return $isValid;
	}
	
}


?>