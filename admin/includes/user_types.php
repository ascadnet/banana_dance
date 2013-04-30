<?php

if ($privileges['is_admin'] != "1") {
	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
} else {
	
	// Create a query for sorting user
	$page_name = "user_types";
	$mysql_table = TABLE_PREFIX . "user_types";
	$default_sort = "name";
	$default_search =  array("name");
	include "includes/run_page_sorting.php";
	
	foreach ($return_results as $this_result) {
 		$this_user_type = $session->get_usertype_settings($this_result);
		// Styling for the user type
		$style = "";
		if (! empty($this_user_type['color'])) {
			$style .= "background-color:#" . $this_user_type['color'] . ";";
		}
		if (! empty($this_user_type['font_color'])) {
			$style .= "color:#" . $this_user_type['font_color'] . ";";
		}
		// Admin?
		if ($this_user_type['is_admin'] == "1") {
			$show_status = "<img src=\"imgs/icon-star.png\" id=\"status" . $this_user_type['is_admin'] . "\" width=16 height=16 border=\"0\" alt=\"Admin\" title=\"Admin\" />";
		}
		else {
			$show_status = "";
		}
	   	$list .= "<tr id=\"" . $this_user_type['id'] . "\">
	   	<td valign=\"top\" class=\"center\">$show_status</td>
	   	<td valign=\"top\"><a href=\"index.php?l=user_types_edit&id=" . $this_user_type['id'] . "\">" . $this_user_type['name'] . "</a></td>
	   	<td valign=\"top\"><span style=\"$style\">Username</span></td>
	   	<td valign=\"top\" class=\"center\"><a href=\"#\" onClick=\"deleteID('$mysql_table','" . $this_user_type['id'] . "');return false;\"><img src=\"imgs/icon-delete.png\" border=\"0\" alt=\"Delete\" title=\"Delete\" /></a></td>
	   	</tr>";
	}
?>

<script type="text/javascript" language="JavaScript">
<!--
$(document).ready(function() {
	// call the tablesorter plugin
	$("#usertype_list").tablesorter({
		// sort on the first column order desc
		sortList: [[1,0]],
		headers: { 0:{sorter: false}, 2:{sorter: false}}, 3:{sorter: false}}
	});
});
-->
</script>

<div id="content_overlay">

   	<div class="submit">
   		<a href="http://www.doyoubananadance.com/Users/User-Types" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
   	</div>
   	
	<div id="actions_right">
		<ul>
			<li><a href="index.php?l=user_types_add<?php if (! empty($_GET['category'])) { echo "&category=" . $_GET['category']; } ?>">Create</a></li>
		</ul>
	</div>
	
	<h1>Listing User Types (<?php echo $queryInfo['count']; ?>)</h1>

		<?php
			include "sort_page_top.php";
		?>
	
	<table cellspacing="0" cellpadding="0" id="usertype_list" class="sort_table">
	<thead>
	<tr>
	<th width="25">&nbsp;</th>
	<th>Name</th>
	<th>Color Settings</th>
	<th width="25">&nbsp;</th>
	</tr>
	</thead> 
	<tbody>
	<?php
	echo $list;
	?>
	</tbody>
	</table>

</div>

<?php
}
?>