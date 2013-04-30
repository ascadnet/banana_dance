<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Change to/from mobile templates.
	
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

if (! empty($_COOKIE['skip_mobile'])) {
	$db->delete_cookie('skip_mobile');
} else {
	$years = time() + 31556926;
	$db->create_cookie('skip_mobile','1',$years);
}

if (! empty($_SERVER['HTTP_REFERER'])) {
	header('Location: ' . $_SERVER['HTTP_REFERER']);
	exit;
} else {
	header('Location: ' . URL);
	exit;
}

?>