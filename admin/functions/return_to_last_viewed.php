<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Returns the user from the admin CP to the
	last viewed page on the website.
	
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

require "../../config.php";
require "../../includes/admin.functions.php";
$admin = new admin;

// ----------------------------------------------------------------------------------
//	Logged in and has privileges?
$admin->check_permission('is_admin',$user,$privileges);

// ----------------------------------------------------------------------------------
//	Continue

$user_opts = unserialize($user_data['options']);

if (! empty($user_opts['last_page_viewed'])) {
	$link = $manual->prepare_link($user_opts['last_page_viewed']);
} else {
	$link = URL;
}

header("Location: $link");
exit;

?>