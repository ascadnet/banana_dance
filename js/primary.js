
/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: The primary JS file with functions relating
	to almost all JS-related features.
	
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


var inner;
var upload_type;
var current_subs;
var timer;
var classifying_comment;
var viewing_thread;
var ed_format_type;
var fb_open;
var editingArticle	= '';
var expandMenuType	= 'fade'; // fade, slide, instant
var showing_headers	= 0;
var functions_path	= program_url + "/functions";
var js_path		= program_url + "/js";
var current_showing	= current_category_id;
var use_bg_image	= "transparent_white.png"; // Found in templates/html/_imgs
var hold_ori_status 	= current_status;
var active_search 	= 0;
var exact_result	= '';

// ---------------------------------------------------------------------------------------
//	Document Ready Functions

$(document).ready(function() {

        $('#search_input').keyup(function(event) {
                len = $(this).val().length;
                if (len > 2) {
                	exact_result = '';
                	if (event.keyCode === 38 || event.keyCode === 40) {
                		
                	} else {
		      		js_path_put = functions_path + "/search_instant.php";
		      		send_data = "q=" + $(this).val();
		      	    	$.post(js_path_put, send_data, function(theResponse) {
		      			var returned = theResponse.split('+++');
		      	        	if (returned['0'] == '1') {
		      	        		active_search = 1;
		      	        		width = $('#search_input').outerWidth();
		      	        		height = $('#search_input').outerHeight();
		      	        		pos = $('#search_input').offset();
		      	        		theTop = height + pos.top;
		      	        		$('#ajax_search').remove();
		      	        		element = "<ul id=\"ajax_search\" style=\"width:" + width + "px;position:absolute;top:" + theTop + "px;left:" + pos.left + "px;\">";
		      	        		element += returned['1'];
		      	        		element += "</ul>";
		      	        		$('body').append(element);
		      	        		
					      	var $items = $('#ajax_search li');
					      	$items.first().addClass('selected');
					      	$(window).keyup(function(e) {
					      	    var $old = $items.filter('.selected'),
					      	        $new;
					      	    switch ( e.keyCode ) {
					      	    case 38:
					      	        $new = $old.prev();
					      	        break;
					      	    case 40:
					      	        $new = $old.next();
					      	        break; 
					      	    case 13:
					      	        // location.href = $old.find('a').attr('href');
					      	    }
					      	    if ( $new.is('li') ) {
					      	        exact_result = $new.find('a').attr('href');
					      	    	$('#search_input').val($new.attr('id'));
					      	        $old.removeClass('selected');
					      	        $new.addClass('selected');  
					      	    }
					      	});
		      	      		}
		      	    	});
	      	    	}
      	    	} else {
      	    		active_search = 0;
      	    		current_ajax_search = '';
			$('#ajax_search').remove();
      	    	}
        });
        
        
	$('#bd_manage_bar').css('z-index','99998');
	$('#bd_manage_notices').css('z-index','99999');
});

function submit_search() {
	if (exact_result) {
		window.location = exact_result;
		return false;
	} else {
		return true;
	}
}


// --------------------------------------------
//	Maintain selection

function get_selected_text(field) {
	if (! field) { field = 'content'; }
	var range = $('#' + field).getSelection();
	return range.text;
}


// ------------------------------------------
//	Upvote/downvote page

function ratePage(id,value) {
	js_path_put = functions_path + "/ajax.php";
	send_data = "action=vote_page&page=" + id + "&vote=" + value;
    	$.post(js_path_put, send_data, function(theResponse) {
		var returned = theResponse.split('+++');
		if (returned['0'] == "1") {
			if (value > 0) {
				var getVal = 'current_ups';
			} else {
				var getVal = 'current_downs';
			}
	      		var text_value = $('#' + getVal).html();
      			var cur_replies = parseInt(text_value);
      			cur_replies += 1;
      			$('#' + getVal).html(cur_replies);
			showSaved();
			closeError();
	      		return false;
		} else {
			process_error(theResponse);
		}
    	});
}


// ------------------------------------------
//	Upvote/downvote comment

function vote_comment(id,value) {
	js_path_put = functions_path + "/ajax.php";
	send_data = "action=vote_comment&comment=" + id + "&vote=" + value;
    	$.post(js_path_put, send_data, function(theResponse) {
		var returned = theResponse.split('+++');
		if (returned['0'] == "1") {
			if (value > 0) {
				$('#votedUp' + id).addClass('bd_voted');
				$('#votedDown' + id).removeClass('bd_voted');
			} else {
				$('#votedDown' + id).addClass('bd_voted');
				$('#votedUp' + id).removeClass('bd_voted');
			}
			$('#voteTotal' + id).html(returned['2']);
			showSaved();
			closeError();
		} else {
			process_error(theResponse);
		}
    	});
}

// ------------------------------------------
//	Ban a user

function userBan(username,comment_id) {
	js_path_put = functions_path + "/ajax.php";
	send_data = "action=ban_user&username=" + username + "&comment_id=" + comment_id;
    	$.post(js_path_put, send_data, function(theResponse) {
		var returned = theResponse.split('+++');
		if (returned['0'] == "1") {
			alert('User has been banned.');
			closeError();
		} else {
			process_error(theResponse);
		}
    	});
}

// ------------------------------------------
//	Edit a comment

function editComment(comment_id) {
	js_path_put = functions_path + "/ajax.php";
	send_data = "action=edit_comment&comment=" + $('#edit' + comment_id).val() + "&comment_id=" + comment_id;
    	$.post(js_path_put, send_data, function(theResponse) {
		var returned = theResponse.split('+++');
		if (returned['0'] == "1") {
		   	$('#showEdit' + comment_id).fadeOut('300', function () {
		   		$('#commentMain' + comment_id).html(returned['1']);
	   			$('#showReplyTop' + comment_id).fadeIn('300');
	   			$('#commentMain' + comment_id).fadeIn('300');
	   		});
			closeError();
		} else {
			process_error(theResponse);
		}
    	});
}

// ------------------------------------------
//	Delete a comment

function commentDelete(comment_id) {
	if (confirm('Confirm deletion...')) {
		js_path_put = functions_path + "/ajax.php";
		send_data = "action=del_comment&comment_id=" + comment_id;
	    	$.post(js_path_put, send_data, function(theResponse) {
			var returned = theResponse.split('+++');
			if (returned['0'] == "1") {
				if (returned['1'] == "2") {
					$('#comment' + comment_id).fadeOut('300', function () {
						$('#comment' + comment_id).remove();
		   			});
				} else {
					$('#commentMain' + comment_id).html('<span class="deleted_comment">Deleted</span>');
				}
				closeError();
			} else {
				process_error(theResponse);
			}
	    	});
    	}
}

// ------------------------------------------
//	Delete a page
//	type = page or category

function delete_cate_or_page(type,id) {
      	// Delete current category
      	if (type == 'category') {
      		id = current_category_id;
      	}
	if (confirm('Confirm deletion of ID ' + id + ' of type ' + type)) {
		if (type == 'category') {
			id = current_category_id;
		}
		js_path_put = functions_path + "/ajax.php";
		send_data = "action=del_" + type + "&id=" + id;
	    	$.post(js_path_put, send_data, function(theResponse) {
			var returned = theResponse.split('+++');
			if (returned['0'] == "1") {
				window.location = program_url;
			} else {
				process_error(theResponse);
			}
	    	});
    	}
}

// ------------------------------------------
//	Make a page the category homepage.

function makeCategoryHomepage(id) {
	if (confirm('Confirm that this page should be the category\'s homepage?')) {
		js_path_put = functions_path + "/ajax.php";
		send_data = "action=make_homepage&id=" + id;
	    	$.post(js_path_put, send_data, function(theResponse) {
			var returned = theResponse.split('+++');
			if (returned['0'] == "1") {
				closeError();
				showSaved(returned['1']);
			} else {
				process_error(theResponse);
			}
	    	});
    	}
}


// ------------------------------------------
//	Approve a comment
//	Only available for users with
//	the correct privileges.

function approveComment(comment_id) {
	js_path_put = functions_path + "/ajax.php";
	send_data = "action=approve_comment&comment_id=" + comment_id;
    	$.post(js_path_put, send_data, function(theResponse) {
		var returned = theResponse.split('+++');
		if (returned['0'] == "1") {
			$('#pending' + comment_id).fadeOut('300');
			closeError();
		} else {
			process_error(theResponse);
		}
    	});
}

// ------------------------------------------
//	Posts a comment
//	comment_id is for replies

function postComment(article,comment_id) {
	// For "Forum" style threaded comments
	if (viewing_thread !== undefined) {
		comment_id = viewing_thread;
		//replace_box = "showReply" + comment_id;
		text = $('#commentText').val();
	} else {
		if (comment_id) {
			text = $('#reply' + comment_id).val();
			//replace_box = "showReply" + comment_id;
		} else {
			text = $('#commentText').val();
			//replace_box = "bd_comment_box";
		}
	}
	js_path_put = functions_path + "/ajax.php";
	send_data = "action=post_comment&article=" + article + "&comment=" + text + "&comment_id=" + comment_id;
    	$.post(js_path_put, send_data, function(theResponse) {
		var returned = theResponse.split('+++');
		if (returned['0'] == "1") {
			$('#bd_no_comments').hide();
			$('#commentText').val('');
			closeError();
			// Tree style or primary comment?
			if (viewing_thread === undefined) {
				if (comment_id) {
					sub_com_put = "bd_com_overall" + comment_id;
					final_put = '<div id="subcommentsX" class="bd_discussion_bubble"><div id="bd_com_overallX" class="bd_com_overall">' + returned['1'] + '</div></div>';
					$('#' + sub_com_put).append(final_put).fadeIn('150');
					cancelReply(comment_id);
				} else {
					$('#bd_all_comments').append(returned['1']).hide().fadeIn('150');
				}
			} else {
				var text_value = $('#currentReplies' + viewing_thread).html();
				var cur_replies = parseInt(text_value);
				cur_replies += 1;
				$('#bd_all_comments').append(returned['1']).hide().fadeIn('150');
				$('#currentReplies' + viewing_thread).html(cur_replies);
			}
		} else {
			process_error(theResponse);
		}
    	});
    	return false;
}

// ------------------------------------------
//	Process login.

function processLogin() {
	showLoading();
	js_path_put = functions_path + "/ajax.php";
	if ($('#login_3').is(':checked')) {
		remember = 1;
	} else {
		remember = 0;
	}
	send_data = "action=login&username=" + $('#login_1').val() + "&password=" + $('#login_2').val() + "&remember_me=" + remember + "&c=" + $('#bd_c_field_login');
    	$.post(js_path_put, send_data, function(theResponse) {
		var returned = theResponse.split('+++');
		if (returned['0'] == "1") {
			process_login_show($('#login_1').val());
			closeError();
			closeCaptcha();
		} else {
			process_error(theResponse,returned['2']);
		}
    	});
	return false;
}

function start_fb_session() {
	js_path_put = functions_path + "/fb.php";
	send_data = 'fblogin=1';
    	$.post(js_path_put, send_data, function(theResponse) {
		process_login_show(theResponse);
    	});
}

function process_login_show(user_id) {	
	js_path_put = functions_path + "/ajax.php";
	send_data = "action=get_template&name=logged_in_sidebar&user=" + user_id;
	$.post(js_path_put, send_data, function(inner) {
		$('#bd_logged_session').fadeOut('300', function () {
			$('#bd_logged_session').html(inner);
			$('#bd_logged_session').fadeIn('300');
			// Re-work the article
			if (current_article_id != '0') {
				runArticleReProcess('0','1');
				getManageBar();
			} else {
				window.location.reload();
			}
		});
	});
	// window.location.reload();
}

// ------------------------------------------
//	Renders the management bar.

function getManageBar() {
	js_path_put = functions_path + "/ajax.php";
	send_data = 'action=getManageBar&article=' + current_article_id;
	$.post(js_path_put, send_data, function(theResponse) {
		$('body').append($(theResponse).hide().fadeIn('300'));
	});
}

// ------------------------------------------
//	Re-process article

function runArticleReProcess(logout,sidebar,skipComments,skipArticle) {
      if (current_article_id) {
		// Now redo the article!
		js_path_put = functions_path + "/process_article.php";
		send_dataA = "id=" + current_article_id + "&category=" + current_category_id;
		if (sidebar == '1') {
			send_dataA += "&sidebar=1";
		}
		if (skipComments == '1') {
			send_dataA += "&skipComments=1";
		}
		if (skipArticle == '1') {
			send_dataA += "&skipArticle=1";
		}
      		showLoading();
          	$.post(js_path_put, send_dataA, function(innerA) {
          		if (innerA) {
				var returned_elements = innerA.split('|||||');
				// Page
				if (skipArticle != '1') {
		      	    		$('#primary_article_holder').fadeOut('300', function () {
		      				$('#primary_article_holder').html(returned_elements['0']);
		      				$('#primary_article_holder').fadeIn('300');
		      	    		});
	      	    		}
	      	    		// Category Tree
	      	    		if (sidebar == '1' && returned_elements['2']) {
	      	    			// Some themes don't use the standard
	      	    			// category tree feature.
	      	    			if ($("#bd_floating_area").length > 0) {
			      	    		$('#bd_floating_area').fadeOut('300', function () {
			      				$('#bd_floating_area').html(returned_elements['2']);
			      				$('#bd_floating_area').fadeIn('300');
			      	    		});
		      	    		}
	      	    		}
          	
	      	    		// Comments
	      	    		if (skipComments != '1' && returned_elements['1']) {
		      	    		$('#primary_comment_holder').fadeOut('300', function () {
			      	    		if (logout != "1") {
		      	    				$('textarea#commentText').attr("disabled", false);
		      	    				$('textarea#commentSubmit').attr("disabled", false);
		      	    				$('textarea#commentText').val('');
		      	    			} else {
		      	    				$('textarea#commentText').attr("disabled", true);
		      	    				$('textarea#commentSubmit').attr("disabled", true);
		      	    				$('textarea#commentText').val('Login to comment.');
		      	    			}
		      				$('#primary_comment_holder').html(returned_elements['1']);
		      				$('#primary_comment_holder').fadeIn('300');
		      	    		});
	      	    		}
          		}
          	});
  	}
	closeLoading();
}

// ------------------------------------------
//	Process CAPTCHA

function processCaptcha() {
	js_path_put = functions_path + "/ajax.php";
	send_data = "action=check_captcha&c=" + $('#bd_captcha_input').val();
    	$.post(js_path_put, send_data, function(theResponse) {
		var returned = theResponse.split('+++');
		if (returned['0'] == "1") {
			$('#bd_logged_session').fadeOut('300');
			closeError();
		} else {
			if (returned['2'] == "2") {
				window.location.reload();
			}
			else if (returned['2'] == "3") {
				closeCaptcha();
			}
			else {
				process_error(theResponse,'1');
			}
		}
    	});
}

// ------------------------------------------
//	Process lost password.

function processLostPass() {
	js_path_put = functions_path + "/ajax.php";
	send_data = "action=lost_pass&email=" + $('#lost_email').val() + "&c=" + $('#bd_c_field_lost_pass');
    	$.post(js_path_put, send_data, function(theResponse) {
		var returned = theResponse.split('+++');
		if (returned['0'] == "1") {
			inner = "Your new password has been sent. <a href=\"#\" onClick=\"showLogin();return false;\">Click here</a> to login.";
			$('#bd_logged_session').fadeOut('300', function () {
				$('#bd_logged_session').html(inner);
				$('#bd_logged_session').fadeIn('300');
			}
			);
			closeError();
			closeCaptcha();
		} else {
			process_error(theResponse,returned['2']);
		}
    	});
	return false;
}


// ------------------------------------------
//	Favorite an article

function addFavorite(article,add) {
	js_path_put = functions_path + "/ajax.php";
	send_data = "action=favorite&article=" + article + "&add=" + add;
    	$.post(js_path_put, send_data, function(theResponse) {
		var returned = theResponse.split('+++');
		if (returned['0'] == "1") {
			showSaved(returned['1']);
			$('#favorite_img').attr('src',returned['2']);
			number = parseInt($('#article_favorites').html());
			returned = parseInt(returned['3']);
			together = number + returned;
			if (together < 0) {
				together = 0;
			}
			$('#article_favorites').html(together);
			closeError();
		} else {
			process_error(theResponse);
		}
    	});
}

// ------------------------------------------
//	Follow an article

function addFollow(article,add) {
	js_path_put = functions_path + "/ajax.php";
	send_data = "action=follow&article=" + article + "&add=" + add;
    	$.post(js_path_put, send_data, function(theResponse) {
		var returned = theResponse.split('+++');
		if (returned['0'] == "1") {
			showSaved(returned['1']);
			$('#follow_img').attr('src',returned['2']);
			number = parseInt($('#article_follows').html());
			returned = parseInt(returned['3']);
			together = number + returned;
			if (together < 0) {
				together = 0;
			}
			$('#article_follows').html(together);
			closeError();
		} else {
			process_error(theResponse);
		}
    	});
}

// ------------------------------------------
//	E-Mail Suggest Article

function emailArticle(article) {
	showLoading();
	js_path_put = functions_path + "/ajax.php";
	send_data = "action=get_template&name=email_article";
	$.post(js_path_put, send_data, function(inner) {
		show_captcha(inner);
	});
}

// ------------------------------------------
//	Process registration.

function processEmailFriend() {
	js_path_put = functions_path + "/ajax.php";
	send_data = "article=" + current_article_id + "&" + $('form#email_form').serialize();
    	$.post(js_path_put, send_data, function(theResponse) {
		var returned = theResponse.split('+++');
		if (returned['0'] == "1") {
			closeError();
			closeCaptcha();
			showSaved(returned['1']);
		} else {
			process_error(theResponse);
		}
    	});
}

// ------------------------------------------
//	Process registration.

function processRegistration() {
	if (allow_registration == "1") {
		showLoading();
		js_path_put = functions_path + "/ajax.php";
		send_data = $('form#register_form').serialize();
	    	$.post(js_path_put, send_data, function(theResponse) {
			var returned = theResponse.split('+++');
			if (returned['0'] == "1") {
				send_data = "action=get_template&category=" + current_category_id + "&name=logged_in_sidebar&user=" + $('#reg_username').val();
			    	$.post(js_path_put, send_data, function(inner) {
					$('#bd_logged_session').fadeOut('300', function () {
						$('#bd_logged_session').html(inner);
						$('#bd_logged_session').fadeIn('300');
						// Reprocess article
						getManageBar();
						runArticleReProcess('0','1');
						// window.location.reload();
					});
			    	});
				// window.location.reload();
				closeError();
				closeCaptcha();
			} else {
				process_error(theResponse,returned['2']);
				if (returned['2']) {
					// Highlight fields
					var fields = returned['2'].split('--');
					var len = fields.length;
					for (var i=0; i<len; i++) {
						$('#reg_' + fields[i]).addClass('bd_error_field');
					}
				}
			}
	    	});
	} else {
		process_error('Registration has been disabled.');
    	}
	return false;
}

function clearText(element,default_text) {
	if (element.value == default_text) {
		element.value = '';
	}
}

// ---------------------------------------------------------------------------------------
//	Inline article editing

function triggerEdit() {
	needToConfirm = true;
}

function triggerNoEdit() {
	needToConfirm = false;
}

function minimizeEdit(article_name) {
	$('#bd_article_inline_edit').fadeOut('fast');
	item = "<li class=\"article\" id=\"minEdit\"><a href=\"#\" onclick=\"maximizeEdit();\"><img src=\"" + program_url + "/templates/html/_imgs/manage_bar/icon-maximize.png\" width=\"16\" height=\"16\" border=\"0\" style=\"vertical-align:middle;margin-right:4px;\" alt=\"Click to maximize\" title=\"Click to maximize\" />Editing \"" + article_name + "\"</a></li>";
	$('.bd_options').append(item);
	needToConfirm = true;
}

function maximizeEdit() {
	$('#bd_article_inline_edit').fadeIn('fast');
	$('#minEdit').remove();
	needToConfirm = false;
}

function showAddLink(type) {
	var selected_text = get_selected_text();
	selected_text = selected_text.replace(/\"/g,'&quot;');
	selected_text = selected_text.replace(/\'/g,"&#39;");
	html = "<div id=\"bd_addlink\"><p class=\"bd_small\" style=\"float: right;\"><a href=\"#\" onclick=\"showAddLink('internal');return false;\">Internal Link</a><br /><a href=\"#\" onclick=\"showAddLink('external');return false;\">External Link</a></p>";
	if (type == 'internal') {
		html += "<h2>Internal Link</h2><label>Link Display Text<p class=\"bd_small\" style=\"margin: -5px 0 0 0;\">Optional: controls what is displayed to visitors on the page for this link.</p></label><input type=\"text\" name=\"addLinkName\" id=\"addLinkName\" value=\"" + selected_text + "\" /><br /><label>Webpage Name<p class=\"bd_small\" style=\"margin: -5px 0 0 0;\">Start typing an existing page's name. Select from the returned suggestions to complete the process.</p></label><input type=\"text\" name=\"addLink\" id=\"addLink\" onkeyup=\"suggest('articles',this.value,'name','addLink','id','category,id','linking');\" />";
	} else {
		html += "<h2>External Link</h2><label>Link Display Text<p class=\"bd_small\" style=\"margin: -5px 0 0 0;\">Optional: controls what is displayed to visitors on the page for this link.</p></label><input type=\"text\" name=\"addLinkName\" id=\"addLinkName\" value=\"" + selected_text + "\" /><br /><label>Link URL</label><input type=\"text\" name=\"addLinkURL\" id=\"addLinkURL\" /><input type=\"button\" value=\"Add Link\" onclick=\"addExternalLink();\" />";
	}
	html += "<p class=\"bd_small bd_center\"><a href=\"#\" onclick=\"closeCaptcha();return false;\">Cancel</a></p></div>";
	show_captcha(html);
}

function addExternalLink() {
	if (ed_format_type == 'cms') {
		if ($('#addLinkName').val()) {
			put = "<a href=\"" + $('#addLinkURL').val() + "\">" + $('#addLinkName').val() + "</a>";
		} else {
			put = "<a href=\"" + $('#addLinkURL').val() + "\"></a>";
		}
	} else {
		if ($('#addLinkName').val()) {
			put = "[[" + $('#addLinkURL').val() + "|" + $('#addLinkName').val() + "]]";
		} else {
			put = "[[" + $('#addLinkURL').val() + "]]";
		}
	}
      	addCaller('content',put);
      	closeCaptcha();
}


// ------------------------------------------
//	Logs a user out.

function logout() {
	js_path_put = functions_path + "/ajax.php";
	send_data = "action=logout";
    	$.post(js_path_put, send_data, function(theResponse) {
		var returned = theResponse.split('+++');
		if (returned['0'] == "1") {
      			send_data = "action=get_template&category=" + current_category_id + "&name=logged_out_sidebar";
      		    	$.post(js_path_put, send_data, function(inner) {
      				if ($("#bd_logged_session").length > 0) {
      			   		$('#bd_logged_session').fadeOut('300', function () {
      			   			$('#bd_logged_session').html(inner).fadeIn('300');
      			   			$('#bd_manage_bar').fadeOut('300');
      			   			$('#bd_manage_notices').hide();
      		      				// Re-work the article
      		      				if (current_article_id != '0') {
      							runArticleReProcess('1','1');
      							closeArticleEdit();
      							closePreview();
      						} else {
      							window.location.reload();
      						}
      			   		});
      				} else {
      					window.location.reload();
      				}
      		    	});
			// window.location.reload();
   		} else {
			process_error(theResponse);
   		}
    	});
}

// optional_category and optional_name are used for the
// 404 not found "Create Page" link.

function editArticle(id,optional_name,optional_category) {
	if ($('#bd_article_inline_edit').is(":visible")) {
      		triggerNoEdit();
		$('#bd_manage_article_new').css('background-color','');
		$('#bd_article_inline_edit').hide();
      		clearInterval(timer);
      		closeMedia();
      		editingArticle = '';
	}
	else {
		// Loading
		showLoading();
		// New article
		if (optional_category) {
			current_category_id = optional_category;
		}
		// Send it
		js_path_put = functions_path + "/ajax.php";
		if (! optional_name) {
			optional_name = '';
		}
		send_data = "action=generateEditArticle&id=" + id + "&category=" + current_category_id + "&name=" + optional_name;
	      	$.post(js_path_put, send_data, function(theResponse) {
			var returned = theResponse.split('+++');
			if (returned['0'] == '1') {
			
				// For JS usage
				editingArticle = id;
				
				// Manage bar Page tab to black
				$('#bd_manage_article_new').css('background-color','#000');
		
				// Create the element and add to body,
				// but hide it at first
				$('body').append("<div id=\"bd_article_inline_edit\">" + returned['1'] + "</div>");
		  		$('#bd_article_inline_edit').show();
				//alert('3');
		  		
				// Browser window
				var h = $(window).height();
				var w = $(window).width();
				var difference = h - 39;
				
		  		// Establish the height/width of the preview screen
				var text_difx = h - 120;
				var text_dif_x = w - 50;
	   			$('#bd_article_preview').css('height',text_difx);
	   			$('#bd_article_preview').css('width',text_dif_x);
	   			
	   			// Main Editor Window Height/Width
	   			// Window Height - Manage Bar Height
				$('#bd_article_inline_edit').css('height',difference);
		  		$('#bd_article_inline_edit').html(returned['1']);
		  		
		  		// Right content width
		  		// Window Width - Left Sidebar
				var bd_edit_right_width = w - 282;
				$('#bd_edit_right').css('width',bd_edit_right_width);
				
				// Textarea height/width
				// Window Height - Icon UL Height - Manage Bar Height - Some Space
				var icon_height = $('#bd_edit_icons').innerHeight();
				var content_height = h - icon_height - 39 - 24;
				// Remove right save menu width
				var content_width = bd_edit_right_width - 40;
				$('#content').css('height',content_height);
				$('#content').css('width',content_width);
		  		
	   			// Remove loading
	   			closeError();
	   			
	  		} else {
				process_error(theResponse);
	  		}
	      	});
      	}
      	return false;
}


function previewArticle() {
	if ($('#bd_article_preview').is(":visible")) {
		closePreview();
	} else {
		showLoading();
		js_path_put = functions_path + "/ajax.php";
		send_data = "action=previewArticle&id=" + editingArticle + "&" + $('#bd_articleEdit').serialize();
	      	$.post(js_path_put, send_data, function(theResponse) {
			$('#bd_article_preview').html(theResponse);
		   	$('#bd_aie_body').fadeOut('300', function () {
	   			$('#bd_article_preview').fadeIn('300')
				$('#bd_aie_saved').html('<a href="#" onclick="closePreview();return false;">Previewing Article</a>');
				$("#bd_air_preview_button").attr('value', 'Close Preview');
	   			closeError();
	   		});
	      	});
      	}
}

function closePreview() {
     	$('#bd_article_preview').fadeOut('300', function () {
    		$('#bd_aie_body').fadeIn('300')
  		$('#bd_article_preview').html('');
		$('#bd_aie_saved').html('');
		$("#bd_air_preview_button").attr('value', 'Preview');
    	});
}

function saveArticle(done,autosave) {
	if (autosave && editingArticle == 'new') {
		clearInterval(timer);
	} else {
		if (editingArticle) {
			showLoading();
			js_path_put = functions_path + "/ajax.php";
			send_data = "action=editArticle&id=" + editingArticle + "&done=" + done + "&" + $('#bd_articleEdit').serialize();
		      	$.post(js_path_put, send_data, function(theResponse) {
				var returned = theResponse.split('+++');
				if (returned['0'] == '1') {
      					triggerNoEdit();
					if (editingArticle == 'new') {
      						triggerNoEdit();
						editingArticle = '';
						window.location = returned['1'];
					} else {
						if (done != '1') {
							if (autosave == '1') {
								showSaved('Auto-saved.');
							} else {
								showSaved('Saved!');
							}
						} else {
							clearInterval(timer);
							editingArticle = '';
							// Close editor
							// $('#bd_article_inline_edit').remove();
			      				// Re-work the article.
							runArticleReProcess('0','1');
							// window.location.reload();
							// Close the edit.
							timer = '';
							closeArticleEdit();
		      					closeMedia();
						}
					}
			   		closeError();
		  		} else {
					process_error(theResponse);
		  		}
		      	});
	      	} else {
      			triggerNoEdit();
			clearInterval(timer);
	      	}
      	}
      	return false;
}


function closeArticleEdit() {
	if (editingArticle) {
		if (confirm('All unsaved changes will be lost!\n\nPlease confirm that you wish to close the editor...')) {
			$('#bd_article_inline_edit').remove();
			$('#bd_manage_article_new').css('background-color','');
			closeError();
			closeSaved();
			clearInterval(timer);
		}
	} else {
		closeError();
		closeSaved();
		clearInterval(timer);
		$('#bd_article_inline_edit').remove();
		$('#bd_manage_article_new').css('background-color','');
	}
}

//	Auto-save
function timerMethod() {
    saveArticle('0','1');
}


// Closes the media gallery display.
function closeMedia() {
	screen_open = '0';
	$('#media_box').fadeOut(300);
	$('#media_box').remove();
}

function closeUploadFile() {
	
}


// --------------------------------------------
// Show Upload Box
// type 1 = file
// type 2 = image
function showUpload(type,path,article,id) {
	upload_type = type;
	// Upload HTML
	final_html = '<div id="bd_upload_box">';
	final_html += '<form action="' + functions_path + '/upload_file.php" method="post" enctype="multipart/form-data" onsubmit="confirmUpload();">';
	final_html += '<p style=\"float:right;\"><a href="#" onclick="closeDiv(\'bd_upload_box\');return false;">Close</a></p><h1 class="bd_h1">File Not Found</h1><p>We were unable to find this file on your server. Please either upload it using the form provided below or upload it directly to your server using an FTP client.</p>';
	final_html += '<h2 class="bd_h2" style="margin-top:25px;">Select the file</h2><input type="hidden" name="article" value="' + article + '" /><input type="hidden" name="type" value="' + type + '" /><input type="hidden" name="path" id="upload_path" value="' + path + '" /><input type="hidden" name="id" id="upload_id" value="' + id + '" />';
	final_html += '<input type="file" name="file" id="file_input" />';
	if (type == '1') {
		final_html += '<h2 class="bd_h2" style="margin-top:25px;">Additional Options</h2>';
		final_html += '<p><b>Require that users be logged in to download?</b><br /><input type="radio" name="login" value="1" /> Yes <input type="radio" name="login" value="0" checked="checked" /> No<br /><br />';
		final_html += '<b>Limit downloads per person (0 = unlimited)?</b><br /><input type="text" name="limit" value="0" style="width:100px;" /></p>';
	}
	final_html += '<input type="submit" name="submit" value="Upload" /><input type="button" value="Cancel" onclick="closeArticleEdit();" />';
	final_html += '</form>';
	final_html += '</div>';
    	// Browser window
    	var h = $(window).height();
    	var w = $(window).width();
    	var difference = h - 29;
    	$('#bd_article_inline_edit').css('height',difference);
      	$('#bd_article_inline_edit').html(final_html);
      	$('#bd_article_inline_edit').fadeIn('300');
}


// Used before the upload is submitted to the server.
// upload_file.php will run some checks to ensure
// that this user can upload this file.
function confirmUpload() {
      	if (editingArticle) {
      		saveArticle('0');
      	}
	js_path_put = functions_path + "/upload_file.php";
	send_data = "action=confirm&id=" + $('#upload_id').val() + "path=" + $('#upload_path').val() + "&type=" + upload_type + "&file=" + $('#file_input').val();
    	$.post(js_path_put, send_data, function(theResponse) {
		var returned = theResponse.split('+++');
		// Continue with submission
		if (returned['0'] == '1') {
			return true;
		}
		// Prevent form submission
		else {
			process_error(theResponse);
			return false;
		}
    	});
    	return false;
}

// ---------------------------------------------------------------------------------------
//	Classify comment

function reClassifyComment(comment_id) {
	classifying_comment = comment_id;
	js_path_put = functions_path + "/ajax.php";
	send_data = "action=get_comment_types&comment_id=" + comment_id;
    	$.post(js_path_put, send_data, function(inner) {
    		var returned = inner.split('+++');
    		if (returned['0'] == '1') {
			show_captcha(returned['1']);
		} else {
			process_error(theResponse);
		}
    	});
	
}

function sendClassify() {
	js_path_put = functions_path + "/ajax.php";
	send_data = "action=perform_classify&status=" + $('input:radio[name=new_cm_type]:checked').val() + "&id=" + classifying_comment;
    	$.post(js_path_put, send_data, function(inner) {
    		var returned = inner.split('+++');
    		if (returned['0'] == '1') {
			// Get new updated comments
			js_path_put = functions_path + "/ajax.php";
			send_data = "action=reget_comments&article=" + current_article_id ;
		    	$.post(js_path_put, send_data, function(newhtml) {
			   	$('#primary_comment_holder').fadeOut('300', function () {
		   			$('#primary_comment_holder').html(newhtml);
		   			$('#primary_comment_holder').fadeIn('300');
					// Show saved dialogue!
		   			current_status = hold_ori_status;
					closeCaptcha();
					showSaved();
					classifying_comment = '';
		   		});
		    	});
		} else {
			process_error(theResponse);
		}
    	});
}


// ---------------------------------------------------------------------------------------
//	Add a category

function addCategory(category) {
	if ($('#add_category').is(":visible")) {
		closeCategoryAdd();
	} else {
		var position = $('#bd_manage_category_new').position();
		var width = $('#bd_manage_category_new').innerWidth();
		var leftA = position.left;
		var element = "<div id=\"add_category\" style=\"width:" + width + "px;background-color:#000;position:fixed;bottom:40px;left:" + leftA + "px;\"><div style=\"padding:24px;\"><form id=\"bda_cate_add\" onsubmit=\"return addCategoryFinal('" + category + "');\"><label style=\"padding:0;margin:0 0 4px 0;color:#fff !important;\">Category Name</label><input type=\"text\" name=\"name\" id=\"bda_category_name\" value=\"\" style=\"width:120px;\" /><br /><p class=\"small\"><a href=\"" + program_url + "/functions/go_to_admin.php?action=category_add\">Create from control panel &raquo;</a></p></form></div></div>";
		$('body').append($(element).hide());
		$('#add_category').fadeIn('300');
		$('#bd_manage_category_new').css('background-color','#000');
		$('#add_category').css('z-index','99999');
	}
}

function closeCategoryAdd() {
      	$('#add_category').fadeOut('300', function() {
      		$('#add_category').remove();
      		// Manage bar Page tab to black
      		$('#bd_manage_category_new').css('background-color','');
      	});
}

function addCategoryFinal(category) {
	js_path_put = functions_path + "/ajax.php";
	send_data = "action=addCategory&name=" + escape($('#bda_category_name').val()) + "&category=" + current_category_id;
    	$.post(js_path_put, send_data, function(theResponse) {
    		var returned = theResponse.split('+++');
    		if (returned['0'] == '1') {
    			runArticleReProcess('0','1','1','1');
			closeCategoryAdd();
			showSaved();
			closeCaptcha();
		} else {
			process_error(theResponse);
		}
    	});
    	return false;
}

// ---------------------------------------------------------------------------------------
//	Display divs

function expandDiscussion(id) {
	   	$('#subcommentsTop' + id).fadeOut('300', function () {
   			$('#subcomments' + id).fadeIn('300')
   		});
}

function commentReply(id) {
	   	$('#showReplyTop' + id).fadeOut('300', function () {
   			$('#showReply' + id).fadeIn('300')
   		});
}

function commentEdit(id) {
	   	$('#showReplyTop' + id).fadeOut('300', function () {
   			$('#showEdit' + id).fadeIn('300')
   		});
}

function cancelReply(id) {
	   	$('#showReply' + id).fadeOut('300', function () {
   			$('#showReplyTop' + id).fadeIn('300')
   		});
}

function cancelEdit(id) {
	   	$('#showEdit' + id).fadeOut('300', function () {
   			$('#showReplyTop' + id).fadeIn('300')
   		});
}

function showLogin() {
	showLoading();
	js_path_put = functions_path + "/ajax.php";
	send_data = "action=get_template&name=login_sidebar";
	//alert(js_path_put + '--' + send_data);
    	$.post(js_path_put, send_data, function(inner) {
        	if (login_type == '1') {
			show_captcha(inner);
      		} else {
			$('#bd_logged_session').fadeOut('300', function () {
				$('#bd_logged_session').html(inner);
				$('#bd_logged_session').fadeIn('300');
				closeCaptcha();
			});
		}
    	});
}

function showLostPass() {
	showLoading();
	js_path_put = functions_path + "/ajax.php";
	send_data = "action=get_template&name=lost_password_sidebar";
    	$.post(js_path_put, send_data, function(inner) {
        	if (login_type == '1') {
			show_captcha(inner);
      		} else {
			$('#bd_logged_session').fadeOut('300', function () {
				$('#bd_logged_session').html(inner);
				$('#bd_logged_session').fadeIn('300');
				closeCaptcha();
			});
		}
    	});
}


function showRegister() {
	showLoading();
	if (allow_registration == "1") {
		js_path_put = functions_path + "/ajax.php";
		send_data = "action=get_template&name=register_sidebar";
	    	$.post(js_path_put, send_data, function(inner) {
	    		if (registration_type == '1') {
				show_captcha(inner);
			} else {
				$('#bd_logged_session').fadeOut('300', function () {
					$('#bd_logged_session').html(inner);
					$('#bd_logged_session').fadeIn('300');
					closeCaptcha();
				});
			}
	    	});
    	} else {
		process_error('Registration has been disabled.');
    	}
}

function showComment(id) {
	$('#hiddenText' + id).hide();
	$('#hidden' + id).fadeIn('300');
}

function show_captcha(inner) {
	if ($('#bd_captcha').is(":visible")) {
		$('#bd_captcha').html(inner);
	} else {
		closeCaptcha();
		combine = "<div id=\"bd_captcha\" class=\"bd_dropshadow\" style=\"z-index:5061;display:none;\">" + inner + "</div>";
		element = "<div id=\"bd_image_display\" style=\"z-index:5060;position:fixed; top:0; left:0; width:100%; height:100%; background:url('" + program_url + "/templates/html/_imgs/" + use_bg_image + "') top left;\">";
		element += combine
		element += "</div>";
		$('body').append($(element).hide());
		$('#bd_image_display').show(function() {
			$('#bd_captcha').slideDown('fast');
		});
	}
	closeLoading();
}

function closeCaptcha() {
	closeError();
	$('#bd_captcha').slideUp('fast',function() {
		$('#bd_captcha').remove();
		$('#bd_image_display').remove();
	});
}

function closeDiv(id) {
	$('#' + id).fadeOut('100');
      	$('#bd_article_inline_edit').hide();
}

function process_error(error,captcha) {
	closeLoading();
	if (captcha == "1") {
		inner = '<form id="captcha_form" onSubmit="return false;"><center>';
		inner += '<img src="' + program_url + '/functions/captcha.php" class="bd_img_border" style="margin-bottom:10px;" />';
		inner += '<p class=\"bd_normal_text\" style=\"color:red;\">' + error + '</p>';
		inner += '<label>Confirmation Required</label>';
		inner += '<input type="text" name="bd_captcha_input" id="bd_captcha_input" style="width:180px;" />';
		inner += '<p class="bd_field_desc">Please confirm that you are human by inputting the text above into this textbox.</p>';
		inner += '<input type="button" onClick="processCaptcha();return false;" value="Confirm" />';
		inner += '</center></form>';
		closeError();
		show_captcha(inner);
	} else {
		var returned = error.split('+++');
		if (returned['1']) {
			theError = returned['1'];
		}
		else {
			theError = error;
		}
		show_error(theError);
	}
	return false;
}

function show_error(error) {
	closeError();
	combine = "<div id=\"bd_ajax_error\" style=\"z-index:5065;display:block;cursor:pointer;\"><div class=\"bd_ae_pad\" id=\"bd_ae_pad\" onclick=\"closeError();\">" + error + "</div></div>";
	$('body').append($(combine).hide().fadeIn(50));
}

function closeError(error) {
	closeLoading();
	$('#bd_ajax_error').hide('fast');
	$('#bd_ajax_error').remove();
	$('#bd_ae_pad').remove();
}

function closeCategory(id) {
	$('#articles' + id).hide();
}

function hideCategories() {
	$('#bd_floating_area ul').hide();
}

function expandCategory(id) {

//	alert(current_showing + '---' + id + '---' + expandMenuType);

	$('#articles' + current_showing).hide();
	if (expandMenuType == 'fade' || expandMenuType === undefined) {
		$('#articles' + id).fadeIn('300');
	}
	else if (expandMenuType == 'slide') {
		$('#articles' + id).slideDown();
	}
	else if (expandMenuType == 'instant') {
		$('#articles' + id).show();
	}
	current_showing = id;
	current_category_id = id;
}

function originalMenu(previous) {
	if (! previous) {
		previous = "0";
	}
	$('#articles' + current_showing).hide();
	if (expandMenuType == 'fade' || expandMenuType === undefined) {
		$('#articles' + previous).fadeIn('300');
	}
	else if (expandMenuType == 'slide') {
		$('#articles' + previous).slideDown();
	}
	else if (expandMenuType == 'instant') {
		$('#articles' + previous).show();
	}
	current_showing = previous;
	current_category_id = previous;
}

function show_status(status) {
	if (! current_status) {
		current_status = '0';
	}
	$('#status' + current_status).hide();
	$('#statusActive' + current_status).removeClass('on');
	$('#status' + status).fadeIn('300');
	$('#statusActive' + status).addClass('on');
	current_status = status;
}


function showHiddenHeaders() {
	if (showing_headers == 1) {
		showing_headers = 0;
		$('li.hide').hide();
	} else {
		showing_headers = 1;
		$('li.hide').show();
	}
	return false;
}


function openHelpBubble(id) {
	div = "help_" + id;
	// Get the location relative
	// to the page and the element
	// holding the image.
	var offset = $('#' + id).position();
	topy = offset.top;
	leftx = offset.left;
	var offset_true = $('#' + id).offset();
	true_left = offset_true.left;
	// Make sure this isn't going
	// off the page.
	user_width = $(window).width();
	bubble_width = $('#' + div).outerWidth();
	total_right = true_left + bubble_width + 25;
	if (total_right > user_width) {
		add_offset = leftx - bubble_width;
	} else {
		add_offset = leftx + 25;
	}
	// Display the help bubble
	$('#' + div).css('top',topy);
	$('#' + div).css('left',add_offset);
	$('#' + div).fadeIn('150');
}

function closeHelpBubble(id) {
	div = "help_" + id;
	$('#'+div).fadeOut('150');
}



function showSubcomments(id) {
	sub_coms = "subcomments" + id;
	main_coms = "comment" + id;
      	size = $('.bd_a_main_comment').size();
      	if (size <= 1) {
      		run_subcom_hide(id,main_coms,sub_coms);
      	} else {
      	    	$('.bd_a_main_comment').not('#' + main_coms).hide(function () {
      	    		run_subcom_hide(id,main_coms,sub_coms);
      	    	});
      	}
}

function run_subcom_hide(id,main_coms,sub_coms) {
      	$('#' + main_coms).show();
      	$('#' + sub_coms).fadeIn('300');
      	$('#' + main_coms).addClass('bd_thread_active');
      	$('#bd_subcom_return').show();
      	current_subs = sub_coms;
      	viewing_thread = id;
}

function hideSubcomments(id) {
	sub_coms = "subcomments" + id;
	main_coms = "comment" + id;
    	$('#' + current_subs).fadeOut('300', function () {
    		$('.bd_a_main_comment').fadeIn('300');
    		$('#bd_subcom_return').hide();
    		current_subs = '';
    		viewing_thread = '';
	    	$('.bd_a_main_comment').removeClass('bd_thread_active');
    	});
}

function showSaved(text) {
	closeError();
	if (! text) { text = 'Saved!'; }
	element = "<div id=\"bd_saved\" class=\"bd_shadow_white\" style=\"z-index:99999;cursor:pointer;\" onclick=\"closeSaved();\">" + text + "</div>";
	$('body').append($(element).hide().fadeIn('150'));
	setTimeout(function () { closeSaved() }, 2000);
}

function closeSaved() {
	$("#bd_saved").fadeOut(50);
	$("#bd_saved").remove();
}

/* --------------- Images --------------------- */


function viewFullImage(path,caption,title,full_width,full_height) {
	window_x = $(window).width();
	window_y = $(window).height();
	
	final_full_width = full_width;
	final_full_height = full_height;
	
	// Make sure image fits on screen
	if (full_width > window_x) {
		new_x = window_x - 60;
		scaling = new_x / full_width;
		new_y = Math.round(full_height * scaling);
		final_full_width = new_x;
		final_full_height = new_y;
	} else {
		final_full_width = full_width;
	}
	
	if (full_height > window_y) {
		new_y = window_y - 60;
		scaling = new_y / full_height;
		new_x = Math.round(full_width * scaling);
		final_full_width = new_x;
		final_full_height = new_y;
	} else {
		final_full_height = full_height;
	}
	
	margx = Math.round((window_x - final_full_width) / 2);
	margy = Math.round((window_y - final_full_height) / 2);
	from_top = window_y - final_full_height;
	
	element = "<div id=\"bd_image_display\" onclick=\"closeImage();return false;\" style=\"z-index:5060;cursor:pointer; position:fixed; top:0; left:0; width:100%; height:100%; background:url('" + program_url + "/templates/html/_imgs/" + use_bg_image + "') top left;\">";
	element += "<div id=\"bd_img_inner\" style=\"width:" + final_full_width + "px;margin:" + margy + "px auto 0 auto;text-align:center;\"><img src=\"" + path + "\" width=\"" + final_full_width + "\" height=\"" + final_full_height + "\" border=\"0\" alt=\"" + title + "\" title=\"" + title + "\" class=\"bd_gallery_img\" />";
		if (caption) {
			element += "<div class=\"bd_gallery_caption\">" + caption + "</div>";
		}
	element += "</div></div>";
	$('body').append($(element).hide().fadeIn('150'));
}

function closeImage() {
	$('#bd_image_display').hide();
	$('#bd_image_display').remove();
}


function showLoading() {
	div = "<div id=\"bd_sys_loading\" onclick=\"closeLoading();\" style=\"width:42px;height:42px;background-color:#5092FE;border:1px solid #4681F9;position:absolute;top:50%;left:50%;margin-left:-21px;margin-top:-21px;z-index:9000;-webkit-border-radius:21px;-moz-border-radius:21px;border-radius:21px;\"><img src=\"" + program_url + "/templates/html/_imgs/loading.gif\" width=32 height=32 border=0 title=\"Loading, please wait...\" alt=\"Loading, please wait...\" style=\"display:block;margin: 5px auto;\" /></div>";
	$('body').append($(div));
}

function closeLoading() {
	$('#bd_sys_loading').remove();
}


/* --------------- To Do List --------------------- */

function markToDo(item_id,widget_id) {
	showLoading();
	js_path_put = functions_path + "/widgets.php";
	send_data = "action=mark_todo&id=" + item_id + "&widget=" + widget_id;
    	$.post(js_path_put, send_data, function(theResponse) {
		var returned = theResponse.split('+++');
        	if (returned['0'] == '1') {
        		li_item = 'todoItem' + item_id;
        		todoImg = 'todoImg' + item_id;
        		// 1 = complete
        		// 0 = incomplete
        		if (returned['1'] == '1') {
        			$('#' + li_item).removeClass('incomplete');
        			$('#' + li_item).addClass('complete');
        			finalImg = theme + '/imgs/check_on.png';
        		} else {
        			$('#' + li_item).removeClass('complete');
        			$('#' + li_item).addClass('incomplete');
        			finalImg = theme + '/imgs/check_off.png';
        		}
        		$('#' + todoImg).attr('src',finalImg);
        		showSaved();
      		} else {
			process_error(theResponse);
		}
    	});
	closeLoading();
	return false;
}

function delToDo(item_id,widget_id) {
	showLoading();
	js_path_put = functions_path + "/widgets.php";
	send_data = "action=del_todo&id=" + item_id + "&widget=" + widget_id;
    	$.post(js_path_put, send_data, function(theResponse) {
		var returned = theResponse.split('+++');
        	if (returned['0'] == '1') {
        		li_item = 'todoItem' + item_id;
        		$('#' + li_item).fadeOut('300', function() {
        			$('#' + li_item).remove();
        		});
      		} else {
			process_error(theResponse);
		}
    	});
	closeLoading();
	return false;
}

function toDoAdd(widget_id) {
	showLoading();
	js_path_put = functions_path + "/widgets.php";
	var addVal = widget_id + 'newitem';
	var ulList = 'widget' + widget_id;
	send_data = "action=addItem&widget=" + widget_id + "&name=" + $('#' + addVal).val();
    	$.post(js_path_put, send_data, function(theResponse) {
		var returned = theResponse.split('+++');
        	if (returned['0'] == '1') {
			$("#" + ulList + " li:last-child").before(returned['1']);
			$('#' + addVal).val('');
        		showSaved();
      		} else {
			process_error(theResponse);
		}
    	});
	closeLoading();
}


/* --------------- Date List --------------------- */

function getPageRange(month,widget_id) {
	showLoading();
        addTo = widget_id + month;
        addToA = widget_id + month + "items";
        
        if ($("#" + addToA).length > 0) {
		$('#' + addToA).slideToggle();
        } else {
		js_path_put = functions_path + "/widgets.php";
		send_data = "action=getPagesByMonth&widget=" + widget_id + "&month=" + month;
	    	$.post(js_path_put, send_data, function(theResponse) {
			var returned = theResponse.split('+++');
	        	if (returned['0'] == '1') {
	        		$('#' + addTo).append(returned['1']).hide().slideDown();
	      		} else {
				process_error(theResponse);
			}
	    	});
    	}
	closeLoading();
}


/* --------------- Manipulate Divs --------------------- */

function showDiv(id) {
	$('#' + id).fadeIn('300');
}

function hideDiv(id) {
	$('#' + id).fadeOut('300');
}

function toggleDiv(id) {
	$('#' + id).toggle();
}

function crossfade(id1,id2) {
	$('#' + id1).fadeOut('300', function(){
		$('#' + id2).fadeIn('300');
	});
	return false;
}