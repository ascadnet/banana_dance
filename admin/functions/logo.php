<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Handles the logo upload.
	
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
	
	require PATH . "/includes/image.functions.php";
	$image = new image;

	// -----------------------------------------
	//   Remove Existing Logo
		
	if ($_POST['remove_logo'] == '1') {
		$current = $db->get_option('logo');
		$ext = $image->get_extension($current);
		if (empty($ext)) {
			$path1 = PATH . "/generated/logo.jpg";
			$path2 = PATH . "/generated/logo.png";
			$path3 = PATH . "/generated/logo.gif";
			$del1 = @unlink($path1);
			$del2 = @unlink($path2);
			$del2 = @unlink($path3);
		} else {
			$path = PATH . "/generated/logo." . $ext;
			$del = unlink($path);
		}
		$update = $db->update_option('logo','');
		echo "1+++" . $db->get_option('site_name');
		exit;
	
	}
	
	// -----------------------------------------
	//   Upload a logo
	
	else {
		
		$ext = $image->get_extension($_FILES["logo"]['name']);
		
		// File selected?
		if (empty($_FILES["logo"]['name'])) {
			$db->show_error(lg_select_file);
			exit;
		}
		
		// Incorrect extension?
		if ($ext != "jpg" && $ext != "jpeg" && $ext != "png" && $ext != "gif") {
			$db->show_error(lg_pic_ext);
			exit;
		}
		
		// Move to generated
		$path = PATH . "/generated/logo." . $ext;
		$url = URL . "/generated/logo." . $ext;
	   	move_uploaded_file($_FILES["logo"]["tmp_name"],$path);
		
		if (! empty($_POST['height']) || ! empty($_POST['width'])) {
			if (! empty($_POST['height']) && ! empty($_POST['width'])) {
	   			// Crop the image
				$crop_image = $image->crop_image($path,$path,$_POST['width'],$_POST['height'],$ext,'0');
			}
			else if (! empty($_POST['height'])) {
				$crop_image = $image->crop_image($path,$path,'',$_POST['height'],$ext,'0');
			}
			else if (! empty($_POST['width'])) {
				$crop_image = $image->crop_image($path,$path,$_POST['width'],'',$ext,'0');
			}
			else {
				$db->show_error('Please input either a width, height, or both for the program to resize your logo to.');
				exit;
			}
		}
		
		// Update option
		$update = $db->update_option('logo',$url);
		
		header('Location: ' . ADMIN_URL . '/index.php?l=logo');
		exit;
	
	}

?>