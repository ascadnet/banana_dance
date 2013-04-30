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
	
	// Special considerations
	$(document).ready(function() {
		$("#act").change(function () {
			value = $('#act').val();
			if (value == 'comment_status_changed') {
				$('#comment_types').show();
			} else {
				$('#comment_types').hide();
			}
		});
	});
-->
</script>

<div id="content_overlay">

   	<div class="submit">
   		<img src="imgs/icon-save.png" width="16" height="16" border="0" onclick="editID('new');" />
   	   	<div class="submit_split"></div>
   		<a href="http://www.doyoubananadance.com/Users/Point-and-Badge-System" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
   	</div>
		
	<h1>Create a Badge</h1>
	
	<form id="edit" onsubmit="return editID('new');">
	<input type="hidden" name="id" value="new" />
	<input type="hidden" name="action" value="add_badge" />
	
	<div class="main_pad">
	
			<h2>General Settings</h2>
			
		   		<label>Name</label>
		   		<input type="text" name="name" maxlength="35" style="width:90%;" value="" />
		   		
		   		<label>Short Description</label>
		   		<input type="text" name="desc" style="width:90%;" value="" maxlength="255" />


			<h2>Requirements for Badge</h2>

		   		<label>Action</label>
		   		<select name="act" id="act" style="width:300px;">
		   		<option value="score">Achieve score</option>
		   		<option value="article_add">Add pages</option>
		   		<option value="article_edit">Edit pages</option>
		   		<option value="comment_post">Post comments</option>
		   		<option value="comment_status_changed">Have comments changed to a specific comment type.</option>
		   		</select>
	   			<p class="field_desc">What action will trigger this badge?</p>
	   			
	   			<div id="comment_types" style="display:none;">
					<label>Changed to</label>
					<select name="act_id" style="width:300px;">
					<option value="" selected="selected"></option>
					<?php
					$list1 = $manual->get_comment_statuses();
					echo $list1;
					?>
					</select>
	   			</div>
	   			
		   		<label>Number</label>
		   		<input type="text" name="points_required" style="width:100px;" maxlength="6" value="" />
	   			<p class="field_desc">Number of "action" required to achieve this badge.</p>


			<h2>Badge Styles</h2>
	   		
			<div class="col50">
	   			<label>Font Color</label>
	   			#<input type="text" name="font_color" style="width:100px;" maxlength="6" value="" />
	   			<p class="field_desc">Color of the user's username in the comments.</p>
			</div>
			<div class="col50">
	   			<label>Background Color</label>
	   			#<input type="text" name="color" style="width:100px;" maxlength="6" value="" />
	   			<p class="field_desc">Background color of the user's username in the comments.</p>
			</div>
			<div class="clear"></div>
			
	</div>
	</form>

</div>

<?php
}
?>