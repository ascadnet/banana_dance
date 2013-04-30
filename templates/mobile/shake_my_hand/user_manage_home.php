
		<div id="user_headers">
			%user_panel%
			
			<div class="user_headers_in">
				<span><a href="%program_url%/user/%username%/articles">Articles</a></span>
				<span class="divide">&#183;</span>
				<span><a href="%program_url%/user/%username%/comments">Comments</a></span>
				<span class="divide">&#183;</span>
				<span><a href="%program_url%/user/%username%/favorites">Favorites</a></span>
				
				<br />
				
				<span><a href="%program_url%/user/%username%/edit">Edit Account</a></span>
				<span class="divide">&#183;</span>
				<span><a href="%program_url%/user/%username%/profile_pic">Profile Picture</a></span>
				<span class="divide">&#183;</span>
				<span><a href="%program_url%/user/%username%/notices">Notices</a></span>
			</div>
				
			<div class="user_headers_in">
				<span><a href="%program_url%/user/%username%/public">View Public Profile</a></span>
			</div>
		</div>

		
		<div class="pad_bot">
			<h1 class="bd_h1"><a href="%url%/user/%username%/articles">Recent <?php echo lg_article; ?>s (%articles%)</a></h1>
			<ul class="bd_widget_ul">
				%recent_articles%
			</ul>
		</div>
		
		<div class="pad_bot">	
			<h1 class="bd_h1"><a href="%url%/user/%username%/comments">Recent <?php echo lg_comment; ?>s (%comments%)</a></h1>
			<ul class="bd_widget_ul">
				%recent_comments%
			</ul>
		</div>
		