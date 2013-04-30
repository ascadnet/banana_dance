<form onsubmit="processLogin();return false;" id="login_form">
	<span><input type="text" id="login_1" name="username" value="username" onfocus="this.value='';" style="width:80px;" /></span>
	<span> <input type="password" id="login_2" name="password" value="password" onfocus="this.value='';" style="width:80px;" /></span>
	<span><input type="submit" value="Login" /></span>
	<span style="margin-left:20px;"><a href="#" onclick="showRegister();return false;">Register</a>&nbsp;&nbsp;<a href="#" onclick="showLostPass();">Lost Pass</a></span>
</form>