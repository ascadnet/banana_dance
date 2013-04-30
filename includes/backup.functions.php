<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: DB and general-use functions.
	
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

class backup extends db {


	// -----------------------------------------------------------
	//	Backup the database
	
	function backup_db($notices = '1') {
		
		$time = time();
		
		$db_folder = PATH . "/generated/db_backup";
		if (! file_exists($db_folder)) {
			$make = @mkdir($db_folder) or die('Please create a directory named "db_backup" in the generated folder, set its permissions to 777, and try again!');
		} else {
			$this->delete_dir($db_folder);
		}
		
		$resultG = mysql_query("SHOW tables");
		for ($G = 0; $G < mysql_num_rows($resultG); $G++) {
			$tablename = mysql_result($resultG, $G, 0);
			
			if ($notices == '1') {
				echo "<li>Backing up <b>$tablename</b>...";
			}
			
	   		// Get all fields names in table "name_list" in database "tutorial".
	   		$table = $tablename;
	   		
	   		$result = mysql_query("SELECT * FROM $table");
	   		$fields = mysql_list_fields(MYSQL_DB,$table);
	   		
	   		// Count the table fields and put the value into $columns. 
	   		$columns = mysql_num_fields($fields);
	   		
	   		// Output variable
	   		// Put the name of all fields to $out. 
	   		$out = '';
	   		for ($i = 0; $i < $columns; $i++) {
	   			$l = mysql_field_name($fields, $i);
	   			$out .= '"' . $l . '",';
	   		}
	   		$out .= "\n";
	   		
	   		// Add all values in the table to $out. 
	   		while ($l = mysql_fetch_array($result)) {
	   			for ($i = 0; $i < $columns; $i++) {
	   				$out .= '"' . addslashes($l[$i]) . '",';
	   			}
	   			$out .= "\n";
	   		}
	   		
	   		// Open file export.csv.
	   		// Put all values from $out to export.csv.
	   		$uniq = $time . "_" . $table . "_" . substr(uniqid(),0,10);
	   		$filename = $db_folder . '/' . $uniq . '.csv';
	   		
	   		$f = fopen($filename,'w');
	   		fputs($f, $out);
	   		fclose($f);
	   		
			if ($notices == '1') {
				echo "<font color=\"green\"> complete!</font> ($uniq.csv)</li>";
			}
			
		}
	}
	
	
	// -----------------------------------------------------------
	//	ZIP Theme files.

	function zip_theme($notices = '1') {
	
		$theme_bk_folder = PATH . "/generated/theme_backup";
		if (! file_exists($theme_bk_folder)) {
			$make = mkdir($theme_bk_folder) or die('Please create a directory named "theme_backup" in the generated folder, set its permissions to 777, and try again!');
		} else {
			$this->delete_dir($theme_bk_folder);
		}
	
		global $theme;
		$source = PATH . "/templates/html/" . $theme;
		$destination = $theme_bk_folder . "/theme_backup.zip";
	
		if (extension_loaded('zip') === true) {
		   if (file_exists($source) === true) {
		       $zip = new ZipArchive();
		       if ($zip->open($destination, ZIPARCHIVE::CREATE) === true) {
		           $source = realpath($source);
		           if (is_dir($source) === true) {
		               $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
		               foreach ($files as $file) {
		                   $file = realpath($file);
		                   if (is_dir($file) === true) {
		                       $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
		                   }
		                   else if (is_file($file) === true) {
		                       $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
		                   }
		               }
					if ($notices == '1') {
						echo "<li>Backed up folder <b>$source</b>";
					}
		           }
		           else if (is_file($source) === true) {
		               $zip->addFromString(basename($source), file_get_contents($source));
					if ($notices == '1') {
						echo "<li>Backed up file <b>$source</b>";
					}
		           }
		       }
		       return $zip->close();
		   }
		}
		else {
					if ($notices == '1') {
						echo "<li>Could not back up theme files: no ZIP class found.";
					}
		}
		
	}
	
}

?>
