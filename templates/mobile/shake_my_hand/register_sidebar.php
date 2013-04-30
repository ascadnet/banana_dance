<form name="register_form" id="register_form" onsubmit="processRegistration();return false;">
<p class="bd_small" style="float:right;"><a href="#" onclick="closeCaptcha();return false;">Close [x]</a></p>
<h2 class="bd_h2">Register</h2>

<div style="width:49%;float:left;">
	<p><b>Desired Username</b><br /><input type="text" id="reg_username" name="username" style="width:180px;" /><br />
	<p><b>Password</b><br /><input type="password" id="reg_pass" name="pass" style="width:180px;" /></p>
	<p><b>Repeat Password</b><br /><input type="password" name="pass1" id="pass1" style="width:180px;" /></p>
</div>
<div style="width:49%;float:left;">
	<p><b>Name</b><br /><input type="text" id="reg_name" name="name" style="width:180px;" /></p>
	<p><b>E-Mail</b><br /><input type="text" id="reg_email" name="email" style="width:180px;" /></p>
</div>
<div class="bd_clear"></div>

<p class="bd_center"><input type="button" value="Register" onclick="processRegistration();return false;" /></p>
<input type="hidden" name="action" value="register" />
</form>