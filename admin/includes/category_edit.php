<?php

/*

	REMOVED ON 26 SEP 2011

	<h2>Article Options</h2>
	<label>Allow Article Creation by Anyone?</label>
	<input type="radio" name="allow_article_creation" value="1"<?php if ($category['allow_article_creation'] == "1") { echo " checked=\"checked\""; } ?> /> Yes <input type="radio" name="allow_article_creation" value="0"<?php if ($category['allow_article_creation'] != "1") { echo " checked=\"checked\""; } ?> /> No
		

*/

if ($privileges['can_alter_categories'] != "1") {

	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
	
} else {

		if (empty($_GET['id'])) { $_GET['id'] = 'base'; }
		$category = $manual->get_category($_GET['id']);
		
		if (empty($category['id'])) {
			$db->admin_inline_error('Category does not exist.');
		} else {
		
			$link = $manual->prepare_link('',$category['id'],'');
		
?>

<script>
<!--

	$(function() {
		$("#sortable").sortable({ opacity: 0.4, cursor: 'move'});
		$("#sortableA").sortable({ opacity: 0.4, cursor: 'move'});
	});

	
	$(document).ready(function() {
		$("input[name=public]").click(function() {
			if ($('input[name=public]:checked').val() == '2') {
				$('#article_user_access').fadeIn('300');
			} else {
				$('#article_user_access').fadeOut('300');
			}
		});
	});
	
	// --------------------------------------------
	//	CTRL-S Saves a Form
	$.ctrl('S', function() {
	    saveCategoryChanges('<?php echo $category['id']; ?>');
	});
	
	
	//	For suggestions
	var current_function = 'category_permissions';
	var additional = '<?php echo $category['id']; ?>';
-->
</script>
<script type="text/javascript" src="<?php echo URL; ?>/js/suggest.js"></script>


<div class="submit">
	<img src="imgs/icon-save.png" width="16" height="16" border="0" onclick="saveCategoryChanges('<?php echo $category['id']; ?>');" />
   	<div class="submit_split"></div>
   	<a href="http://www.doyoubananadance.com/Pages/Access-Controls" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
   	<div class="submit_split"></div>
   	<a href="#" onClick="deleteID('categories','<?php echo $category['id']; ?>','category_<?php echo $category['id']; ?>,ex_<?php echo $category['id']; ?>');return false;"><img src="imgs/icon-delete.png" border="0" width="16" height="16" alt="Delete this item" title="Delete this item" /></a>
</div>


<form id="edit" onsubmit="return saveCategoryChanges('<?php echo $category['id']; ?>');">
<input type="hidden" name="id" value="<?php echo $category['id']; ?>" />

	<div id="actions_right">
		<ul>
			<li><a href="<?php echo $link; ?>">View</a></li>
			<li><a href="<?php echo $link; ?>76ea0bebb3c22822b4f0dd9c9fd021c5">Create Page</a></li>
			<li><a href="index.php?l=defaults&category=<?php echo $category['id']; ?>">Default Page Settings</a></li>
		</ul>
	</div>
	
	<h1>Managing Category (<?php echo $category['name']; ?>)</h1>

		<h2>Category Basics</h2>
			
		<label>Title</label>
		<?php
		if ($category['base'] == "1") {
			echo "
			<input type=\"text\" name=\"nameABS\" style=\"width:97%;\" disabled=\"disabled\" value=\"Base Category\" />
			<input type=\"hidden\" name=\"name\" value=\"Home\" />
			";
		} else {
			echo "<input type=\"text\" name=\"name\" style=\"width:97%;\" value=\"" . $category['name'] . "\" />";
		}
		?>
		
		
		<?php
		if ($category['base'] != "1") {
		?>
		<label>Sub-Category Of</label>
		<select name="subcat" id="subcat" style="width:97%;">
		<?php
		$categories = $manual->category_select($category['subcat']);
		echo $categories;
		?>
		</select>
		<?php
		}
		?>
		
		
		<h2>Access Controls</h2>
		
		<?php
		if ($category['base'] == "1") {
			echo "<p class=\"attention\">To make your website private, set the access controls below. If private, users will have to log in to use the site. User profiles will also be set to private.</p>";
		}
		?>
		
		<label>Category Status</label>
		<ul class="option_list" id="A">
			<li<?php if ($category['public'] == "1") { echo " class=\"selected\""; } ?>>
				<input type="radio" name="public" value="1"<?php if ($category['public'] == "1") { echo " checked=\"checked\""; } ?> /> Public<span class="help" id="h-2">(?)</span><div class="help_bubble" id="h-2b"><div class="hbpad">Makes the page open to all users and visitors.</div></div>
			</li>
			<li<?php if ($category['public'] == "0") { echo " class=\"selected\""; } ?>>
				<input type="radio" name="public" value="0"<?php if ($category['public'] == "0") { echo " checked=\"checked\""; } ?> /> Private<span class="help" id="h-4">(?)</span><div class="help_bubble" id="h-4b"><div class="hbpad">Limits visibility to administrators and user types with access to private pages only.</div></div>
			</li>
			
		<?php
		if ($category['base'] != "1") {
		?>
		
			<li<?php if ($category['public'] == "2") { echo " class=\"selected\""; } ?>>
				<input type="radio" name="public" value="2"<?php if ($category['public'] == "2") { echo " checked=\"checked\""; } ?> /> Restricted Access<span class="help" id="h-3">(?)</span><div class="help_bubble" id="h-3b"><div class="hbpad">Limits access to specific users and user types. You can set access controls for pages with categories as well.</div></div>
			</li>
			<li<?php if ($category['public'] == "3") { echo " class=\"selected\""; } ?>>
				<input type="radio" name="public" value="3"<?php if ($category['public'] == "3") { echo " checked=\"checked\""; } ?> /> Undergoing Maintenance<span class="help" id="h-5">(?)</span><div class="help_bubble" id="h-5b"><div class="hbpad">If selected, visitors will be shown a temporary maintenance message rather than the standard page.</div></div>
			</li>
		
		<?php
		}
		?>
		
		</ul>

		<div id="article_user_access" style="display:<?php
			if ($category['public'] == '1') { echo "none"; }
			else { echo "block"; }
		?>;">
		
		
			<h2>Access List</h2>
			
			<p class="attention">Individual page settings take precedence over category settings. So if there is a "Restricted Access" page in this "Restricted Access" category, and "userX" has access to the category but not that page, that user will NOT be able to see that page.</p>
			
			<div class="home_box_lg">
			<ul class="user_permission_list" id="user_permission_list">
			<?php
	   			$user_table = TABLE_PREFIX . "users";
	   			$mysql_table = TABLE_PREFIX . "user_permissions";
	   			echo "<li id=\"add_user\"><b>Grant a user/user type access:</b> <input type=\"text\" id=\"add_user_field\" name=\"add_user\" onkeyup=\"suggest('" . $user_table . "',this.value,'username','add_user_field','id','username','article_permissions');\" style=\"width: 200px;\" /></li>";
	   			echo "<li><img src=\"imgs/icon-usergroup.png\" width=16 height=16 border=0 alt=\"User Type\" title=\"User Type\" class=\"icon\" />Administrators and owner.</li>";
	   			$found_user = '0';
	   			$q = "SELECT `id`,`user_id`,`user_type` FROM `" . TABLE_PREFIX . "user_permissions` WHERE `category`='" . $category['id'] . "'";
	   			$users = $db->run_query($q);
	   			if ($users) {
		   			while ($row = mysql_fetch_array($users)) {
		   				if (! empty($row['user_id'])) {
			   				$username = $session->get_username_from_id($row['user_id']);
			   				echo "<li id=\"" . $row['id'] . "\"><a href=\"index.php?l=users_edit&id=" . $row['user_id'] . "\"><img src=\"imgs/icon-user.png\" width=16 height=16 border=0 alt=\"User\" title=\"User\" class=\"icon\" />" . $username . "</a><div class=\"icon_float_right\"><a href=\"#\" onClick=\"deleteID('$mysql_table','" . $row['id'] . "');return false;\"><img src=\"imgs/icon-delete.png\" border=\"0\" alt=\"Delete\" title=\"Delete\" /></a></div></li>";
		   				} else {
			   				$typename = $session->get_usertype_settings($row['user_type'],'name');
			   				echo "<li id=\"" . $row['id'] . "\"><a href=\"index.php?l=users_edit&id=" . $row['user_type'] . "\"><img src=\"imgs/icon-usergroup.png\" width=16 height=16 border=0 alt=\"User Type\" title=\"User Type\" class=\"icon\" />" . $typename['name'] . "</a><div class=\"icon_float_right\"><a href=\"#\" onClick=\"deleteID('$mysql_table','" . $row['id'] . "');return false;\"><img src=\"imgs/icon-delete.png\" border=\"0\" alt=\"Delete\" title=\"Delete\" class=\"icon_nopad\" /></a></div></li>";
		   				}
		   				$found_user++;
		   			}
	   			}
			?>
			</ul>
			<div class="clear"></div>
		</div>
		</div>
		
		
		<h2>Default Page Settings</h2>
		<p>Click on "Default Page Settings" above (below the banana) to alter the default options applied to new pages created in this category, and/or to apply new settings to all existing pages in this category.</p>

</form>

<?php
}
}
?>