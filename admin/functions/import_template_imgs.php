<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Import images from a theme into the
	media library.
	
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
require "../../includes/image.functions.php";
$image = new image;


// ----------------------------------------------------------------------------------
//	Run the commands

// type=[theme|mobile]&theme=[theme_name]

if ($_GET['type'] == 'theme') {
	$dir = PATH . "/templates/html/" . $_GET['theme'] . "/imgs";
}
else if ($_GET['type'] == 'mobile') {
	$dir = PATH . "/templates/mobile/" . $_GET['theme'] . "/imgs";
}

$import = $image->get_images_in_dir($dir);

$notice = "Imported images.";

if ($_GET['type'] == 'theme') {
	header('Location: ' . ADMIN_URL . '/index.php?l=themes&notice=' . $notice);
	exit;
} else {
	header('Location: ' . ADMIN_URL . '/index.php?l=mobile_themes&notice=' . $notice);
	exit;
}

?>