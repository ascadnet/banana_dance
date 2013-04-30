<?php

if ($privileges['is_admin'] != "1") {
	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
} else {
	
	$template_list = $admin->get_template_list('array','0');
	$custom_template_list = $admin->get_template_list('array','1');
	
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
   		<a href="http://www.doyoubananadance.com/Templates/Email-Templates" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
   	</div>

	<h1>Custom E-Mail Templates</h1>
	
	<table cellspacing="0" cellpadding="0" id="templates_custom" class="sort_table">
	<thead>
	<tr>
	<th width="25">&nbsp;</th>
	<th>Name</th>
	<th>Description</th>
	<th>Details</th>
	<th width="25">&nbsp;</th>
	</tr>
	</thead> 
	<tbody>
	<?php
	
	foreach ($custom_template_list as $template) {
		$show_status = "<a href=\"#\" onclick=\"switchStatus('" . TABLE_PREFIX . "templates','" . $template['id'] . "','status');\">";
		if ($template['status'] == "1") {
			$show_status .= "<img src=\"imgs/status-on.png\" id=\"status" . $template['id'] . "\" width=16 height=16 border=\"0\" alt=\"Active\" title=\"Active\" />";
		} else {
			$show_status .= "<img src=\"imgs/status-off.png\" id=\"status" . $template['id'] . "\" width=16 height=16 border=\"0\" alt=\"Inactive\" title=\"Inactive\" />";
		}
		$show_status .= "</a>";
		echo "<tr id=\"" . $template['id'] . "\">\n";
		echo "<td class=\"center\" width=25 valign=\"top\">$show_status</td>\n";
		echo "<td valign=\"top\"><a href=\"index.php?l=templates_email_edit&id=" . $template['id'] . "\">" . $template['title'] . "</a></td>\n";
		echo "<td valign=\"top\">" . $template['desc'] . "</td>\n";
		echo "<td valign=\"top\">Created on " . $db->format_date($template['created']) . " by " . $template['created_by'] . "</td>\n";
		echo "<td class=\"center\" valign=\"top\"><a href=\"#\" onClick=\"deleteID('" . TABLE_PREFIX . "templates','" . $template['id'] . "');\"><img src=\"imgs/icon-delete.png\" border=\"0\" alt=\"Delete\" title=\"Delete\" /></a></td>\n";
		echo "</tr>\n";
	}
	
	?>
	</tbody>
	</table>
	
	
	<h1>Default E-Mail Templates</h1>
	
	<table cellspacing="0" cellpadding="0" id="templates_standard" class="sort_table">
	<thead>
	<tr>
	<th width="25">&nbsp;</th>
	<th>Name</th>
	<th>Description</th>
	<th width="25">&nbsp;</th>
	</tr>
	</thead> 
	<tbody>
	<?php
	
	foreach ($template_list as $template) {
		$show_status = "<a href=\"#\" onclick=\"switchStatus('" . TABLE_PREFIX . "templates','" . $template['id'] . "','status');\">";
		if ($template['status'] == "1") {
			$show_status .= "<img src=\"imgs/status-on.png\" id=\"status" . $template['id'] . "\" width=16 height=16 border=\"0\" alt=\"Active\" title=\"Active\" />";
		} else {
			$show_status .= "<img src=\"imgs/status-off.png\" id=\"status" . $template['id'] . "\" width=16 height=16 border=\"0\" alt=\"Inactive\" title=\"Inactive\" />";
		}
		$show_status .= "</a>";
		echo "<tr id=\"" . $template['id'] . "\">\n";
		echo "<td class=\"center\" width=25 valign=\"top\">$show_status</td>\n";
		echo "<td valign=\"top\"><a href=\"index.php?l=templates_email_edit&id=" . $template['id'] . "\">" . $template['title'] . "</a></td>\n";
		echo "<td valign=\"top\">" . $template['desc'] . "</td>\n";
		echo "<td class=\"center\" valign=\"top\"><a href=\"index.php?l=templates_email_add&id=" . $template['id'] . "\"><img src=\"imgs/icon-add.png\" border=\"0\" alt=\"New\" title=\"New\" /></a></td>\n";
		echo "</tr>\n";
	}
	
	?>
	</tbody>
	</table>

</div>

<?php
}
?>