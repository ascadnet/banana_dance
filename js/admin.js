
/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Various admin CP functions.
	
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


	// --------------------------------------------
	//	START UP FUNCTIONS
	//	Window resizing features.
	
	// This checks the page when "Back" button is clicked.
	//window.onload = function() {
	//	
	//};
	
	var js_path = '../js';
	var functions_path = '../functions';
	
	$(window).load(function() {
		resizeWindow();
	});
	
	$(window).resize(function() {
		resizeWindow();
	});
	
	function resizeWindow() {
		// Browser window
		var h = $(window).height();
		var w = $(window).width();
		var difference = h - 98;
		$('#main').css('height',difference);
	}
	
	// --------------------------------------------
	//	Navigation menu features

	function showLinks(id) {
		$('#links' + id).slideToggle('fast');
	}
	
	// --------------------------------------------
	//	Search Hover
	
	function showSearch(html,field_id) {
		var offset = $('#' + field_id).offset();
		var height = $('#' + field_id).height();
		$('#search_q_body').html(html);
		$('#search_q_hover').css('top',offset.top+height+10+'px');
		$('#search_q_hover').css('left',offset.left+'px');
		$('#search_q_hover').show('fast');
	}
	
	$(document).ready(function() {
		$('.search_hover').hover(
		  function () {
		    showText($(this).attr('id'));
		  },
		  function () {
		    clearSearch()
		  }
		)
	});
	
	function showText(element) {
		get_text = element + "_text";
		putText = $('#' + get_text).html();
		showSearch(putText,element);
	}
	
	function clearSearch() {
		$('#search_q_hover').hide('fast');
	}
	
	// --------------------------------------------
	//	Show filters on a page
	
	function showFilters() {
		$('#filters').slideToggle('fast');
	}
	
	// --------------------------------------------
	//	Swap a tab
	
	var current_tab = '1';
	function swapTab(id) {
		if (id != current_tab) {
	   		$('#tab' + current_tab).fadeOut('50', function () {
	   			$('#tab' + id).fadeIn('50');
	   		});
	   		$('#litab' + current_tab).removeClass('on');
	   		$('#litab' + id).addClass('on');
	   		current_tab = id;
   		}
	}
	
	
	// --------------------------------------------
	//	Submit form on "enter" press unless we
	//	are editing a textarea field.
	
	var valid_submit = "1";
	$(document).ready(function() {
		$("textarea").focus(function () {
			valid_submit = "0";
		});
		$("textarea").blur(function () {
			valid_submit = "1";
		});
		
		$(document).keyup(function(event) { 
			if (event.keyCode == 13 && valid_submit == "1") {
				$("#edit").submit();
			}
		});
	});
	
	// --------------------------------------------
	//	Allow tabs in textareas
	
	$(function() {
	    $("textarea").tabby();
	});
	



// -----------------------------------------------------------------
// 	Shows the formatting guide

function showFormatting() {
	$('#formatting_guide').toggle('fast');
}

// -----------------------------------------------------------------
// 	Set a theme

function setTheme(theme,type) {
	// type 1 = html | type 2 = mobile
	send_data = "action=set_theme&theme=" + theme + "&type=" + type;
    	$.post('functions/ajax.php', send_data, function(theResponse) {
		var returned = theResponse.split('+++');
		if (returned['0'] == "1") {
			$('.theme_status').attr('src','imgs/status-off.png');
			$('#status' + theme).attr('src','imgs/status-on.png');
			showSaved();
			admin_close_error();
		} else {
			admin_error(theResponse);
		}
    	});
    	return false;
}


// -----------------------------------------------------------------
// 	Set a theme

function clearPage(id) {
	if (confirm('Confirm page clearing...')) {
		// type 1 = html | type 2 = mobile
		send_data = "action=clear_page&id=" + id ;
	    	$.post('functions/ajax.php', send_data, function(theResponse) {
			var returned = theResponse.split('+++');
			if (returned['0'] == "1") {
				showSaved();
				admin_close_error();
			} else {
				admin_error(theResponse);
			}
	    	});
	    	return false;
    	}
}

// -----------------------------------------------------------------
// 	Revert a Page

function revertPage(id) {
	send_data = "action=revert_page&id=" + id;
    	$.post('functions/ajax.php', send_data, function(theResponse) {
		var returned = theResponse.split('+++');
		if (returned['0'] == "1") {
			showSaved();
			admin_close_error();
		} else {
			admin_error(theResponse);
		}
    	});
    	return false;
}

// -----------------------------------------------------------------
// 	Remove a logo

function removeLogo() {
	send_data = "remove_logo=1";
    	$.post('functions/logo.php', send_data, function(theResponse) {
		var returned = theResponse.split('+++');
		if (returned['0'] == "1") {
			showSaved();
			$('#current_logo').html(returned['1']);
			admin_close_error();
		} else {
			admin_error(theResponse);
		}
    	});
    	return false;
}

// -----------------------------------------------------------------
// 	Run a generic delete command for anything requiring
//	administrative privileges to delete from the database.

function deleteID(table,id,removeDivs) {
	if (confirm('Confirm deletion...')) {
		send_data = "action=delete&id=" + id + "&table=" + table;
	    	$.post('functions/ajax.php', send_data, function(theResponse) {
			var returned = theResponse.split('+++');
			if (returned['0'] == "1") {
				if (removeDivs) {
					$('#right_contain').fadeOut('300', function () {
						$('#right_contain').html('<i>Deleted</i>');
						$('#right_contain').fadeIn('300');
					});
					// Remove DIVs
					var blow_divs = removeDivs.split(',');
					var len = blow_divs.length;
					for(var i=0; i<len; i++) {
						$('#' + blow_divs[i]).fadeOut('300');
					}
					
				} else {
					$('#' + id).fadeOut('300', function () {
						$('#' + id).remove();
					});
				}
				admin_close_error();
			} else {
				admin_error(theResponse);
			}
	    	});
    	}
    	return false;
}


// -----------------------------------------------------------------
// 	For fancier forms. Inputs selection into
//	a hidden field of ID=name, and highlights
//	an element with ID=[name][value]
//	UL should be ID=[name]_ul
//	Class on UL should be "field_option"

function highlightSelected(name,value) {
	ul_name = name + "_ul";
	li_name = name + value;
	$('#' + ul_name + ' li').each(function(index) {
	    $(this).removeClass('selected');
	});
	$('#' + name).val(value);
	$('#' + li_name).addClass('selected');
    	return false;
}


// -----------------------------------------------------------------
// 	Switch something's status

function switchStatus(table,id,field_name) {
	final_page = "functions/ajax.php";
	send_data = "action=switchStatus&table=" + table + "&id=" + id + "&field=" + field_name;
    	$.post(final_page, send_data, function(theResponse) {
		var returned = theResponse.split('+++');
		if (returned['0'] == "1") {
			img = "status" + id;
			$('#' + img).attr('src',returned['1']);
			showSaved();
			admin_close_error();
		} else {
			admin_error(theResponse);
		}
    	});
    	return false;
}


// -----------------------------------------------------------------
// 	Run a generic edit command for anything requiring
//	administrative privileges to delete from the database.

function editID(id,page_name,form) {
	if (! page_name) {
		final_page = "functions/ajax.php";
	} else {
		final_page = "functions/" + page_name + ".php";
	}
	if (! form) {
		form = 'edit';
	}
	send_data = $('form#' + form).serialize();
    	$.post(final_page, send_data, function(theResponse) {
		// alert(theResponse);
		var returned = theResponse.split('+++');
		if (returned['0'] == "1") {
			if (id == "new") {
				showSaved();
				window.location = "index.php?saved=1&l=" + returned['2'] + "&id=" + returned['1'];
			} else {
				showSaved();
			}
			admin_close_error();
		} else {
			admin_error(theResponse);
		}
    	});
    	return false;
}


// -----------------------------------------------------------------
// 	Updates or creates an article

function saveChanges(article_id,final_update) {
	send_data = $('form#edit').serialize();
	if (final_update == '1') {
		send_data += '&finalize=1';
	}
    	$.post('functions/edit_article.php', send_data, function(theResponse) {
		var returned = theResponse.split('+++');
		if (returned['0'] == "1") {
			if (returned['1'] == 'notice') {
				admin_error(returned['2'],'1');
			} else {
				if (article_id == "new") {
					showSaved();
					window.location = "index.php?l=article_edit&id=" + returned['1'];
				}
				else if (final_update == '1') {
					showSaved();
					window.location = returned['1'];
				}
				else {
					showSaved();
				}
				admin_close_error();
			}
		} else {
			admin_error(theResponse);
		}
    	});
    	return false;
}


// -----------------------------------------------------------------
// 	Create a redirect rule

function createRedirectRule(article_name,category_name,article_id) {
	send_data = "action=redirectRule&article=" + article_id + "&name=" + article_name + "&category=" + category_name;
    	$.post('functions/ajax.php', send_data, function(theResponse) {
		var returned = theResponse.split('+++');
		if (returned['0'] == "1") {
			showSaved();
			admin_close_error();
		} else {
			admin_directions(returned['1']);
		}
    	});
    	return false;
}


// -----------------------------------------------------------------
// 	Edit a comment's settings

function saveCommentChanges(comment_id) {
	send_data = $('form#edit').serialize();
    	$.post('functions/edit_comment.php', send_data, function(theResponse) {
		var returned = theResponse.split('+++');
		if (returned['0'] == "1") {
			showSaved();
			admin_close_error();
		} else {
			admin_error(theResponse);
		}
    	});
    	return false;
}


// -----------------------------------------------------------------
// 	Creates a POST list from a sortable list

function getSortable(id) {
	var order = $('#' + id).sortable("serialize");
	return order;
}


// -----------------------------------------------------------------
// 	Updates a category

function saveCategoryChanges(category_id) {
	send_data = $('form#edit').serialize();
	var order = getSortable('sortable');
	var orderA = getSortable('sortableA');
	send_data = send_data + "&" + order + "&" + orderA;
    	$.post('functions/edit_category.php', send_data, function(theResponse) {
		var returned = theResponse.split('+++');
		if (returned['0'] == "1") {
			if (category_id == "new") {
				showSaved();
				window.location = "index.php?saved=1&l=category_edit&id=" + returned['1'];
			} else {
				showSaved();
				admin_close_error();
			}
		} else {
			admin_error(theResponse);
		}
    	});
    	return false;
}


// -----------------------------------------------------------------
// 	Updates a category's default article

function makeDefault(id,article) {
	send_data = "action=make_default&id=" + id + "&article=" + article;
    	$.post('functions/ajax.php', send_data, function(theResponse) {
		var returned = theResponse.split('+++');
		if (returned['0'] == "1") {
			// Old default
			html = "<a href=\"#\" onClick=\"makeDefault('" + returned['3'] + "','" + returned['2'] + "');return false;\">Make Homepage</a>";
			$('#default' + returned['2']).html(html);
			// New default
			$('#default' + returned['1']).html('<b>Homepage</b>');
			showSaved();
			admin_close_error();
		} else {
			admin_error(theResponse);
		}
    	});
    	return false;
}


// -----------------------------------------------------------------
// 	Delete an article
//	Found on the edit category page.
/*
function deleteArticle(id) {
	if (confirm('Confirm deletion...')) {
		send_data = "action=del_article&id=" + id;
	    	$.post('functions/ajax.php', send_data, function(theResponse) {
			var returned = theResponse.split('+++');
			if (returned['0'] == "1") {
				$('#article_' + id).fadeOut('300', function () {
					$('#article_' + id).remove();
				});
				admin_close_error();
			} else {
				admin_error(returned['1']);
			}
	    	});
    	}
}
*/

// -----------------------------------------------------------------
// 	Delete a category
//	Found on the edit category page.

function deleteCategory(id) {
	if (confirm('Confirm deletion...')) {
		send_data = "action=del_category&id=" + id;
	    	$.post('functions/ajax.php', send_data, function(theResponse) {
			var returned = theResponse.split('+++');
			if (returned['0'] == "1") {
				$('#category_' + id).fadeOut('300', function () {
					$('#category_' + id).remove();
				});
				admin_close_error();
			} else {
				admin_error(theResponse);
			}
	    	});
    	}
    	return false;
}

// -----------------------------------------------------------------
// 	Give user a badge

function giveBadge() {
	id = $('#badge_give').val();
  	send_data = "action=give_badge&user=" + user_id + "&id=" + id;
      	$.post('functions/ajax.php', send_data, function(theResponse) {
  		var returned = theResponse.split('+++');
  		if (returned['0'] == "1") {
			$("#new_badge").before(returned['1']);
  			showSaved();
  		} else {
  			admin_error(theResponse);
  		}
      	});
    	return false;
}

// --------------------------------------------
//	Logout
	
function logout() {
	window.location = 'functions/logout.php';
}
	
	
// -----------------------------------------------------------------
// 	Display various divs

function showSaved() {
	$('#saved').fadeIn('300');
	setTimeout(closeSaved,2000);
}

function closeSaved() {
	$('#saved').fadeOut('300');
}

function admin_error(error,use_second_spot) {
	var returned = error.split('+++');
	
	if (use_second_spot == '1') {
		final_error = returned['2'];
	}
	else if (returned['1']) {
		final_error = returned['1'];
	}
	else {
		final_error = error;
	}
	$('#error').html(error);
	$('#error').fadeIn('300');
}

function admin_directions(directions,file) {
	directions = "<p class=\"center\"><a href=\"#\" onclick=\"admin_close_directions();return false;\">Close Window</a></p>" + directions + "<p class=\"center\"><a href=\"#\" onclick=\"admin_close_directions();return false;\">Close Window</a></p>";
	$('#directions').html(directions);
	$('#directions').fadeIn('300');
}

function admin_close_directions() {
	$('#directions').fadeOut('300', function () {
		$('#directions').html('');
	});
}

function admin_close_error(error) {
	$('#error').fadeOut('300', function () {
		$('#error').html('');
	});
}

function showActions() {
	var position = $('#actions_right').offset();
	var button_width = $('#actions_right').width();
	var put_left = position.left - button_width + 14;
	var put_top = position.top + 28;
	$('#page_actions').css('left',put_left + 'px');
	$('#page_actions').css('top',put_top + 'px');
	$('#page_actions').toggle('fast');
}

function show(id) {
	$('#' + id).show();
}

function hide(id) {
	$('#' + id).hide();
}