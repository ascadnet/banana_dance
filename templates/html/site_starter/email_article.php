
<form name="email_form" id="email_form" onsubmit="processEmailFriend();return false;">

	<div id="close_popup" onclick="closeCaptcha();return false;"></div>
	<h3 class="no_margin">E-Mail This Page</h3>
	
	<div class="col50">
		<label class="left">Your Name</label>
		<input type="text" id="name" name="name" value="" style="width:92%;" />
		
		<label class="left">Your E-Mail</label>
		<input type="text" id="email" name="email" value="" style="width:92%;" />
		
		<label class="left">Friend E-Mail No. 1</label>
		<input type="text" id="fe1" name="friend_email[]" value="" style="width:92%;" />
		
		<label class="left">Friend E-Mail No. 2</label>
		<input type="text" id="fe2" name="friend_email[]" value="" style="width:92%;" />
	</div>
	<div class="col50">
		<label class="left">Message to Friend(s)</label>
		<textarea id="message" name="message" style="width:92%;height:200px;"></textarea></p>
	</div>
	<div class="clear"></div>
	
	<div class="space"></div>
	
	<div style="float:right;text-align:right;">
		<input type="hidden" name="action" value="email_friend" />
		<input type="submit" value="Send E-Mail" class="no_margin" />
	</div>
	<div class="clear"></div>