<?php

	require "css_definitions.php";
	
?>
   
   
/* ----------------------------------------------------------
			Login/Registration
			* Required element
   ---------------------------------------------------------- */

#bd_logged_session {
	float: right;
	line-height: 32px;
	color: #666;
	font-size: <?php echo $fonts_size_small; ?>;
}


/* ----------------------------------------------------------
			Page Content
			* Required element
   ---------------------------------------------------------- */
   
/* Holds the actual content of a page. */
#primary_article_holder {
	font-size: <?php echo $fonts_size; ?>;
}

/* Used to place the breadcrumb trail. */
#breadcrumbs {
	font-size: <?php echo $fonts_size_small; ?>;
	color: <?php echo $fonts_color_secondary; ?>;
	margin-left: 226px;
}

/* Each link within the breadcrumb trail. */
#breadcrumbs a {

}

/* Sharing code on an article. */

#bd_sharing {
	margin: 30px auto 10px auto;
	width: 138px;
}



/* ----------------------------------------------------------
			Page Internal Links
   ---------------------------------------------------------- */

/* List containing all internal links */
ul.bd_headers {
	list-style-position: inside;
	list-style-type: none;
	margin-top: <?php echo $pad_less; ?>;
}
	
	/* General internal links LI settings */
	ul.bd_headers li {
		line-height: 1;
		padding: 4px 0 4px 0;
	}
	
	/* Level 1 heading */
	ul.bd_headers li.h1 {
		font-size: 100%;
	}
	
	/* Level 2 heading */
	ul.bd_headers li.h2 {
		font-size: 90%;
		padding-left: 8px;
	}
	
	/* Level 3 heading */
	ul.bd_headers li.h3 {
		font-size: 80%;
		padding-left: 16px;
	}
	
	/* Class added to the first element in the list */
	ul.bd_headers li.first {
		padding-top: <?php echo $pad; ?>;
	}
	
	/* Class added to the last element in the list */
	ul.bd_headers li.last {
		padding-bottom: <?php echo $pad; ?>;
		list-style: none;
	}
	
	/*  */
	ul.bd_headers li.hide {
		display: none;
	}
	
	/* General internal links LI settings */
	ul.bd_headers li.none {
	
	}


/* ----------------------------------------------------------
			Footnotes
   ---------------------------------------------------------- */

ol#bd_footnotes {
	border-top: <?php echo $borders; ?>;
	margin: <?php echo $pad_max; ?> 0 <?php echo $pad_more; ?> 0;
	padding: <?php echo $pad; ?> 0 <?php echo $pad; ?> 16px;
	font-size: <?php echo $fonts_size_small; ?>;
	color: <?php echo $fonts_color_secondary; ?>;
}

	ol#bd_footnotes li {
		padding: 0 0 <?php echo $pad_less; ?> 0;
		line-height: 1em;
	}

/* ----------------------------------------------------------
			Page Tags and Related Pages
   ---------------------------------------------------------- */
   
/* List of page tags */
ul#page_tags {
	list-style: square inside;
	margin-top: <?php echo $pad_less; ?>;
}

	/* Generic page tag entry */
	ul#page_tags li {
		line-height: 1;
	}

	/* No page tags found */
	ul#page_tags li.none {
	
	}

/* List of related page */
ul#related_pages {
	list-style: square inside;
	margin-top: <?php echo $pad_less; ?>;
}

	/* Generic related page entry */
	ul#related_pages li {
		line-height: 1;
	}

	/* No related pages found */
	ul#related_pages li.none {
	
	}
	

/* ----------------------------------------------------------
			Primary Navigation
   ---------------------------------------------------------- */

/* DIV element containing the Primary Navigation */
#bd_floating_area {
	border: <?php echo $borders; ?>;
	background-color: #fff;
	width: 200px;
	margin: -95px 0 0 0;
	#margin: -45px 0 0 0;
}

/* Padding within #bd_floating_area */
#bd_float_pad {
	padding-right: <?php echo $pad_more; ?>;
}

/* Actual list element that holds the links */
#bd_floating_area ul {
	list-style: none;
	margin: 0;
	padding: <?php echo $pad; ?> 0 <?php echo $pad; ?> 0;
}
	
	/* Generic LI styles for navigation */
	#bd_floating_area li {
		padding: 1px <?php echo $pad; ?> 1px <?php echo $pad; ?>;
	}
	
	/* Heading above all categories */
	#bd_floating_area li.categories_heading {
		display: none;
	}
	
	/* Category you are currently viewing/navigating */
	#bd_floating_area li.category_name {
		display: none;
	}
	
	/* Category entry */
	#bd_floating_area li.category_entry {
		font-weight: bold;
	}
	
	/* Separates categories and pages */
	#bd_floating_area li.category_separator {
		height: <?php echo $pad; ?>;
		padding: 0;
	}
	
	/* Heading above all pages */
	#bd_floating_area li.articles_heading {
		display: none;
	}
	
	/* Page within category */
	#bd_floating_area li.sub_link {
	
	}
	
	/* Currently viewing page */
	#bd_floating_area li.on {
		color: #5B7ADD;
		background-color: <?php echo $border_color; ?>;
		font-style: italic;
	}



/* ----------------------------------------------------------
			In-page Lists
   ---------------------------------------------------------- */

/* General Settings For "Nothing Found" li */

li.none {
	font-size: <?php echo $fonts_size_small; ?>;
	color: <?php echo $fonts_color_secondary; ?>;
	font-style: italic;
}

/* List with follow and favorite icons */
ul#bd_article_follow {
	list-style: none inside;
	margin: 0 8px 0 0;
	height: 60px;
}
	
	/* Generic follow list LI */
	ul#bd_article_follow li {
		float: left;
		padding: 0 0 0 5px;
		#padding: 24px 0 0 5px;
	}

	/* Link to favorite the page */
	ul#bd_article_follow li.favorites {
	
	}
	
	/* Link to follow the page */
	ul#bd_article_follow li.follows {
		margin-right: 4px;
	}
	
	/* Link to comments */
	ul#bd_article_follow li.comments {
	
	}
	
	/* Link to print the page */
	ul#bd_article_follow li.print {
	
	}
	
	/* Link to print all pages in the category */
	ul#bd_article_follow li.print_category {
	
	}
	
	/* Link to create a PDF of the page */
	ul#bd_article_follow li.pdf {
	
	}
	
	/* Link to email the page */
	ul#bd_article_follow li.email {
	
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
	list-style: none !important;
}

	ul.bd_widget_ul li {
		padding: 0px;
		margin: 0px;
	}
	
	ul.bd_widget_ul li.articles_heading,
	ul.bd_widget_ul li.categories_heading,
	ul.bd_widget_ul li.category_separator {
		display: none !important;
	}
	
	ul.bd_widget_ul li.sub_link {
		
	}
	
	ul.bd_widget_ul li.category_name {
		font-weight: bold;
	}
	
	ul.bd_widget_ul li.on {
		font-weight: bold;
	}
	

span.bg_widget_list_title {
	color: <?php echo $fonts_color_secondary; ?>;
	font-size: <?php echo $fonts_size_tiny; ?>;
	display: block;
}

span.bd_widget_list_sub {
	display: block;
	margin-bottom: <?php echo $pad; ?>;
}


/* ----- To Do List ----- */

/* List used for most widgets */
ul.bd_todo {
	list-style: none !important;
}

	/* To do list entry... */
	ul.bd_todo li {
		padding: <?php echo $pad_less; ?>;
		border-bottom: <?php echo $borders; ?>;
	}
	
	/* Complete item */
	ul.bd_todo li.complete {
		background-color: <?php echo $bg1; ?>;
		font-style: italic;
	}
	
	/* Hover Item */
	ul.bd_todo li:hover {
		background-color: <?php echo $bg1; ?>;
	}
	
	/* Incomplete item */
	ul.bd_todo li.incomplete {
		
	}
	
	
/* ----- Page Date List ----- */

/* List used for most widgets */
ul.bd_date_list {
	list-style: none !important;
}

	/* Article List within larger list */
	ul.bd_date_list ul {
		margin: 0;
		padding: <?php echo $pad_less; ?> 0 <?php echo $pad_less; ?> 0;
	}
	
	/* Article Entry in the list */
	ul.bd_date_list ul li {
		padding: <?php echo $pad_less; ?> 0 <?php echo $pad_less; ?> 0;
	}
	
	/* Date Year Headings in the list */
	ul.bd_date_list li.date_title {
		border-bottom: <?php echo $borders; ?>;
	}
	
/* ----- Tag List ----- */

/* List used for most widgets */
ul.bd_tags {
	list-style: none !important;
}

	/* Font size will be automatically
	   set by the program. */
	ul.bd_tags li {
		display:-moz-inline-stack;
		display:inline-block;
		zoom:1;
		*display:inline;
		padding: <?php echo $pad_less; ?>;
		margin: 0 <?php echo $pad_less; ?> <?php echo $pad_less; ?> 0;
	}


/* ----- Galleries ----- */

/* List used to generate galleries
   The program auto generates the correct widths
   for gallery LI elements. */
   
#primary_article_holder ul.img_gallery {
	list-style: none !important;
	border: 1px solid red !imporant;
	margin: <?php echo $pad_more; ?> 0 <?php echo $pad_more; ?> 0 !imporant;
	padding: 0 !imporant;
}

	ul.img_gallery li {
		
	}


/* Applied to each image in a gallery */
.bd_image {
   	display: block;
   	margin: 0 auto 0 auto;
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
	list-style: none;
	font-size: <?php echo $fonts_size_small; ?>;
	margin: 0;
}

	ul#bd_statusTypes li {
		display: inline-block;
		margin-left: <?php echo $pad_less; ?>;
		padding: <?php echo $pad_less; ?> <?php echo $pad; ?> <?php echo $pad_less; ?> <?php echo $pad; ?>;
		border-bottom: <?php echo $borders; ?>;
	}

	/* Status type current being viewed */
	ul#bd_statusTypes li.on,
	ul#bd_statusTypes li:hover {
		font-weight: bold;
		background-color: <?php echo $border_color; ?>;
	}
	

/* ----- Comments + Related ----- */

/* Overall comments holder, nothing else. */
#bd_all_comments {
	border-top: 3px solid <?php echo $border_color; ?>;
}

/* No comments for this page. */
#bd_no_comments {
	font-size: <?php echo $fonts_size_small; ?>;
	color: <?php echo $fonts_color_secondary; ?>;
	font-style: italic;
	margin-top: <?php echo $pad_more; ?>;
}

/* ----- Discussion Section ----- */

/* DIV element holding the textarea */
#bd_comment_box {
	margin-top: <?php echo $pad_more; ?>;
}

	/* Textarea field to post a comment. */
	textarea#commentText {
		height: 150px;
		width: 100%;
	}
	
	/* Submit button to post a comment. */
	#commentSubmit {
		float: right;
		margin-top: 4px;
	}

	/* List of formatting options for the comment */
	#commentFormatting {
		width: 100px;
		margin-top: 4px;
	}



	/* ----- Individual Comments: All Types ----- */

	/* Holds an individual comment's content */
	.bd_comment {
		min-height: 63px;
		padding: <?php echo $pad; ?> <?php echo $pad_less; ?> <?php echo $pad; ?> <?php echo $pad_less; ?>;
	}
	
	/* Padding within individual comment box. */
	.bd_comment_pad {
		padding: <?php echo $pad; ?>;
	}
	
	/* Comment that is below threashold for display.
	   Needs to be clicked to be expanded. */
	.bd_comment_hidden {
		background-color: <?php echo $bg1; ?>;
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
		width: 60px;
		float: left;
		text-align: center;
   		margin-right: <?php echo $pad; ?>;
	}
	
   	/* Upvote comment */
   	.bd_comment_up {
   		margin-bottom: <?php echo $pad_less; ?>;
   		float: left;
   		margin-right: <?php echo $pad; ?>;
   	}
   	
   	/* Voted! */
   	.voted {
   		background-color: <?php echo $hover_color; ?>;
   	}
   	
   	/* Applied to the up/down vote images */
   	img.vote_icon {
   		vertical-align: middle;
   	}
   		
   	/* Current Comment Score */
   	.bd_comment_total {
   		float: left;
   		margin-right: <?php echo $pad; ?>;
   	}
   	
   	/* Downvote Comment */
   	.bd_comment_down {
   		float: left;
   		margin-right: <?php echo $pad; ?>;
   	}
   	
   	/* Comment Rating */
   	#bd_comment_rating {
   		margin-top: <?php echo $pad; ?>;
   	}
   	
	/* Right part of comment: contains comment, poster
	   date, and option links. */
	.bd_comment_right {
		margin-left: <?php $math = 60 + rtrim($pad,'px'); echo $math . 'px'; ?>;
	}
	
		/* Top section of the comment, above main comment content. */
		.bd_comment_top {
			margin-bottom: <?php echo $pad; ?>;
			color: <?php echo $fonts_color_secondary; ?>;
		}
		
		.bd_comment_top span {
			font-size: <?php echo $fonts_size_tiny; ?>
		}
		
			/* Date comment was posted on */
			span.bd_comment_date {
			
			}
			
			/* Username of comment poster */
			span.bd_comment_user {
			
			}
			
		/* Main comment content */
		.bd_comment_main {
			font-size: <?php echo $fonts_size; ?>;
			line-height: <?php echo $line_height; ?>;
		}
		
		/* Program links below comment: Edit, Reply, etc. */
		.bd_comment_options {
			font-size: <?php echo $fonts_size_tiny; ?>;
			color: <?php echo $fonts_color_secondary; ?>;
			margin-top: <?php echo $pad_less; ?>;
			padding-top: <?php echo $pad_less; ?>;
			border-top: 1px dotted <?php echo $border_color; ?>
		}
		
		/* Reply Box */
		.bd_comment_reply {
   			display: none;
   			margin-top: 5px;
		}
		
		/* Reply textarea */
		.bd_comment_reply textarea {
			
		}
		
		/* Appears when a comment has been deleted */
		.bd_deleted_comment {
			font-style: italic;
			color: <?php echo $fonts_color_secondary; ?>;
		}
	

	/* ----- Individual Comments: Tree-Style ----- */
	
	/* The container with a dotted trail that
	   makes nested comments easier to following. */
	.bd_com_overall {
		border-left: 1px dotted <?php echo $border_color; ?>;
		padding-left: <?php echo $pad_more; ?>;
		margin-left: 30px;
	}
	
	/* Holds all subcomments for a primary comment. */
	.bd_discussion_bubble {
	
	}

	/* ----- Individual Comments: Forum-Style ----- */


	/* Active thread highlight class -- */
	.bd_thread_active {
		background-color: <?php echo $hover_color; ?>;
	}

	/* Button to expand a thread. */
	.expandCommentThread {
		float: right;
		cursor: pointer;
		border: <?php echo $borders; ?>;
   		background: url('imgs/save-button-back.png') top left repeat-x #B6E368;
   		font-family: <?php echo $fonts; ?>;
   		color: #111;
   		padding: 5px 10px 5px 10px;
   		text-align: center;
	}
	
		/* Total number of replies to a thread. */
		span.bd_sc_replies {
			font-size: <?php echo $fonts_size_large; ?>;
			display: block;
			
		}
		
		/* The word "replies" above the total number. */
		span.bd_sc_replies_text {
			font-size: <?php echo $fonts_size_small; ?>;
		}

	/* Return to main comments.
	   When a user is viewing a thread, this
	   is where the link that allows them
	   to return to the other comments is. */
	#bd_subcom_return {
		float: right;
		padding: 5px;
		font-weight: bold;
		background-color: <?php echo $bg2; ?>;
		font-family: <?php echo $fonts; ?>;
		font-size: <?php echo $fonts_size; ?>;
		color: #fff;
	}
	
	#bd_subcom_return a {
		color: #fff;
	}
	
	/* */
	#postingTo {
	
	}



/* ----------------------------------------------------------
			Search Results
   ---------------------------------------------------------- */

.bd_search_result {
	margin: 0 0 <?php echo $pad_more; ?> 0;
	font-size: <?php echo $fonts_size; ?>;
}

	span.bd_result_title {
		padding: 0;
		font-size: <?php echo $fonts_size_large; ?>;
		margin: 0;
	}

	span.bd_result_breadcrumbs {
		font-size: <?php echo $fonts_size_tiny; ?>;
		color: <?php echo $fonts_color_secondary; ?>;
		padding: 0 0 <?php echo $pad_less; ?> 0;
		margin-left: <?php echo $pad_more; ?>;
	}

	span.bd_result_snippet {
		display: block;
		padding: <?php echo $pad_less; ?> 0 0 0;
		margin: 0;
		line-height: <?php echo $line_height; ?>;
	}



/* ----------------------------------------------------------
			User Profile Pages
   ---------------------------------------------------------- */

#user_panel {
	
}

#user_panel span {
	font-size: 8.5pt;
	color: <?php echo $fonts_color_secondary; ?>;
	line-height: 1;
	margin-top: 8px;
	display: inline-block;
}

.badge {
	display: inline-block;
	background-color: <?php echo $bg1; ?>;
	font-size: <?php echo $fonts_size; ?>;
	padding: <?php echo $pad_less; ?>;
	margin: <?php echo $pad_less; ?> <?php echo $pad_less; ?> 0 0;
}

#bd_pic_remove_link {
	margin-top: <?php echo $pad_less; ?>;
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
	background-color: <?php echo $bg1; ?>;
	border: <?php echo $borders; ?>;
	padding: <?php echo $pad_more; ?>;
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
	list-style: none;
	border-left: <?php echo $input_border_br; ?>;
	border-right: <?php echo $input_border_br; ?>;
	background-color: #fff;
	margin: 0;
	padding: 0;
}

ul#ajax_search li {
	cursor: pointer;
	padding: 4px 8px 4px 8px;
	font-size: <?php echo $fonts_size; ?>;
	border-top: 1px solid #f1f1f1;
	border-bottom: <?php echo $input_border_br; ?>;
}

ul#ajax_search li:hover, ul#ajax_search li.selected {
	background-color: <?php echo $hover_color; ?>;
}



/* ----------------------------------------------------------
			Activity Feeds
   ---------------------------------------------------------- */

.feed_entry {
	margin-bottom: <?php echo $pad_less; ?>;
	min-height: 60px;
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
	float: left;
	width: 60px;
	text-align: right;
}
	
.feed_info {
	margin-left: 72px;
	background-color: <?php echo $bg1; ?>;
	padding: <?php echo $pad; ?>;
	<?php echo $rounding_less; ?>
}

.feed_post {
	margin-left: 72px;
	padding: 0 <?php echo $pad; ?> <?php echo $pad; ?> <?php echo $pad; ?>;
}

p.feed_date {
	font-size: <?php echo $fonts_size_tiny; ?>;
	margin: 0 0 4px 0;
	padding: 0 0 4px 0;
	border-bottom: 1px dotted <?php echo $border_color; ?>;
}

p.feed_dets {
	font-size: <?php echo $fonts_size; ?>;
	margin: 0;
	padding: 0;
}

p.feed_options {
	float:right;
	font-size: <?php echo $fonts_size_tiny; ?>;
	margin: 0 0 0 0;
}

#areapostsubmit {
	background-color: <?php echo $bg1; ?>;
	padding:12px;
	text-align:center;
	display:none;
}