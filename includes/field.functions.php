<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Field functions.
	
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


//	Generates fields for a form
//	FORM BASICS
//	- Fieldsets + columns in set
//	- Redirect or custom confirmation page text.
//	- CAPTCHA option
//	- Maximum submissions
//	- Date range when form is valid
//	- Max one submission per IP
//	- Send e-mail confirmation
//	- Label placement for fields (top, left, bottom)
//	FIELD BASICS:
//	Types of fields:
//	Basic fields:
//	- text, textarea
//		- Limit type of text: numbers only, letters only, numbers and letters only
//	- select, multi-select, radio
//	- checkbox, multi-checkbox
//	- hidden
//	Special fields:
//	- date (With "date select" image includes) / Option to add time.
//	- random ID
//	- website url (confirms formatting)
//	- email (confirms formatting)
//	- Likert
//		- 			| Strongly Disagree | Disagree | Agree | Strongly Agree
//		- Statement 1	| (radio)			|(radio)	 | (radio) | (radio)
//		- Statement 2		%				%		%		%
//	- File upload
//		- Limit file types & file size.
//	Formatting:
//	- Section Break (title + description)
//	- Page Break
//	Defaults/Presets:
//	- address (street 1, street 2, city, state, zip, country)
//	- phone (###)-(###)-(####) or International (##########) <- standard text box
//	- 
//
//	ALL FIELD OPTIONS:
//	Name / Display Name
//	Description
//	Required
//	Pre-defined value
//	Custom Class Names
//	Width (height in some cases)
//
//	CONDITIONAL LOGIN:
//	- If field "x" = "y", Do something: (display another field, update a field, etc.)


class fields extends db {

	// -------------------------------------------------
	//	Get field sets belonging to this
	//	location. Locations are:
	//	1-9999: Form ID
	//	10001: User Update Screen
	//	10002: Admin Update Screen
	//	10003: Forced Update Screen
	function get_field_sets($location) {
		$q = "SELECT `set_id` FROM `" . TABLE_PREFIX . "fields_sets_locations` WHERE `location`='$location' ORDER BY `order` ASC";
		$result = $this->run_query($q);
		$sets = array();
		while ($row = mysql_fetch_array($result)) {
			$sets[] = $row['set_id'];
		}
		return $sets;
	}

	// -------------------------------------------------
	//	Get basic information on a field set.
	function field_set_data($set_id) {
		$q = "SELECT * FROM `" . TABLE_PREFIX . "fields_sets` WHERE `id`='$set_id' LIMIT 1";
		$info = $this->get_array($q);
		return $info;
	}
	
	// -------------------------------------------------
	//	Generate a Field Set
	//		manual_fields_sets_locations: This stores where each field set is located (form ID, admin update page, etc.).
	//		manual_fields_sets: This stores general information on the field set.
	//		manual_fields_sets_comps: This stores information about the fields in a set.
	function generate_field_set($set_id, $set_information = "", $user_data = "", $skip_title = '0') {
		if (empty($set_information)) {
			$set_information = $this->field_set_data($set_id);
		}
		$data = "<fieldset>";
		// Start generating the fields
		$fields_in_set = $this->get_set_fields($set_id,$set_information['cols'],$user_data);
		if (! empty($fields_in_set['1'])) {
			$data .= "<script language=\"text/javascript\" type=\"javascript\">\n";
			$data .= "<!--\n";
			$data .= $fields_in_set['1'];
			$data .= "-->\n";
			$data .= "</script>\n\n";
		}
		// Name
		if (! empty($set_information['name']) && $skip_title != '1') {
			$data .= "<label>" . $set_information['name'] . "</label>";
		}
		// Description
		if (! empty($set_information['description'])) {
			$data .= "<p class=\"fieldset_description\">" . $set_information['description'] . "</p>";
		}
		$data .= $fields_in_set['0'];
		// Close the fieldset
		$data .= "</fieldset>";
		// Return set
		return $data;
	}

	// -------------------------------------------------
	//	Get fields in this set
	function get_set_fields($set_id,$columns = '1',$user_data = "") {
		$js_inclusions = array();
		if ($columns == "0") {
			$columns = "1";
		}
		$col_width = floor(100 / $columns);
		$current = "0";
		$these_fields = "<!-- start field set $set_id -->\n\n";
		while ($columns > 0) {
			$current++;
			$these_fields .= "<div class=\"field_set_col\" style=\"float:left;width:" . $col_width . "%;\">\n";
			$these_fields .= "<div class=\"field_set_col_pad\">\n";
			// Start looping this col's fields
			$q = "SELECT `field_id`,`req`,`tabindex` FROM `" . TABLE_PREFIX . "fields_sets_comps` WHERE `set_id`='$set_id' AND `col`='$current' ORDER BY `order` ASC";
			$fields = $this->run_query($q);
			$count_tabs = 7;
			while ($row = mysql_fetch_array($fields)) {
				$count_tabs++;
				if (empty($row['tabindex'])) {
					$row['tabindex'] = $count_tabs;
				}
				// Get this field's information
				$this_field_data = $this->get_field($row['field_id']);
				// Generate the field based on it's type
				// Text
				if ($this_field_data['type'] == "1") {
					$generated_field = $this->field_text($this_field_data,$row['req'],$row['tabindex'],$user_data[$row['field_id']]);
				}
				// Textarea
				else if ($this_field_data['type'] == "2") {
					$generated_field = $this->field_textarea($this_field_data,$row['req'],$row['tabindex'],$user_data[$row['field_id']]);
				}
				// Select
				else if ($this_field_data['type'] == "3") {
					$generated_field = $this->field_select($this_field_data,$row['req'],$row['tabindex'],$user_data[$row['field_id']]);
				}
				// Multi-select
				else if ($this_field_data['type'] == "4") {
					$generated_field = $this->field_multiselect($this_field_data,$row['req'],$row['tabindex'],$user_data[$row['field_id']]);
				}
				// Radio
				else if ($this_field_data['type'] == "5") {
					$generated_field = $this->field_radio($this_field_data,$row['req'],$row['tabindex'],$user_data[$row['field_id']]);
				}
				// Checkbox
				else if ($this_field_data['type'] == "6") {
					$generated_field = $this->field_checkbox($this_field_data,$row['req'],$row['tabindex'],$user_data[$row['field_id']]);
				}
				// Multi-checkbox
				else if ($this_field_data['type'] == "7") {
					$generated_field = $this->field_multicheckbox($this_field_data,$row['req'],$row['tabindex'],$user_data[$row['field_id']]);
				}
				// Hidden
				else if ($this_field_data['type'] == "8") {
					$generated_field = $this->field_hidden($this_field_data,$row['req'],$row['tabindex'],$user_data[$row['field_id']]);
				}
				// Date
				else if ($this_field_data['type'] == "9") {
					$generated_field = $this->field_date($this_field_data,$row['req'],$row['tabindex'],$user_data[$row['field_id']]);
				}
				// Linkert
				else if ($this_field_data['type'] == "10") {
					$generated_field = $this->field_linkert($this_field_data,$row['req'],$row['tabindex'],$user_data[$row['field_id']]);
				}
				// File Upload
				else if ($this_field_data['type'] == "10") {
					$generated_field = $this->field_fileupload($this_field_data,$row['req'],$row['tabindex'],$user_data[$row['field_id']]);
				}
				// Terms
				else if ($this_field_data['type'] == "11") {
					$generated_field = $this->field_terms($this_field_data,$row['req'],$row['tabindex'],$user_data[$row['field_id']]);
				}
				// Add the field to the list
				// $generated_field comes back with:
				// 0 = Field itself
				// 1 = JS Inclusions
				$these_fields .= $generated_field['0'];
				if (! empty($generated_field['1'])) {
					$js_inclusions[] = $generated_field['1'];
				}
			}
			$these_fields .= "</div>\n";
			$these_fields .= "</div>\n";
			$columns--;
		}
		$these_fields .= "<div class=\"clear\"></div>\n";
		$these_fields .= "<!-- end field set $set_id -->\n\n";
		$final_array = array(
			$these_fields,
			$js_inclusions
		);
		return $final_array;
	}
	
	
	// -------------------------------------------------
	//	Get a field information
	
	function get_field($field_id) {
		$q = "SELECT * FROM `" . TABLE_PREFIX . "fields` WHERE `id`='$field_id' LIMIT 1";
		$field = $this->get_array($q);
		return $field;
	}
	
	// -------------------------------------------------
	//	Get field type's name
	
	function get_type_name($type) {
		if ($type == '1') {
			return "Single-line text";
		}
		else if ($type == '2') {
			return "Multi-line text";
		}
		else if ($type == '3') {
			return "Drop Down";
		}
		else if ($type == '4') {
			return "Multiple Select";
		}
		else if ($type == '5') {
			return "Multiple Choice";
		}
		else if ($type == '6') {
			return "Checkbox";
		}
		else if ($type == '7') {
			return "Multiple Checkboxes";
		}
		else if ($type == '8') {
			return "Hidden";
		}
		else if ($type == '9') {
			return "Date";
		}
		else if ($type == '10') {
			return "Linkert";
		}
		else if ($type == '11') {
			return "File Upload";
		}
		else if ($type == '12') {
			return "Terms and Conditions";
		}
	}
	
	
	// -------------------------------------------------
	//	Basics to every field
	
	function field_basics($field_data,$value) {
		// Field value set?
		if (! empty($value)) {
			$show_value = $value;
			if ($field_data['encrypted'] == "1") {
				$show_value = $this->decode_data($value);
			}
		} else {
			if (! empty($field_data['default_value'])) {
				$show_value = $field_data['default_value'];
			}
		}
		// Styling?
		if (! empty($field_data['styling'])) {
			$style_components = unserialize($field_data['styling']);
			$style = "";
			foreach ($style_components as $component => $value) {
				$style .= $component . ":" . $value . ";";
			}
		}
		// This is only set to "1" if conditional
		// login has been applied to the field.
		if ($field_data['style_hide'] == "1") {
			$style .= "display:none;";
		}
		// Array
		$final_array = array(
			'style' => $style,
			'value' => $show_value,
			'js' => $js,
			'js_considerations' => $js_considerations
		);
		// Return
		return $final_array;
	}
	
	
	// -------------------------------------------------
	//	Text field
	
	function field_text($field_data,$req = '0',$tabindex = '',$value = "",$error = "0") {
		// Secondary Type?
		//	1 = Random ID
		//	2 = URL
		//	3 = E-Mail
		//	4 = Phone
		//	5 = State
		//	6 = Country
		// The basics
		$field_basics = $this->field_basics($field_data,$value);
		// Maxlength
		if (empty($field_data['maxlength'])) {
			$field_data['maxlength'] = "255";
		}
		// Render the field
		$field .= $this->put_name($field_data['display_name'],$req,$field_data['encrypted']);
		$field .= "<input type=\"text\" id=\"" . $field_data['id'] . "\" name=\"" . $field_data['id'] . "\" value=\"" . $field_basics['value'] . "\" style=\"" . $field_basics['style'] . "\" " . $field_basics['js'] . " maxlength=\"" . $field_data['maxlength'] . "\" tabindex=\"$tabindex\" />";
		// Description
		$field .= $this->put_description($field_data['description']);
		// Prepare array
		$send_back = array(
			$field,
			$field_basics['js_considerations']
		);
		// Return
		return $send_back;
	}
	
	// -------------------------------------------------
	//	Textarea field
	
	function field_textarea($field_data,$req = '0',$tabindex = '',$value = "",$error = "0") {
		// The basics
		$field_basics = $this->field_basics($field_data,$value);
		// Render the field
		$field .= $this->put_name($field_data['display_name'],$req,$field_data['encrypted']);
		
		
		// Description
		$field .= $this->put_description($field_data['description']);
		// Prepare array
		$send_back = array(
			$field,
			$field_basics['js_considerations']
		);
		// Return
		return $send_back;
	}
	
	// -------------------------------------------------
	//	Select field
	
	function field_select($field_data,$req = '0',$tabindex = '',$value = "",$error = "0") {
		// The basics
		$field_basics = $this->field_basics($field_data,$value);
		// Special fields
		// State
		if (! empty($field_data['secondary_type'])) {
			if ($field_data['secondary_type'] == "5") {
				$options = $this->state_list($value);
			}
			// Country
			else if ($field_data['secondary_type'] == "6") {
				$options = $this->country_list($value);
			}
		} else {
			$options = explode("\n",$field_data['options']);
		}
		// Options for this field
		$current = 0;
		// Render the field
		$field .= $this->put_name($field_data['display_name'],$req,$field_data['encrypted']);
		// Options
		$field .= "<select name=\"" . $field_data['id'] . "\" id=\"" . $field_data['id'] . "\" style=\"" . $field_basics['style'] . "\" " . $field_basics['js'] . ">\n";
		foreach ($options as $this_option) {
			$this_option = trim($this_option);
			$current++;
			$field .= "<option value=\"" . $this_option . "\"";
			if ($field_basics['value'] == $this_option) {
				$field .= " selected=\"selected\"";
			}
			$field .= " /> " . $this_option . "</option>\n";
		}
		$field .= "</select>\n";
		// Description
		$field .= $this->put_description($field_data['description']);
		// Prepare array
		$send_back = array(
			$field,
			$field_basics['js_considerations']
		);
		// Return
		return $send_back;
	}
	
	// -------------------------------------------------
	//	Multi-select field
	
	function field_multiselect($field_data,$req = '0',$tabindex = '',$value = "",$error = "0") {
		// The basics
		$field_basics = $this->field_basics($field_data,$value);
		// Render the field
		$field .= $this->put_name($field_data['display_name'],$req,$field_data['encrypted']);
		
		
		// Description
		$field .= $this->put_description($field_data['description']);
		// Prepare array
		$send_back = array(
			$field,
			$field_basics['js_considerations']
		);
		// Return
		return $send_back;
	}
	
	// -------------------------------------------------
	//	Radio field
	
	function field_radio($field_data,$req = '0',$tabindex = '',$value = "",$error = "0") {
		// The basics
		$field_basics = $this->field_basics($field_data,$value);
		// Render the field
		$field .= $this->put_name($field_data['display_name'],$req,$field_data['encrypted']);
		// Options for this field
		$current = 0;
		$options = explode("\n",$field_data['options']);
		foreach ($options as $this_option) {
			$this_option = trim($this_option);
			$current++;
			$field .= "<input type=\"radio\" id=\"" . $field_data['id'] . "_" . $current . "\" name=\"" . $field_data['id'] . "\"";
			if ($field_basics['value'] == $this_option) {
				$field .= " checked=\"checked\"";
			}
			$field .= " value=\"$this_option\" /> " . $this_option . "<br />";
		}
		// Description
		$field .= $this->put_description($field_data['description']);
		// Prepare array
		$send_back = array(
			$field,
			$field_basics['js_considerations']
		);
		// Return
		return $send_back;
	}
	
	// -------------------------------------------------
	//	Checkbox field
	
	function field_checkbox($field_data,$req = '0',$tabindex = '',$value = "",$error = "0") {
		// The basics
		$field_basics = $this->field_basics($field_data,$value);
		// Render the field
		$field .= $this->put_name($field_data['display_name'],$req,$field_data['encrypted']);
		
		
		// Description
		$field .= $this->put_description($field_data['description']);
		// Prepare array
		$send_back = array(
			$field,
			$field_basics['js_considerations']
		);
		// Return
		return $send_back;
	}
	
	// -------------------------------------------------
	//	Multi-checkbox field
	
	function field_multicheckbox($field_data,$req = '0',$tabindex = '',$value = "",$error = "0") {
		// The basics
		$field_basics = $this->field_basics($field_data,$value);
		// Render the field
		$field .= $this->put_name($field_data['display_name'],$req,$field_data['encrypted']);
		
		
		// Description
		$field .= $this->put_description($field_data['description']);
		// Prepare array
		$send_back = array(
			$field,
			$field_basics['js_considerations']
		);
		// Return
		return $send_back;
	}
	
	// -------------------------------------------------
	//	Hidden field
	
	function field_hidden($field_data,$req = '0',$tabindex = '',$value = "",$error = "0") {
		// The basics
		$field_basics = $this->field_basics($field_data,$value);
		// Render the field
		$field .= $this->put_name($field_data['display_name'],$req,$field_data['encrypted']);
		
		
		// Description
		$field .= $this->put_description($field_data['description']);
		// Prepare array
		$send_back = array(
			$field,
			$field_basics['js_considerations']
		);
		// Return
		return $send_back;
	}
	
	// -------------------------------------------------
	//	Date field
	
	function field_date($field_data,$req = '0',$tabindex = '',$value = "",$error = "0") {
		// The basics
		$field_basics = $this->field_basics($field_data,$value);
		// Render the field
		$field .= $this->put_name($field_data['display_name'],$req,$field_data['encrypted']);
		
		
		// Description
		$field .= $this->put_description($field_data['description']);
		// Prepare array
		$send_back = array(
			$field,
			$field_basics['js_considerations']
		);
		// Return
		return $send_back;
	}
	
	// -------------------------------------------------
	//	Linkert field
	
	function field_linkert($field_data,$req = '0',$tabindex = '',$value = "",$error = "0") {
		// The basics
		$field_basics = $this->field_basics($field_data,$value);
		// Render the field
		$field .= $this->put_name($field_data['display_name'],$req,$field_data['encrypted']);
		
		
		// Description
		$field .= $this->put_description($field_data['description']);
		// Prepare array
		$send_back = array(
			$field,
			$field_basics['js_considerations']
		);
		// Return
		return $send_back;
	}
	
	// -------------------------------------------------
	//	File Upload field
	
	function field_fileupload($field_data,$req = '0',$tabindex = '',$value = "",$error = "0") {
		// The basics
		$field_basics = $this->field_basics($field_data,$value);
		// Render the field
		$field .= $this->put_name($field_data['display_name'],$req,$field_data['encrypted']);
		
		
		// Description
		$field .= $this->put_description($field_data['description']);
		// Prepare array
		$send_back = array(
			$field,
			$field_basics['js_considerations']
		);
		// Return
		return $send_back;
	}
	
	// -------------------------------------------------
	//	Terms field
	
	function field_terms($field_data,$req = '0',$tabindex = '',$value = "",$error = "0") {
		// The basics
		$field_basics = $this->field_basics($field_data,$value);
		// Render the field
		$field .= $this->put_name($field_data['display_name'],$req,$field_data['encrypted']);
		
		
		// Description
		$field .= $this->put_description($field_data['description']);
		// Prepare array
		$send_back = array(
			$field,
			$field_basics['js_considerations']
		);
		// Return
		return $send_back;
	}

	// -------------------------------------------------
	//	Put Field Name
	function put_name($name,$req = '0',$encrypted = '0') {
		$data .= "<label>" . $name . "";
		if ($req == '1') {
			$data .= "<span class=\"req\">*</span>";
		}
		// Encrypted
		if ($encrypted == "1") {
			$data .= "<img src=\"" . URL . "/imgs/icon-encrypted.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"Encrypted in the database\" title=\"Encrypted in the database\" class=\"icon_l\" />";
		}
		$data .= "</label>\n";
		// Return
		return $data;
	}
	
	// -------------------------------------------------
	//	Put Field Description
	function put_description($description) {
		$data .= "<p class=\"field_description\">" . $description . "</p>";
		return $data;
	}

	// -------------------------------------------------
	//	State List
	
	function state_list($value,$short = '0') {
		$states = array();
		$states[""] = "";
		$states["AL"] = "Alabama";
		$states["AK"] = "Alaska";
		$states["AS"] = "American Samoa";
		$states["AZ"] = "Arizona";
		$states["AR"] = "Arkansas";
		$states["CA"] = "California";
		$states["CO"] = "Colorado";
		$states["CT"] = "Connecticut";
		$states["DE"] = "Delaware";
		$states["DC"] = "District of Columbia";
		$states["FL"] = "Florida";
		$states["GA"] = "Georgia";
		$states["GU"] = "Guam";
		$states["HI"] = "Hawaii";
		$states["ID"] = "Idaho";
		$states["IL"] = "Illinois";
		$states["IN"] = "Indiana";
		$states["IA"] = "Iowa";
		$states["KS"] = "Kansas";
		$states["KY"] = "Kentucky";
		$states["LA"] = "Louisiana";
		$states["ME"] = "Maine";
		$states["MH"] = "Marshall Islands";
		$states["MD"] = "Maryland";
		$states["MA"] = "Massachusetts";
		$states["MI"] = "Michigan";
		$states["MN"] = "Minnesota";
		$states["MS"] = "Mississippi";
		$states["MO"] = "Missouri";
		$states["MT"] = "Montana";
		$states["NE"] = "Nebraska";
		$states["NV"] = "Nevada";
		$states["NH"] = "New Hampshire";
		$states["NJ"] = "New Jersey";
		$states["NM"] = "New Mexico";
		$states["NY"] = "New York";
		$states["NC"] = "North Carolina";
		$states["ND"] = "North Dakota";
		$states["MP"] = "Northern Mariana Islands";
		$states["OH"] = "Ohio";
		$states["OK"] = "Oklahoma";
		$states["OR"] = "Oregon";
		$states["PW"] = "Palau";
		$states["PA"] = "Pennsylvania";
		$states["PR"] = "Puerto Rico";
		$states["RI"] = "Rhode Island";
		$states["SC"] = "South Carolina";
		$states["SD"] = "South Dakota";
		$states["TN"] = "Tennessee";
		$states["TX"] = "Texas";
		$states["UT"] = "Utah";
		$states["VT"] = "Vermont";
		$states["VI"] = "Virgin Islands";
		$states["VA"] = "Virginia";
		$states["WA"] = "Washington";
		$states["WV"] = "West Virginia";
		$states["WI"] = "Wisconsin";
		$states["WY"] = "Wyoming";
		$states["AA"] = "Armed Forces Americas";
		$states["AE"] = "Armed Forces";
		$states["AP"] = "Armed Forces Pacific";
		$states[""] = "";
		$states[""] = "Canadian Provinces";
		$states[""] = "";
		$states["AB"] = "Alberta";
		$states["BC"] = "British Columbia";
		$states["MB"] = "Manitoba";
		$states["NB"] = "New Brunswick";
		$states["NL"] = "Newfoundland and Labrador";
		$states["NT"] = "Northwest Territories";
		$states["NS"] = "Nova Scotia";
		$states["NU"] = "Nunavut";
		$states["ON"] = "Ontario";
		$states["PE"] = "Prince Edward Island";
		$states["QC"] = "Quebec";
		$states["SK"] = "Saskatchewan";
		$states["YT"] = "Yukon";
		$states[""] = "";
		$states["INT"] = "International";
		foreach ($states as $name => $value) {
			if ($short == "1") {
				$options[] = $name;
			} else {
				$options[] = $value;
			}
		}
		return $options;
	}

	// -------------------------------------------------
	//	Country List
	
	function country_list($value,$short = '1') {
		$countries = array();
		$countries[""] = "";
		$countries["US"] = "United States";
		$countries["CA"] = "Canada";
		$countries[""] = "";
		$countries["AF"] = "Afghanistan";
		$countries["AL"] = "Albania";
		$countries["DZ"] = "Algeria";
		$countries["AS"] = "American Samoa";
		$countries["AD"] = "Andorra";
		$countries["AO"] = "Angola";
		$countries["AI"] = "Anguilla";
		$countries["AQ"] = "Antarctica";
		$countries["AG"] = "Antigua and Barbuda";
		$countries["AR"] = "Argentina";
		$countries["AM"] = "Armenia";
		$countries["AW"] = "Aruba";
		$countries["AU"] = "Australia";
		$countries["AT"] = "Austria";
		$countries["AZ"] = "Azerbaijan";
		$countries["BS"] = "Bahamas";
		$countries["BH"] = "Bahrain";
		$countries["BD"] = "Bangladesh";
		$countries["BB"] = "Barbados";
		$countries["BY"] = "Belarus";
		$countries["BE"] = "Belgium";
		$countries["BZ"] = "Belize";
		$countries["BJ"] = "Benin";
		$countries["BM"] = "Bermuda";
		$countries["BT"] = "Bhutan";
		$countries["BO"] = "Bolivia";
		$countries["BA"] = "Bosnia and Herzegowina";
		$countries["BW"] = "Botswana";
		$countries["BV"] = "Bouvet Island";
		$countries["BR"] = "Brazil";
		$countries["IO"] = "British Indian Ocean Territory";
		$countries["BN"] = "Brunei Darussalam";
		$countries["BG"] = "Bulgaria";
		$countries["BF"] = "Burkina Faso";
		$countries["BI"] = "Burundi";
		$countries["KH"] = "Cambodia";
		$countries["CM"] = "Cameroon";
		$countries["CV"] = "Cape Verde";
		$countries["KY"] = "Cayman Islands";
		$countries["CF"] = "Central African Republic";
		$countries["TD"] = "Chad";
		$countries["CL"] = "Chile";
		$countries["CN"] = "China";
		$countries["CX"] = "Christmas Island";
		$countries["CC"] = "Cocos (Keeling) Islands";
		$countries["CO"] = "Colombia";
		$countries["KM"] = "Comoros";
		$countries["CG"] = "Congo";
		$countries["CD"] = "Congo (Democration Republic)";
		$countries["CK"] = "Cook Islands";
		$countries["CR"] = "Costa Rica";
		$countries["CI"] = "Cote d'Ivoire";
		$countries["HR"] = "Croatia (Hrvatska)";
		$countries["CU"] = "Cuba";
		$countries["CY"] = "Cyprus";
		$countries["CZ"] = "Czech Republic";
		$countries["DK"] = "Denmark";
		$countries["DJ"] = "Djibouti";
		$countries["DM"] = "Dominica";
		$countries["DO"] = "Dominican Republic";
		$countries["TP"] = "East Timor";
		$countries["EC"] = "Ecuador";
		$countries["EG"] = "Egypt";
		$countries["SV"] = "El Salvador";
		$countries["GQ"] = "Equatorial Guinea";
		$countries["ER"] = "Eritrea";
		$countries["EE"] = "Estonia";
		$countries["ET"] = "Ethiopia";
		$countries["FK"] = "Falkland Islands (Malvinas)";
		$countries["FO"] = "Faroe Islands";
		$countries["FJ"] = "Fiji";
		$countries["FI"] = "Finland";
		$countries["FR"] = "France";
		$countries["FX"] = "France, Metropolitan";
		$countries["GF"] = "French Guiana";
		$countries["PF"] = "French Polynesia";
		$countries["TF"] = "French Southern Territories";
		$countries["GA"] = "Gabon";
		$countries["GM"] = "Gambia";
		$countries["GE"] = "Georgia";
		$countries["DE"] = "Germany";
		$countries["GH"] = "Ghana";
		$countries["GI"] = "Gibraltar";
		$countries["GR"] = "Greece";
		$countries["GL"] = "Greenland";
		$countries["GD"] = "Grenada";
		$countries["GP"] = "Guadeloupe";
		$countries["GU"] = "Guam";
		$countries["GT"] = "Guatemala";
		$countries["GN"] = "Guinea";
		$countries["GW"] = "Guinea-Bissau";
		$countries["GY"] = "Guyana";
		$countries["HT"] = "Haiti";
		$countries["HM"] = "Heard and Mc Donald Islands";
		$countries["VA"] = "Holy See (Vatican City State)";
		$countries["HN"] = "Honduras";
		$countries["HK"] = "Hong Kong";
		$countries["HU"] = "Hungary";
		$countries["IS"] = "Iceland";
		$countries["IN"] = "India";
		$countries["ID"] = "Indonesia";
		$countries["IR"] = "Iran (Islamic Republic of)";
		$countries["IQ"] = "Iraq";
		$countries["IE"] = "Ireland";
		$countries["IL"] = "Israel";
		$countries["IT"] = "Italy";
		$countries["JM"] = "Jamaica";
		$countries["JP"] = "Japan";
		$countries["JO"] = "Jordan";
		$countries["KZ"] = "Kazakhstan";
		$countries["KE"] = "Kenya";
		$countries["KI"] = "Kiribati";
		$countries["KP"] = "Korea (Democratic People's Republic of)";
		$countries["KR"] = "Korea (Republic of)";
		$countries["KW"] = "Kuwait";
		$countries["KG"] = "Kyrgyzstan";
		$countries["LA"] = "Laos";
		$countries["LV"] = "Latvia";
		$countries["LB"] = "Lebanon";
		$countries["LS"] = "Lesotho";
		$countries["LR"] = "Liberia";
		$countries["LY"] = "Libyan Arab Jamahiriya";
		$countries["LI"] = "Liechtenstein";
		$countries["LT"] = "Lithuania";
		$countries["LU"] = "Luxembourg";
		$countries["MO"] = "Macau";
		$countries["MK"] = "Macedonia, The Former Yugoslav Republic of";
		$countries["MG"] = "Madagascar";
		$countries["MW"] = "Malawi";
		$countries["MY"] = "Malaysia";
		$countries["MV"] = "Maldives";
		$countries["ML"] = "Mali";
		$countries["MT"] = "Malta";
		$countries["MH"] = "Marshall Islands";
		$countries["MQ"] = "Martinique";
		$countries["MR"] = "Mauritania";
		$countries["MU"] = "Mauritius";
		$countries["YT"] = "Mayotte";
		$countries["MX"] = "Mexico";
		$countries["FM"] = "Micronesia, Federated States of";
		$countries["MD"] = "Moldova, Republic of";
		$countries["MC"] = "Monaco";
		$countries["MN"] = "Mongolia";
		$countries["MS"] = "Montserrat";
		$countries["MA"] = "Morocco";
		$countries["MZ"] = "Mozambique";
		$countries["MM"] = "Myanmar";
		$countries["NA"] = "Namibia";
		$countries["NR"] = "Nauru";
		$countries["NP"] = "Nepal";
		$countries["NL"] = "Netherlands";
		$countries["AN"] = "Netherlands Antilles";
		$countries["NC"] = "New Caledonia";
		$countries["NZ"] = "New Zealand";
		$countries["NI"] = "Nicaragua";
		$countries["NE"] = "Niger";
		$countries["NG"] = "Nigeria";
		$countries["NU"] = "Niue";
		$countries["NF"] = "Norfolk Island";
		$countries["MP"] = "Northern Mariana Islands";
		$countries["NO"] = "Norway";
		$countries["OM"] = "Oman";
		$countries["PK"] = "Pakistan";
		$countries["PW"] = "Palau";
		$countries["PA"] = "Panama";
		$countries["PG"] = "Papua New Guinea";
		$countries["PY"] = "Paraguay";
		$countries["PE"] = "Peru";
		$countries["PH"] = "Philippines";
		$countries["PN"] = "Pitcairn";
		$countries["PL"] = "Poland";
		$countries["PT"] = "Portugal";
		$countries["PR"] = "Puerto Rico";
		$countries["QA"] = "Qatar";
		$countries["RE"] = "Reunion";
		$countries["RO"] = "Romania";
		$countries["RU"] = "Russian Federation";
		$countries["RW"] = "Rwanda";
		$countries["KN"] = "Saint Kitts and Nevis";
		$countries["LC"] = "Saint Lucia";
		$countries["VC"] = "Saint Vincent and the Grenadines";
		$countries["WS"] = "Samoa";
		$countries["SM"] = "San Marino";
		$countries["ST"] = "Sao Tome and Principe";
		$countries["SA"] = "Saudi Arabia";
		$countries["SN"] = "Senegal";
		$countries["SC"] = "Seychelles";
		$countries["SL"] = "Sierra Leone";
		$countries["SG"] = "Singapore";
		$countries["SK"] = "Slovakia (Slovak Republic)";
		$countries["SI"] = "Slovenia";
		$countries["SB"] = "Solomon Islands";
		$countries["SO"] = "Somalia";
		$countries["ZA"] = "South Africa";
		$countries["GS"] = "South Georgia and the South Sandwich Islands";
		$countries["ES"] = "Spain";
		$countries["LK"] = "Sri Lanka";
		$countries["SH"] = "St. Helena";
		$countries["PM"] = "St. Pierre and Miquelon";
		$countries["SD"] = "Sudan";
		$countries["SR"] = "Suriname";
		$countries["SJ"] = "Svalbard and Jan Mayen Islands";
		$countries["SZ"] = "Swaziland";
		$countries["SE"] = "Sweden";
		$countries["CH"] = "Switzerland";
		$countries["SY"] = "Syrian Arab Republic";
		$countries["TW"] = "Taiwan, Province of China";
		$countries["TJ"] = "Tajikistan";
		$countries["TZ"] = "Tanzania, United Republic of";
		$countries["TH"] = "Thailand";
		$countries["TG"] = "Togo";
		$countries["TK"] = "Tokelau";
		$countries["TO"] = "Tonga";
		$countries["TT"] = "Trinidad and Tobago";
		$countries["TN"] = "Tunisia";
		$countries["TR"] = "Turkey";
		$countries["TM"] = "Turkmenistan";
		$countries["TC"] = "Turks and Caicos Islands";
		$countries["TV"] = "Tuvalu";
		$countries["UG"] = "Uganda";
		$countries["UA"] = "Ukraine";
		$countries["AE"] = "United Arab Emirates";
		$countries["GB"] = "United Kingdom";
		$countries["UM"] = "United States Minor Outlying Islands";
		$countries["UY"] = "Uruguay";
		$countries["UZ"] = "Uzbekistan";
		$countries["VU"] = "Vanuatu";
		$countries["VE"] = "Venezuela";
		$countries["VN"] = "Viet Nam";
		$countries["VG"] = "Virgin Islands (British)";
		$countries["VI"] = "Virgin Islands (U.S.)";
		$countries["WF"] = "Wallis and Futuna Islands";
		$countries["EH"] = "Western Sahara";
		$countries["YE"] = "Yemen";
		$countries["YU"] = "Yugoslavia";
		$countries["ZM"] = "Zambia";
		$countries["ZW"] = "Zimbabwe";
		foreach ($countries as $name => $value) {
			if ($short == "1") {
				$options[] = $value;
			} else {
				$options[] = $name;
			}
		}
		return $options;
	}

}

?>