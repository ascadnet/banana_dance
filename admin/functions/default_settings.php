<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Establishes default category or website settings
	for pages.
	
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
//	Continue

// Loop through submitted fields.
// Only fields within the form named "edit"
// will be sent from admin.js.
$table = TABLE_PREFIX . "item_options";
foreach ($_POST as $name => $value) {
	if ($name == 'act_id' || $name == 'type' || $name == 'overwrite' || $name == 'id') {
		continue;
	} else {
		$update_eav = $db->update_eav($name,$value,$_POST['act_id'],'act_id',$table,$_POST['type']);
		if ($name != 'category_default') {
			$update .= ",`" . $db->mysql_clean($name) . "`='" . $db->mysql_clean($value) . "'";
		}
	}
}
$update = substr($update,1);

$clean_id = $db->mysql_clean($_POST['act_id']);

// Update settings?
if ($_POST['overwrite'] == '3') {
	$where = "`category`='" . $clean_id . "'";
}
else if ($_POST['overwrite'] == '2') {
	// Subcategories
	$subcates = $manual->get_subcategories_of($_POST['act_id']);
	$exp_subs = explode(',',$subcates);
	foreach ($exp_subs as $aSub) {
		$where .= " OR `category`='$aSub'";
	}
	$where = substr($where,4);
}
else {
	$where = '';
}


if (! empty($where)) {	
	$q = "UPDATE `" . TABLE_PREFIX . "articles` SET $update WHERE $where";
	$update = $db->update($q);
}


// User Type Stripped Privs
// Stripped Privileges
$q = "DELETE FROM `bd_stripped_privs` WHERE `category`='" . $clean_id . "'";
$del = $db->delete($q);

$insert_strip = '';
$q = "SELECT `id` FROM `" . TABLE_PREFIX . "user_types`";
$results = $db->run_query($q);
while ($row = mysql_fetch_array($results)) {
	$name = 'strip_' . $row['id'];
	$priv = 'strip_priv_' . $row['id'];
	if ($_POST[$name] == '1') {
		if (empty($_POST[$priv])) {
			$_POST[$priv] = 'all';
		}
		$insert_strip .= ",('" . $clean_id . "','" . $db->mysql_clean($_POST[$priv]) . "','" . $row['id'] . "')";
	}
}
if (! empty($insert_strip)) {
	$insert_strip = ltrim($insert_strip,',');
	$q1 = "INSERT INTO `" . TABLE_PREFIX . "stripped_privs` (`category`,`privilege`,`group`) VALUES $insert_strip";
	$insert = $db->insert($q1);
}

echo "1+++Saved";
exit;

?>