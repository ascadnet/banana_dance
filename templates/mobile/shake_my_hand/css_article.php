<?php

	include "css_definitions.php";

?>


/* -- Generic -- */

.bd_normal_text {
	font-size: <?php echo $fonts_size; ?>;
	font-family: <?php echo $fonts; ?>;
	color: <?php echo $fonts_color; ?>;
	line-height: <?php echo $line_height; ?>;
}

.bd_help_icon {
	padding: 0 0 0 3px;
	vertical-align: text-top;
}


/* -- Widget Styles -- */

ul.bd_widget_ul {
	list-style: none !important;
	margin: 0;
	padding: 0;
	font-size: <?php echo $fonts_size; ?>;
	font-family: <?php echo $fonts; ?>;
}

ul.bd_widget_ul li {
	margin-bottom: 10px !important;
}

ul.bd_widget_ul li.category_name {
	font-weight: bold !important;
}

ul.bd_widget_ul li.sub_link {
	margin-bottom: 5px !important;
}

span.bg_widget_list_title {
	display: block;
	margin-bottom: 2px;
	font-weight: bold;
}

span.bd_widget_list_sub {
	font-size: <?php echo $fonts_size_sm; ?>;
}


/* --  List holding an article's stats -- */

ul#bd_article_stats {
	list-style: none !important;
	display: block;
	width: 100%;
	margin: 25px 0 25px 0;
	padding: 5px 0 5px 0;
	border-top: 1px dotted <?php echo $borders; ?>;
	font-size: <?php echo $fonts_size_sm; ?>;
	font-family: <?php echo $fonts; ?>;
	background-color: #<?php echo $box_bg; ?>;
}

ul#bd_article_stats li {
	display: inline;
	padding: 0 10px 0 0 !important;
}

/* -- Sharing code on an article. -- */

#bd_sharing {
	margin: 30px auto 10px auto;
	width: 138px;
}

/* -- Images -- */

.bd_image {
	border: 1px solid <?php echo $borders; ?>;
	margin: 10px 0 20px 0;
}

p.bd_image_caption {
	text-align: center;
	font-size: <?php echo $fonts_size_sm; ?> !important;
	margin: -16px 0 20px 0;
	padding: 0;
	font-style: italic;
}

/* -- Quoted text -- */

.bd_quoted_text {
	background-color: <?php echo $box_bg; ?>;
	line-height: <?php echo $line_height; ?>;
	-webkit-border-radius: 10px;
	-moz-border-radius: 10px;
	border-radius: 10px;
	margin: 10px 0 10px 0;
	font-style: italic;
}

p.bd_quoted_text_single {
	font-size: <?php echo $fonts_size; ?> !important;
	font-family: <?php echo $fonts; ?> !important;
	background-color: <?php echo $box_bg; ?>;
	margin: 0;
	padding: 0 30px 0 30px;
	font-style: italic;
}

.bd_quote_pad {
	padding: 20px;
}


/* -- Help Bubble -- */

.bd_help_bubble {
	display: none;
	position: absolute;
	width: 250px;
	background: url('imgs/help_bubble_back.png') top left repeat-x #e1e1e1;
	-webkit-border-radius: 10px;
	-moz-border-radius: 10px;
	border-radius: 10px;
	padding: 20px;
	font-size: <?php echo $fonts_size; ?>;
	font: <?php echo $fonts; ?>;
	border: 1px solid #d1d1d1;
	color: #000;
}


/* -- Code -- */

.bd_code {
	font-family: courier;
	font-size: <?php echo $fonts_size; ?>;
	border: 1px dotted <?php echo $borders; ?>;
	background-color: <?php echo $box_bg; ?>;
	padding: 10px;
	overflow-x: auto;
	margin: 12px 0 12px 0;
}

.bd_code_plain {
	font-family: courier;
	font-size: <?php echo $fonts_size; ?>;
	background-color: <?php echo $box_bg; ?>;
	overflow-x: auto;
	margin: 0 0 0 10px;
}

/* -- Link not found styling -- */

.bd_no_link {
	border-bottom: 1px dotted #888;
}

/* -- Standard paragraph -- */

#bd_article p {
	font-family: <?php echo $fonts; ?>;
	color: <?php echo $fonts_color; ?>;
	line-height: <?php echo $line_height; ?>;
	font-size: <?php echo $fonts_size; ?>;
}

/* -- Lists -- */

#bd_article ul {
	list-style: square outside;
	margin: 10px 0 10px 0;
	paddding: 0;
}

#bd_article li {
	padding: 0;
	margin: 0;
}

#bd_article ol {

}

#bd_article dl {
	list-style: none;
}

#bd_article dt {
	font-weight: bold;
	padding: 2px 0 2px 0;
}

#bd_article dd {
	padding: 2px 0 2px 0;
}

/* -- Section divider -- */

.bd_divider {
	border-bottom: 1px dotted <?php echo $borders; ?>;
	margin: 35px 0 15px 0;
}

/* -- Highlighted text -- */

.bd_highlighted {
	background-color: <?php echo $hover_color; ?>;
}

.bd_highlight_pad {
	padding: 20px;
}

/* --- Tables --- */

table.bd_table {
	border-left: 1px solid <?php echo $borders; ?>;
	margin: 10px 0 10px 0;
	font-family: <?php echo $fonts; ?>;
	color: <?php echo $fonts_color; ?>;
	font-size: <?php echo $fonts_size; ?>;
}

table.bd_table th {
	border-top: 1px solid <?php echo $borders; ?>;
	border-bottom: 2px solid <?php echo $borders; ?>;
	background-color: <?php echo $box_bg; ?>;
	padding: 5px;
	font-weight: bold;
	border-right: 1px solid <?php echo $borders; ?>;
}

table.bd_table td {
	border-bottom: 1px solid <?php echo $borders; ?>;
	padding: 5px;
	border-right: 1px solid <?php echo $borders; ?>;
}


/* --- Floats --- */

.bd_left_menu {
	float: left;
	margin: 10px 10px 0 10px;
	width: 210px;
	font-size: <?php echo $fonts_size; ?>;
	font-family: <?php echo $fonts; ?>;
	color: <?php echo $fonts_color; ?>;
	background-color: <?php echo $box_bg; ?>;
}

.bd_left_menu_pad {
	padding: 20px;
}

.bd_right_menu {
	float: right;
	margin: 10px 0 10px 10px;
	width: 210px;
	font-size: <?php echo $fonts_size; ?>;
	font-family: <?php echo $fonts; ?>;
	color: <?php echo $fonts_color; ?>;
	background-color: <?php echo $box_bg; ?>;
}

.bd_right_menu_pad {
	padding: 20px;
}


/* --- Files and attachments --- */

.bd_attachment_internal {
	line-height: 32px;
	padding: 10px 10px 10px 50px;
	margin: 20px 0 20px 0;
	border: 1px dotted <?php echo $borders; ?>;
	font-family: <?php echo $fonts; ?>;
}

.bd_attachment_external {
	line-height: 32px;
	padding: 10px 10px 10px 50px;
	margin: 20px 0 20px 0;
	border: 1px dotted <?php echo $borders; ?>;
	font-family: <?php echo $fonts; ?>;
}

.bd_dl_missing {
	background: url('imgs/icon-attention.png') 10px center no-repeat;
}

.bd_dl_icon {
	background: url('imgs/icon-download.png') 10px center no-repeat;
}

span.bd_attachment_title {
	font-size: <?php echo $fonts_size; ?>;
}

span.bd_attachment_details {
	margin-left: 20px;
	font-size: <?php echo $fonts_size_sm; ?>;
}


/* -------------- Headings and Fonts ---------------------------------------- */

h1.bd_h1 {
	font-family: <?php echo $heading_font; ?>;
	font-size: <?php echo $h1_size; ?>;
	margin: 0 0 11px 0;
	padding: 0;
	letter-spacing: -1px;
	font-weight: normal;
	color: <?php echo $heading_color; ?>;
	-moz-text-shadow: 1px 1px 0 rgba(255, 255, 255, 0.5);
	-webkit-text-shadow: 1px 1px 0 rgba(255, 255, 255, 0.5);
	text-shadow: 1px 1px 0 rgba(255, 255, 255, 0.5);
}

h2.bd_h2 {
	font-family: <?php echo $heading_font; ?>;
	font-size: <?php echo $h2_size; ?>;
	margin: 0 0 4px 0;
	padding: 0;
	letter-spacing: -1px;
	font-weight: normal;
	color: <?php echo $heading_color; ?>;
	-moz-text-shadow: 1px 1px 0 rgba(255, 255, 255, 0.5);
	-webkit-text-shadow: 1px 1px 0 rgba(255, 255, 255, 0.5);
	text-shadow: 1px 1px 0 rgba(255, 255, 255, 0.5);
}


h3.bd_h3 {
	font-family: <?php echo $heading_font; ?>;
	font-size: <?php echo $h3_size; ?>;
	margin: 0 0 4px 0;
	padding: 0;
	letter-spacing: -1px;
	font-weight: normal;
	color: <?php echo $heading_color; ?>;
	-moz-text-shadow: 1px 1px 0 rgba(255, 255, 255, 0.5);
	-webkit-text-shadow: 1px 1px 0 rgba(255, 255, 255, 0.5);
	text-shadow: 1px 1px 0 rgba(255, 255, 255, 0.5);
}

.bd_center {
	text-align: center;
}

.bd_small {
	font-size: <?php echo $fonts_size_sm; ?> !important;
	font-family: <?php echo $fonts; ?>;
	margin: 10px 0 10px 0;
	padding: 0;
}

.bd_hr {
	color: <?php echo $borders; ?>;
	background-color: <?php echo $borders; ?>;
	height: 1px;
	margin: 10px 0 10px 0;
}

.bd_attention {
	line-height: <?php echo $line_height; ?>;
	-webkit-border-radius: 10px;
	-moz-border-radius: 10px;
	border-radius: 10px;
	margin: 10px 0 10px 0;
	padding: 10px 10px 10px 50px;
	background: url('imgs/attention.png') 10px 15px no-repeat #FEFF82;
}

.bd_attention_pad {
	padding: 20px;
}
