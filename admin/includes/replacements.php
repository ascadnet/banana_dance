<?php

if ($privileges['is_admin'] != "1") {
	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
} else {

	// -----------------------------------------------------------------
	// 	Filter Considerations?
	
	if ($_GET['filter'] == "1") {
		if (! empty($_GET['user_type'])) {
			$special_where_clause .= " AND `type`='" . $db->mysql_clean($_GET['type']) . "'";	
		}
	}
	
	// -----------------------------------------------------------------
	// 	Create a query for sorting user
	
	$page_name = "replacements";
	$mysql_table = TABLE_PREFIX . "custom_callers";
	$default_sort = "caller";
	$default_search = array("caller");
	include "includes/run_page_sorting.php";

	// -----------------------------------------------------------------
	// 	Loop Results
	
	foreach ($return_results as $this_result) {
 		$this_caller = $manual->get_custom_caller($this_result);
 		
 		if ($this_caller['type'] == 'link') {
 			$show_type = 'Page Link';
			if (strpos($this_caller['replacement'],'http') !== false) {
 				$link = $this_caller['replacement'];
 				$external = " target=\"_blank\"";
 			}
 			// Article Links
 			else {
 				$link = $manual->prepare_link($this_caller['replacement']);
 				$external = "";
 			}
 			$final_replace = "<a href=\"$link\"$external>" . $this_caller['caller'] . "</a>";
 		}
 		else if ($this_caller['type'] == 'bubble') {
 			$show_type = 'Help Bubble';
 			$len = strlen($this_caller['replacement']);
 			if ($len > 50) {
 				$final_replace = substr($this_caller['replacement'],0,50) . '...';
 			} else {
 				$final_replace = $this_caller['replacement'];
 			}
 		}
 		else {
 			$show_type = 'Standard Replacement';
 			$final_replace = $this_caller['replacement'];
 		}
 		
 		//caller	replacement	category	type
 		
	   	$list .= "<tr id=\"" . $this_caller['id'] . "\">
	   	<td valign=\"top\" onmouseover=\"show('arf" . $this_caller['id'] . "');\" onmouseout=\"hide('arf" . $this_caller['id'] . "');\"$class><a href=\"index.php?l=caller_edit&id=" . $this_caller['id'] . "\">" . $this_caller['caller'] . "</a></td>
	   	<td valign=\"top\"$class>$show_type</td>
	   	<td valign=\"top\"$class>$final_replace</td>
	   	<td valign=\"top\"$class><a href=\"#\" onClick=\"deleteID('$mysql_table','" . $this_caller['id'] . "');return false;\"><img src=\"imgs/icon-delete.png\" border=\"0\" alt=\"Delete\" title=\"Delete\" class=\"icon_nopad\" /></a></td>
	   	</tr>";
	   	
	}
?>

<script type="text/javascript" language="JavaScript">
<!--
$(document).ready(function() {
	// call the tablesorter plugin
	$("#user_list").tablesorter({
		// sort on the first column order desc
		sortList: [[0,0]],
		headers: { 4:{sorter: false}}
	});
});
-->
</script>

<div id="content_overlay">
	
	<div class="submit">
   		<a href="http://www.doyoubananadance.com/" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
	</div>
	
	<div id="actions_right">
		<ul>
			<li><a href="index.php?l=caller_add">Create</a></li>
		</ul>
	</div>

	<h1>Listing Custom Callers (<?php echo $queryInfo['count']; ?>)</h1>

		<?php
			include "sort_page_top.php";
		?>
		
		<table cellspacing="0" cellpadding="0" id="user_list" class="sort_table">
		<thead>
		<tr>
		<th>Caller</th>
		<th>Type</th>
		<th>Replacement</th>
		<th width="30">&nbsp;</th>
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