<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Primary server-side file for ajax functions.
	
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

// -----------------------------------------------------------------
//	Quick edit inline option for articles.
//	Generates the edit page.

if ($_POST['action'] == "generateEditArticle") {
	if (empty($user)) {
		echo "0+++" . lg_login_to_use_feature;
		exit;
	}
	// Editing article
	if (! empty($_POST['id'])) {
		// Can user do this?
		if ($_POST['id'] == 'new') {
			if ($privileges['can_create_articles'] != "1") {
				echo "0+++" . lg_cannot_create_articles;
				exit;
			} else {
				// Defaults
				$article = $db->get_item_options($_POST['category'],'category');
   				$cate_trail = $manual->breadcrumbs($_POST['category'],'','','1');
   				$cate_trail = rtrim($cate_trail,' / ');
   				$article['name'] = $_POST['name'];
			}
		}
		// Editing page
		else {
			$article = $manual->get_article($_POST['id'],'1');
			if ($privileges['can_alter_articles'] != "1" && $article['owner'] != $user) {
				echo "0+++" . lg_cannot_edit_articles . "---" . $privileges['can_alter_articles'] . "-" . $user . '/' . $article['owner'];
				exit;
			} else {
				// Get the article
   				$cate_trail = $manual->breadcrumbs($article['category'],$article['id'],$article,'1');
			}
			// Locked?
			$check_lock = $manual->check_lock($article['id'],$user_data['id'],$article['locked'],$article['locked_to']);
			if ($check_lock == '1') {
	 			echo "0+++" . lg_locked_cannot_edit;
	 			exit;
			} else {
				$lockit = $manual->lock_page($article['id'],'1',$user_data['id']);
			}
		}
 		// Get the category
    		$category = $manual->get_category($_POST['category']);
 		// Generate the page
 		$file = PATH . "/" . ADMIN_FOLDER . "/includes/formatting_guide.php";
   		ob_start();
   		if (! empty($user_data['option_editor'])) {
   			$format_type_editor = $user_data['option_editor'];
   		} else {
   			$format_type_editor = $db->get_option('editor_type');
   		}
   		include($file);
   		$format_guide = ob_get_contents();
   		ob_end_clean();
   		// Check for stripped privileges
    		if ($privileges['is_admin'] != '1') {
 			$check_group_stripped = $manual->check_usertype_stripped(BD_CATEGORY_VIEWING,$user_data['type'],'all');
 			if ($check_group_stripped == '1') {
 				$privileges = array();
 			}
 		}
 		// Options
   		if ($privileges['can_create_articles'] == '1' || $privileges['can_alter_articles'] == '1' || $privileges['is_admin'] == '1') {
   		
   			// -------------------------------------
   			//	MAIN MENU
   			
   			if (empty($_POST['category'])) {
   				$_POST['category'] = $article['category'];
   			}
   			$categories = $manual->category_select($_POST['category']);
   			$templates = $template->list_templates('article','3',$article['template']);
   			$menu_main = "<ul>";
   			$menu_main .= "<li><label class=\"sys\">" . lg_title . "</label><input type=\"text\" class=\"sys_field\" name=\"name\" tabindex=\"1\" value=\"" . htmlspecialchars($article['name']) . "\" /></li>";
   			$menu_main .= "	<li><label class=\"sys\">" . lg_category . "</label><select name=\"category\" id=\"category\" class=\"sys_field\" style=\"width:300px;\">" . $categories . "</select></li>";
   			
	   		if ($privileges['is_admin'] == '1') {
   				$menu_main .= "	<li><label class=\"sys\">" . lg_template . "</label><select name=\"template\" id=\"template\" class=\"sys_field\" style=\"width:300px;\">" . $templates . "</select></li>";
   			}
   			
   			$menu_main .= "	<li class=\"first\"><label class=\"sys\">" . lg_article_format_type . "</label>";
 			if ($article['format_type'] == '1') {
 				$menu_main .= "<input type=\"radio\" name=\"format_type\" value=\"1\" checked=\"checked\" /> " . lg_wiki . " <input type=\"radio\" name=\"format_type\" value=\"2\" /> " . lg_cms;
 			} else {
 				$menu_main .= "<input type=\"radio\" name=\"format_type\" value=\"1\" /> " . lg_wiki . " <input type=\"radio\" name=\"format_type\" value=\"2\" checked=\"checked\" /> " . lg_cms;
 			}
   			$menu_main .= "  </li>";
   			
   			// Page Statistics
   			if ($_POST['id'] != 'new') {
		   		// Edit users with access
		   		if ($db->get_option('save_article_history') == '1') {
		   			$last_revision = $manual->get_last_revision($article['id']);
		   			if (! empty($last_revision['date'])) {
		   				$last_updated_date = $db->format_date($last_revision['date']);
		   				$last_updated_user = $last_revision['user'];
		   			} else {
		   				$last_updated_date = "n/a";
		   				$last_updated_user = "n/a";
		   			}
		   			$revision_link = " (<a href=\"" . ADMIN_URL . "/index.php?l=article_history&id=" . $article['id'] . "\">View revisions</a>)";
		   		} else {
		   			if ($article['created'] != $article['last_updated']) {
		   				$last_updated_date = $db->format_date($article['last_updated']);
		   			} else {
		   				$last_updated_date = 'n/a';
		   			}
		   			$last_updated_user = "n/a";
		   			$revision_link = "";
		   		}
		   		
	    			$menu_main .= "<li><label class=\"sys\">General Information</label><p class=\"small sys\">";
	    			$menu_main .= "	By " . $article['owner'] . " on " . $db->format_date($article['created']) . "<br />";
	    			$menu_main .= "	Last updated on " . $last_updated_date . " by $last_updated_user $revision_link.<br />";
	    			$menu_main .= "	Viewed " . $article['views'] . " time(s).<br />";
	    			$menu_main .= "	Upvotes: " . $article['upvoted'] . " / Downvotes: " . $article['downvoted'] . "";
	    			$menu_main .= "</p></li>";
	    			$menu_main .= "<li><label class=\"sys\">Re-publish Date?</label><input type=\"checkbox\" name=\"republish\" value=\"1\" /> If checked, the creation date will be reset to today.</li>";
    			}
   			
   			$menu_main .= "</ul>";
   			
   		} else {
   			$menu_main .= "<input type=\"hidden\" class=\"sys_field\" value=\"" . htmlspecialchars($article['name']) . "\" />";
   		}
   		
   		if ($privileges['is_admin'] == "1" || $privileges['can_alter_article_options'] == '1') {
   			
   			// Generate an options menu
   			$option_names = array(
   				// 'public' => lg_article_option_public,
   				// 'show_stats' => lg_article_option_stats,
   				'display_on_sidebar' => lg_article_option_primary_nav,
   				'sharing_options' => lg_article_option_sharing,
   				'in_widgets' => lg_article_option_inwidgets,
   			);
   			
   			$comment_options = array(
   				'allow_comments' => lg_article_option_ac,
   				'email_comment_posted' => lg_article_option_email_comment,
   				'login_to_comment' => lg_article_option_login_comment,
   				'allow_comment_edits' => lg_article_option_comment_editing
   			);
   			
   			// -------------------------------------
   			//	SETTINGS
   			
	   		//if ($privileges['is_admin'] == '1') {
	   			$options = "<ul id=\"bd_aie_article_options\">";
	   			$options .= "	<li class=\"first\"><label class=\"sys\">" . lg_article_option_catehome . "</label>";
	 			if ($category['home_article'] == $article['id']) {
	 				$options .= "<input type=\"radio\" name=\"category_default\" value=\"1\" checked=\"checked\" /> " . lg_yes . " <input type=\"radio\" name=\"category_default\" value=\"0\" /> " . lg_no;
	 			} else {
	 				$options .= "<input type=\"radio\" name=\"category_default\" value=\"1\" /> " . lg_yes . " <input type=\"radio\" name=\"category_default\" value=\"0\" checked=\"checked\" /> " . lg_no;
	 			}
	   			$options .= "  </li>";
	   			foreach ($option_names as $anOption => $showName) {
	   				$options .= "<li><label class=\"sys\">$showName</label>";
	   				if ($article[$anOption] == '1') {
	   					$options .= "<input type=\"radio\" name=\"$anOption\" value=\"1\" checked=\"checked\" /> " . lg_yes . " <input type=\"radio\" name=\"$anOption\" value=\"0\" /> " . lg_no;
	   				} else {
	   					$options .= "<input type=\"radio\" name=\"$anOption\" value=\"1\" /> " . lg_yes . " <input type=\"radio\" name=\"$anOption\" value=\"0\" checked=\"checked\" /> " . lg_no;
	   				}
	   				$options .= "</li>";
	   			}
	   			$options .= "</ul>";
   			//}
   			
   			
   			// -------------------------------------
   			//	COMMENTING
   			
   			
	   		//if ($privileges['is_admin'] == '1') {
	   			$comment_statuses = $manual->get_comment_statuses($article['default_comment_type_show']);
	   			$comment_menu = "<ul>";
	   			$comment_menu .= "	<li><label class=\"sys\">" . lg_article_options_default_com_status . "</label><select name=\"default_comment_type_show\" id=\"default_comment_type_show\" class=\"sys_field\" style=\"width:300px;\">" . $comment_statuses . "</select></li>";
	   			$comment_menu .= "	<li><label class=\"sys\">" . lg_article_option_thread_style . "</label>";
	   			if (empty($article['comment_thread_style'])) {
	   				$article['comment_thread_style'] = $db->get_option('thread_style');
	   			}
	 			if ($article['comment_thread_style'] == 'Tree') {
	 				$comment_menu .= "<input type=\"radio\" name=\"comment_thread_style\" value=\"Tree\" checked=\"checked\" /> " . lg_comment_style_tree . " <input type=\"radio\" name=\"comment_thread_style\" value=\"Forum\" /> " . lg_comment_style_forum;
	 			} else {
	 				$comment_menu .= "<input type=\"radio\" name=\"comment_thread_style\" value=\"Tree\" /> " . lg_comment_style_tree . " <input type=\"radio\" name=\"comment_thread_style\" value=\"Forum\" checked=\"checked\" /> " . lg_comment_style_forum;
	 			}
	   			$comment_menu .= "  </li>";
	   			foreach ($comment_options as $anOption => $showName) {
	   				$comment_menu .= "<li><label class=\"sys\">$showName</label>";
	   				if ($article[$anOption] == '1') {
	   					$comment_menu .= "<input type=\"radio\" name=\"$anOption\" value=\"1\" checked=\"checked\" /> " . lg_yes . " <input type=\"radio\" name=\"$anOption\" value=\"0\" /> " . lg_no;
	   				} else {
	   					$comment_menu .= "<input type=\"radio\" name=\"$anOption\" value=\"1\" /> " . lg_yes . " <input type=\"radio\" name=\"$anOption\" value=\"0\" checked=\"checked\" /> " . lg_no;
	   				}
	   				$comment_menu .= "</li>";
	   			}
	   			$comment_menu .= "	<li><label class=\"sys\">" . lg_article_options_max_thread . "</label> <input type=\"text\" name=\"max_threading\" maxlength=\"2\" value=\"" . $article['max_threading'] . "\" style=\"width:50px;\" class=\"sys_field\" /></li>";
	   			$comment_menu .= "	<li><label class=\"sys\">" . lg_article_options_hide_commments . "</label> <input type=\"text\" name=\"comment_hide_threshold\" maxlength=\"4\" value=\"" . $article['comment_hide_threshold'] . "\" style=\"width:50px;\" class=\"sys_field\" /></li>";
	   			$comment_menu .= "</ul>";
   			//}
   			
   			
   			// -------------------------------------
   			//	MENU 5 = ACCESS
   			
   			
	   		$access_menu = "<ul>";
	   		//if ($privileges['is_admin'] == '1') {
	   			$access_menu .= "	<li><label class=\"sys\">" . lg_article_option_login_req . "</label>";
	   			if ($article['login_to_view'] == '1') {
	   				$access_menu .= "<input type=\"radio\" name=\"login_to_view\" value=\"1\" checked=\"checked\" /> " . lg_yes . " <input type=\"radio\" name=\"login_to_view\" value=\"0\" /> " . lg_no;
	   			} else {
	   				$access_menu .= "<input type=\"radio\" name=\"login_to_view\" value=\"1\" /> " . lg_yes . " <input type=\"radio\" name=\"login_to_view\" value=\"0\" checked=\"checked\" /> " . lg_no;
	   			}
	   			$access_menu .= "</li>";
   			//}
   			
   			
	   		if ($privileges['is_admin'] != '1' && $privileges['new_articles_public'] != '1') {
	   			$access_menu .= "<li><label class=\"sys\">" . lg_status . "</label>Pages will be pending until approved.</li>";
	   		} else {
	  			$access_menu .= "	<li><label class=\"sys\">" . lg_status . "</label><select name=\"public\" id=\"public\" class=\"sys_field\" style=\"width:300px;\">";
		   			if ($article['public'] == '1') {
		   				$access_menu .= "<option value=\"1\" selected=\"selected\">Public</option>";
		   			} else {
		   				$access_menu .= "<option value=\"1\">Public</option>";
		   			}
		   			if ($article['public'] == '0') {
		   				$access_menu .= "<option value=\"0\" selected=\"selected\">Private</option>";
		   			} else {
		   				$access_menu .= "<option value=\"0\">Private</option>";
		   			}
		   			if ($article['public'] == '2') {
		   				$access_menu .= "<option value=\"2\" selected=\"selected\">Limited to specific users (set from advanced edit)</option>";
		   			} else {
		   				$access_menu .= "<option value=\"2\">Limited to specific users (set from advanced edit)</option>";
		   			}
		   			if ($article['public'] == '3') {
		   				$access_menu .= "<option value=\"3\" selected=\"selected\">Maintenance</option>";
		   			} else {
		   				$access_menu .= "<option value=\"3\">Maintenance</option>";
		   			}
	   			$access_menu .= "</select></li>";
   			}
	   		
	   		// Edit users with access
	   		if ($privileges['is_admin'] == '1') {
	   			$access_menu .= "<li class=\"small\"><a href=\"" . ADMIN_URL . "/index.php?l=article_edit&id=" . $article['id'] . "\">Click here for more advanced editing of this page's access controls</a></li>";
		   		
		   		// Edit users with access
	   			if ($_POST['id'] != 'new') {
		   			$access_menu .= "	<li><label class=\"sys\">" . lg_article_owner . "</label><input type=\"text\" name=\"owner\" maxlength=\"150\" value=\"" . $article['owner'] . "\" style=\"width:300px;\" class=\"sys_field\" /></li>";
	   			}
		   			
	   			$access_menu .= "	<li><label class=\"sys\">" . lg_article_redirect . "</label><input type=\"text\" name=\"redirect\" maxlength=\"150\" value=\"" . $article['redirect'] . "\" style=\"width:300px;\" class=\"sys_field\" /></li>";
	   			$access_menu .= "</ul>";
	   		}
   			
   			
   			// -------------------------------------
   			//	META
   			
	   		//if ($privileges['is_admin'] == '1') {
	   			$meta_menu = "<ul>";
	   			$meta_menu .= "	<li><label class=\"sys\">" . lg_article_options_meta_t . "</label><input type=\"text\" name=\"meta_title\" maxlength=\"50\" value=\"" . $article['meta_title'] . "\" style=\"width:300px;\" class=\"sys_field\" /></li>";
	   			$meta_menu .= "	<li><label class=\"sys\">" . lg_article_options_meta_d . "</label><input type=\"text\" name=\"meta_desc\" maxlength=\"255\" value=\"" . $article['meta_desc'] . "\" style=\"width:300px;\" class=\"sys_field\" /></li>";
	   			$meta_menu .= "	<li><label class=\"sys\">" . lg_article_options_meta_k . "</label><input type=\"text\" name=\"meta_keywords\" maxlength=\"150\" value=\"" . $article['meta_keywords'] . "\" style=\"width:300px;\" class=\"sys_field\" /></li>";
	   			$meta_menu .= "</ul>";
   			//}
			
   		} else {
   			$options = "";
   			$menu2 = "";
   			$admin_edit = "";
   		}
   		
   		// What type of editor are we using?
   		if (! empty($user_data['option_editor'])) {
   			$final_editor = $user_data['option_editor'];
   		} else {
   			$final_editor = $db->get_option('editor_type');
   		}
   		
   		$editor = '';
   		$user_owner_not_admin = '0';
   		if ($privileges['is_admin'] != "1" && $privileges['can_create_articles'] != '1' && $privileges['can_alter_articles'] != '1') {
   			if ($final_editor == 'WYSIWYG') {
 	   			$editor .= '<input type="hidden" name="format_type" value="2" />';
 	   		}
 	   		$editor .= '<script type="text/javascript" src="' . URL . '/js/cleditor/jquery.cleditor.js"></script>';
 	   		$editor .= '<script type="text/javascript" src="' . URL . '/js/cleditor/jquery.cleditor.table.js"></script>';
   		}

   		// Editor adjustments
   		$editor .= '<script type="text/javascript">
   				$(document).ready(function() {
   				width = $(window).width();
   				height = $(window).height();
   				adjust_height = height - 39 - 45;
   				adjust_width = width - 282 - 40;
   				
   				$("#bd_aie_container").css("height",adjust_height);
   				$("#bd_aie_container").css("width",adjust_width);
   				$("#bd_aie_container").css("margin-top","12px");
   				
   				$("#bd_right_save").css("margin","12px auto 0 auto");';
   				
   		if ($final_editor == 'WYSIWYG') {
   			$editor .= '$("#content").cleditor({width:adjust_width, height:adjust_height})[0].focus();';
   		} else {
   			$editor .= '
      			$("textarea#content").css("padding","4px !important");
   			';
   		}
   		$editor .= '});
   			</script>';
   		
   		// echo "0+++<textarea rows=100 cols=100>$editor</textarea>";
   		
   		// Special Considerations
 		if ($_POST['id'] == 'new') {
 			if (! empty($article['pre_populate'])) {
 				$final_content = $article['pre_populate'];
 			} else {
 				$final_content = '';
 				
 			}
			$admin_edit .= "<li class=\"option\" id=\"baie_category_in\">" . lg_creating_in . "$cate_trail</li>";
	 		$special_changes = array(
	 			'%formatting_guide%' => $format_guide,
	 			'%article_content%' => $final_content,
	 			'%editor%' => $editor,
	 			
	 			'%access_menu%' => $access_menu,
	 			'%options_menu%' => $options,
	 			'%main_menu%' => $menu_main,
	 			'%meta_menu%' => $meta_menu,
	 			'%comment_menu%' => $comment_menu,
	 		);
 			$data = $template->render_template('article_inline_add',$user,$special_changes,'1','1');
			// Complete the task
		   	$log = $db->complete_task('article_generate_new',$user,'');
 		} else {
			$admin_edit .= "<li class=\"option\" id=\"baie_category_in\">" . lg_page_in . "$cate_trail</li>";
	 		$special_changes = array(
	 			'%article_name%' => htmlspecialchars($article['name']),
	 			'%article_content%' => htmlspecialchars($article['content']),
	 			'%formatting_guide%' => $format_guide,
	 			'%editor%' => $editor,
	 			
	 			'%access_menu%' => $access_menu,
	 			'%options_menu%' => $options,
	 			'%main_menu%' => $menu_main,
	 			'%meta_menu%' => $meta_menu,
	 			'%comment_menu%' => $comment_menu,
	 		);
 			$data = $template->render_template('article_inline_edit',$user,$special_changes,'1','1','','','','1');
			// Complete the task
		   	$log = $db->complete_task('article_generate_edit',$user,$article['id']);
 		}
 		echo "1+++" . $data;
 		exit;
	}
	else {
		echo "0+++" . lg_select_article_to_edit;
		exit;
	}
}

// -----------------------------------------------------------------
//	Quick edit of article.

else if ($_POST['action'] == "editArticle") {
	if (empty($user)) {
		echo "0+++" . lg_login_to_use_feature;
		exit;
	}
	// Editing article
	if (! empty($_POST['id'])) {
		// Can user do this?
		if ($_POST['id'] == 'new') {
		   	// Begin the action
		   	$log = $db->begin_task('article_add',$user,'');
			if ($privileges['can_create_articles'] != "1") {
				echo "0+++" . lg_cannot_create_articles;
				exit;
			}
			else if (empty($_POST['name'])) {
				echo "0+++" . lg_title_required;
				exit;
			}
		} else {
		   	// Begin the action
		   	$log = $db->begin_task('article_edit',$user,$_POST['id']);
			$article = $manual->get_article($_POST['id'],'1');
			if ($privileges['can_alter_articles'] != "1" && $article['owner'] != $user) {
				echo "0+++" . lg_cannot_edit_articles;
				exit;
			}
			$check_lock = $manual->check_lock($article['id'],$user_data['id'],$article['locked'],$article['locked_to']);
			if ($check_lock == '1') {
	 			echo "0+++" . lg_locked_cannot_edit;
	 			exit;
			} else {
				$lockit = $manual->lock_page($article['id'],'1',$user_data['id']);
			}
		}
		
		$_POST['name'] = $manual->convert_link_characters($_POST['name']);
		
		if (! empty($_POST['name'])) {
			// Sanitize name
			$_POST['name'] = $manual->sanitize_name($_POST['name']);
			// Duplicate names?
			$manual->find_duplicates($_POST['name'],$_POST['id'],$_POST['category']);
		}
		
		// Get category
    		$category = $manual->get_category($_POST['category']);
		// Now create the article
   		// Go through post fields
   		if (! empty($user_data['option_editor'])) {
   			$editor_type = $user_data['option_editor'];
   		} else {
   			$editor_type = $db->get_option('editor_type');
   		}
   		
   		foreach ($_POST as $name => $value) {
   			if ($name == 'done' || $name == 'action') {
   				// Skip
   			}
   			// Main content?
   			else if ($name == 'article_content') {
		    		// Sanitize Word document characters
		    		$value = $manual->remove_ms_word_characters($value);
		    		// WYSIWYG?
		    		if ($editor_type == 'WYSIWYG') {
		    			$value = $manual->cleditor_to_html($value);
		    		}
		    		$value = str_replace('+++','&#43;&#43;&#43;',$value);
   				$insert1 .= ",`content`";
   				$insert2 .= ",'" . $db->mysql_clean($value) . "'";
   				$update_add .= ",`content`='" . $db->mysql_clean($value) . "'";
   			}
   			else if ($name == 'republish') {
   				if ($value == '1') {
	   				$update_add .= ",`created`='" . $db->current_date() . "',`last_updated`='" . $db->current_date() . "'";
   				}
   			}
   			else if ($name == 'format_type') {
				if ($editor_type == 'WYSIWYG') {
	   				$insert1 .= ",`format_type`";
	   				$insert2 .= ",'2'";
	   				$update_add .= ",`format_type`='2'";
				} else {
	   				$insert1 .= ",`$name`";
	   				$insert2 .= ",'" . $db->mysql_clean($value) . "'";
	   				$update_add .= ",`$name`='" . $db->mysql_clean($value) . "'";
				}
   			}
   			// Check if this is the new category default
   			else if ($name == 'category_default') {
   				if ($value != '1') {
   					$category = $manual->get_category($_POST['category']);
   					if ($category['home_article'] == $article['id'] && $privileges['is_admin'] == '1') {
   						$q1 = "UPDATE `" . TABLE_PREFIX . "categories` SET `home_article`='' WHERE `id`='" . $db->mysql_clean($_POST['category']) . "' LIMIT 1";
   						$update = $db->update($q1);
   					}
   				} else {
 					$make_default = '1';
   				}
   			}
   			// Compile fields.
   			else {
   				
   				// Make sure user's articles are public status
	   			if ($name == 'public' && $privileges['is_admin'] != '1' && $privileges['new_articles_public'] != '1') {
      				$value = "0";
	   			}
   			
   				$insert1 .= ",`$name`";
   				$insert2 .= ",'" . $db->mysql_clean($value) . "'";
   				$update_add .= ",`$name`='" . $db->mysql_clean($value) . "'";
   			}
   		}
   		
   		$insert1 = substr($insert1,1);
   		$insert2 = substr($insert2,1);
   		$update_add = substr($update_add,1);
   		
   		// Main article
   		if ($_POST['id'] == "new") {
   			$_POST['done'] = '1';
   			$total_pages_in_category = $manual->pages_in_category($_POST['category']);
   			$next = $total_pages_in_category['next_order'];
   			// Add the page
	   		$q = "INSERT INTO `" . TABLE_PREFIX . "articles` ($insert1,`owner`,`created`,`last_updated`,`last_updated_by`,`order`) VALUES ($insert2,'$user','" . $db->current_date() . "','" . $db->current_date() . "','$user','$next')";
	   		$insert = $db->insert($q);
	   		$final_id = $insert;
	   		// Update stats
 		  	$update = $db->update_eav('articles',"add",$user,'username','');
	   		// Prepare link
	   		$link = $manual->prepare_link($final_id,$_POST['category'],$_POST['name']);
	   		$reply = $link;
			// Complete the task
			if ($_POST['done'] == '1') {
		   		$log = $db->complete_task('article_add',$user,$final_id);
		   	}
   		}
   		
   		// Editing page
   		else {
			// Get current data
			$current_article_data = $manual->get_article($_POST['id'],'1','name,id,category');
			// Changed category or name?
			// We need a rewrite rule...
			if ($current_article_data['category'] != $_POST['category'] || $current_article_data['name'] != $_POST['name']) {
				$category_name = $manual->get_category_name_from_id($current_article_data['category']);
				$newrule = @$manual->create_rewrite_rule($category_name,$current_article_data['name'],$current_article_data['id']);
			}
   			// Make the update
	   		$q = "UPDATE `" . TABLE_PREFIX . "articles` SET $update_add,`last_updated`='" . $db->current_date() . "',`last_updated_by`='" . $user . "' WHERE `id`='" . $article['id'] . "' LIMIT 1";
	   		$update = $db->update($q);
	   		$final_id = $article['id'];
	   		$reply = lg_saved;
	   		// Update Stats
 		  	$update = $db->update_eav('articles_edited',"add",$user,'username','');
			// Complete the task
			if ($_POST['done'] == '1') {
		   		$log = $db->complete_task('article_edit',$user,$article['id']);
		   	}
   		}
   		
   		// Make default?
   		if ($make_default == '1') {
			// Base Category
			if (empty($_POST['category']) || $_POST['category'] == '0') {
				$usehm_cat = '0';
			} else {
				$usehm_cat = $_POST['category'];
			}
			$q1 = "UPDATE `" . TABLE_PREFIX . "categories` SET `home_article`='" . $final_id . "' WHERE `id`='" . $usehm_cat . "' LIMIT 1";
 			$update = $db->update($q1);
   		}
   		
   		// Cache?
   		if ($db->get_option('cache_articles') == '1') {
   			$manual->cache_article($final_id);
   		}
   		
   		if ($_POST['done'] == '1' || $_POST['id'] == 'new') {
	   		// Save tags and check for mentions
		    	$line = $manual->check_mentions($_POST['article_content'],$final_id,'',$new_data);
		    	$tags = $manual->add_tags($_POST['article_content'],$article['id'],$article['category']);
	      	// Notify Followers
	   	  	$update = $manual->notify_followers($new_data,'','article_edit');
 		  	// Unlock page
			$unlock = $manual->lock_page($article['id'],'2',$user_data['id']);
   		}
   		
   		// Save history?
   		if ($db->get_option('save_article_history') == '1' && $_POST['done'] == '1') {
	 		// ---------------------------
	    		// Twitter-style usernames
	    		// Put this here to avoid changes
	    		// in code.
	    		$new_data = $manual->get_article($final_id);
			// Add to history
   			$q = "INSERT INTO `" . TABLE_PREFIX . "articles_history` (`user`,`ip`,`article_id`,`category`,`name`,`content`,`date`) VALUE ('$user','" . $db->mysql_clean($_SERVER['REMOTE_ADDR']) . "','" . $article['id'] . "','" . $article['category'] . "','" . $db->mysql_clean($article['name']) . "','" . $db->mysql_clean($_POST['article_content']) . "','" . $db->current_date() . "')";
   			$insertA = $db->insert($q);
   		}
   		// Reply
   		echo "1+++$reply";
   		exit;
	}
	else {
		echo "0+++" . lg_select_article_to_edit;
		exit;
	}
}

// -----------------------------------------------------------------
//	Quick add of category.

else if ($_POST['action'] == "addCategory") {
	$add = $manual->create_category($_POST['name'],$_POST['category'],$options);
}


// -----------------------------------------------------------------
//	Preview article being edited

else if ($_POST['action'] == "previewArticle") {
	if (empty($user)) {
		echo "0+++" . lg_login_to_use_feature;
		exit;
	}
	// Get the article
	$article = $manual->get_article($_POST['id'],'1');
	// Editing article
	if (! empty($_POST['id'])) {
		if ($privileges['can_alter_articles'] != "1" && $article['owner'] != $user) {
			echo "0+++" . lg_privilieges_req;
			exit;
		} else {
		   	// Preview
			$article_data = $manual->format_article($article,$_POST['article_content'],$user,'','1','1','1');
			echo $article_data;
			// Complete the task
		   	$log = $db->complete_task('article_preview',$user,$article['id']);
			exit;
		}
	}
	else {
		echo "0+++" . lg_select_article_to_preview;
		exit;
	}
}


// -----------------------------------------------------------------
//	Delete a page 

else if ($_POST['action'] == "del_page") {
	if (empty($user)) {
		echo "0+++" . lg_login_to_use_feature;
		exit;
	}
	$article = $manual->get_article($_POST['id'],'1');
	if (! empty($_POST['id'])) {
		if ($privileges['is_admin'] != "1" && $article['owner'] != $user && $article['can_delete_articles'] != '1') {
			echo "0+++" . lg_privilieges_req;
			exit;
		} else {
			$del = $manual->delete_page($_POST['id']);
			echo "1+++" . lg_saved;
			exit;
		}
	}
	else {
		echo "0+++" . lg_error;
		exit;
	}
}

// -----------------------------------------------------------------
//	Delete a category 

else if ($_POST['action'] == "del_category") {
	if (empty($user)) {
		echo "0+++" . lg_login_to_use_feature;
		exit;
	}
	if (! empty($_POST['id'])) {
		if ($privileges['is_admin'] != "1" && $article['can_delete_categories'] != '1') {
			echo "0+++" . lg_privilieges_req;
			exit;
		} else {
			$del = $manual->delete_category($_POST['id']);
			echo "1+++" . lg_saved;
			exit;
		}
	}
	else {
		echo "0+++" . lg_error;
		exit;
	}
}

// -----------------------------------------------------------------
//	Make a page a category's homepage

else if ($_POST['action'] == "make_homepage") {
	if (empty($user)) {
		echo "0+++" . lg_login_to_use_feature;
		exit;
	}
	if (! empty($_POST['id'])) {
		if ($privileges['is_admin'] != "1") {
			echo "0+++" . lg_privilieges_req;
			exit;
		} else {
			$article = $manual->get_article($_POST['id'],'1','category');
			if ($article['category'] == '0') {
				$article['category'] = $manual->base_category_id();
			}
			$q = "UPDATE `" . TABLE_PREFIX . "categories` SET `home_article`='" . $db->mysql_clean($_POST['id']) . "' WHERE `id`='" . $article['category'] . "' LIMIT 1";
			$update = $db->update($q);
			echo "1+++" . lg_saved;
			exit;
		}
	}
	else {
		echo "0+++" . lg_error;
		exit;
	}
}

// -----------------------------------------------------------------
//	Ban a user
else if ($_POST['action'] == "ban_user") {
	// Owner?
	if ($privileges['can_ban'] == "1") {
		// Get the user information
		$user_info = $session->get_user_data($db->mysql_clean($_POST['username']));
		// Can he/she ban this user?
		$myType = $session->get_user_type($user);
		if ($myType['id'] == "1") {
			if ($user_info['type'] == "1") {
				echo "0+++" . lg_cannot_ban_admin;
				exit;
			}
		}
		else if ($myType['id'] == "2") {
			if ($user_info['type'] == "1") {
				echo "0+++" . lg_cannot_ban_admin;
				exit;
			}
			else if ($user_info['type'] == "2") {
				echo "0+++" . lg_cannot_ban_mod;
				exit;
			}
		}
		// Get the comment
		$comment = $manual->get_a_comment($db->mysql_clean($_POST['comment_id']));
		// Reason
		$reason = "The following comment was deemed to be in violation of our rules and regulations: ";
		$reason .= '"' . $manual->format_comment($comment['comment']) . '"';
		// Delete comments
		$q2 = "UPDATE `" . TABLE_PREFIX . "comments` SET `deleted`='" . $db->current_date() . "',`deleted_by`='" . $user . "' WHERE `user`='" . $db->mysql_clean($username) . "'";
		$update = $db->update($q2);
		// Complete the task
	   	$log = $db->complete_task('ban_user',$user,$_POST['username']);
		// Complete the ban
		$session->ban_user($reason,$_POST['username'],$ip,$email);
		// Return
		echo "1+++" . lg_user_banned;
		exit;
	} else {
		echo "0+++" . lg_cannot_ban_user;
		exit;
	}
}

// -----------------------------------------------------------------
//	Edit a Comment
else if ($_POST['action'] == "edit_comment") {
	// Comment info
	$comment = $manual->get_a_comment($db->mysql_clean($_POST['comment_id']));
	// Owner?
	if ($user == $comment['user'] || $privileges['can_alter_comments'] == "1") {
		$q = "UPDATE `" . TABLE_PREFIX . "comments` SET `comment`='" . $db->mysql_clean($_POST['comment']) . "',`edits`=(`edits`+1),`last_edited`='" . $db->current_date() . "',`edited_by`='$user' WHERE `id`='" . $db->mysql_clean($_POST['comment_id']) . "' LIMIT 1";
		$update = $db->update($q);
 		// ---------------------------
    		// Twitter-style usernames
    		// Put this here to avoid changes
    		// in code.
    		$get_comment = $manual->get_a_comment($_POST['comment_id']);
    		$line = $manual->check_mentions($_POST['comment'],$get_comment['article'],$_POST['comment_id'],'',$get_comment);
	 	// ---------------------------
	 	//	Caching?
	 	if ($db->get_option('cache_comments') == '1') {
	 		$manual->get_comments($comment['article'],'','','1');
	 	}
	   	// Complete the action
	   	$log = $db->complete_task('comment_edit',$user,$_POST['username']);
	   	// Format and return comment
	   	$formatted_comment = $manual->format_comment($_POST['comment']);
		echo "1+++" . $formatted_comment;
		exit;
	} else {
		echo "0+++" . lg_cannot_edit_comment;
		exit;
	}
}

// -----------------------------------------------------------------
//	Get Comment Types
//	Inline comment editing.

else if ($_POST['action'] == "get_comment_types") {
	if ($privileges['edit_comment_status'] == "1") {
		// This comment
		$comment = $manual->get_a_comment($_POST['comment_id'],'status,comment');
		$list = "<p>\"" . $comment['comment'] . "\"</p><p>";
		// Default status
		if ($comment['status'] == '0') {
			$list .= "<input type=\"radio\" name=\"new_cm_type\" checked=\"checked\" value=\"0\" /> " . lg_comment . "<br />";
		} else {
			$list .= "<input type=\"radio\" name=\"new_cm_type\" value=\"0\" /> " . lg_comment . "<br />";
		}
		// Loop existing
		$q = "SELECT `id`,`title`,`desc` FROM  `" . TABLE_PREFIX . "comment_statuses` ORDER BY `title` ASC";
		$results = $db->run_query($q);
		while ($row = mysql_fetch_array($results)) {
			if ($comment['status'] == $row['id']) {
				$list .= "<input type=\"radio\" name=\"new_cm_type\" checked=\"checked\" value=\"" . $row['id'] . "\" /> " . $row['title'];
			} else {
				$list .= "<input type=\"radio\" name=\"new_cm_type\" value=\"" . $row['id'] . "\" /> " . $row['title'];
			}
			if (! empty($row['desc'])) {
				$list .= "<span style=\"margin-left:15px;font-size:8.5pt;font-style:italic;\">" . $row['desc'] . "</span>";
			}
			$list .= "<br />";
		}
		$list .= "</p><center><input type=\"button\" onclick=\"sendClassify();\" value=\"" . lg_save . "\" /></center>";
		echo "1+++" . $list;
		exit;
	} else {
   		echo "0+++" . lg_privilieges_req;
   		exit;
	}
}


// -----------------------------------------------------------------
//	Change Comment Status
//	Change a comment type based on
//	an inline edit.

else if ($_POST['action'] == 'perform_classify') {
	if ($privileges['edit_comment_status'] == "1") {
		$this_comment = $manual->get_a_comment($_POST['id']);
	   	$commands = $manual->update_subcomments($_POST['id'],$_POST['status'],$this_comment['article']);
	   	// New Status?
	   	if ($_POST['status'] != $this_comment['status']) {
	   		$custom_data = array(
	   			'old_status' => $this_comment['status'],
	   			'new_status' => $_POST['status']
	   		);
	    		if (! empty($_POST['status'])) {
	   			$name_ct = "comments_status" . $_POST['status'];
	   	 		$update = $db->update_eav($name_ct,"add",$user,'username');
	    		}
	    		if (! empty($this_comment['status'])) {
	   			$name_ct_old = "comments_status" . $this_comment['status'];
	   	 		$update = $db->update_eav($name_ct_old,"subtract",$user,'username');
	    		}
	   		$custom_data = array('comment_type' => $_POST['status']);
	   		$log = $db->complete_task('comment_status_changed',$user,$_POST['id'],$custom_data);
	   	}
	   	echo "1+++" . lg_saved;
	   	exit;
   	} else {
   		echo "0+++" . lg_privilieges_req;
   		exit;
   	}

}

// -----------------------------------------------------------------
//	Get updated comments
//	Re-render comments for an article.
//	Used after inline edit of comment.

else if ($_POST['action'] == 'reget_comments') {
	$comments = $manual->get_comments($_POST['article'],$user);
	echo $comments;
	exit;
}


// -----------------------------------------------------------------
//	Delete Comment

else if ($_POST['action'] == "del_comment") {
	// Owner?
	if ($user == $comment['user'] || $privileges['can_alter_comments'] == "1" || $privileges['is_admin'] == "1") {
		// Begin task
		$log = $db->begin_task('comment_delete',$user,'');
		// Comment delete
		$manual->delete_comment($_POST['comment_id']);
	   	// Complete the action
	   	$log = $db->complete_task('comment_delete',$user,$_POST['username']);
		echo "1+++$reply";
		exit;
	} else {
		echo "0+++" . lg_cannot_del_comment;
		exit;
	}
}


// -----------------------------------------------------------------
//	Log a user in
else if ($_POST['action'] == "login") {
	// Check spam session
	$spam_session = $session->current_spam_session();
	// SALT
	$q = "SELECT `id`,`salt`,`password` FROM `" . TABLE_PREFIX . "users` WHERE `username`='" . $db->mysql_clean($_POST['username']) . "' LIMIT 1";
	$salt = $db->get_array($q);
	if (empty($salt['salt'])) {
		// Check spam session
		$check = $session->check_spam_session('login','1');
		// Continue...
   	   	if ($_POST['onsite'] == '1') {
 			$db->show_error(lg_account_not_found);
 			exit;
   	   	} else {
			echo "0+++" . lg_account_not_found . "+++" . $spam_session['req_captcha'];
			exit;
		}
	} else {
   		// Password functions
   		require PATH . "/includes/password.functions.php";
   		$password = new password;
   		$check_pass = $password->encode_password($_POST['password'],$salt['salt']);   		
   		// Password?
   		if ($check_pass == $salt['password']) {
   			// Update stats
   			$update = $db->update_eav('logins',"add",$salt['id'],'user_id','');
   			$update1 = $db->update_eav('last_login',$db->current_date(),$salt['id'],'user_id','');
   			// Begin a session
   			$start_session = $session->start_session($_POST['username'],$_POST['remember_me']);
   		   	// Complete the action
   		   	$log = $db->complete_task('login',$_POST['username'],'');
   		   	if ($_POST['onsite'] == '1') {
   				header('Location: ' . URL . '/user/' . $_POST['username']);
				exit;	   	
   		   	} else {
	   			echo "1+++" . lg_saved;
	   			exit;
   			}
   		} else {
   			// Check spam session
   			$check = $session->check_spam_session('login','2');
   			// Continue...
   		   	if ($_POST['onsite'] == '1') {
				$db->show_error(lg_account_wrong_info);
				exit;
   		   	} else {
	   			echo "0+++" . lg_account_wrong_info . "+++" . $spam_session['req_captcha'];
	   			exit;
   			}
   		}
	}
}


// -----------------------------------------------------------------
//	Get management bar post-login

else if ($_POST['action'] == "getManageBar") {
	if ($user) {
		if (! empty($_POST['article'])) {
			$article = $manual->get_article($_POST['article']);
		} else {
			$article = "";
		}
		$bar = $manual->article_sidebar($article,'1','');
		echo "$bar";
		exit;
	} else {
		echo "";
		exit;
	}
}

// -----------------------------------------------------------------
//	Check CAPTCHA

else if ($_POST['action'] == "check_captcha") {
	// Prepare the user's CAPTCHA
	$theCapt = $session->get_spam_captcha();
	$check_against = str_replace('|','',$theCapt['0']);
	// Prepare the input
	$_POST['c'] = trim($_POST['c']);
	$_POST['c'] = strtolower($_POST['c']);
	$_POST['c'] = str_replace(' ','',$_POST['c']);
	if ($_POST['c'] == $check_against) {
   		$q = "UPDATE `" . TABLE_PREFIX . "spam` SET `failed_captcha`='0',`captcha`='',`proven_captcha`='1',`last_activity`='" . time() . "' WHERE `ip`='" . $_SERVER['REMOTE_ADDR'] . "'LIMIT 1";
   		$update = $db->update($q);
		echo "0+++x+++3";
		exit;
	} else {
		if ($theCapt['1']+1 > "5") {
			// Delete the spam entry
			$del = $session->delete_spam_session();
			// Complete the ban
			$until = time()+3600;
			$reason = "Excessive failed CAPTCHA attempts.";
			$session->ban_user($reason,'',$_SERVER['REMOTE_ADDR'],'',$until,'1');
			echo "0+++" . lg_excessing_failed_captchas . "+++2";
			exit;
		} else {
			$q = "UPDATE `" . TABLE_PREFIX . "spam` SET `failed_captcha`=(`failed_captcha`+1),`last_activity`='" . time() . "' WHERE `ip`='" . $_SERVER['REMOTE_ADDR'] . "'LIMIT 1";
			$update = $db->update($q);
			echo "0+++" . lg_incorrect_captcha;
			exit;
		}
	}
	// Reply with info
	echo $return;
	exit;
}

// -----------------------------------------------------------------
//	Approve Comment
else if ($_POST['action'] == "approve_comment") {
	// Comment ID?
	if (empty($_POST['comment_id'])) {
   		echo "0+++" . lg_error;
   		exit;
	}
	// Can the user approve this?
	if ($privileges['is_admin'] == "1" || $privileges['can_alter_comments'] == "1") {
		// Begin task
	   	$log = $db->begin_task('comment_approve',$user,'');
	   	// Update
		$q = "UPDATE `" . TABLE_PREFIX . "comments` SET `pending`='0' WHERE `id`='" . $db->mysql_clean($_POST['comment_id']) . "' LIMIT 1";
		$go = $db->update($q);
		// E-Mail the article's owner
 		$comment_info = $manual->get_a_comment($_POST['comment_id']);
 		if (! empty($comment_info['subcomment'])) {
			$email_owner = $manual->comment_email_article_owner($_POST['comment_id'],$comment_info,'',$comment_info['user'],'1');
 		} else {
			$email_owner = $manual->comment_email_article_owner($_POST['comment_id'],$comment_info);
		}
		// Check mentions
    		$line = $manual->check_mentions($_POST['comment_id'],$comment_info['article'],$comment_info['id'],'',$comment_info);
	 	// ---------------------------
	 	//	Caching?
	 	if ($db->get_option('cache_comments') == '1') {
	 		$comment = $manual->get_a_comment($_POST['comment_id']);
	 		$manual->get_comments($comment_info['article'],'','','1');
	 	}
		$update = $db->update_eav('comments',"add",$user,'username','');
	   	// Complete the action
	   	$log = $db->complete_task('comment_approve',$user,'');
		echo "1+++" . lg_comment_approved;
		exit;
	} else {
   		echo "0+++" . lg_approve_comment;
   		exit;
	}
}

// -----------------------------------------------------------------
//	Lost password recovery
else if ($_POST['action'] == "lost_pass") {
	$spam_session = $session->current_spam_session();
	// Find account
	$q = "SELECT username FROM `" . TABLE_PREFIX . "users` WHERE `email`='" . $db->mysql_clean($_POST['email']) . "' LIMIT 1";
	$found = $db->get_array($q);
	if (empty($found['username'])) {
		$check = $session->check_spam_session('lost_pass','1');
		echo "0+++" . lg_account_not_found;
		exit;
	} else {
	   	$log = $db->begin_task('lost_password_recovery',$user,$_POST['username']);
		// Temp Pass
		$temp_pass = substr(md5(time()+rand(10000,99999)),0,12);
	   	// Password functions
	   	require PATH . "/includes/password.functions.php";
	   	$password = new password;
	   	$salt = $password->generate_salt($found['username']);
	   	$encode_pass = $password->encode_password($temp_pass,$salt);
	   	// Update account
	   	$q1 = "UPDATE `" . TABLE_PREFIX . "users` SET `password`='$encode_pass',`salt`='$salt' WHERE `username`='" . $found['username'] . "' LIMIT 1";
	   	$update = $db->update($q1);
   		// E-mail the user his/her password
   		$special_changes = array('%password%' => $temp_pass);
   		$sent = $template->send_template($found['username'],'password_reset',"",$special_changes);
	   	// Complete the action
	   	$log = $db->complete_task('lost_password_recovery',$user,$_POST['username']);
		// Complete process
		if ($_POST['onsite'] == '1') {
   			$db->show_error(lg_password_recovered,'0',lg_pass_rec_title);
   			exit;
		} else {
			echo "1+++" . lg_pass_rec_title;
			exit;
		}
	}
}


// -----------------------------------------------------------------
//	Log a user out
else if ($_POST['action'] == "logout") {
	// Kill the session
	$session->kill_session();
    	// Complete the action
    	$log = $db->complete_task('logout',$user,$_POST['username']);
	echo "1+++Logout complete.";
	exit;
}

// -----------------------------------------------------------------
//	Favorite an article
else if ($_POST['action'] == "favorite") {
	if (empty($user)) {
		echo "0+++" . lg_login_to_use_feature;
		exit;
	} else {
		$q1 = "SELECT `id` FROM `" . TABLE_PREFIX . "favorites` WHERE `user_id`='" . $user_data['id'] . "' AND `article`='" . $db->mysql_clean($_POST['article']) . "' LIMIT 1";
		$found = $db->get_array($q1);
		if (empty($found['id'])) {
			$q = "INSERT INTO `" . TABLE_PREFIX . "favorites` (`article`,`user_id`,`date`) VALUES ('" . $db->mysql_clean($_POST['article']) . "','" . $user_data['id'] . "','" . $db->current_date() . "')";
			$insert = $db->insert($q);
			$new_img = URL . "/templates/html/" . $theme . "/imgs/favorite_on.png";
			$update = $db->update_eav('favorited','add',$_POST['article'],'act_id','item_options','article');
			$type = "1";
		} else {
			$q = "DELETE FROM `" . TABLE_PREFIX . "favorites` WHERE `id`='" . $found['id'] . "' LIMIT 1";
			$del = $db->delete($q);
			$new_img = URL . "/templates/html/" . $theme . "/imgs/favorite_off.png";
			$update = $db->update_eav('favorited','subtract',$_POST['article'],'act_id','item_options','article');
			$type = "-1";
		}
		echo "1+++" . lg_saved . "+++" . $new_img . "+++" . $type;
		exit;
	}
}


// -----------------------------------------------------------------
//	E-Mail friend an article

else if ($_POST['action'] == "email_friend") {
	
	if (empty($_POST['email'])) {
		echo "0+++" . lg_req_fields . "email";
		exit;
	}
	if (empty($_POST['name'])) {
		echo "0+++" . lg_req_fields . "name.";
		exit;
	}
	
	$article = $manual->get_article($_POST['article'],'1');
	$article_link = $manual->prepare_link($article['id'],$article['category'],$article['name']);
	
	$user_email = $_POST['name'] . "<" . $_POST['email'] . ">";
	$changes = array(
		'%email%' => $user_email,
		'%name%' => $_POST['name'],
		'%message%' => $_POST['message'],
		'%link%' => $article_link,
		'%article_name%' => $article['name']
	);
	foreach ($_POST['friend_email'] as $email) {
		$email = trim($email);
		if (! empty($email)) {
			$changes['%friend_email%'] = $email;
   			$sent = $template->send_template($user,'email_article',$email,$changes);
		}
	}
	
	// Proceed
	echo "1+++" . lg_sent;
	exit;
	
}

// -----------------------------------------------------------------
//	Folow an article
else if ($_POST['action'] == "follow") {
	if (empty($user)) {
		echo "0+++" . lg_login_to_use_feature;
		exit;
	} else {
		$q1 = "SELECT `id` FROM `" . TABLE_PREFIX . "following` WHERE `user_id`='" . $user_data['id'] . "' AND `article`='" . $db->mysql_clean($_POST['article']) . "' LIMIT 1";
		$found = $db->get_array($q1);
		if (empty($found['id'])) {
			$q = "INSERT INTO `" . TABLE_PREFIX . "following` (`article`,`user_id`,`date`) VALUES ('" . $db->mysql_clean($_POST['article']) . "','" . $user_data['id'] . "','" . $db->current_date() . "')";
			$insert = $db->insert($q);
			$new_img = URL . "/templates/html/" . $theme . "/imgs/follow_on.png";
			$update = $db->update_eav('following','add',$_POST['article'],'act_id','item_options','article');
			$type = "1";
		} else {
			$q = "DELETE FROM `" . TABLE_PREFIX . "following` WHERE `id`='" . $found['id'] . "' LIMIT 1";
			$del = $db->delete($q);
			$new_img = URL . "/templates/html/" . $theme . "/imgs/follow_off.png";
			$update = $db->update_eav('following','subtract',$_POST['article'],'act_id','item_options','article');
			$type = "-1";
		}
		echo "1+++" . lg_saved . "+++" . $new_img . "+++" . $type;
		exit;
	}
}

// -----------------------------------------------------------------
//	Register a user
else if ($_POST['action'] == "register") {

    	$log = $db->begin_task('register',$_POST['username'],'');
    	
	// ------------------------------------------
	// Check spam session
	$spam_session = $session->current_spam_session();
	
	// ------------------------------------------
	// General registration checks
	require PATH . "/includes/password.functions.php";
	$password = new password;
	$password->registration_checks('0');
	
	// ------------------------------------------
	// Encode the password
	$salt = $password->generate_salt($_POST['username']);
	$encoded_pass = $password->encode_password($_POST['pass'],$salt);
	
	// ------------------------------------------
	// Continue
	$insert = $password->create_user($_POST['username'],$encoded_pass,$salt,$_POST['email'],$_POST['name']);
	
	// ------------------------------------------
	// Additional Information
	$add_on = "";
	$ignore = array('username','pass','pass1','name','email','action');
	foreach ($_POST as $name => $value) {
		if (! in_array($name,$ignore)) {
			// Create the field in the DB
			$q1 = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "fields` WHERE `id`='" . $db->mysql_clean($name) . "'";
			$found = $db->get_array($q1);
			if ($found['0'] <= 0) {
				$leng = strlen($value);
				if ($leng > 255) {
					$type = '2';
				} else {
					$type = '1';
				}
				$display_name = str_replace('_',' ',$name);
				$display_name = ucwords($display_name);
				// Add to fields
				$q = "INSERT INTO `" . TABLE_PREFIX . "fields` (`id`,`display_name`,`type`) VALUES ('" . $db->mysql_clean($name) . "','" . $db->mysql_clean($display_name) . "','$type')";
				$add = $db->insert($q);
				// Add to main field set
				$q1 = "INSERT INTO `" . TABLE_PREFIX . "fields_sets_comps` (`set_id`,`field_id`,`col`) VALUES ('1','" . $db->mysql_clean($name) . "','1')";
				$add = $db->insert($q1);
			}
			// Add on
			$add_on .= ",('" . $db->mysql_clean($name) . "','" . $db->mysql_clean($value) . "','$insert')";
		}
	}
	$add_on = substr($add_on,1);
	if (! empty($add_on)) {
		$q2 = "
			INSERT INTO `" . TABLE_PREFIX . "user_data` (`key`,`value`,`user_id`) VALUES
			$add_on
		";
		$insertB = $db->insert($q2);
	}
	
	// ------------------------------------------
	// Begin a session
	$start_session = $session->start_session($_POST['username']);
	
	// ------------------------------------------
	// Send Template
   	$special_changes = array(
   		'%username%' => $_POST['username'],
   		'%name%' => $_POST['name'],
   		'%email%' => $_POST['email'],
   		'%password%' => $_POST['pass']
   	);
   	$sent = $template->send_template($_POST['username'],'registration_complete',"",$special_changes);
   	
	// ------------------------------------------
   	// Add login
	$update = $db->update_eav('logins',"add",$insert,'user_id','');
	
	// ------------------------------------------
    	// Complete the action
    	$log = $db->complete_task('register',$_POST['username'],'');
    	
	// ------------------------------------------
	// Complete
   	if ($_POST['onsite'] == '1') {
   		header('Location: ' . URL . '/user/' . $_POST['username']);
   		exit; 
	} else {
		echo "1+++Account created!";
		exit;
	}
}


// -----------------------------------------------------------------
//	Post a comment

else if ($_POST['action'] == "post_comment") {
	if (empty($_POST['comment'])) {
		echo "0+++" . lg_input_a_comment;
		exit;
	} else {
		if ($db->get_option('login_to_comment') == "1" && empty($user)) {
			echo "0+++" . lg_login_to_use_feature;
			exit;
		} else {
		   	// Begin the action
		   	$log = $db->begin_task('comment_post',$user,'');
			$format_date = $db->format_date($db->current_date());
			// Can the user post links?
			if ($privileges['post_code'] != '1') {
				$_POST['comment'] = strip_tags($_POST['comment']);
				$_POST['comment'] = $db->remove_links($_POST['comment']);
			}
		   	// Instant approval?
			if ($privileges['new_comments_approved'] == "1") {
				$pending = "0";
			} else {
				$pending = "1";
			}
			// Status type?
			if (! empty($_POST['comment_id'])) {
				$comment_info = $manual->get_a_comment($_POST['comment_id']);
				$status = $comment_info['status'];
			} else {
				$status = "0";
			}
			// Add the comment
			$q1 = "INSERT INTO `" . TABLE_PREFIX . "comments` (`date`,`user`,`comment`,`article`,`pending`,`subcomment`,`status`) VALUES ('" . $db->current_date() . "','$user','" . $db->mysql_clean($_POST['comment']) . "','" . $db->mysql_clean($_POST['article']) . "','" . $pending . "','" . $db->mysql_clean($_POST['comment_id']) . "','$status')";
			$insert = $db->insert($q1);
	   		
			// ---------------------------
			// Get the comment DB information
			$row = $manual->get_a_comment($insert);
			
			// ---------------------------
	   		// Twitter-style usernames
	   		// Put this here to avoid changes
	   		// in code.
	   		$line = $manual->check_mentions($_POST['comment'],$row['article'],$insert,'',$row);
	   		
			// ---------------------------
			// Update user's stats and
			// notices + inform user
			if ($pending != '1') {
				$article = $manual->get_article($_POST['article'],'1');
				$update = $db->update_eav('comments',"add",$user,'username','');
				// ---------------------------
				//	Reply?
				if (! empty($_POST['comment_id'])) {
					$user_id = $session->get_user_id($comment_info['user']);
					// $check_un = $session->get_username_from_id($user_id);
					// Check if the user posting is the same as
					// the user who posted the comment. If it
					// isn't, notify some people...
					if ($comment_info['user'] != $user) {
						// Notice
						$notice = $session->add_notice($user_id,'comment_reply',$insert);
						// E-Mailing the comment owner
			 			$link = $manual->prepare_link($article['id'],$article['category'],$article['name']);
			 			$special_changes = array(
			 				'%original_comment%' => $manual->format_comment($comment_info['comment']),
			 				'%comment%' => $manual->format_comment($_POST['comment']),
			 				'%article%' => $article['name'],
			 				'%article_link%' => $link,
			 				'%posted_by%' => $user
			 			);
				   		$sent = $template->send_template($comment_info['user'],"comment_posted","",$special_changes);
					}
				} else {
					$user_id = $session->get_user_id($article['owner']);
					if ($article['owner'] != $user) {
						// Notice
						$notice = $session->add_notice($user_id,'comment_post',$insert);
						// E-Mailing
						$email = $manual->comment_email_article_owner($insert,$row,$article,'0');
					}
				}
		   		// Notify Followers
	 		  	$update = $manual->notify_followers($article,'','comment',$row);
			   	// Complete the action
			   	$log = $db->complete_task('comment_post',$user,$article['id'],'',$insert);
			} else {
			   	// Complete the action
			   	$log = $db->complete_task('comment_post_unapproved',$user,$article['id'],'',$insert);
			}
			
			// ---------------------------
			//	Caching?
			if ($db->get_option('cache_comments') == '1') {
				$manual->get_comments($_POST['article'],'','','1');
			}
 		  	
			// ---------------------------
			// 	Format the comment and return info
			$formatted_comment = $manual->render_comment($insert,$row,'1',$user,'0',$article);
			
			echo "1+++$formatted_comment+++$insert";
			exit;
		}
	}
}


// -----------------------------------------------------------------
//	Vote on a comment

else if ($_POST['action'] == "vote_comment") {

	// Establish some basic variables...
	$comment = $db->mysql_clean($_POST['comment']);
	$rating = $db->mysql_clean($_POST['vote']);

	// Complete the action
	$log = $db->begin_task('comment_vote',$user,'');
	
	// Can this vote proceed?
	$item_options = $db->get_item_options($comment['article'],'page');
	$can_vote = '0';
	if (empty($user) && $item_options['login_to_comment'] == "1") {
   		echo "0+++" . lg_login_to_use_feature;
   		exit;
	}
	
	// Has this user voted?
   	$change_rating = '0';
   	if (! empty($user)) {
   		// Has the user voted?
   		$q = "SELECT `rating` FROM `" . TABLE_PREFIX . "comment_ratings` WHERE `user`='$user' AND `comment`='$comment' LIMIT 1";
   		$found = $db->get_array($q);
   		if (! empty($found['rating'])) {
   			if ($rating == $found['rating']) {
		   		echo "0+++" . lg_already_voted;
		   		exit;
   			} else {
   				$change_rating = '1';
   			}
   		}
   	} else {
   		// Has the user voted?
   		$q = "SELECT `rating` FROM `" . TABLE_PREFIX . "comment_ratings` WHERE `ip`='" . $_SERVER['REMOTE_ADDR'] . "' AND `comment`='$comment' LIMIT 1";
   		$found = $db->get_array($q);
   		if (! empty($found['rating'])) {
   			if ($rating == $found['rating']) {
		   		echo "0+++" . lg_already_voted;
		   		exit;
   			} else {
   				$change_rating = '1';
   			}
   		}
   	}
   	
	// Comment information
	$comment_info = $manual->get_a_comment($comment,'user,ip');
	
	// Own comment?
	if ($comment_info['user'] == $user || $comment_info['ip'] == $_SERVER['REMOTE_ADDR']) {
    		echo "0+++" . lg_cannot_vote_own_comment;
    		exit;
	}
	
   	// Changing rating?
   	if ($change_rating == '1') {
   	
   		// Begin task
   		// Change to upvote
   		if ($rating == '1') {
			$log = $db->begin_task('comment_vote_changed_up',$user,$comment);
			
	   		$q2 = "UPDATE `" . TABLE_PREFIX . "comments` SET `up`=(`up`+1),`down`=(`down`-1) WHERE `id`='$comment' LIMIT 1";
	   		$q3 = "UPDATE `" . TABLE_PREFIX . "users` SET `upvoted`=(`upvoted`+1),`downvoted`=(`downvoted`-1) WHERE `username`='" . $comment_info['user'] . "' LIMIT 1";
	   		$update = $db->update($q2);
	   		$update1 = $db->update($q3);
	   		
			$log = $db->complete_task('comment_vote_changed_up',$user,$comment);
   		}
   		
   		// Change to downvote
   		else {
			$log = $db->begin_task('comment_vote_changed_down',$user,$comment);
			
	   		$q2 = "UPDATE `" . TABLE_PREFIX . "comments` SET `up`=(`up`-1),`down`=(`down`+1) WHERE `id`='$comment' LIMIT 1";
	   		$q3 = "UPDATE `" . TABLE_PREFIX . "users` SET `upvoted`=(`upvoted`-1),`downvoted`=(`downvoted`+1) WHERE `username`='" . $comment_info['user'] . "' LIMIT 1";
	   		$update = $db->update($q2);
	   		$update1 = $db->update($q3);
	   		
			$log = $db->complete_task('comment_vote_changed_down',$user,$comment);
   		}
	
	   	// Update this user's stats
	   	if (! empty($user)) {
			$update1 = $db->update_eav('downvotes',"add",$user,'username','');
			$update2 = $db->update_eav('upvotes',"subtract",$user,'username','');
		}
		
   		// Update rating
   		$q1 = "UPDATE `" . TABLE_PREFIX . "comment_ratings` SET `rating`='$rating' WHERE `comment`='$comment' AND (`user`='$user' OR `ip`='" . $_SERVER['REMOTE_ADDR'] . "')";
   		$update = $db->update($q1);
   		
   		// Message
   	   	$message = "Vote changed.";
   		
   	}
   	
   	// New vote
   	else {
   		
   		// New upvote
   		if ($rating == '1') {
   		
			$log = $db->begin_task('comment_vote_up',$user,'');
			
	   		$q2 = "UPDATE `" . TABLE_PREFIX . "comments` SET `up`=(`up`+1) WHERE `id`='$comment' LIMIT 1";
	   		$q3 = "UPDATE `" . TABLE_PREFIX . "users` SET `upvoted`=(`upvoted`+1) WHERE `username`='" . $comment_info['user'] . "' LIMIT 1";
	   		$update = $db->update($q2);
	   		$update1 = $db->update($q3);
	   		
			$log = $db->complete_task('comment_vote_down',$user,'');
			
		   	// Update this user's stats
		   	if (! empty($user)) {
				$update2 = $db->update_eav('upvotes',"subtract",$user,'username','');
			}
			
   		}
   		// New downvote
   		else {
   		
			$log = $db->begin_task('comment_vote_down',$user,'');
			
	   		$q2 = "UPDATE `" . TABLE_PREFIX . "comments` SET `down`=(`down`+1) WHERE `id`='$comment' LIMIT 1";
	   		$q3 = "UPDATE `" . TABLE_PREFIX . "users` SET `downvoted`=(`downvoted`+1) WHERE `username`='" . $comment_info['user'] . "' LIMIT 1";
	   		$update = $db->update($q2);
	   		$update1 = $db->update($q3);
	   		
			$log = $db->complete_task('comment_vote_down',$user,'');
			
		   	// Update this user's stats
		   	if (! empty($user)) {
				$update1 = $db->update_eav('downvotes',"add",$user,'username','');
			}
   		}
   		
   		// Insert rating
   		$q1 = "INSERT INTO `" . TABLE_PREFIX . "comment_ratings` (`comment`,`rating`,`ip`,`user`) VALUES ('$comment','$rating','" . $_SERVER['REMOTE_ADDR'] . "','$user')";
   		$insert = $db->insert($q1);
   		
   		// Message
   	   	$message = "Vote recorded";
   		
   	}

 	//	Caching?
 	if ($db->get_option('cache_comments') == '1') {
 		$manual->get_comments($comment_info['article'],'','','1');
 	}
   	
	// Complete the action
	$log = $db->complete_task('comment_vote',$user,'');
	
	// Get new comment score
   	$q3 = "SELECT SUM(up-down) FROM `" . TABLE_PREFIX . "comments` WHERE `id`='$comment' LIMIT 1";
   	$new_total = $db->get_array($q3);
   	
	// Reply with new information
   	echo "1+++" . $message . "+++" . $new_total['0'];
   	exit;
   	
}


// -----------------------------------------------------------------
//	Vate a page

else if ($_POST['action'] == 'vote_page') {

	// Establish some basic variables...
	$page = $db->mysql_clean($_POST['page']);
	$rating = $db->mysql_clean($_POST['vote']);

	// Complete the action
	$log = $db->begin_task('page_vote',$user,'');
	
	// Can this vote proceed?
	$item_options = $db->get_item_options($page,'page');
	$can_vote = '0';
	if (empty($user) && $item_options['login_to_comment'] == "1") {
   		echo "0+++" . lg_login_to_use_feature;
   		exit;
	}
	
	// Has this user voted?
   	$change_rating = '0';
   	if (! empty($user)) {
   		// Has the user voted?
   		$q = "SELECT `rating` FROM `" . TABLE_PREFIX . "page_ratings` WHERE `user`='$user' AND `page`='$page' LIMIT 1";
   		$found = $db->get_array($q);
   		if (! empty($found['rating'])) {
   			if ($rating == $found['rating']) {
		   		echo "0+++" . lg_already_voted;
		   		exit;
   			} else {
   				$change_rating = '1';
   			}
   		}
   	} else {
   		// Has the user voted?
   		$q = "SELECT `rating` FROM `" . TABLE_PREFIX . "page_ratings` WHERE `ip`='" . $_SERVER['REMOTE_ADDR'] . "' AND `page`='$page' LIMIT 1";
   		$found = $db->get_array($q);
   		if (! empty($found['rating'])) {
   			if ($rating == $found['rating']) {
		   		echo "0+++" . lg_already_voted;
		   		exit;
   			} else {
   				$change_rating = '1';
   			}
   		}
   	}
   	
	// Comment information
	$page_info = $manual->get_article($page,'0','owner','0','0','0');
	
	// Own comment?
	if ($page_info['owner'] == $user || $page_info['ip'] == $_SERVER['REMOTE_ADDR']) {
    		echo "0+++" . lg_cannot_vote_own_comment;
    		exit;
	}
	
   	// Changing rating?
   	if ($change_rating == '1') {
   	
   		// Begin task
   		// Change to upvote
   		if ($rating == '1') {
			$log = $db->begin_task('page_vote_changed_up',$user,$page);
			
	   		$q2 = "UPDATE `" . TABLE_PREFIX . "articles` SET `upvoted`=(`upvoted`+1),`downvoted`=(`downvoted`-1) WHERE `id`='$page' LIMIT 1";
	   		$q3 = "UPDATE `" . TABLE_PREFIX . "users` SET `upvoted`=(`upvoted`+1),`downvoted`=(`downvoted`-1) WHERE `username`='" . $page_info['owner'] . "' LIMIT 1";
	   		$update = $db->update($q2);
	   		$update1 = $db->update($q3);
	   		
			$log = $db->complete_task('page_vote_changed_up',$user,$page);
   		}
   		
   		// Change to downvote
   		else {
			$log = $db->begin_task('page_vote_changed_down',$user,$page);
			
	   		$q2 = "UPDATE `" . TABLE_PREFIX . "articles` SET `upvoted`=(`upvoted`-1),`downvoted`=(`downvoted`+1) WHERE `id`='$page' LIMIT 1";
	   		$q3 = "UPDATE `" . TABLE_PREFIX . "users` SET `upvoted`=(`upvoted`-1),`downvoted`=(`downvoted`+1) WHERE `username`='" . $page_info['owner'] . "' LIMIT 1";
	   		$update = $db->update($q2);
	   		$update1 = $db->update($q3);
	   		
			$log = $db->complete_task('page_vote_changed_down',$user,$page);
   		}
	
	   	// Update this user's stats
	   	if (! empty($user)) {
			$update1 = $db->update_eav('pages_downvoted',"add",$user,'username','');
			$update2 = $db->update_eav('pages_upvoted',"subtract",$user,'username','');
		}
		
   		// Update rating
   		$q1 = "UPDATE `" . TABLE_PREFIX . "page_ratings` SET `rating`='$rating' WHERE `page`='$page' AND (`user`='$user' OR `ip`='" . $_SERVER['REMOTE_ADDR'] . "')";
   		$update = $db->update($q1);
   		
   		// Message
   	   	$message = "Vote changed.";
   		
   	}
   	
   	// New vote
   	else {
   		
   		// New upvote
   		if ($rating == '1') {
   		
			$log = $db->begin_task('page_vote_up',$user,'');
			
	   		$q2 = "UPDATE `" . TABLE_PREFIX . "articles` SET `upvoted`=(`upvoted`+1) WHERE `id`='$page' LIMIT 1";
	   		$q3 = "UPDATE `" . TABLE_PREFIX . "users` SET `upvoted`=(`upvoted`+1) WHERE `username`='" . $page_info['owner'] . "' LIMIT 1";
	   		$update = $db->update($q2);
	   		$update1 = $db->update($q3);
	   		
			$log = $db->complete_task('page_vote_down',$user,'');
			
		   	// Update this user's stats
		   	if (! empty($user)) {
				$update2 = $db->update_eav('pages_upvoted',"subtract",$user,'username','');
			}
			
   		}
   		// New downvote
   		else {
   		
			$log = $db->begin_task('page_vote_down',$user,'');
			
	   		$q2 = "UPDATE `" . TABLE_PREFIX . "articles` SET `downvoted`=(`downvoted`+1) WHERE `id`='$page' LIMIT 1";
	   		$q3 = "UPDATE `" . TABLE_PREFIX . "users` SET `downvoted`=(`downvoted`+1) WHERE `username`='" . $page_info['owner'] . "' LIMIT 1";
	   		$update = $db->update($q2);
	   		$update1 = $db->update($q3);
	   		
			$log = $db->complete_task('page_vote_down',$user,'');
			
		   	// Update this user's stats
		   	if (! empty($user)) {
				$update1 = $db->update_eav('pages_downvoted',"add",$user,'username','');
			}
   		}
   		
   		// Insert rating
   		$q1 = "INSERT INTO `" . TABLE_PREFIX . "page_ratings` (`page`,`rating`,`ip`,`user`) VALUES ('$page','$rating','" . $_SERVER['REMOTE_ADDR'] . "','$user')";
   		$insert = $db->insert($q1);
   		
   		// Message
   	   	$message = "Vote recorded";
   		
   	}

 	//	Caching?
 	if ($db->get_option('cache_comments') == '1') {
 		$manual->get_comments($page_info['article'],'','','1');
 	}
   	
	// Complete the action
	$log = $db->complete_task('page_vote',$user,'');
   	
	// Reply with new information
   	echo "1+++" . $message . "+++" . $new_total['0'];
   	exit;
   	
}


// -----------------------------------------------------------------
//	Get a template

else if ($_POST['action'] == "get_template") {
	// $contents = $template->get_contents('html',$_POST['name']);
	$contents = $template->render_template($_POST['name'],$user,'','1');
	// Process user information?
	if (! empty($_POST['user'])) {
		$contents = $template->process_user($contents,$_POST['user']);
	}
 	// Complete the task
    	$log = $db->complete_task('template_get',$user,$_POST['name']);
	echo $contents;
	exit;
}

?>