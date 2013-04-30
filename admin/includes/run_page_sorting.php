<?php

	// Page
	if (! empty($_GET['p'])) {
		$page = $_GET['p'];
	} else {
		$page = "1";
	}
	
	// Display
	if (! empty($_GET['d'])) {
		$display = $_GET['d'];
	} else {
		$display = "50";
	}
	
	// Display
	if (! empty($_GET['dir'])) {
		$dir = $_GET['dir'];
	} else {
		if (! empty($default_dir)) {
			$dir = $default_dir;
		} else {
			$dir = "ASC";
		}
	}
	
	// Alpha list?
	if (! empty($_GET['alpha'])) {
		$alpha = $_GET['alpha'];
	} else {
		$alpha = "";
	}
	
	// Search Query?
	if (! empty($_GET['q'])) {
		foreach ($default_search as $search_field) {		
			$search = " OR LOWER(`" . $search_field . "`) LIKE '%" . strtolower($_GET['q']) . "%'";
		}
   		$search = trim($search, " OR ");
   		$search = "(" . $search . ")";
		$search_show = $_GET['q'];
	} else {
		$search = "";
		$search_show = "";
	}

	// Create a query for sorting user
	$exp_string = explode('&',$_SERVER['QUERY_STRING']);
	foreach ($exp_string as $component) {
		$exp_comp = explode('=',$component);
   		if ($exp_comp['0'] != "p") {
   			// Used for pagination
   			if ($exp_comp['0'] == "alpha") {
   				$full_get_string .= "&" . $component;
   			} else {
   				$alpha_string .= "&" . $component;
   				$full_get_string .= "&" . $component;
   			}
   			// Used for searching
   			if ($exp_comp['0'] != "q" && $exp_comp['0'] != 'd') {
   				$fields_list .= "<input type=\"hidden\" name=\"" . $exp_comp['0'] . "\" value=\"" . $exp_comp['1'] . "\" />\n";
   			}
   		}
   		else {
   			// Used for searching
   			$fields_list .= "<input type=\"hidden\" name=\"p\" value=\"1\" />\n";
   		}
	}
	$alpha_string = substr($alpha_string,1);
	$full_get_string = substr($full_get_string,1);
	
	$link = ADMIN_URL . "/index.php?" . $full_get_string;
	
	$get_string .= "l=$page_name&p=1&d=$display";
	
	$az_list = $admin->alpha_list($alpha_string,$alpha);
	
	$queryInfo = $db->form_query($mysql_table,$display,$page,$alpha,$default_sort,$dir,$special_where_clause,$search,'1');
	
	$return_results = $admin->list_results($queryInfo['query']);
	
	$pagination = $admin->paginate($queryInfo['count'],$display,$link,$page);
	
?>