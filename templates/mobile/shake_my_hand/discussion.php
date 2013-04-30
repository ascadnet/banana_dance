				<a name="comments"></a>
      			<h1 style="text-align:center;">%article_name%</h1>
				<h2 class="center">Discussion</h2>
				<p class="center make_button"><a href="#" onclick="showDiscussion('1');return false;">Back to Page Content</a></p>
				
				<div id="bd_discussion_box">								
						<div id="primary_comment_holder">
						%discussion%
						</div>
						
						<div id="bd_comment_box">
							<h2>Post a Comment</h2>
							<textarea name="comment" id="commentText" style="width:98%;height:50px;" rows="5" cols="50" %disabled%>%box_text%</textarea>
							<p class="bd_center"><input type="button" id="commentSubmit" value="Comment" %disabled% onclick="postComment('%article_id%','');" /></p>
						</div>
				</div>