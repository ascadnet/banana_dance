
		<div id="user_headers">
			%user_panel%
			<div class="user_headers_in">
				<span><a href="%program_url%/user/%username%/articles">Articles</a></span>
				<span class="divide">&#183;</span>
				<span><a href="%program_url%/user/%username%/comments">Comments</a></span>
				<span class="divide">&#183;</span>
				<span><a href="%program_url%/user/%username%/favorites">Favorites</a></span>
			</div>
		</div>
				
			
 		<h1 class="bd_h1">Feed</h1>
 		
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
		
		%feed%
 		
 		
   		<h1 class="bd_h1">Badges (%total_badges%)</h1>
   		%badges%