<form action="#" onsubmit="return processLostPass();" id="lostpass_form">

<div id="close_popup" onclick="closeCaptcha();return false;"></div>
<h3 class="no_margin">Lost Password Recovery</h3>

<label class="left">E-Mail</label>
<input type="text" id="lost_email" name="lost_email" style="width:92%;" />

<div class="space"></div>

<div style="float:right;text-align:right;">
<input type="submit" value="Recover Password" class="no_margin" />
</div>
<div style="float:left;">
<p class="small"><a href="#" onclick="return showLogin();">Login</a> | <a href="#" onclick="return showRegister();">Register</a></a></p>
</div>
<div class="clear"></div>

</form>