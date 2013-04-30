<?php


/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	http://www.ascadnetworks.com/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Completes the setup.
	
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


// ----------------------------------------
//	Current Version
$version = "B.2.3";
			

// ----------------------------------------
// Load the setup functions
require "functions.php";


// ----------------------------------------
//	Make sure the path is correct

if (! file_exists($_POST['path'] . "/index.php")) {
	$error = "The path you entered does not appear to be correct.";
	setup_error($error);
	exit;
}


// ----------------------------------------
//	MySQL Database Connection Test

if (! @mysql_connect($_POST['db_host'],$_POST['db_user'],$_POST['db_pass'])) {
	$error = "MySQL information incorrect.";
	setup_error($error);
	exit;
} else {
	if (! @mysql_select_db($_POST['db_name'])) {
		$error = "Could not select MySQL database.";
		setup_error($error);
		exit;
	}
}

// ----------------------------------------
// 	Language selected?
if (empty($_POST['language'])) {
	$_POST['language'] = 'english';
}

$mysql1 = $_POST['path'] . "/setup/mysql/tables.php";
$mysql2 = $_POST['path'] . "/setup/mysql/inserts.php";
if (! file_exists($mysql1) && ! file_exists($mysql2)) {
   	$error = "Could not select MySQL files in 'setup/mysql' folder.";
   	setup_error($error);
   	exit;
}


// ----------------------------------------
//	Check Admin Information

if ($_POST['password'] != $_POST['repeat_password']) {
   	$error = "Administrative passwords did not match!";
   	setup_error($error);
   	exit;
}


// Check password strength
require "../includes/password.functions.php";
$password = new password;

$strength = $password->check_strength($_POST['password'],'number');
if ($strength < 2) {
   	$error = "Password not strong enough: try adding more variations in characters, numbers, and symbols.";
   	setup_error($error);
   	exit;
}

// Encode the password
$salt = $password->generate_salt($_POST['username']);
$encode_pass = $password->encode_password($_POST['password'],$salt);


// ----------------------------------------
//	config.php file

$config_path = $_POST['path'] . "/config.php";
if (is_writable($_POST['path'])) {
	$show_config = '0';
} else {
	$show_config = '1';
}

// Generate the salts
$put_salt = substr(uniqid(),5,4);
$put_salt1 = uniqid() . md5(time()) . md5(rand(10000,99999)) . uniqid();
$put_salt1 = substr($put_salt1,0,60);
$put_salt2 = rand(1000000000,9999999999) . rand(1000000000,9999999999) . rand(1000000000,9999999999) . rand(1000000000,9999999999) . rand(1000000000,9999999999) . rand(1000000000,9999999999);

$_POST['url'] = rtrim($_POST['url'],'/');

// Line files for config.php
$config_lines = "<?php\n";
$config_lines .= "\n";
$config_lines .= "/*	====================================================\n";
$config_lines .= "\n";
$config_lines .= "	BANANA DANCE by Ascad Networks\n";
$config_lines .= "	http://www.doyoubananadance.com/\n";
$config_lines .= "	http://www.ascadnetworks.com/\n";
$config_lines .= "	Copyright (C) 2011  Ascad Networks\n";
$config_lines .= "	\n";
$config_lines .= "	File Function: Basic program configuration. Also\n";
$config_lines .= "	loads the requires classes to run the program.\n";
$config_lines .= "	This needs to be called on every program file.\n";
$config_lines .= "	\n";
$config_lines .= "	This program is free software: you can redistribute it and/or modify\n";
$config_lines .= "	it under the terms of the GNU General Public License as published by\n";
$config_lines .= "	the Free Software Foundation, either version 3 of the License, or\n";
$config_lines .= "	(at your option) any later version.\n";
$config_lines .= "	\n";
$config_lines .= "	This program is distributed in the hope that it will be useful,\n";
$config_lines .= "	but WITHOUT ANY WARRANTY; without even the implied warranty of\n";
$config_lines .= "	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the\n";
$config_lines .= "	GNU General Public License for more details.\n";
$config_lines .= "	\n";
$config_lines .= "	You should have received a copy of the GNU General Public License\n";
$config_lines .= "	along with this program.  If not, see <http://www.gnu.org/licenses/>.\n";
$config_lines .= "\n";
$config_lines .= "====================================================== */\n";
$config_lines .= "\n";
$config_lines .= "\n";
$config_lines .= "// ----------------------------------------\n";
$config_lines .= "// 	MySQL\n";
$config_lines .= "\n";
$config_lines .= "define('TABLE_PREFIX','" . $_POST['db_prefix'] . "');\n";
$config_lines .= "define('MYSQL_HOST','" . $_POST['db_host'] . "');\n";
$config_lines .= "define('MYSQL_DB','" . $_POST['db_name'] . "');\n";
$config_lines .= "define('MYSQL_USER','" . $_POST['db_user'] . "');\n";
$config_lines .= "define('MYSQL_PASS','" . $_POST['db_pass'] . "');\n";
$config_lines .= "\n";
$config_lines .= "\n";
$config_lines .= "// ----------------------------------------\n";
$config_lines .= "// 	Paths\n";
$config_lines .= "\n";
$config_lines .= "define('SITE_SALT','" . $put_salt . "');\n";
$config_lines .= "define('SALT1','" . $put_salt1 . "');\n";
$config_lines .= "define('SALT2','" . $put_salt2 . "');\n";
$config_lines .= "\n";
$config_lines .= "define('COMPANY_URL','" . $_POST['company_url'] . "');\n";
$config_lines .= "\n";
$config_lines .= "define('PATH','" . rtrim($_POST['path'],'/') . "');\n";
$config_lines .= "define('URLHOLD','" . rtrim($_POST['url'],'/') . "');\n";
$config_lines .= "define('ADMIN_FOLDER','" . trim($_POST['admin_url'],'/') . "');\n";
$config_lines .= "define('ADMIN_URL','" . rtrim($_POST['url'],'/') . "/" . trim($_POST['admin_url'],'/') . "');\n";
$config_lines .= "\n";
$config_lines .= "\n";
$config_lines .= "// ----------------------------------------\n";
$config_lines .= "//	PHP Configuration\n";
$config_lines .= "\n";
$config_lines .= "// ini_set('display_errors', 1); \n";
$config_lines .= "// ini_set('error_log', PATH . '/generated/error_log.txt'); \n";
$config_lines .= "define('PERFORMANCE_TESTS','0');\n";
$config_lines .= "\n";
$config_lines .= "\n";
$config_lines .= "// ----------------------------------------\n";
$config_lines .= "// 	Let's start dancing...\n";
$config_lines .= "\n";
$config_lines .= "define('LANGUAGE','" . $_POST['language'] . "');\n";
$config_lines .= "require PATH . '/addons/languages/' . LANGUAGE . '.php';\n";
$config_lines .= "require PATH . '/start_load.php';\n";
// $config_lines .= "require PATH . '/addons/languages/" . $_POST['language'] . ".php';\n";
$config_lines .= "\n";
$config_lines .= "?>";

$fp = @fopen($config_path, 'w');
@fwrite($fp, $config_lines);
@fclose($fp);

// This will be updated the first time the
// options are updated.
$globals_path = $_POST['path'] . "/generated/globals.php";
$fp = @fopen($globals_path, 'w');
@fwrite($fp, '');
@fclose($fp);


// ----------------------------------------
//	MySQL

mysql_connect($_POST['db_host'],$_POST['db_user'],$_POST['db_pass']) or die('MySQL Error A: ' . mysql_error());
mysql_select_db($_POST['db_name']) or die('MySQL Error B: ' . mysql_error());

require $mysql1;
require $mysql2;


// ----------------------------------------
//	Continue

if ($show_config == '1') {
	
	$title = "Setup (Almost) Complete";
	$error = "<p class=\"error\">The config.php file could not be written! Please copy and paste the following code and save it as \"config.php\" within your program's main directory.</p>";
	$error .= "<textarea name=\x\" style=\"width:100%;height:300px;\">$config_lines</textarea>";
	$error .= "<p>Once you have created the file, <b>delete the setup directory</b>, and start dancing. Enjoy!</p>";
	setup_error($error,$title,$_POST['url'],'I created the file and deleted the setup directory, take me to my Banana Dance site!');
	exit;
	
} else {

	$title = "Setup Complete";
	$error = "<p>Congratulations, your setup is complete and you're ready to start Banana Dancing!</p>";
	$error .= "<p class=\"error\">Delete the setup folder from your server before continuing!</p>";
	setup_error($error,$title,$_POST['url'],'Click here to start dancing!');
	exit;
	
}

?>