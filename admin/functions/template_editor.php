<?php


/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Receives ajax calls from the template project manager.
	
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
//	Get a template for inclusion
	
	if ($_POST['action'] == 'save_project') {
	
		$exp = explode(',',$_POST['templates']);
		foreach ($exp as $temp) {
			$name = $template->get_template_info('html',$temp,'0','title');
			$return .= ",$temp+++" . $name['title'];
		}
		$return = substr($return,1);
		$serialized_array = array();
		$serialized_array['templates'] = $return;
		$serialized_array['date'] = $db->current_date();
		$serialized_array = serialize($serialized_array);
		
	//	if (empty($_POST['active_project'])) {
	//		$rand = rand(100,10000);
	//		$q = "INSERT INTO `" . TABLE_PREFIX . "user_data` (`user_id`,`key`,`value`) VALUES ('" . $user_data['id'] . "','project$rand','$serialized_array')";
	//		$rand = $db->insert($q);
	//	} else {
			$rand = $_POST['active_project'];
			$q = "UPDATE `" . TABLE_PREFIX . "user_data` SET `value`='$serialized_array' WHERE `id`='$rand' LIMIT 1";
			$update = $db->update($q);
	//	}
		
		// $update = $db->update_eav('project',$serialized_array,$user_data['id'],'user_id');
		echo "$rand";
		exit;
		
	}
	
	else if ($_POST['action'] == 'load_project') {
	
		$q = "SELECT `value` FROM `" . TABLE_PREFIX . "user_data` WHERE `id`='" . $db->mysql_clean($_POST['id']) . "' LIMIT 1";
		$data = $db->get_array($q);
		$undone = unserialize($data['value']);
		echo $undone['templates'];
		exit;
	
	}
	
	else {
	
		$templateInfo = $template->get_template_info('html',$_POST['id'],'1');
		$filename = PATH . "/html/templates/" . $theme . "/" . $templateInfo['template'] . ".php";
		$templateInfo['filename'] = $filename;
		$json = json_encode($templateInfo);
		echo $json;
		exit;
		
	}

?>