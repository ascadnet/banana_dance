<form action="#" onsubmit="return processLogin();" id="login_form">

<div id="close_popup" onclick="closeCaptcha();return false;"></div>
<h3 class="no_margin">Login</h3>

<div class="col50">
	<label class="left">Username</label>
	<input type="text" id="login_1" name="username" tabindex="1" style="width:92%;" />
</div>
<div class="col50">
	<label class="left">Password</label>
	<input type="password" id="login_2" name="password" tabindex="2" style="width:92%;" />
</div>
<div class="clear"></div>

<div class="space"></div>

<div style="float:right;text-align:right;">
	<span class="small" style="margin-right:12px;">
	<a href="#" onclick="return showRegister();">Register</a> | <a href="#" onclick="return showLostPass();">Lost Password</a>
	</span>
	<input type="submit" value="Login" class="no_margin" tabindex="4" />
</div>
<div style="float:left;">
	<p class="small"><input type="checkbox" name="remember_me" tabindex="3" id="login_3" value="1" /> Remember me for a month</p>
</div>
<div class="clear"></div>

</form>