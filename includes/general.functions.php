<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Various general functions.
	
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

// ----------------------------------------
// 	Loader Class

function __autoload($class) {
	include_once(PATH . "/includes/" . $class . ".functions.php"); 
} 

// ----------------------------------------
// 	Comment options

function show_comment_options($owner,$comment_id,$article_id,$comment_text,$deleted = '0',$pending = '0',$max_threading = '',$padding_left = '',$thread_style = '') {
	global $db;
	global $user;
	global $privileges;
	global $manual;
	
	$return_comments = '';
 	
 	// Allow comment edits?
 	$articledata = $manual->get_article($article_id,'0','allow_comment_edits');
	$allow_comment_edits = $articledata['allow_comment_edits'];
		
	// Must be logged in for any
	// options to be possible.
 	if (! empty($user)) {
 	
 		// User privilieges
 		// $privileges = $this->get_user_privileges($user);
 		// $return_comments .= "<div class=\"bd_comment_options\" id=\"showReplyTop" . $comment_id . "\">";
 			// Reply possible?
 			// Removed: $login_to_comment == "1" && 
 			//  ^ $user already established above.
 			if ($padding_left < $max_threading && $thread_style != 'Forum') {
    				$show_reply = "1";
 				$return_add_comments .= " | <a href=\"#\" onclick=\"commentReply('" . $comment_id . "');return false;\">" . lg_comment_reply . "</a>";
 			} else {
    				$show_reply = "0";
 			}
 			// Edit comment?
 	   		if (($owner == $user && $allow_comment_edits == "1") || $privileges['can_alter_comments'] == "1") {
 	   			if ($deleted != "1") {
 	   				$show_edit = "1";
 	   				if ($pending == "1" && $privileges['can_alter_comments'] == "1") {
 						$return_add_comments .= " | <a href=\"#\" onclick=\"approveComment('" . $comment_id . "');return false;\">" . lg_approve . "</a>";
 					}
 					
 					$return_add_comments .= " | <a href=\"#\" onclick=\"commentEdit('" . $comment_id . "');return false;\">" . lg_quick_edit . "</a>";
 					
		 	   		if ($privileges['is_admin'] == '1') {
		 				$return_add_comments .= " | <a href=\"" . ADMIN_URL . "/index.php?l=comment&id=" . $comment_id . "\">" . lg_full_edit . "</a>";
		 	   		}	
 					
 					$return_add_comments .= " | <a href=\"#\" onclick=\"commentDelete('" . $comment_id . "');return false;\">" . lg_delete . "</a>";
 				}
 	   		}
 	   		// Ban User?
 	   		if ($privileges['can_ban'] == "1") {
 				$return_add_comments .= " | <a href=\"#\" onclick=\"userBan('" . $owner . "','" . $comment_id . "');return false;\">" . lg_ban_user . "</a>";
 	   		}
 	   		// Edit Status
 	   		if ($privileges['edit_comment_status'] == "1") {
 				// $return_add_comments .= " | <a href=\"" . ADMIN_URL . "/index.php?l=comment&id=" . $comment_id . "\">Change Status</a>";
 				$return_add_comments .= " | <a href=\"#\" onclick=\"reClassifyComment('$comment_id');\">" . lg_reclassify . "</a>";
 	   		}
 			$return_add_comments = ltrim($return_add_comments,' | ');
 			$return_comments .= $return_add_comments;
 		// $return_comments .= "</div>";
	 	// Reply Box
	 	if ($show_reply == "1") {
	 		$return_comments .= "<div class=\"bd_comment_reply\" id=\"showReply" . $comment_id . "\">
	 			<textarea name=\"reply" . $comment_id . "\" id=\"reply" . $comment_id . "\" style=\"width:100%;display:block;height:100px;\"></textarea>
	 			<p class=\"center\"><input type=\"button\" value=\"" . lg_comment_reply . "\" onclick=\"postComment('" . $article_id . "','" . $comment_id . "')\" /> <a href=\"#\" onclick=\"cancelReply('" . $comment_id . "');return false;\">Cancel</a></p>
	 		</div>";
	 	}
	 	// Edit Box
	 	if ($show_edit == "1") {
	 		$return_comments .= "<div class=\"bd_comment_reply\" id=\"showEdit" . $comment_id . "\">
	 			<textarea name=\"edit" . $comment_id . "\" id=\"edit" . $comment_id . "\" style=\"width:100%;display:block;height:100px;\">" . $comment_text . "</textarea>
	 			<p class=\"center\"><input type=\"button\" value=\"" . lg_edit . "\" onclick=\"editComment('" . $comment_id . "')\" /> <a href=\"#\" onclick=\"cancelEdit('" . $comment_id . "');return false;\">Cancel</a></p>
	 		</div>";
	 	}
 	}
	return $return_comments;
}


function article_links($article,$category,$favorites = '0',$follows = '0',$comments = '0') {
	global $user;
	global $user_data;
	global $manual;
	global $theme;
	// Images
	$image_url = URL . "/templates/html/" . $theme . "/imgs";
	// Basics
	if (empty($follows)) {
		$follows = '0';
	}
	if (empty($favorites)) {
		$favorites = '0';
	}
	// Comments
	$send  = "<ul id=\"bd_article_follow\">";
	// Follow / Favorite
	if (! empty($user)) {
		// Favorite?
		$fav = $manual->check_favorite($article,$user_data['id']);
		if ($fav == '1') {
			$send .= "<li class=\"favorites\"><a href=\"#\" onclick=\"addFavorite('" . $article . "','0');return false;\"><img src=\"$image_url/favorite_on.png\" id=\"favorite_img\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . lg_favorites_remove . "\" title=\"" . lg_favorites_remove . "\" /> <span id=\"article_favorites\">$favorites</span></a></li>";
		} else {
			$send .= "<li class=\"favorites\"><a href=\"#\" onclick=\"addFavorite('" . $article . "','1');return false;\"><img src=\"$image_url/favorite_off.png\" id=\"favorite_img\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . lg_favorites_add . "\" title=\"" . lg_favorites_add . "\" /> <span id=\"article_favorites\">$favorites</span></a></li>";
		}
		// Following
		$follow = $manual->check_follow($article,$user_data['id']);
		if ($follow == '1') {
			$send .= "<li class=\"follows\"><a href=\"#\" onclick=\"addFollow('" . $article . "','0');return false;\"><img src=\"$image_url/follow_on.png\" id=\"follow_img\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . lg_unfollow . "\" title=\"" . lg_unfollow . "\" /> <span id=\"article_follows\">$follows</span></a></li>";
		} else {
			$send .= "<li class=\"follows\"><a href=\"#\" onclick=\"addFollow('" . $article . "','1');return false;\"><img src=\"$image_url/follow_off.png\" id=\"follow_img\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . lg_follow . "\" title=\"" . lg_follow . "\" /> <span id=\"article_follows\">$follows</span></a></li>";
		}
		
	} else {
		$send .= "<li class=\"favorites\"><a href=\"#\" onclick=\"showRegister();return false;\"><img src=\"$image_url/favorite_off.png\" id=\"favorite_img\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . lg_favorites_add . "\" title=\"" . lg_favorites_add . "\" /> <span id=\"article_favorites\">$favorites</span></a></li>";
		$send .= "<li class=\"follows\"><a href=\"#\" onclick=\"showRegister();return false;\"><img src=\"$image_url/follow_off.png\" id=\"follow_img\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . lg_follow . "\" title=\"" . lg_follow . "\" /> <span id=\"article_follows\">$follows</span></a></li>";
	}
	// Print+Email Icons
//	$send .= "</ul><ul id=\"bd_article_links\">";
	
	if ($comments > 0) {
		$send .= "<li class=\"comments\"><a href=\"#comments\"><img src=\"$image_url/comments_on.png\" id=\"comment_img\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . lg_discuss . "\" title=\"" . lg_discuss . "\"/></a></li>";
	} else {
		$send .= "<li class=\"comments\"><a href=\"#comments\"><img src=\"$image_url/comments_off.png\" id=\"comment_img\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . lg_discuss . "\" title=\"" . lg_discuss . "\"/></a></li>";
	}
	
	$send .= "<li class=\"print\"><a href=\"" . URL . "/print/" . $category . "/" . $article . "\"><img src=\"$image_url/print.png\" id=\"print_img\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . lg_print . "\" title=\"" . lg_print . "\" /></a></li>";
	$send .= "<li class=\"print_category\"><a href=\"" . URL . "/print_all/" . $category . "/all\"><img src=\"$image_url/print_category.png\" id=\"print_category_img\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . lg_print_category . "\" title=\"" . lg_print_category . "\" /></a></li>";
	$send .= "<li class=\"pdf\"><a href=\"" . URL . "/print_pdf/article/" . $category . "/" . $article . "\"><img src=\"$image_url/pdf.png\" id=\"pdf_img\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . lg_pdf . "\" title=\"" . lg_pdf . "\" /></a></li>";
	$send .= "<li class=\"email\"><a href=\"#\" onclick=\"emailArticle('" . $article . "');return false;\"><img src=\"$image_url/email.png\" id=\"email_img\" width=\"16\" height=\"16\" border=\"0\" alt=\"" . lg_email . "\" title=\"" . lg_email . "\" /></a></li>";
	$send .= "</ul>";
	return $send;
}


// -----------------------------------------------------------------
//	Preset function provided by Facebook for
//	decoding the cookie.

function get_facebook_cookie($app_id, $app_secret) {
	$args = array();
	parse_str(trim($_COOKIE['fbs_' . $app_id], '\\"'), $args);
	ksort($args);
	$payload = '';
	foreach ($args as $key => $value) {
		if ($key != 'sig') {
			$payload .= $key . '=' . $value;
		}
	}
	if (md5($payload . $app_secret) != $args['sig']) {
		return null;
	}
	return $args;
}


?>