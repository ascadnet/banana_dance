<?php

if ($privileges['is_admin'] != "1") {
	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
} else {
	
?>


<script>
<!--
	var current_function = 'custom_caller';
	
	function toggleShow(showing) {
		$('#type_ul li').each(function(index) {
		    id = $(this).attr('id');
		    together = id + "_options";
		    $('#' + together).hide();
		});
		$('#' + showing).show();
	}
	
	// --------------------------------------------
	//	CTRL-S Saves a Form
	$.ctrl('S', function() {
	    editID('new');
	});
-->
</script>
<script type="text/javascript" src="<?php echo URL; ?>/js/suggest.js"></script>

<div id="content_overlay">

	<h1>Editing Custom Caller Tag</h1>
	
   	<div class="submit">
		<img src="imgs/icon-save.png" width="16" height="16" border="0" onclick="editID('new');" />
		<div class="submit_split"></div>
   		<a href="http://www.doyoubananadance.com/" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
   	</div>
   	
	<form id="edit" onsubmit="return editID('new');">
	<input type="hidden" name="id" value="new" />
	<input type="hidden" name="action" value="add_caller" />
	
			<div class="col_left_sm"><div class="col_pad">
			
				<h2 style="margin-top:0px;">Caller Type</h2>
			
	    			<label>Type</label>
				<ul class="option_list" id="type_ul">
					<li id="type1" class="selected">
						<input type="radio" name="type" value="caller" onclick="toggleShow('type1_options');" checked="checked" /> Standard Replacement
					</li>
					<li id="type2">
						<input type="radio" name="type" value="link" onclick="toggleShow('type2_options');" /> Link
					</li>
					<li id="type3">
						<input type="radio" name="type" value="bubble" onclick="toggleShow('type3_options');" /> Help Bubble
					</li>
				</ul>
				
			</div></div>
			<div class="col_right_sm white_box drop_shadow"><div class="col_pad">
			
				<div id="type1_options">
					
					<h2>Standard Replacement</h2>
		   			
		   			<label>Text to Find</label>
		   			<input type="text" name="caller_1" style="width:90%;" value="" />
		   			<p class="field_desc">What to search pages for.</p>
	
		   			<label>Replacement</label>
		   			<input type="text" name="replacement_1" style="width:90%;" value="" />
		   			<p class="field_desc">What to replace it with.</p>
		   			
				</div>
				
				<div id="type2_options" style="display:none;">
				
					<h2>Link</h2>
		   			
		   			<label>Text to Find</label>
		   			<input type="text" name="caller_2" style="width:90%;" value="" />
		   			<p class="field_desc">What to search pages for.</p>
	
		   			<label>Article</label>
		    			<input type="text" name="article_name" id="article_name_pos" onkeyup="suggest('<?php echo TABLE_PREFIX; ?>articles',this.value,'name','article_name_pos','id','name');" value="" style="width:97%;" />
					<input type="hidden" name="replacement_2" id="article_name_pos_val" value="" />
		   			<p class="field_desc">Page to link to. Either type an existing article's name or type a full URL.</p>
		   			
				</div>
				
				<div id="type3_options" style="display:none;">
				
					<h2>Help Bubble</h2>
				
		   			<label>Text to Find</label>
		   			<input type="text" name="caller_3" style="width:90%;" value="" />
		   			<p class="field_desc">What to search pages for.</p>
	
		   			<label>Replacement</label>
		   			<input type="text" name="replacement_3" style="width:90%;" maxlength="255" value="" />
		   			<p class="field_desc">Help bubble content.</p>
		   			
				</div>
				
			</div></div>
			
	</form>

</div>

<?php
}
?>