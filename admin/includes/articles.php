<?php

if ($privileges['is_admin'] != "1") {
	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
} else {

	// -----------------------------------------------------------------
	// 	Filter Considerations?
	
	if ($_GET['filter'] == "1") {
		if (! empty($_GET['public'])) {
			if ($_GET['public'] == "1") { $public_setting = "1"; }
			else { $public_setting = "0"; }
			$special_where_clause .= " AND `public`='" . $public_setting . "'";
		}
		if (! empty($_GET['category'])) {
			$special_where_clause .= " AND `category`='" . $db->mysql_clean($_GET['category']) . "'";
		}
		if (! empty($_GET['owner'])) {
			$special_where_clause .= " AND `owner`='" . $db->mysql_clean($_GET['owner']) . "'";
		}
	}
	
	// -----------------------------------------------------------------
	// 	Create a query for sorting user
	
	$page_name = "articles";
	$mysql_table = TABLE_PREFIX . "articles";
	$default_sort = "name";
	$default_search = array("name");
	include "includes/run_page_sorting.php";

	// -----------------------------------------------------------------
	// 	Loop Results
	
	foreach ($return_results as $this_result) {
 		$this_article = $manual->get_article($this_result);
		// Prepare link
		$get_link = $manual->prepare_link($this_article['id'],$this_article['category'],$this_article['name']);
		// Public?
		$show_status = "<a href=\"#\" onclick=\"switchStatus('" . TABLE_PREFIX . "articles','" . $this_article['id'] . "','public');\">";
		if ($this_article['public'] == "1") {
			$show_status .= "<img src=\"imgs/status-on.png\" id=\"status" . $this_article['id'] . "\" width=16 height=16 border=\"0\" alt=\"Public\" title=\"Public\" />";
		}
		else {
			$show_status .= "<img src=\"imgs/status-off.png\" id=\"status" . $this_article['id'] . "\" width=16 height=16 border=\"0\" alt=\"Private\" title=\"Private\" />";
		}
		$show_status .= "</a>";
		// Category
		if ($this_article['category'] == "0") {
			$category = "Base";
		} else {
			$category = $manual->get_category_name_from_id($this_article['category']);
		}
		// Score
		if ($this_article['allow_ratings'] == '1') {
			$score = $this_article['upvoted'] - $this_article['downvoted'];
		} else {
			$score = '-';
		}
		
		// Commenting
		if ($this_article['allow_comments'] == '1') {
			$show_comments = "<a href=\"index.php?l=comments&filter=1&article=" . $this_article['id'] . "\">" . $this_article['comments'] . "</a>";
		} else {
			$show_comments = "-";
		}
		// List it
	   	$list .= "<tr id=\"" . $this_article['id'] . "\">
	   	<td valign=\"top\"><center>$show_status</center></td>
	   	<td valign=\"top\"><center>" . $this_article['id'] . "</center></td>
	   	<td valign=\"top\" onmouseover=\"show('arf" . $this_article['id'] . "');\" onmouseout=\"hide('arf" . $this_article['id'] . "');\">
	   		<a href=\"index.php?l=article_edit&id=" . $this_article['id'] . "\">" . $this_article['name'] . "</a>
	   		<div id=\"arf" . $this_article['id'] . "\" style=\"float:right;display:none;\">
	   			<a href=\"index.php?l=article_history&id=" . $this_article['id'] . "\"><img src=\"imgs/icon-history.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"Page History\" title=\"Page History\" class=\"icon_less\" /></a>
	   			<a href=\"" . $get_link . "\"><img src=\"imgs/icon-view.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"View Public Page\" title=\"View Public Page\" class=\"icon_nopad\" /></a>
	   		</div>
	   	</td>
	   	<td valign=\"top\"><a href=\"index.php?l=users_edit&id=" . $this_article['owner'] . "\">" . $this_article['owner'] . "</a></td>
	   	<td valign=\"top\">" . $db->format_date($this_article['last_updated']) . "</td>
	   	<td valign=\"top\"><a href=\"index.php?l=category_edit&id=" . $this_article['category'] . "\">" . $category . "</a></td>
	   	<td valign=\"top\">" . $this_article['views'] . "</td>
	   	<td valign=\"top\">$show_comments</td>
	   	<td valign=\"top\">" . $score . "</td>
	   	<td valign=\"top\"><center><a href=\"#\" onClick=\"deleteID('$mysql_table','" . $this_article['id'] . "');return false;\"><img src=\"imgs/icon-delete.png\" border=\"0\" alt=\"Delete\" title=\"Delete\" /></a></center></td>
	   	</tr>";
	}
?>

<script type="text/javascript" language="JavaScript">
<!--
$(document).ready(function() {
	// call the tablesorter plugin
	$("#article_list").tablesorter({
		// sort on the first column order desc
		sortList: [[2,0]],
		headers: { 0:{sorter: false}, 9:{sorter: false}}
	});
});
-->
</script>

<div id="content_overlay">

   	<div class="submit">
   		<a href="http://www.doyoubananadance.com/Pages/" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
   	</div>
   	
	<h1>Listing Pages (<?php echo $queryInfo['count']; ?>)</h1>

		<?php
			include "sort_page_top.php";
		?>
		
		<form action="index.php" method="get">
		<input type="hidden" name="filter" value="1" />
   		<?php
   		echo $fields_list;
   		?>
		<div class="subpage_links" id="filters" style="<?php if (! empty($_GET['filter'])) { echo "display:block;"; } else { echo "display:none;"; } ?>">
			<div id="filters_top"></div>
			<div id="filters_body"><div id="filters_pad">
				<span class="heading">Category:</span>
					<select name="category" id="category" style="width:350px;">
					<?php
					$categories = $manual->category_select($_GET['category']);
					echo $categories;
					?>
					</select> <button type="submit" value="Go" class="small_button">Go</button>
				<br />
				<span class="heading">Status:</span>
				<span><a href="index.php?<?php echo $full_get_string; ?>&filter=1&public=1">Public</a></span>
				<span class="vertical">|</span>
				<span><a href="index.php?<?php echo $full_get_string; ?>&filter=1&public=2">Private</a></span>
			</div></div>
		</div>
		</form>
		
		<table cellspacing="0" cellpadding="0" id="article_list" class="sort_table">
		<thead>
		<tr>
		<th width="20">&nbsp;</th>
		<th>ID</th>
		<th>Name</th>
		<th>Author</th>
		<th>Last Updated</th>
		<th>Category</th>
		<th width="45"><img src="imgs/icon-views.png" width=16 height=16 border="0" alt="Views" title="Views" /></th>
		<th width="30"><img src="imgs/icon-comments.png" width=16 height=16 border="0" alt="Comments" title="Comments" /></th>
		<th width="30"><img src="imgs/icon-score.png" width=16 height=16 border="0" alt="Score" title="Score" /></th>
		<th width="20">&nbsp;</th>
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