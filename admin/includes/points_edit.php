<?php

if ($privileges['is_admin'] != "1") {
	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
} else {

	$pv = $manual->get_point_value($_GET['id']);
	if (empty($pv['id'])) {
		$db->show_inline_error('Point value does not exist.','1');
	}

?>

<script>
<!--
	// --------------------------------------------
	//	CTRL-S Saves a Form
	$.ctrl('S', function() {
	    editID('<?php echo $pv['id']; ?>');
	});
	
	// Special considerations
	$(document).ready(function() {
		$("#task").change(function () {
			value = $('#task').val();
			if (value == 'comment_status_changed') {
				$('#comment_types').show();
			} else {
				$('#comment_types').hide();
			}
		});
	});
	
	function toggleList(type) {
		if (type == 'on') {
			$('#user_list').show();
		} else {
			$('#user_list').hide();
		}
	}
-->
</script>

<form id="edit" onsubmit="return editID('<?php echo $pv['id']; ?>');">
<input type="hidden" name="id" value="<?php echo $pv['id']; ?>" />
<input type="hidden" name="action" value="add_point_values" />

   	<div class="submit">
		<img src="imgs/icon-save.png" width="16" height="16" border="0" onclick="editID('<?php echo $pv['id']; ?>');" />
   		<div class="submit_split"></div>
   		<a href="http://www.doyoubananadance.com/Users/Point-and-Badge-System" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
   	</div>
   	
	<h1>Editing Point Value</h1>

		<label>Task</label>
		<select name="task" id="task" style="width:300px;">
		<?php
		$list = $manual->task_list($pv['task']);
		echo $list;
		?>
		</select>
		
		<div id="comment_types" style="display:<?php if ($pv['task'] == 'comment_status_changed') { echo "block"; } else { echo "none"; } ?>;">
			<option value="" selected="selected"></option>
			<label>Changed to</label>
			<select name="comment_status" style="width:300px;">
			<?php
			$list1 = $manual->get_comment_statuses($pv['act_on_id']);
			echo $list1;
			?>
			</select>
		</div>
		
		
		<label>Points</label>
		<input type="text" name="points" value="<?php if (! empty($pv['required'])) { $type="req"; echo $pv['required']; } else { $type="grant"; echo $pv['points']; } ?>" style="width:100px;" maxlength="5" /><br />


		
		<label>Requirement or Granted?</label>
		<ul id="E" class="option_list">
			<li<?php if ($type == 'req') { echo " class=\"checked\""; } ?>>
			<input type="radio" name="type" value="required" <?php if ($type == 'req') { echo " checked=\"checked\""; } ?> onclick="toggleList('off');" /> Required to perform the task
			</li>
			<li<?php if ($type != 'req') { echo " class=\"checked\""; } ?>>
			<input type="radio" name="type" value="granted" <?php if ($type != 'req') { echo " checked=\"checked\""; } ?> onclick="toggleList('on');" /> Granted to the receipient
			</li>
		</ul>
		

		<div id="user_list" style="display:<?php if ($type == 'req') { echo "none"; } else { echo "block"; } ?>;">
			<label>Who gets the points?</label>
			<ul id="R" class="option_list">
				<li<?php if ($pv['act_on'] == 'user' || empty($pv['act_on'])) { echo " class=\"checked\""; } ?>>
				<input type="radio" name="act_on" value="user" <?php if ($pv['act_on'] == 'user' || empty($pv['act_on'])) { echo " checked=\"checked\""; } ?> /> User performing the task.
				</li>
				<li<?php if ($pv['act_on'] == 'act_on') { echo " class=\"checked\""; } ?>>
				<input type="radio" name="act_on" value="act_on" <?php if ($pv['act_on'] == 'act_on') { echo " checked=\"checked\""; } ?> /> Owner of the item acted upon.
				</li>
			</ul>
		</div>
	
		
		
</form>

<?php
}
?>