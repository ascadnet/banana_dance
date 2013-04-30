<?php

if ($privileges['is_admin'] != "1") {
	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
} else {

?>


<script>
<!--
	// --------------------------------------------
	//	CTRL-S Saves a Form
	$.ctrl('S', function() {
	    editID('new');
	});
-->
</script>


<div id="content_overlay">
	
	
   	<div class="submit">
   		<img src="imgs/icon-save.png" width="16" height="16" border="0" onclick="editID('new');" />
		<div class="submit_split"></div>
   		<a href="http://www.doyoubananadance.com/Users/" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
   	</div>
   	
	<h1>Create a New User</h1>
	
	<form id="edit" onsubmit="return editID('new');">
	<input type="hidden" name="id" value="new" />
	<input type="hidden" name="action" value="add_user" />
	
			<h2>User Type<span class="req">*</span></h2>
			<select name="type" style="width:90%;" tabindex="6">
			<?php
			echo $admin->user_types_select();
			?>
			</select>
	
			<h2>General Information</h2>
			<div class="col50">
	   			<label>Username<span class="req">*</span></label>
	   			<input type="text" name="username" style="width:90%;" tabindex="1" value="" />
	   			
	   			<label>Name</label>
	   			<input type="text" name="name" style="width:90%;" tabindex="3" value="" />
			</div>
			<div class="col50">
	   			<label>E-Mail<span class="req">*</span></label>
	   			<input type="text" name="email" style="width:97%;" tabindex="2" value="" />
			</div>
			<div class="clear"></div>
			
			
			<h2>Password</h2>
			<div class="col50">
	   			<label>Password</label>
	   			<input type="password" name="pass" style="width:90%;" tabindex="4" />
	   			<p class="field_desc">Select a password.</p>
			</div>
			<div class="col50">
	   			<label>Repeat Password</label>
	   			<input type="password" name="pass1" style="width:97%;" tabindex="5" />
	   			<p class="field_desc">Repeat your password.</p>
			</div>
			<div class="clear"></div>
		
		
		<div class="clear"></div>
	   	<div class="divide"></div>
	   	
	   	<!--
	   		These represent the field sets currently in the database.
	   	-->
	   	
	   	<?php
	   	require PATH . "/includes/field.functions.php";
	   	$fields = new fields;
	   	$set_list = $admin->field_set_li('10002');
	   	?>
	   		
	   	<script language="JavaScript" src="<?php echo URL ?>/js/admin_fields.js"></script> 
	   	<ul id="inner_page_tabs_top">
	   	<?php
	   	$field_sets = $fields->get_field_sets('10002');
	   	$final_sets = "";
	   	if (! empty($field_sets)) {
	   		$current = 0;
	   		foreach ($field_sets as $field_set_id) {
	   			$current++;
	   			// Style
	   			if ($current == "1") { $display = "block"; $class = "on"; }
	   			else { $display = "none"; $class = "off"; }
	   			// Fieldset Information
	   			$set_information = $fields->field_set_data($field_set_id);
	   			$set_fields = $fields->generate_field_set($field_set_id,$set_information,$user_data,'1');
	   			echo "<li id=\"litab" . $field_set_id . "\" onMouseOver=\"showDel('" . $field_set_id . "');\" onMouseOut=\"hideDel('" . $field_set_id . "');\" class=\"$class\"><a href=\"#\" onClick=\"swapTab('" . $field_set_id . "');return false;\">" . $set_information['name'] . "</a><a href=\"#\" onClick=\"deleteFieldSet('10002','" . $field_set_id . "');\"><img src=\"imgs/icon-delete.png\" width=16 height=16 border=0 title=\"Remove Tab\" title=\"Remove Tab\" id=\"delete_tab" . $field_set_id . "\" style=\"display:none;\" class=\"icon_l\" /></a></li> ";
	   			// Field sets
	   			$final_sets .= "<div id=\"tab$field_set_id\" style=\"display:$display;\">";
	   			$final_sets .= $set_fields;
	   			$final_sets .= "</div>";
	   		}
	   	}
	   	?>
	   	<li id="litab_new"><a href="#" onClick="addFieldSet('10002');return false;"><img src="imgs/icon-add.png" width="16" height="16" border="0" alt="Add Set" title="Add Set" class="icon_nopad" /></a></li>
	   	</ul>
	   	
	   	<div class="clear"></div>
	   	<div class="home_box" style="margin-top:-1px;"><div class="pad12">
	   		<?php
	   			echo $final_sets;
	   		?>
	   		<div id="place_new_here" style="display:none;"></div>
	   	</div></div>
	
	</form>

</div>

<?php
}
?>