<?php

	include "css_definitions.php";
	include "css_article.php";
	include "../_css/css_system.css";

?>


/* -------------- Body and Misc ---------------------------------------- */

body {
	margin: 0;
	padding: 0;
	background-color: #fff;
}

img {
	max-width: 100%;
}

div, p, ul, li {
	font-family: <?php echo $fonts; ?>;
	font-size: <?php echo $fonts_size; ?>;
}

input {
	height: 39px;
	line-height: 39px;
	font-size: <?php echo $fonts_size_lg; ?>;
	padding: 10px;
}

h1 {
	font-family: <?php echo $heading_font; ?>;
	font-size: <?php echo $h1_size; ?>;
	color: <?php echo $heading_color; ?>;
	margin: 0;
	padding: 0;
}

h2 {
	font-family: <?php echo $heading_font; ?>;
	font-size: <?php echo $h2_size; ?>;
	color: <?php echo $heading_color; ?>;
	margin: 0;
	padding: 0;
}

h3 {
	font-family: <?php echo $heading_font; ?>;
	font-size: <?php echo $h2_size; ?>;
	color: <?php echo $heading_color; ?>;
	margin: 0;
	padding: 0;
}	
	
a {
	color: blue;
}

a:hover {
	color: red;
}

.center {
	text-align: center;
}

.make_button {
	padding: 20px;
	border: 1px solid <?php echo $borders; ?>;
	background-color: <?php echo $box_bg; ?>;
}

/* -------------- Holders ---------------------------------------- */

.bd_holder {
	padding: 0 4% 0 4%;
}



/* -------------- Category Tree ---------------------------------------- */

#bd_floating_area {
	display: none;
	background-color: <?php echo $box_bg; ?>;
}

#bd_float_pad {

}

#bd_floating_area ul {
	margin: 0;
	padding: 0;
	list-style: none;
}

#bd_floating_area ul li {
	padding: 28px 3% 28px 3%;
	text-align: center;
	border-bottom: 1px solid <?php echo $borders; ?>;
	border-top: 1px solid <?php echo $box_bg_secondary; ?>;
	font-size: 32pt !important;
}

#bd_floating_area ul li.categories_heading,
#bd_floating_area ul li.category_separator,
#bd_floating_area ul li.articles_heading {
	display: none;
}


#bd_floating_area ul li.category_entry {
	font-weight: bold;
}

#bd_floating_area ul li.sub_link {
	
}



/* -------------- User Bar ---------------------------------------- */

#bg_logged_session {
	text-align: center;
	font-size: <?php echo $fonts_size_sm ?> !important;
	padding: 0;
}



/* -------------- Articles ---------------------------------------- */

p#breadcrumbs {
	padding: 20px 0 20px 0;
	margin-bottom: 30px;
	font-size: <?php echo $fonts_size_sm ?> !important;
	border-bottom: 1px solid <?php echo $borders; ?>;
}


ul.bd_headers {
	margin: 0;
	padding: 20px !important;
	list-style: none !important;
	border: 1px solid <?php echo $borders; ?>;
	background-color: <?php echo $box_bg; ?>;
	text-align: center;
}

ul.bd_headers li {
	padding: 7px 2% 7px 2% !important;
}

ul.bd_headers li.last {
	display: none;
}


#primary_article_holder {
	padding: 20px !important;
	margin: 20px 0 20px 0 !important;
	border: 1px solid <?php echo $borders; ?>;
}


ul#bd_article_follow {
	display: none;
}

ul#bd_article_links {
	display: none;
}


/* -------------- Comments ---------------------------------------- */


#bd_discussion_box {
	padding: 20px !important;
	margin: 20px 0 20px 0 !important;
	border: 1px solid <?php echo $borders; ?>;
}

#bd_all_comments {
	
}

.bd_comment {
	font-size: <?php echo $fonts_size; ?>;
	font-family: <?php echo $fonts; ?>;
	color: <?php echo $fonts_color; ?>;
	padding: 10px;
	margin: 0 0 10px 0;
}

.bd_comment_hidden {
	background-color: <?php echo $box_bg; ?>;
}

.bd_a_main_comment {

}

.bd_a_subcomment {

}

.bd_dicussions_bubble {
	border-left: 1px solid <?php echo $borders; ?>;
}

#bd_comment_box {
	margin-top: 40px;
}

#bd_comment_box {
	height: 100px;
}

ul#bd_statusTypes {
	margin: 0;
	padding: 0 4% 20px 4% !important;
	list-style: none !important;
	text-align: center;
	font-size: <?php echo $fonts_size_sm; ?> !important;
}

ul#bd_statusTypes li {
	padding: 7px 2% 7px 2% !important;
}


	/* -- Applied to primary comment when the thread is expanded -- */
	.bd_thread_active {
		background-color: <?php echo $hover_color; ?>;
	}

	.bd_comment_none {
		font-size: <?php echo $fonts_size_sm; ?>;
		font-family: <?php echo $fonts; ?>;
		color: #888;
		padding: 10px 0 25px 0;
	}

	.bd_comment_left {
		width: 35px;
		float: left;
	}
	
		.bd_comment_total {
			margin-top: 5px;
			margin-bottom: 5px;
			z-index: 20;
		}
		
		.bd_comment_up {
			color: green;
			z-index: 100;
		}
		
		.bd_comment_down {
			color: <?php echo $error_color; ?>;
			z-index: 100;
		}

			.bd_voted {
				background-color: <?php echo $fonts_color; ?>;
				-webkit-border-radius: 5px;
				-moz-border-radius: 5px;
				border-radius: 5px;
			}
	
		.bd_comment_right {
			margin-left: 55px;
		}

			.expandCommentThread {
				float: right;
				border: 1px solid #B2DF63;
				background: url('imgs/save-button-back.png') top left repeat-x #B6E368;
				font-family: <?php echo $fonts; ?>;
				color: #111;
				padding: 5px 10px 5px 10px;
				text-align: center;
			}
			
				span.bd_sc_replies {
					font-size: <?php echo $fonts_size_lg; ?>;
					display: block;
				}
				
				span.bd_sc_replies_text {
					font-size: <?php echo $fonts_size_sm; ?>;
				}


			.bd_comment_top {
				margin-bottom: 10px;
				font-size: <?php echo $fonts_size_sm; ?>;
			}
			
			.bd_comment_user {
				margin-right: 15px;
			}
			
			.bd_comment_date {
			
			}
			
			.bd_comment_main {
				margin-bottom: 10px;
				font-size: <?php echo $fonts_size; ?>;
			}

			.bd_comment_bottom {
				font-size: <?php echo $fonts_size_sm; ?>;
				margin-bottom: 20px;
			}

			/* -- Allow user to post reply to/edit comment -- */

			.bd_comment_reply {
				display: none;
				margin-top: 5px;
			}
			
			.bd_deleted_comment {
				background-color: <?php echo $box_bg; ?>;
				font-style: italic;
				font-size: <?php echo $fonts_size; ?>;
			}
			
			.bd_comment_options {
				margin-top: 5px;
				font-size: <?php echo $fonts_size_sm; ?>;
			}
			
			
.smilie {
	vertical-align: middle;
	margin: 0 4px 0 4px;
}

.bd_com_overall {
	border-left: 1px dotted <?php echo $borders; ?>;
	margin-left: 17px;
}


/* -------------- User Management ---------------------------------------- */

#user_headers {
	text-align: center;
	background-color: <?php echo $box_bg; ?>;
	border: 1px solid <?php echo $borders; ?>;
	padding: 25px;
	margin: 0 0 30px 0;
}

.user_headers_in {
	padding-top: 12px;
}

#user_headers span.divide {
	margin: 0 8px 0 8px;
}

#bd_user_panel {
	margin: 0;
	text-align: center;
}

#bd_user_panel p {
	margin: 8px 0 0 0 !important;
}

#bd_user_panel img {
	margin: 8px 0 8px 0;
}

.pad_bot {
	margin: 12px 0 38px 0;
}

/* -------------- Widgets ---------------------------------------- */

ul.bd_widget_ul {
	margin: 12px 0 20px 0;
	padding: 0;
}

ul.bd_widget_ul li {
	padding: 0 0 4px 0;
}


/* -------------- Theme Custom ---------------------------------------- */

#footer {
	text-align: center;
	padding: 50px 2% 50px 2%;
	font-size: <?php echo $fonts_size_sm ?> !important;
}

#topbar {
	width: 100%;
	background: <?php echo $box_bg; ?>;
	border-bottom: 1px solid <?php echo $borders; ?>;
	text-align: center;
	padding: 15px 2% 15px 2%;
}

#toppad {
	padding: 15px 2% 15px 2%;
}

#logo {
	font-family: <?php echo $heading_font; ?>;
	font-size: <?php echo $h1_size; ?> !important;
	padding: 15px 0 5px 0;
}

#theNav {
	margin-bottom: 20px;
}