<?php

if ($privileges['is_admin'] != "1") {
	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
}
else {

	$template_info = $admin->get_template($_GET['id'],'0');
	if (empty($template_info['id'])) {
		$db->show_inline_error('Does not exist.','1');
	} else {
	
		if (empty($template_info['override_content'])) {
			$contents = $template->get_contents('email',$template_info['template']);
			$filename = PATH . "/templates/email/" . $template_info['template'] . ".html";
		} else {
			$contents = $template_info['override_content'];
		}

?>

<script>
<!--
	// --------------------------------------------
	//	CTRL-S Saves a Form
	$.ctrl('S', function() {
	    editID('<?php echo $template_info['id']; ?>');return false;
	});

-->
</script>

<form id="edit" onsubmit="return editID('<?php echo $template_info['id']; ?>');">
<input type="hidden" name="action" value="edit_template" />
<input type="hidden" name="template" value="<?php echo $template_info['template']; ?>" />
<input type="hidden" name="id" value="<?php echo $template_info['id']; ?>" />
<input type="hidden" name="custom" value="<?php echo $template_info['custom']; ?>" />

	<div class="submit">
		<img src="imgs/icon-save.png" width="16" height="16" border="0" onClick="editID('<?php echo $template_info['id']; ?>');" />
		<div class="submit_split"></div>
   		<a href="http://www.doyoubananadance.com/Templates/Email-Templates" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
	</div>
		
		
	<h1>Editing an E-Mail Template</h1>
	
	<?php
	if ($_GET['saved'] == "1") {
		echo "<p class=\"success\">Your custom template has been created!</p>";
	}
	?>

		<h2>Reference</h2>
		
		<label>Title</label>
		<input type="text" name="title" style="width:97%;" value="<?php echo $template_info['title']; ?>" />
		
		<label>Description</label>
		<textarea name="desc" style="width:95%;height:60px;"><?php echo $template_info['desc']; ?></textarea>
		
		
		<h2>E-Mail Headers</h2>
		
			<label>To</label>
			 <?php
			 if ($template_info['custom'] != '1') {
			 	if ($template_info['to'] == '%user%') {
			 		echo "User triggering the act.";
			 	} else {
			 		echo $template_info['to'];
			 	}
			 	echo "<input type=\"hidden\" name=\"to\" value=\"" . $template_info['to'] . "\" />";
			 } else {
			 	echo "<input type=\"text\" name=\"to\" style=\"width:97%;\" value=\"" . $template_info['to'] . "\" />";
			 	echo "<p class=\"field_desc\">Input <u>%user%</u> to send this to the user who triggered the action, otherwise input an e-mail address.</p>";
			 }
			 ?>
		
			<label>From</label>
			<input type="text" name="from" style="width:97%;" value="<?php echo $template_info['from']; ?>" />
			<p class="field_desc">If left empty, emails will come from "noreply@<?php echo $db->get_domain(); ?>".</p>

			<label>CC</label>
			<input type="text" name="cc" style="width:97%;" value="<?php echo $template_info['cc']; ?>" />

			<label>BCC</label>
			<input type="text" name="bcc" style="width:97%;" value="<?php echo $template_info['bcc']; ?>" />


		<h2>E-Mail Formatting</h2>
		
			<input type="hidden" name="status" value="<?php echo $template_info['status']; ?>" />
			
			<label>Format</label>
			<ul class="option_list" id="A">
				<li <?php if ($template_info['format'] == "1") { echo " class=\"checked\""; } ?>>
				<input type="radio" name="format" value="1" <?php if ($template_info['format'] == "1") { echo " checked=\"checked\""; } ?> /> HTML
				</li>
				<li <?php if ($template_info['format'] != "1") { echo " class=\"checked\""; } ?>>
					<input type="radio" name="format" value="0" <?php if ($template_info['format'] != "1") { echo " checked=\"checked\""; } ?> /> Plain Text
				</li>
			</ul>

			<label>Save a Copy?</label>
			<ul class="option_list" id="B">
				<li<?php if ($template_info['save'] == "1") { echo " class=\"checked\""; } ?>>
					<input type="radio" name="save" value="1" <?php if ($template_info['save'] == "1") { echo " checked=\"checked\""; } ?> /> Yes
				</li>
				<li<?php if ($template_info['save'] != "1") { echo " class=\"checked\""; } ?>>
					<input type="radio" name="save" value="0" <?php if ($template_info['save'] != "1") { echo " checked=\"checked\""; } ?> /> No
				</li>
			</ul>
			
		<h2>E-Mail Contents</h2>

   			<label>Subject</label>
   			<input type="text" name="subject" style="width:97%;" value="<?php echo $template_info['subject']; ?>" />
		
		
		<?php
		if (! empty($filename)) {
			if (empty($contents)) {
				echo "<p class=\"attention\">It appears that the template file may have been deleted from your server. Please restore the template following file to the \"templates/email\" directory:<br />$filename</p>";
			}
			if (! is_writable($filename)) {
				echo "<p class=\"attention\">The files in the \"templates/email\" directory need to be write enabled. Do this by setting their permissions to 777 using an FTP client. You will not be able to edit this template's content until this change has been made.</p>";
			}
		}
		?>
		
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

<?php
	}
}
?>