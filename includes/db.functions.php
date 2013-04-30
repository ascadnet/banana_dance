<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: DB and general-use functions.
	
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

class db {

	// --------------------------------------------------------------------
	function connect() {
		mysql_connect(MYSQL_HOST,MYSQL_USER,MYSQL_PASS) or die("MySQL connection error: " . mysql_error());
		mysql_select_db(MYSQL_DB);
	}
	
	// --------------------------------------------------------------------
	function disconnect() {
		mysql_close();
	}


	// --------------------------------------------------------------------
	function insert($query) {
		$result = mysql_query($query);
		if (! $result) {
			die("Invalid query ($query): " . mysql_error());
			exit;
		}
		$last_id = mysql_insert_id();
		if ($last_id) {
			return mysql_insert_id();
		} else {
			return "Success";
		}
	}


	// --------------------------------------------------------------------
	function delete($query) {
		$result = mysql_query($query);
		if (! $result) {
			die("Invalid query ($query): " . mysql_error());
			exit;
		}
		return "Success";
	}


	// --------------------------------------------------------------------
	function update($query) {
		$result = mysql_query($query);
		if (! $result) {
			die("Invalid query ($query): " . mysql_error());
			exit;
		}
		return "Success";
	}
	

	// --------------------------------------------------------------------
	function get_array($query) {
		$array = mysql_fetch_array(mysql_query($query));
		if (mysql_error()) {
			die("Invalid query ($query): " . mysql_error());
			exit;
		}
		return $array;
	}


	// --------------------------------------------------------------------
	function run_query($query) {
		$result = mysql_query($query);
		return $result;
	}

	// --------------------------------------------------------------------
	function get_assoc_array($query,$db = "",$e1 = "key",$e2 = "value") {
		$result = mysql_query($query);
		if (mysql_error()) {
			die("Invalid query:<br /><font family=courier size=-1><u>$query</u></font><br /><b>Error: </b>" . mysql_error());
			exit;
		}
		$final_array = array();
		while ($row = mysql_fetch_assoc($result)) {
			$final_array[$row[$e1]] = $row[$e2];
		}
		return $final_array;
	}
	
	
	// --------------------------------------------------------------------
	// 	Clean MySQL Inputs
	
	function mysql_clean($string,$non_english = "0") {
		if (! empty($string)) {
			if (get_magic_quotes_gpc()) {
				$string = stripslashes($string);
			}
			// Remove non-english characters
			// for searching, etc.?
			if ($non_english == "1") {
				$string = htmlentities($string);
				$string = $this->remove_accents($string);
			}
			return mysql_real_escape_string($string);
		} else {
			return '';
		}
	}
	
	// --------------------------------------------------------------------
	//	Write a file
	
	function write_file($location,$contents) {
		$fh = @fopen($location, 'w');
		@fwrite($fh, $contents);
		@fclose($fh);
	}
	
	
	// --------------------------------------------------------------------
	// Special character encoding
	
	function remove_accents($string) {
		$string = strtr($string,
		  "\xA1\xAA\xBA\xBF\xC0\xC1\xC2\xC3\xC5\xC7
		   \xC8\xC9\xCA\xCB\xCC\xCD\xCE\xCF\xD0\xD1
		   \xD2\xD3\xD4\xD5\xD8\xD9\xDA\xDB\xDD\xE0
		   \xE1\xE2\xE3\xE5\xE7\xE8\xE9\xEA\xEB\xEC
		   \xED\xEE\xEF\xF0\xF1\xF2\xF3\xF4\xF5\xF8
		   \xF9\xFA\xFB\xFD\xFF",
		   "!ao?AAAAAC
		   EEEEIIIIDN
		   OOOOOUUUYa
		   aaaaceeeei
		   iiidnooooo
		   uuuyy");
		$string = strtr($string, array("\xC4"=>"Ae", "\xC6"=>"AE", "\xD6"=>"Oe", "\xDC"=>"Ue", "\xDE"=>"TH", "\xDF"=>"ss", "\xE4"=>"ae", "\xE6"=>"ae", "\xF6"=>"oe", "\xFC"=>"ue", "\xFE"=>"th"));
		return($string);
	}
	
	
	// --------------------------------------------------------------------
	// 	Format a date
	
	function current_date() {
		$offset = $this->get_option('time_offset');
		$offset = ltrim($offset,'+');
		$time_offset = $offset*3600;
		return date('Y-m-d H:i:s',time()+$time_offset);
	}
	
	
	// --------------------------------------------------------------------
	// 	Make URLs Clickable
	
	function make_url_clickable($text) {
		$ret = eregi_replace("([[:alnum:]]+)://([^[:space:])>]*)([[:alnum:]#?/&=])", "<a href=\"\\1://\\2\\3\" target=\"_blank\">\\1://\\2\\3</a>", $text); 
		$ret = eregi_replace("(([a-z0-9_]|\\-|\\.)+@([^[:space:]]*)([[:alnum:]-]))", "<a href=\"mailto:\\1\">\\1</a>", $ret); 
		return $ret;
	}
	
	// --------------------------------------------------------------------
	// 	Remove Links
	
	function remove_links($text) {
		$ret = eregi_replace("([[:alnum:]]+)://([^[:space:])>]*)([[:alnum:]#?/&=])", lg_links_blocked, $text); 
		$ret = eregi_replace("(([a-z0-9_]|\\-|\\.)+@([^[:space:]]*)([[:alnum:]-]))", lg_links_blocked, $ret); 
		return $ret;
	}
	
	// ---------------------------------------------------------------------------
	// 	Visually satisfying links
	//	Consider using "rawurlencode" as well, but
	//	for readability, it's not the best thing ever.
	
	function urlencodeclean($string) {
		$string = urlencode($string);
		$string = str_replace('+','_',$string);
		$string = str_replace('%252Bt','_',$string);
		$string = str_replace('%2B','-',$string);
		return $string;
	}
	
	function urldecodeclean($string) {
		$string = str_replace('-',' ',$string);
		$string = urldecode($string);
		//$string = str_replace('_','+',$string);
		return $string;
	}
	
	// --------------------------------------------------------------------
	// 	Format a date
	
	function format_date($date,$force_format = '') {
		if (! empty($force_format)) {
			return date($force_format,strtotime($date));
		} else {
			return date($this->get_option('date_format'),strtotime($date));
		}
	}
	
	
	// --------------------------------------------------------------------
	// 	Get the current URL
	
	function current_url() {
		if ($_SERVER['HTTPS'] == "on") {
			$current_url = "https://";
		} else {
			$current_url = "http://";
		}
		if ($_SERVER['SERVER_PORT'] != "80") {
			$current_url .= $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
		} else {
			$current_url .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		}
		return $current_url;
	}
	
	
	// --------------------------------------------------------------------
	// 	Create Cookie
	
	function create_cookie($name,$value,$time = "",$domain = "") {
		if (empty($domain)) {
			$final_domain = $this->get_domain();
		} else {
			$final_domain = $domain;
		}
		if ($time == "none") {
			setcookie($name, $value, NULL, "/");
			setcookie($name, $value, NULL, "/", ".$final_domain");
			setcookie($name, $value, NULL, "/", "www.$final_domain");
		} else {
			if (empty($time)) {
				$time = strtotime($this->current_date())+86400;
			}
			setcookie($name, $value, $time, "/");
			setcookie($name, $value, $time, "/", ".$final_domain");
			setcookie($name, $value, $time, "/", "www.$final_domain");
		}
	}
	
	// --------------------------------------------------------------------
	// 	Get domain for a cookie
	
	function get_cookie_domain() {
   		$domain = $this->get_domain();
   		/*
   		$exp_domain = explode('.',$domain);
   		if (sizeof($exp_domain) > 2) {
	   		$theDomain = array_shift($exp_domain);
	   		$final_domain = implode('.',$exp_domain);
   		} else {
   			$final_domain = $domain;
   		}
   		*/
   		return $final_domain;
	}
	
	// --------------------------------------------------------------------
	// 	Delete Cookie
	
	function delete_cookie($name) {
		$final_domain = $this->get_cookie_domain();
		@setcookie($name, "x", time()-900000, "/");
		@setcookie($name, "x", time()-900000, "/", ".$final_domain");
		@setcookie($name, "x", time()-900000, "/", "www.$final_domain");
	}
	
	
	// -----------------------------------------------------------------------------------------
	//    Determine a user's browser
	
	function determine_browser() {
		$user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
		$browser = array();
		if (stristr($user_agent,"msie")) {
			$type = "ie";
			$name = "Internet Explorer";
		}
		else if (stristr($user_agent,"firefox")) {
			$type = "ff";
			$name = "Firefox";
		}
		else if (stristr($user_agent,"chrome")) {
			$type = "ch";
			$name = "Chrome";
		}
		else if (stristr($user_agent,"opera")) {
			$type = "op";
			$name = "Opera";
		}
		else if (stristr($user_agent,"safari")) {
			$type = "sa";
			$name = "Safari";
		}
		else {
			$type = "ot";
			$name = "Not sure...";
		}
		// Check version
		$version = $this->browser_version($type);
		if ($type == 'ie') {
			if ($version < 7) {
				$good = '0';
			} else {
				$good = '1';
			}
		} else {
			$good = '1';
		}
		// Return the details
   		$browser['status'] = $good;
   		$browser['type'] = $type;
   		$browser['name'] = $name . " v" . $version;
   		$browser['version'] = $version;
   		$browser['full'] = $_SERVER['HTTP_USER_AGENT'];
   		return $browser;
	}


	// --------------------------------------------------------------------
	// 	Determine a user's browser version
	
	function browser_version($browser) {
		$browser_info = strtolower($_SERVER['HTTP_USER_AGENT']);
	     // Internet Explorer
	     if ($browser == "ie") {
	          $cut_up = explode("(",$browser_info);
	          $more_cuts = explode(";",$cut_up['1']);
	          foreach ($more_cuts as $cut) {
	               if (stristr($cut,"msie")) {
	                    $cut = str_replace("msie","",$cut);
	                    $final_version = trim($cut);
	                    break;
	               }
	          }
	     }
	     // Firefox
	     else if ($browser == "ff") {
	          $cut_up = explode("firefox",$browser_info);
	          $final_version = $cut_up['1'];
	          $final_version = str_replace("/","",$final_version);
	     }
	     // Chrome
	     else if ($browser == "ch") {
	          $cut_up = explode("chrome",$browser_info);
	          $version = explode(" ",$cut_up['1']);
	          $final_version = $version['0'];
	          $final_version = str_replace("/","",$final_version);
	     }
	     // Safari
	     else if ($browser == "sa") {
	          $cut_up = explode("version",$browser_info);
	          $more_cuts = explode(" ",$cut_up['1']);
	          $final_version = str_replace("/","",$more_cuts['0']);
	     }
	     // Opera
	     else if ($browser == "op") {
	          $cut_up = explode(" ",$browser_info);
	          $more_cuts = explode("/",$cut_up['0']);
	          $final_version = $more_cuts['1'];
	     }
	     // Other
	     else if ($browser == "oh") {
	    		$final_version = "na";
	     }
	     return $final_version;
	}
	
	
	// --------------------------------------------------------------------
	// 	Domain base, used primarily for cookies.
	
	function get_domain() {
		if (empty($domain)) {
			$domain = $_SERVER["HTTP_HOST"];
			$domain = str_replace('www.','',$domain);
		}
		$check_domain = explode(":",$domain);
		if (! empty($check_domain['1'])) {
			$domain = $check_domain['0'];
		}
		return $domain;
	}


	// --------------------------------------------------------------------
	// 	Form a query for listing
	//	$special_where_clause,$search,'1');
	
	function form_query($table, $display = "300", $page = "1", $alpha = "", $order = "username", $dir = "ASC", $special_where = "", $search = "", $count = "1") {	
		// By letter?
		$where = "";
   		if (! empty($alpha)) {
   			$where_established = "1";
   			$where .= " AND LOWER(`$order`) LIKE '$alpha%'";
   		}
   		// Searching?
   		if (! empty($search)) {
   			$where_established = "1";
   			$where .= " AND " . $search;
   		}
   		// Special where clause?
		if (! empty($special_where)) {
			$where .= $special_where;
		}
   		$where = trim($where, " AND ");
   		$where = trim($where, " OR ");
   		if (! empty($where)) {
   			$where = " WHERE " . $where;
   		}
		// Order
		if (empty($order)) {
			$order = 'username';
		}
		if (empty($dir)) {
			$dir = 'ASC';
		}
		$orderBy = " ORDER BY `$order` $dir";
		// Limit
		$low = $page * $display - $display;
		$limit = " LIMIT $low,$display";
		// Get users
		$query = "SELECT `id` FROM `$table` $where $orderBy $limit";
		// Count
		if ($count == "1") {
			$q = "SELECT COUNT(*) FROM `$table` $where";
			$count = $this->get_array($q);
			$final_return['count'] = $count['0'];
		}
		$final_return['query'] = $query;
		return $final_return;
	}
	
	
	// --------------------------------------------------------------------
	// Get EAV Value
	
	function get_eav($key,$uid,$f1 = 'user_id', $table = '') {
		if (empty($table)) {
			$table = TABLE_PREFIX . "user_data";
		} else {
			if (! strstr($table,TABLE_PREFIX)) {
				$table = TABLE_PREFIX . $table;
			}
		}
		$q = "SELECT `value` FROM `$table` WHERE `key`='" . $this->mysql_clean($key) . "' AND `" . $this->mysql_clean($f1) . "`='" . $this->mysql_clean($uid) . "' LIMIT 1";
		$value = $this->get_array($q);
		return $value['value'];
	}
	
	
	// --------------------------------------------------------------------
	// Update information in an entity-attribute-value
	// database table.
	// $key is the key we are updating/inserting
	// $value is what we are inserting
	// $uid is the value of field $f1
	// $table is the table in the DB
	// $type is for the "bd_item_options" "type" column.
	// $exists is if we know the field is in the DB.
	function update_eav($key,$value,$uid,$f1 = 'user_id', $table = '', $type = "", $exists = '0') {
		if (empty($table)) {
			$table = TABLE_PREFIX . "user_data";
		} else {
			if (! strstr($table,TABLE_PREFIX)) {
				$table = TABLE_PREFIX . $table;
			}
		}
		// Needs to be user_id, but we
		// can submit it as a username
		// if reuqired.
		if ($f1 == "username") {
			global $session;
			$f1 = "user_id";
			$uid = $session->get_user_id($uid);
		}
		// Type
		if (! empty($type)) {
			$add_where = " AND `type`='$type'";
			$i1 = ",`type`";
			$i2 = ",'" . $this->mysql_clean($type) . "'";
		}
		// Does it exist?
		if ($exists == '1') {
			$count['0'] = '1';
		} else {
			$q = "SELECT COUNT(*) FROM `$table` WHERE `key`='$key' AND `$f1`='$uid'$add_where LIMIT 1";
			$count = $this->get_array($q);
		}
		// Yes, it exists
   		$exp_value = explode('=',$value);
		if ($count['0'] > 0) {
			if ($exp_value['0'] == "add") {
   				if (empty($exp_value['1'])) { $exp_value['1'] = '1'; }
				$uu = "UPDATE `$table` SET `value`=(value+" . $exp_value['1'] . ") WHERE `key`='$key' AND `$f1`='$uid' $add_where LIMIT 1";
			}
			else if ($exp_value['0'] == "subtract") {
   				if (empty($exp_value['1'])) { $exp_value['1'] = '1'; }
				$uu = "UPDATE `$table` SET `value`=(value-" . $exp_value['1'] . ") WHERE `key`='$key' AND `$f1`='$uid' $add_where LIMIT 1";
			}
			else {
				$uu = "UPDATE `$table` SET `value`='" . $this->mysql_clean($value) . "' WHERE `key`='$key' AND `$f1`='$uid' $add_where LIMIT 1";
			}
			$update = $this->update($uu);
		}
		// No it doesn't exist
		else {
			if ($exp_value['0'] == "add") {
   				if (empty($exp_value['1'])) { $exp_value['1'] = '1'; }
				$ii = "INSERT INTO `$table` (`$f1`,`key`,`value`$i1) VALUES ('$uid','$key','" . $exp_value['1'] . "'$i2)";
			}
			else if ($exp_value['0'] == "subtract") {
   				if (empty($exp_value['1'])) { $exp_value['1'] = '1'; }
				$ii = "INSERT INTO `$table` (`$f1`,`key`,`value`$i1) VALUES ('$uid','$key','" . $exp_value['1'] . "'$i2)";
			}
			else {
				$ii = "INSERT INTO `$table` (`$f1`,`key`,`value`$i1) VALUES ('$uid','$key','" . $this->mysql_clean($value) . "'$i2)";
			}
			$insert = $this->insert($ii);
		}
	}
	
	
	// --------------------------------------------------------------------
	// 	Get an Option
	//	To reduce load on the database, you can
	//	define any option in the config.php file
	//	as a global.
	
	function get_option($option) {
		// It should be in the globals file.
		if (array_key_exists($option, $GLOBALS)) {
			return $GLOBALS[$option];
		}
		// This is a fall back if there is an issue
		// with the generated/globals.php file.
		else {
			$q = "SELECT `value` FROM `" . TABLE_PREFIX . "options` WHERE `key`='$option' LIMIT 1";
			$option = $this->get_array($q);
			return $option['value'];
		}
	}

	// --------------------------------------------------------------------
	// 	Update an Option
	//	For system options
	
	function update_option($key,$value) {
		$q = "UPDATE `" . TABLE_PREFIX . "options` SET `value`='" . $this->mysql_clean($value) . "' WHERE `key`='" . $this->mysql_clean($key) . "' LIMIT 1";
		$update = $this->update($q);
	}
	
	
	// --------------------------------------------------------------------
	// 	Run a cURL call
	function curl_call($url,$string) {
		$allow_outside_connections = $this->get_option('allow_outside_connections');
		if ($allow_outside_connections == '1') {
			$curl_proxy = $this->get_option('curl_proxy');
			$curl = curl_init();
			$ch = curl_init() or die('No cURL!');
			curl_setopt ($ch, CURLOPT_URL, $url);
			curl_setopt ($ch, CURLOPT_POST, 1);
			curl_setopt ($ch, CURLOPT_POSTFIELDS, $string);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			if (! empty($curl_proxy)) {
				curl_setopt ($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
				curl_setopt ($ch, CURLOPT_PROXY, $curl_proxy);
			}
			$result = curl_exec($ch);
			return $result;
		} else {
			return "disabled";
		}
	}
	
	
	// --------------------------------------------------------------------
	//	Cleanly outputs an array's content
	//	in a structured tree.
	
	function print_array($array) {
		echo "<pre>";
		print_r($array);
		echo "</pre>";
	}
	
	// ------------------------------------
	// 	Get a component of XML
	
	function get_xml_value($string,$full_input) {
		$find = "<" . $string . ">";
		$find1 = "</" . $string . ">";
		$explode_xml = explode($find,$full_input);
		$final_stuff = explode($find1,$explode_xml['1']);
		return $final_stuff['0'];
	}
	
	
	// ------------------------------------
	// 	Turn XML into array
	
	function xml_to_array($xml,$main_heading = '') {
		$deXml = simplexml_load_string($xml);
		$deJson = json_encode($deXml);
		$xml_array = json_decode($deJson,TRUE);
		if (! empty($main_heading)) {
			$returned = $xml_array[$main_heading];
			return $returned;
		} else {
			return $xml_array;
		}
	}
	
	// ----------------------------------------
	//	Start DOM
	
	function start_dom() {
		include PATH . "/includes/simple_html_dom/simple_html_dom.php";
	}
	
	// --------------------------------------------------------------------
	// 	Show a user error
	function show_error($error, $return = '0', $title = '') {
		global $template;
		global $user;
		global $manual;
		// Title
		if (empty($title)) {
			$title = 'Error';
		}
		// Category tree
		$category_tree = $manual->category_tree('0','',$this->get_option('cache_category_list'));
		// User sidebar
		if (! empty($user)) {
			$user_sidebar = $template->render_template('logged_in_sidebar',$user,'','1');
		} else {
			$user_sidebar = $template->render_template('logged_out_sidebar','','','1');
		}
		// Bread
		$breadcrumbs = "<a href=\"" . URL . "\">" . NAME . "</a> / " . "<a href=\"#\">" . lg_error . "</a>";
		// Changes
		$changes = array(
			'%title%' => $title,
			'%error%' => $error,
			'%user_sidebar%' => $user_sidebar,
			'%category_tree%' => $category_tree,
			'%breadcrumbs%' => $breadcrumbs,
		);
		$data = $template->render_template('error','',$changes,'0');
		if ($return == '1') {
			return $data;
		} else {
			echo $data;
		}
		exit;
	}
	
	
	// --------------------------------------------------------------------
	// 	Show a user error
	//	Original function deprecated.
	
	function show_inline_error($error, $return = '0') {
		$this->show_error($error,$return);
	}
	

	// --------------------------------------------------------------------
	// 	Admin CP inline error
	//	Inline error for admin control panel
	
	function admin_inline_error($error) {
		echo "<h1>Error</h1><p>$error</p>";
	}
	
	
	// --------------------------------------------------------------------
	// 	Gets file information from the server
	
	function get_file_info($final_id) {
		// ID exists?
	 	$skip_db = '1';
	 	$q123 = "SELECT * FROM `" . TABLE_PREFIX . "attachments` WHERE `id`='" . $this->mysql_clean($final_id) . "' LIMIT 1";
    		$db_info = $this->get_array($q123);
    		$filepath = $db_info['server_path'];
		$data = array();
 		// Filename
   		$exp_options = explode('/',$filepath);
   		$total_options = sizeof($exp_options) - 1;
   		$actual_filename = $exp_options[$total_options];
   		$path_to_directory = str_replace("/" . $actual_filename,'',$filepath);
   		// Type
   		$afn_exp = explode('.',$actual_filename);
   		$afne_size = sizeof($afn_exp) - 1;
   		$ext = strtolower($afn_exp[$afne_size]);
   		// Ext class image
   		// Commented out temporarily.
		// $ext_image = $this->file_type($ext);
   		// Continue
		if (file_exists($filepath)) {
			// In bytes
			// 1 Mb = 1048576
			// 1 Kb = 1024
			$size = filesize($filepath);
			$show_size = $this->convert_file_size($size);
   			// Prepare and return information
   			$data['size'] = $show_size;
   			$data['byte_size'] = $size;
   			$data['found'] = '1';
		} else {
   			$data['found'] = '0';
		}
   		$data['id'] = $db_info['id'];
   		$data['ext'] = $ext;
   		$data['ext_image'] = $ext_image;
   		$data['path'] = $filepath;
   		$data['url'] = $db_info['path'];
   		$data['name'] = $actual_filename;
   		$data['directory'] = $path_to_directory;
   		$data['downloads'] = $db_info['downloads'];
   		$data['login'] = $db_info['login'];
   		$data['limit'] = $db_info['limit'];
   		$data['owner'] = $db_info['owner'];
   		return $data;
	}
	
	
	// --------------------------------------------------------------------
	// 	Convert bytes into better format.
	
	function convert_file_size($size) {
   		if (($size/1073741824) > 1) {
   			$show_size = round(($size/1073741824), 2) . "Gb";
   		}
   		else if (($size/1048576) > 1) {
   			$show_size = round(($size/1048576), 2) . "Mb";
   		}
   		else if (($size/1024) > 1) {
   			$show_size = round(($size/1024), 2) . "Kb";
   		}
   		else {
   			$show_size = $size . " bytes";
   		}
   		return $show_size;
	}
	
	
	// --------------------------------------------------------------------
	// 	Determine a file's generic type
	
	function file_type($ext) {
   		if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'png' || $ext == 'tif' || $ext == 'tiff') {
	   		$ext_image = "image";
   		}
   		else if ($ext == 'pdf') {
	   		$ext_image = "pdf";
   		}
   		else if ($ext == 'doc' || $ext == 'txt' || $ext == 'rtf' || $ext == 'odt') {
	   		$ext_image = "text";
   		}
   		else if ($ext == 'xls' || $ext == 'xlsx' || $ext == 'ods') {
	   		$ext_image = "spreadsheet";
   		}
   		else if ($ext == 'zip' || $ext == 'exe' || $ext == 'rar') {
	   		$ext_image = "executable";
   		}
   		else if ($ext == 'pps' || $ext == 'ppt') {
	   		$ext_image = "powerpoint";
   		}
   		else if ($ext == 'wav' || $ext == 'wma' || $ext == 'm4a' || $ext == 'mp3' || $ext == 'mid' || $ext == 'mp3') {
	   		$ext_image = "music";
   		}
   		else if ($ext == 'mpg' || $ext == 'mp4' || $ext == 'flv' || $ext == 'wmv' || $ext == 'mov' || $ext == 'avi') {
	   		$ext_image = "video";
   		}
   		else {
   			$ext_image = "other";
   		}
   		return $ext_image;
	}
	
	
	// --------------------------------------------------------------------
	// 	Begin a task
	//	$custom_action_data is an array used for custom
	//	actions. $user_acted_upon
	
	function begin_task($task,$user_running_task = "",$acted_upon = "",$custom_action_data = "") {
   		// Custom actions?
   		$custom_actions = $this->custom_actions($task,$user_running_task,$acted_upon,$custom_action_data,'1');
   		// Custom point system
   		if ($this->get_option('use_point_system') == '1') {
	   		global $manual;
   			$check_points = $manual->check_point_reqs($task,$user_running_task,$acted_upon,$custom_action_data);
   		}
	}
	
	
	// --------------------------------------------------------------------
	// 	Complete a task
	//	$custom_action_data is an array used for custom
	//	actions.
	
	function complete_task($task,$user_running_task = "",$acted_upon = "",$custom_action_data = "",$sup_id = "") {
		global $privileges;
		global $session;
		global $manual;
		if (empty($privileges)) {
			$privileges = $session->get_user_privileges($user_running_task);
		}
		
   		/**
   		 *	Track Tasks
   		 */
   		if ($privileges['track_tasks'] == "1") {
   			$q = "INSERT INTO `" . TABLE_PREFIX . "tasks` (`date`,`action`,`user`,`performed_on`) VALUES ('" . $this->current_date() . "','$task','$user_running_task','$acted_upon')";
   			$insert = $this->insert($q);
   		}
   		
   		/**
   		 *	Activity Feeds
   		 */
   		$activity = 0;
   		$final_category = '';
   		$final_act_name = '';
   		
   		if (! is_numeric($user_running_task)) {
   			$final_user_id = $session->get_user_id($user_running_task);
   		} else {
	   		$final_user_id = $user_running_task;
   		}
   		
   		if ($task == 'comment_post') {
   			$activity = '1';
   			$final_type = 'comment';
   			$art_info = $manual->get_article($acted_upon,'1','name,category','0','0','0');
   			$final_category = $art_info['category'];
   			$final_act_name = $art_info['name'];
   		}
   		else if ($task == 'article_add') {
   			$activity = '1';
   			$final_type = 'newpages';
   			$art_info = $manual->get_article($acted_upon,'1','name,category','0','0','0');
   			$final_category = $art_info['category'];
   			$final_act_name = $art_info['name'];
   		}
   		else if ($task == 'article_edit') {
   			$activity = '1';
   			$final_type = 'editpages';
   			$art_info = $manual->get_article($acted_upon,'1','name,category','0','0','0');
   			$final_category = $art_info['category'];
   			$final_act_name = $art_info['name'];
   		}
   		else if ($task == 'givebadge') {
   			$activity = '1';
   			$final_type = 'badges';
   			$final_act_name = '';
   		}
   		else if ($task == 'register') {
   			$activity = '1';
   			$final_type = 'newuser';
   		}
   		else if ($task == 'mention') {
   			$activity = '1';
   			$final_type = 'mentions';
   			$art_info = $manual->get_article($acted_upon,'1','name,category','0','0','0');
   			$final_category = $art_info['category'];
   			$final_act_name = $art_info['name'];
   			// $sup_id = $session->get_user_id($sup_id);
   		}
   		
   		if ($activity == '1') {
   			
   			$q = "
	   			INSERT INTO `" . TABLE_PREFIX . "activity` (`user`,`type`,`act_id`,`sup_id`,`act_name`,`category_id`,`date`)
	   			VALUES ('$final_user_id','$final_type','$acted_upon','$sup_id','" . $this->mysql_clean($final_act_name) . "','$final_category','" . $this->current_date() . "')
   			";
   			$insert = $this->insert($q);
   		}
   		
   		/**
   		 *	Custom Actions
   		 */
   		$custom_actions = $this->custom_actions($task,$user_running_task,$acted_upon,$custom_action_data);
   		// Delete spam session
		$del = $session->delete_spam_session();
   		// Custom point system
   		if ($this->get_option('use_point_system') == '1') {
			global $manual;
   			$add_points = $manual->add_points($task,$user_running_task,$acted_upon,$custom_action_data);
   		}
	}
	
	
	// --------------------------------------------------------------------
	// 	Get array of options for an item.
	//	Type = category or [page|article]
	
	function get_item_options($item,$type) {
		global $manual;
		// Category Settings
		if ($item != 'd') {
			if ($item == 'base') {
				$item = '0';
			}
			$q = "SELECT `key`,`value` FROM `" . TABLE_PREFIX . "item_options` WHERE `type`='$type' AND `act_id`='$item'";
			$options = $this->get_assoc_array($q);
			// If the parent category doesn't have settings,
			// check parent category settings.
			if ($type == 'category' && empty($options)) {
				$primary_category = $manual->get_primary_categories($item);
				foreach ($primary_category as $aCate) {
					$options = $this->get_item_options($aCate,'category');
					if (! empty($options)) {
						break;
					}
				}
			}
			else if ($type != 'category' && empty($options)) {
				$page_category = $manual->get_article($item,'1','category','0','0','0');
				$options = $this->get_item_options($page_category['category'],'category');
			}
		}
		// Defaults
		if (empty($options) || $item == 'd') {
			$q = "SELECT `key`,`value` FROM `" . TABLE_PREFIX . "item_options` WHERE `type`='article' AND `act_id`='d'";
			$options = $this->get_assoc_array($q);
		}
		return $options;
	}
	
	// --------------------------------------------------------------------
	// 	Get a single item option
	
	function get_an_item_option($key,$item,$type = '') {
		if (! empty($type)) {
			$add_where = " AND `type`='$type'";
		}
		$q = "SELECT `value` FROM `" . TABLE_PREFIX . "item_options` WHERE `key`='$key' AND `act_id`='$item'" . $add_where;
		$result = $this->get_array($q);
		return $result['value'];
	}
	
	// --------------------------------------------------------------------
	// 	Update an item's option.
	
	function update_item_option($key,$item,$type,$value) {
		$q = "UPDATE `" . TABLE_PREFIX . "item_options` SET `value`='" . $this->mysql_clean($value) . "' WHERE `key`='$key' AND `type`='$type' AND `act_id`='$item'";
		$update = $this->update($q);
	}
	
	// -----------------------------------------------------------
	//	Function to delete a directory
	//	and all files in that directory.
	
	function delete_dir($dir,$remove_dir = '0') {
		$mydir = opendir($dir);
		while (false !== ($file = readdir($mydir))) {
			if ($file != "." && $file != "..") {
				// chmod($dir.$file, 0777);
				if (is_dir($dir . "/" . $file)) {
					chdir('.');
					destroy($dir . '/' . $file . '/');
					rmdir($dir . '/' . $file);
				}
				else {
					unlink($dir . '/' . $file);
				}
			}
		}
		if ($remove_dir == '1') {
			rmdir($dir);
		}
		closedir($mydir);
	}
	
	
	// --------------------------------------------------------------------
	// 	Check for an SSL connection.
	
	function check_ssl() {
		if (isset($_SERVER['HTTPS'])) {
		    if (strtolower($_SERVER['HTTPS']) == 'on') { return '1'; }
		    else if ($_SERVER['HTTPS'] == '1') { return '1'; }
		}
		elseif (isset($_SERVER['SERVER_PORT']) && ( '443' == $_SERVER['SERVER_PORT'])) {
		    return '1';
		}
		else {
			return '0';
		}
	}
	
	
	// --------------------------------------------------------------------
	// 	Get's time since a specific date.
	
	function get_age($date,$date_start = '') {
		if (empty($date_start)) {
			$date_start = $this->current_date();
		}
		$joined = strtotime($date_start) - strtotime($date);
		// Seconds
		if ($joined <= 60) {
			$final = $joined . " " . lg_seconds;
		}
		// Minutes
		else if ($joined <= 3600) {
			$final = ceil($joined / 60);
			$final .= " " . lg_minutes;
		}
		// Hours
		else if ($joined <= 86400) {
			$final = ceil($joined / 3600);
			$final .= " " . lg_hours;
		}
		// Days 
		else if ($joined <= 2629743) {
			$final = ceil($joined / 86400);
			$final .= " " . lg_days;
		}
		// Months 
		else {
			$final = ceil($joined / 2629743);
			$final .= " " . lg_months;
		}
		return $final;
	}
	
	
	// --------------------------------------------------------------------
	// 	Remove MS Word characters
	//	DEPRECATED!
	//	No longer used due to complications
	//	with foreign language special characters.
	
	function remove_ms_word_characters($text) {
		/*
		$search = array(
			chr(130),
			chr(132),
			chr(133),
			chr(145),
			chr(146),
			chr(147),
			chr(148)
		);
		$replace = array(
			',',
			'"',
			'...',
			"'",
			"'",
			'"',
			'"'
		);
		$text = str_replace($search, $replace, $text);
		*/
		// $text = mb_convert_encoding($text, 'HTML-ENTITIES', 'UTF-8');
		return $text;
	}
	
	
	// --------------------------------------------------------------------
	// 	Run a custom action.
	//	Possible criteria:
	//	date_after
	//	date_before
	//	user_running
	//	user_acted_upon
	//	Anything in $data array
	
	function custom_actions($action,$user_running_task = "",$user_acted_upon = "",$data = "") {
		$q = "SELECT `criteria`,`mysql_command`,`code` FROM `" . TABLE_PREFIX . "custom_actions` WHERE `action`='$action' ORDER BY `run_order` ASC";
		$ca = $this->run_query($q);
		while ($row = mysql_fetch_assoc($ca)) {
			$run = "1";
			// Criteria match?
			if (! empty($row['criteria'])) {
				$criteria = unserialize($row['criteria']);
				foreach ($criteria as $field => $value) {
					if ($field == "date_after") {
						if ($this->current_date <= $value) { $run = "0"; }
					}
					else if ($field == "date_before") {
						if ($this->current_date >= $value) { $run = "0"; }
					}
					else if ($field == "user_running") {
						if ($user_running_task != $value) { $run = "0"; }
					}
					else if ($field == "user_acted_upon") {
						if ($user_acted_upon != $value) { $run = "0"; }
					}
					else {
						if ($data[$name] != $value) { $run = "0"; }
					}
				}
			}
			// Did this match the criteria?
   			if ($run == "1") {
   				// MySQL Command
   				if (! empty($row['mysql_command'])) {
   					$qF = $row['mysql_command'];
   					$mysql_run = $this->run_query($qF);
   				}
   				// Code
   				if (! empty($row['code'])) {
   					@eval($row['code']);
   				}
   				// Code
   				if (! empty($row['include'])) {
   					@eval($row['code']);
   				}
   			}
   		}
	}
	
	
	// --------------------------------------------------------------------
	// Random ID
	
	function random_id($length = '10') {
		return substr(md5(uniqid()),0,$length);
	}
	
}

?>
