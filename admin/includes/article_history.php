<?php

if ($privileges['is_admin'] != "1") {
	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
} else {

	// -----------------------------------------------------------------
	// 	Create a query for sorting user
	
	$page_name = "article_history";
	$mysql_table = TABLE_PREFIX . "articles_history";
	$special_where_clause = "`article_id`='" . $db->mysql_clean($_GET['id']) . "'";
	$default_sort = "date";
	$default_search = array("date");
	include "includes/run_page_sorting.php";

	$article = $manual->get_article($_GET['id'],'1','name');

	// -----------------------------------------------------------------
	// 	Loop Results
	
	foreach ($return_results as $this_result) {
	
		$revision = $manual->get_revision($this_result);
		
		$list .= "<tr id=\"" . $revision['id'] . "\">
		<td valign=\"top\"><a href=\"" . URL . "/revision/" . $revision['id'] . "\">" . $db->format_date($revision['date']) . "</a></td>
		<td valign=\"top\"><a href=\"index.php?l=users_edit&id=" . $revision['user'] . "\">" . $revision['user'] . "</a></td>
		<td valign=\"top\">" . $revision['ip'] . "</td>
		<td valign=\"top\">" . $revision['name'] . "</td>
		<td width=\"40\" valign=\"top\"><center><a href=\"#\" onClick=\"revertPage('" . $revision['id'] . "');return false;\"><img src=\"imgs/icon-revert.png\" border=\"0\" alt=\"Revert to this version\" title=\"Revert to this version\" class=\"icon_less\" /></a><a href=\"#\" onClick=\"deleteID('$mysql_table','" . $revision['id'] . "');return false;\"><img src=\"imgs/icon-delete.png\" border=\"0\" alt=\"Delete\" title=\"Delete\" class=\"icon_nopad\" /></a></center></td>
		</tr>";
	
	}
?>

<script type="text/javascript" language="JavaScript">
<!--
$(document).ready(function() {
	// call the tablesorter plugin
	$("#article_list").tablesorter({
		// sort on the first column order desc
		sortList: [[0,0]],
		headers: { 4:{sorter: false} }
	});
});
-->
</script>

<div id="content_overlay">

   	<div class="submit">
   		<a href="http://www.doyoubananadance.com/Pages/" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
   	</div>
   	
	<h1>History of Changes for "<?php echo $article['name']; ?>" (Revisions = <?php echo $queryInfo['count']; ?>)</h1>

   	<table cellspacing="0" cellpadding="0" id="article_list" class="sort_table">
   	<thead>
   	<tr>
   	<th>Date</th>
   	<th>By</th>
   	<th>IP</th>
   	<th>Page Name</th>
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