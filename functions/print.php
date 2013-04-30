<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Create a printer-friendly version of a category
	or a webpage.
	
	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 2 of the License, or
	(at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.

====================================================== */


if (file_exists('../config.php')) {
	require "../config.php";
} else {
	header('Location: ../setup/index.php');
	exit;
}

if (PERFORMANCE_TESTS == '1') {
	$start = microtime(true);
}

// Category?
if (empty($_GET['category'])) {
	$subcat = '0';
	$category_name = NAME;
} else {
	$subcat = $_GET['category'];
	$category_name = NAME . $manual->get_category_name_from_id($_GET['category']);
}

// Load the class
require PATH . "/includes/print.functions.php";
$print = new printing;

// Get everything there is to get...
$returned = $print->get_categories($subcat,$_GET['subcategories']);

ob_start();
$print->format_categories($returned);
$get_data = ob_get_contents();
ob_end_clean();

ob_start();
$print->get_formatted_articles($returned);
$get_formatted_articles = ob_get_contents();
ob_end_clean();

// Template considerations
$special_changes = array(
	'%category%' => $subcat,
	'%categories%' => $get_data,
	'%put_articles%' => $get_formatted_articles,
	'%category_name%' => $category_name,
	'%category_id%' => $_GET['category'],
	'%subcategories%' => $_GET['subcategories']
);
$display_everything = $template->render_template('article_print_all',$user,$special_changes,'1','1','0','','1');

// Rendering a PDF?
// Must appear before the performance testing.
if ($_GET['pdf'] == '1') {
	$filename = str_replace(' ','_',NAME);
	require PATH . "/includes/dompdf/dompdf_config.inc.php";
	$dompdf = new DOMPDF();
	$dompdf->load_html($display_everything);
	$dompdf->render();
	$dompdf->stream($filename);
	exit;
}

if (PERFORMANCE_TESTS == '1') {
	$end = microtime(true);
	$dif = $end - $start;
	echo "<div class=\"bd_system\"><b>Performance Testing: $dif</b></div>";
}

echo $display_everything;
exit;

?>