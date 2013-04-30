<?php

if ($privileges['is_admin'] != "1") {
	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
} else {
	
?>

<div id="content_overlay">

   	<div class="submit">
   		<a href="http://www.doyoubananadance.com/Product-Manual/Updating-Banana-Dance" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
   	</div>
   	
	<h1>Database Updater</h1>
	
	<p class="attention"><a href="http://www.doyoubananadance.com/Product-Manual/Updating-Banana-Dance" target="_blank">Click here</a> for more information on updating.</p>
	
	<?php
	
		// Talk to the server
		$url = "http://www.doyoubananadance.com/program/auto_updater/send_updates.php";
		$string = "update=" . $_GET['update'] . "&version=" . $db->get_option('version') . "&name=" . NAME . "&company=" . COMPANY . "&url=" . URL . "&table_prefix=" . TABLE_PREFIX;
		$reply = $db->curl_call($url,$string);
		
		if ($_GET['update'] == '1') {
			
			// ------------------------------------
			// 	Back up the DB!
			
			$db->backup_db('1');
			
			// -----------------------------------------------------------
			// 	Update the database
			
			if ($_GET['no_db'] != '1') {
				echo "<h2>Updating Database</h2>";
	
				$update = '1';
				$get_version = explode('/////',$reply);
				$main_reply = ltrim($get_version['1'],'=-=-=-=');
				if (empty($main_reply)) {
					echo "<li>Database is up-to-date!</li>";
				} else {
					$returned_versions = explode('=-=-=-=',$main_reply);
					foreach ($returned_versions as $aVersion) {
						$version_commands = explode('++++',$aVersion);
						$theCommands = ltrim($version_commands['1'],'||||');
						$run_command = explode('||||',$theCommands);
						echo "<h3>v" . $version_commands['0'] . " (database sync)</h3>";
						foreach ($run_command as $DoCommand) {
							if ($_GET['show_commands'] == '1') {
								echo "<p>" . $DoCommand . "</p>";
							} else {
								$run = $db->run_query($DoCommand);
							}
						}
						echo  $version_commands['2'];
					}
					// Update the user's version
					$q = "UPDATE `" . TABLE_PREFIX . "options` SET `value`='" . $db->mysql_clean($get_version['0']) . "' WHERE `key`='version' LIMIT 1";
					$update = $db->update($q);
					$q1 = "UPDATE `" . TABLE_PREFIX . "options` SET `value`='" . $db->current_date() . "' WHERE `key`='last_updated' LIMIT 1";
					$update = $db->update($q1);
				}
			}
			
			// -----------------------------------------------------------
			// 	Update the program files
		
			if ($_GET['no_files'] != '1') {
				echo "<h2>Updating Program Files</h2>";
				
				// ------------------------------------
				// 	Set up the paths
				
				$dir = PATH . '/generated';
				$extract = PATH;
				$local_file = 'banana_dance_update.zip';
				$full = $dir . "/" . $local_file;
				
					
				// ------------------------------------
				// 	Connect to the FTP server
				
				$c = curl_init("ftp://doyoubananadance.com/$local_file");
				$fh = fopen($full, 'w') or die('Error'); 
				curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($c, CURLOPT_FILE, $fh);
				curl_setopt($c, CURLOPT_USERPWD, "anonymous@doyoubananadance.com:");
				curl_exec($c);
				curl_close($c);
				
				// ------------------------------------
				// 	Unzip the ZIP file
				
				$zip = new ZipArchive;
				$res = $zip->open($full);
				if ($res === TRUE) {
					$zip->extractTo($extract);
					$zip->close();
				} else {
					echo "<li><b>Error:</b> Failed to extract files. Please ensure that PHP on your server can use the Zip functions.</li>";
				}
				
				// ------------------------------------
				// 	Destory the unzipped directory
				// 	and delete the downloaded ZIP file.
				unlink($full);
			}
			
		}
		
		// We didn't update
		if ($update != '1') {
			if ($reply == 'disabled') {
					echo "<h3>Outside Connections Disabled</h3>";
					echo "<p>It appears that you've disabled outside connections. You'll need to either enable them from the settings or go to the Banana Dance website to get the latest updates.</p>";
			} else {
				$cut_up = explode('+++',$reply);
				if (empty($cut_up['1'])) {
					$cut_up['1'] = 'Your dance moves are up-to-date.';
				}
				if ($cut_up['0'] == '1') {
					echo "<h3>" . $cut_up['1'] . "</h3>";
					echo $cut_up['2'];
				} else {
					echo "<h3>" . $cut_up['1'] . "</h3>";
					echo "<p>Looks like you're running the most recent version! Update anyway? <a href=\"index.php?l=update&update=1&no_files=1\">Yes, but only my DB</a> - <a href=\"index.php?l=update&update=1&no_db=1\">Yes, but only the program files</a> - <a href=\"index.php?l=update&update=1\">Yes, update everything</a></p>";
				}
				echo "<p>You last updated on " . $db->format_date($db->get_option('last_updated')) . "</p>";
			}
		}
	?>
	
</div>

<?php
}
?>