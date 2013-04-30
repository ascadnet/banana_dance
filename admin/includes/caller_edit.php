<?php

if ($privileges['is_admin'] != "1") {
	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
} else {

	$caller_tag = $manual->get_custom_caller($_GET['id']);
	if (empty($caller_tag['id'])) {
		$db->show_inline_error('Does not exist.','1');
	} else {
	
?>


<script>
<!--

	var current_function = 'custom_caller';
	
	// --------------------------------------------
	//	CTRL-S Saves a Form
	$.ctrl('S', function() {
	    editID('<?php echo $caller_tag['id']; ?>');
	});
-->
</script>
<script type="text/javascript" src="<?php echo URL; ?>/js/suggest.js"></script>

<div id="content_overlay">

	<h1>Editing Custom Caller Tag</h1>
	
   	<div class="submit">
		<img src="imgs/icon-save.png" width="16" height="16" border="0" onclick="editID('<?php echo $caller_tag['id']; ?>');" />
		<div class="submit_split"></div>
   		<a href="http://www.doyoubananadance.com/" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
   	</div>
   	
	<form id="edit" onsubmit="return editID('<?php echo $caller_tag['id']; ?>');">
	<input type="hidden" name="id" value="<?php echo $caller_tag['id']; ?>" />
	<input type="hidden" name="action" value="add_caller" />
	
			<div class="col_left_sm"><div class="col_pad">
			
				<h2 style="margin-top:0px;">Caller Type</h2>
			
				<ul class="option_list" id="type_ul">
					<li id="type1"<?php if ($caller_tag['type'] == 'caller') { echo " class=\"selected\""; } ?>> Standard Replacement</li>
					<li id="type2"<?php if ($caller_tag['type'] == 'link') { echo " class=\"selected\""; } ?>> Link</li>
					<li id="type3"<?php if ($caller_tag['type'] == 'bubble') { echo " class=\"selected\""; } ?>> Help Bubble</li>
				</ul>
				<input type="hidden" name="type" value="<?php echo $caller_tag['type']; ?>" />
				
			</div></div>
			<div class="col_right_sm white_box drop_shadow"><div class="col_pad">
			
				<?php
				if ($caller_tag['type'] == 'caller') {
				?>
				<div id="type1">
					
					<h2>Standard Replacement</h2>
		   			
		   			<label>Text to Find</label>
		   			<input type="text" name="caller" style="width:90%;" value="<?php echo $caller_tag['caller']; ?>" />
		   			<p class="field_desc">What to search pages for.</p>
	
		   			<label>Replacement</label>
		   			<input type="text" name="replacement" style="width:90%;" value="<?php echo $caller_tag['replacement']; ?>" />
		   			<p class="field_desc">What to replace it with.</p>
		   			
				</div>
				<?php
				}
				else if ($caller_tag['type'] == 'link') {
				?>
				<div id="type2">
				
					<h2>Link</h2>
		   			
		   			<label>Text to Find</label>
		   			<input type="text" name="caller" style="width:90%;" value="<?php echo $caller_tag['caller']; ?>" />
		   			<p class="field_desc">What to search pages for.</p>
					
					<?php
					if (strpos($caller_tag['replacement'],'http') !== false) {
						$f1 = $caller_tag['replacement'];
						$f2 = '';
					} else {
						$article_name = $manual->get_article_name_from_id($caller_tag['replacement']);
						$f1 = $article_name;
						$f2 = $caller_tag['replacement'];
					}
					?>
					
		   			<label>Article</label>
		    			<input type="text" name="article_name" id="article_name_pos" onkeyup="suggest('<?php echo TABLE_PREFIX; ?>articles',this.value,'name','article_name_pos','id','name');" value="<?php echo $f1; ?>" style="width:97%;" />
					<input type="hidden" name="replacement" id="article_name_pos_val" value="<?php echo $f2; ?>" />
		   			<p class="field_desc">Page to link to. Either type an existing article's name or type a full URL.</p>
		   			
				</div>
				<?php
				}
				else if ($caller_tag['type'] == 'bubble') {
				?>
				<div id="type3">
				
					<h2>Help Bubble</h2>
				
		   			<label>Text to Find</label>
		   			<input type="text" name="caller" style="width:90%;" value="<?php echo $caller_tag['caller']; ?>" />
		   			<p class="field_desc">What to search pages for.</p>
	
		   			<label>Replacement</label>
		   			<input type="text" name="replacement" style="width:90%;" maxlength="255" value="<?php echo $caller_tag['replacement']; ?>" />
		   			<p class="field_desc">Help bubble content.</p>
		   			
				</div>
				
				<?php
				}
				?>
				
			</div></div>
			
	</form>

</div>

<?php
	}
}
?>