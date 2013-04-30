<?php

if ($privileges['is_admin'] != "1") {
	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
} else {

	// -----------------------------------------------------------------
	// 	Filter Considerations?
	
	if ($_GET['filter'] == "1") {
		if (! empty($_GET['dl'])) {
			$special_where_clause .= " AND `type`='" . $db->mysql_clean($_GET['dl']) . "'";	
		}
		if (! empty($_GET['date'])) {
			$special_where_clause .= " AND `date` LIKE '" . $db->mysql_clean($_GET['date']) . "%'";
		}
		if (! empty($_GET['user'])) {
			$special_where_clause .= " AND `user`='" . $db->mysql_clean($_GET['user']) . "'";	
		}
		if (! empty($_GET['ip'])) {
			$special_where_clause .= " AND `ip`='" . $db->mysql_clean($_GET['ip']) . "'";	
		}
		if (! empty($_GET['start'])) {
   			if (empty($_GET['end'])) {
				$special_where_clause .= " AND `date` LIKE '" . $db->mysql_clean($_GET['start']) . "%'";
   			} else {
   				$special_where_clause .= " AND (`date`>='" . $db->mysql_clean($_GET['start']) . "%' AND `date`<='" . $db->mysql_clean($_GET['end']) . "%')";
   			}
		}
	}
	
	// -----------------------------------------------------------------
	// 	Create a query for sorting user
	
	$page_name = "download_activity";
	$mysql_table = TABLE_PREFIX . "attachments_dls";
	$default_sort = "date";
	$default_dir = "DESC";
	$default_search = array("user");
	include "includes/run_page_sorting.php";

	// -----------------------------------------------------------------
	// 	Loop Results
	
	foreach ($return_results as $this_result) {
	
 		$q = "SELECT * FROM `" . TABLE_PREFIX . "attachments_dls` WHERE `id`='$this_result' LIMIT 1";
 		$download = $db->get_array($q);
 		
 		$this_download = $db->get_file_info('','',$download['dl']);
 		$user_id = $session->get_user_id($download['user']);
 		
	   	$list .= "<tr id=\"" . $download['id'] . "\">
	   	<td valign=\"top\"$class>" . $db->format_date($download['date']) . "</td>
	   	<td valign=\"top\"$class><a href=\"index.php?l=download_edit&id=" . $this_download['id'] . "\">" . $this_download['name'] . "</a></td>
	   	<td valign=\"top\"$class><a href=\"index.php?l=users_edit&id=" . $user_id . "\">" . $download['user'] . "</a></td>
	   	<td valign=\"top\"$class>" . $download['ip'] . "</td>
	   	<td valign=\"top\"$class><a href=\"#\" onClick=\"deleteID('$mysql_table','" . $this_result . "');return false;\"><img src=\"imgs/icon-delete.png\" border=\"0\" alt=\"Delete\" title=\"Delete\" class=\"icon_nopad\" /></a></td>
	   	</tr>";
	   	
	}
?>

<script type="text/javascript" language="JavaScript">
<!--
$(document).ready(function() {
	// call the tablesorter plugin
	$("#dl_list").tablesorter({
		// sort on the first column order desc
		sortList: [[0,1]],
		headers: { 3:{sorter: false}}
	});
});
-->
</script>

<div id="content_overlay">
	
	<div class="submit">
   		<a href="http://www.doyoubananadance.com/Media-Library/Downloadable-Content" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
	</div>
	
	<h1>Download Activity (<?php echo $queryInfo['count']; ?>)</h1>

		<?php
			include "sort_page_top.php";
		?>
		
		<div class="subpage_links" id="filters" style="<?php if (! empty($_GET['filter'])) { echo "display:block;"; } else { echo "display:none;"; } ?>">
			<div id="filters_top"></div>
			<div id="filters_body"><div id="filters_pad">
				<input type="hidden" name="filter" value="1" />
				<input type="hidden" name="clear" value="1" />
				<span class="heading">User:</span>
				<span><input type="text" name="user" value="<?php echo $_GET['user']; ?>" style="width:100px;" />
				<br />
				<span class="heading">Date Range (yyyy-mm-dd):</span>
				<span><input type="text" name="start" style="width:100px;" maxlength="10" value="<?php echo $_GET['start']; ?>" /></span>
				<span><input type="text" name="end" style="width:100px;" maxlength="10" value="<?php echo $_GET['end']; ?>" /></span>
				<span class="heading">Date:</span>
				<span><a href="index.php?<?php echo $get_string; ?>&filter=1&date=<?php echo date('Y-m-d'); ?>">Today</a></span>
				<span class="vertical">|</span>
				<span><a href="index.php?<?php echo $get_string; ?>&filter=1&start=<?php
				$seven_days_ago = time()-(86400*7);
				echo date('Y-m-d',$seven_days_ago);
				?>">Last 7 Days</a></span>
				<span class="vertical">|</span>
				<span><a href="index.php?<?php echo $get_string; ?>&filter=1&joined=<?php echo date('Y-m'); ?>">This Month</a></span>
				<span class="vertical">|</span>
				<span><a href="index.php?<?php echo $get_string; ?>&filter=1&joined=<?php echo date('Y'); ?>">This Year</a></span>
				<br />
				<span class="heading">Download ID</span>
				<span><input type="text" name="dl" style="width:100px;" maxlength="15" value="<?php echo $_GET['dl']; ?>" /></span>
			</div></div>
		</div>
		</form>
		
		<table cellspacing="0" cellpadding="0" id="dl_list" class="sort_table">
		<thead>
		<tr>
		<th>Date</th>
		<th>File</th>
		<th>User</th>
		<th>IP</th>
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