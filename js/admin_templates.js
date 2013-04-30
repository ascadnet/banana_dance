
/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Used for project management on the admin CP.
	
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
		var active_project = '';
		var active_template = '';
		
		function addTemplate(id) {
			if (active != '1') {
				html = "<select onchange=\"addTemplateFinal(null,null,'1');return false;\" name=\"template\" id=\"template_set_id\" style=\"width:250px;\" class=\"xtra_small\"><option value=\"\"></option>";
				html += template_list;
				html += "</select> <a href=\"#\" onClick=\"removeTemplate();return false;\"><img src=\"imgs/icon-delete.png\" width16 height=16 border=0 alt=\"Remove\" title=\"Remove\" class=\"icon_nopad\" /></a>";
			
				// $('#litab_new_put').toggle('fast');
				$("#inner_page_tabs li:last").prev().after('<li id="active_addition">' + html + '</li>');
				active = 1;
			}
		}
		
		function addTemplateFinal(force_id,force_name,check) {
			var nogo = '0';
			
			if (force_id == null) {
				id = $('#template_set_id').val();
			} else {
				id = force_id;
			}
			
			if (force_name == null) {
				textT = $("#template_set_id option:selected").text();
				if (textT) {
	      				removeTemplate();
	      				swapTemplate(id);
	      			} else {
	      				nogo = '1';
	      			}
			} else {
				textT = force_name;
			}
			
			if (nogo != '1') {
	      			active_edits.push(id);
	      			
				$("#inner_page_tabs li:last").prev().after('<li class="template_tab" id="litab' + id + '" onMouseOver="showDel(\'' + id + '\');" onMouseOut="hideDel(\'' + id + '\');"><a href="#" onClick="swapTemplate(\'' + id + '\');return false;">' + textT + '</a><a href="#" onClick="removeTemplateFromView(\'' + id + '\');return false;"><img src="imgs/icon-delete.png" width=16 height=16 border=0 title="Remove Tab" title="Remove Tab" id="delete_tab' + id + '" style="display:none;" class="icon_l" /></a></li>');
			}
			
			if (check == '1' && active_project) {
				saveProject();
			}
		}
		
		function swapTemplate(id,skip) {

			if (! skip) {
				if ($('#content').val().length > 0) {
					editID('');
				}
			}

			send_data = "action=add&id=" + id;
			
		    	$.post('functions/template_editor.php', send_data, function(theResponse) {
		    	
		    		check = jQuery.parseJSON(theResponse);
				removeTemplate();
				
				$('#id').val(check.id);
				$('#title').val(check.title);
				$('#desc').val(check.desc);
				$('#template').val(check.template);
				$('#type').val(check.type);
				$('textarea#content').val(check.content);
				$('#path').val(check.path);
				$('#filename').val(check.filename);
				
				$('#custom_header').val(check.custom_header);
				$('#custom_footer').val(check.custom_footer);
				
				$('#filename_show').html(check.filename);
				
				$('#inner_page_tabs li').removeClass('on');
				tab = 'litab' + id;
				$('#' + tab).addClass('on');
				
		    	});
		    	
		    	active_template = id;
		    	
		}
		
		function saveProject() {
			send_data = "action=save_project&templates=" + active_edits + "&active_project=" + active_project;
		    	$.post('functions/template_editor.php', send_data, function(theResponse) {
		    		active_project = theResponse;
		    		showSaved();
		    	});
		}
		
		function loadProject(id) {
			
			active_edits.length = 0;
			
			$('#inner_page_tabs li.template_tab').remove();
			
			var current = 0;
			send_data = "action=load_project&id=" + id;
		    	$.post('functions/template_editor.php', send_data, function(theResponse) {
				var returned = theResponse.split(',');
				$.each(returned, function(index, value) {
					var splitIt = value.split('+++');
					current++;
					if (current == 1) {
		    				swapTemplate(splitIt['0'],'1');
					}
					// active_edits.push(splitIt['0']);
					addTemplateFinal(splitIt['0'],splitIt['1']);
				});
		    	});
		    	
		    	active_project = id;
		    	
		    	// $('#headfoot').remove();
		    	// alert(active_edits);
		    	
		}
		
		function removeTemplate(id) {
			active = 0;
			$('#active_addition').hide('fast');
			$('#active_addition').remove();
			saveProject();
		}
		
		function showDel(id) {
			$('#delete_tab' + id).show();
		}
		
		function hideDel(id) {
			$('#delete_tab' + id).hide();
		}
		
		function removeTemplateFromView(id) {
			tab = 'litab' + id;
			$('#' + tab).hide();
			swapTemplate(initial_edit);
		}