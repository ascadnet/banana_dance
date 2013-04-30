<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: http://www.doyoubananadance.com/community/article/Classes+and+Functions/Overview
	
	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.

====================================================== */

if (file_exists('config.php')) {
	require "config.php";
} else {
	header('Location: setup/index.php');
	exit;
}

// ----------------------------------------
// 	Ignoring?

$ignore = array('/favicon.ico');
$_GET['filename'] = str_replace(PATH,'',$_GET['filename']);
if (in_array($_GET['filename'],$ignore)) {
	exit;
}

// -------------------------------------
// 	PERFORMANCE TESTS

if (PERFORMANCE_TESTS == '1') {
	$start = microtime(true);
}

// -------------------------------------
//	Error type?

if ($_GET['type'] == '404') {
	$final_error = 'The page you are trying to load does not exist.';
	$final_type = '404';
}
else if ($_GET['type'] == '500') {
	$final_error = 'A programming error has occurred. Please try again in 10 minutes. If the issue persists, please contact us!';
	$final_type = '500';
}
else {
	$final_error = 'An error has occur but we could not determine the cause.';
	$final_type = '';
}

// Log the error
$q = "INSERT INTO `" . TABLE_PREFIX . "errors` (`file`,`referrer`,`date`,`ip`,`type`) VALUES ('" . $db->mysql_clean($_GET['filename']) . "','" . $db->mysql_clean($_SERVER['HTTP_REFERER']) . "','" . $db->current_date() . "','" . $db->mysql_clean($_SERVER['REMOTE_ADDR']) . "','" . $final_type . "')";
$insert = $db->insert($q);

$error = $db->show_error($final_error);

if (PERFORMANCE_TESTS == '1') {
	$end = microtime(true);
	$dif = $end - $start;
	echo "<div class=\"bd_system\"><b>Performance Testing: $dif</b></div>";
}

echo $error;
exit;

?>