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
	$mysql_table = TABLE_PREFIX . "users";
	$default_sort = "username";
	$default_search = array("username");
	include "includes/run_page_sorting.php";

	// -----------------------------------------------------------------
	// 	Loop Results
	
	foreach ($return_results as $this_result) {
 		$this_user = $session->get_user_data('',$this_result);
 		
		// Styling for the user type
		$style = "";
		if (! empty($this_user['usertype']['color'])) {
			$style .= "background-color:#" . $this_user['usertype']['color'] . ";";
		}
		if (! empty($this_user['usertype']['font_color'])) {
			$style .= "color:#" . $this_user['usertype']['font_color'] . ";";
		}
		// Status
		if ($this_user['banned'] == "banned") {
			$show_status = "<img src=\"imgs/status-banned.png\" id=\"status" . $this_user['id'] . "\" width=16 height=16 border=\"0\" alt=\"Banned\" title=\"Banned\" />";
		}
		else if (! empty($this_user['banned'])) {
			$show_status = "<img src=\"imgs/status-off.png\" id=\"status" . $this_user['id'] . "\" width=16 height=16 border=\"0\" alt=\"Temporary Ban\" title=\"Temporary Ban\" />";
		}
		else {
			$show_status = "<img src=\"imgs/status-on.png\" id=\"status" . $this_user['id'] . "\" width=16 height=16 border=\"0\" alt=\"Active\" title=\"Active\" />";
		}
	   	$list .= "<tr id=\"" . $this_user['id'] . "\">
	   	<td valign=\"top\"$class><center>$show_status</center></td>
	   	<td valign=\"top\"$class><a href=\"index.php?l=users_edit&id=" . $this_user['id'] . "\">" . $this_user['username'] . "</a></td>
	   	<td valign=\"top\"$class>" . $db->format_date($this_user['joined']) . "</td>
	   	<td valign=\"top\"$class><span style=\"$style\">" . $this_user['usertype_name'] . "</span></td>
	   	<td valign=\"top\"$class>" . $this_user['score'] . "</td>
	   	<td valign=\"top\"$class>" . $this_user['comments'] . "</td>
	   	<td valign=\"top\"$class>" . $this_user['logins'] . "</td>
	   	<td valign=\"top\"$class><a href=\"#\" onClick=\"deleteID('$mysql_table','" . $this_user['id'] . "');return false;\"><img src=\"imgs/icon-delete.png\" border=\"0\" alt=\"Delete\" title=\"Delete\" /></a></td>
	   	</tr>";
	}
?>

<script type="text/javascript" language="JavaScript">
<!--
$(document).ready(function() {
	// call the tablesorter plugin
	$("#user_list").tablesorter({
		// sort on the first column order desc
		sortList: [[1,0]],
		headers: { 0:{sorter: false}, 7:{sorter: false}}
	});
});
-->
</script>

<div id="content_overlay">

   	<div class="submit">
   		<a href="http://www.doyoubananadance.com/Users/" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
   	</div>
   	
	<div id="actions_right">
		<ul>
			<li><a href="index.php?l=users_add<?php if (! empty($_GET['category'])) { echo "&category=" . $_GET['category']; } ?>">Create</a></li>
		</ul>
	</div>
	
	<h1>Listing Users (<?php echo $queryInfo['count']; ?>)</h1>

		<?php
			include "sort_page_top.php";
		?>
		
		<div class="subpage_links" id="filters" style="<?php if (! empty($_GET['filter'])) { echo "display:block;"; } else { echo "display:none;"; } ?>">
			<div id="filters_top"></div>
			<div id="filters_body"><div id="filters_pad">
				<span class="heading">User Types:</span>
				<span><a href="index.php?<?php echo $get_string; ?>">All</a></span>
				<?php
				$q = "SELECT id,name FROM `" . TABLE_PREFIX . "user_types` ORDER BY `name` ASC";
				$user_types = $db->run_query($q);
				while ($row = mysql_fetch_assoc($user_types)) {
					echo "<span class=\"vertical\">|</span>\n";
					echo "<span><a href=\"index.php?$get_string&filter=1&user_type=" . $row['id'] . "\">" . $row['name'] . "</a></span>\n";
				}
				?>
				<br />
				<span class="heading">Joined:</span>
				<span><a href="index.php?<?php echo $get_string; ?>&filter=1&joined=<?php echo date('Y-m-d'); ?>">Today</a></span>
				<span class="vertical">|</span>
				<span><a href="index.php?<?php echo $get_string; ?>&filter=1&start=<?php
				$seven_days_ago = time()-(86400*7);
				echo date('Y-m-d',$seven_days_ago);
				?>">Last 7 Days</a></span>
				<span class="vertical">|</span>
				<span><a href="index.php?<?php echo $get_string; ?>&filter=1&joined=<?php echo date('Y-m'); ?>">This Month</a></span>
				<span class="vertical">|</span>
				<span><a href="index.php?<?php echo $get_string; ?>&filter=1&joined=<?php echo date('Y'); ?>">This Year</a></span>
			</div></div>
		</div>
		
		
		<table cellspacing="0" cellpadding="0" id="user_list" class="sort_table">
		<thead>
		<tr>
		<th width="25">&nbsp;</th>
		<th>Username</th>
		<th>Joined</th>
		<th>Type</th>
		<th>Score</th>
		<th>Comments</th>
		<th>Logins</th>
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