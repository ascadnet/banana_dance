<form name="register_form" id="register_form" action="#" onsubmit="return processRegistration();">

<div id="close_popup" onclick="closeCaptcha();return false;"></div>
<h3 class="no_margin">Register</h3>

<div class="col50">

	<label class="left">Username</label>
	<input type="text" id="reg_username" name="username" tabindex="1" style="width:92%;" />
	
	<label class="left">Name</label>
	<input type="text" id="reg_name" name="name" tabindex="2" style="width:92%;" />
	
	<label class="left">E-Mail</label>
	<input type="text" id="reg_email" name="email" tabindex="3" style="width:92%;" />

</div>
<div class="col50">

	<label class="left">Password</label>
	<input type="password" id="reg_pass" name="pass" tabindex="4" style="width:92%;" />
	
	<label class="left">Repeat Password</label>
	<input type="password" id="pass1" name="pass1" tabindex="5" style="width:92%;" />

</div>
<div class="clear"></div>

<div class="space"></div>

<div style="float:right;text-align:right;">
<input type="submit" value="Register" class="no_margin" tabindex="6" /><input type="hidden" name="action" value="register" />
</div>
<div style="float:left;">
<p class="small"><a href="#" onclick="return showLogin();">Already have an account?</a></a></p>
</div>
<div class="clear"></div>

</form>