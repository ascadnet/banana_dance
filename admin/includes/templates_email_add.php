<?php

if ($privileges['is_admin'] != "1") {
	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
} else {

	$template_info = $admin->get_template($_GET['id']);
	$contents = $template->get_contents('email',$template_info['template']);

?>

<script>
<!--
	// --------------------------------------------
	//	CTRL-S Saves a Form
	$.ctrl('S', function() {
	    editID('new');return false;
	});

-->
</script>

<div id="content_overlay">

<form id="edit" onsubmit="return editID('new');">
<input type="hidden" name="template" value="<?php echo $template_info['template']; ?>" />
<input type="hidden" name="id" value="new" />
<input type="hidden" name="clone_id" value="<?php echo $template_info['id']; ?>" />
<input type="hidden" name="custom" value="1" />
<input type="hidden" name="action" value="edit_template" />

	<div class="submit">
		<img src="imgs/icon-save.png" width="16" height="16" border="0" onclick="editID('new');" />
		<div class="submit_split"></div>
   		<a href="http://www.doyoubananadance.com/Templates/Email-Templates" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
	</div>
		
	<h1>Create a Custom E-Mail Template</h1>
		
		<h2>Reference</h2>
		
		<label>Title</label>
		<input type="text" name="title" style="width:97%;" value="<?php echo $template_info['title']; ?>" />
		
		<label>Description</label>
		<textarea name="desc" style="width:95%;height:60px;"><?php echo $template_info['desc']; ?></textarea>
		
		
		<h2>E-Mail Headers</h2>
		
		<label>To</label>
		<input type="text" name="to" style="width:97%;" value="" />
		<p class="field_desc">Input <u>%user%</u> to send this to the user who triggered the action, otherwise input an e-mail address.</p>

		<label>From</label>
		<input type="text" name="from" style="width:97%;" value="<?php echo $template_info['from']; ?>" />
		<p class="field_desc">If left empty, emails will come from "noreply@<?php echo $db->get_domain(); ?>".</p>
		
		<label>CC</label>
		<input type="text" name="cc" style="width:97%;" value="<?php echo $template_info['cc']; ?>" />

		<label>BCC</label>
		<input type="text" name="bcc" style="width:97%;" value="<?php echo $template_info['bcc']; ?>" />

		<h2>E-Mail Formatting</h2>

			<label>Format</label>
			<ul class="option_list" id="A">
				<li>
				<input type="radio" name="format" value="1" checked="checked" /> HTML
				</li>
				<li class="checked">
					<input type="radio" name="format" value="0" checked="checked" /> Plain Text
				</li>
			</ul>
		
		<input type="hidden" name="status" value="1" />
		
			<label>Save a Copy?</label>
			<ul class="option_list" id="B">
				<li>
					<input type="radio" name="save" value="1" /> Yes
				</li>
				<li class="checked">
					<input type="radio" name="save" value="0" checked="checked" /> No
				</li>
			</ul>
			

		<h2>E-Mail Contents</h2>
		
   		<label>Subject</label>
   		<input type="text" name="subject" style="width:97%;" value="<?php echo $template_info['subject']; ?>" />
		
		<label>Content</label>
		<textarea name="content" id="content" style="width:97%;height:500px;"><?php
			echo $contents;
		?></textarea>
		
		<div id="caller_tags">
			<?php
			$tags = $admin->format_caller_tags($template_info['caller_tags'],'content');
			echo $tags;
			?>
		</div>

</form>
</div>

<?php
}
?>