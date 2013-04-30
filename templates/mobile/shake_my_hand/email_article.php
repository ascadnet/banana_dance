
<form name="email_form" id="email_form" onsubmit="processEmailFriend();return false;">
<p><b>Your Name</b><br /><input type="text" id="name" name="name" value="%name%" style="width:180px;" /></p>
<p><b>Your E-Mail</b><br /><input type="text" id="email" name="email" value="%email%" style="width:180px;" /></p>
<p><b>Friends' Email(s)</b><br />
<input type="text" name="friend_email[]" style="width:180px;" maxlength="40" /><br />
<input type="text" name="friend_email[]" style="width:180px;" maxlength="40" /><br />
<input type="text" name="friend_email[]" style="width:180px;" maxlength="40" /></p>
<p><b>Message to Friends</b><br />
<textarea id="message" name="message" style="width:180px;height:100px;"></textarea></p>

<p class="bd_center"><input type="button" value="Send E-Mail" onclick="processEmailFriend();return false;" /><br /><a href="#" onclick="closeCaptcha();return false;">Cancel</a></p>
<input type="hidden" name="action" value="email_friend" />
</form>