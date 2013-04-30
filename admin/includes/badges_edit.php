<?php

if ($privileges['is_admin'] != "1") {
	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
} else {

	$badge = $manual->get_badge($_GET['id']);
	if (empty($badge['id'])) {
		$db->show_inline_error('Point value does not exist.','1');
	}

?>

<script>
<!--
	// --------------------------------------------
	//	CTRL-S Saves a Form
	$.ctrl('S', function() {
	    editID('<?php echo $badge['id']; ?>');
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
   		<img src="imgs/icon-save.png" width="16" height="16" border="0" onclick="editID('<?php echo $badge['id']; ?>');" />
   	   	<div class="submit_split"></div>
   		<a href="http://www.doyoubananadance.com/Users/Point-and-Badge-System" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
   	</div>
   	
	<h1>Editing Badge (<?php echo $badge['name']; ?>)</h1>
	
	<form id="edit" onsubmit="return editID('<?php echo $badge['id']; ?>');">
	<input type="hidden" name="id" value="<?php echo $badge['id']; ?>" />
	<input type="hidden" name="action" value="add_badge" />
	
			<h2>General Settings</h2>
			
		   		<label>Name</label>
		   		<input type="text" name="name" style="width:90%;" maxlength="35" value="<?php echo $badge['name']; ?>" />
		   		
		   		<label>Short Description</label>
		   		<input type="text" name="desc" style="width:90%;" value="<?php echo $badge['desc']; ?>" maxlength="255" />



			<h2>Requirements for Badge</h2>

		   		<label>Action</label>
		   		<select id="act" name="act" style="width:300px;">
		   		<option value="score"<?php if ($badge['act'] == 'score') { echo " selected=\"selected\""; } ?>>Achieve score</option>
		   		<option value="article_add"<?php if ($badge['act'] == 'article_add') { echo " selected=\"selected\""; } ?>>Add articles</option>
		   		<option value="article_edit"<?php if ($badge['act'] == 'article_edit') { echo " selected=\"selected\""; } ?>>Edit articles</option>
		   		<option value="comment_post"<?php if ($badge['act'] == 'comment_post') { echo " selected=\"selected\""; } ?>>Post comments</option>
		   		<option value="comment_status_changed"<?php if ($badge['act'] == 'comment_status_changed') { echo " selected=\"selected\""; } ?>>Have comments changed to a specific comment type.</option>
		   		</select>
	   			<p class="field_desc">What action will trigger this badge?</p>
	   			
	   			<div id="comment_types" style="display:<?php if (! empty($badge['act_id'])) { echo 'block'; } else { echo 'none'; } ?>;">
					<label>Changed to</label>
					<select name="act_id" style="width:300px;">
					<option value=""></option>
					<?php
					$list1 = $manual->get_comment_statuses($badge['act_id']);
					echo $list1;
					?>
					</select>
	   			</div>
	   			
		   		<label>Number</label>
		   		<input type="text" name="points_required" style="width:100px;" maxlength="6" value="<?php echo $badge['points_required']; ?>" />
	   			<p class="field_desc">Number of "action" required to achieve this badge.</p>
	   			
	   		
	   		
			<h2>Badge Styles</h2>

			<div class="col50">
	   			<label>Font Color</label>
	   			#<input type="text" name="font_color" style="width:100px;" maxlength="6" value="<?php echo $badge['font_color']; ?>" />
	   			<p class="field_desc">Color of the font in the badge.</p>
			</div>
			<div class="col50">
	   			<label>Background Color</label>
	   			#<input type="text" name="color" style="width:100px;" maxlength="6" value="<?php echo $badge['color']; ?>" />
	   			<p class="field_desc">Background color of the badge.</p>
			</div>
			<div class="clear"></div>
			
	</form>

</div>

<?php
}
?>