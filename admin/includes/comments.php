<?php

if ($privileges['is_admin'] != "1") {
	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
} else {

	// -----------------------------------------------------------------
	// 	Filter Considerations?
	
	if ($_GET['filter'] == "1") {
		if (! empty($_GET['pending'])) {
			if ($_GET['pending'] == "1") {
				$special_where_clause .= " AND `pending`='1'";
			} else {
				$special_where_clause .= " AND `pending`='0'";
			}
		}
		if (! empty($_GET['article'])) {
			$special_where_clause .= " AND `article`='" . $db->mysql_clean($_GET['article']) . "'";
		}
		if (! empty($_GET['user'])) {
			$special_where_clause .= " AND `user`='" . $db->mysql_clean($_GET['user']) . "'";
		}
		if (! empty($_GET['type'])) {
			if ($_GET['type'] == "x") {
				$special_where_clause .= " AND `status`='0'";
			} else {
				$special_where_clause .= " AND `status`='" . $db->mysql_clean($_GET['type']) . "'";
			}
		}
		if (! empty($_GET['posted'])) {
			$special_where_clause .= " AND `date` LIKE '" . $db->mysql_clean($_GET['posted']) . "%'";
		}
		if (! empty($_GET['start'])) {
   			if (empty($_GET['end'])) {
   				$_GET['end'] = date('Y-m-d');
   			}
   			$special_where_clause .= " AND (`date`>='" . $db->mysql_clean($_GET['start']) . "%' AND `date`<='" . $db->mysql_clean($_GET['end']) . "%')";
		}
		if (! empty($_GET['score'])) {
			$special_where_clause .= " AND (`uo`-`down`)>='" . $db->mysql_clean($_GET['score']) . "'";
		}
	}
	
	// -----------------------------------------------------------------
	// 	Create a query for sorting user
	
	$page_name = "comments";
	$mysql_table = TABLE_PREFIX . "comments";
	$default_sort = "user";
	$default_search =  array("user");
	include "includes/run_page_sorting.php";

	// -----------------------------------------------------------------
	// 	Loop Results
	
	foreach ($return_results as $this_result) {
 		$this_comment = $manual->get_a_comment($this_result);
		// Status
		$show_status = "<a href=\"#\" onclick=\"switchStatus('" . TABLE_PREFIX . "comments','" . $this_comment['id'] . "','pending');\">";
		if ($this_comment['pending'] == "1") {
			$show_status .= "<img src=\"imgs/status-off.png\" id=\"status" . $this_comment['id'] . "\" width=16 height=16 border=\"0\" alt=\"Pending\" title=\"Pending\" />";
		}
		else {
			$show_status .= "<img src=\"imgs/status-on.png\" id=\"status" . $this_comment['id'] . "\" width=16 height=16 border=\"0\" alt=\"Approved\" title=\"Approved\" />";
		}
		$show_status .= "</a>";
		// Comment Type
		if ($this_comment['status'] == "0") {
			$comment_type = "<i>N/A</i>";
		} else {
			$status_settings = $manual->get_status_settings($this_comment['status']);
			$comment_type = $status_settings['title'];
		}
		// Article
 		$home_article = $manual->get_article($this_comment['article'],'1');
 		$show_article = "<a href=\"index.php?l=article_edit&id=" . $home_article['id'] . "\">" . $home_article['name'] . "</a>";
 		
	   	$list .= "<tr id=\"" . $this_comment['id'] . "\">
	   	<td valign=\"top\"$class><center>$show_status</center></td>
	   	<td valign=\"top\"$class><a href=\"index.php?l=comment&id=" . $this_comment['id'] . "\">" . $db->format_date($this_comment['date']) . "</a></td>
	   	<td valign=\"top\"$class><a href=\"index.php?l=users_edit&id=" . $this_comment['user'] . "\">" . $this_comment['user'] . "</a></td>
	   	<td valign=\"top\"$class>Posted to article $show_article<br /><br /><span class=small>" . wordwrap($manual->format_comment($this_comment['comment']),75,'<br />') . "</span></td>
	   	<td valign=\"top\"$class>" . $this_comment['score'] . "</td>
	   	<td valign=\"top\"$class>" . $comment_type . "</td>
	   	<td valign=\"top\"$class><a href=\"#\" onClick=\"deleteID('$mysql_table','" . $this_comment['id'] . "');return false;\"><img src=\"imgs/icon-delete.png\" border=\"0\" alt=\"Delete\" title=\"Delete\" /></a></td>
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
		headers: { 0:{sorter: false}, 3:{sorter: false}, 6:{sorter: false}}
	});
});
-->
</script>

<div id="content_overlay">

	<div class="submit">
   		<a href="http://www.doyoubananadance.com/Comments/" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
	</div>
	
	<h1>Listing Comments (<?php echo $queryInfo['count']; ?>)</h1>

		<?php
			include "sort_page_top.php";
		?>
		
		<div class="subpage_links" id="filters" style="<?php if (! empty($_GET['filter'])) { echo "display:block;"; } else { echo "display:none;"; } ?>">
			<div id="filters_top"></div>
			<div id="filters_body"><div id="filters_pad">
				<span class="heading">Status:</span>
				<span><a href="index.php?<?php echo $get_string; ?>">All</a></span>
				<span class="vertical">|</span>
				<span><a href="index.php?<?php echo $get_string; ?>&filter=1&pending=2">Approved</a></span>
				<span class="vertical">|</span>
				<span><a href="index.php?<?php echo $get_string; ?>&filter=1&pending=1">Pending</a></span><br />
				<span class="heading">Comment Type:</span>
				<span><a href="index.php?<?php echo $get_string; ?>&filter=1&type=x">Standard</a></span>
				<?php
				$q = "SELECT id,title FROM `" . TABLE_PREFIX . "comment_statuses` ORDER BY `title` ASC";
				$user_types = $db->run_query($q);
				while ($row = mysql_fetch_assoc($user_types)) {
					echo "<span class=\"vertical\">|</span>\n";
					echo "<span><a href=\"index.php?$get_string&filter=1&type=" . $row['id'] . "\">" . $row['title'] . "</a></span>\n";
				}
				?>
				<br />
				<span class="heading">Posted:</span>
				<span><a href="index.php?<?php echo $get_string; ?>&filter=1&posted=<?php echo date('Y-m-d'); ?>">Today</a></span>
				<span class="vertical">|</span>
				<span><a href="index.php?<?php echo $get_string; ?>&filter=1&start=<?php
				$seven_days_ago = time()-(86400*7);
				echo date('Y-m-d',$seven_days_ago);
				?>">Last 7 Days</a></span>
				<span class="vertical">|</span>
				<span><a href="index.php?<?php echo $get_string; ?>&filter=1&posted=<?php echo date('Y-m'); ?>">This Month</a></span>
				<span class="vertical">|</span>
				<span><a href="index.php?<?php echo $get_string; ?>&filter=1&posted=<?php echo date('Y'); ?>">This Year</a></span>
			</div></div>
		</div>
		
		
		<table cellspacing="0" cellpadding="0" id="user_list" class="sort_table">
		<thead>
		<tr>
		<th width="25">&nbsp;</th>
		<th>Date</th>
		<th>Username</th>
		<th>Comment</th>
		<th width="35"><img src="imgs/icon-score.png" width="16" height="16" border="0" alt="Score" title="Score"></th>
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