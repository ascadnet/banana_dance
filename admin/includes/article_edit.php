<?php

if ($privileges['can_alter_articles'] != "1") {
	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
} else {

   	if (empty($_GET['id'])) {
		$db->show_inline_error('Does not exist.','1');
   	} else {
   		$article = $manual->get_article($_GET['id']);
   		$category = $manual->get_category($article['category']);
   		$link = $manual->prepare_link($article['id'],$article['category'],$article['name']);
?>


<script>
<!--
	// --------------------------------------------
	//	CTRL-S Saves a Form
	$.ctrl('S', function() {
	    saveChanges('<?php echo $article['id']; ?>');
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
	
	//	For suggestions
	var current_function = 'article_permissions';
	var additional = '<?php echo $article['id']; ?>';

-->
</script>
<script type="text/javascript" src="<?php echo URL; ?>/js/suggest.js"></script>

<div class="submit">
	<img src="imgs/icon-save.png" width="16" height="16" border="0" onClick="saveChanges('<?php echo $article['id']; ?>');" />
   	<div class="submit_split"></div>
   	<a href="http://www.doyoubananadance.com/Pages/Access-Controls" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
   	<div class="submit_split"></div>
   	<a href="#" onClick="deleteID('articles','<?php echo $article['id']; ?>','page_<?php echo $article['id']; ?>');return false;"><img src="imgs/icon-delete.png" border="0" width="16" height="16" alt="Delete this item" title="Delete this item" /></a>
</div>

<form id="edit" onsubmit="return saveChanges('<?php echo $article['id']; ?>');">
<input type="hidden" name="id" value="<?php echo $article['id']; ?>" />

	<div id="actions_right">
		<ul>
			<li><a href="<?php echo $link; ?>">View Online</a></li>
			<li><a href="<?php echo $link; ?>/de95b43bceeb4b998aed4aed5cef1ae7">Edit</a></li>
			<li><a href="#" onClick="return clearPage('<?php echo $article['id']; ?>');">Clear Content</a></li>
		</ul>
	</div>
	
	<h1>Page Settings (<?php echo $article['name']; ?>)</h1>
	
	<h2>Basic Information</h2>
	
	<div class="col50">
   		<ul class="user_permission_list">
   			<li>
   				<span class="stat_col_l">Created</span>
   				<span class="stat_col_r"><?php echo $db->format_date($article['created']); ?></span>
   			</li>
   			<li>
   				<span class="stat_col_l">Last Updated</span>
   				<span class="stat_col_r"><?php echo $db->format_date($article['last_updated']); ?></span>
   			</li>
   			<li>
   				<span class="stat_col_l">Owner</span>
   				<span class="stat_col_r"><a href="index.php?l=users_edit&id=<?php echo $article['owner']; ?>"><?php echo $article['owner']; ?></a></span>
   			</li>
   		</ul>
	</div>
	<div class="col50">
   		<ul class="user_permission_list">
   			<li>
   				<span class="stat_col_l">Views</span>
   				<span class="stat_col_r"><?php echo $article['views']; ?></span>
   			</li>
   			<li>
   				<span class="stat_col_l">Using Template</span>
   				<span class="stat_col_r"><?php
				if ($article['template'] != '0') {
					$template_info = $template->get_template_info('html',$article['template'],'0','title');
					echo "<a href=\"index.php?l=templates_html_edit&id=" . $article['template'] . "\">" . $template_info['title'] . "</a>";
				} else {
					echo "<a href=\"index.php?l=templates_html_edit&id=1\">Default Page Template</a>";
				}
				?></span>
   			</li>
   			<li>
   				<span class="stat_col_l">Comment Type</span>
   				<span class="stat_col_r"><?php
			if ($article['allow_comments'] == '1') {
				echo $article['comment_thread_style'];
			} else {
				echo "<i>Not permitted.</i>";
			}
			?></span>
   			</li>
   		</ul>
	</div>
	<div class="clear"></div>
	
	
	<h2>User Access Controls</h2>
	
		<label>Login Requirement<span class="help" id="h-1">(?)</span><div class="help_bubble" id="h-1b"><div class="hbpad">If set to "Yes", users will need to be logged in to view this page. Note that a page can be public and still require login, whereas non-public pages are off limits unless a user has been granted access below.</div></div></label>
		
		<ul class="option_list" id="A">
			<li<?php if ($article['login_to_view'] == "1") { echo " class=\"selected\""; } ?>>
				<input type="radio" name="login_to_view" value="1"<?php if ($article['login_to_view'] == "1") { echo " checked=\"checked\""; } ?> /> Yes 
			</li>
			<li<?php if ($article['login_to_view'] != "1") { echo " class=\"selected\""; } ?>>
				<input type="radio" name="login_to_view" value="0"<?php if ($article['login_to_view'] != "1") { echo " checked=\"checked\""; } ?> /> No
			</li>
		</ul>
		
		<label>Page Status</label>
		<ul class="option_list" id="B">
			<li<?php if ($article['public'] == "1") { echo " class=\"selected\""; } ?>>
				<input type="radio" name="public" value="1"<?php if ($article['public'] == "1") { echo " checked=\"checked\""; } ?> /> Public<span class="help" id="h-2">(?)</span><div class="help_bubble" id="h-2b"><div class="hbpad">Makes the page open to all users and visitors. <b>Note:</b> If you set "Login Requirement" to "Yes" above, even if a page is public, users will still need to log in to view the page.</div></div>
			</li>
			<li<?php if ($article['public'] == "2") { echo " class=\"selected\""; } ?>>
				<input type="radio" name="public" value="2"<?php if ($article['public'] == "2") { echo " checked=\"checked\""; } ?> /> Restricted Access<span class="help" id="h-3">(?)</span><div class="help_bubble" id="h-3b"><div class="hbpad">Limits access to specific users and user types. You can also set user access controls for entire categories by editing the category.</div></div>
			</li>
			<li<?php if ($article['public'] == "0") { echo " class=\"selected\""; } ?>>
				<input type="radio" name="public" value="0"<?php if ($article['public'] == "0") { echo " checked=\"checked\""; } ?> /> Private<span class="help" id="h-4">(?)</span><div class="help_bubble" id="h-4b"><div class="hbpad">Limits visibility to administrators, the page's creator, and user types with access to private pages only.</div></div>
			</li>
			<li<?php if ($article['public'] == "3") { echo " class=\"selected\""; } ?>>
				<input type="radio" name="public" value="3"<?php if ($article['public'] == "3") { echo " checked=\"checked\""; } ?> /> Undergoing Maintenance<span class="help" id="h-5">(?)</span><div class="help_bubble" id="h-5b"><div class="hbpad">If selected, visitors will be shown a temporary maintenance message rather than the standard page.</div></div>
			</li>
		</ul>
		
		
		<div id="article_user_access" style="display:<?php
			if ($article['public'] == '1') { echo "none"; }
			else { echo "block"; }
		?>;">
		
			<h2>Access List</h2>
			<div class="home_box_lg">
				<ul class="user_permission_list" id="user_permission_list">
				<?php
		   			$user_table = TABLE_PREFIX . "users";
		   			$mysql_table = TABLE_PREFIX . "user_permissions";
		   			echo "<li id=\"add_user\"><b>Grant a user/user type access:</b> <input type=\"text\" id=\"add_user_field\" name=\"add_user\" onkeyup=\"suggest('" . $user_table . "',this.value,'username','add_user_field','id','username','article_permissions');\" style=\"width: 200px;\" /></li>";
		   			echo "<li><img src=\"imgs/icon-usergroup.png\" width=16 height=16 border=0 alt=\"User Type\" title=\"User Type\" class=\"icon\" />Administrators and owner.</li>";
		   			$found_user = '0';
		   			$q = "SELECT `id`,`user_id`,`user_type` FROM `" . TABLE_PREFIX . "user_permissions` WHERE `permission`='" . $article['id'] . "'";
		   			$users = $db->run_query($q);
		   			if ($users) {
			   			while ($row = mysql_fetch_array($users)) {
			   				if (! empty($row['user_id'])) {
				   				$username = $session->get_username_from_id($row['user_id']);
				   				echo "<li id=\"" . $row['id'] . "\"><a href=\"index.php?l=users_edit&id=" . $row['user_id'] . "\"><img src=\"imgs/icon-user.png\" width=16 height=16 border=0 alt=\"User\" title=\"User\" class=\"icon\" />" . $username . "</a><div class=\"icon_float_right\"><a href=\"#\" onClick=\"deleteID('$mysql_table','" . $row['id'] . "');return false;\"><img src=\"imgs/icon-delete.png\" border=\"0\" alt=\"Delete\" title=\"Delete\" class=\"icon_nopad\" /></a></div></li>";
			   				} else {
				   				$typename = $session->get_usertype_settings($row['user_type'],'name');
				   				echo "<li id=\"" . $row['id'] . "\"><a href=\"index.php?l=user_types_edit&id=" . $row['user_type'] . "\"><img src=\"imgs/icon-usergroup.png\" width=16 height=16 border=0 alt=\"User Type\" title=\"User Type\" class=\"icon\" />" . $typename['name'] . "</a><div class=\"icon_float_right\"><a href=\"#\" onClick=\"deleteID('$mysql_table','" . $row['id'] . "');return false;\"><img src=\"imgs/icon-delete.png\" border=\"0\" alt=\"Delete\" title=\"Delete\" class=\"icon_nopad\" /></a></div></li>";
			   				}
			   				$found_user++;
			   			}
		   			}
				?>
				</ul>
				<div class="clear"></div>
			</div>
		</div>

</form>
		
<?php
	}
}
?>