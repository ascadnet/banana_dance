
/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: User update functions.
	
	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 2 of the License, or
	(at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.

====================================================== */


// ---------------------------------------------------------------------------------------
//	Ajax communication

function editAccount(id,value) {
	js_path_put = functions_path + "/users.php";
	send_data = "action=edit_account&";
	send_data += $('form#edit_form').serialize();
    	$.post(js_path_put, send_data, function(theResponse) {
		var returned = theResponse.split('+++');
		if (returned['0'] == "1") {
			showSaved(returned['1']);
		} else {
			process_error(returned['1']);
		}
    	});
}

function removeProfilePic() {
	js_path_put = functions_path  + "/users.php";
	send_data = "action=remove_pic";
    	$.post(js_path_put, send_data, function(theResponse) {
		var returned = theResponse.split('+++');
		if (returned['0'] == "1") {
			$('#bd_pic_remove_link').fadeOut('400');
			$('.bd_profile_pic').attr('src',returned['1']);
			showSaved();
			closeError();
		} else {
			process_error(returned['1']);
		}
    	});
}