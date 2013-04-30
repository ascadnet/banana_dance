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
	}
	
	// -----------------------------------------------------------------
	// 	Create a query for sorting user
	
	$page_name = "categories";
	$mysql_table = TABLE_PREFIX . "categories";
	$default_sort = "name";
	$default_search = array("name");
	include "includes/run_page_sorting.php";

	// -----------------------------------------------------------------
	// 	Loop Results
	
	foreach ($return_results as $this_result) {
 		$this_category = $manual->get_category($this_result,'1');
		$current++;
		if ($odd = $current%2) {
			$class = " class=\"odd\"";
		} else {
			$class = "";
		}
		// Category
		if ($this_category['subcat'] != "0") {
			$sub_category = $manual->get_category_name_from_id($this_category['subcat']);
		} else {
			$sub_category = "<i>Base Category</i>";
		}
		// Home Article
		if (! empty($this_category['home_article'])) {
 			$home_article = $manual->get_article($this_category['home_article'],'1');
 			$show_article = "<a href=\"index.php?l=article_edit&id=" . $home_article['id'] . "\">" . $home_article['name'] . "</a>";
		} else {
 			$show_article = "<i>Not Set</i>";
		}
		// List it
		if ($this_category['base'] == '1') {
		   	$list .= "<tr id=\"" . $this_category['id'] . "\">
		   	<td valign=\"top\"$class><a href=\"index.php?l=category_edit&id=0\">" . $this_category['name'] . "</a></td>
		   	<td valign=\"top\"$class><i>This is your base category.</i></td>
		   	<td valign=\"top\"$class>\"<i>$show_article</i>\"</td>
		   	<td valign=\"top\"$class>" . $this_category['articles'] . "</td>
		   	<td valign=\"top\"$class>&nbsp;</td>
		   	</tr>";
		} else {
		   	$list .= "<tr id=\"" . $this_category['id'] . "\">
		   	<td valign=\"top\"$class><a href=\"index.php?l=category_edit&id=" . $this_category['id'] . "\">" . $this_category['name'] . "</a></td>
		   	<td valign=\"top\"$class><a href=\"index.php?l=category_edit&id=" . $this_category['subcat'] . "\">" . $sub_category . "</a></td>
		   	<td valign=\"top\"$class>\"<i>$show_article</i>\"</td>
		   	<td valign=\"top\"$class>" . $this_category['articles'] . "</td>
		   	<td valign=\"top\"$class><center><a href=\"#\" onClick=\"deleteID('$mysql_table','" . $this_category['id'] . "');return false;\"><img src=\"imgs/icon-delete.png\" border=\"0\" alt=\"Delete\" title=\"Delete\" /></a></center></td>
		   	</tr>";
		}
	}
?>

<script type="text/javascript" language="JavaScript">
<!--
$(document).ready(function() {
	// call the tablesorter plugin
	$("#article_list").tablesorter({
		// sort on the first column order desc
		sortList: [[1,0]],
		headers: { 4:{sorter: false} }
	});
});
-->
</script>

<div id="content_overlay">

	<div class="submit">
		<a href="http://www.doyoubananadance.com/Pages/Categories" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
	</div>
	
	<div id="actions_right">
		<ul>
			<li><a href="index.php?l=category_add<?php if (! empty($_GET['category'])) { echo "&category=" . $_GET['category']; } ?>">Create</a></li>
		</ul>
	</div>

	<h1>Listing Categories (<?php echo $queryInfo['count']; ?>)</h1>

		<?php
			include "sort_page_top.php";
		?>
		<form action="index.php" method="get">
		<div class="subpage_links" id="filters" style="<?php if (! empty($_GET['filter'])) { echo "display:block;"; } else { echo "display:none;"; } ?>">
			<div id="filters_top"></div>
			<div id="filters_body"><div id="filters_pad"><i>N/A</i></div></div>
		</div>
		</form>
		
		<table cellspacing="0" cellpadding="0" id="article_list" class="sort_table">
		<thead>
		<tr>
		<th>Name</th>
		<th>Sub-Category Of</th>
		<th>Homepage</th>
		<th width="35"><img src="imgs/icon-articles.png" width=16 height=16 border="0" alt="Pages" title="Pages" /></th>
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