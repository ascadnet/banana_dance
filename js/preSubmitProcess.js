
/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Confirms form submission data.
	
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


// ------------------------------------------
//	Pre-submission confirmation
//	of required fields on a form.
//	Required fields just need the
//	class "bd_required" on them
//	for this to work.
//	Only for traditionally
//	submitted forms as opposed to
//	ajax submitted forms.

function preConfirmForm(formname) {
	var error = '0';
	if (! formname) {
		formname = 'edit_form';
	}
	$(".bd_required").each(function() {
		if ($.trim($(this).val()).length == 0) {
			$(this).addClass("bd_error_field");
			error = '1';
		} else {
			$(this).removeClass("bd_error_field");
			error = '0';
		}
	});
	if (error == '1') {
		return false;
	} else {
		$('#' + formname).submit();
		return true;
	}
}