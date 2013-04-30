<form name="register_form" id="register_form" onsubmit="return processRegistration();">

	<div id="close_popup" onclick="closeCaptcha();return false;"></div>
	<h3 class="no_margin">Register</h3>

	<div class="col50">
		<label class="left">Username</label>
		<input type="text" id="reg_username" name="username" style="width:92%;" />
		
		<label class="left">Name</label>
		<input type="text" id="reg_name" name="name" style="width:92%;" />
		
		<label class="left">E-Mail</label>
		<input type="text" id="reg_email" name="email" style="width:92%;" />
		
	</div>
	<div class="col50">
	
		<label class="left">Password</label>
		<input type="password" id="reg_pass" name="pass" style="width:92%;" />
		
		<label class="left">Repeat Password</label>
		<input type="password" id="pass1" name="pass1" style="width:92%;" />
		
	</div>
	<div class="clear"></div>
	
	<div class="space"></div>


	<div style="float:right;text-align:right;">
		<input type="submit" value="Register" class="no_margin" /><input type="hidden" name="action" value="register" />
	</div>
	<div style="float:left;">
		<p class="small"><a href="#" onclick="showLogin();return false;">Already have an account?</a></a></p>
	</div>
	<div class="clear"></div>
	
</form>