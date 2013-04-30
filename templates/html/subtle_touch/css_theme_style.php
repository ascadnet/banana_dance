

.holder {
	padding: 0 16px 0 16px;	
}


/* --- */

#topbar {
	height: 38px;
}

#back_to_top {
	position: fixed;
	bottom: 12px;
	right: 12px;
	font-size: <?php echo $fonts_size_small; ?>;
}

/* --- */

#logo_search {
	height: 116px;
	border-top: <?php echo $borders; ?>;
	border-bottom: <?php echo $borders; ?>;
	background-color: <?php echo $bg1 ?>;
}

#logo {
	float: left;
	width: 50%;
	font-size: 18pt;
	height: 116px;
	line-height: 116px;
}

#logo a {
	color: <?php echo $fonts_color; ?>;
}

#search {
	margin-left: 50%;
	text-align: right;
}

#search input[type=text] {
	width: 400px;
	line-height: 35px;
	height: 35px;
	margin-top: 41px;
}


/* --- */

#under_top {
	height: 59px;
	line-height: 59px;
	border-bottom: <?php echo $borders; ?>;
}

#under_top a, #main_right li a {
	color: <?php echo $fonts_color_secondary; ?> !important;
}


/* --- */

#main_content {

}

#main_left {
	float: left;
	width: 226px;
}

#main_right {
	float: right;
	width: 226px;
	margin: -63px 0 0 0;
}

#main_right_nomarg {
	float: right;
	width: 226px;
	margin: 0;
}

#main_center {
	margin: <?php echo $pad_more; ?> 274px 0 226px;
}


/* --- */

#footer {
	margin: 48px auto 48px auto;
	border-top: 1px solid <?php echo $borders; ?>;
	color: <?php echo $fonts_color_secondary; ?>;
	font-size: <?php echo $fonts_size_small; ?>;
}


/* --- */

#article_info {
	margin-top: 35px;
}

#article_info span {
	font-size: 8.5pt;
	color: <?php echo $fonts_color_secondary; ?>;
	line-height: 1;
	margin-top: 10px;
	display: inline-block;
}

img.article_creator_pic {
	float: left;
	margin-right: 12px;
}

span.right_title {
	display: block;
	margin-top: <?php echo $pad_more; ?>;
	font-size: <?php echo $fonts_size_small; ?>;
	font-weight: bold;
}


/* --- */

#current_password {
	background-color: <?php echo $bg1; ?>;
	border: <?php echo $borders; ?>;
}