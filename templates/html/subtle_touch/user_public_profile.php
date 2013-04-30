<div id="main_right_nomarg">
	<a href="%program_url%/user/%username%"><img src="%profile_pic%" border="0" alt="%username%" title="%username%" /></a>
	<h3><a href="%program_url%/user/%username%/">%username%</a></h3>
	<p class="small">Joined %time_member% ago.<br />Score: %myScore%<br />Comment Score: %score%</p>
	
	<span class="right_title">Links</span>
	%user_menu%
	
	<span class="right_title">Badges (%total_badges%)</span>
	%badges%
</div>
   
<div id="main_center">

	<!--start:post-->
	<script type="text/javascript" src="%url%/js/profiles.js"></script>
	<form action="#" method="post" onsubmit="return postToProfile();">
	<div class="feed_entry">
		<div class="feed_img"><a href="%user_link%"><img src="%my_thumbnail%" border="0" alt="%poster_username%" title="%poster_username%" /></a></div>
		<div class="feed_post">
			<textarea name="post" style="width:100%;height:70px;" id="areapost"></textarea>
			<div id="areapostsubmit" style="display:none;">
				<input type="hidden" name="postingId" id="postingId" value="%id%" />
				<input type="submit" value="Post" style="margin:0px;" />
			</div>
		</div>
		<div class="bd_clear"></div>
	</div>
	</form>
	<!--end:post-->
	
	<div id="fullfeed">
	%feed%
	</div>

</div>
   