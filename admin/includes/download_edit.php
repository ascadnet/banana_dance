<?php

if ($privileges['upload_files'] != "1") {
	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
} else {

   	if (empty($_GET['id'])) {
		$db->show_inline_error('Does not exist.','1');
   	} else {
   		$dl = $db->get_file_info('','',$_GET['id']);
   		
?>


		
   	<div class="submit">
   		<img src="imgs/icon-save.png" width="16" height="16" border="0" class="icon" />
   		<a href="http://www.doyoubananadance.com/Media-Library/Downloadable-Content" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
   	</div>
   	
	<form id="edit" method="post" action="functions/ajax.php" enctype="multipart/form-data">
	<input type="hidden" name="id" value="<?php echo $dl['id']; ?>" />
	<input type="hidden" name="action" value="edit_dl" />

	<h1>Editing Download</h1>

	<div class="col_left"><div class="col_pad">
		
		<h2>Replace File</h2>
		<input type="file" name="file" id="file_input" />
		<p class="small">The extension of the replacement file must match the extension of the current file (<?php echo $dl['ext']; ?>)! Any uploaded file will be renamed to "<?php echo $dl['name']; ?>".</p>
		
		<h2>File Overview</h2>
		<label>Filename</label> <?php echo $dl['name']; ?>
		<label>Location</label> <a href="<?php echo $dl['url']; ?>"><?php echo $dl['url']; ?></a>
		<label>Path</label> <?php echo $dl['path']; ?>
		<label>Extension</label> <?php echo $dl['ext']; ?>
	
	</div></div>
	<div class="col_right"><div class="col_pad">

		<h2>Options</h2>
		
		<label>Limit each person's downloads?</label>
		<input type="text" name="limit" value="<?php echo $dl['limit'] ?>" style="width:60px;" />
		<p class="small" style="margin-top:2px;">Set to "0" (zero) for no limit, otherwise input the max number of times each user can download this file.</p>

		<label>Require login to download?</label>
		<input type="radio" name="login" value="1"<?php if ($dl['login'] == "1") { echo " checked=\"checked\""; } ?> /> Yes <input type="radio" name="login" value="0"<?php if ($dl['login'] != "1") { echo " checked=\"checked\""; } ?> /> No
		<p class="small" style="margin-top:2px;">Set to "Yes" if you would like users to have to be logged in to download this file.</p>

	</div></div>
	<div class="clear"></div>

</form>

<?php
	}
}
?>