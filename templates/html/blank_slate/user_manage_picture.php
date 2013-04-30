
	<script src="%program_url%/js/preSubmitProcess.js" type="text/javascript"></script>
	<script src="%program_url%/js/users.js" type="text/javascript"></script>
		
   	<div id="main_right_nomarg">
		
		%user_panel%
		
   		<span class="right_title">Links</span>
   		%user_menu%
   		
   		<span class="right_title">Badges (%total_badges%)</span>
   		%badges%
   		
   	</div>
   	
	<div id="main_center">
			
		<form name="edit_form" id="edit_form" method="post" onsubmit="preConfirmForm('edit_form');return false;" action="%program_url%/functions/users_profile_pic.php" enctype="multipart/form-data">
		
			<div id="current_password"><div class="pad">
				<label class="left">Current Password</label>
				<input type="password" id="current_password" name="current_password" style="width:180px;" /></p>
			</div></div>
		

   			<div class="col50">
   			
   				<h3>Current Picture</h3>
   				<div id="current_pic">
   				<img src="%profile_pic%" border="0" alt="%username%" title="%username%" class="bd_profile_pic" />
   				<p id="bd_pic_remove_link" class="small">%remove_link%</p>
   				</div>
   				
   			</div>
   			
   			<div class="col50">
   				<h3>Replace Picture</h3>
   				<input type="file" name="file" id="file" class="bd_required" />
   				<input type="submit" value="Upload Picture" />
   			</div>
   			
   			</form>
   			<div class="bd_clear"></div>
		
		</form>	
			
   	</div>