<?php

if ($privileges['is_admin'] != "1") {
	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
} else {

	$cs_info = $admin->get_comment_status($_GET['id']);
	if (empty($cs_info['id'])) {
		$db->show_inline_error('Does not exist.','1');
	} else {

?>

<script>
<!--
	// --------------------------------------------
	//	CTRL-S Saves a Form
	$.ctrl('S', function() {
	    editID('<?php echo $cs_info['id']; ?>');
	});
-->
</script>

<form id="edit" onsubmit="return editID('<?php echo $cs_info['id']; ?>');">
<input type="hidden" name="id" value="<?php echo $cs_info['id']; ?>" />
<input type="hidden" name="action" value="add_comment_type" />

		
		<div class="submit">
			<img src="imgs/icon-save.png" width="16" height="16" border="0" onclick="editID('<?php echo $cs_info['id']; ?>');" />
		   	<div class="submit_split"></div>
		   	<a href="http://www.doyoubananadance.com/Comments/Comment-Types" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
		</div>

		<h1>Editing Comment Type (<?php echo $cs_info['title']; ?>)</h1>

		<label>Title</label>
		<input type="text" name="title" style="width:97%;" value="<?php echo $cs_info['title']; ?>" />
		
		<label>Description</label>
		<textarea name="desc" id="desc" style="width:97%;height:120px;"><?php echo $cs_info['desc']; ?></textarea>
	
		<label>Contract Subcomments?<span class="help" id="h-1">(?)</span><div class="help_bubble" id="h-1b"><div class="hbpad">If set to yes, sub-comments will be contracted by default, requiring that the user manually expand them before they can be seen. Only works with "Tree" style commenting.</div></div></label>
		<ul class="option_list" id="A">
			<li<?php if ($cs_info['contract_subcomments'] == "1") { echo " class=\"selected\""; } ?>>
				<input type="radio" name="contract_subcomments" value="1" <?php if ($cs_info['contract_subcomments'] == "1") { echo " checked=\"checked\""; } ?> /> Yes
			</li>
			<li<?php if ($cs_info['contract_subcomments'] != "1") { echo " class=\"selected\""; } ?>>
				<input type="radio" name="contract_subcomments" value="0" <?php if ($cs_info['contract_subcomments'] != "1") { echo " checked=\"checked\""; } ?> /> No
			</li>
		</ul>
		
</form>

<?php
}
}
?>