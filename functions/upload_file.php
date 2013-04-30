<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: File upload management.
	
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

if ($_GET['none'] == '1') {
	exit;
}

require "../config.php";

// Privilieges and checks
if (empty($user)) {
   $error = '1';
}

//if (empty($_POST['id'])) {
	if ($_POST['type'] == 'file' && $privileges['upload_files'] != '1') {
		$error = '1';
		$error_msg = lg_cannot_upload;
	}
	else if ($_POST['type'] == 'image' && $privileges['upload_images'] != '1') {
		$error = '1';
		$error_msg = lg_cannot_upload_img;
	}
	else if (empty($_POST['type'])) {
		$error = '1';
		$error_msg = lg_error;
	}
	else if (empty($_FILES['file']) && empty($_POST['onserver'])) {
		$error = '1';
		$error_msg = lg_upload_nofile_selected;
	}
	else if (! empty($_POST['onserver']) && ! file_exists($_POST['onserver'])) {
		$error = '1';
		$error_msg = lg_file_not_on_server;
	}
//}


if ($error == '1') {
	iframe_error($error_msg);
	exit;
}


// On server
if (! empty($_POST['onserver'])) {

	echo "0|on server.";

}

// Uploading
else {
				
	// Downloadable File
	if ($_POST['type'] == 'file') {

		if (! empty($_POST['onserver'])) {
			// Nothing...
		} else {
			$new_path = PATH . "/generated/" . $_FILES['file']['name'];
			$url_path = URL . "/generated/" . $_FILES['file']['name'];
		}
		
		$id_put = uniqid();
		
   	   	// MySQL query
		// Editing media
		if (empty($_POST['edit_id'])) {
   	   	
		   	move_uploaded_file($_FILES["file"]["tmp_name"],$new_path);
		   	
	   	   	$q = "
	   	   		INSERT INTO `" . TABLE_PREFIX . "attachments` (`id`,`path`,`server_path`,`filename`,`owner`,`login`,`limit`)
	   	   		VALUES ('" . $id_put . "','" . $db->mysql_clean($url_path) . "','" . $db->mysql_clean($new_path) . "','" . $db->mysql_clean($_FILES['file']['name']) . "','$user','" . $db->mysql_clean($_POST['login_req']) . "','" . $db->mysql_clean($_POST['limit_dls']) . "')
	   	   	";
	   	   	$insert_id = $db->insert($q);
	   	   	
   	   	} else {
   	   	
	   		$dl_info = $db->get_file_info($_POST['edit_id']);
	   	   		
			if (! empty($_FILES["file"]["name"])) {
	   	   		if ($_FILES["file"]["name"] != $dl_info['filename']) {
	   	   			$unlink = @unlink($dl_info['server_path']);
	   	   		}
				$new_path = PATH . "/generated/" . $_FILES['file']['name'];
				$url_path = URL . "/generated/" . $_FILES['file']['name'];
		   		move_uploaded_file($_FILES["file"]["tmp_name"],$new_path);
				$final_fname = $_FILES['file']['name'];
			} else {
				$final_fname = $dl_info['name'];
				$url_path = $dl_info['url'];
				$new_path = $dl_info['path'];
			}
		
	   	   	$q = "
	   	   		UPDATE `" . TABLE_PREFIX . "attachments` SET `filename`='" . $db->mysql_clean($final_fname) . "',`path`='" . $db->mysql_clean($url_path) . "',`server_path`='" . $db->mysql_clean($new_path) . "',`limit`='" . $db->mysql_clean($_POST['limit_dls']) . "',`login`='" . $db->mysql_clean($_POST['login_req']) . "' WHERE `id`='" . $db->mysql_clean($_POST['edit_id']) . "' LIMIT 1
	   	   	";
	   	   	$update = $db->update($q);
	   	   	
   	   	}
	
		// Reply and reload...
   		refresh_list('file');
   		exit;
	
	}
	
	
	// -------------------------------
	// Image
	
	else {
	
		if (! empty($_FILES["file"]["name"])) {
			$ext = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));
			if ($ext != 'jpg' && $ext != 'jpeg' && $ext != 'png' && $ext != 'gif') {
				$error_msg = "The Media Library is for images and only supports jpg, png, and gif files. Please upload all other files to the download library.";
				iframe_error($error_msg);
				exit;
			}
		}
	
		require "../includes/image.functions.php";
		$image = new image;
	
		if (! empty($_POST['edit_id'])) {
	   		$path = PATH . $img_info['location'];
   			$img_info = $image->get_image($_POST['edit_id']);
	   		$new_path = PATH . $img_info['location'];
			$relative_path = $img_info['location'];
		} else {
	   		$path = PATH . "/generated/media";
	   		if (! file_exists($path)) {
	   			mkdir($path);
	   		}
	   		$new_path = $path . "/" . $_FILES["file"]["name"];
	   		// $_SERVER['DOCUMENT_ROOT'] instead of PATH is what
	   		// it should be, but the damn framework isn't built
	   		// on that, so smooth move JB... start hacking the
	   		// other functions.
			$relative_path = str_replace(PATH,'',$new_path);
		}
		
		// Remove unwanted
		$_POST['title'] = str_replace('"','',$_POST['title']);
		$_POST['title'] = str_replace("'",'',$_POST['title']);		   	
		   	
		// Editing media
		if (! empty($_POST['edit_id'])) {
		
			$insert_id = $db->mysql_clean($_POST['edit_id']);
		
			if (! empty($_FILES["file"]["name"])) {
		 	  	if (@move_uploaded_file($_FILES["file"]["tmp_name"],$new_path)) {
					$thumb_name = $path . "/tb-" . $img_info['filename'];
					$thumbnail = $image->crop_image($new_path,$thumb_name,'250','');
				} else {
					$error = str_replace('%file%',$img_info['filename'],lg_ml_not_writable);
					iframe_error($error);
					exit;
				}
			}
			
			$q = "UPDATE `" . TABLE_PREFIX . "media` SET `title`='" . $db->mysql_clean($_POST['title']) . "',`caption`='" . $db->mysql_clean($_POST['caption']) . "',`public`='" . $db->mysql_clean($_POST['public']) . "' WHERE `id`='" . $insert_id . "' LIMIT 1";
			$update = $db->update($q);
		
			$q1 = "DELETE FROM `" . TABLE_PREFIX . "media_tags` WHERE `img_id`='" . $insert_id . "'";
			$del = $db->delete($q1);
			
		}
		
		// Adding media
		else {
		
		   	move_uploaded_file($_FILES["file"]["tmp_name"],$new_path);
		   	
		   	// MySQL query
		   	$q = "
		   		INSERT INTO `" . TABLE_PREFIX . "media` (`filename`,`location`,`title`,`caption`,`owner`,`date`,`public`)
		   		VALUES ('" . $db->mysql_clean($_FILES["file"]["name"]) . "','" . $db->mysql_clean($relative_path) . "','" . $db->mysql_clean($_POST['title']) . "','" . $db->mysql_clean($_POST['caption']) . "','" . $user_data['id'] . "','" . $db->current_date() . "','" . $db->mysql_clean($_POST['public']) . "')
		   	";
		   	$insert_id = $db->insert($q);

			$thumb_name = $path . "/tb-" . $_FILES["file"]["name"];
			$thumbnail = $image->crop_image($new_path,$thumb_name,'250','');

		}

		// Tags
   	   	$tags = explode(',',$_POST['tags']);
   	   	foreach ($tags as $aTag) {
   	   		$aTag = trim($aTag);
   	   		if (! empty($aTag)) {
   	   			$values .= ", ('$insert_id','" . $db->mysql_clean($aTag) . "')";
   	   		}
   	   	}
   	   	if (! empty($values)) {
   	   		$values = substr($values,2);
   		   	$q1 = "INSERT INTO `" . TABLE_PREFIX . "media_tags` (`img_id`,`tag`) VALUES $values";
   		   	$insert2 = $db->insert($q1);
   		}
   		
		// Reply and reload...
   		refresh_list();
   		exit;
		
	}
	
}


function refresh_list() {
   	echo "<html>
   	<body>
   	<head>
   	<script src=\"" . URL . "/js/jquery.js\" type=\"text/javascript\"></script>
   	<script type=\"text/javascript\">
   		parent.refreshMedia('');
   	</script>
   	</head>
   	</body>
   	</html>
   	";
   	exit;
}

function iframe_error($error) {
	echo "<html>
	<body>
	<head>
	<script src=\"" . URL . "/js/jquery.js\" type=\"text/javascript\"></script>
	<script type=\"text/javascript\">
	$('#upload_error_display',parent.document.body).show();
	$('#upload_error_display',parent.document.body).html('$error');
	</script>
	</head>
	</body>
	</html>
	";
	exit;
}

?>