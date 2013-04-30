<?php

if ($privileges['is_admin'] != "1") {
	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
} else {
	
	$widget_list = $admin->get_widget_list('array');
	
	// Get widget names
	include "widget_names.php";
	
?>

<script type="text/javascript" language="JavaScript">
<!--
$(document).ready(function() {
	// call the tablesorter plugin
	$("#templates_custom").tablesorter({
		// sort on the first column order desc
		sortList: [[1,0]],
		headers: { 0:{sorter: false}, 4:{sorter: false}}
	});
	$("#templates_standard").tablesorter({
		// sort on the first column order desc
		sortList: [[1,0]],
		headers: { 0:{sorter: false}, 3:{sorter: false}}
	});
});
-->
</script>

<div id="content_overlay">


   	<div class="submit">
   		<a href="http://www.doyoubananadance.com/Pages/Widgets" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
   	</div>
   	
   	
	<div id="actions_right">
		<ul>
			<li><a href="index.php?l=widgets_add">Create</a></li>
			<li><a href="index.php?l=plugins">Plugins</a></li>
		</ul>
	</div>
	
	<h1>Widgets</h1>
	
	<p class="attention">The following list allows you to activate, de-activate, and delete widgets.<br />To edit a widget, please use the inline widget editor from in the page editor.</p>
	
	<div class="white_box drop_shadow"><div class="col_pad">
	<table cellspacing="0" cellpadding="0" id="templates_custom" class="sort_table">
	<thead>
	<tr>
	<th width="25">&nbsp;</th>
	<th>Name</th>
	<th width="75"><span id="tag_id" class="search_hover">Tag</span><span class="hide" id="tag_id_text">Copy and paste this into any page or template to make the widget appear!</span></th>
	<th>Type</th>
	<th>Details</th>
	<th width="25">&nbsp;</th>
	</tr>
	</thead> 
	<tbody>
	<?php
	
	foreach ($widget_list as $widget) {
		if ($widget['type'] != '5') {
			$show_status = "<a href=\"#\" onclick=\"switchStatus('" . TABLE_PREFIX . "widgets','" . $widget['id'] . "','active');\">";
			if ($widget['active'] == "1") {
				$show_status .= "<img src=\"imgs/status-on.png\" id=\"status" . $widget['id'] . "\" width=16 height=16 border=\"0\" alt=\"Active\" title=\"Active\" />";
			} else {
				$show_status .= "<img src=\"imgs/status-off.png\" id=\"status" . $widget['id'] . "\" width=16 height=16 border=\"0\" alt=\"Inactive\" title=\"Inactive\" />";
			}
			$show_status .= "</a>";
			echo "<tr id=\"" . $widget['id'] . "\">\n";
			echo "<td class=\"center\" width=25 valign=\"top\">$show_status</td>\n";
			echo "<td valign=\"top\">" . $widget['name'] . "</td>\n";
			echo "<td class=\"center\" width=25 valign=\"top\">{-" . $widget['id'] . "-}</td>\n";
			echo "<td valign=\"top\">" . $widget_names[$widget['type']] . "</td>\n";
			echo "<td valign=\"top\">Created on " . $db->format_date($widget['date']) . " by " . $widget['owner'] . "</td>\n";
			echo "<td class=\"center\" valign=\"top\"><a href=\"#\" onClick=\"deleteID('" . TABLE_PREFIX . "widgets','" . $widget['id'] . "');\"><img src=\"imgs/icon-delete.png\" border=\"0\" alt=\"Delete\" title=\"Delete\" /></a></td>\n";
			echo "</tr>\n";
		}
	}
	
	?>
	</tbody>
	</table>
	</div></div>

</div>

<?php
}
?>