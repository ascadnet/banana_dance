
/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Used for field set management on the admin CP.
	
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


		var active = 0;
		function addFieldSet(id) {
			if (active != '1') {
				shtml = "<li id=active_addition>";
				shtml += "<select name=\"field_set\" id=\"field_set_id\" style=\"width:250px;\" class=\"xtra_small\">";
				shtml += setlist;
				shtml += "</select> <a href=\"#\" onClick=\"addFieldSetFinal('" + id + "','" + user_id + "');return false;\"><img src=\"imgs/icon-save.png\" width16 height=16 border=0 alt=\"Add\" title=\"Add\" class=\"icon\" /></a><a href=\"#\" onClick=\"removeFieldSet();return false;\"><img src=\"imgs/icon-delete.png\" width16 height=16 border=0 alt=\"Remove\" title=\"Remove\" class=\"icon_nopad\" /></a>";
				shtml += "</li>";
				// $('#litab_new_put').show('fast');
				// $('#litab_new').html(html);
				$("#inner_page_tabs_top li:last").after(shtml);
				active = 1;
			}
		}
		
		function addFieldSetFinal(id,user_id) {
			send_data = "action=add&user=" + user_id + "&position=" + id + "&set=" + $('#field_set_id').val();
		    	$.post('functions/fieldsets.php', send_data, function(theResponse) {
				var returned = theResponse.split('+++');
				if (returned['0'] == "1") {
					// $('#place_new_here').html(returned['1']);
					$('#active_addition').remove();
					$("#inner_page_tabs_top li:last").after('<li id="litab' + returned['2'] + '" onMouseOver="showDel(\'' + returned['2'] + '\');" onMouseOut="hideDel(\'' + returned['2'] + '\');"><a href="#" onClick="swapTab(\'' + returned['2'] + '\');return false;">' + returned['3'] + '</a><a href="#" onClick="deleteFieldSet(\'10002\',\'' + returned['2'] + '\');return false;"><img src="imgs/icon-delete.png" width=16 height=16 border=0 title="Remove Tab" title="Remove Tab" id="delete_tab' + returned['2'] + '" style="display:none;" class="icon_l" /></a></li>');
					$("#place_new_here").before('<div id="tab' + returned['2'] + '" style=\"display:none;\">' + returned['1'] + '</div>');
					admin_close_error();
				} else {
					admin_error(returned['1']);
				}
		    	});
		}
		
		function swapTab(id) {
			$('#inner_page_tabs_top li').each(function(index) {
				thisid = $(this).attr('id');
				cutid = thisid.substr(5);
				$('#' + thisid).removeClass('on');
				if (cutid != 'new') {
					if (cutid == id) {
						$('#tab' + cutid).show();
						$(this).addClass('on');
					} else {
						$('#tab' + cutid).hide();
					}
				}
			});
		}
		
		function removeFieldSet(id) {
			active = 0;
			$('#active_addition').hide('fast');
			$('#active_addition').remove();
		}
		
		function deleteFieldSet(id,set_id) {
			send_data = "action=delete&position=" + id + "&set=" + set_id;
		    	$.post('functions/fieldsets.php', send_data, function(theResponse) {
				var returned = theResponse.split('+++');
				if (returned['0'] == "1") {
					$('#tab' + set_id).remove();
					$('#litab' + set_id).remove();
					active = 0;
					admin_close_error();
				} else {
					admin_error(returned['1']);
				}
		    	});
		}
		
		function showDel(set_id) {
			$('#delete_tab' + set_id).show();
		}
		
		function hideDel(set_id) {
			$('#delete_tab' + set_id).hide();
		}