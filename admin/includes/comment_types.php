<?php

if ($privileges['is_admin'] != "1") {
	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
} else {
	
	// Create a query for sorting user
	$page_name = "comment_types";
	$mysql_table = TABLE_PREFIX . "comment_statuses";
	$default_sort = "title";
	$default_search = array("title");
	$special_where_clause = "";
	include "includes/run_page_sorting.php";
	
	foreach ($return_results as $this_result) {
 		$this_cs = $admin->get_comment_status($this_result);

	   	$list .= "<tr id=\"" . $this_cs['id'] . "\">
	   	<td valign=\"top\"><a href=\"index.php?l=comment_types_edit&id=" . $this_cs['id'] . "\">" . $this_cs['title'] . "</a></td>
	   	<td valign=\"top\">" . $this_cs['desc'] . "</td>
	   	<td valign=\"top\" class=\"center\"><a href=\"#\" onClick=\"deleteID('" . TABLE_PREFIX . "comment_statuses','" . $this_cs['id'] . "');return false;\"><img src=\"imgs/icon-delete.png\" border=\"0\" alt=\"Delete\" title=\"Delete\" /></a></td>
	   	</tr>";
	}
?>

<script type="text/javascript" language="JavaScript">
<!--
$(document).ready(function() {
	// call the tablesorter plugin
	$("#cs_list").tablesorter({
		// sort on the first column order desc
		sortList: [[2,0]],
		headers: { 2:{sorter: false} }
	});
});
-->
</script>

<div id="content_overlay">

	<div class="submit">
		   	<a href="http://www.doyoubananadance.com/Comments/Comment-Types" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
	</div>
		
	<div id="actions_right">
		<ul>
			<li><a href="index.php?l=comment_types_add<?php if (! empty($_GET['category'])) { echo "&category=" . $_GET['category']; } ?>">Create</a></li>
		</ul>
	</div>
	
	<h1>Comment Types (<?php echo $queryInfo['count']; ?>)</h1>
	
	<table cellspacing="0" cellpadding="0" id="cs_list" class="sort_table">
	<thead>
	<tr>
	<th>Name</th>
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