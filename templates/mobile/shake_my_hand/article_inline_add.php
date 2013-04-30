<link href="%program_url%/templates/html/_css/inline_editor.css" rel="stylesheet" type="text/css" />
<script src="%program_url%/js/jquery.ctrl.js" type="text/javascript"></script>
<script src="%program_url%/js/suggest.js" type="text/javascript"></script>
<script src="%program_url%/js/aie_editor_functions.js" type="text/javascript"></script>

<form id="bd_articleEdit">
<div id="bd_aie_editor">
%options_menu%
%meta_menu%
	<div id="bd_aie_top">
		<ul id="bd_aie_top_left">
			<li class="name"><input type="text" name="name" value="Input a name" tabindex="1" onfocus="clearText(this,'Input a name');" style="width:250px;" /></li>
			<li class="option" id="baie_options"><a href="#" onclick="showArticleOptions();return false;"><img src="%theme%/imgs/icon-options.png" width="16" height="16" border="0" alt="Options" title="Options" style="vertical-align:middle;" /></a></li>
			<li class="option" id="baie_meta"><a href="#" onclick="showArticleMeta();return false;"><img src="%theme%/imgs/icon-meta.png" width="16" height="16" border="0" alt="Meta Information" title="Meta Information" style="vertical-align:middle;" /></a></li>
		</ul>
		<ul id="bd_aie_top_right">
			<li><a href="#" onclick="minimizeEdit('Untitled');return false;"><img src="%theme%/imgs/icon-minimize.png" width="16" height="16" border="0" alt="Minimize editor" title="Minimize Editor" style="vertical-align:middle;" /></a></li>
			<li><input type="button" id="bd_air_done_button" value="Done" onclick="saveArticle('1');" /></li>
			<li><input type="button" id="bd_air_save_button" value="Save" onclick="saveArticle('0');" /></li>
			<li><input type="button" id="bd_air_preview_button" value="Preview" onclick="previewArticle();" /></li>
			<li class="bd_aie_saved" id="bd_aie_saved"></li>
		</ul>
		<div class="bd_clear"></div>
	</div>
	<div id="bd_aie_body">
		<div id="bd_aie_formatting">%formatting_guide%</div>
		<div class="bd_clear"></div>
		%editor%
		<div id="bd_aie_container">
			<textarea name="article_content" cols="1" rows="1" id="content" tabindex="2">%article_content%</textarea>
		</div>
	</div>
	<div id="bd_article_preview">
	</div>
</div>
</form>