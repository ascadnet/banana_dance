<?php


/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Various widget-based functions. Creates widgets.
	
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


class widget extends db {

	public $type;
	public $data;
	public $user;
	public $widget_id;
	public $error;
	public $updating;
	
	// Make the variables
	
	function __construct($type,$data) {
		// Set up the variables
		$this->type = $this->mysql_clean($type);
		$this->data = $data;
		
		// Updating or Creating?
		if (! empty($data['id'])) {
			$this->updating = '1';
		} else {
			$this->updating = '0';
		}
		
		// User
		global $user;
		$this->user = $user;
		
		// Category Page Index (1)
		if ($type == '1') {
			if (empty($data['name'])) { $this->data['name'] = 'Untitled Category Page Index'; }
			return $this->complete_add();
		}
		// Recent Comments to Page (2)
		// Recent Pages in Category (4)
		// Recent Users (6)
		// Tag Could (9)
		else if ($type == '2' || $type == '4' || $type == '6' || $type == '9' || $type == '19') {
			if (empty($this->data['name'])) {
				if ($type == '2') { $this->data['name'] = 'Untitled Recent Comments to Page'; }
				else if ($type == '4') { $this->data['name'] = 'Untitled Recent Pages in Category'; }
				else if ($type == '6') { $this->data['name'] = 'Untitled Recent Users'; }
				else if ($type == '9') { $this->data['name'] = 'Untitled Tag Cloud'; }
				else if ($type == '19') { $this->data['name'] = 'Untitled Activity Log'; }
			}
			return $this->complete_add();
		}
		// Page Date List (18)
		else if ($type == '18') {
			if (empty($this->data['name'])) {
				$this->data['name'] = 'Untitled Page Date List';
			}
			return $this->create_date_list();
		}
		// Custom HTML (3)
		// Video (12)
		// Map (13)
		// Calendar (14)
		// Spreadsheet (16)
		else if ($type == '3' || $type == '12' || $type == '13' || $type == '14' || $type == '16') {
			if (empty($this->data['name'])) {
				if ($type == '3') { $this->data['name'] = 'Untitled Custom HTML'; }
				else if ($type == '12') { $this->data['name'] = 'Untitled Video'; }
				else if ($type == '13') { $this->data['name'] = 'Untitled Map'; }
				else if ($type == '14') { $this->data['name'] = 'Untitled Calendar'; }
				else if ($type == '16') { $this->data['name'] = 'Untitled Spreadsheet'; }
			}
			return $this->create_custom_html();
		}
		// To Do List (11)
		else if ($type == '11') {
			if (empty($data['name'])) { $this->data['name'] = 'Untitled To Do List'; }
			return $this->create_todo_list();
		}
	}

	// ----------------------------------------
	//	String reply

	function __toString() {
		return (string) $this->widget_id;
	}
	
	
	// ----------------------------------------
	//	Complete the addition
	
	function complete_add() {

		$serialize = serialize($this->data['options']);
		
		if ($this->updating == '1') {
		
			$q = "
				UPDATE `" . TABLE_PREFIX . "widgets`
				SET `name`='" . $this->mysql_clean($this->data['name']) . "',`html`='" . $this->mysql_clean($this->data['html']) . "',`html_insert`='" . $this->mysql_clean($this->data['html_insert']) . "',`options`='" . $this->mysql_clean($serialize) . "'
				WHERE `id`='" . $this->mysql_clean($this->data['id']) . "'
				LIMIT 1
			";
			$update = $this->update($q);
		
			$final_id = $this->data['id'];
		} else {
			$q = "
				INSERT INTO `" . TABLE_PREFIX . "widgets` (`date`,`name`,`owner`,`type`,`html`,`html_insert`,`active`,`options`)
				VALUES ('" . $this->current_date() . "','" . $this->mysql_clean($this->data['name']) . "','" . $this->user . "','" . $this->type . "','" . $this->get_widget_html($this->type,$this->data['html']) . "','" . $this->get_widget_html_insert($this->type,$this->data['html_insert']) . "','1','" . $this->mysql_clean($serialize) . "')
			";
			$final_id = $this->insert($q);
		}
		
		$this->widget_id = "1+++" . $final_id;
		
	}

	// ----------------------------------------
	//	Create Axtivity Log
		
	function create_activity_log() {
		
   		// Range
   		if (! empty($this->data['options']['end_date']) && ! empty($this->data['options']['start_date'])) {
   			if ($this->data['options']['end_date'] <= $this->data['options']['start_date']) {
   				$this->error = '1';
   				$this->widget_id = "0+++End date must be after start date.";
   			}
   		}
   		else if (empty($this->data['options']['end_date']) && empty($this->data['options']['start_date'])) {
   			$this->error = '1';
   			$this->widget_id = "0+++Input a start date, end date, or both for a date range.";
   		}
   		else {
   			$this->error = '0';
   		}
   		
   		if ($this->error != '1') {
   			$this->complete_add();
   		}
	
	}
	
	// ----------------------------------------
	//	Create Date List
		
	function create_date_list() {
	
   		// Date integrity checks
   		if (! empty($this->data['options']['start_date'])) {
   			$sexp_date = explode('-',$this->data['options']['start_date']);
   			if ($sexp_date['1'] < 10) {
   				$sexp_date['1'] = 0 . $sexp_date['1'];
   				$this->data['options']['start_date'] = $sexp_date['0'] . '-' . $sexp_date['1'];
   			}
   		}
   		if (! empty($this->data['options']['end_date'])) {
   			$sexp_date = explode('-',$this->data['options']['end_date']);
   			if ($sexp_date['1'] < 10) {
   				$sexp_date['1'] = 0 . $sexp_date['1'];
   				$this->data['options']['end_date'] = $sexp_date['0'] . '-' . $sexp_date['1'];
   			}
   			
   		}
   		
   		// Range
   		if (! empty($this->data['options']['end_date']) && ! empty($this->data['options']['start_date'])) {
   			if ($this->data['options']['end_date'] <= $this->data['options']['start_date']) {
   				$this->error = '1';
   				$this->widget_id = "0+++End date must be after start date.";
   			}
   		}
   		else if (empty($this->data['options']['end_date']) && empty($this->data['options']['start_date'])) {
   			$this->error = '1';
   			$this->widget_id = "0+++Input a start date, end date, or both for a date range.";
   		}
   		else {
   			$this->error = '0';
   		}
   		
   		if ($this->error != '1') {
   			$this->complete_add();
   		}
	
	}

	// ----------------------------------------
	//	Create Custom HTML
		
	function create_custom_html() {
		
		// Video
		if ($this->type == '12') {
			$this->start_dom();
			$html = str_get_html($this->data['html']);
			
			// Need a name!
			if (empty($_POST['name'])) {
				$_POST['name'] = "Online Video";
			}
		
			if (! empty($_POST['width'])) {
				$_POST['width'] = rtrim($_POST['width'],'px');
				$html->find('iframe', 0)->width = $_POST['width'];
			}
			
			if (! empty($_POST['height'])) {
				$_POST['height'] = rtrim($_POST['height'],'px');
				$html->find('iframe', 0)->height = $_POST['height'];
			}
		
			$html = $html->find('iframe', 0);
			
		}
		
		// Map
		else if ($this->type == '13') {
			$this->start_dom();
			$html = str_get_html($this->data['html']);
			
			if (empty($_POST['width'])) {
				$_POST['width'] = '100%';
			} else {
				$_POST['width'] = rtrim($_POST['width'],'px');
			}
			if (empty($_POST['height'])) {
				$_POST['height'] = '600';
			} else {
				$_POST['height'] = rtrim($_POST['height'],'px');
			}
			
			if (strpos($html,'mapquest') !== false) {
			
				$style = '';
				if (strpos($_POST['width'],'%')) {
					$style .= "width:" . $_POST['width'] . ";";
				} else {
					$style .= "width:" . $_POST['width'] . "px;";
				}
				if (strpos($_POST['height'],'%')) {
					$style .= "height:" . $_POST['height'] . ";";
				} else {
					$style .= "height:" . $_POST['height'] . "px;";
				}
				$html->find('iframe', 0)->style = $style;
				
			} else {
				$html->find('iframe', 0)->height = $_POST['height'];
				$html->find('iframe', 0)->width = $_POST['width'];
			}
			
			$html = $html->find('iframe', 0);
			
		}
		
		// Calendar
		else if ($this->type == '14') {
			$this->start_dom();
			$html = str_get_html($this->data['html']);
			if (empty($_POST['width'])) {
				$_POST['width'] = '100%';
			} else {
				$_POST['width'] = rtrim($_POST['width'],'px');
			}
			if (empty($_POST['height'])) {
				$_POST['height'] = '600';
			} else {
				$_POST['height'] = rtrim($_POST['height'],'px');
			}
			$html->find('iframe', 0)->height = $_POST['height'];
			$html->find('iframe', 0)->width = $_POST['width'];
			// Add title
			$html->find('iframe', 0)->src = trim($html->find('iframe', 0)->src,'"') . "&title=" . $_POST['name'];
		}
		
		// Spreadsheet
		else if ($this->type == '16') {
			$html = "<iframe src=\"" . $_POST['html'] . "\" style=\"border: 0;\" width=\"100%\" height=\"600\" frameborder=\"0\" scrolling=\"no\"></iframe>";
		}
		
		// Custom HTML
		else {
			$html = $_POST['html'];
		}

		// Set to full HTML format
		$options = array(
			'format' => '1',
			'width' => $_POST['width'],
			'height' => $_POST['height'],
		);
		$serialize = serialize($options);

// <iframe src="https://www.google.com/calendar/embed?src=jonnyobell%40gmail.com&ctz=America/Chicago"&title=Test Calendar style="border: 0" width=100% height=100% frameborder="0" scrolling="no"></iframe>

		if ($this->updating == '1') {
			
			$q = "
				UPDATE `" . TABLE_PREFIX . "widgets`
				SET `name`='" . $this->mysql_clean($this->data['name']) . "',`html`='" . $this->mysql_clean($this->data['html']) . "',`options`='" . $this->mysql_clean($serialize) . "'
				WHERE `id`='" . $this->mysql_clean($this->data['id']) . "'
				LIMIT 1
			";
			$update = $this->update($q);
			$final_id = $this->data['id'];
			
		} else {
			$q = "
				INSERT INTO `" . TABLE_PREFIX . "widgets` (`date`,`name`,`owner`,`type`,`html`,`active`,`options`)
				VALUES ('" . $this->current_date() . "','" . $this->mysql_clean($this->data['name']) . "','" . $this->user . "','" . $this->type . "','" . $this->mysql_clean($html) . "','1','" . $this->mysql_clean($serialize) . "')
			";
			$final_id = $this->insert($q);
		}
		$this->widget_id = "1+++" . $final_id;
		
	}
	
	
	// ----------------------------------------
	//	Create 
		
	function create_page_index() {
	
	}
	
	// ----------------------------------------
	//	Create 
		
	function create_recent_pages() {
	
	}

	// ----------------------------------------
	//	Create 
		
	function create_recent_users() {
	
	}
	
	// ----------------------------------------
	//	Create 
		
	function create_most_commented() {
	
	}
	
	// ----------------------------------------
	//	Create 
		
	function create_tree_list() {
	
	}
	
	// ----------------------------------------
	//	Create 
		
	function create_todo_list() {
	
		$serialize = serialize($this->data['options']);
		if ($this->updating == '1') {
			
			$q = "
				UPDATE `" . TABLE_PREFIX . "widgets`
				SET `name`='" . $this->mysql_clean($this->data['name']) . "',`html`='" . $this->mysql_clean($this->data['html']) . "',`html_insert`='" . $this->mysql_clean($this->data['html_insert']) . "',`options`='" . $this->mysql_clean($serialize) . "'
				WHERE `id`='" . $this->mysql_clean($this->data['id']) . "'
				LIMIT 1
			";
			$update = $this->update($q);
			
			$insert_id = $this->data['id'];
		} else {
			$q = "
				INSERT INTO `" . TABLE_PREFIX . "widgets` (`date`,`name`,`owner`,`type`,`html`,`html_insert`,`active`,`options`)
				VALUES ('" . $this->current_date() . "','" . $this->mysql_clean($this->data['name']) . "','" . $this->user . "','" . $this->type . "','" . $this->get_widget_html($this->type,$this->data['html']) . "','" . $this->get_widget_html_insert($this->type,$this->data['html_insert']) . "','1','" . $this->mysql_clean($serialize) . "')
			";
			$insert_id = $this->insert($q);
		}
		
		if ($this->updating != '1') {
			$today = $this->current_date();
			$todo_insert = '';
			foreach ($this->data['items'] as $anItem) {
				if (! empty($anItem)) {
					$todo_insert .= ",('$insert_id','" . $this->mysql_clean($anItem) . "','0','$today')";
				}
			}
			$todo_insert = ltrim($todo_insert,',');
			if (! empty($todo_insert)) {
				$q1 = "INSERT INTO `" . TABLE_PREFIX . "widgets_todo` (`list_id`,`name`,`complete`,`date`) VALUES $todo_insert";
			}
			$insert = $this->insert($q1);
		}
		
		$this->widget_id = "1+++" . $insert_id;
		
	}
	
	// ----------------------------------------
	//	Create 
		
	function create_video() {
	
	}
	
	// ----------------------------------------
	//	Create 
		
	function create_map() {
	
	}
	
	// ----------------------------------------
	//	Create 
		
	function create_calendar() {
	
	}
	
	// ----------------------------------------
	//	Create 
		
	function create_poll() {
	
	}
	
	// ----------------------------------------
	//	Create 
		
	function create_tagged_pages_list() {
	
	}
	
	
	
	// ----------------------------------------
	//	Get standard widget HTML
	
	function get_widget_html($type,$html) {
		if (empty($html)) {
			// Tag Cloud
			if ($type == '9') {
				$html = "<ul class=\"bd_tags\">%entries%</ul>";
			}
			// Page Date List
			else if ($type == '18') {
				$html = "<ul class=\"bd_date_list\">%entries%</ul>";
			}
			else {
				$html = "<ul class=\"bd_widget_ul\">%entries%</ul>";
			}
		}
		return $html;
	}
	
	function get_widget_html_insert($type,$html_insert) {
		if (empty($html_insert)) {
			// Tag Cloud
			if ($type == '9') {
				$html_insert = "<li%style%>%tag%</li>";
			}
			// Comment
			else if ($type == '2') {
				$html_insert = "<li>\n<span class=\"bg_widget_list_title\">Posted to <a href=\"%article_link%\">%article_name%</a> by <a href=\"%comment_user_link%\">%comment_user%</a> on %comment_date%</span><br />\n<span class=\"bd_widget_list_sub\">%comment%</span>\n</li>";
			}
			// Recent Users
			else if ($type == '6') {
				$html_insert = "<li><span class=\"bg_widget_list_title\"><a href=\"/user/%username%\">%username%</a></span><span class=\"bd_widget_list_sub\">%joined%</span></li>";
			}
			// Page Date List
			else if ($type == '18') {
				$html_insert = "<li><a href=\"%article_link%\">%article_name%</a></li>";
			}
			else {
				$html_insert = "<li>\n<span class=\"bg_widget_list_title\"><a href=\"%article_link%\">%article_name%</a></span>\n<span class=\"bd_widget_list_sub\">%article_created% by <a href=\"/user/%username%\">%username%</a></span>\n</li>";
			}
		}
		return $html_insert;
	}

}


?>