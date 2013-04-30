<?php

if ($privileges['is_admin'] != "1") {
	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
}
else {
	
	$template_info = $template->get_template_info('html',$_GET['id']);
   	$contents = $template->get_contents('html',$template_info['template'],$template_info['type'],$template_info['id'],$template_info);
   	$contents = htmlspecialchars($contents);
   	if ($template_info['template'] == 'css_style' || $template_info['template'] == 'css_definitions' || $template_info['template'] == 'css_print' || $template_info['template'] == 'css_article') {
   		$ext = '.css';
   	} else {
   		$ext = '.php';
   	}
   	
   	if ($template_info['type'] == '0' || empty($template_info['type'])) {
   		$filename = PATH . "/templates/html/" . $theme . "/" . $template_info['template'] . $ext;
   	} else {
   		if (! empty($template_info['path'])) {
	   		$filename = PATH . "/templates/html/" . $template_info['theme'] . "/" . $template_info['path'];
	   		$custom_template = '1';
   		} else {
	   		$filename = PATH . "/generated/template-" . $template_info['id'] . $ext;
	   		$custom_template = '1';
   		}
   	}

?>

<script>
<!--
	
	// --------------------------------------------
	//	CTRL-S Saves a Form
	$.ctrl('S', function() {
	    editID('<?php echo $template_info['id']; ?>');return false;
	});
	
	$(document).ready(function() {
	<?php
	if (! empty($_GET['load'])) {
	?>
		loadProject('<?php echo $_GET['load']; ?>');
	<?php
	}
	?>
			
		width = $('#main_content').width();
		putWidth = width - 150;
		$('#html_temp_editor').width(putWidth);
	});
	
-->
</script>

<form id="edit" onsubmit="return editID('<?php echo $template_info['id']; ?>');">
<input type="hidden" name="action" value="edit_html_template" />
<input type="hidden" id="template" name="template" value="<?php echo $template_info['template']; ?>" />
<input type="hidden" id="filename" name="filename" value="<?php echo $filename; ?>" />
<input type="hidden" id="type" name="type" value="<?php echo $template_info['type']; ?>" />
<input type="hidden" id="id" name="id" value="<?php echo $template_info['id']; ?>" />
<input type="hidden" id="path" name="path" value="<?php echo $template_info['path']; ?>" />

<div class="submit">
	<img src="imgs/icon-save.png" width="16" height="16" border="0" onClick="editID('<?php echo $template_info['id']; ?>');" />
</div>


	<h1>Project</h1>

		<script>
		var initial_edit = '<?php echo $template_info['id']; ?>';
		var active_edits = new Array();
		active_edits.push(<?php
		echo $template_info['id'];
		?>);
		var template_list = '<?php
		$set_list = $admin->get_template_html_list('select','all');
		echo $set_list;
		?>';
		</script>
		
		<script language="JavaScript" src="<?php echo URL ?>/js/admin_templates.js"></script> 
		<ul id="inner_page_tabs">
		<li id="litab_save"><a href="#" onClick="addTemplate();return false;"><img src="imgs/icon-add.png" width="16" height="16" border="0" alt="Add Template" title="Add Template" class="icon_nopad" /></a></li>
			<li id="litab<?php echo $template_info['id']; ?>" class="on template_tab"><a href="#" onClick="swapTemplate('<?php echo $template_info['id']; ?>');return false;"><?php echo $template_info['title']; ?></a><a href="#" onClick="removeTemplateFromView('<?php echo $template_info['id']; ?>');return false;"><img src="imgs/icon-delete.png" width=16 height=16 border=0 title="Remove Tab" title="Remove Tab" id="delete_tab'<?php echo $template_info['id']; ?>'" style="display:none;" class="icon_l" /></a></li>
			<li id="litab_new" style="display:none;"></li>
		</ul>
		
		

	<h1>Editing HTML Template</h1>
	
	<div id="content_overlay">
		
		<?php
		if (empty($_GET['load'])) {
			if (! file_exists($filename)) {
					echo "<p class=\"attention\">It appears that the template file may have been deleted from your server. Please restore the template following file to the \"templates/html/$theme\" directory:<br />$filename</p>";
			} else {
				if (! is_writable($filename)) {
					echo "<p class=\"attention\">The files in the \"templates/html/$theme\" directory need to be write enabled. Do this by setting their permissions to 777 using an FTP client. You will not be able to edit this template's content until this change has been made.</p>";
				}
			}
		}
		?>
		
		<div class="col50">
			<label>Title</label>
			<input type="text" name="title" id="title" maxlength="100" value="<?php echo $template_info['title']; ?>" style="width:90%;margin-bottom:5px;" />
		</div>
		<div class="col50">
			<label>Description</label>
			<input type="text" name="desc" id="desc" maxlength="255" value="<?php echo $template_info['desc']; ?>" style="width:90%;margin-bottom:5px;" />
		</div>
		
		<?php
		if ($template_info['type'] != '2' && $template_info['type'] != '1') {
		?>
			
			<div id="headfoot">
				<div class="col50">
					<label>Custom Header</label>
					<select id="custom_header" name="custom_header" style="width:90%;margin-bottom:5px;">
					<?php
					$list = $template->list_templates('header','1',$template_info['custom_header']);
					echo $list;
					?>
					</select>
				</div>
				<div class="col50">
					<label>Custom Footer</label>
					<select id="custom_footer" name="custom_footer" style="width:90%;margin-bottom:5px;">
					<?php
					$list1 = $template->list_templates('footer','2',$template_info['custom_footer']);
					echo $list1;
					?>
					</select>
				</div>
			</div>
		
		<?php
		}
		?>
		<div class="clear"></div>
		
		<textarea name="content" id="content" style="width:98%;height:450px;"><?php
			echo $contents;
		?></textarea>
		<div id="fileLoc"><b>Physical Location:</b> <span id="filename_show"><?php echo $filename; ?></span></div>

		<!--
		<div id="caller_tags">
			//if ($template_info['template'] != 'style.css') {
			//	$tags = $admin->format_caller_tags($template_info['caller_tags'],'content');
			//	echo $tags;
			//}
		</div>
		-->

	</div>
	<div class="clear"></div>
</form>

<?php
}
?>