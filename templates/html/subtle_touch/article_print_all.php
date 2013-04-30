<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" lang="%bd_language%" xml:lang="%bd_language%"> 
<head> 
<title>%meta_title%</title> 
<meta http-equiv="Content-Type" content="text/html; charset=%bd_charset%" /> 
<meta name="author" content="%company%" /> 
<meta name="description" content="%meta_desc%" /> 
<meta name="keywords" content="%meta_keywords%" /> 
<meta name="robots" content="none" />

<link href="%theme%/css_print.php" rel="stylesheet" type="text/css" /> 

</head>
<body>

<a name="top"></a>
<div id="bd_article">
	<div class="print_holder">
		<h1 class="header">%category_name%</h1>
		<span class="toTop"><b><a href="#" onclick="window.print();return false;">Print this page</a> - <a href="%program_url%/print_pdf/%category%/%subcategories%">Save PDF</a></b></span>
		<h2 class="header">Table of Contents</h2>
		<!-- START CATEGORIES -->
		<ul id="print_primary">%categories%</ul>
		<!-- END CATEGORIES -->
	</div>
	<!-- START ARTICLES -->
	%put_articles%
	<!-- END ARTICLES -->
</div>

</body>
</html>