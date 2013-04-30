<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Displays pages used on the admin CP.
	
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

require "../config.php";

if (PERFORMANCE_TESTS == '1') {
	$start = microtime(true);
}

require "../includes/admin.functions.php";
$admin = new admin;

// ----------------------------------------------------------------------------------
//	Logged in as admin?

if (empty($user) || $privileges['cp_access'] != "1") {
	$db->show_error('You do not have the privilieges to access this page.');
	exit;
}

// ----------------------------------------------------------------------------------
//	Available Features

$user_opts = unserialize($user_data['options']);

if (empty($_GET['l'])) {
	// If the user simply clicks through to the
	// control panel, be sure to return him/her
	// to the last page they were on.
	if (! empty($user_opts['admin_location'])) {
		header('Location: ' . ADMIN_URL . "/index.php?" . $user_opts['admin_location']);
		exit;
	}
	// But we can only do that if the information
	// available to us, so use this as a fall back.
	else {
		$page = "includes/index.php";
	}
}

// Here the user is browsing the
// admin CP. Let him browse!
else {
	if ($_GET['l'] == 'home') {
		$_GET['l'] = 'index';
	}
	$page = "includes/" . $_GET['l'] . ".php";
	// Create the location memory
	$array_loc = array(
		'admin_location' => $_SERVER['QUERY_STRING']
	);
	$session->update_user_options($array_loc);
}

ob_start();
include($page);
$contents = ob_get_contents();
ob_end_clean();

if (PERFORMANCE_TESTS == '1') {
	$end = microtime(true);
	$dif = $end - $start;
	echo "<div style=\"z-index:9999999;position:absolute;top:53;left:403px;padding:8px;font-size:0.7em;font-weight:bold;\"><b>Load time = $dif seconds.</b></div>";
}

require "includes/header.php";
echo $contents;
require "includes/footer.php";
exit;

?>