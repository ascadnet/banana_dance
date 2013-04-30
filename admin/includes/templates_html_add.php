<?php

if ($privileges['is_admin'] != "1") {
	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
}
else {
	
	$template_info = $template->get_template_info('html',$_GET['id']);
   	$contents = $template->get_contents('html',$template_info['template']);
   	$contents = htmlspecialchars($contents);
   	$filename = PATH . "/templates/html/" . $theme . "/" . $template_info['template'] . $ext;

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

<form id="edit" onsubmit="return editID('new');">
<input type="hidden" name="action" value="add_html_template" />
<input type="hidden" name="id" value="<?php echo $template_info['id']; ?>" />
<input type="hidden" name="template" value="<?php echo $template_info['template']; ?>" />


	<div class="submit">
	<img src="imgs/icon-save.png" width="16" height="16" border="0" onClick="editID('new');" />
	</div>
		

	<h1>Creating New Template (<?php echo $template_info['title']; ?>)</h1>
	
		<div class="col50">
			<label>Title</label>
			<input type="text" name="title" maxlength="100" value="<?php echo $template_info['title']; ?>" style="width:90%;" />
		</div>
		<div class="col50">
			<label>Description</label>
			<input type="text" name="desc" maxlength="255" value="<?php echo $template_info['desc']; ?>" style="width:90%;" />
		</div>
		
		<div id="headfoot" style="display:<?php if ($template_info['template'] != 'header' && $template_info['template'] != 'footer') { echo "block"; } else { echo "none"; } ?>;">
			<h3>Custom Header/Footer?</h3>
			<div class="col50">
				<label>Custom Header</label>
				<select name="custom_header" style="width:90%;">
				<?php
				$list = $template->list_templates('header','1');
				echo $list;
				?>
				</select>
			</div>
			<div class="col50">
				<label>Custom Footer</label>
				<select name="custom_footer" style="width:90%;">
				<?php
				$list1 = $template->list_templates('footer','2');
				echo $list1;
				?>
				</select>
			</div>
		</div>
		
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
?>