
<!-- Begin comment thread: %comment_id% -->
<a name="comment%comment_id%"></a>
<div id="bd_com_overall%comment_id%" %com_style%>
	<div class="bd_comment %add_class%" id="comment%comment_id%">
		<div class="bd_comment_left">
			<center>
			<a href="%comment_user_link%"><img src="%comment_user_thumbnail%" border="0" alt="%comment_username%" title="%comment_username%" /></a>
			</center>
		</div>
		<div class="bd_comment_right">
			%expand_code%
			<div class="bd_comment_top">
<span class="bd_comment_date">%comment_date%</span>
<span class="bd_comment_user">%comment_username%</span>
			</div>
			<div class="bd_comment_main" id="commentMain%comment_id%">%comment%</div>
			
			<div id="bd_comment_rating" class="small">
<div class="bd_comment_total">Comment Score: <span id="voteTotal%comment_id%">%comment_score%</span></div>
<div id="votedUp%comment_id%" class="bd_comment_up %voted_up%"><a href="#" onclick="vote_comment('%comment_id%','1');return false;"><img src="%images%/upvote.png" width="16" height="16" border="0" alt="Upvote this comment" title="Upvote this comment" class="vote_icon" /> Upvote (%comment_upvotes%)</a></div>
<div id="votedDown%comment_id%" class="bd_comment_down %voted_down%"><a href="#" onclick="vote_comment('%comment_id%','-1');return false;"><img src="%images%/downvote.png" width="16" height="16" border="0" alt="Downvote this comment" title="Downvote this comment" class="vote_icon" /> Downvote (%comment_downvotes%)</a></div>
			</div>
			
			<div class="bd_comment_options" id="showReplyTop%comment_id%">%comment_options%</div>
		</div>
	 	<div class="bd_clear"></div>
	</div>
	<!-- Begin subcomments: %comment_id% -->
	%subcomments%
	<!-- End subcomments: %comment_id% -->
</div>
<!-- End comment thread: %comment_id% -->