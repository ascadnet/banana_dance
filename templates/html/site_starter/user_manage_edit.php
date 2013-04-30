
	<script src="%program_url%/js/users.js" type="text/javascript"></script>

   	<div id="main_right_nomarg">
		
		%user_panel%
		
   		<span class="right_title">Links</span>
   		%user_menu%
   		
   		<span class="right_title">Badges (%total_badges%)</span>
   		%badges%
   		
   	</div>
   	
	<div id="main_center">
	
		<h3>Update Account</h3>
		
		<form name="edit_form" id="edit_form" onsubmit="editAccount();return false;">
		
			<div id="current_password"><div class="pad">
				<label class="left">Current Password</label>
				<input type="password" id="current_password" name="current_password" style="width:180px;" /></p>
			</div></div>
				
			<div class="col50">
			
				<h3>Update Your Password</h3>
				<label>Password</label>
				<input type="password" id="pass" name="pass" style="width:90%;" />
				
				<label>Repeat</label>
				<input type="password" id="pass1" name="pass1" style="width:90%;" />
				
				<input type="submit" value="Update Password" />
						
			</div>
			<div class="col50">
			
				<h3>Account Details</h3>
   				<label>Name</label>
   				<input type="text" id="name" name="name" value="%name%" style="width:90%;" />
   				
   				<label>E-Mail</label>
   				<input type="text" id="email" name="email" value="%email%" style="width:90%;" />

   				<input type="submit" value="Update Account" />
   				
   				
				<h3>Account Settings</h3>
   				<label>Profile Visibility</label>
   				%field_hide_profile%
   				
   				<label>Default Editor?</label>
   				%field_default_editor%
   				
   				<label>E-Mail Format?</label>
   				%field_email_format%

   				<br /><input type="submit" value="Update Settings" />
   				
			</div>
			<div class="clear"></div>
					
		</form>
   	</div>
   	