<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Download file functions.
	
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

// Get file info from database
$q = "SELECT * FROM `" . TABLE_PREFIX . "attachments` WHERE `id`='" . $db->mysql_clean($_GET['file']) . "' LIMIT 1";
$file = $db->get_array($q);

// Checks
if (empty($file['id'])) {
	echo "0+++" . lg_dl_file_not_found;
	exit;
}

else if ($file['login'] == '1' && empty($user)) {
	$db->show_error(ld_dl_login_to_dl);
	exit;
}

else {

	// Download limit?
	if ($file['limit'] > 0) {
		if (! empty($user)) {
			$add_where = "AND (`ip`='" . $_SERVER['REMOTE_ADDR'] . "' OR `user`='$user')";
		} else {
			$add_where = "AND (`ip`='" . $_SERVER['REMOTE_ADDR'] . "')";
		}
		$count = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "attachments_dls` WHERE `dl`='" . $db->mysql_clean($_GET['file']) . "'" . $add_where;
		$total = $db->get_array($count);
		if ($total['0'] > $file['limit']) {
			$msg = str_replace('','',ld_dl_limit);
			$db->show_error($msg);
			exit;
		}
	}
	
	// Now download it
	$host = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	
	$q = "INSERT INTO `" . TABLE_PREFIX . "attachments_dls` (`date`,`ip`,`host`,`user`,`dl`) VALUES ('" . $db->current_date() . "','" . $db->mysql_clean($_SERVER['REMOTE_ADDR']) . "','" . $host . "','$user','" . $db->mysql_clean($_GET['file']) . "')";
	$insert = $db->insert($q);
	
	$q1 = "UPDATE `" . TABLE_PREFIX . "attachments` SET `downloads`=(`downloads`+1) WHERE `id`='" . $db->mysql_clean($_GET['file']) . "' LIMIT 1";
	$update = $db->update($q1);
	
	// E-mail admin?
   	$special_changes = array(
   		'%filename%' => $file['filename'],
   		'%path%' => $file['server_path'],
   		'%url%' => $file['path']
   	);
   	$sent = $template->send_template($user,'file_downloaded',"",$special_changes);
	
	// Send to user
	$mm_type = "application/octet-stream";
	header("Content-type: application/force-stream");
	header("Content-Transfer-Encoding: Binary");
	header("Content-length: " . filesize($file['server_path']));
	header("Content-disposition: attachment; filename=\"" . basename($file['filename'])."\"");
	readfile($file['server_path']);
	exit;
	
}

?>