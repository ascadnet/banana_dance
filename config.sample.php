<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Basic program configuration. Also
	loads the requires classes to run the program.
	This needs to be called on every program file.
	
	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.

====================================================== */


// ----------------------------------------
// 	MySQL

define('TABLE_PREFIX','bd_');
define('MYSQL_HOST','');
define('MYSQL_DB','');
define('MYSQL_USER','');
define('MYSQL_PASS','');


// ----------------------------------------
// 	Paths

define('SITE_SALT','38Fc');
define('SALT1','abc12def45kjafv834unanjfn230u8cqw0jnefsjdfgv3n40tu-3jmdcc238u');
define('SALT2','123456789012345678901234567890123456789012345678901234567890');

define('COMPANY_URL','http://www.yoursite.com/');

define('PATH','/full/path/to/base/banana_dance/directory');
define('URLHOLD','http://www.yoursite.com/');
define('ADMIN_FOLDER','admin');
define('ADMIN_URL','http://www.yoursite.com/admin');


// ----------------------------------------
//	PHP Configuration

// ini_set('display_errors', 1); 
// ini_set('error_log', PATH . '/generated/error_log.txt'); 
define('PERFORMANCE_TESTS','0');


// ----------------------------------------
// 	Let's start dancing...

define('LANGUAGE','english');
require PATH . '/addons/languages/' . LANGUAGE . '.php';
require PATH . '/start_load.php';

?>