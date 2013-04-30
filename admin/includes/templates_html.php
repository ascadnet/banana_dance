<?php

if ($privileges['is_admin'] != "1") {
	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
} else {
	
	$scan = $admin->scan_new_template_files();
	
	$template_list = $admin->get_template_html_list('array');
	$template_list_custom = $admin->get_template_html_list('array','1');
	
?>

<div id="content_overlay">
	
	<h1>HTML Templates</h1>
	
	<div class="submit">
			
		<?php
			$q = "SELECT `id`,`key` FROM `" . TABLE_PREFIX . "user_data` WHERE `user_id`='" . $user_data['id'] . "' AND `key` LIKE 'project%'";
			$row = $db->run_query($q);
			while ($find = mysql_fetch_array($row)) {
				if (! empty($find['id'])) {
					echo "<a href=\"index.php?l=templates_html_edit&load=" . $find['id'] . "\"><img src=\"imgs/load_project.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"Load Project " . $find['key'] . "\" title=\"Load Project " . $find['key'] . "\" class=\"icon_nopad\" /></a><br />";			
				}
			}
		?>	
		
		<div class="submit_split"></div>
		<a href="http://www.doyoubananadance.com/Templates/Template-System" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>

	</div>
	
	
		<h2>Custom Templates</h2>
		<table cellspacing="0" cellpadding="0" id="templates_standard" class="sort_table">
		<thead>
		<tr>
		<th>Name</th>
		<th>Description</th>
		<th>&nbsp;</th>
		</tr>
		</thead>
		<tbody>
		<?php
		
		foreach ($template_list_custom as $template) {
			if (! empty($template['subtemplate'])) {
				$style = "style=\"padding-left:30px;background: url('imgs/icon-bullet.png') 10px center no-repeat #f1f1f1;\"";
			} else {
				$style = "";
			}
			echo "<tr id=\"" . $template['id'] . "\">\n";
			echo "<td $style valign=\"top\"><a href=\"index.php?l=templates_html_edit&id=" . $template['id'] . "\">" . $template['title'] . "</a></td>\n";
			echo "<td $style valign=\"top\">" . $template['desc'] . "</td>\n";
			echo "<td $style class=\"center\" valign=\"top\"><a href=\"#\" onClick=\"deleteID('" . TABLE_PREFIX . "templates_html','" . $template['id'] . "');\"><img src=\"imgs/icon-delete.png\" border=\"0\" alt=\"Delete\" title=\"Delete\" /></a></td>\n";
			echo "</tr>\n";
		}
		
		?>
		</tbody>
		</table>
		
		
		
		<h2>Default Templates</h2>
		<table cellspacing="0" cellpadding="0" id="templates_standard1" class="sort_table">
		<thead>
		<tr>
		<th>Name</th>
		<th>Description</th>
		<th>&nbsp;</th>
		</tr>
		</thead>
		<tbody>
		<?php
		
		foreach ($template_list as $template) {
			if (! empty($template['subtemplate'])) {
				$style = "style=\"padding-left:30px;background: url('imgs/icon-bullet.png') 10px center no-repeat #f1f1f1;\"";
			} else {
				$style = "";
			}
			
			if ($template['template'] == 'article' || $template['template'] == 'footer' || $template['template'] == 'header') {
				$dup = "<a href=\"index.php?l=templates_html_add&id=" . $template['id'] . "\"><img src=\"imgs/icon-add.png\" border=\"0\" alt=\"New\" title=\"New\" /></a>";
			} else {
				$dup = '';
			}
			
			echo "<tr id=\"" . $template['id'] . "\">\n";
			echo "<td $style valign=\"top\"><a href=\"index.php?l=templates_html_edit&id=" . $template['id'] . "\">" . $template['title'] . "</a></td>\n";
			echo "<td $style valign=\"top\">" . $template['desc'] . "</td>\n";
			echo "<td $style class=\"center\" valign=\"top\">$dup</td>\n";
			echo "</tr>\n";
		}
		
		?>
		</tbody>
		</table>

</div>

<?php
}
?>