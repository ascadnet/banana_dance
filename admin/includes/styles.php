<?php

if ($privileges['is_admin'] != "1") {
	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
} else {

	include PATH . "/generated/css.php";

?>

<script>
<!--
	// --------------------------------------------
	//	CTRL-S Saves a Form
	$.ctrl('S', function() {
	    document.forms["edit"].submit();
	});
	

   	$(document).ready( function() {
   		$(".colors").miniColors();
   	});
-->
</script>

<script type="text/javascript" src="../../js/miniColors.js"></script>
<link type="text/css" rel="stylesheet" href="../../templates/html/_css/miniColors.css" />

<form id="edit" action="functions/styles.php" method="post" enctype="multipart/form-data">

   	<div class="submit">
	   	<input type="image" src="imgs/icon-save.png" />
   		<div class="submit_split"></div>
   		<a href="http://www.doyoubananadance.com/Templates/Template-System" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
   	</div>
	
	<div class="col20">
		<h1>General</h1>
		
		<h2>Background Colors</h2>
		
		<label>Body Background</label>
		<input type="text" name="background" class="colors" style="width:85px;" value="<?php echo $background; ?>" />
		
		<label>1st Scheme Color</label>
		<input type="text" name="bg1" class="colors" style="width:85px;" value="<?php echo $bg1; ?>" />
		
		<label>2nd Scheme Color</label>
		<input type="text" name="bg2" class="colors" style="width:85px;" value="<?php echo $bg2; ?>" />
		
		<label>3rd Scheme Color</label>
		<input type="text" name="bg3" class="colors" style="width:85px;" value="<?php echo $bg3; ?>" />
		
		<label>Error Color</label>
		<input type="text" name="error_color" class="colors" style="width:85px;" value="<?php echo $error_color; ?>" />
		
		<label>Hover Color</label>
		<input type="text" name="hover_color" class="colors" style="width:85px;" value="<?php echo $hover_color; ?>" />
		
		<h2>Links</h2>
		
		<label>Inactive</label>
		<input type="text" name="link_color" class="colors" style="width:85px;" value="<?php echo $link_color; ?>" /> 
		<select name="link_decoration" style="width:85px;">
			<option value="none"<?php if ($link_decoration == 'none' || empty($link_decoration)) { echo " selected=\"selected=\""; } ?>>None</option>
			<option value="underline"<?php if ($border_type == 'underline') { echo " selected=\"selected=\""; } ?>>Underlined</option>
		</select>
		
		<label>Hover</label>
		<input type="text" name="link_color_hover" class="colors" style="width:85px;" value="<?php echo $link_color_hover; ?>" /> 
		<select name="link_decoration_hover" style="width:85px;">
			<option value="none"<?php if ($link_decoration_hover == 'none' || empty($link_decoration_hover)) { echo " selected=\"selected=\""; } ?>>None</option>
			<option value="underline"<?php if ($link_decoration_hover == 'underline') { echo " selected=\"selected=\""; } ?>>Underlined</option>
		</select>
		
		<h2>Quote Boxes</h2>
		
		<label>Font</label>
		<input type="text" name="quote_font" style="width:90%;" value="<?php echo $fonts; ?>" />
		
		<label>Font Color</label>
		<input type="text" name="quote_color" class="colors" style="width:85px;" value="<?php echo $fonts_color; ?>" />
		
		<label>Font Size</label>
		<input type="text" name="quote_size" style="width:55px;" value="<?php echo $fonts_size_large; ?>" /> (pt, px, em)
		
		<center>
		<label>Margin (Top)</label>
		<input type="text" name="quote_margin_top" style="width:55px;" value="<?php echo $quote_margin_top; ?>" /> (px)
		</center>
		<br />
		<div class="col50">
		<label>Margin (Left)</label>
		<input type="text" name="quote_margin_left" style="width:55px;" value="<?php echo $quote_margin_left; ?>" /> (px)
		</div>
		<div class="col50" style="text-align:right;">
		<label>Margin (Right)</label>
		<input type="text" name="quote_margin_right" style="width:55px;" value="<?php echo $quote_margin_right; ?>" /> (px)
		</div>
		<div class="clear"></div>
		<center>
		<label>Margin (Bottom)</label>
		<input type="text" name="quote_margin_bot" style="width:55px;" value="<?php echo $quote_margin_bottom; ?>" /> (px)
		</center>
		
		<label>Options</label>
		<input type="checkbox" name="heading3_b" value="<?php echo $quote_b; ?>" /> <b>Bold</b><br /><input type="checkbox" name="heading3_i" value="<?php echo $quote_i; ?>" /> <i>Italic</i><br /><input type="checkbox" name="heading3_u" value="<?php echo $quote_u; ?>" /> <u>Underline</u>
		
		<label>Background</label>
		<input type="text" name="quote_background" class="colors" style="width:85px;" value="<?php echo $heading3_background; ?>" />
		
		
		<h2>Form Fields</h2>
		
		<h2>Submit Buttons</h2>
		
	</div>
	
	<div class="col20">
		<h1>Fonts</h1>
		
		<h2>Basics</h2>
		
		<label>Primary Font</label>
		<input type="text" name="fonts" style="width:90%;" value="<?php echo $fonts; ?>" />
		
		<label>Color</label>
		<input type="text" name="fonts_color" class="colors" style="width:85px;" value="<?php echo $fonts_color; ?>" />
		
		<label>Secondary Color</label>
		<input type="text" name="fonts_color_secondary" class="colors" style="width:85px;" value="<?php echo $fonts_color_secondary; ?>" />
		
		<label>Size (Large)</label>
		<input type="text" name="fonts_size_large" style="width:55px;" value="<?php echo $fonts_size_large; ?>" /> (pt, px, em)
		
		<label>Size (Medium)</label>
		<input type="text" name="fonts_size" style="width:55px;" value="<?php echo $fonts_size; ?>" /> (pt, px, em)
		
		<label>Size (Small)</label>
		<input type="text" name="fonts_size_small" style="width:55px;" value="<?php echo $fonts_size_small; ?>" /> (pt, px, em)
		
		<label>Size (Tiny)</label>
		<input type="text" name="fonts_size_tiny" style="width:55px;" value="<?php echo $fonts_size_tiny; ?>" /> (pt, px, em)
		
		<label>Line Height</label>
		<input type="text" name="line_height" style="width:55px;" value="<?php echo $line_height; ?>" /> (pt, px, em)
		
		
		<h2>Primary Heading</h2>
		
		<label>Primary Font</label>
		<input type="text" name="heading1_font" style="width:90%;" value="<?php echo $fonts; ?>" />
		
		<label>Color</label>
		<input type="text" name="heading1_color" class="colors" style="width:85px;" value="<?php echo $heading1_color; ?>" />
		
		<label>Line Height</label>
		<input type="text" name="heading1_size" style="width:55px;" value="<?php echo $heading1_size; ?>" /> (pt, px, em)
		
		<label>Options</label>
		<input type="checkbox" name="heading1_b" value="<?php echo $heading1_b; ?>" /> <b>Bold</b><br /><input type="checkbox" name="heading1_i" value="<?php echo $heading1_i; ?>" /> <i>Italic</i><br /><input type="checkbox" name="heading1_u" value="<?php echo $heading1_u; ?>" /> <u>Underline</u>
		
		<label>Background</label>
		<input type="text" name="heading1_background" class="colors" style="width:85px;" value="<?php echo $heading1_background; ?>" />
		
		<br />
		<div class="col50">
		<label>Margin (Top)</label>
		<input type="text" name="heading1_marg_top" style="width:55px;" value="<?php echo $heading1_marg_top; ?>" /> (px)
		</div>
		<div class="col50">
		<label>Margin (Bottom)</label>
		<input type="text" name="heading1_marg_bot" style="width:55px;" value="<?php echo $heading1_marg_bot; ?>" /> (px)
		</div>
		<div class="clear"></div>
		
		
		<h2>2nd Heading</h2>
		
		<label>Primary Font</label>
		<input type="text" name="heading2_font" style="width:90%;" value="<?php echo $fonts; ?>" />
		
		<label>Color</label>
		<input type="text" name="heading2_color" class="colors" style="width:85px;" value="<?php echo $heading2_color; ?>" />
		
		<label>Line Height</label>
		<input type="text" name="heading2_size" style="width:55px;" value="<?php echo $heading2_size; ?>" /> (pt, px, em)
		
		<label>Options</label>
		<input type="checkbox" name="heading2_b" value="<?php echo $heading2_b; ?>" /> <b>Bold</b><br /><input type="checkbox" name="heading2_i" value="<?php echo $heading2_i; ?>" /> <i>Italic</i><br /><input type="checkbox" name="heading2_u" value="<?php echo $heading2_u; ?>" /> <u>Underline</u>
		
		<label>Background</label>
		<input type="text" name="heading2_background" class="colors" style="width:85px;" value="<?php echo $heading2_background; ?>" />
		
		<br />
		<div class="col50">
		<label>Margin (Top)</label>
		<input type="text" name="heading2_marg_top" style="width:55px;" value="<?php echo $heading2_marg_top; ?>" /> (px)
		</div>
		<div class="col50">
		<label>Margin (Bottom)</label>
		<input type="text" name="heading2_marg_bot" style="width:55px;" value="<?php echo $heading2_marg_bot; ?>" /> (px)
		</div>
		<div class="clear"></div>
		
		
		
		<h2>3rd Heading</h2>
		
		<label>Primary Font</label>
		<input type="text" name="heading3_font" style="width:90%;" value="<?php echo $fonts; ?>" />
		
		<label>Color</label>
		<input type="text" name="heading3_color" class="colors" style="width:85px;" value="<?php echo $heading3_color; ?>" />
		
		<label>Line Height</label>
		<input type="text" name="heading3_size" style="width:55px;" value="<?php echo $heading3_size; ?>" /> (pt, px, em)
		
		<label>Options</label>
		<input type="checkbox" name="heading3_b" value="<?php echo $heading3_b; ?>" /> <b>Bold</b><br /><input type="checkbox" name="heading3_i" value="<?php echo $heading3_i; ?>" /> <i>Italic</i><br /><input type="checkbox" name="heading3_u" value="<?php echo $heading3_u; ?>" /> <u>Underline</u>
		
		<label>Background</label>
		<input type="text" name="heading3_background" class="colors" style="width:85px;" value="<?php echo $heading3_background; ?>" />
		
		<br />
		<div class="col50">
		<label>Margin (Top)</label>
		<input type="text" name="heading3_marg_top" style="width:55px;" value="<?php echo $heading3_marg_top; ?>" /> (px)
		</div>
		<div class="col50">
		<label>Margin (Bottom)</label>
		<input type="text" name="heading3_marg_bot" style="width:55px;" value="<?php echo $heading3_marg_bot; ?>" /> (px)
		</div>
		<div class="clear"></div>
		
		<h2></h2>
		
	</div>
	
	<div class="col20">
		<h1>The "Nitty-Gritty"</h1>
		
		<h2>Whitespace Controls</h2>
		<p>The program will use your maximum whitespace and minimum whitespace controls to calculate working ratios.</p>
		
		<div class="col50">
		<label>Minimum</label>
		<input type="text" name="whitespace_ratio_min" style="width:55px;" value="<?php echo $whitespace_ratio_min; ?>" /> (px)
		</div>
		<div class="col50">
		<label>Maximum</label>
		<input type="text" name="whitespace_ratio_max" style="width:55px;" value="<?php echo $whitespace_ratio_max; ?>" /> (px)
		</div>
		<div class="clear"></div>
		
		<h2>Borders</h2>
		
		<label>Color</label>
		<input type="text" name="border_color" class="colors" style="width:85px;" value="<?php echo $border_color; ?>" />
		
		<label>Thickness</label>
		<input type="text" name="border_px" style="width:55px;" value="<?php echo $border_px; ?>" /> (px)
		
		<label>Type</label>
		<select name="border_type">
			<option value="solid"<?php if ($border_type == 'solid' || empty($border_type)) { echo " selected=\"selected=\""; } ?>>Solid Line</option>
			<option value="dotted"<?php if ($border_type == 'dotted') { echo " selected=\"selected=\""; } ?>>Dotted Line</option>
			<option value="dashed"<?php if ($border_type == 'dashed') { echo " selected=\"selected=\""; } ?>>Dashed Line</option>
		</select>
		<br />
		
		<div class="col50">
		<label>Rounding (Large)</label>
		<input type="text" name="div_rounding" style="width:55px;" value="<?php echo $div_rounding; ?>" /> (px)
		</div>
		<div class="col50">
		<label>Rounding (Small)</label>
		<input type="text" name="div_rounding_less" style="width:55px;" value="<?php echo $div_rounding_less; ?>" /> (px)
		</div>
		<div class="clear"></div>
		
		
		<h2>Shadows</h2>
		
		<label>Color</label>
		<input type="text" name="shadow_color" class="colors" style="width:85px;" value="<?php echo $shadow_color; ?>" />
		
		<label>Strength</label>
		<input type="text" name="shadow_opacity" style="width:55px;" maxlength="3" value="<?php echo $shadow_opacity * 100; ?>" /> (%)
		
		<label>Size</label>
		<input type="text" name="shadow_blur_radius" style="width:55px;" maxlength="3" value="<?php echo $shadow_blur_radius; ?>" /> (px)
		
	</div>
	
	<div class="clear"></div>
	
</form>

<?php
}
?>