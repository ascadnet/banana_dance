<?php

if ($privileges['is_admin'] != "1") {
	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
} else {
	
	$themes = $admin->get_theme_list('array');
	
?>

<script type="text/javascript" language="JavaScript">
<!--
$(document).ready(function() {
	// call the tablesorter plugin
	$("#themes_custom").tablesorter({
		// sort on the first column order desc
		sortList: [[1,0]],
		headers: { 0:{sorter: false}, 4:{sorter: false}}
	});
});
-->
</script>

<div id="content_overlay">


	<div class="submit">
   		<a href="http://www.doyoubananadance.com/Themes/" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
   		<div class="submit_split"></div>
   		<a href="http://www.doyoubananadance.com/Download/Theme-Library" target="_blank"><img src="imgs/icon-download.png" width="16" height="16" border="0" title="Download New" alt="Download New" /></a>
	</div>
	

	<div id="actions_right">
		<ul>
			<li><a href="index.php?l=themes_mobile">Switch to Mobile Themes</a></li>
		</ul>
	</div>
	
	<h1>Themes</h1>
	
		<table cellspacing="0" cellpadding="0" id="plugins_custom" class="sort_table">
		<thead>
		<tr>
		<th width="25">&nbsp;</th>
		<th width="150">Screenshot</th>
		<th>Theme Details</th>
		</tr>
		</thead> 
		<tbody>
		<?php
		
		foreach ($themes as $aTheme) {
			// Status
			$show_status = "<a href=\"#\" onclick=\"setTheme('" . $aTheme['folder_name'] . "','1');\">";
			if ($aTheme['selected'] == "1") {
				$show_status .= "<img src=\"imgs/status-on.png\" id=\"status" . $aTheme['folder_name'] . "\" class=\"theme_status\" width=16 height=16 border=\"0\" alt=\"Active\" title=\"Active\" />";
			} else {
				$show_status .= "<img src=\"imgs/status-off.png\" id=\"status" . $aTheme['folder_name'] . "\" class=\"theme_status\" width=16 height=16 border=\"0\" alt=\"Inactive\" title=\"Inactive\" />";
			}
			$show_status .= "</a>";
			// Type
			if ($aTheme['type'] == 'cms') {
				$final_type = 'CMS';
			}
			else if ($aTheme['type'] == 'combo') {
				$final_type = 'Combo Wiki/CMS';
			}
			else {
				$final_type = 'Wiki';
			}
			
			// Screen?
			$screen = PATH . "/templates/html/" . $aTheme['folder_name'] . "/_author/screen.jpg";
	   		if (file_exists($screen)) {
	   			$link = URL . "/templates/html/" . $aTheme['folder_name'] . "/_author/screen.jpg";
				$screen = "<a href=\"$link\" target=\"_blank\" /><img src=\"$link\" width=\"133\" height=\"106\" border=\"0\" alt=\"Screen shot\" title=\"Screen shot\" /></a>";
			} else {
				$screen = "N/A";
			}
			// Developer
			if (! empty($aTheme['author_url'])) {
				$final_dev = "<a href=\"" . $aTheme['author_url'] . "\" target=\"_blank\">" . $aTheme['author'] . "</a>";
			} else {
				$final_dev = $aTheme['author'];
			}
			// Table Entry
			echo "<tr id=\"" . $aTheme['name'] . "\">\n";
			echo "<td class=\"center\" width=25 valign=\"top\">$show_status</td>\n";
			echo "<td valign=\"top\">$screen</td>\n";
			echo "<td valign=\"top\">
				<h3 style=\"margin: 12px 0 4px 0;\">" . $aTheme['name'] . "</h3>
				<p class=\"small\">Type: $final_type by $final_dev</p>";
				if (! empty($aTheme['description'])) {
					echo "<p style=\"margin-top: 12px;\" class=\"desc\">" . $aTheme['description'] . "</p>";
				}
			echo "<p style=\"margin: 12px 0 12px 0;\" class=\"small\"><a href=\"functions/import_template_imgs.php?type=theme&theme=" . $aTheme['folder_name'] . "\">Import Images to Media Gallery</a></p>";
			echo "</td>\n";
			echo "</tr>\n";
		}
		
		?>
		</tbody>
		</table>
		
</div>

<?php
}
?>