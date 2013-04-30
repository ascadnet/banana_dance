<style type="text/css">
<!--
#formatting_guide {
	margin: 0;
	font-size: 10pt;
	font-family: arial;
}

ul.bubble_options {
	list-style: none;
	margin: 0;
	padding: 12px 0 12px 0;
}

ul.bubble_options li.divider {
	background: none;
	width: 6px;
	padding: 0;
	margin: 0;
	float: left;
}

ul.bubble_options li {
	margin: 0;
	line-height: 16px;
	height: 16px;
	padding: 0 2px 0 0;
	font-size: 80%;
	font-weight: bold;
	color: #fff;
	float: left;
}
-->
</style>

<?php

if (! empty($article['format_type'])) {
	if ($article['format_type'] == '1') {
		$f_type = 'wiki';
	} else {
		$f_type = 'cms';
	}
} else {
	$f_type = $theme_type;
}


if ($format_type_editor == 'WYSIWYG') {
?>
	

	<script type="text/javascript">
		var ed_format_type = 'cms';
		var using_editor = 'wys';
	</script>

<?php
}

else if ($f_type == 'cms') {
?>


	<script type="text/javascript">
		$.ctrl('B', function() { addCaller('content','<b></b>','4');return false; });
		$.ctrl('U', function() { addCaller('content','<u></u>','4');return false; });
		$.ctrl('I', function() { addCaller('content','<i></i>','4');return false; });
	
		var ed_format_type = 'cms';
	</script>

		<div id="formatting_guide">
			<ul class="bubble_options">
				<li id="format_type_current"><b>Full HTML</b></li>
				<li class="divider"></li>
				
				<li><a href="#" onclick="return addCaller('content','<h1></h1>','5');"><img src="%program_url%/templates/html/_imgs/editor/text_heading_1.png" width="20" height="20" border="0" alt="Major Heading" title="Major Heading" /></a></li>
				<li><a href="#" onclick="return addCaller('content','<h2></h2>','5');"><img src="%program_url%/templates/html/_imgs/editor/text_heading_2.png" width="20" height="20" border="0" alt="Secondary Heading" title="Secondary Heading" /></a></li>
				<li><a href="#" onclick="return addCaller('content','<h3></h3>','5');"><img src="%program_url%/templates/html/_imgs/editor/text_heading_3.png" width="20" height="20" border="0" alt="Smaller Heading" title="Smaller Heading" /></a></li>
				
				<li class="divider"></li>
				
				<li><a href="#" onclick='return addCaller("content","<hr \/\>","0");'><img src="%program_url%/templates/html/_imgs/editor/text_horizontalrule.png" width="20" height="20" border="0" alt="Horizontal Section Divider" title="Horizontal Section Divider" /></a></li>
				<li><a href="#" onclick='return addCaller("content","<br \/\>","0");'><img src="%program_url%/templates/html/_imgs/editor/line_break.png" width="20" height="20" border="0" alt="Link Break" title="Link Break" /></a></li>
		
				<li class="divider"></li>
				
				<li><a href="#" onclick="return addCaller('content','<b></b>','4');"><img src="%program_url%/templates/html/_imgs/editor/text_bold.png" width="20" height="20" border="0" alt="Bold" title="Bold" /></a></li>
				<li><a href="#" onclick="return addCaller('content','<u></u>','4');"><img src="%program_url%/templates/html/_imgs/editor/text_underline.png" width="20" height="20" border="0" alt="Underline" title="Underline" /></a></li>
				<li><a href="#" onclick="return addCaller('content','<i></i>','4');"><img src="%program_url%/templates/html/_imgs/editor/text_italic.png" width="20" height="20" border="0" alt="Italics" title="Italics" /></a></li>
				<li><a href="#" onclick='return addCaller("content","<p style=\"text-align:center;\"></p>","4");'><img src="%program_url%/templates/html/_imgs/editor/align_center.png" width="20" height="20" border="0" alt="Text align center" title="Text align center" /></a></li>
				<li><a href="#" onclick='return addCaller("content","<p style=\"text-align:right;\"></p>","4");'><img src="%program_url%/templates/html/_imgs/editor/align_right.png" width="20" height="20" border="0" alt="Text align right" title="Text align right" /></a></li>
				<li><a href="#" onclick="return addCaller('content','<s></s>','4');"><img src="%program_url%/templates/html/_imgs/editor/strike.png" width="20" height="20" border="0" alt="Strikethrough text" title="Strikethrough text" /></a></li>
				<li><a href="#" onclick="return addCaller('content','<sup></sup>','6');"><img src="%program_url%/templates/html/_imgs/editor/supscript.png" width="20" height="20" border="0" alt="supSCRIPT" title="supSCRIPT" /></a></li>
				<li><a href="#" onclick="return addCaller('content','<sub></sub>','6');"><img src="%program_url%/templates/html/_imgs/editor/subscript.png" width="20" height="20" border="0" alt="SUBscript" title="SUBscript" /></a></li>
				
				<li class="divider"></li>
				
				<li><a href="#" onclick="return addCaller('content','<ul>\n  <li></li>\n</ul>','11');"><img src="%program_url%/templates/html/_imgs/editor/list_bullets.png" width="20" height="20" border="0" alt="Bullet List" title="Bullet List" /></a></li>
				<li><a href="#" onclick="return addCaller('content','<ol>\n  <li></li>\n</ol>','11');"><img src="%program_url%/templates/html/_imgs/editor/list_numbers.png" width="20" height="20" border="0" alt="Numbered List" title="Numbered List" /></a></li>
				<li><a href="#" onclick="return addCaller('content','<dl>\n  <dt></dt><dd></dd>\n</dl>','20');"><img src="%program_url%/templates/html/_imgs/editor/list_def.png" width="20" height="20" border="0" alt="Definition List" title="Definition List" /></a></li>
				
				<li class="divider"></li>
				
				<li><a href="#" onclick="return showAddLink('internal');"><img src="%program_url%/templates/html/_imgs/editor/link.png" width="20" height="20" border="0" alt="Internal Site Link" title="Internal Site Link" /></a></li>
				<li><a href="#" onclick="return showAddLink('external');"><img src="%program_url%/templates/html/_imgs/editor/link_ext.png" width="20" height="20" border="0" alt="External Site Link" title="External Site Link" /></a></li>
	
				<li><a href="#" onclick='return uploadFile("image","cms");'><img src="%program_url%/templates/html/_imgs/editor/image.png" width="20" height="20" border="0" alt="Image" title="Image" /></a></li>
				<li><a href="#" onclick='return uploadFile("file","wiki");'><img src="%program_url%/templates/html/_imgs/editor/file_attach.png" width="20" height="20" border="0" alt="Downloadable File" title="Downloadable File" /></a></li>
				<li><a href="#" onclick='addCaller("content","<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr>\n  <th></th>\n  <th></th>\n</tr><tr>\n  <td></td>\n  <td></td>\n</tr></table>","0");return false;'><img src="%program_url%/templates/html/_imgs/editor/table.png" width="20" height="20" border="0" alt="Table" title="Table" /></a></li>
				
				<li class="divider"></li>
				
				<?php
					$rand = rand(100,99999);
					$theme_images = URL . "/templates/html/" . $theme . "/imgs";
				?>
				<li><a href="#" onclick="return addCaller('content','<blockquote></blockquote>','13');"><img src="%program_url%/templates/html/_imgs/editor/quote.png" width="20" height="20" border="0" alt="Quoted Text" title="Quoted Text" /></a></li>
				<li><a href="#" onclick='return addCaller("content","<span style=\"background-color:#FFEC4D;\"></span>","7");'><img src="%program_url%/templates/html/_imgs/editor/highlight.png" width="20" height="20" border="0" alt="Highlight Text" title="Highlight Text" /></a></li>
				<li><a href="#" onclick='return addCaller("content","<img src=\"<?php echo $theme_images; ?>/icon-help.png\" width=\"12\" height=\"12\" border=\"0\" alt=\"Additional Information\" class=\"bd_help_icon\" id=\"<?php echo $rand; ?>\" onmouseover=openHelpBubble(\"<?php echo $rand; ?>\"); onmouseout=closeHelpBubble(\"<?php echo $rand; ?>\"); /><span class=\"bd_help_bubble\" id=\"help_<?php echo $rand; ?>\"></span>","7");'><img src="%program_url%/templates/html/_imgs/editor/help_bubble.png" width="20" height="20" border="0" alt="Help bubble" title="Help bubble" /></a></li>
				<li><a href="#" onclick='return addCaller("content","<div style=\"width:200px;float:left;\"></div>","6");'><img src="%program_url%/templates/html/_imgs/editor/left_menu.png" width="20" height="20" border="0" alt="Left floating menu" title="Left floating menu" /></a></li>
				<li><a href="#" onclick='return addCaller("content","<div style=\"width:200px;float:right;\"></div>","6");'><img src="%program_url%/templates/html/_imgs/editor/right_menu.png" width="20" height="20" border="0" alt="Right floating menu" title="Right floating menu" /></a></li>
				<li class="divider"></li>
				
				<li><a href="#" onclick='return addCaller("content","<div class=\"\"></div>","8");'><img src="%program_url%/templates/html/_imgs/editor/custom_div.png" width="20" height="20" border="0" alt="Custom section with CSS styling" title="Custom section with CSS styling" /></a></li>
				<li id="editor_widgets"><a href="#" onclick="return showWidgets();"><img src="%program_url%/templates/html/_imgs/editor/plugin.png" width="20" height="20" border="0" alt="Widget or Plugin" title="Widget or Plugin" /></a></li>
			</ul>
		</div>

<?php
} else {
?>


	<script type="text/javascript">
		$.ctrl('B', function() { addCaller('content','****','2');return false; });
		$.ctrl('U', function() { addCaller('content','____','2');return false; });
		$.ctrl('I', function() { addCaller('content','////','2');return false; });
		var ed_format_type = 'wiki';
	</script>
	
		<div id="formatting_guide">
			<ul class="bubble_options">
				<li id="format_type_current"><a href="http://www.doyoubananadance.com/Pages/Wiki-Syntax" target="_blank">Wiki Syntax</a></li>
				<li class="divider"></li>
				
				<li><a href="#" onclick="return addCaller('content','--------','4');"><img src="%program_url%/templates/html/_imgs/editor/text_heading_1.png" width="20" height="20" border="0" alt="Major Heading" title="Major Heading" /></a></li>
				<li><a href="#" onclick="return addCaller('content','------','3');"><img src="%program_url%/templates/html/_imgs/editor/text_heading_2.png" width="20" height="20" border="0" alt="Secondary Heading" title="Secondary Heading" /></a></li>
				<li><a href="#" onclick="return addCaller('content','----','2');"><img src="%program_url%/templates/html/_imgs/editor/text_heading_3.png" width="20" height="20" border="0" alt="Smaller Heading" title="Smaller Heading" /></a></li>
				
				<li class="divider"></li>
				
				<li><a href="#" onclick="return addCaller('content','====','0');"><img src="%program_url%/templates/html/_imgs/editor/text_horizontalrule.png" width="20" height="20" border="0" alt="Horizontal Section Divider" title="Horizontal Section Divider" /></a></li>
				<li><a href="#" onclick="return addCaller('content','\\\\','0');"><img src="%program_url%/templates/html/_imgs/editor/line_break.png" width="20" height="20" border="0" alt="Link Break" title="Link Break" /></a></li>
		
				<li class="divider"></li>
				
				<li><a href="#" onclick="return addCaller('content','****','2');"><img src="%program_url%/templates/html/_imgs/editor/text_bold.png" width="20" height="20" border="0" alt="Bold" title="Bold" /></a></li>
				<li><a href="#" onclick="return addCaller('content','____','2');"><img src="%program_url%/templates/html/_imgs/editor/text_underline.png" width="20" height="20" border="0" alt="Underline" title="Underline" /></a></li>
				<li><a href="#" onclick="return addCaller('content','////','2');"><img src="%program_url%/templates/html/_imgs/editor/text_italic.png" width="20" height="20" border="0" alt="Italics" title="Italics" /></a></li>
				<li><a href="#" onclick="return addCaller('content','<->','0');"><img src="%program_url%/templates/html/_imgs/editor/align_center.png" width="20" height="20" border="0" alt="Text align center" title="Text align center" /></a></li>
				<li><a href="#" onclick="return addCaller('content','-->','0');"><img src="%program_url%/templates/html/_imgs/editor/align_right.png" width="20" height="20" border="0" alt="Text align right" title="Text align right" /></a></li>
				<li><a href="#" onclick="return addCaller('content','[strike][/strike]','9');"><img src="%program_url%/templates/html/_imgs/editor/strike.png" width="20" height="20" border="0" alt="Strikethrough text" title="Strikethrough text" /></a></li>
				<li><a href="#" onclick="return addCaller('content','[sup][/sup]','6');"><img src="%program_url%/templates/html/_imgs/editor/supscript.png" width="20" height="20" border="0" alt="supSCRIPT" title="supSCRIPT" /></a></li>
				<li><a href="#" onclick="return addCaller('content','[sub][/sub]','6');"><img src="%program_url%/templates/html/_imgs/editor/subscript.png" width="20" height="20" border="0" alt="SUBscript" title="SUBscript" /></a></li>
				
				<li class="divider"></li>
				
				<li><a href="#" onclick="return addCaller('content','  - ','0');"><img src="%program_url%/templates/html/_imgs/editor/list_bullets.png" width="20" height="20" border="0" alt="Bullet List" title="Bullet List" /></a></li>
				<li><a href="#" onclick="return addCaller('content','  # ','0');"><img src="%program_url%/templates/html/_imgs/editor/list_numbers.png" width="20" height="20" border="0" alt="Numbered List" title="Numbered List" /></a></li>
				<li><a href="#" onclick="return  addCaller('content','[def]\nTerm: Definition\n[/def]','0');"><img src="%program_url%/templates/html/_imgs/editor/list_def.png" width="20" height="20" border="0" alt="Definition List" title="Definition List" /></a></li>
				
				<li class="divider"></li>
				
				<li><a href="#" onclick="return showAddLink('internal');"><img src="%program_url%/templates/html/_imgs/editor/link.png" width="20" height="20" border="0" alt="Internal Site Link" title="Internal Site Link" /></a></li>
				<li><a href="#" onclick="return showAddLink('external');"><img src="%program_url%/templates/html/_imgs/editor/link_ext.png" width="20" height="20" border="0" alt="External Site Link" title="External Site Link" /></a></li>
	
				<li><a href="#" onclick='return uploadFile("image","wiki");'><img src="%program_url%/templates/html/_imgs/editor/image.png" width="20" height="20" border="0" alt="Image" title="Image" /></a></li>
				<li><a href="#" onclick='return uploadFile("file","wiki");'><img src="%program_url%/templates/html/_imgs/editor/file_attach.png" width="20" height="20" border="0" alt="Downloadable File" title="Downloadable File" /></a></li>
				<li><a href="#" onclick="return addCaller('content','[table]\n^Column 1 Heading^Column 2 Heading\nRow 1, Col 1 Text|Row 1, Col 2 Text\n[/table]','0');"><img src="%program_url%/templates/html/_imgs/editor/table.png" width="20" height="20" border="0" alt="Table" title="Table" /></a></li>
				
				<li class="divider"></li>
				
				<li><a href="#" onclick="return addCaller('content','[quote]\n\n[/quote]','9');"><img src="%program_url%/templates/html/_imgs/editor/quote.png" width="20" height="20" border="0" alt="Quoted Text" title="Quoted Text" /></a></li>
				<li><a href="#" onclick="return addCaller('content','[highlight][/highlight]','12');"><img src="%program_url%/templates/html/_imgs/editor/highlight.png" width="20" height="20" border="0" alt="Highlight Text" title="Highlight Text" /></a></li>
				<li><a href="#" onclick="return addCaller('content','[?][/?]','4');"><img src="%program_url%/templates/html/_imgs/editor/help_bubble.png" width="20" height="20" border="0" alt="Help bubble" title="Help bubble" /></a></li>
				<li><a href="#" onclick="return addCaller('content','[fn][/fn]','5');"><img src="%program_url%/templates/html/_imgs/editor/footnote.png" width="20" height="20" border="0" alt="Footnote" title="Footnote" /></a></li>
				<li><a href="#" onclick="return addCaller('content','[left]\n\n[/left]','8');"><img src="%program_url%/templates/html/_imgs/editor/left_menu.png" width="20" height="20" border="0" alt="Left floating menu" title="Left floating menu" /></a></li>
				<li><a href="#" onclick="return addCaller('content','[right]\n\n[/right]','9');"><img src="%program_url%/templates/html/_imgs/editor/right_menu.png" width="20" height="20" border="0" alt="Right floating menu" title="Right floating menu" /></a></li>
				
				<li class="divider"></li>
				
				<li><a href="#" onclick="return addCaller('content','{{class_name|CSS style rules here}}\nSection content here\n{{end}}','8');"><img src="%program_url%/templates/html/_imgs/editor/custom_div.png" width="20" height="20" border="0" alt="Custom section with CSS styling" title="Custom section with CSS styling" /></a></li>
				
				<li><a href="#" onclick="return addCaller('content','[html]\n\n[/html]','8');;"><img src="%program_url%/templates/html/_imgs/editor/html.png" width="20" height="20" border="0" alt="HTML: No wiki formatting" title="HTML: No wiki formatting" /></a></li>
				<li><a href="#" onclick="return addCaller('content','[code:language:line numbers (1 or 0)]\nCode here\n[/code]','7');"><img src="%program_url%/templates/html/_imgs/editor/code.png" width="20" height="20" border="0" alt="Code Block" title="Code Block" /></a></li>
				<li><a href="#" onclick="return showWidgets();"><img src="%program_url%/templates/html/_imgs/editor/plugin.png" width="20" height="20" border="0" alt="Widget or Plugin" title="Widget or Plugin" /></a></li>
				
			</ul>
		</div>
		
<?php
}
?>
		