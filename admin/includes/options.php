<?php

if ($privileges['is_admin'] != "1") {
	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
} else {
	
	// Get this option group's name
	$q = "SELECT `value` FROM `" . TABLE_PREFIX . "options` WHERE `type`='2' AND `key`='" . $db->mysql_clean($_GET['set']) . "' LIMIT 1";
	$options_set = $db->get_array($q);

	if (empty($options_set['value'])) {
		$db->show_inline_error('Could not find options set.','1');
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

<form id="edit" onsubmit="return editID('<?php echo $_GET['set']; ?>');">
<input type="hidden" name="set" value="<?php echo $_GET['set']; ?>" />
<input type="hidden" name="action" value="edit_options" />

   	<div class="submit">
  	 	<img src="imgs/icon-save.png" width="16" height="16" border="0" onClick="editID('<?php echo $_GET['set']; ?>');" />
   		<div class="submit_split"></div>
   		<a href="http://www.doyoubananadance.com/Product-Manual/System-Options" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
   	</div>
   	
   	
	<h1>Settings: <?php echo $options_set['value']; ?></h1>
	
	<?php
	$q = "SELECT * FROM `" . TABLE_PREFIX . "options` WHERE `type`='1' AND `group`='" . $db->mysql_clean($_GET['set']) . "' ORDER BY `field_order` ASC, `display_name` ASC";
	$result = $db->run_query($q);
	while ($row = mysql_fetch_array($result)) {

		// Field Type
		//	display_name / description / value
		//	1 = Yes or No
		//	2 = Text Input
		//		field_width
		//		left_padding
		//	3 = Select
		//		Options = fixed_selections (separator: |)
		
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
	?>

</form>

<?php
	}
}
?>