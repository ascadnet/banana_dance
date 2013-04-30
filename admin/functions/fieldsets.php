<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Manages ajax calls for field sets on the user screen.
	
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


// ----------------------------------------------------------------------------------
//	Load the basics

require "../../config.php";
require "../../includes/admin.functions.php";
$admin = new admin;

// ----------------------------------------------------------------------------------
//	Logged in and has privileges?

$admin->check_permission('is_admin',$user,$privileges);


// ----------------------------------------------------------------------------------
//	Add a Field Set

if ($_POST['action'] == "add") {

	if (empty($_POST['set'])) {
		echo "0+++Set" . lg_something_required;
		exit;
	}
	if (empty($_POST['position'])) {
		echo "0+++Location" . lg_something_required;
		exit;
	}
	// Exists already?
	$q = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "fields_sets_locations` WHERE `set_id`='" . $db->mysql_clean($_POST['set']) . "' AND `location`='" . $db->mysql_clean($_POST['position']) . "'";
	$count = $db->get_array($q);
	if ($count['0'] > 0) {
		echo "0+++" . lg_fieldset_exists;
		exit;
	}
	
	// Order it
	$q1 = "SELECT `order` FROM `" . TABLE_PREFIX . "fields_sets_locations` WHERE `location`='" . $db->mysql_clean($_POST['position']) . "' ORDER BY `order` DESC LIMIT 1";
	$order = $db->get_array($q1);
	$next_order = $order['order'] + 1;
	
	// Add the field set
	$q2 = "INSERT INTO `" . TABLE_PREFIX . "fields_sets_locations` (`set_id`,`location`,`order`) VALUES ('" . $db->mysql_clean($_POST['set']) . "','" . $db->mysql_clean($_POST['position']) . "','$next_order')";
	$insert = $db->insert($q2);
	
	// User?
	if (! empty($_POST['user'])) {
		$user_data = $session->get_user_data('',$_POST['user']);
	}
	
	// Field Functions
	require PATH . "/includes/field.functions.php";
	$fields = new fields;
	
	// Field Set Data
	$set_info = $fields->field_set_data($db->mysql_clean($_POST['set']));
	// Generate the set with the user's information
	$field_set = $fields->generate_field_set($db->mysql_clean($_POST['set']),$set_info,$user_data,'1');

	echo "1+++" . $field_set . "+++" . $_POST['set'] . "+++" . $set_info['name'];
	exit;

}


// ----------------------------------------------------------------------------------
//	Delete a Field Set From Page

else if ($_POST['action'] == "delete") {
	
	if (empty($_POST['set'])) {
		echo "0+++Fieldset" . lg_something_required;
		exit;
	}
	
	if (empty($_POST['position'])) {
		echo "0+++Location" . lg_something_required;
		exit;
	}
	
	// Add the field set
	$q2 = "DELETE FROM `" . TABLE_PREFIX . "fields_sets_locations` WHERE `set_id`='" . $db->mysql_clean($_POST['set']) . "' AND `location`='" . $db->mysql_clean($_POST['position']) . "' LIMIT 1";
	$del = $db->delete($q2);
	
	echo "1+++" . lg_deleted;
	exit;
	
}

?>