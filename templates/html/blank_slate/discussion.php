				<a name="comments"></a>
				
				<div id="bd_discussion_box">
				
					<div id="primary_comment_holder">
						%discussion%
					</div>
					
					<div id="bd_comment_box">
						<div id="postingTo" style="float:left;margin-right:20px;"></div>
						<form action="#" onsubmit="return postComment('%article_id%','');">
						<textarea name="comment" id="commentText" rows="5" cols="50" %disabled%>%box_text%</textarea>
		   				<div id="commentFormatting" style="float:left;margin-right:20px;">
		   					<a href="#" onclick="addCaller('commentText','****','2');return false;"><img src="%program_url%/templates/html/_imgs/editor/text_bold.png" width="20" height="20" border="0" alt="Bold" title="Bold" /></a> <a href="#" onclick="addCaller('commentText','////','2');return false;"><img src="%program_url%/templates/html/_imgs/editor/text_italic.png" width="20" height="20" border="0" alt="Italic" title="Italic" /></a> <a href="#" onclick="addCaller('commentText','____','2');return false;"><img src="%program_url%/templates/html/_imgs/editor/text_underline.png" width="20" height="20" border="0" alt="Underline" title="Underline" /></a> <a href="#" onclick="addCaller('commentText','\n    \n','1');return false;"><img src="%program_url%/templates/html/_imgs/editor/code.png" width="20" height="20" border="0" alt="Code" title="Code" /></a>
		   				</div>
						<input type="submit" id="commentSubmit" value="Post Comment" %disabled% />
						</form>
						<div class="clear"></div>
					</div>
				
				</div>