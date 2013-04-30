<?php

if ($privileges['can_create_categories'] != "1") {
	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
} else {

?>

<script>
<!--
	// --------------------------------------------
	//	CTRL-S Saves a Form
	$.ctrl('S', function() {
	    saveCategoryChanges('new');
	});
-->
</script>


<div class="submit">
	<img src="imgs/icon-save.png" width="16" height="16" border="0" onclick="saveCategoryChanges('new');" />
   	<div class="submit_split"></div>
   	<a href="http://www.doyoubananadance.com/Pages/Categories" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
</div>

<form id="edit" onsubmit="return saveCategoryChanges('new');">
<input type="hidden" name="id" value="new" />

	<h1>Create a New Category</h1>

	<h2>Category Basics</h2>
		
	<label>Category Name</label>
	<input type="text" name="name" style="width:97%;" value="" />
		
	<label>Sub-Category Of</label>
	<select name="subcat" id="subcat" style="width:97%;">
	<?php
	$categories = $manual->category_select($_GET['id']);
	echo $categories;
	?>
	</select>
	
	<h2>Access Controls</h2>
	
	<p>Access settings and controls can be established once the category has been created.</p>

</form>

<?php
}
?>