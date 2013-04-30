
/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Suggests various options to users using
	auto-complete.
	
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


var ed_format_type;
var text_input_id;
var additional;

function suggest(table,value,field_to_search,div_position,return_field,display_field) {
	len = value.length;
	if (len > 0) {
		text_input_id = div_position;
		$('#' + text_input_id).css('background-color','');
		js_path_put = functions_path + "/suggest.php";
		send_data = "table=" + table + '&value=' + value + '&search=' + field_to_search + '&return=' + return_field + '&display=' + display_field + "&function=" + current_function;
		// For linking
		if ($('#addLinkName').val()) {
			send_data += "&custom_link_name=" + $('#addLinkName').val();
		}
		send_data += "&ed_type=" + ed_format_type;
	    	$.post(js_path_put, send_data, function(theResponse) {
			var returned = theResponse.split('+++');
	      		if (returned['0'] == '1') {
				$("<ul/>", {"id": "suggest_box"}).appendTo("body");
				var offset = $('#' + div_position).offset();
				var width = $('#' + div_position).outerWidth();
				var height = $('#' + div_position).outerHeight();
				var top = offset.top + height;
				var left = offset.left;
				$('#suggest_box').css('top',top);
				$('#suggest_box').css('left',left);
				$('#suggest_box').css('width',width);
		    		$('#suggest_box').html(returned['1']);
		    		$('#suggest_box').show();
	    		} else {
	    			admin_error(returned['1']);
	    		}
	    	});
	}
}

function selectSuggest(id,name) {
	// If something needs to be updated
	// in the database, do it, otherwise
	// just populate a field in the next
	// step (complete_suggest).
	if (additional) {
	  	js_path_put = functions_path + "/suggest.php";
	  	send_data = "action=complete&id=" + id + "&function=" + current_function + "&additional=" + additional;
	      	$.post(js_path_put, send_data, function(theResponse) {
			var returned = theResponse.split('+++');
	      		if (returned['0'] == '1') {
	      			complete_suggest(returned['1']);
	      		} else {
		    		admin_error(returned['1']);
	      		}
	      	});
	} else {
		complete_suggest(id,name);
	}
}


function complete_suggest(returned,name) {
	if (current_function == 'widget') {
		$('#putPageID').val(returned);
		$('#' + text_input_id).css('background-color','#FFF4AA');
	}
	else if (current_function == 'article_permissions' || current_function == 'category_permissions') {
		$('#user_permission_list').append(returned);
		$('#' + text_input_id).val('');
	}
	else if (current_function == 'comment_edit') {
		$('#article_id').val(returned);
		$('#' + text_input_id).css('background-color','#FFF4AA');
	}
	else {
		populate = text_input_id + "_val";
		$('#' + populate).val(returned);
		$('#' + text_input_id).css('background-color','#FFF4AA');
	}
	if (name) {
		$('#' + text_input_id).val(name);
	}
	$('#suggest_box').hide();
	$('#suggest_box').html();
}