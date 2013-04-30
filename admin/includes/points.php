<?php

if ($privileges['is_admin'] != "1") {
	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
} else {
	
	// Create a query for sorting user
	$page_name = "points";
	$mysql_table = TABLE_PREFIX . "point_values";
	$default_sort = "task";
	$default_search =  array("task");
	include "includes/run_page_sorting.php";
	
	foreach ($return_results as $this_result) {
 		$row = $manual->get_point_value($this_result);
   		$showname = ucwords(str_replace('_',' ',$row['task']));
   		
   		if ($row['task'] == 'comment_status_changed') {
   			$status_name = $manual->get_status_settings($row['act_on_id'],'title');
   			$showname .= " to \"" . $status_name['title'] . "\"";
   		}
   		
   		if (! empty($row['required'])) {
   			$type = "Require <b>" . $row['required'] . "</b> points by";
   		} else {
   			$type = "Award <b>" . $row['points'] . "</b> points to";
   		}
   		if ($row['act_on'] == 'act_on') {
   			$type .= " the owner of item acted upon.";
   		} else {
   			$type .= " the user performing the task.";
   		}
   		$list .= "<tr>
   		<td><a href=\"index.php?l=points_edit&id=" . $row['id'] . "\">$showname</a></td>
   		<td>$type</td>
   		<td><a href=\"#\" onClick=\"deleteID('$mysql_table','" . $row['id'] . "');return false;\"><img src=\"imgs/icon-delete.png\" border=\"0\" alt=\"Delete\" title=\"Delete\" /></a></td>
   		</tr>";
	}
	
?>

<script type="text/javascript" language="JavaScript">
<!--
$(document).ready(function() {
	// call the tablesorter plugin
	$("#points").tablesorter({
		// sort on the first column order desc
		sortList: [[0,0]],
		headers: { 1:{sorter: false}, 2:{sorter: false} }
	});
});
-->
</script>

<div id="content_overlay">
	
   	<div class="submit">
   		<a href="http://www.doyoubananadance.com/Users/Point-and-Badge-System" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
   	</div>
   	
	<div id="actions_right">
		<ul>
			<li><a href="index.php?l=points_add">Create</a></li>
			<li><a href="index.php?l=badges">Badges</a></li>
		</ul>
	</div>
	
	<h1>Point Values</h1>
		
		<?php
			include "sort_page_top.php";
		?>
		
		<table cellspacing="0" cellpadding="0" id="points" class="sort_table">
		<thead>
		<tr>
		<th>Task</th>
		<th>Description</th>
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