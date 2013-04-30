<?php


/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	http://www.ascadnetworks.com/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Setup Functions.
	
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

error_reporting('1');

// Load the DB functions
require "../includes/db.functions.php";
$db = new db;


// ----------------------------------------

function setup_check_config() {
	if (file_exists('../config.php')) {
		setup_error('It appears that the installation is already complete. Please delete this setup folder!');
	}
}


// ----------------------------------------

function setup_error($error,$title = 'Error!',$link = "javascript:history.go(-1);",$link_name = "Click here to try again!") {
	setup_header();
echo <<<qq
<h1>$title</h1>
<center>
$error
</center>
<p style="text-align:center;"><a href="$link">$link_name</a></p>
qq;
	setup_footer();
	exit;
}

// ----------------------------------------

function setup_success($text,$title = 'Error!') {
	setup_header();
echo <<<qq
<h1>$title</h1>
<center>
$text
</center>
qq;
	setup_footer();
	exit;
}


// ----------------------------------------

function setup_header() {
echo <<<qq

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head> 
<title>Banana Dance Quick and Easy Program Setup</title> 
<style type="text/css">
<!--
body {
	margin: 0 0 50px 0;
	padding: 0;
	background: url('http://www.doyoubananadance.com/imgs/bg.png') top left repeat-x #F7E440;
	font-family: tahoma, arial, verdana;
	color: #666;
}

a {
	color: #A4C745;
	text-decoration: none;
}

h1 {
	font-weight: normal;
	color: #A4C745;
	font-size: 240%;
	text-align: center;
}

.shadow {
	-moz-text-shadow: 1px 1px 0 rgba(255, 255, 255, 0.75);
	-webkit-text-shadow: 1px 1px 0 rgba(255, 255, 255, 0.75);
	text-shadow: 1px 1px 0 rgba(255, 255, 255, 0.75);
}

#contain {
	width: 900px;
	margin: 0 auto 0 auto;
}

#main {
	font-size: 80%;
	line-height: 160%;
	width: 900px;
	z-index: 1;
}

#main_top {
	padding-top: 20px;
}

#logo {
	height: 137px;
	z-index: 1000;
	float: right;
	margin-right: 10px;
}

#main_content {
	background-color: #fff;
	border: 1px solid #272B2E;
	-webkit-border-radius: 15px;
	-moz-border-radius: 15px;
	border-radius: 15px;
	margin-top: -15px
} 

.pad {
	padding: 30px 50px 30px 50px;
}

.clear {
	clear: both;
}

label {
	font-weight: bold;
	float: left;
	width: 150px;
}

input, select {
	margin: 0 0 20px 0;
	border: 1px solid #ccc;
	padding: 5px;
}

fieldset {
	border: 0px;
	border-top: 1px solid #ccc;
	margin: 25px 0 0 0;
}

legend {
	padding: 0 10px 0 10px;
	font-weight: bold;
	font-size: 120%;
}

p {
	margin: 15px 0 25px 0;
}

.good {
	color: #A4C745;
}

.bad {
	color: #cc0000;
}

.icon {
	padding: 0 11px 0 0;
	vertical-align: middle;
}

p.field_desc {
	margin: -15px 0 25px 150px;
	font-size: 80%;
}

.error {
	padding: 10px;
	color: #fff;
	background-color: #ff0000;
	text-align: center;
}

.highlight {
	background-color: #FFF9C3;
}
-->
</style>
</head>
<body>

<form action="complete.php" method="post">
<div id="contain">
	<div id="main">
		<div id="logo"><img src="http://www.doyoubananadance.com/imgs/setup_logo.png" width="" height="" border="0" alt="Do you Banana Dance?" /></div>
		<div id="main_content">
			<div class="pad">
qq;
}

function setup_footer() {
echo <<<qq
			</div>
		</div>
	</div>
	<div class="clear"></div>
</div>
<p style="text-align:center;font-size:80%;">Distrubuted under the "<a href="http://www.gnu.org/licenses/gpl.html" target="_blank">GNU General Public License</a>".<br />&copy; 2011 <a href="http://www.ascadnetworks.com/" target="_blank">Ascad Networks</a>.</p>
</form>

</body>
</html>
qq;
}

?>