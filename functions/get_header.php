<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Retrieves the Banana Dance header.
	
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

require_once "../config.php";

$header = $template->render_template('header','','','1','0','','','1');

$breadcrumbs = "<a href=\"" . URL . "\">Home</a>";
$category_name = "";

$header = str_replace('%breadcrumbs%',$breadcrumbs,$header);
$header = str_replace('%category_name%',$category_name,$header);

echo $header;

?>