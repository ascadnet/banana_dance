<?php

	// ----------------------------------------------------------
	//	DO NOT TOUCH!
	require "../../../generated/css.php";
	header("Content-type: text/css");
	// ----------------------------------------------------------
	
?>

html, body, div, span, applet, object, iframe,
h1, h2, h3, h4, h5, h6, p, blockquote, pre,
a, abbr, acronym, address, big, cite, code,
del, dfn, em, img, ins, kbd, q, s, samp,
small, tt, var, center,
dl, dt, dd, ol, ul, li,
fieldset, form, label, legend,
table, caption, tbody, tfoot, thead, tr, th, td,
article, aside, canvas, details, embed, 
figure, figcaption, footer, header, hgroup, 
menu, nav, output, ruby, section, summary,
time, mark, audio, video {
	margin: 0;
	padding: 0;
	border: 0;
	font-size: 100%;
	font: inherit;
	vertical-align: baseline;
}

article, aside, details, figcaption, figure, 
footer, header, hgroup, menu, nav, section {
	display: block;
}

body {
	text-rendering: optimizeLegibility;
	line-height: 1;
	<?php
		if ($background_type == 'color') {
			echo "background-color: " . $background . ";";
		} else {
			echo "background: url('" . $background . "') top left repeat-x;";
		}
	?>
	font-family: <?php echo $fonts; ?>;
	margin-bottom: 60px;
}

p, ol, ul, blockquote, table, label {
	line-height: <?php echo $line_height; ?>;
	color: <?php echo $fonts_color; ?>;
	font-size: <?php echo $fonts_size; ?>;
}

p {
	margin-bottom: 12px;
}

ul {
	<?php
	if (! empty($list_style)) {
		echo "list-style: " . $list_style . ";\n";
	}
	
	if ($list_margin != '0') {
		echo "margin: " . $list_margin . ";\n";
	}
	?>
}

ol {
	list-style: decimal inside;
	<?php
	if ($list_margin != '0') {
		echo "margin: " . $list_margin . ";\n";
	}
	?>
}

li {
	<?php
	if ($list_padding != '0') {
		echo "padding: " . $list_padding . ";\n";
	}
	if (! empty($list_border)) {
		echo "border: " . $list_border . ";\n";
	}
	?>
}

sup, sub {
	height: 0;
	line-height: 1;
	vertical-align: baseline;
	_vertical-align: bottom;
	position: relative;
	
}

sup {
	bottom: 1ex;
}

sub {
	top: .5ex;
}

blockquote, q {
	quotes: none;
	<?php
		if (! empty($quote_size)) { echo "font-size: " . $quote_size . ";\n"; }
		if (! empty($quote_color)) { echo "color: " . $quote_color . ";\n"; }
		if (! empty($quote_font)) { echo "font-family: " . $quote_font . ";\n"; }
		if (! empty($quote_margin)) { echo "margin: " . $quote_margin . ";\n"; }
		if (! empty($quote_padding)) { echo "padding: " . $quote_padding . ";\n"; }
		if (! empty($quote_border)) { echo "border: " . $quote_border . ";\n"; }
		if (! empty($quote_i)) { echo "font-style: italic;\n"; }
		if (! empty($quote_b)) { echo "font-weight: bold;\n"; }
		if (! empty($quote_u)) { echo "text-decoration: underline;\n"; }
		if ($quote_background_type == 'color') {
			echo "background-color: " . $quote_background . ";";
		} else {
			echo "background: url('" . $quote_background . "') top left repeat-x;";
		}
	?>
}

blockquote:before, blockquote:after,
q:before, q:after {
	content: '';
	content: none;
}


/* ----------------------------------------------------------
			Tables
   ---------------------------------------------------------- */
 
table {
	border-collapse: collapse;
	border-spacing: 0;
	<?php
	echo $fonts_static;
	?>
}

table th {
	font-weight: bold;
}

table td, table th {
	<?php
	if ($table_border != '0') {
		echo "border-bottom: " . $table_border_size . " solid " . $table_border_color . ";\n";
	}
	if ($table_padding != '0') {
		echo "padding: " . $table_padding . ";\n";
	}
	?>
}


/* ----------------------------------------------------------
			Form Fields
   ---------------------------------------------------------- */
   
label {
	display: block;
	padding: <?php echo $pad; ?> 0 <?php echo $pad_less; ?> 0;
}

label.left {
	display: inline-block;
	margin-right: <?php echo $pad; ?>;
	width: 125px;
}

input[type=text], input[type=password], textarea, select {
	<?php
		if (! empty($input_size)) { echo "font-size: " . $input_size . ";\n"; }
		if (! empty($input_color)) { echo "color: " . $input_color . ";\n"; }
		if (! empty($input_font)) { echo "font-family: " . $input_font . ";\n"; }
		if (! empty($input_margin)) { echo "margin: " . $input_margin . ";\n"; } else { echo "margin: 0;\n"; }
		if (! empty($input_padding)) { echo "padding: " . $input_padding . ";\n"; }
		if (! empty($input_height)) { echo "height: " . $input_height . ";\n"; }
		if (! empty($input_line_height)) { echo "line-height: " . $input_line_height . ";\n"; }
		if (! empty($input_border_br)) { echo "border-bottom: " . $input_border_br . ";\nborder-right: " . $input_border_br . ";\n"; }
		if (! empty($input_border_tl)) { echo "border-top: " . $input_border_tl . ";\nborder-left: " . $input_border_tl . ";\n"; }
		if (! empty($input_i)) { echo "font-style: italic;\n"; }
		if (! empty($input_b)) { echo "font-weight: bold;\n"; }
		if (! empty($input_u)) { echo "text-decoration: underline;\n"; }
		if ($input_background_type == 'color') {
			echo "background-color: " . $input_background . " !important;\n";
		} else {
			echo "background: url('" . $input_background . "') top left repeat-x;\n";
		}
		if (! empty($input_shadow)) { echo "-moz-box-shadow: inset $input_shadow $input_shadow $input_shadow_blur_radius $input_shadow_color;\n-webkit-box-shadow: inset $input_shadow $input_shadow $input_shadow_blur_radius $input_shadow_color;\n"; }
		if (! empty($input_radius)) { echo "-webkit-border-radius: $input_radius;\n-moz-border-radius: $input_radius;\nborder-radius: $input_radius;\n"; }
	?>
}

select {
	padding-right: 0px !important;
}

input[type=file] {
	<?php
		if (! empty($input_size)) { echo "font-size: " . $input_size . ";\n"; }
		if (! empty($input_color)) { echo "color: " . $input_color . ";\n"; }
		if (! empty($input_font)) { echo "font-family: " . $input_font . ";\n"; }
		if (! empty($input_margin)) { echo "margin: " . $input_margin . ";\n"; }
	?>
	padding: <?php echo $pad; ?> 0 <?php echo $pad; ?> 0;
	border: 0;
}

textarea {
	box-sizing: border-box;
	-webkit-box-sizing:border-box;
	-moz-box-sizing: border-box;
	-ms-box-sizing: border-box;
	line-height: 1.2em;
	resize: vertical;
	<?php
	if (! empty($textarea_padding)) { echo "padding: " . $textarea_padding . ";\n"; }
	?>
}

input[type=button], input[type=submit] {
	<?php
		if (! empty($button_size)) { echo "font-size: " . $button_size . ";\n"; }
		if (! empty($button_color)) { echo "color: " . $button_color . ";\n"; }
		if (! empty($button_font)) { echo "font-family: " . $button_font . ";\n"; }
		if (! empty($button_margin)) { echo "margin: " . $button_margin . ";\n"; }
		if (! empty($button_padding)) { echo "padding: " . $button_padding . ";\n"; }
		if (! empty($button_height)) { echo "height: " . $button_height . ";\n"; }
		if (! empty($button_line_height)) { echo "line-height: " . $button_line_height . ";\n"; }
		if (! empty($button_border_br)) { echo "border-bottom: " . $button_border_br . ";\nborder-right: " . $button_border_br . ";\n"; }
		if (! empty($button_border_tl)) { echo "border-top: " . $button_border_tl . ";\nborder-left: " . $button_border_tl . ";\n"; }
		if (! empty($button_i)) { echo "font-style: italic;\n"; }
		if (! empty($button_b)) { echo "font-weight: bold;\n"; }
		if (! empty($button_u)) { echo "text-decoration: underline;\n"; }
		if ($button_background_type == 'color') {
			echo "background-color: " . $button_background . ";";
		} else {
			echo "background: url('" . $button_background . "') top left repeat-x;";
		}
		if (! empty($button_shadow)) { echo "-moz-box-shadow: inset $button_shadow $button_shadow $button_shadow_blur_radius $button_shadow_color;\n-webkit-box-shadow: inset $button_shadow $button_shadow $button_shadow_blur_radius $button_shadow_color;\n"; }
		if (! empty($button_radius)) { echo "-webkit-border-radius: $button_radius;\n-moz-border-radius: $button_radius;\nborder-radius: $button_radius;\n"; }
	?>
}


/* ----------------------------------------------------------
			Headings
   ---------------------------------------------------------- */
 
h1 {
	<?php
		if (! empty($heading1_size)) { echo "font-size: " . $heading1_size . ";\n"; }
		if (! empty($heading1_color)) { echo "color: " . $heading1_color . ";\n"; }
		if (! empty($heading1_font)) { echo "font-family: " . $heading1_font . ";\n"; }
		if (! empty($heading1_margin)) { echo "margin: " . $heading1_margin . ";\n"; }
		if (! empty($heading1_padding)) { echo "padding: " . $heading1_padding . ";\n"; }
		if (! empty($heading1_border)) { echo "border-bottom: " . $heading1_border . ";\n"; }
		if (! empty($heading1_i)) { echo "font-style: italic;\n"; }
		if (! empty($heading1_b)) { echo "font-weight: bold;\n"; }
		if (! empty($heading1_u)) { echo "text-decoration: underline;\n"; }
		if ($heading1_background_type == 'color') {
			echo "background-color: " . $heading1_background . ";";
		} else {
			echo "background: url('" . $heading1_background . "') top left repeat-x;";
		}
	?>
}

h2 {
	<?php
		if (! empty($heading2_size)) { echo "font-size: " . $heading2_size . ";\n"; }
		if (! empty($heading2_color)) { echo "color: " . $heading2_color . ";\n"; }
		if (! empty($heading2_font)) { echo "font-family: " . $heading2_font . ";\n"; }
		if (! empty($heading2_margin)) { echo "margin: " . $heading2_margin . ";\n"; }
		if (! empty($heading2_padding)) { echo "padding: " . $heading2_padding . ";\n"; }
		if (! empty($heading2_border)) { echo "border-bottom: " . $heading2_border . ";\n"; }
		if (! empty($heading2_i)) { echo "font-style: italic;\n"; }
		if (! empty($heading2_b)) { echo "font-weight: bold;\n"; }
		if (! empty($heading2_u)) { echo "text-decoration: underline;\n"; }
		if ($heading3_background_type == 'color') {
			echo "background-color: " . $heading2_background . ";";
		} else {
			echo "background: url('" . $heading2_background . "') top left repeat-x;";
		}
	?>
}

h3 {
	<?php
		if (! empty($heading3_size)) { echo "font-size: " . $heading3_size . ";\n"; }
		if (! empty($heading3_color)) { echo "color: " . $heading3_color . ";\n"; }
		if (! empty($heading3_font)) { echo "font-family: " . $heading3_font . ";\n"; }
		if (! empty($heading3_margin)) { echo "margin: " . $heading3_margin . ";\n"; }
		if (! empty($heading3_padding)) { echo "padding: " . $heading3_padding . ";\n"; }
		if (! empty($heading3_border)) { echo "border-bottom: " . $heading3_border . ";\n"; }
		if (! empty($heading3_i)) { echo "font-style: italic;\n"; }
		if (! empty($heading3_b)) { echo "font-weight: bold;\n"; }
		if (! empty($heading3_u)) { echo "text-decoration: underline;\n"; }
		if ($heading3_background_type == 'color') {
			echo "background-color: " . $heading3_background . ";";
		} else {
			echo "background: url('" . $heading3_background . "') top left repeat-x;";
		}
	?>
}


/* ----------------------------------------------------------
			Other
   ---------------------------------------------------------- */
 
/* --- Links --- */

a:link, a:visited {
	color: <?php echo $link_color; ?>;
	text-decoration: <?php echo $link_decoration; ?>;
}

a:hover, a:active {
	color: <?php echo $link_color_hover; ?>;
	text-decoration: <?php echo $link_decoration_hover; ?>;
}


/* --- Text --- */

.small, .bd_small { font-size: <?php echo $fonts_size_tiny; ?>; color: <?php echo $fonts_color_secondary; ?>; }
.italic { text-style: italic; }
.right { text-align: right; }
.center { text-align: center; }

/* --- Float Clearing --- */

.bd_clear, .clear {
	width: 100%;
	clear: both !important;
	display: inline-block;
}

.bd_clear:after, .clear:after {
	content: ".";
	display: block;
	height: 0;
	clear: both;
	visibility: hidden;
}

.no_top_margin {
	margin-top: 0px !important;
}

.no_margin {
	margin: 0 !important;
}


/* --- Padding --- */

.pad { padding: <?php echo $pad; ?>; }
.pad_less { padding: <?php echo $pad_less; ?>; }
.pad_more { padding: <?php echo $pad_more; ?>; }


/* --- Text and box shadows --- */

/* Dark Text Shadows */
.bd_shadow {
	-moz-text-shadow: 1px 1px 0 rgba(0, 0, 0, 0.5);
	-webkit-text-shadow: 1px 1px 0 rgba(0, 0, 0, 0.5);
	text-shadow: 1px 1px 0 rgba(0, 0, 0, 0.5);
}

/* Light Text Shadows */
.bd_shadow_white {
	-moz-text-shadow: 1px 1px 0 rgba(255, 255, 255, 0.5);
	-webkit-text-shadow: 1px 1px 0 rgba(255, 255, 255, 0.5);
	text-shadow: 1px 1px 0 rgba(255, 255, 255, 0.5);
}

/* Box Shadows */
.bd_dropshadow {
	-moz-box-shadow: 1px 2px 2px #e1e1e1;
	-webkit-box-shadow: 1px 2px 2px #e1e1e1;
	box-shadow: 1px 1px 2px #e1e1e1;
}

/* Box Shadows */
.bd_dropshadow_white {
	-moz-box-shadow: 1px 1px 2px #fff;
	-webkit-box-shadow: 1px 1px 2px #fff;
	box-shadow: 1px 1px 2px #fff;
}

/* --- Columns --- */

/* 2 columns */
.col50 {
	float: left;
	width: 48%;
	margin-right: 1%;
}

/* 3 columns */

.col33 {
	float: left;
	width: 31%;
	margin-right: 1%;
}

/* 4 columns */

.col25 {
	float: left;
	width: 23%;
	margin-right: 1%;
}

/* --- Others --- */

/* Standardized element stylings */
.bd_white_box {

}


/* --- White Space --- */

.less_space {
	height: <?php echo $pad; ?>;
}

.space {
	height: <?php echo $pad_more; ?>;
}

.more_space {
	height: <?php echo $pad_max; ?>;
}

/* Horizontal Line */
.line {
	border-top: <?php echo $borders; ?>;
	margin: <?php echo $pad_more; ?> 0 <?php echo $pad_more; ?> 0;
}

.thick_line {
	border-top: 3px solid <?php echo $border_color; ?>;
	margin: <?php echo $pad_more; ?> 0 <?php echo $pad_more; ?> 0;
}


/* Saved Box */
#bd_saved {
	z-index: 5001;
	position: fixed;
	width: 120px;
	top: 80%;
	left: 50%;
	min-height: 32px;
	margin-left: -60px;
	color: #000;
	text-align: center;
	height: 29px;
	line-height: 29px;
	font-size: <?php echo $fonts_size; ?>;
	font-family: <?php echo $fonts; ?>;
	background-color: #62E262;
	border: 1px solid #5EDA5E;
	-webkit-border-radius: 2px;
	-moz-border-radius: 2px;
	border-radius: 2px;
}


/* Error Notice */
#bd_ajax_error {
	font-family: arial;
	border: 1px solid #F44545;
	color: #fff;
	background: url('imgs/icon-attention.png') 10px 10px no-repeat #FE4D4D;
	width: 300px;
	margin-left: -150px;
	position: fixed;
	top: 50%;
	left: 50%;
	z-index: 5000;
	display: none;
	-webkit-border-radius: 10px;
	-moz-border-radius: 10px;
	border-radius: 10px;
	cursor: pointer;
}

.bd_ae_pad {
	background: url('imgs/close.png') right 10px no-repeat;
	margin-right: 15px;
	padding: 15px 25px 15px 50px;
}


<?php

	include "css_theme_style.php";	// Custom theme styles.
	include "css_article.php";		// Wiki-syntax styles.
	include "../_css/css_system.css";	// System styles.

?>