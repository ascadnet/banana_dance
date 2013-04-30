<?php

	header("Content-type: text/css");

	$fonts = 'verdana, arial';
	$fonts_color = '#000';
	
	$fonts_size_sm = '15pt';
	$fonts_size = '25pt';
	$fonts_size_lg = '30pt';
	
	$line_height = '150%';
	
	$h1_size = '45pt';
	$h2_size = '32pt';
	$h3_size = '25pt';
	
	$heading_font = 'verdana, arial';
	$heading_color = '#222E33';

	$box_bg = "#f1f1f1";
	$box_bg_secondary = "#fff";
	$borders = "#e1e1e1";
	
	$error_color = "#FFD2D3";
	$hover_color = "#FFF8C0";
	$shadow_color = "#e1e1e1";

?>

/* --- Links --- */

a:link, a:visited {
	color: #3996C3;
	text-decoration: none;
}

a:hover, a:active {
	color: #E2A326;
	text-decoration: none;
}

.bd_clear {
	clear: both;
}


/* --- Text and box shadows --- */

.bd_shadow {
	-moz-text-shadow: 1px 1px 0 rgba(0, 0, 0, 0.5);
	-webkit-text-shadow: 1px 1px 0 rgba(0, 0, 0, 0.5);
	text-shadow: 1px 1px 0 rgba(0, 0, 0, 0.5);
}

.bd_shadow_white {
	-moz-text-shadow: 1px 1px 0 rgba(255, 255, 255, 0.5);
	-webkit-text-shadow: 1px 1px 0 rgba(255, 255, 255, 0.5);
	text-shadow: 1px 1px 0 rgba(255, 255, 255, 0.5);
}

.bd_dropshadow {
	-moz-box-shadow: 2px 2px 2px #fff;
	-webkit-box-shadow: 2px 2px 2px #fff;
	box-shadow: 2px 2px 2px #fff;
}


/* --- System Generated Information --- */

.bd_system {
	border: 1px solid #e1e1e1;
	background-color: #f1f1f1;
	font-family: verdana;
	font-size: 8.5pt;
	padding: 10px;
	margin-bottom: 20px;
}