<?php

if ($privileges['is_admin'] != "1") {
	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
} else {

	// -----------------------------------------------------------------
	// 	Filter Considerations?
	
	if ($_GET['filter'] == "1") {
		if (! empty($_GET['user_type'])) {
			$special_where_clause .= " AND `type`='" . $db->mysql_clean($_GET['user_type']) . "'";	
		}
		if (! empty($_GET['joined'])) {
			$special_where_clause .= " AND `joined` LIKE '" . $db->mysql_clean($_GET['joined']) . "%'";
		}
		if (! empty($_GET['start'])) {
   			if (empty($_GET['end'])) {
   				$_GET['end'] = date('Y-m-d');
   			}
   			$special_where_clause .= " AND (`joined`>='" . $db->mysql_clean($_GET['start']) . "%' AND `joined`<='" . $db->mysql_clean($_GET['end']) . "%')";
		}
		if (! empty($_GET['score'])) {
			$special_where_clause .= " AND (`upvoted`-`downvoted`)>='" . $db->mysql_clean($_GET['score']) . "'";
		}
	}
	
	// -----------------------------------------------------------------
	// 	Create a query for sorting user
	
	$page_name = "users";
	$mysql_table = TABLE_PREFIX . "attachments";
	$default_sort = "filename";
	$default_search = array("filename");
	include "includes/run_page_sorting.php";

	// -----------------------------------------------------------------
	// 	Loop Results
	
	foreach ($return_results as $this_result) {
 		$this_download = $db->get_file_info('','',$this_result);
 		
 		$show_path = str_replace(PATH,'',$this_download['path']);
 		
 		if ($this_download['found'] == '1') {
 			$link = "<a href=\"" . $this_download['url'] . "\"><img src=\"imgs/icon-view.png\" id=\"arf" . $this_download['id'] . "\" width=\"16\" height=\"16\" border=\"0\" alt=\"Open file\" title=\"Open file\" style=\"float:right;display:none;\" /></a>";
 		} else {
 			$link = "<img src=\"imgs/attention.png\" id=\"arf" . $this_download['id'] . "\" width=\"16\" height=\"16\" border=\"0\" alt=\"File not on server!\" title=\"File not on server!\" style=\"float:right;display:none;\" />";
 		}
 		
	   	$list .= "<tr id=\"" . $this_download['id'] . "\">
	   	<td valign=\"top\" onmouseover=\"show('arf" . $this_download['id'] . "');\" onmouseout=\"hide('arf" . $this_download['id'] . "');\"$class><a href=\"index.php?l=download_edit&id=" . $this_download['id'] . "\">" . $show_path . "</a>$link</td>
	   	<td valign=\"top\"$class>" . $this_download['ext'] . "</td>
	   	<td valign=\"top\"$class>" . $this_download['downloads'] . "</td>
	   	<td valign=\"top\"$class>" . $last_download . "</td>
	   	<td valign=\"top\"$class><a href=\"index.php?l=download_activity\"><img src=\"imgs/icon-dl_activity.png\" border=\"0\" alt=\"Download Activity\" title=\"Download Activity\" class=\"icon_less\" /></a><a href=\"#\" onClick=\"deleteID('$mysql_table','" . $this_download['id'] . "');return false;\"><img src=\"imgs/icon-delete.png\" border=\"0\" alt=\"Delete\" title=\"Delete\" class=\"icon_nopad\" /></a></td>
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
   		<a href="http://www.doyoubananadance.com/Media-Library/Downloadable-Content" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
	</div>
	
	<h1>Listing Downloads (<?php echo $queryInfo['count']; ?>)</h1>

		<?php
			include "sort_page_top.php";
		?>
		
		<table cellspacing="0" cellpadding="0" id="user_list" class="sort_table">
		<thead>
		<tr>
		<th>Filename</th>
		<th>Type</th>
		<th>Downloads</th>
		<th>Last Download</th>
		<th width="40">&nbsp;</th>
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