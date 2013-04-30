<?php

// -------------------------------------------------------------------------------
//	Outside of the Admin CP

// Website Headings
define('lg_charset','UTF-8');
define('lg_language','en');

// Basics
define('lg_yes','Yes');
define('lg_no','No');
define('lg_save','Save');
define('lg_saved','Saved!');
define('lg_deleted','Deleted!');
define('lg_sent','Sent!');
define('lg_seconds','seconds');
define('lg_minutes','minutes');
define('lg_hours','hours');
define('lg_days','days');
define('lg_months','months');
define('lg_badge','badge');
define('lg_replies','replies');
define('lg_reply','reply');
define('lg_fb_text','Connect');
define('lg_close','Close');
define('lg_home','Home');
define('lg_directory',' Directory');
define('lg_back','Back');
define('lg_loading','Loading');
define('lg_nothing','N/A');


define('lg_visible','Public');
define('lg_visible_to_users','Visible to registered users');
define('lg_not_visible','Private');
define('lg_editor_standard','Standard Editor');
define('lg_editor_wysiwyg','Visual Editor');
define('lg_email_format_text','Plain Text');
define('lg_email_format_html','HTML');

// Management Bar
define('lg_welcome','Welcome');
define('lg_cp','Control Panel');
define('lg_article','Page');
define('lg_article_edited','Page Edited');
define('lg_title','Page Title');
define('lg_category','Category');
define('lg_logout','Logout');
define('lg_favorites','Favorites');
define('lg_template','Template');

define('lg_sublinks','Links');
define('lg_subcates','Sections');

define('lg_comment','Comment');
define('lg_comment_reply','Reply');
define('lg_comment_type','Comment Type Changed');
define('lg_mention_type','Mention');
define('lg_tag_search','Tag Search');
define('lg_expand_discussion','Expand Discussion');

define('lg_search_results','Search Results');

define('lg_ban_user','Ban User');
define('lg_reclassify','Change Comment Type');
define('lg_approve','Approve');
define('lg_delete','Delete');
define('lg_new','New');
define('lg_edit','Edit');
define('lg_full_edit','Full Edit');
define('lg_quick_edit','Quick Edit');
define('lg_login_req','Log in required');
define('lg_no_permissions','You do not have the required permissions to perform this task.');

define('lg_widgets','Other');
define('lg_media_gallery','Media Library');
define('lg_file_gallery','Download Library');
define('lg_mg_upload','Upload Media');
define('lg_mg_gallery','Create Gallery');

// Media Library Errors
define('lg_ml_not_writable','File not writable. Please give the file "%file%" write permissions and try again.');
define('lg_cannot_upload','You cannot upload files.');
define('lg_cannot_upload_img','You cannot upload images.');
define('lg_upload_nofile_selected','No file selected.');
define('lg_file_not_on_server','File does not exist on server.');

// User Management
define('lg_user_manage','Member Management');
define('lg_profile_public','Public Profile');
define('lg_profile','Profile');
define('lg_manage_profile','Manage Profile');
define('lg_notices','Notices');
define('lg_profile_edit','Edit Profile');
define('lg_profile_pic','Profile Picture');
define('lg_profile_comments','Comments');
define('lg_profile_pages','Pages');
define('lg_profile_favorites','Favorites');
define('lg_no_comments_found','<p>No comments found.</p>');
define('lg_no_articles_found','<p>No pages found.</p>');
define('lg_no_favorites_found','<p>No favorites found.</p>');
define('lg_no_notices_found','<p>No notices found.</p>');
define('lg_no_badges','No badges found.');
define('lg_user_does_not_exist','User does not exist.');
define('lg_not_available','n/a');

// Comments
define('lg_comment_return','&laquo; Return to comments');
define('lg_comment_style_forum','Forum');
define('lg_comment_style_tree','Tree');
define('lg_links_blocked','<i>linking blocked</i>');
define('lg_upvote','Upvote this comment');
define('lg_downvote','Downvote this comment');

// Article Settings
define('lg_article_option_catehome','Category homepage?');
define('lg_article_option_thread_style','Comment Thread Style?');
define('lg_article_option_public','Access Controls');
define('lg_article_owner','Page Owner');
define('lg_article_option_ac','Allow commenting?');
define('lg_article_option_stats','Show statistics bar?');
define('lg_article_option_login_req','Require users be logged in to view?');
define('lg_article_option_primary_nav','Display in primary navigation?');
define('lg_article_option_email_comment','E-mail page owner when comments are posted?');
define('lg_article_option_sharing','Display sharing options?');
define('lg_article_option_inwidgets','Display in widgets?');
define('lg_article_option_login_comment','Required users be logged in to comment?');
define('lg_article_option_comment_editing','Allow comment editing?');
define('lg_article_options_max_thread','Max Comment Threading');
define('lg_article_options_hide_commments','Hide Comments Below Score');
define('lg_article_options_default_com_status','Default Comment Type to Display?');
define('lg_article_format_type','Format Type?');
define('lg_article_redirect','Redirect to URL?');
define('lg_wiki','Wiki Mode');
define('lg_cms','CMS Mode');
define('lg_article_options_meta_t','Meta Title');
define('lg_article_options_meta_d','Meta Description');
define('lg_article_options_meta_k','Meta Keywords');
define('lg_status','Status');
define('lg_article_dup_name','Identical page warning: no two pages in the same category can have the same name.');
define('lg_no_comments','There are currently no comments... be the first to post one?');
define('lg_no_tags_found','No tags found.');
define('lg_no_related_found','No related pages found.');
define('lg_no_internal_links','No internal page links.');

// System Errors
define('lg_privilieges_req','You do not have the necessary privilieges to perform this task.');
define('lg_error','Error performing task.');
define('lg_login_to_use_feature','Log in to use this feature.');
define('lg_already_voted','You have already voted.');
define('lg_cannot_vote_own_comment','You cannot vote on your own content.');
define('lg_not_enough_points','You need %required% point(s) to perform that task, but you currently only have %user_points%!');
define('lg_cannot_create_articles','You cannot create pages!');
define('lg_cannot_edit_articles','You cannot edit this page!');
define('lg_locked_cannot_edit','Editing of this page has been temporarily locked while it is being edited by another user.');
define('lg_select_article_to_edit','No article selected to edit!');
define('lg_title_required','Please input a title for this page.');
define('lg_cannot_preview_article','You cannot preview this page.');
define('lg_select_article_to_preview','No page selected to preview!');
define('lg_cannot_ban_admin','You cannot ban an administrator!');
define('lg_cannot_ban_mod','You cannot ban a moderator.');
define('lg_input_a_comment','Input a comment!');
define('lg_off_limits','This section of %username%\'s profile is off limits.');
define('lg_user_banned','User banned!');
define('lg_cannot_ban_user','You cannot ban this user.');
define('lg_cannot_del_comment','You cannot delete this comment.');
define('lg_cannot_edit_comment','You cannot edit this comment.');
define('lg_account_not_found','Account not found.');
define('lg_account_wrong_info','Login failed: Incorrect credentials.');
define('lg_incorrect_captcha','Incorrect CAPTCHA, please try again.');
define('lg_approve_comment','You are not permitted to approve comments.');
define('lg_excessing_failed_captchas','Banned due to excessive failed CAPTCHA attempts.');
define('lg_excessive_attempts_try_again','Try again in %difference%');
define('lg_slow_down','Slow down... you are moving too fast!');
define('lg_prove_you_are_human','Prove you are human.');
define('lg_incorrect_password','Incorrect password. If updating your account, please note that you must input your current password to make changes.');
define('lg_req_fields','Required fields missing: ');
define('lg_password_no_match','Passwords do not match.');
define('lg_req_disabled','Registration has been disabled.');
define('lg_pass_strength','Password not strong enough. Add numbers, letters, and symbols to increase its strength.');
define('lg_user_taken','Username taken.');
define('lg_email_taken','E-Mail address already in use.');
define('lg_invalid_email','Invalid email address submitted.');
define('lg_one_per_user','Maximum of one account per user.');
define('lg_pic_ext','Profile pictures can only be JPG or PNG files.');
define('lg_pic_too_large','Profile pictures cannot be larger than %size%.');
define('lg_select_file','Select a file to upload.');
define('lg_upload_notwritable','Directory %directory% is not writable. Set permissions or manually upload the file using FTP.');
define('lg_user_has_access','User already has access to this page!');
define('lg_dl_file_not_found','File not found!');
define('ld_dl_login_to_dl','You must be logged in to download this file.');
define('ld_dl_limit','There is a limit of %limit% download(s) per user for this file.');
define('lg_comment_approved','Comment approved!');
define('lg_no_del_base','Cannot delete the base category.');
define('lg_no_del_homepage','You cannot delete your website\'s homepage.');
define('lg_private_site','This site is private. Log in to continue.');
define('lg_private_site_title','Website Is Private');
define('lg_create_page','Create this page');

define('lg_password_recovered','Password recovered. Check your e-mail inbox for details.');
define('lg_pass_rec_title','Password Recovered');

// Page Stuff
define('lg_category_maintenance','This category is undergoing temporary maintenance. Please try again shortly.');
define('lg_category_private','Private! You do not have access to this category.');
define('lg_article_maintenance','This page is undergoing temporary maintenance. Please try again shortly.');
define('lg_article_private','Private! You do not have access to this page.');
define('lg_favorites_remove','Remove From Favorites');
define('lg_favorites_add','Add to Favorites');
define('lg_follow','Follow');
define('lg_unfollow','Unfollow');
define('lg_email','E-Mail');
define('lg_print','Print');
define('lg_print_category','Print Category');
define('lg_pdf','Save PDF');
define('lg_discuss','Discussion');
define('lg_page_not_found','Page not found!');
define('lg_creating_in','Creating page in: ');
define('lg_page_in','Page currently in: ');

// -------------------------------------------------------------------------------
// 	Admin CP

define('lg_something_required',' is required.'); // Program will automatically add the field name before.
define('lg_admin_no_write','<p>Banana Dance could not write to the primary .htaccess file, meaning that you will need to do this manually. Please replace the following line:</p><blockquote># next rule here</blockquote><p>With this:</p><blockquote>%rule%</blockquote>');
define('lg_admin_article_not_found','Page ID not found!');
define('lg_admin_task_exists','This task already exists. <a href="%link%">Click here to edit it.</a>');
define('lg_admin_nodel_admin','You cannot delete an administrator.');
define('lg_admin_nodel_yourself','You cannot delete yourself.');
define('lg_admin_nodel_primary_admin','You cannot delete the primary administrator.');
define('lg_fieldset_exists','Fieldset already exists in this location.');
define('lg_admin_rewrite_rule_needed','Your edit is complete and the changes have been saved, but the title of this page has changed. That means that some links may no longer work. Would you like to create a redirection rule to send all old links to the new page?');

?>