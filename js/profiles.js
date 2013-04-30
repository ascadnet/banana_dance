

// ------------------------------------------
//	Displays the "Post to Profile"
//	text box.

$(document).ready(function() {
	$('#areapost').focus(function() {
		$('#areapostsubmit').slideDown();
	});
});


// ------------------------------------------
//	Post to Profile

function postToProfile() {
	js_path_put = functions_path + "/profile_post.php";
	send_data = "action=post&post=" + $('#areapost').val() + "&id=" + $('#postingId').val();
    	$.post(js_path_put, send_data, function(theResponse) {
		var returned = theResponse.split('+++');
		if (returned['0'] == "1") {
			$('#areapost').val('');
			$('#areapostsubmit').slideUp();
			$('#fullfeed').prepend($(returned['1']).hide().fadeIn(300));
			closeError();
			closeCaptcha();
		} else {
			process_error(theResponse);
		}
    	});
    	return false;
}

// ------------------------------------------
//	Post to Profile

function delPosting(id) {
	js_path_put = functions_path + "/profile_post.php";
	send_data = "action=del&id=" + id;
    	$.post(js_path_put, send_data, function(theResponse) {
		var returned = theResponse.split('+++');
		if (returned['0'] == "1") {
			$('#ppost' + id).fadeOut('200');
			closeError();
			closeCaptcha();
		} else {
			process_error(theResponse);
		}
    	});
    	return false;
}