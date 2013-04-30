
/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Media Library Functions
	
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

var id;
var selected_image;
var img_width;
var img_height;
var img_caption;
var img_title;
var img_location;
var new_x;
var new_y;
var hold_x;
var hold_y;
var using_editor;
var list_view_type = 'list';
var jsonObt = '';
var screen_open = '0';
var js_media = functions_path + "/mediaLibrary.php";


// -------------------------------------------------------------------------------------
//	Switch Galleries

function switchView(type) {
	closeMedia();
	if (type == 'files') {
		getMediaScreen('file','');
	} else {
		getMediaScreen('image','');
	}
}

function closeFloats() {
	$('#add_form_x01')[0].reset();
	$('#add_gallery_x01')[0].reset();
	$('#uploader_form')[0].reset();
	$('#showDivUpload').hide();
	$('#new_file').hide();
	$('#img_create_gal').hide();
	$('#add_file').hide();
	clearFields();
}


// -------------------------------------------------------------------------------------
//	Images

function addMedia() {
	if ($('#bd_article_inline_edit').is(":visible")) {
		if (new_y && new_x) {
			final_width = new_x;
			final_height = new_y;
		} else {
			final_width = img_width;
			final_height = img_height;
		}
		// program_url + 
		img_location = img_location;
		if (ed_format_type == 'wiki') {
			put = "((" + img_location + "|" + final_width + "|" + final_height + "))";
		}
		else {
			put = "<div style=\"width:" + final_width + "px;\">";
			put += "<a href=\"#\" onclick=\"viewFullImage('" + img_location + "','" + addslashes(img_caption) + "','" + addslashes(img_title) + "','" + img_width + "','" + img_height + "');return false;\"><img src=\"" + img_location + "\" width=\"" + final_width + "\" height=\"" + final_height + "\" title=\"" + addslashes(img_title) + "\" alt=\"" + addslashes(img_title) + "\" border=\"0\" class=\"bd_image\" /></a>";
			if (img_caption) {
				put += "<p class=\"bd_image_caption\">" + img_caption + "</p>";
			}
			put += "</div>";
		}
		if (using_editor == 'wys') {
			var editor = $('#content').cleditor()[0];
			editor.execCommand('inserthtml', put);
			editor.updateTextArea();
			closeFloats();
			return false;
		} else {
			addCaller('content',put,'0');
			closeFloats();
		}
		// closeMedia();
	} else {
		process_error('Edit or create a page to add media.');
	}
}


function getMediaScreen(type,format) {
	if (screen_open == '1') {
		closeMedia();
	} else {
		showLoading();
		send_data = "type=" + type;
		$.post(js_media, send_data, function(inner) {
			// alert(js_media + '--' + send_data + '--' + inner);
			cutIt = inner.split('+++');
			if (cutIt['0'] == '1') {
				screen_open = '1';
				putit = cutIt['1'];
				$('body').append($(putit).hide().fadeIn('200'));
				return false;
			} else {
				process_error(cutIt['1']);
			}
		});
		closeLoading();
	}
}


function uploadFile(type,formatting) {
	getMediaScreen(type,formatting);
}

function openUpload() {
	if ($('#new_file').is(":visible")) {
		showDivUpload('close', function() {
			closeFloats();
		});
	} else {
		$('#new_file').show();
		$('#img_create_gal').hide();
		$('#add_file').hide();
		
		$('#editDets').hide();
      		$('#new_files').show();
      		$('#media_upload_entry').hide();
		$('#editUps').css('width','100%');
		
		showDivUpload('open');
	}
}

function showDivUpload(force) {
	if (force == 'open') {
		$('#showDivUpload').slideDown();
	}
	else if (force == 'close') {
		$('#showDivUpload').slideUp(
			function () {
			clearFields();
			}
		);
	}
	else {
		if ($('#showDivUpload').is(":visible")) {
			$('#showDivUpload').slideUp();
			clearFields();
		} else {
			$('#showDivUpload').slideDown();
		}
	}
}


function clearFields() {
	$('#img_title').val('');
	$('#img_caption').val('');
	$('#img_tags').val('');
	$('#img_caption').val('');
	$('#img_edit_id').val('');
	$('#up_file').val('');
	$('#img_public').removeAttr("checked");
	$('#img_dimensions').val('N/A');
	$('#right_header').html('Upload Media');
	$('input.img_submit').attr('value','Add Media');
	img_width = '';
	img_height = '';
	img_caption = '';
	img_title = '';
	img_location = '';
}


function editImage(id,type) {
	var getDiv;
	$('.image_entry').removeClass('onmedia');
	if (selected_image && selected_image == id) {
		getDiv = "img" + selected_image;
		selected_image = '';
	      	showDivUpload('close');
	} else {
		showLoading();
		send_data = "id=" + id;
		$.post(js_media, send_data, function(inner) {
			var jsonObt = eval(inner);
			$('#new_file').show();
			$('#img_create_gal').hide();
	      		$('#img_filename').html(jsonObt['filename']);
	      		$('#img_location').html(jsonObt['location']);
	      		$('#img_title').val(jsonObt['title']);
	      		$('#img_caption').val(jsonObt['caption']);
	      		$('#img_tags').val(jsonObt['tags']);
	      		$('#img_edit_id').val(jsonObt['id']);
	      		$('#right_header').html('Editing Media');
	      		if (type == 'add') {
	      			$('input.img_submit').attr('value','Add Media to Page');
	      		} else {
	      			$('input.img_submit').attr('value','Edit Media');
	      		}
	      		if (jsonObt['public'] == '1') {
	      			$('#img_public').attr('checked','checked');
	      		} else {
	      			$('#img_public').removeAttr("checked");
	      		}
	      		dims = jsonObt['width'] + 'x' + jsonObt['height'];
			$('#img_dimensions').html(dims);
			$('#imgtd' + id).addClass('onmedia');
			img_width = jsonObt['width'];
			img_height = jsonObt['height'];
			img_caption = jsonObt['caption'];
			img_title = jsonObt['title'];
			img_location = jsonObt['location'];
					//alert(img_caption);
		
			$('#editDets').show();
			$('#new_files').hide();
			$('#media_upload_entry').show();
			$('#editUps').css('width','160px');
		
			selected_image = id;
			if (type == 'add') {
				hold_x = parseInt(jsonObt['width']);
				hold_y = parseInt(jsonObt['height']);
				new_x = hold_x;
				new_y = hold_y;
				$('#img_width').val(hold_x);
				$('#img_height').val(hold_y);
				showAddDetails();
			} else {
	      			showDivUpload('open');
	      		}
		});
		closeLoading();
	}
}


function showAddDetails() {
	if ($('#bd_article_inline_edit').is(":visible")) {
		$(document).ready(function() {
			$('#img_height').keyup(function() {
				if ($('#scale_proportions').is(':checked')) {
					parseY = parseInt($('#img_height').val());
					change = parseY / hold_y;
					new_x = Math.round(hold_x * change);
					new_y = parseY;
					$('#img_width').val(new_x);
					$('#img_height').val(new_y);
				}
			});
			$('#img_width').keyup(function() {
				if ($('#scale_proportions').is(':checked')) {
					parseX = parseInt($('#img_width').val());
					change = parseX / hold_x;
					new_x = parseX;
					new_y = Math.round(hold_y * change);
					$('#img_width').val(new_x);
					$('#img_height').val(new_y);
				}
			});
		});
		$('#add_file').show();
		$('#new_file').hide();
		$('#img_create_gal').hide();
		showDivUpload('open');
	} else {
		process_error('Edit or create a page to add media.');
	}
}


function refreshMedia(list_type) {
	showLoading();
	if (list_type == 'file') {
		send_data = "refresh_file_list=1";
	} else {
		send_data = "refresh_list=1";
	}
	if ($('#filter_tag').val()) {
		send_data += "&tag=" + $('#filter_tag').val();
	}
	if (list_type == 'list') {
		send_data += "&view=list";
		list_view_type = 'list';
	}
	else if (list_type == 'gallery') {
		send_data += "&view=gallery";
		list_view_type = 'gallery';
	}
	else {
		send_data += "&view=" + list_view_type;
	}
	$.post(js_media, send_data, function(inner) {
		// alert(inner);
		cutIt = inner.split('+++');
		if (cutIt['0'] == '1') {
			$('#put_media').html(cutIt['1']);
			showDivUpload('close');
		} else {
			process_error(cutIt);
		}
	});
	closeLoading();
}

function deleteMedia(id) {
	if (confirm('Confirm deletion')) {
		showLoading();
		send_data = "action=delete&delid=" + id;
		$.post(js_media, send_data, function(inner) {
			// alert(inner);
			cutIt = inner.split('+++');
			if (cutIt['0'] == '1') {
				$('#imgtd' + id).fadeTo('fast','0.25');
			} else {
				process_error(cutIt);
			}
		});
		closeLoading();
	}
}


function addslashes(str) {
	str = str.replace(/\'/g,'\\\'');
	return str;
}

function addGallery() {
	if ($('#img_create_gal').is(":visible")) {
		showDivUpload('close', function() {
			$('#new_file').hide();
			$('#img_create_gal').hide();
			$('#add_file').hide();
		});
	} else {
		$('#new_file').hide();
		$('#img_create_gal').show();
		$('#add_file').hide();
		showDivUpload('open');
	}
}

function finalizeGallery() {
	if ($('#img_existing_gallery').val()) {
		if ($('#bd_article_inline_edit').is(":visible")) {
		      	put = '{-' + $('#img_existing_gallery').val() + '-}';
			if (using_editor == 'wys') {
				var editor = $('#content').cleditor()[0];
				editor.execCommand('inserthtml', put); 
				editor.updateTextArea();
				return false;
			} else {
			      	addCaller('content',put,'0');
			}
			showDivUpload('close');
		} else {
			process_error('Edit or create a page to add gallery to a page.');
		}
	} else {
		showLoading();
		serialize = $('#add_gallery_x01').serialize();
		send_data = "action=create_gallery&" + serialize;
		// alert(send_data);
	      	$.post(js_media, send_data, function(inner) {
	      		cutIt = inner.split('+++');
	      		if (cutIt['0'] == '1') {
				if ($('#bd_article_inline_edit').is(":visible")) {
					put = '{-' + cutIt['1'] + '-}';
					if (using_editor == 'wys') {
						var editor = $('#content').cleditor()[0];
						editor.execCommand('inserthtml', put); 
						editor.updateTextArea();
						return false;
					} else {
						addCaller('content',put,'0');
					}
				} else {
					process_error('Gallery created. Edit or create a page to include it on your website.');
				}
				showDivUpload('close');
	      		} else {
	      			process_error(cutIt);
	      		}
	      	});
		closeLoading();
      	}
}


// -------------------------------------------------------------------------------------
//	Files

function addFileToPage(path) {
	if ($('#bd_article_inline_edit').is(":visible")) {
		put = '{|' + path + '|}';
	      	if (using_editor == 'wys') {
			var editor = $('#content').cleditor()[0];
	      		editor.execCommand('inserthtml', put);
			editor.updateTextArea();
	      		return false;
	      	} else {
	      	      	addCaller('content',put,'0');
	      	}
      	} else {
		process_error('Edit or create a page to add files.');
      	}
	// closeMedia();
}

function refreshFiles() {
	showLoading();
	send_data = "refresh_file_list=1";
	$.post(js_media, send_data, function(inner) {
		// alert(inner);
		cutIt = inner.split('+++');
		if (cutIt['0'] == '1') {
			$('#put_media').html(cutIt['1']);
			showDivUpload('close');
			closeLoading();
		} else {
			process_error(cutIt);
		}
	});
}


function editFile(id) {
	$('.file_entry').removeClass('onmedia');
	if (selected_image && selected_image == id) {
		getDiv = "img" + selected_image;
		selected_image = '';
	      	showDivUpload('close');
	} else {
		showLoading();
		send_data = "file_id=" + id;
		$.post(js_media, send_data, function(inner) {
		
			var jsonObt = eval(inner);
		      	$('#img_limit_dls').val(jsonObt['limit']);
		      	if (jsonObt['login'] == '1') {
		      		$('#img_login_req').attr('checked','checked');
		      	} else {
		      		$('#img_login_req').removeAttr("checked");
		      	}
		      	$('#img_edit_id').val(jsonObt['id']);
			$('#imgtd' + id).addClass('onmedia');
			
			$('#img_filename').html(jsonObt['name']);
	      		$('#right_header').html('Editing File');
	      		$('input.img_submit').attr('value','Edit File');
	      		$('#img_limit_dls').val(jsonObt['limit']);
	      		$('#img_dimensions').html(jsonObt['size']);
	      		
			selected_image = id;
		      	showDivUpload('open');
			closeLoading();
		});
	}
}

function deleteFile(id) {
	if (confirm('Confirm deletion')) {
		showLoading();
		send_data = "action=delete_file&delid=" + id;
		$.post(js_media, send_data, function(inner) {
			// alert(inner);
			cutIt = inner.split('+++');
			if (cutIt['0'] == '1') {
				$('#imgtd' + id).fadeTo('fast','0.25');
				closeLoading();
			} else {
				process_error(cutIt);
			}
		});
	}
}