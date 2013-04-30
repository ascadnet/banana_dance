<div id="bd_aie_editor">
	<script src="%program_url%/js/aie_autosave.js" type="text/javascript"></script>

	<form id="bd_articleEdit" onsubmit="return saveArticle('0');">

	<div id="bd_edit_left">
		<h3 onclick="return expandSection('1');" class="notop"><img src="%program_url%/templates/html/_imgs/editor/tile_up.png" width="20" height="20" border="0" alt="Contract" title="Contract" class="up_down" id="menuimg1" />Page Overview</h3>
		<div class="left_pad" id="menu1">
			%main_menu%
		</div>
		
		<h3 onclick="return expandSection('2');"><img src="%program_url%/templates/html/_imgs/editor/tile_down.png" width="20" height="20" border="0" alt="Expand" title="Expand" class="up_down" id="menuimg2" />Access Controls</h3>		
		<div class="left_pad" id="menu2" style="display:none;">
			%access_menu%
		</div>

		<h3 onclick="return expandSection('3');"><img src="%program_url%/templates/html/_imgs/editor/tile_down.png" width="20" height="20" border="0" alt="Expand" title="Expand" class="up_down" id="menuimg3" />Settings</h3>
		<div class="left_pad" id="menu3" style="display:none;">
			%options_menu%
		</div>
		
		<h3 onclick="return expandSection('4');"><img src="%program_url%/templates/html/_imgs/editor/tile_down.png" width="20" height="20" border="0" alt="Expand" title="Expand" class="up_down" id="menuimg4" />Commenting</h3>		
		<div class="left_pad" id="menu4" style="display:none;">
			%comment_menu%
		</div>

		<h3 onclick="return expandSection('5');"><img src="%program_url%/templates/html/_imgs/editor/tile_down.png" width="20" height="20" border="0" alt="Expand" title="Expand" class="up_down" id="menuimg5" />Meta Information</h3>		
		<div class="left_pad" id="menu5" style="display:none;">
			%meta_menu%
		</div>
	</div>
		
	<div id="bd_edit_right">
	
		<div id="bd_edit_icons">%formatting_guide%</div>
			
		<ul id="bd_right_save">
			<li class="close"><center><a href="#" onclick="return closeArticleEdit();"><img src="%program_url%/templates/html/_imgs/icon-close.png" width="20" height="20" border="0" alt="Close editor" title="Close Editor" /></a></center></li>
			<li class="minimize"><center><a href="#" onclick="return  minimizeEdit('%article_name%');"><img src="%program_url%/templates/html/_imgs/icon-minimize.png" width="20" height="20" border="0" alt="Minimize editor" title="Minimize Editor" /></a></center></li>
			<li style="margin:12px 0 12px 0;"><center><a href="#" onclick="return showWidgets();"><img src="%program_url%/templates/html/_imgs/editor/plugin.png" width="20" height="20" border="0" alt="Widget or Plugin" title="Widget or Plugin" /></a></center></li>
			<li><center><input type="image" src="%program_url%/templates/html/_imgs/editor/icon-save.png" width="20" height="20" border="0" alt="Save and continue editing" title="Save and continue editing" /></center></li>
			<li><center><a href="#" onclick="return previewArticle();"><img src="%program_url%/templates/html/_imgs/editor/icon-preview.png" width="20" height="20" border="0" alt="Preview Page" title="Preview Page" /></a></center></li>
			<li><center><a href="#" onclick="return saveArticle('1');"><img src="%program_url%/templates/html/_imgs/editor/icon-done.png" width="20" height="20" border="0" alt="Save and finalize edits" title="Save and finalize edits" /></a></center></li>
			<li><center><a href="http://www.bananadance.org/Pages/Page-Editor" target="_blank"><img src="%program_url%/templates/html/_imgs/editor/help_bubble.png" width="16" height="16" border="0" alt="Get Help" title="Get Help" /></a></center></li>
		</ul>
		
   		<div id="bd_aie_container">
			%editor%
   			<textarea name="article_content" class="sys_field" cols="1" rows="1" tabindex="2" id="content" onkeyup="return triggerEdit();" onchange="return triggerEdit();" style="width:100%;height:500px;">%article_content%</textarea>
   			<script> jQuery(document).ready(function () {  $("#content").tabby();  }); </script>
   		</div>
	
	</div>
	<div id="bd_article_preview" style="display:none;"></div>

	</form>
</div>