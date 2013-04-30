<?php

if ($privileges['is_admin'] != "1") {
	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
} else {
	
	require PATH . "/includes/field.functions.php";
	$fields = new fields;
	
	// Create a query for sorting user
	$page_name = "fields";
	$mysql_table = TABLE_PREFIX . "fields";
	$default_sort = "display_name";
	$default_search = "display_name";
	include "includes/run_page_sorting.php";
	
	foreach ($return_results as $this_result) {
 		$this_field = $fields->get_field($this_result);
		$field_type = $fields->get_type_name($this_field['type']);
		// Create field entry
	   	$list .= "<tr id=\"" . $this_field['id'] . "\">
	   	<td valign=\"top\">" . $this_field['display_name'] . "</td>
	   	<td valign=\"top\">" . $this_field['id'] . "</td>
	   	<td valign=\"top\">$field_type</td>
	   	<td valign=\"top\" class=\"center\"><a href=\"#\" onClick=\"deleteID('$mysql_table','" . $this_field['id'] . "');return false;\"><img src=\"imgs/icon-delete.png\" border=\"0\" alt=\"Delete\" title=\"Delete\" /></a></td>
	   	</tr>";
	}
?>

<script type="text/javascript" language="JavaScript">
<!--
$(document).ready(function() {
	// call the tablesorter plugin
	$("#fields_list").tablesorter({
		// sort on the first column order desc
		sortList: [[0,0]],
		headers: { 3:{sorter: false}}
	});
});
-->
</script>

<div id="content_overlay">

   	<div class="submit">
   		<a href="http://www.doyoubananadance.com/Users/User-Registration" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
   	</div>
	
	<h1>Listing Fields (<?php echo $queryInfo['count']; ?>)</h1>
	<div class="clear"></div>
	
		<?php
			include "sort_page_top.php";
		?>
	
	<table cellspacing="0" cellpadding="0" id="fields_list" class="sort_table">
	<thead>
	<tr>
	<th>Display Name</th>
	<th>Actual Name</th>
	<th>Type</th>
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