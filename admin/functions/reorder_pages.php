<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Re-order pages in category tree.
	
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

require '../../config.php';

// ----------------------------------------------------------------------------------
//	Logged in as admin?

if (empty($user) || $privileges['cp_access'] != "1") {
	$db->show_error('You do not have the privilieges to access this page.');
	exit;
}

// ----------------------------------------------------------------------------------
//	Proceed to re-order pages and categories.

require '../../includes/admin.functions.php';
$admin = new admin;

$_POST['values'] = ltrim($_POST['values'],'----');
$values = explode('----',$_POST['values']);

$order = 0;

foreach ($values as $ordered) {
	$element = explode(':',$ordered);
	$category_reside = ltrim($element['1'],'c-');
	$order++;
	
	// Skip garbage...
	if (strpos($element['0'],'ex_') !== false) {
		
	}
	
	// A page
	else if (strpos($element['0'],'page_') !== false) {
		$this_page = ltrim($element['0'],'page_');
		$q = "UPDATE `" . TABLE_PREFIX . "articles` SET `order`='$order',`category`='" . $db->mysql_clean($category_reside) . "' WHERE `id`='" . $db->mysql_clean($this_page) . "' LIMIT 1";
		$update = $db->update($q);
	}
	
	// A category
	else if (strpos($element['0'],'category_') !== false) {
		$this_category = ltrim($element['0'],'category_');
		$q1 = "UPDATE `" . TABLE_PREFIX . "categories` SET `order`='$order',`subcat`='" . $db->mysql_clean($category_reside) . "' WHERE `id`='" . $db->mysql_clean($this_category) . "' LIMIT 1";
		$update = $db->update($q1);
	}

}


?>