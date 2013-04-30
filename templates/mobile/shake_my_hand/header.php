<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" lang="%bd_language%" xml:lang="%bd_language%"> 
<head> 
	<title>%meta_title%</title> 
	<meta http-equiv="Content-Type" content="text/html; charset=%bd_charset%" /> 
	<meta name="author" content="%company%" /> 
	<meta name="description" content="%meta_desc%" /> 
	<meta name="keywords" content="%meta_keywords%" /> 
	<meta name="robots" content="all" /> 
	<meta name="revisit-after" content="7 days" /> 
	<meta name="generator" content="Banana Dance" />
	<meta name="viewport" content="width=device-width, initial-scale=0.75, width=device-width" />
</head>
<body>

<script type="text/javascript">
<!--
function showNav() {
	$('#bd_floating_area').show();
	$('#theNav').hide();
}
-->
</script>

<form action="%program_url%/search.php" method="get">
<div id="topbar"><div id="toppad">

	<div id="logo"><a href="%url%">%site_name%</a></div>
	
	<div id="bd_logged_session">
		%user_sidebar%
	</div>
	
</div></div>
</form>

<div class="bd_holder">

	<div id="theNav" class="make_button center"><a href="#" onclick="showNav();return false;">Navigation</a></div>

	<div id="bd_floating_area"><div id="bd_float_pad">
	<!-- START CATEGORY NAV -->
	%category_tree%
	<!-- END CATEGORY NAV -->
	</div></div>
	
	<p id="breadcrumbs">%breadcrumbs%</p>