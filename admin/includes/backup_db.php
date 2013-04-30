<?php

if ($privileges['is_admin'] != "1") {
	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
} else {
	
?>

<div id="content_overlay">

   	<div class="submit">
   		<a href="http://www.bananadance.org/Product-Manual/" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
   	</div>
   	
	<h1>Database and Theme Backup</h1>
	
	<?php
	
	$backup = new backup;
	$backup->backup_db('1');
	$backup->zip_theme();
	
	echo "<p class=\"highlight\">Backup complete!<br />Database tables have been saved to the \"generated/db_backup\" folder.<br />Theme files have been saved to the \"generated/theme_backup\" folder.</p>";
	
	?>
	
</div>

<?php
}
?>