<?php

if ($privileges['is_admin'] != "1") {
	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
} else {
	
	$plugins = $admin->get_plugin_list('array');
	
?>

<script type="text/javascript" language="JavaScript">
<!--
$(document).ready(function() {
	// call the tablesorter plugin
	$("#plugins_custom").tablesorter({
		// sort on the first column order desc
		sortList: [[1,0]],
		headers: { 0:{sorter: false}, 4:{sorter: false}}
	});
});
-->
</script>

<div id="content_overlay">

   	<div class="submit">
   		<a href="http://www.doyoubananadance.com/Widgets-and-Plugins/Creating-Plugins" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
   		<div class="submit_split"></div>
   		<a href="http://www.doyoubananadance.com/Download/Plugin-Library" target="_blank"><img src="imgs/icon-download.png" width="16" height="16" border="0" title="Download New" alt="Download New" /></a>
   	</div>


	<div id="actions_right">
		<ul>
			<li><a href="index.php?l=widgets">Widgets</a></li>
		</ul>
	</div>
	
	<h1>Plugins</h1>
	
	<p><b>Tip:</b> You must activate the plugin before using it.</p>
	
	<table cellspacing="0" cellpadding="0" id="plugins_custom" class="sort_table">
	<thead>
	<tr>
	<th width="25">&nbsp;</th>
	<th>Name</th>
	<th width="75"><span id="tag_id" class="search_hover">Tag</span><span class="hide" id="tag_id_text">Copy and paste this into any article or template to make the widget appear!</span></th>
	<th>Developer</th>
	<th>Description</th>
	<th width="50">&nbsp;</th>
	</tr>
	</thead> 
	<tbody>
	<?php
	
	foreach ($plugins as $aPlugin) {
		$options = unserialize($aPlugin['options']);
		if (! empty($options['developer'])) {
			if (! empty($options['developer_url'])) {
				$final_dev = "<a href=\"" .  $options['developer_url'] . "\" target=\"_blank\">" . $options['developer'] . "</a>";
			} else {
				$final_dev = $options['developer'];
			}
		}
		// Name and version
		$show_name = "<a href=\"index.php?l=plugins_options&plugin=" . $aPlugin['filename'] . "\">" . $aPlugin['name'];
		if (! empty($options['version'])) {
			$show_name .= " v" . ltrim($options['version'],'v');
		}
		$show_name .= "</a>";
		// Status
		$show_status = "<a href=\"#\" onclick=\"switchStatus('" . TABLE_PREFIX . "widgets','" . $aPlugin['id'] . "','active');\">";
		if ($aPlugin['active'] == "1") {
			$show_status .= "<img src=\"imgs/status-on.png\" id=\"status" . $aPlugin['id'] . "\" width=16 height=16 border=\"0\" alt=\"Active\" title=\"Active\" />";
		} else {
			$show_status .= "<img src=\"imgs/status-off.png\" id=\"status" . $aPlugin['id'] . "\" width=16 height=16 border=\"0\" alt=\"Inactive\" title=\"Inactive\" />";
		}
		$show_status .= "</a>";
		// Readme?
		$readme_loc = PATH . "/addons/widgets/" . $aPlugin['filename'] . "/README";
		if (file_exists($readme_loc)) {
			/*
			$readme_loc = URL . "/addons/widgets/" . $aPlugin['filename'] . "/README";
			$readme_contents = trim(file_get_contents($readme_loc));
			$readme_contents = nl2br($readme_contents);
			$readme_contents = str_replace("\t","  ",$readme_contents);
			$readme_contents = str_replace("\n","",$readme_contents);
			$readme = " (<a href=\"#\" onclick=\"admin_directions('$readme_contents');\">Instructions</a>)";
			*/
			$admin_path = URL . "/addons/widgets/" . $aPlugin['filename'] . "/README";
			$readme = " (<a href=\"$admin_path\" target=\"_blank\">Instructions</a>)";
		} else {
			$readme = '';
		}
		// Table Entry
		echo "<tr id=\"" . $aPlugin['id'] . "\">\n";
		echo "<td class=\"center\" width=25 valign=\"top\">$show_status</td>\n";
		echo "<td valign=\"top\">$show_name</td>\n";
		echo "<td class=\"center\" width=25 valign=\"top\">{-" . $aPlugin['id'] . "-}</td>\n";
		echo "<td valign=\"top\">" . $final_dev . "</td>\n";
		echo "<td valign=\"top\">" . $options['desc'] . "$readme</td>\n";
		echo "<td class=\"center\" valign=\"top\"><a href=\"#\" onClick=\"deleteID('" . TABLE_PREFIX . "widgets','" . $widget['id'] . "');\"><img src=\"imgs/icon-delete.png\" border=\"0\" alt=\"Delete\" title=\"Delete\" /></a></td>\n";
		echo "</tr>\n";
	}
	
	?>
	</tbody>
	</table>

</div>

<?php
}
?>