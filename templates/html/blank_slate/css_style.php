<?php

	require "css_definitions.php";
	
?>
   
   
/* ----------------------------------------------------------
			Login/Registration
			* Required element
   ---------------------------------------------------------- */

#bd_logged_session {

}


/* ----------------------------------------------------------
			Page Content
			* Required element
   ---------------------------------------------------------- */
   
/* Holds the actual content of a page. */
#primary_article_holder {

}

/* Used to place the breadcrumb trail. */
#breadcrumbs {

}

/* Each link within the breadcrumb trail. */
#breadcrumbs a {

}

/* Sharing code on an article. */

#bd_sharing {

}



/* ----------------------------------------------------------
			Page Internal Links
   ---------------------------------------------------------- */

/* List containing all internal links */
ul.bd_headers {

}
	
	/* General internal links LI settings */
	ul.bd_headers li {
	
	}
	
	/* Level 1 heading */
	ul.bd_headers li.h1 {
	
	}
	
	/* Level 2 heading */
	ul.bd_headers li.h2 {
	
	}
	
	/* Level 3 heading */
	ul.bd_headers li.h3 {
	
	}
	
	/* Class added to the first element in the list */
	ul.bd_headers li.first {
	
	}
	
	/* Class added to the last element in the list */
	ul.bd_headers li.last {
	
	}
	
	/*  */
	ul.bd_headers li.hide {
	
	}
	
	/* General internal links LI settings */
	ul.bd_headers li.none {
	
	}

/* ----------------------------------------------------------
			Footnotes
   ---------------------------------------------------------- */

ol#bd_footnotes {

}

	ol#bd_footnotes li {
	
	}
	
/* ----------------------------------------------------------
			Page Tags and Related Pages
   ---------------------------------------------------------- */
   
/* List of page tags */
ul#page_tags {

}

	/* Generic page tag entry */
	ul#page_tags li {
	
	}

	/* No page tags found */
	ul#page_tags li.none {
	
	}

/* List of related page */
ul#related_pages {

}

	/* Generic related page entry */
	ul#related_pages li {
	
	}

	/* No related pages found */
	ul#related_pages li.none {
	
	}
	

/* ----------------------------------------------------------
			Primary Navigation
   ---------------------------------------------------------- */

/* DIV element containing the Primary Navigation */
#bd_floating_area {

}

/* Padding within #bd_floating_area */
#bd_float_pad {

}

/* Actual list element that holds the links */
#bd_floating_area ul {

}
	
	/* Generic LI styles for navigation */
	#bd_floating_area li {
	
	}
	
	/* Heading above all categories */
	#bd_floating_area li.categories_heading {
	
	}
	
	/* Category you are currently viewing/navigating */
	#bd_floating_area li.category_name {
	
	}
	
	/* Category entry */
	#bd_floating_area li.category_entry {
	
	}
	
	/* Separates categories and pages */
	#bd_floating_area li.category_separator {
	
	}
	
	/* Heading above all pages */
	#bd_floating_area li.articles_heading {
	
	}
	
	/* Page within category */
	#bd_floating_area li.sub_link {
	
	}
	
	/* Currently viewing page */
	#bd_floating_area li.on {
	
	}



/* ----------------------------------------------------------
			In-page Lists
   ---------------------------------------------------------- */

/* General Settings For "Nothing Found" li */

li.none {

}

/* List with follow and favorite icons */
ul#bd_article_follow {
	
}
	
	/* Generic follow list LI */
	ul#bd_article_follow li {
	
	}
	
	/* Link to favorite the page */
	ul#bd_article_follow li.favorites {
	
	}
	
	/* Link to follow the page */
	ul#bd_article_follow li.follows {
	
	}

/* List with print, PDF, email icons */
ul#bd_article_links {

}
	
	/* Generic link list LI */
	ul#bd_article_links li {
	
	}
	
	/* Link to comments */
	ul#bd_article_links li.comments {
	
	}
	
	/* Link to print the page */
	ul#bd_article_links li.print {
	
	}
	
	/* Link to print all pages in the category */
	ul#bd_article_links li.print_category {
	
	}
	
	/* Link to create a PDF of the page */
	ul#bd_article_links li.pdf {
	
	}
	
	/* Link to email the page */
	ul#bd_article_links li.email {
	
	}
		

/* List with page's stats */
ul#bd_article_stats {

}

ul#bd_article_stats li {
	
}


/* ----------------------------------------------------------
			Widgets
   ---------------------------------------------------------- */

/* ----- Generic Widgets ----- */

/* List used for most widgets */
ul.bd_widget_ul {

}

	ul.bd_widget_ul li {
	
	}
	
	ul.bd_widget_ul li.articles_heading,
	ul.bd_widget_ul li.categories_heading,
	ul.bd_widget_ul li.category_separator {
	
	}
	
	ul.bd_widget_ul li.sub_link {
		
	}
	
	ul.bd_widget_ul li.category_name {
	
	}
	
	ul.bd_widget_ul li.on {
	
	}
	

span.bg_widget_list_title {

}

span.bd_widget_list_sub {

}


/* ----- Tag List ----- */

/* List used for most widgets */
ul.bd_tags {

}

	/* Font size will be automatically
	   set by the program. */
	ul.bd_tags li {
	
	}

/* ----- Galleries ----- */

/* List used to generate galleries
   The program auto generates the correct widths
   for gallery LI elements. */
   
#primary_article_holder ul.img_gallery {

}

	ul.img_gallery li {
		
	}


/* Applied to each image in a gallery */
.bd_image {
   	
}



/* ----------------------------------------------------------
			Comments
			<a name="comments"></a>
   ---------------------------------------------------------- */
   
/* ----- Overall Discussion Section ----- */

/* Overall discussion section holder */
#bd_discussion_box {

}

/* Overall comments holder + comment type UL. */
#primary_comment_holder {

}

/* ----- Comment Type List ----- */

/* List containing all comment types. */
ul#bd_statusTypes {

}

	ul#bd_statusTypes li {
	
	}

	/* Status type current being viewed */
	ul#bd_statusTypes li.on,
	ul#bd_statusTypes li:hover {
	
	}
	

/* ----- Comments + Related ----- */

/* Overall comments holder, nothing else. */
#bd_all_comments {

}

/* No comments for this page. */
#bd_no_comments {

}

/* ----- Discussion Section ----- */

/* DIV element holding the textarea */
#bd_comment_box {
	margin-top: <?php echo $pad_more; ?>;
}

	/* Textarea field to post a comment. */
	textarea#commentText {
	
	}
	
	/* Submit button to post a comment. */
	#commentSubmit {
	
	}

	/* List of formatting options for the comment */
	#commentFormatting {
	
	}



	/* ----- Individual Comments: All Types ----- */

	/* Holds an individual comment's content */
	.bd_comment {
	
	}
	
	/* Padding within individual comment box. */
	.bd_comment_pad {
	
	}
	
	/* Comment that is below threashold for display.
	   Needs to be clicked to be expanded. */
	.bd_comment_hidden {
	
	}

	/* Top level comment */
	.bd_a_main_comment {
		
	}
	
	/* Subcomment */
	.bd_a_subcomment {
		
	}
	
	/* Left part of comment: contains upvote,
	   downvote, and score. */
	.bd_comment_left {
	
	}
	
   	/* Upvote comment */
   	.bd_comment_up {
   	
   	}
   	
   	/* Voted! */
   	.voted {
   	
   	}
   	
   	/* Applied to the up/down vote images */
   	img.vote_icon {
   		
   	}
   		
   	/* Current Comment Score */
   	.bd_comment_total {
   	
   	}
   	
   	/* Downvote Comment */
   	.bd_comment_down {
   	
   	}
   	
   	/* Comment Rating */
   	#bd_comment_rating {
   	
   	}
   	
	/* Right part of comment: contains comment, poster
	   date, and option links. */
	.bd_comment_right {
	
	}
	
		/* Top section of the comment, above main comment content. */
		.bd_comment_top {
		
		}
		
		.bd_comment_top span {
			
		}
		
			/* Date comment was posted on */
			span.bd_comment_date {
			
			}
			
			/* Username of comment poster */
			span.bd_comment_user {
			
			}
			
		/* Main comment content */
		.bd_comment_main {
		
		}
		
		/* Program links below comment: Edit, Reply, etc. */
		.bd_comment_options {
			
		}
		
		/* Reply Box */
		.bd_comment_reply {
		
		}
		
		/* Reply textarea */
		.bd_comment_reply textarea {
			
		}
		
		/* Appears when a comment has been deleted */
		.bd_deleted_comment {
		
		}
	

	/* ----- Individual Comments: Tree-Style ----- */
	
	/* The container with a dotted trail that
	   makes nested comments easier to following. */
	.bd_com_overall {
	
	}
	
	/* Holds all subcomments for a primary comment. */
	.bd_discussion_bubble {
	
	}

	/* ----- Individual Comments: Forum-Style ----- */


	/* Active thread highlight class -- */
	.bd_thread_active {
	
	}

	/* Button to expand a thread. */
	.expandCommentThread {
	
	}
	
		/* Total number of replies to a thread. */
		span.bd_sc_replies {
		
			
		}
		
		/* The word "replies" above the total number. */
		span.bd_sc_replies_text {
		
		}

	/* Return to main comments.
	   When a user is viewing a thread, this
	   is where the link that allows them
	   to return to the other comments is. */
	#bd_subcom_return {
	
	}
	
	#bd_subcom_return a {
	
	}
	
	/* */
	#postingTo {
	
	}



/* ----------------------------------------------------------
			Search Results
   ---------------------------------------------------------- */

.bd_search_result {

}

	span.bd_result_title {
	
	}

	span.bd_result_breadcrumbs {
	
	}

	span.bd_result_snippet {
	
	}



/* ----------------------------------------------------------
			User Profile Pages
   ---------------------------------------------------------- */

#user_panel {
	
}

#user_panel span {
	
}

.badge {

}

#bd_pic_remove_link {
	
}

/* ----------------------------------------------------------
			Error Messages
   ---------------------------------------------------------- */

#bd_ajax_error {
	border: 1px solid #C00000;
	color: #fff;
	background: url('imgs/icon-attention.png') 10px 10px no-repeat #cc0000;
	width: 300px;
	margin-left: -150px;
	position: fixed;
	top: 50%;
	left: 50%;
	display: none;
	cursor: pointer;
}

.bd_ae_pad {
	background: url('imgs/close.png') right 10px no-repeat;
	margin-right: 15px;
	padding: 15px 25px 15px 50px;
}


/* ----------------------------------------------------------
			Pop Up Windows
   ---------------------------------------------------------- */

#bd_captcha {
	display: none;
	background-color: #f1f1f1;
	border: 1px solid #e1e1e1;
}

#close_popup {
	float: right;
	background: url('imgs/close.png') top left no-repeat;
	width: 16px;
	height: 16px;
	cursor: pointer;
}



/* ----------------------------------------------------------
			Suggest Box
   ---------------------------------------------------------- */
   
ul#suggest_box {
	border-top: 1px solid <?php echo $border_color; ?>;
	border-left: 1px solid <?php echo $border_color; ?>;
	border-right: 1px solid <?php echo $border_color; ?>;
	background-color: <?php echo $bg1; ?>;
	list-style: none;
	margin: 0;
	padding: 0;
	position: absolute;
	display: none;
	z-index: 5100;
}

ul#suggest_box li {
	padding: 3px 5px 3px 5px;
	border-top: 1px solid #fff;
	border-bottom: 1px solid #e1e1e1;
}

ul#suggest_box li:hover {
	cursor: pointer;
	background-color: #FDFFCB;
}



/* ----------------------------------------------------------
			Inline Page Editor
   ---------------------------------------------------------- */

#bd_article_inline_edit {
	position: fixed;
	z-index: 4501;
	top: 0px;
	left: 0px;
	width: 100%;
	background: url('../_imgs/editor/back_main.png') top left;
	display: none;
}

#bd_aie_editor {

}

#bd_edit_left {
	position: absolute;
	left: 21px;
	top: 0;
	width: 234px;
	color: #fff !important;
	background: url('../_imgs/editor/back_left.png') top left;
	border-left: 1px solid #3E4245;
	border-right: 1px solid #3E4245;
	border-bottom: 1px solid #3E4245;
	-moz-box-shadow: 0px 0px 10px #070707;
	-webkit-box-shadow: 0px 0px 10px #070707;
	box-shadow: 0px 0px 10px #070707;
	-moz-border-radius:0px 0px 4px 4px;
	-webkit-border-radius:0px 0px 4px 4px;
	border-radius:0px 0px 4px 4px;
}

#bd_edit_left label {
	color: #fff !important;
	font-weight: bold;
	font-size: 0.8em;
	line-height: 1em;
}

#bd_edit_left input[type=text],
#bd_edit_left select {
	width: 190px !important;
	padding: 2px !important;
}

.left_pad {
	padding: 0 12px 12px 12px;
}

#bd_edit_left h3 {
	height: 35px;
	line-height: 30px;
	width: 246px;
	padding: 0 12px 0 12px;
	background: url('../_imgs/editor/blue_section.png') top left no-repeat;
	margin: -4px 0 0 -6px;
	font-family: arial, verdana;
	font-size: 0.9em;
	font-weight: normal;
	color: #fff;
	-moz-text-shadow: 1px 1px 0 rgba(59, 122, 249, 1.0);
	-webkit-text-shadow: 1px 1px 0 rgba(59, 122, 249, 1.0);
	text-shadow: 1px 1px 0 rgba(59, 122, 249, 1.0);
	cursor: pointer;
}

#bd_edit_left h3.notop {
	margin: 6px 0 0 -6px !important;
}

#bd_edit_left ul {
	list-style: none;
	margin: 0;
	padding: 0;
}

#bd_edit_left ul li {
	margin: 2px 0 6px 0;
	padding: 0;
	color: #fff !important;
	line-height: 1.2em;
}

#bd_edit_right {
	position: absolute;
	left: 282px;
	top: 0;
}

#bd_edit_icons {
	margin: 0 0 0 0;
}

.up_down {
	float: right;
	margin: 5px 8px 0 0;
}

ul#bd_right_save {
	float: right;
	margin: 0 12px 0 0;
	list-style: none;
	width: 26px;
}

ul#bd_right_save li {
	margin-bottom: 0px;
	padding: 4px 0 0 0;
}

ul#bd_right_save li.minimize {
	margin-bottom: 12px;
	padding: 4px 0 0 0;
}

#bd_edit_right textarea {
	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
	border-radius: 4px;
}


/* ----------------------------------------------------------
			Ajax Search
   ---------------------------------------------------------- */

ul#ajax_search {
	
}

ul#ajax_search li {

}

ul#ajax_search li:hover, ul#ajax_search li.selected {

}


/* ----------------------------------------------------------
			Activity Feeds
   ---------------------------------------------------------- */

.feed_entry {

}

	.feed_newpages {
		
	}
	
	.feed_comments {
	
	}
	
	.feed_badges {
	
	}
	
	.feed_editpages {
	
	}
	
	.feed_mentions {
	
	}
	
	
.feed_img {

}
	
.feed_info {

}

.feed_post {

}

p.feed_date {

}

p.feed_dets {

}

p.feed_options {

}

#areapostsubmit {

}