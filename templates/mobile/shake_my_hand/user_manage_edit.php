
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
		
		
   		<form name="edit_form" id="edit_form" onsubmit="editAccount();return false;">

	   	<h1>Edit Profile</h1>
	   		
		<div class="pad_bot">
	   		<h2>Current Password</h2>
	   		<p><b>Current Password</b><br /><input type="password" id="current_password" name="current_password" style="width:99%;" /></p>
		</div>
   		
		<div class="pad_bot">
	   		<h2>Update Password</h2>
		   	<p><b>Password</b><br /><input type="password" id="pass" name="pass" style="width:99%;" /></p>
		   	<p><b>Repeat</b><br /><input type="password" id="pass1" name="pass1" style="width:99%;" /></p>
		   	<input type="submit" value="Update Password" />
		</div>

		<div class="pad_bot">
	   		<h2>Details</h2>
	   		<p><b>Name</b><br /><input type="text" id="name" name="name" value="%name%" style="width:99%;" /></p>
	   		<p><b>E-Mail</b><br /><input type="text" id="email" name="email" value="%email%" style="width:99%;" /></p>
	   		<input type="submit" value="Update Account" />
		</div>
		
   		</form>