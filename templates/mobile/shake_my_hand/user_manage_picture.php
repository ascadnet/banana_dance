
		<script src="%program_url%/js/preSubmitProcess.js" type="text/javascript"></script>
		<script src="%program_url%/js/users.js" type="text/javascript"></script>


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
		
   		
   		
   		<form name="edit_form" id="edit_form" method="post" onsubmit="preConfirmForm('edit_form');return false;" action="%program_url%/functions/users_profile_pic.php" enctype="multipart/form-data">
   	
   	
   		<h1>Profile Picture</h1>
		<div class="pad_bot">
	   		<h2>Current Password</h2>
	   		<p><b>Current Password</b><br /><input type="password" id="current_password" name="current_password" style="width:99%;" class="bd_required" /></p>
   		</div>
   		
		<div class="pad_bot">
	   		<h2>Replace Picture</h2>
	   		<input type="file" name="file" id="file" class="bd_required" style="width:99%;" />
	   		<input type="submit" value="Upload Picture" />
	   	</div>
	   	
   		</form>