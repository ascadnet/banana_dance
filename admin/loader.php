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

if (empty($_GET['l'])) {
	$page = "includes/index.php";
} else {
	$page = "includes/" . $_GET['l'] . ".php";
}

ob_start();
include($page);
$contents = ob_get_contents();
ob_end_clean();

if (PERFORMANCE_TESTS == '1') {
	$end = microtime(true);
	$dif = $end - $start;
	echo "<div class=\"bd_system\"><b>Performance Testing: $dif</b></div>";
}

echo $contents;
exit;

?>