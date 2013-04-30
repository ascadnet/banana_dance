<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: User profile pic management.
	
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

if (empty($user)) {
	echo "0+++" . lg_login_to_use_feature;
	exit;
} else {

	// ------------------------------------------
	// 	Check the current password
	require PATH . "/includes/password.functions.php";
	$password = new password;
	if (empty($_POST['current_password'])) {
		$db->show_error(lg_incorrect_password);
		exit;
	} else {
		$q = "SELECT `id`,`salt`,`password` FROM `" . TABLE_PREFIX . "users` WHERE `username`='$user' LIMIT 1";
		$salt = $db->get_array($q);
   		$check_pass = $password->encode_password($_POST['current_password'],$salt['salt']);
   		if ($check_pass != $salt['password']) {
			$db->show_error(lg_incorrect_password);
			exit;
   		}
	}
	
	require PATH . "/includes/image.functions.php";
	$image = new image;
	
	// -------------------------------------------------
	// 	Upload the image
		
	$ext = $image->get_extension($_FILES["file"]['name']);
	// File selected?
	if (empty($_FILES["file"]['name'])) {
		$db->show_error(lg_select_file);
		exit;
	}
	// Incorrect extension?
	if ($ext != "jpg" && $ext != "jpeg" && $ext != "png" && $ext != "gif") {
		$db->show_error(lg_pic_ext);
		exit;
	}
	// Too large?
	if ($_FILES["file"]["size"] > $db->get_option('max_profile_pic_size')) {
		$error = lg_pic_too_large;
		$max_size = $db->convert_file_size($db->get_option('max_profile_pic_size'));
		$error = str_replace('%size%',$max_size,$error);
		$db->show_error($error);
		exit;
	}
	
	// Final path
   	$path = PATH . "/generated/user_" . $user_data['id'] . "." . $ext;
   	$path2 = PATH . "/generated/user_" . $user_data['id'] . "_tb." . $ext;
   	move_uploaded_file($_FILES["file"]["tmp_name"],$path);
   	
   	// Crop the image
	$crop_image2 = $image->crop_image($path,$path2,'60','',$ext,'0','0','1');
	$crop_image1 = $image->crop_image($path,$path,$db->get_option('profile_pic_max_width'),$db->get_option('profile_pic_max_height'),$ext,'0','0','1');


   	// ------------------------------------------
   	//	Update DB
   	
   	$update = $db->update_eav('picture','user_' . $user_data['id'] . "." . $ext,$salt['id'],'user_id');
   	$update = $db->update_eav('thumbnail','user_' . $user_data['id'] . '_tb.' . $ext,$salt['id'],'user_id');
   	
   	// ------------------------------------------
   	//	Reply
   	header('Location: ' . URL . "/user/$user/profile_pic");
   	exit;

}


?>