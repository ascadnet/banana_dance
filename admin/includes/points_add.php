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

<form id="edit" onsubmit="return editID('new');">
<input type="hidden" name="id" value="new" />
<input type="hidden" name="action" value="add_point_values" />


   	<div class="submit">
		<img src="imgs/icon-save.png" width="16" height="16" border="0" onclick="editID('new');" />
   		<div class="submit_split"></div>
   		<a href="http://www.doyoubananadance.com/Users/Point-and-Badge-System" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
   	</div>
   	
	<h1>Creating Point Value</h1>


			<label>Task</label>
			<select name="task" id="task" style="width:300px;">
			<option value="" selected="selected"></option>
			<?php
			$list = $manual->task_list();
			echo $list;
			?>
			</select>
			
			<div id="comment_types" style="display:none;">
				<label>Changed to</label>
				<select name="comment_status" style="width:300px;">
				<option value="" selected="selected"></option>
				<?php
				$list1 = $manual->get_comment_statuses();
				echo $list1;
				?>
				</select>
			</div>
			
			
		<label>Points</label>
		<input type="text" name="points" value="" style="width:100px;" maxlength="5" />
			
		
		<label>Requirement or Granted?</label>
		<ul id="E" class="option_list">
			<li>
			<input type="radio" name="type" value="required" onclick="toggleList('off');" /> Required to perform the task
			</li>
			<li class="selected">
			<input type="radio" name="type" value="granted" onclick="toggleList('on');" checked="checked" /> Granted to the receipient
			</li>
		</ul>
		
		<div id="user_list" style="display:block;">
			<label>Who gets the points?</label>
			<ul id="R" class="option_list">
				<li class="selected">
				<input type="radio" name="act_on" value="user" checked="checked" /> User performing the task
				</li>
				<li>
				<input type="radio" name="act_on" value="act_on" /> Owner of the item acted upon.
				</li>
			</ul>
		</div>

</form>

<?php
}
?>