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
	    editID('na','default_settings');
	});
-->
</script>

	
<form id="edit" onsubmit="return editID('na','default_settings');">
<input type="hidden" name="id" value="na" />

   	<div class="submit">
   		<img src="imgs/icon-save.png" width="16" height="16" border="0" onclick="editID('na','default_settings');" />
   		<div class="submit_split"></div>
   		<a href="http://www.doyoubananadance.com/Pages/Default-Setting-Hierarchy" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
   	</div>
		
	<?php
	
	if (! empty($_GET['category']) || $_GET['category'] == '0') {
		$type = "category";
		
		$base_category = $manual->base_category_id();
		if ($_GET['category'] == $base_category) {
			$act_id = '0';
		} else {
			$act_id = $_GET['category'];
		}
		
		echo "
	<h1>Default New Page Settings For This Category</h1>
	<p class=\"attention\">These settings are applied to all new pages created within this category unless overwritten by specific page settings.</p>
		";
		
	} else {
		$type = "article";
		$act_id = "d";
		
		echo "
	<h1>Page Settings</h1>
		";
	}
	
	?>
	

   				<label>Apply These Settings To:</label>
				<ul class="option_list" id="SD1">
					<li class="checked">
						<input type="radio" name="overwrite" value="1" checked="checked" /> New pages only
					</li>
					<li>
						<input type="radio" name="overwrite" value="3" /> New pages &amp; existing pages in this category
					</li>
					<li>
						<input type="radio" name="overwrite" value="2" /> New pages, existing pages in this category, and existing pages in sub-categories.
					</li>
				</ul>
		
	
	
	   		<script language="JavaScript" src="<?php echo URL ?>/js/admin_fields.js"></script> 
			<ul id="inner_page_tabs_top">
				<li id="litabmeta" class="on"><a href="#" onClick="swapTab('meta');return false;">Meta Tags</a></li>
				<li id="litablook"><a href="#" onClick="swapTab('look');return false;">Look and Feel</a></li>
				<li id="litabaccess"><a href="#" onClick="swapTab('access');return false;">Access Controls</a></li>
				<li id="litabcomments"><a href="#" onClick="swapTab('comments');return false;">Comments</a></li>
				<li id="litabother"><a href="#" onClick="swapTab('other');return false;">Other</a></li>
				<li id="litabpreformat"><a href="#" onClick="swapTab('preformat');return false;">Preset Content</a></li>
				<li id="litabstrip_privs"><a href="#" onClick="swapTab('strip_privs');return false;">Strip Privileges</a></li>
			</ul>
			
			<div class="home_box" style="margin-top:-1px;"><div class="pad12">
		
	
	<?php
	
	include "default_settings_pages.php";
	
	?>
	
				<!-- Start tab -->
				<div id="tabstrip_privs" style="display:none;">
				
				<p>By ticking any checkbox below, you will remove that user type's privileges in this category.</p>
				
				<table border="0"><thead><tr>
				<th>User Type</th>
				<th>Remove Privileges</th>
				</tr></thead>
				<tbody>
				<?php
				
		   		$q = "SELECT `id`,`name` FROM `" . TABLE_PREFIX . "user_types` ORDER BY `name` ASC";
		   		$results = $db->run_query($q);
		   		while ($row = mysql_fetch_array($results)) {
		   			$check_group_stripped = $manual->check_usertype_stripped($_GET['category'],$row['id'],'all');
		   			if ($check_group_stripped == '1') {
		   				$checked = ' checked="checked"';
		   			} else {
		   				$checked = '';
		   			}
					echo "<tr>
					<td>" . $row['name'] . "</td>
					<td><input type=\"checkbox\" name=\"strip_" . $row['id'] . "\" value=\"1\" $checked /></td>
					</tr>";
				}
				
				?>
				</tbody>
				</table>
				
				</div>
	
			</div></div>

</form>

	
<?php
}
?>