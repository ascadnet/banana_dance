<form action="../functions/ajax.php" method="post">
Username<br />
<input type="text" name="username" value="" />
<br /><br />
Password<br />
<input type="password" name="password" value="" />
<br /><br />
<input type="checkbox" name="remember_me" value="1" /> Remember me
<br /><br />
<input type="hidden" name="action" value="login" />
<input type="hidden" name="onsite" value="1" />
<input type="submit" value="Log In" />
</form>