<?php

	// ----------------------------------------------------------
	//	DO NOT TOUCH!
	// require "../../../generated/css.php";
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
	margin-bottom: 60px;
}

p, ol, ul, blockquote, table, label {

}

p {
	margin-bottom: 12px;
}

ol, ul {

}

li {

}

blockquote, q {

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
}

table th {
	font-weight: bold;
}

table td, table th {

}


/* ----------------------------------------------------------
			Form Fields
   ---------------------------------------------------------- */
   
label {

}

label.left {

}

input[type=text], input[type=password], textarea, select {

}

select {
	
}

input[type=file] {

}

textarea {

}

input[type=button], input[type=submit] {

}


/* ----------------------------------------------------------
			Headings
   ---------------------------------------------------------- */
 
h1 {

}

h2 {

}

h3 {

}


/* ----------------------------------------------------------
			Other
   ---------------------------------------------------------- */
 
/* --- Links --- */

a:link, a:visited {

}

a:hover, a:active {

}


/* --- Text --- */

.small, .bd_small {  }
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

.pad { }
.pad_less { }
.pad_more { }


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

}

.space {

}

.more_space {

}

/* The following is important for smooth
   editing and creation of pages. */
#bd_article_inline_edit {
	position: absolute;
	left: 0;
	bottom: 0;
	display: none;
}

/* Horizontal Line */
.line {
	
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