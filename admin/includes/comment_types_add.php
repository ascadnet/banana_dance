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

<form id="edit" onsubmit="return editID('new');">
<input type="hidden" name="id" value="new" />
<input type="hidden" name="action" value="add_comment_type" />

	<div class="submit">
		<img src="imgs/icon-save.png" width="16" height="16" border="0" onclick="editID('new');" />
	   	<div class="submit_split"></div>
	   	<a href="http://www.doyoubananadance.com/Comments/Comment-Types" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
	</div>

	<h1>Creating Comment Type</h1>

		<label>Title</label>
		<input type="text" name="title" style="width:97%;" value="" />
		
		<label>Description</label>
		<textarea name="desc" id="desc" style="width:97%;height:120px;"></textarea>
	
		<label>Contract Subcomments?<span class="help" id="h-1">(?)</span><div class="help_bubble" id="h-1b"><div class="hbpad">If set to yes, sub-comments will be contracted by default, requiring that the user manually expand them before they can be seen. Only works with "Tree" style commenting.</div></div></label>
		<ul class="option_list" id="A">
			<li>
				<input type="radio" name="contract_subcomments" value="1" /> Yes
			</li>
			<li class="checked">
				<input type="radio" name="contract_subcomments" value="0" checked="checked" /> No
			</li>
		</ul>
		

</form>

<?php
}
?>