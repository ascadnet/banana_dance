
/* ----- Highlight/Attention Block ----- */

.bd_attention {
	margin: <?php echo $pad; ?> 0 <?php echo $pad; ?> 0;
	padding: <?php echo $pad; ?> <?php echo $pad; ?> <?php echo $pad; ?> 50px;
	background: url('imgs/icon-attention.png') 10px center no-repeat #FEFF82;
	<?php
	if (! empty($rounding_less)) { echo $rounding_less; }
	?>
}

.bd_attention_pad {
	padding: <?php echo $pad; ?>;
}



/* ----- Help Bubble ----- */

/*  Help Icon  */
.bd_help_icon {
	padding: 0 0 0 3px;
	vertical-align: text-top;
}

/*  Pop up help bubble  */
.bd_help_bubble {
	display: none;
	position: absolute;
	width: 250px;
	background: <?php echo $bg1; ?>;
	padding: 20px;
	font-size: <?php echo $fonts_size; ?>;
	font: <?php echo $fonts; ?>;
	border: <?php echo $borders; ?>;
	color: #000;
	<?php
	if (! empty($rounding_less)) { echo $rounding_less; }
	?>
}



/* ----- Images ----- */
.bd_image {
	border: 1px solid <?php echo $border_color; ?>;
	margin: 10px 0 20px 0;
}

/*  Image Caation  */
p.bd_image_caption {
	text-align: center;
	font-size: <?php echo $fonts_size_sm; ?> !important;
	margin: -16px 0 20px 0;
	padding: 0;
	font-style: italic;
}



/* ----- Quoted Text ----- */

.bd_quoted_text {
	font-family: <?php echo $quote_font; ?>;
	color: <?php echo $quote_color; ?>;
	font-size: <?php echo $quote_size; ?>;
	<?php 
		if ($quote_background_type == 'color') {
			echo "background-color: " . $quote_background . ";";
		} else {
			echo "background: url('" . $quote_background . "') top left;";
		}
		if ($quote_i == '1') {
			echo "font-style: italic;";
		}
		if ($quote_b == '1') {
			echo "font-weight: bold;";
		}
		if ($quote_u == '1') {
			echo "text-decoration: underline;";
		}
		if (! empty($quote_border)) {
			echo "border: $quote_border\n";
		}
	?>
	line-height: <?php echo $quote_line_height; ?>;
	margin: <?php echo $quote_margin; ?>;
	<?php
	if (! empty($rounding)) { echo $rounding; }
	?>
	padding: <?php echo $quote_padding; ?>;
}



/* ----- Code Blocks ----- */

.bd_code {
	font-family: courier;
	font-size: <?php echo $fonts_size; ?>;
	border: 1px dotted <?php echo $border_color; ?>;
	background-color: <?php echo $box_bg; ?>;
	padding: 10px;
	overflow: auto;
	margin: 12px 0 12px 0;
	<?php
	if (! empty($rounding)) { echo $rounding; }
	?>
}

.bd_code_plain {
	font-family: courier;
	font-size: <?php echo $fonts_size; ?>;
	background-color: <?php echo $box_bg; ?>;
	overflow: auto;
	margin: 0 0 0 10px;
	padding: 10px 0 10px 0;
}




/* ----- Lists ----- */
/* 	The program will apply the
	".in_page" class to all lists
	generated through the editor. */

#primary_article_holder ul.in_page {
	list-style: square outside !important;
	margin: <?php echo $pad; ?> 0 <?php echo $pad; ?> <?php echo $pad_max; ?>;
}

	#primary_article_holder ul.in_page li {
		padding: 0;
		margin: 0;
	}

#primary_article_holder ol.in_page {
	list-style: decimal outside;
	margin: <?php echo $pad; ?> 0 <?php echo $pad; ?> <?php echo $pad_max; ?>;
}

	#primary_article_holder ol.in_page li {
		padding: 0;
		margin: 0;
	}

#primary_article_holder dl.in_page {
	list-style: none;
	margin: <?php echo $pad; ?> 0 <?php echo $pad; ?> 0;
}

#primary_article_holder dt {
	font-weight: bold;
	padding: 2px 0 2px 0;
}

#primary_article_holder dd {
	padding: 2px 0 2px 0;
}



/* ----- Highlighted Text ----- */

.bd_highlighted {
	background-color: <?php echo $hover_color; ?>;
	<?php
	if (! empty($rounding)) { echo $rounding; }
	?>
}

.bd_highlight_pad {
	padding: 20px;
}


/* ----- Tables ----- */

table.bd_table {
	border-left: 1px solid <?php echo $border_color; ?>;
	margin: <?php echo $pad_more; ?> 0 <?php echo $pad_more; ?> 0;
}

table.bd_table th {
	border-top: 1px solid <?php echo $border_color; ?>;
	border-bottom: 2px solid <?php echo $border_color; ?>;
	border-right: 1px solid <?php echo $border_color; ?>;
	background-color: <?php echo $box_bg; ?>;
	padding: <?php echo $pad_less; ?>;
	font-weight: bold;
}

table.bd_table td {
	border-bottom: 1px solid <?php echo $border_color; ?>;
	border-right: 1px solid <?php echo $border_color; ?>;
	padding: <?php echo $pad_less; ?>;
}



/* ----- Floating Menus ----- */

.bd_left_menu {
	float: left;
	margin: <?php echo $pad; ?> <?php echo $pad; ?> 0 <?php echo $pad; ?>;
	width: 210px;
	font-size: <?php echo $fonts_size; ?>;
	font-family: <?php echo $fonts; ?>;
	color: <?php echo $fonts_color; ?>;
	background-color: <?php echo $box_bg; ?>;
	<?php
	if (! empty($rounding)) { echo $rounding; }
	?>
}

.bd_left_menu_pad {
	padding: 20px;
}

.bd_right_menu {
	float: right;
	margin: <?php echo $pad; ?> 0 <?php echo $pad; ?> <?php echo $pad; ?>;
	width: 210px;
	font-size: <?php echo $fonts_size; ?>;
	font-family: <?php echo $fonts; ?>;
	color: <?php echo $fonts_color; ?>;
	background-color: <?php echo $box_bg; ?>;
	<?php
	if (! empty($rounding)) { echo $rounding; }
	?>
}

.bd_right_menu_pad {
	padding: 20px;
}



/* ----- Downloadable Files ----- */

.bd_attachment_internal {
	padding: <?php echo $pad; ?> 10px <?php echo $pad; ?> 50px;
	margin: <?php echo $pad_more; ?> 0 <?php echo $pad_more; ?> 0;
	border: 1px dotted <?php echo $border_color; ?>;
	font-size: <?php echo $fonts_size; ?>;
	color: <?php echo $fonts_color; ?>;
	<?php
	if (! empty($rounding)) { echo $rounding; }
	?>
}

.bd_attachment_external {
	padding: <?php echo $pad; ?> 10px <?php echo $pad; ?> 50px;
	margin: <?php echo $pad_more; ?> 0 <?php echo $pad_more; ?> 0;
	border: 1px dotted <?php echo $border_color; ?>;
	font-size: <?php echo $fonts_size; ?>;
	color: <?php echo $fonts_color; ?>;
	<?php
	if (! empty($rounding)) { echo $rounding; }
	?>
}

.bd_dl_missing {
	background: url('imgs/icon-attention.png') 10px center no-repeat;
}

.bd_dl_icon {
	background: url('imgs/icon-download.png') 10px center no-repeat;
}

span.bd_attachment_title {
	margin-right: <?php echo $pad; ?>;
}

span.bd_attachment_details {
	margin-left: <?php echo $pad; ?>
	font-size: <?php echo $fonts_size_small; ?> !important;
	color: <?php echo $fonts_color_secondary; ?>;
}



/* ----- Miscellaneous ----- */

/*  No Link Found  */
.bd_no_link {
	border-bottom: 1px dotted #888;
}

/* Section divider */

.bd_divider {
	border-bottom: 1px dotted <?php echo $border_color; ?>;
	margin: <?php echo $pad_more; ?> 0 <?php echo $pad; ?> 0;
}

.bd_center {
	text-align: center;
}


/* HR */

.bd_hr {
	color: <?php echo $border_color; ?>;
	background-color: <?php echo $border_color; ?>;
	height: 1px;
	margin: <?php echo $pad; ?> 0 <?php echo $pad; ?> 0;
}