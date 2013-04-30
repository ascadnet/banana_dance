
/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Page editor screen functions.
	
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


	var current_function = 'article_editing';
	var selected_text = '';
      	var js_widgets = functions_path + "/widgets.php";
	var editingWidget = '0';
   	var needToConfirm = false;
	
	// --------------------------------------------
	//	Detect changes
	
   	$(document).ready(function() {
   		window.onbeforeunload = askConfirm;
   		
   		$("select,checkbox,radio").change(function () {
   			triggerEdit();
   		});
   		
   		function askConfirm() {
   			if (needToConfirm === true) {
   				return "Leaving this page without saving your progress will result in lost work. Please confirm that you wish to leave.";
   			}   
   		};
   		
		// --------------------------------------------
		//	CTRL-S Saves a Form
		
		$.ctrl('S', function() { triggerNoEdit();saveArticle('0'); });
		$.ctrl('D', function() { triggerNoEdit();saveArticle('1'); });
		$.shift('V', function() { previewArticle(); });
		
		// --------------------------------------------
		//	Allow tabs in textareas
		
		$(function() {
		    $("textarea").tabby();
		});
		
   	});
   	
	
   	
	// --------------------------------------------
	//	Left side accordian
	
	function expandSection(divin) {
		total = 5
		current = 0;
		while (total > 0) {
			current++;
			div = "menu" + current;
			divimg = "menuimg" + current;
			if ($('#' + div).is(":visible")) {
				img = program_url + '/templates/html/_imgs/editor/tile_down.png';
				$('#' + div).slideUp();
				$('#' + divimg).attr('src',img);
			} else {
				if (current == divin) {
					img = program_url + '/templates/html/_imgs/editor/tile_up.png';
					$('#' + div).slideDown();
					$('#' + divimg).attr('src',img);
				}
			}
			total--;
		}
	}




/* ------------------------------- Widgets ------------------------------------- */


function showWidgets() {

	html = "<div id=\"bd_widgets_add\" style=\"background-color: #f1f1f1 !important;\">";
	html += "<ul id=\"widget_options\">";
	html += "<li id=\"widg_li_5\" onclick=\"return showExtendedWidget('5');\">Plugin</li>";
	html += "<li id=\"widg_li_1\" onclick=\"return showExtendedWidget('1');\">Category Page Index</li>";
	html += "<li id=\"widg_li_2\" onclick=\"return showExtendedWidget('2');\">Recent Comments to Page</li>";
	html += "<li id=\"widg_li_4\" onclick=\"return showExtendedWidget('4');\">Recent Pages in Category</li>";
	html += "<li id=\"widg_li_6\" onclick=\"return showExtendedWidget('6');\">Recent Users</li>";
	html += "<li id=\"widg_li_18\" onclick=\"return showExtendedWidget('18');\">Page Date List</li>";
	// html += "<li id=\"widg_li_8\" onclick=\"return showExtendedWidget('8');\">Most Commented Pages</li>";
	html += "<li id=\"widg_li_9\" onclick=\"return showExtendedWidget('9');\">Tag Cloud</li>";
	// html += "<li id=\"widg_li_17\" onclick=\"return showExtendedWidget('17');\">Tagged Pages</li>";
	// html += "<li id=\"widg_li_10\" onclick=\"return showExtendedWidget('10');\">Expandable Tree List</li>";
	html += "<li id=\"widg_li_11\" onclick=\"return showExtendedWidget('11');\">To Do List</li>";
	// html += "<li id=\"widg_li_15\" onclick=\"sreturn howExtendedWidget('15');\">Poll/Vote</li>";
	html += "<li id=\"widg_li_12\" onclick=\"return showExtendedWidget('12');\">Video</li>";
	html += "<li id=\"widg_li_13\" onclick=\"return showExtendedWidget('13');\">Map</li>";
	html += "<li id=\"widg_li_14\" onclick=\"return showExtendedWidget('14');\">Calendar</li>";
	html += "<li id=\"widg_li_16\" onclick=\"return showExtendedWidget('16');\">Spreadsheet</li>";
	html += "<li id=\"widg_li_3\" onclick=\"return showExtendedWidget('3');\">Custom HTML</li>";
	html += "<li id=\"widg_li_19\" onclick=\"return showExtendedWidget('19');\">Activity Feed</li>";
	html += "</ul>";
	html += "<div id=\"widget_right\">";
	html += "<ul id=\"widgets_top\">";
	html += "<li id=\"li_wid_existing\" onclick=\"return switch_wid('existing');\" class=\"on\">Existing</li>";
	html += "<li id=\"li_wid_new\" onclick=\"return switch_wid('new');\">New</li>";
	html += "<li id=\"li_wid_new\" onclick=\"return closeCaptcha();\" class=\"cancel\">Cancel</li>";
	html += "</ul>";
	html += "<div class=\"clear\"></div><div id=\"display_widget_details\"><div id=\"widg_left_pad\"><p><i>Select a widget to the left to continue...</i></p></div></div>";
	html += "<div class=\"clear\"></div>";
	html += "</div></div>";
	show_captcha(html);
	
}


function switch_wid(show,callback) {
      	$('#widgets_top li').each(function(i, li) {
      		var item = $(li);  
      		$(item).removeClass('on');
      	});
	var li_elem = 'li_wid_' + show;
	$('#' + li_elem).addClass('on');
	
	if (show == 'existing') {
		$('#show_widgets_existing').show();
		$('#show_widgets_new').hide();
	} else {
		$('#show_widgets_new').show();
		$('#show_widgets_existing').hide();
	}
}

function showExtendedWidget(type,switching,selected_value,callback) {
	
	// -------------------------------------------
	// Get existing widgets of this type
	
	old_current_function = current_function; // It is either adding or editing a page...
	current_function = 'widget';
	
	showLoading();
      	send_data = "action=get_widgets&type=" + type;
        $.post(js_widgets, send_data, function(inner) {
        
		// -------------------------------------------
		//  Begin output...
		
		final_output = "<div id=\"show_widgets_existing\" style=\"display:none;\">";
		final_output += inner;
		final_output += "</div><div id=\"show_widgets_new\">";
		
		// Create widget form
		final_output += '<form action="#" id="widget_add_form" onsubmit="return createWidget(\'' + type + '\');">';
		final_output += '<input type="hidden" name="id" id="id" value="" />';
		final_output += '<label class="sys">Widget Name</label><input type="text" class="sys_field" tabindex="1" name="name" style="width:300px;" />';
		
		// Category Page Index
		if (type == '1') {
			final_output += widget1();
			// Get category list...
			getCategoryList(selected_value);
		}
		
		// Recent Comments
		else if (type == '2') {
			final_output += widget2();
			// Get category list...
			//getPageList(selected_value);
		}
		
		// Custom HTML
		else if (type == '3') {
			final_output += widget3();
			// Get category list...
			getCategoryList(selected_value);
		}
		
		// Recent Pages
		else if (type == '4') {
			final_output += widget4();
			// Get category list...
			getCategoryList(selected_value);
		}
		
		// Recent Activity Feed
		else if (type == '19') {
			final_output += widget19();
			// Get category list...
			getCategoryList(selected_value);
		}
		
		// Plugin
		else if (type == '5') {
			final_output += '<p>Plugins cannot be added from this utility. Please use the admin control panel to manage Plugins.</p>';
		}
		
		// Recent Users
		else if (type == '6') {
			final_output += widget6();
			// Get category list...
			getUserTypeList(selected_value);
		}
		
		// Page Date List
		else if (type == '18') {
			final_output += widget18();
			// Get category list...
			getCategoryList(selected_value);
		}
		
		// Most Commented Pages
		else if (type == '8') {
			final_output += widget8();
		}
		
		// Tag Cloud
		else if (type == '9') {
			final_output += widget9();
			// Get category list...
			getCategoryList(selected_value);
		}
		
		// To-Do List
		else if (type == '11') {
			final_output += widget11();
		}
		
		// Video
		else if (type == '12') {
			final_output += widget12();
		}
		
		// Map
		else if (type == '13') {
			final_output += widget13();
		}
		
		// Calendar
		else if (type == '14') {
			// Directions
			final_output += widget14();
		}
		
		// Poll/Vote
		else if (type == '15') {
			final_output += '';
		}
		
		// Spreadsheet
		else if (type == '16') {
			final_output += widget16();
		}
		
		// Pages with a specific tag
		// For now, use %related_tags% caller tag.
		else if (type == '17') {
			final_output += '';
		}
		
		if (! editingWidget) {
     	 		final_output += '<br /><input type="submit" id="widg_submit" value="Create Widget" />';
		} else {
     	 		final_output += '<br /><input type="submit" id="widg_submit" value="Edit Widget" />';
     	 	}
		final_output += '</div></form>';
		
		// -------------------------------------------
		// Add "on" class to selected widget type
		
		$('#widget_options li').each(function(i, li) {
			var item = $(li);  
			$(item).removeClass('on');
		});
		$('#widg_li_' + type).addClass('on');
		
		// -------------------------------------------
		// Output the update information
		
		$('#widg_left_pad').html(final_output);
		
		if (switching == 'new') {
			switch_wid('new');
		} else {
			switch_wid('existing');
		}
		

			// Make the callback
		    if(typeof callback == 'function') {
		      callback.call(this);
		    }
		
        });
	closeLoading();
	
}

// -------------------------------------------

function widget1(data) {
      	// Directions
      	final_output = '<div id="widgets_instructions">';
      	final_output += '<h3 style="margin:0 0 12px 0;">Overview</h3>';
      	final_output += '<p>Creates a site map with correct indents for the selected category and its sub-categories.</p>';
      	final_output += '<h3 style="margin:0 0 12px 0;">Instructions</h3>';
      	final_output += '<ol class="widget_directions">';
      	final_output += '<li>Input a name for your widget and select your desired settings.</li>';
      	final_output += '</ol>';
      	final_output += '</div>';
      	// New information
      	final_output += '<label class="sys">Category</label>';
      	final_output += '<div id="putCategories"></div>';
      	final_output += '<p class="field_help">If you wish to pages in a specific category, select the category above. Sub-categories of that category will be included. Select "Base Category" for all pages and categories.</p>';
      	// New information
      	final_output += '<label class="sys">List Style</label>';
      	final_output += '<p><input type="radio" name="options[type]" value="standard" checked="checked" /> Indented List <input type="radio" name="options[type]" value="tree" /> Expandable Tree</p>';
      	final_output += '<p class="field_help">Leave blank for no limit. Otherwise input how many pages max to include in this list.</p>';
      	final_output += '<label class="sys">Limit Results</label>';
      	final_output += '<input type="text" class="sys_field" name="options[limit]" id="options[limit]" tabindex="3" style="width:50px;" value="" />';
      	final_output += '<p class="field_help">Leave blank for no limit. Otherwise input how many pages max to include in this list.</p>';
      	final_output += '<label class="sys">Columns</label>';
      	final_output += '<input type="text" class="sys_field" name="options[columns]" id="options[columns]" tabindex="4" style="width:50px;" value="1" />';
      	final_output += '<p class="field_help">How many columns should the site tree span?</p>';
      	final_output += '<label class="sys">Page Order</label>';
      	final_output += '<input type="radio" name="options[order]" value="order" /> Order in Category<br /><input type="radio" name="options[order]" value="name" /> Name<br /><input type="radio" name="options[order]" value="views" /> Views<br /><input type="radio" name="options[order]" value="created" /> Date Created<br /><input type="radio" name="options[last_updated]" value="order" /> Date Last Updated<br /><input type="radio" name="options[order]" value="score" />Score';
      	final_output += '<label class="sys">Order Direction</label>';
      	final_output += '<input type="radio" name="options[dir]" value="ASC" />Ascending Order (1-9, a-z)<br /><input type="radio" name="options[dir]" value="DESC" />Descending Order (9-1, z-a)';
      	return final_output;
}

function widget2(data) {
      	// Directions
      	final_output = '<div id="widgets_instructions">';
      	final_output += '<h3 style="margin:0 0 12px 0;">Overview</h3>';
      	final_output += '<p>Creates a list of recent comments, either in general or to a specific page.</p>';
      	final_output += '<h3 style="margin:0 0 12px 0;">Instructions</h3>';
      	final_output += '<ol class="widget_directions">';
      	final_output += '<li>Input a name for your widget and select your desired settings.</li>';
      	final_output += '<li>To display all recent comments, leave the page field blank.</li>';
      	final_output += '</ol>';
      	final_output += '</div>';
      	// New information
      	final_output += '<label class="sys">Page</label>';
      	final_output += '<input type="text" class="sys_field" name="page" id="page" tabindex="2" style="width:300px;" onkeyup="suggest(\'bd_articles\',this.value,\'name\',\'page\',\'id\',\'name\');" /><input type="hidden" id="putPageID" name="options[page]" value="" />';
      	final_output += '<p class="field_help">If you wish to limit comments to a specific page, input the page title above and select it from the options.</p>';
      	// New information
      	final_output += '<label class="sys">Limit Results</label>';
      	final_output += '<input type="text" class="sys_field" name="options[limit]" id="options[limit]" tabindex="3" style="width:50px;" value="" />';
      	final_output += '<p class="field_help">Leave blank for no limit. Otherwise input how many pages max to include in this list.</p>';
      	final_output += '<label class="sys">Short Long Comment</label>';
      	final_output += '<input type="text" class="sys_field" name="options[trim]" id="options[trim]" tabindex="4" style="width:50px;" value="" />';
      	final_output += '<p class="field_help">If you would like to limit the characters displayed for each comment, input the number of characters above. Leave blank to display the entire comment.</p>';
      	final_output += '<label class="sys">Page Order</label>';
      	final_output += '<input type="radio" name="options[order]" value="date" /> Date Posted<br /><input type="radio" name="options[order]" value="score" /> Score';
      	final_output += '<label class="sys">Order Direction</label>';
      	final_output += '<input type="radio" name="options[dir]" value="ASC" />Ascending Order (1-9, a-z)<br /><input type="radio" name="options[dir]" value="DESC" />Descending Order (9-1, z-a)';
      	return final_output;
}

function widget3(data) {
      	// Directions
      	final_output = '<div id="widgets_instructions">';
      	final_output += '<h3 style="margin:0 0 12px 0;">Overview</h3>';
      	final_output += '<p>Creates a snippet of HTML or wiki-syntax code.</p>';
      	final_output += '<h3 style="margin:0 0 12px 0;">Instructions</h3>';
      	final_output += '<ol class="widget_directions">';
      	final_output += '<li>Input a name for your widget and select your desired settings.</li>';
      	final_output += '</ol>';
      	final_output += '</div>';
      	// New information
      	final_output += '<label class="sys">Format</label>';
      	final_output += '<p><input type="radio" name="options[format]" id="options[format]" value="0" /> Wiki Syntax <input type="radio" name="options[format]" id="options[format]" value="1" checked=\"checked\" /> Full HTML</p>';
      	final_output += '<p class="field_help">Select the format of the content, either full HTML or wiki-syntax.</p>';
      	final_output += '<label class="sys">Code Snippet</label>';
      	final_output += '<textarea name="html" id="widget_html" tabindex="3" style="width:400px;height:200px;"></textarea><br />';
      	final_output += '<br /><input type="submit" value="Create Widget" />';
      	return final_output;
}

function widget4(data) {
      	// Directions
      	final_output = '<div id="widgets_instructions">';
      	final_output += '<h3 style="margin:0 0 12px 0;">Overview</h3>';
      	final_output += '<p>Creates a list of pages in a specific category.</p>';
      	final_output += '<h3 style="margin:0 0 12px 0;">Instructions</h3>';
      	final_output += '<ol class="widget_directions">';
      	final_output += '<li>Input a name for your widget and select your desired settings.</li>';
      	final_output += '</ol>';
      	final_output += '</div>';
      	// New information
      	final_output += '<label class="sys">Category</label>';
      	final_output += '<div id="putCategories"></div>';
      	final_output += '<p class="field_help">If you wish to limit pages in a specific category, select the category above. Sub-categories of that category will be included. Select "Base Category" for all pages and categories.</p>';
      	// New information
      	final_output += '<label class="sys">Limit Results</label>';
      	final_output += '<input type="text" class="sys_field" name="options[limit]" id="options[limit]" tabindex="3" style="width:50px;" value="" />';
      	final_output += '<p class="field_help">Leave blank for no limit. Otherwise input how many pages max to include in this list.</p>';
      	final_output += '<label class="sys">Page Order</label>';
      	final_output += '<input type="radio" name="options[order]" value="order" /> Order In Category<br /><input type="radio" name="options[order]" value="name" /> Name<br /><input type="radio" name="options[order]" value="views" /> Views<br /><input type="radio" name="options[order]" value="created" /> Date Created<br /><input type="radio" name="options[order]" value="last_updated" /> Last Updated<br /><input type="radio" name="options[order]" value="score" /> Score';
      	final_output += '<label class="sys">Order Direction</label>';
      	final_output += '<input type="radio" name="options[dir]" value="ASC" />Ascending Order (1-9, a-z)<br /><input type="radio" name="options[dir]" value="DESC" />Descending Order (9-1, z-a)<br />';
      	final_output += '<label class="sys">HTML</label>';
      	final_output += '<textarea name="html" id="html" tabindex="6" style="width:400px;height:110px;"></textarea><br />';
	final_output += '<label class="sys">Page Entry</label>';
      	final_output += '<textarea name="html_insert" id="html_insert" tabindex="7" style="width:400px;height:110px;"></textarea><br />';
      	return final_output;
}

function widget19(data) {
      	// Directions
      	final_output = '<div id="widgets_instructions">';
      	final_output += '<h3 style="margin:0 0 12px 0;">Overview</h3>';
      	final_output += '<p>Creates an activity feed for your website or a category of your website.</p>';
      	final_output += '<h3 style="margin:0 0 12px 0;">Instructions</h3>';
      	final_output += '<ol class="widget_directions">';
      	final_output += '<li>Input a name for your widget and select your desired settings.</li>';
      	final_output += '</ol>';
      	final_output += '</div>';
      	// New information
      	final_output += '<label class="sys">Category</label>';
      	final_output += '<div id="putCategories"></div>';
      	final_output += '<p class="field_help">If you wish to limit pages in a specific category, select the category above. Sub-categories of that category will be included. Select "Base Category" for all pages and categories.</p>';
      	// New information
      	final_output += '<label class="sys">Limit Results</label>';
      	final_output += '<input type="text" class="sys_field" name="options[limit]" id="options[limit]" tabindex="3" style="width:50px;" value="" />';
      	final_output += '<p class="field_help">Leave blank for no limit. Otherwise input how much activity to display.</p>';
      	final_output += '<label class="sys">Display</label>';
      	final_output += '<input type="checkbox" name="options[newpages]" value="1" /> New Pages<br /><input type="checkbox" name="options[editpages]" value="1" /> Page Edits<br /><input type="checkbox" name="options[badges]" value="1" /> Badges Awards<br /><input type="checkbox" name="options[newuser]" value="1" /> New User<br /><input type="checkbox" name="options[comment]" value="1" /> Comment Posted<br /><input type="checkbox" name="options[mentions]" value="1" /> User Mentions<br />';
      	return final_output;
}

function widget6(data) {
      	// Directions
      	final_output = '<div id="widgets_instructions">';
      	final_output += '<h3 style="margin:0 0 12px 0;">Overview</h3>';
      	final_output += '<p>Creates a list of recent users.</p>';
      	final_output += '<h3 style="margin:0 0 12px 0;">Instructions</h3>';
      	final_output += '<ol class="widget_directions">';
      	final_output += '<li>Input a name for your widget and select your desired settings.</li>';
      	final_output += '</ol>';
      	final_output += '</div>';
      	// New information
      	final_output += '<label class="sys">User Types</label>';
      	final_output += '<div id="putUserTypes"></div>';
      	final_output += '<p class="field_help">If you wish to only display a specific type of user, select the user type above. Leave blank to include all user types in the widget.</p>';
      	// New information
      	final_output += '<label class="sys">Limit Results</label>';
      	final_output += '<input type="text" class="sys_field" name="options[limit]" id="options[limit]" tabindex="3" style="width:50px;" value="" />';
      	final_output += '<p class="field_help">Leave blank for no limit. Otherwise input how many users to include on this list.</p>';
      	final_output += '<label class="sys">Page Order</label>';
      	final_output += '<input type="radio" name="options[order]" value="joined" /> Date Joined<br /><input type="radio" name="options[order]" value="username" /> Username<br /><input type="radio" name="options[order]" value="score" /> Comment Score<br /><input type="radio" name="options[order]" value="name" /> Name';
      	final_output += '<label class="sys">Order Direction</label>';
      	final_output += '<input type="radio" name="options[dir]" value="ASC" />Ascending Order (1-9, a-z)<br /><input type="radio" name="options[dir]" value="DESC" />Descending Order (9-1, z-a)';
      	return final_output;
}

function widget9(data) {
      	// Directions
      	final_output = '<div id="widgets_instructions">';
      	final_output += '<h3 style="margin:0 0 12px 0;">Overview</h3>';
      	final_output += '<p>A tag cloud is a visualization of the #hashtags you have used to tag your pages, with the most used tags being emphasized over lesser-used tags.</p>';
      	final_output += '<h3 style="margin:0 0 12px 0;">Instructions</h3>';
      	final_output += '<ol class="widget_directions">';
      	final_output += '<li>Input a name for your tag cloud and select your desired settings.</li>';
      	final_output += '</ol>';
      	final_output += '</div>';
      	// New information
      	final_output += '<label class="sys">Category</label>';
      	final_output += '<div id="putCategories"></div>';
      	final_output += '<p class="field_help">If you wish to limit tags to pages in a specific category, select the category above. Sub-categories of that category will be included. Select "Base Category" for all pages.</p>';
      	// New information
      	final_output += '<label class="sys">Max Tags</label>';
      	final_output += '<input type="text" class="sys_field" name="options[max_tags]" id="options[max_tags]" tabindex="2" style="width:75px;" value="" />';
      	final_output += '<p class="field_help">Limit the tag cloud to a specific number of tags. Leave blank to not limit tags in the tag cloud.</p>';
      	return final_output;
}

function widget11(data) {
      	// Directions
      	final_output = '<div id="widgets_instructions">';
      	final_output += '<h3 style="margin:0 0 12px 0;">Overview</h3>';
      	final_output += '<p>Allows you to create a to do list that can be checked off as a project progresses.</p>';
      	final_output += '<h3 style="margin:0 0 12px 0;">Instructions</h3>';
      	final_output += '<ol class="widget_directions">';
      	final_output += '<li>Input a name for your list.</li>';
      	final_output += '<li>Add items to your list.</li>';
      	final_output += '</ol>';
      	final_output += '</div>';
      	// New information
      	final_output += '<label class="sys">Privacy</label>';
      	final_output += '<p><input type="radio" name="options[privacy]" value="private" checked="checked" /> Private <input type="radio" name="options[privacy]" value="public" /> Public</p>';
      	final_output += '<p class="field_help">If set to "Private", only you will be able to mark items as completed and add new items to the list. Otherwise anyone can alter the list.</p>';
      	// New information
      	if (! editingWidget) {
	      	final_output += '<label class="sys">Options</label>';
	      	final_output += '<ol style="margin: 0;" id="todo_options"><li><input type="text" class="sys_field" name="items[]" value="" maxlength="255" style="width:300px;" /></li><li><a href="#" onclick="newItem();return false;">Add another item [+]</a></li></ol>';
      	} else {
	      	final_output += '<label class="sys">Options</label>';
	      	final_output += '<ul style="margin: 0;" id="todo_options"><li>Alter list items from the page the widget is found on.</li></ul>';    	
      	}
      	return final_output;
}

function widget12(data) {
      	// Directions
      	final_output = '<div id="widgets_instructions">';
      	final_output += '<h3 style="margin:0 0 12px 0;">Instructions</h3>';
      	final_output += '<ol class="widget_directions">';
      	final_output += '<li>Go to the video sharing website on which your video is located, whether it be <a href="http://www.youtube.com/" target="_blank">Youtube</a>, <a href="http://www.vimeo.com/" target="_blank">Vimeo</a>, <a href="http://www.dailymotion.com/" target="_blank">Daily Motion</a>, or any other.</li>';
      	final_output += '<li>Find your video on the website.</li>';
      	final_output += '<li>Click on "Embed" or "Share"</li>';
      	final_output += '<li>Copy and Paste the embed code into the "Video Embed" box, or simply copy and paste the video URL into the Video Embed box.</li>';
      	final_output += '</ol>';
      	final_output += '</div>';
      	// New information
      	final_output += '<label class="sys">Dimensions</label>';
      	final_output += '<input type="text" class="sys_field" name="width" tabindex="2" style="width:75px;" value="" /> x <input type="text" class="sys_field" name="height" style="width:75px;" tabindex="3" value="" />';
      	final_output += '<p class="field_help">Width x Height. Enter in pixels "px" or percent "%" (example "600px" or "80%").<br />Leave blank to allow the system to auto-calculate the best dimensions.</p>';
      	final_output += '<label class="sys">Video Embed</label>';
      	final_output += '<textarea name="html" id="widget_html" tabindex="4" style="width:400px;height:110px;"></textarea><br />';
      	return final_output;
}

function widget13(data) {
      	// Directions
      	final_output = '<div id="widgets_instructions">';
      	final_output += '<h3 style="margin:0 0 12px 0;">Instructions</h3>';
      	final_output += '<ol class="widget_directions">';
      	final_output += '<li>Go to <a href="http://maps.google.com/" target="_blank">Google Maps</a>, <a href="http://www.bing.com/maps/" target="_blank">Bing Maps</a>, <a href="http://www.mapquest.com/" target="_blank">MapQuest</a>, or your favorite mapping website.</li>';
      	final_output += '<li>On your favorite mapping website, create your map, whether it be a single point or directions.</li>';
      	final_output += '<li>Click on "Embed" or "Share"</li>';
      	final_output += '<li>Copy and Paste the website embedding code into the "Video Embed" field above.';
      	final_output += '<ul class="standard_list">';
      	final_output += '<li>Google Maps: Click the "Link" icon and copy the "Paste HTML to embed in website" code.</li>';
      	final_output += '<li>Bing Maps: Click the "Share" link and copy the "EMBED IN A WEBPAGE" code.</li>';
      	final_output += '<li>MapQuest: Click "Link/Embed" and and copy the "Embed in a Web Page" code.</li>';
      	final_output += '<ul>';
      	final_output += '</li>';
      	final_output += '</ol>';
      	final_output += '</div>';
      	// New information
      	final_output += '<label class="sys">Dimensions</label>';
      	final_output += '<input type="text" class="sys_field" name="width" style="width:75px;" tabindex="2" value="" /> x <input type="text" class="sys_field" name="height" style="width:75px;" tabindex="3" value="" />';
      	final_output += '<p class="field_help">Width x Height. Enter in pixels "px" or percent "%" (example "600px" or "80%").<br />Leave blank to allow the system to auto-calculate the best dimensions.</p>';
      	final_output += '<label class="sys">Map Embed</label>';
      	final_output += '<textarea name="html" id="widget_html" tabindex="4" style="width:400px;height:110px;"></textarea><br />';
      	return final_output;
}

function widget14(data) {
	final_output = '<div id="widgets_instructions">';
      	final_output += '<h3 style="margin:0 0 12px 0;">Instructions</h3>';
      	final_output += '<ol class="widget_directions">';
      	final_output += '<li>Log into your <a href="https://www.google.com/calendar" target="_blank">Google Calendar</a> account.</li>';
      	final_output += '<li>From "My Calendars", click on the arrow to the right of your calendar and then "Calendar Settings"</li>';
      	final_output += '<li>Copy the iFrame code listed under "Embed This Calendar"</li>';
      	final_output += '<li>Paste the calendar code into "Calendar Embed" field above.</li>';
      	final_output += '</ol>';
      	final_output += '</div>';
      	// New information
      	final_output += '<label class="sys">Dimensions</label>';
      	final_output += '<input type="text" class="sys_field" name="width" tabindex="2" style="width:75px;" value="" /> x <input type="text" class="sys_field" name="height" tabindex="3" style="width:75px;" value="" />';
      	final_output += '<p class="field_help">Width x Height. Enter in pixels "px" or percent "%" (example "600px" or "80%").<br />Leave blank to allow the system to auto-calculate the best dimensions.<br />Note that the program will overwrite your calendar\'s default settings.</p>';
      	final_output += '<label class="sys">Calendar Embed</label>';
      	final_output += '<textarea name="html" tabindex="4" id="widget_html" style="width:400px;height:110px;"></textarea><br />';
      	return final_output;
}

function widget16(data) {
    	// Directions
    	final_output = '<div id="widgets_instructions">';
    	final_output += '<h3 style="margin:0 0 12px 0;">Instructions</h3>';
    	final_output += '<ol class="widget_directions">';
    	final_output += '<li>Go to <a href="https://docs.google.com/" target="_blank">Google Docs</a> and load your spreadsheet.</li>';
    	final_output += '<li>Click of "File" > "Publish to the Web" > "Start Publishing"</li>';
    	final_output += '<li>Copy and Paste the link in the "Get a link to the published data" section into the "Spreadsheet Link" field.';
    	final_output += '</li>';
    	final_output += '</ol>';
    	final_output += '</div>';
    	// New information
    	final_output += '<label class="sys">Spreadsheet Link</label>';
    	final_output += '<input type="text" class="sys_field" name="html" tabindex="2" id="widget_html" style="width:400px;" /><br />';
    	return final_output;
}

function widget18(data) {
      	// Directions
      	final_output = '<link href="' + program_url + '/templates/html/_css/date_picker.css" rel="stylesheet" type="text/css" />';
      	final_output += '<script src="' + program_url + '/js/date_picker.js"></script>';
      	final_output += '<script type="text/javascript">$("#start_date").glDatePicker({cssName: "android",onChange: function(target, newDate){ target.val (newDate.getFullYear() + "-" + (newDate.getMonth() + 1)); }}); $("#end_date").glDatePicker({cssName: "android",onChange: function(target, newDate){ target.val (newDate.getFullYear() + "-" + (newDate.getMonth() + 1)); }});</script>';
      	final_output += '<div id="widgets_instructions">';
      	final_output += '<h3 style="margin:0 0 12px 0;">Overview</h3>';
      	final_output += '<p>Creates a list pages organized by month published.</p>';
      	final_output += '<h3 style="margin:0 0 12px 0;">Instructions</h3>';
      	final_output += '<ol class="widget_directions">';
      	final_output += '<li>Input a name for your widget and select your desired settings.</li>';
      	final_output += '</ol>';
      	final_output += '</div>';
      	// New information
      	final_output += '<label class="sys">Category</label>';
      	final_output += '<div id="putCategories"></div>';
      	final_output += '<p class="field_help">If you wish to limit pages in a specific category, select the category above. Sub-categories of that category will be included. Select "Base Category" for all pages and categories.</p>';
      	// New information
      	final_output += '<label class="sys">Limit Results</label>';
      	final_output += '<input type="text" class="sys_field" name="options[limit]" id="options[limit]" tabindex="3" style="width:50px;" value="" />';
      	final_output += '<p class="field_help">Leave blank for no limit. Otherwise input how many pages to display for each month in the list.</p>';
      	// New information
      	final_output += '<label class="sys">Published Date</label>';
      	final_output += '<p>Start Month: <input type="text" class="sys_field" name="options[start_date]" id="start_date" tabindex="4 style="width:125px;" value="" /> End Month: <input type="text" class="sys_field" name="options[end_date]" id="end_date" tabindex="5 style="width:125px;" value="" /></p>';
      	final_output += '<p class="field_help">Format = YYYY-MM-DD. Leave blank to include all pages since your website was created. <b>Published After:</b> Input a date into the first field but leave the second field blank. <b>Published Before:</b> Input a date into the second field but leave the first field blank. <b>Published Between:</b> Input a date into both fields.</p>';
      	final_output += '<label class="sys">Page Order</label>';
      	final_output += '<input type="radio" name="options[order]" value="order" /> Order In Category<br /><input type="radio" name="options[order]" value="name" /> Name<br /><input type="radio" name="options[order]" value="views" /> Views<br /><input type="radio" name="options[order]" value="created" /> Date Created<br /><input type="radio" name="options[order]" value="last_updated" /> Date Last Updated<br /><input type="radio" name="options[order]" value="score" /> Score<br />';
      	final_output += '<label class="sys">Order Direction</label>';
      	final_output += '<input type="radio" name="options[dir]" value="ASC" />Ascending Order (1-9, a-z)<br /><input type="radio" name="options[dir]" value="DESC" />Descending Order (9-1, z-a)';
    	return final_output;
}

// -------------------------------------------

function createWidget(type) {
	showLoading();
      	send_data = "action=create_widget&type=" + type + "&" + $('form#widget_add_form').serialize();
        $.post(js_widgets, send_data, function(theResponse) {
    		var returned = theResponse.split('+++');
    		if (returned['0'] == '1') {
    			if (editingWidget == '1') {
    				showSaved();
    			} else {
    				add_widget(returned['1']);
    			}
		} else {
			process_error(theResponse);
		}
        });
	closeLoading();
	return false;
}


function editWidget(id,type) {

	editingWidget = '1';
	switch_wid('new');

      	send_data = "action=get_widget&id=" + id;
	$.getJSON(js_widgets, send_data, function(data) {
	
	      	showExtendedWidget(type,'new',data.category,function(){
	      	
			$('#widget_add_form input[name=name]').val(data.name);
			$("#widget_category").val(data.category);
			
			$.each(data, function(key, value) {
				if (isNaN(key)) {
					if (key == 'opts') {
						$.each(value, function(keyA, valueA) {
							field_type = $('input[name="options[' + keyA + ']"]').attr('type');
							if (field_type == 'radio') {
								$("#widget_add_form input[name='options[" + keyA + "]']").removeAttr("checked");
								$("#widget_add_form input[name='options[" + keyA + "]']").filter("[value=" + valueA + "]").attr("checked","checked");
								
							} else {
								if (keyA == 'category') { }
								else if (keyA == 'page') {
									getPageName(valueA);
								}
								else if (keyA == 'width') {
									$("#widget_add_form input[name=width]").val(valueA);
								}
								else if (keyA == 'height') {
									$("#widget_add_form input[name=height]").val(valueA);
								}
								else {
									// alert(field_type + '---' + keyA + ': ' + valueA);
									$("#widget_add_form input[name='options[" + keyA + "]']").val(valueA);
								}
							}
						});				
					} else {
						if (value) {
							$('#widget_add_form input[name=' + key + ']').val(value);
							$('#widget_add_form textarea[name=' + key + ']').val(value);
							$('#widget_add_form select[name=' + key + ']').val(value);
						}
					}
				}
			});
	      	
	      	
	      	});

	});

      	return false;
		
			// data.name
			// data.category
			// data.html
			// data.html_insert
			// data.active
			// data.opts
			
		/*
		$.each(data, function(i, item){
			// $("#" + item.field).val(item.value);
			alert(item.name + '--' + item.category);
		});
		*/
}


function add_widget(widget_id_here) {
	sendtag = '{-' + widget_id_here + '-}';
	addCaller('content','' + sendtag + '','0');
	current_function = old_current_function;
	closeCaptcha();
	closeLoading();
}


function getCategoryList(selected_value) {
      	send_data = "action=get_category_list&selected=" + selected_value;
	$.post(js_widgets, send_data, function(data) {
		$('#putCategories').html(data);
	});
};

function getPageList(selected_value) {
      	send_data = "action=get_page_list&selected=" + selected_value
	$.post(js_widgets, send_data, function(data) {
		$('#putPages').html(data);
	});
}

function getUserTypeList(selected_value) {
      	send_data = "action=get_usertype_list&selected=" + selected_value
	$.post(js_widgets, send_data, function(data) {
		$('#putUserTypes').html(data);
	});
}

function getPageName(id) {
      	send_data = "action=get_page_name&id=" + id;
	$.post(js_widgets, send_data, function(data) {
		$('#widget_add_form input[name=page]').val(data);
	});
}

function newItem() {
	html_put = '<li><input type="text" name="items[]" value="" style="width:300px;" maxlength="255" /></li>';
	// $('#todo_options').append(html_put);
	$("#todo_options li:last-child").before(html_put);
}