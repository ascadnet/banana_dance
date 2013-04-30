<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Widget management from inline editor.
	
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

require "../config.php";


// -----------------------------------------------------------------
//	List existing widgets of a certain
//	type in a select menu

if ($_POST['action'] == "get_widgets") {
	if (empty($user)) {
		echo lg_login_to_use_feature;
		exit;
	}

	// Table
	$final = "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\" id=\"widgets_table\"><thead><tr>";
	$final .= "<th>Name</th>";
	$final .= "<th>Owner</th>";
	$final .= "<th>Date</th>";
	$final .= "<th>Options</th>";
	$final .= "</tr></thead><tbody>";
	
	// Go through widgets...
	$clean_type = $db->mysql_clean($_POST['type']);
	$q = "SELECT `id`,`name`,`date`,`owner` FROM `" . TABLE_PREFIX . "widgets` WHERE `type`='" . $clean_type . "' ORDER BY `name` ASC";
	$list = $db->run_query($q);
	while ($row = mysql_fetch_array($list)) {
		$final .= "<tr>";
		$final .= "<td><a href=\"#\" onclick=\"editWidget('" . $row['id'] . "','" . $clean_type . "');return false;\">" . $row['name'] . "</a></td>";
		$final .= "<td>" . $row['owner'] . "</td>";
		$final .= "<td>" . $db->format_date($row['date']) . "</td>";
		$final .= "<td><a href=\"#\" onclick=\"add_widget('" . $row['id'] . "');return false;\">Add to Page</a> | <a href=\"deleteWidget('" . $row['id'] . "');return false;\">Delete</a></td>";
		$final .= "</tr>";
	}
	
	// Close and send table
	$final .= "</tbody></table>";
	
	echo $final;
	exit;
}

// -----------------------------------------------------------------
//	Create a widget

else if ($_POST['action'] == "create_widget") {
	if (empty($user)) {
		echo lg_login_to_use_feature;
		exit;
	}

	// Widget class
	$widget = new widget($_POST['type'],$_POST);

	echo $widget;
	exit;

}


// -----------------------------------------------------------------
//	Get a widget

else if ($_GET['action'] == "get_widget") {

	$widget = $manual->widget_info($_GET['id']);
	$options = unserialize($widget['options']);
	foreach ($options as $key => $value) {
		$widget[$key] = $value;
	}
	$js_encode = json_encode($widget);
	echo $js_encode;
	exit;
	
}

// -----------------------------------------------------------------
//	Get a dropdown list of categories.

else if ($_POST['action'] == "get_category_list") {

	$list = $manual->category_select($_POST['selected'],'0','0','category','widget_category');
	echo $list;
	exit;
	
}


// -----------------------------------------------------------------
//	Get a dropdown list of pages

else if ($_POST['action'] == "get_page_list") {

	$list = $manual->category_select($_POST['selected'],'0','0','category');
	echo $list;
	
}


// -----------------------------------------------------------------
//	Get a dropdown list of pages

else if ($_POST['action'] == "get_usertype_list") {

	$list = $manual->user_types_select($_POST['selected'],'radio','1');
	echo $list;
	
}


// -----------------------------------------------------------------
//	Get a page's name

else if ($_POST['action'] == "get_page_name") {

	$list = $manual->get_article_name_from_id($_POST['id']);
	echo $list;
	
}


// -----------------------------------------------------------------
//	Change an item's "complete" status

else if ($_POST['action'] == "mark_todo") {

	$editable = $manual->check_todo_privs($_POST['widget']);
   	
   	if ($editable != '1') {
   		echo "0+++" . lg_privilieges_req;
   		exit;
   	} else {
   		$to_do_status = $manual->get_todo_status($_POST['id'],'1');
   		echo "1+++" . $to_do_status;
   	}
	
}


// -----------------------------------------------------------------
//	Delete a to do item

else if ($_POST['action'] == "del_todo") {

	$editable = $manual->check_todo_privs($_POST['widget']);
   	if ($editable != '1') {
   		echo "0+++" . lg_privilieges_req;
   		exit;
   	} else {
		$delWidget = $manual->delete_widget_item($_POST['id']);
		echo "1+++" . $to_do_status;
		exit;
	}
	
}


// -----------------------------------------------------------------
//	Add a to-do item

else if ($_POST['action'] == "addItem") {

	$editable = $manual->check_todo_privs($_POST['widget']);
   	if ($editable != '1') {
   		echo "0+++" . lg_privilieges_req;
   		exit;
   	} else {
		$addToDo = $manual->add_todo_item($_POST['widget'],$_POST['name']);
		echo "1+++$addToDo";
		exit;
	}
	
}


// -----------------------------------------------------------------
//	Get Pages by Month

else if ($_POST['action'] == "getPagesByMonth") {

	if (empty($_POST['widget']) || empty($_POST['month'])) {
		echo "0+++Required information missing (month or widget ID).";
		exit;
	}

	$widget = $manual->widget_info($_POST['widget']);
	$widoptions = unserialize($widget['options']);

	// Work with the options to create
	// a WHERE clause.
	$where = '';
	$category = '';
	if (! empty($widoptions['category'])) {
		$subcats = $manual->get_subcategories_of($widoptions['category'],'1');
		foreach ($subcats as $aCate) {
			$categories .= " OR `category`='$aCate'";
		}
		$categories = ltrim($categories,' OR ');
	}
	if (! empty($categories)) {
		$where .= " AND ($categories)";
	}
	
	// Order?
	if (empty($widoptions['order'])) {
		$widoptions['order'] = 'created';
	}
	if (empty($widoptions['dir'])) {
		$widoptions['dir'] = 'DESC';
	}
	$order = "ORDER BY `" . $widoptions['order'] . "` " . $widoptions['dir'];
	
	if (empty($widoptions['limit'])) {
		$widoptions['limit'] = "9999";
	} 
	$limit = "LIMIT " . $widoptions['limit'];
	
	// Run the query
	$widget_content = "<ul id=\"" . $_POST['widget'] . $_POST['month'] . "items\">";
	$found = '0';
	$q = "SELECT * FROM `" . TABLE_PREFIX . "articles` WHERE `created` LIKE '" . date('Y-m',$_POST['month']) . "%' AND `public`='1'$where $order $limit";
	$results = $db->run_query($q);
	while ($row = mysql_fetch_array($results)) {
		$found = '1';
		$run_replace = $manual->replace_article_tags($row);
		$widget_content .= strtr($widget['html_insert'], $run_replace);
	}
	
	if ($found == '0') {
		$widget_content .= "<li class=\"none\">" . lg_nothing . "</li>";
	}
	$widget_content .= "</ul>";
		
	echo "1+++" . $widget_content;
	exit;
	
}

?>