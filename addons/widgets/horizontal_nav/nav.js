
/* ------------------------------------------------

	Banana Dance Plugin
	"Horizontal Navigation Bar"
	by Ascad Networks
	http://www.ascadnetworks.com/
	
	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.

------------------------------------------------ */

var active_menu;
var active_me;
var delay;

$(document).ready(function() {
	$('#hnav_primary li').hover(
		function() {
			theid = $(this).attr('id');
			theid = theid.substr(7);
			main_element = "hnav_m_" + theid;
			if ($('#' + main_element + ' li').length > 0) {
				hnav_show_category(theid);
			}
		},
		function() {
			theid = $(this).attr('id');
			theid = theid.substr(7);
			hnav_hide_category(theid);
		}
	);
});


function hnav_show_category(id) {
	// Main Nav Element
	main_element = "hnav_m_" + id;
	// Offset
	main_height = $('#hnav_primary').outerHeight();
	var offset_primary = $('#hnav_primary').position();
	var offset_li = $('#' + main_element).position();
	var offset_true = $('#' + main_element).offset();
	true_left = offset_true.left;
	mTop = offset_primary.top;
	mLeft = offset_li.left;
	// Adjust CSS
	div = "hnav_category" + id;
	// Make sure this isn't going
	// off the page.
	user_width = $(window).width();
	bubble_width = $('#' + main_element).outerWidth();
	total_right = true_left + bubble_width + 150;
	if (total_right > user_width) {
		add_offset = mLeft - bubble_width;
	} else {
		add_offset = mLeft;
	}
	
	$('#' + div).css('top',mTop+main_height);
	$('#' + div).css('left',add_offset);
	$('#' + div).fadeIn('5');
	// $('#' + main_element).addClass('on');
	active_menu = id;
	active_me = main_element;
}

function hnav_hide_category(id) {
	div = "hnav_category" + id;
	main_element = "hnav_m_" + id;
	$('#' + div).hide();
	// $('#' + main_element).removeClass('on');
}