<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Redirects to admin control panel without revealing
	the admin location in links that may be visible by non-admins.
	
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

if ($privileges['is_admin'] == '1') {

	if (! empty($_GET['action'])) {
		header('Location: ' . ADMIN_URL . '/index.php?l=' . $_GET['action']);
		exit;
	} else {
		header('Location: ' . ADMIN_URL);
		exit;
	}

} else {
	$db->show_error(lg_privilieges_req);
	exit;
}

?>