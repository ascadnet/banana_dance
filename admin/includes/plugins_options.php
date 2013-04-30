<?php

if ($privileges['is_admin'] != "1") {
	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
} else {
	
	// Get this option group's name
	$plugin = $manual->get_plugin($_GET['plugin']);
	$plugin_dets = unserialize($plugin['details']);
	
	if (empty($plugin['id'])) {
		$db->show_inline_error('Could not find plugin.','1');
	} else {

?>

<script>
<!--
	// --------------------------------------------
	//	CTRL-S Saves a Form
	$.ctrl('S', function() {
	    editID('<?php echo $_GET['set']; ?>');return false;
	});
-->
</script>

<form id="edit" onsubmit="return editID('<?php echo $plugin['filename']; ?>');">
<input type="hidden" name="plugin" value="<?php echo $plugin['filename']; ?>" />
<input type="hidden" name="action" value="edit_plugin_options" />

   	<div class="submit">
  	 	<img src="imgs/icon-save.png" width="16" height="16" border="0" onClick="editID('<?php echo $plugin['filename']; ?>');" />
   		<div class="submit_split"></div>
   		<a href="http://www.bananadance.org/Pages/Widgets" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
   	</div>
   	
   	
	<h1><?php echo $plugin['name']; ?></h1>
   	
	<div class="col50">
	
		<h2>Plugin Overview</h2>
	
		<div class="explain_name">Active</div>
		<div class="explain_desc"><?php
			if ($plugin['active'] == '1') {
				echo "<font color=green>Active</font>";
			} else {
				echo "<font color=red>Inactive</font>";
			}
		?></div>
		
		<div class="explain_name">Include<span class="help" id="h-1">(?)</span><div class="help_bubble" id="h-1b"><div class="hbpad">Copy and paste either of these "includes" on to any page or template to use the plugin on your website.</div></div></div>
		<div class="explain_desc">{-<?php echo $plugin['id']; ?>-}<p class="small center">OR</p>{-<?php echo $plugin['filename']; ?>-}</div>
	
		<div class="explain_name">Name</div>
		<div class="explain_desc"><?php echo $plugin['name']; ?></div>
		
		<div class="explain_name">Description</div>
		<div class="explain_desc"><?php echo $plugin_dets['desc']; ?></div>
		
		<div class="explain_name">Installed</div>
		<div class="explain_desc"><?php echo $db->format_date($plugin['date']); ?></div>
		
	</div>
	
	<div class="col50">
	
		<h2>Developer</h2>
		
		<?php
			if (! empty($plugin_dets['img_location'])) {
				echo "<div id=\"plugin_logo\"><center><a href=\"" .  $plugin_dets['developer_url'] . "\" target=\"_blank\"><img src=\"" . $plugin_dets['img_location'] . "\" border=\"0\" alt=\"" . $plugin_dets['developer'] . "\" title=\"" . $plugin_dets['developer'] . "\" /></a></center></div>";
			}
		?>
	
		<div class="explain_name">Name</div>
		<div class="explain_desc"><?php
		if (! empty($plugin_dets['developer'])) {
			if (! empty($plugin_dets['developer_url'])) {
				echo "<a href=\"" .  $plugin_dets['developer_url'] . "\" target=\"_blank\">" . $plugin_dets['developer'] . "</a>";
			} else {
				echo $plugin_dets['developer'];
			}
		} else {
			echo "<i>N/A</i>";
		}
		?></div>
		
		<div class="explain_name">Plugin Page</div>
		<div class="explain_desc"><?php
			echo "<a href=\"" .  $plugin_dets['plugin_page'] . "\" target=\"_blank\">Click Here</a>";
		?></div>
		
	</div>
	<div class="clear"></div>
   	
	<script language="JavaScript" src="<?php echo URL ?>/js/admin_fields.js"></script> 
   	<ul id="inner_page_tabs_top">
		<li id="litaboptions" class="on"><a href="#" onClick="swapTab('options');return false;">Options</a></li>
		<li id="litabreadme"><a href="#" onClick="swapTab('readme');return false;">Instructions</a></li>
		<li id="litabscreen"><a href="#" onClick="swapTab('screen');return false;">Screen Shot</a></li>
	</ul>
   	<div class="clear"></div>
   	<div class="home_box_lg" style="margin-top:-1px;"><div class="pad12">
			<div id="taboptions">
				<?php
				$q = "SELECT * FROM `" . TABLE_PREFIX . "options` WHERE `type`='3' AND `plugin`='" . $plugin['filename'] . "' ORDER BY `field_order` ASC, `display_name` ASC";
				$result = $db->run_query($q);
				$found = 0;
				while ($row = mysql_fetch_array($result)) {
					$found++;
					if (! empty($row['left_padding'])) {
						$margin_left = " indent" . $row['left_padding'];
					} else {
						$margin_left = " noindent";
					}
					if (empty($row['field_width'])) {
						$row['field_width'] = "200";
					}
					else if ($row['field_width'] < "50") {
						$row['field_width'] = "50";
					}
					
					echo "<!-- Start Option -->\n";
					echo "<div class=\"option_entry$margin_left\">\n";
					echo "<label class=\"option\">" . $row['display_name'] . "</label>\n";
						
					// Yes or No Option
					if ($row['field_type'] == "1") {
						echo "<input class=\"input_option\" type=\"radio\" name=\"" . $row['id'] . "\" value=\"1\"";
						if ($row['value'] == '1') { echo " checked=\"checked\""; }
						echo " /> Yes&nbsp;&nbsp;&nbsp;";
						echo "<input class=\"input_option\" type=\"radio\" name=\"" . $row['id'] . "\" value=\"0\"";
						if ($row['value'] != '1') { echo " checked=\"checked\""; }
						echo " /> No\n";
					}
					
					// Text input
					else if ($row['field_type'] == "2") {
						echo "<input class=\"input_option\" type=\"text\" name=\"" . $row['id'] . "\" value=\"" . $row['value'] . "\" style=\"width:" . $row['field_width'] . "px;\" />\n";
					}
					
					// Select field
					else if ($row['field_type'] == "3") {
						$exp_selections = explode('|',$row['fixed_selections']);
						echo "<select class=\"input_option\" name=\"" . $row['id'] . "\" style=\"width:" . $row['field_width'] . "px;\">\n";
						foreach ($exp_selections as $selection) {
							echo "<option value=\"" . $selection . "\"";
							if ($selection == $row['value']) {
								echo " selected=\"selected\"";
							}
							echo ">" . $selection;
							echo "</option>\n";
						}
						echo "</select>";
					}
					
					echo "<p class=\"option_desc\">" . $row['description'] . "</p>";
					echo "</div>\n";
					echo "<!-- End Option -->\n\n";
					
				}
				if ($found <= 0) {
					echo "<p>There are no options for this plugin.</p>";
				}
				?>
			</div>
			<div id="tabreadme" style="display:none;">
			   		<?php
						$readme_loc = PATH . "/addons/widgets/" . $plugin['filename'] . "/README";
						if (! file_exists($readme_loc)) {
							$readme = 'No documentation provided.';
						} else {
			   				$readme = file_get_contents($readme_loc);
			   			}
			   			echo nl2br(htmlentities($readme));
			   		?>
			</div>
			<div id="tabscreen" style="display:none;">
			   		<?php
			   			$screen_file = PATH . "/addons/widgets/" . $plugin['filename'] . "/screen.jpg";
				   		$screen_url = URL . "/addons/widgets/" . $plugin['filename'] . "/screen.jpg";
			   			if (file_exists($screen_file)) {
				   			echo "<img src=\"" . $screen_url . "\" border=\"0\" />";
			   			} else {
				   			if (! empty($plugin_dets['screen_shot'])) {
				   				if ($plugin_dets['screen_shot'] != 'screen.jpg') {
				   					$screen_url = $plugin_dets['screen_shot'];
				   				}
				   				echo "<img src=\"" . $screen_url . "\" border=\"0\" />";
				   			} else {
				   				echo "No screen shot.";
				   			}
			   			}
			   		?>
			</div>
   	</div></div>

</form>

<?php
	}
}
?>