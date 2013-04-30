<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2012 Jon Belelieu
	
	File Function: Post to someone's profile!
	
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


/**
 * Delete a posting.
 */
if ($_POST['action'] == 'del') {

	$activity = $manual->get_activity($_POST['id']);
	if ($activity['sup_id'] == $user_data['id'] || $activity['user'] == $user_data['id']) {
		$q = "DELETE FROM `" . TABLE_PREFIX . "activity` WHERE `id`='" . $db->mysql_clean($_POST['id']) . "' AND (`sup_id`='" . $db->mysql_clean($user_data['id']) . "' OR `user`='" . $db->mysql_clean($user_data['id']) . "') LIMIT 1";
		$delete = $db->delete($q);
		echo "1+++Saved";
		exit;
	} else {
		echo "0+++" . lg_no_permissions;
		exit;
	}


}

/**
 * Post to profile page.
 */
else if ($_POST['action'] == 'post') {
	   	
	if (empty($user)) {
		echo "0+++" . lg_login_to_use_feature;
		exit;
	}
	if (empty($_POST['post'])) {
		echo "0+++" . lg_req_fields . " Post";
		exit;
	}
	if (empty($_POST['id'])) {
		echo "0+++" . lg_req_fields . " User ID";
		exit;
	}
	
	$username_send = $manual->get_username_from_id($_POST['id']);
	$log = $db->begin_task('post_to_profile',$user_data['username'],$username_send);
	
	$q = "
		INSERT INTO `" . TABLE_PREFIX . "activity` (`user`,`sup_id`,`date`,`post`,`type`)
		VALUES ('" . $db->mysql_clean($_POST['id']) . "','" . $user_data['id'] . "','" . $db->current_date() . "','" . $db->mysql_clean($_POST['post']) . "','profilepost')
	";
	$insert = $db->insert($q);
	
	// Format the post...
	$user_link = $session->get_user_link($_POST['id']);
	$user_pic = $session->get_profile_thumb($_POST['id']);
	$poster_link = $manual->get_user_link($user_data['username']);
	$final_options = "<p class=\"feed_options\"><a href=\"#\" onclick=\"return delPosting('" . $row['id'] . "');\">Delete Posting</a></p>";
	
	$fpost = $manual->format_comment($_POST['post']);
	
	$special_changes = array(
		'%posting_id%' => $insert,
		'%username_by%' => $username_send,
		'%user_link%' => $user_link,
		'%user_pic%' => $user_pic,
		'%date%' => $db->format_date($db->current_date()),
		'%age%' => $db->get_age($db->current_date()),
		'%poster_link%' => $poster_link,
		'%feedpost_options%' => $final_options,
		'%poster_username%' => $user_data['username'],
		'%poster_pic%' => $user_data['profile_thumbnail'],
		'%post%' => $fpost
	);
	$template_content = $template->render_template('feed_profilepost',$username_send,$special_changes,'1','1');
	
   	$sent = $template->send_template($username_send,'profile_post',"",$special_changes);
   	
	$log = $db->complete_task('post_to_profile',$user_data['username'],$username_send);
	echo "1+++" . $template_content;
	exit;

}

?>