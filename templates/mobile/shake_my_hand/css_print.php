<?php

	include "css_definitions.php";
	include "css_article.php";

?>

body {
	color: #000;
	margin: 40px;
}

.print_holder {
	padding: 30px;
}

a {
	text-decoration: none;
	color: #000 !important;
	border-bottom: 1px dotted #000;
	font-family: arial, verdana;
}

h1, h2, h3 {
	font-family: arial, verdana;
	font-weight: normal;
	margin: 0 0 20px 0;
	padding: 0;
	color: #000 !important;
}

h1.header {
	font-size: 40pt;
	letter-spacing: -4px;
}

h2.header {
	font-size: 30pt;
	margin-top: -25px;
	letter-spacing: -2px;
}

ul#print_primary {
	font-family: arial, verdana;
	line-height: 14pt;
	font-size: 10pt;
	margin: 0 0 10px 0;
	padding: 0 0 0 0;
	list-style: square inside;
}

ul.articles {
	margin: 0 0 10px 0 !important;
	list-style: none inside !important;
}

li.category_header {
	font-weight: bold;
}

li {
	padding: 0 0 0 0;
	margin: 0;
}

span.breadcrumbs {
	font-size: 9pt;
	font-weight: normal;
}

h1.category_name {
	font-size: 60pt;
	border-bottom: 3px solid #000;
	border-top: 5px dotted #000;
	padding: 0;
	font-weight: normal;
	margin: 60px 0 0 0;
	text-transform: uppercase;
}

h1.article_name {
	font-size: 40pt;
	border-bottom: 1px dotted #555;
	padding: 10px 0 10px 0;
	font-weight: normal;
	margin: 0 0 10px 0;
	text-transform: uppercase;
}

h1.bd_h1 {
	font-size: 27pt !important;
}

h2.bd_h2 {
	font-size: 20pt !important;
}

.category_hr {
	height: 50px;
	page-break-after: always;
}

.article_hr {

}

.article_entry {
	margin: 0 20px 20px 20px;
	padding: 20px;
	border-bottom: 3px solid #999;
	border-top: 1px solid #999;
	border-right: 1px solid #999;
	border-left: 1px solid #999;
}

.toTop {
	float: right;
	font-size: 8pt;
}

.sub_articles {
	padding: 20px;
	margin: 0 0 20px 0;
	border: 1px solid #e1e1e1;
	background-color: #f1f1f1;
}

.sub_articles ul {
	margin: 0;
	padding: 0;
	font-family: arial, verdana;
	line-height: 14pt;
	font-size: 10pt;
}

.highlight {
	border-bottom: 1px solid #666;
	padding: 10px;
}

?>