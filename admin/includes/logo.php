<?php

if ($privileges['is_admin'] != "1") {
	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
} else {

?>

<script>
<!--
	// --------------------------------------------
	//	CTRL-S Saves a Form
	$.ctrl('S', function() {
	    document.forms["edit"].submit();
	});
-->
</script>

<form id="edit" action="functions/logo.php" method="post" enctype="multipart/form-data">

   	<div class="submit">
	   	<input type="image" src="imgs/icon-save.png" />
   		<div class="submit_split"></div>
   		<a href="http://www.doyoubananadance.com/Templates/Template-System" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
   	</div>
	
	<h1>Company Logo</h1>
	
	<h2>Current Logo</h2>
	<div id="current_logo">
	<?php
		$logo = $template->find_logo();
		if (! empty($logo)) {
			$company_name = $db->get_option('company_name');
			echo "<img src=\"" . $db->get_option('logo') . "\" alt=\"" . htmlspecialchars($company_name) . "\" title=\"" . htmlspecialchars($company_name) . "\" border=\"0\" /><br /><a href=\"#\" onclick=\"removeLogo();return false;\">Remove Logo</a>";
		} else {
			echo "<i>None... the program will display your website name (" . $db->get_option('site_name') . ") in plain text instead!</i>";
		}
	?>
	</div>
	
	
	
	<h2>Upload New Logo</h2>

	<label>Select an Image</label>
	<input type="file" name="logo" />
	<p class="field_desc">Your logo should be a PNG, JPG, or GIF file.</p>
	
	<label>Resize Image (Optional)</label>
	Width = <input type="text" name="width" style="width:80px;" />px x Height = <input type="text" name="height" style="width:80px;" />px
	<p class="field_desc">If you would like to resize to a specific width, height, or both (in pixels), do so above.<br />You <b>do not</b> have to input both dimensions: input one or both and let the program take care of the rest!</p>
	

</form>

<?php
}
?>