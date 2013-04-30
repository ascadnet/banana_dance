<?php

if ($privileges['is_admin'] != "1") {
	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
} else {
	
	// Create a query for sorting user
	$page_name = "badges";
	$mysql_table = TABLE_PREFIX . "badges";
	$default_sort = "name";
	$default_search =  array("name");
	include "includes/run_page_sorting.php";
	
	foreach ($return_results as $this_result) {
 		$row = $manual->get_badge($this_result);
 		
		$style = "";
		if (! empty($row['color'])) {
			$style .= "background-color:#" . $row['color'] . ";";
		}
		if (! empty($row['font_color'])) {
			$style .= "color:#" . $row['font_color'] . ";";
		}
		
		if ($row['act'] == 'score') {
			$req = "Get " . $row['points_required'] . " points.";
		}
		else if ($row['act'] == 'article_add') {
			$req = "Post " . $row['points_required'] . " articles.";
		}
		else if ($row['act'] == 'article_edit') {
			$req = "Edit " . $row['points_required'] . " articles.";
		}
		else if ($row['act'] == 'comment_post') {
			$req = "Post " . $row['points_required'] . " comments.";
		}
		else if ($row['act'] == 'comment_status_changed') {
			$ctype = $manual->get_status_settings($row['act_id']);
			$req = "Get " . $row['points_required'] . " comments changed to comment type \"" . $ctype['title'] . "\".";
		}
 		
   		$list .= "<tr>
   		<td><span style=\"$style\"><a href=\"index.php?l=badges_edit&id=" . $row['id'] . "\">" . $row['name'] . "</a></span></td>
   		<td>" . $row['desc'] . "</td>
   		<td>" . $req . "</td>
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
		headers: { 1:{sorter: false}, 3:{sorter: false} }
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
			<li><a href="index.php?l=badges_add">Create</a></li>
			<li><a href="index.php?l=points">Point Values</a></li>
		</ul>
	</div>
	
	<h1>Badges</h1>
	
		<?php
			include "sort_page_top.php";
		?>
		
		<table cellspacing="0" cellpadding="0" id="points" class="sort_table">
		<thead>
		<tr>
		<th>Name</th>
		<th>Color Settings</th>
		<th>Requirement</th>
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