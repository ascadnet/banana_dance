<?php

if ($privileges['is_admin'] != "1") {
	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
} else {

	if (! intval($_GET['id'])) {
		$this_data = $session->get_user_data($_GET['id'],'','1');
	} else {
		$this_data = $session->get_user_data('',$_GET['id'],'1');
	}
	if (empty($this_data['id'])) {
		$db->show_inline_error('Cannot find user.','1');
	} else {

?>

<script>
<!--
	// --------------------------------------------
	//	CTRL-S Saves a Form
	$.ctrl('S', function() {
	    editID('<?php echo $this_data['id']; ?>');
	});
-->
</script>

   	<div class="submit">
		<img src="imgs/icon-save.png" width="16" height="16" border="0" onclick="editID('<?php echo $this_data['id']; ?>');" />
		<div class="submit_split"></div>
   		<a href="http://www.doyoubananadance.com/Users/" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
   	</div>
			
	<div id="actions_right">
		<ul>
			<li><a href="<?php echo URL; ?>/user/<?php echo $this_data['username']; ?>/public" target="_blank">View Public Profile</a></li>
		</ul>
	</div>
	
	<h1>Editing User</h1>
	
	<form id="edit" onsubmit="return editID('<?php echo $this_data['id']; ?>');">
	<input type="hidden" name="id" value="<?php echo $this_data['id']; ?>" />
	<input type="hidden" name="action" value="add_user" />
	
			<h2>General Information</h2>
			<div class="col50">
	   			<label>Username<span class="req">*</span></label>
	   			<input type="text" name="username" style="width:90%;" tabindex="1" value="<?php echo $this_data['username']; ?>" />
	   			
	   			<label>Name</label>
	   			<input type="text" name="name" style="width:90%;" tabindex="3" value="<?php echo $this_data['name']; ?>" />
			</div>
			<div class="col50">
	   			<label>E-Mail<span class="req">*</span></label>
	   			<input type="text" name="email" style="width:97%;" tabindex="2" value="<?php echo $this_data['email']; ?>" />
			
	   			<label>User Type<span class="req">*</span></label>
				<select name="type" style="width:90%;" tabindex="6">
				<?php
				echo $admin->user_types_select($this_data['type']);
				?>
				</select>
			</div>
			<div class="clear"></div>
			
			
			<h2>Change Password</h2>
			
			<div class="col50">
	   			<label>Password<span class="help" id="h-1">(?)</span><div class="help_bubble" id="h-1b"><div class="hbpad">If you would like to update this user's password, do so below. Otherwise leave these fields blank to not alter the password.</div></div></label>
	   			<input type="password" name="pass" style="width:90%;" tabindex="4" />
	   			<p class="field_desc">Update password.</p>
			</div>
			<div class="col50">
	   			<label>Repeat Password</label>
	   			<input type="password" name="pass1" style="width:97%;"  tabindex="5" />
	   			<p class="field_desc">Repeat password.</p>
			</div>
			<div class="clear"></div>


			<?php
			if (! empty($this_data['banned'])) {
				if ($this_data['banned'] == 'banned') {
					echo "<p class=\"attention\"><b>Banned</b><br />This user is banned from the website.</p>";
				} else {
					echo "<p class=\"attention\"><b>Temporarily Banned</b><br />This user is banned until " . $db->format_date($this_data['banned']) . ".</p>";
				}
			}
			?>
	
		<div class="clear"></div>
		
		<!--
			These represent the field sets currently in the database.
		-->
		
		<?php
		require PATH . "/includes/field.functions.php";
		$fields = new fields;
		$set_list = $admin->field_set_li('10002');
		?>
		
		<script>
		var setlist = '<?php echo trim(str_replace('"','',$set_list)); ?>';
		var user_id = '<?php echo $this_data['id']; ?>';
		</script>
		<script language="JavaScript" src="<?php echo URL ?>/js/admin_fields.js"></script> 
		<ul id="inner_page_tabs_top">
		<li id="litabstats" class="on"><a href="#" onClick="swapTab('stats');return false;">Statistics</a></li>
		<li id="litabbadges"><a href="#" onClick="swapTab('badges');return false;">Badges</a></li>
		<?php
		$field_sets = $fields->get_field_sets('10002');
		$final_sets = "";
		if (! empty($field_sets)) {
			$current = 0;
			foreach ($field_sets as $field_set_id) {
				$current++;
				// Style
				//if ($current == "1") { $display = "block"; $class = "on"; }
				//else { $display = "none"; $class = "off"; }
				$display = "none"; $class = "off";
				// Fieldset Information
				$set_information = $fields->field_set_data($field_set_id);
				$set_fields = $fields->generate_field_set($field_set_id,$set_information,$this_data,'1');
				echo "<li id=\"litab" . $field_set_id . "\" onMouseOver=\"showDel('" . $field_set_id . "');\" onMouseOut=\"hideDel('" . $field_set_id . "');\" class=\"$class\"><a href=\"#\" onClick=\"swapTab('" . $field_set_id . "');return false;\">" . $set_information['name'] . "</a><a href=\"#\" onClick=\"deleteFieldSet('10002','" . $field_set_id . "');\"><img src=\"imgs/icon-delete.png\" width=16 height=16 border=0 title=\"Remove Tab\" title=\"Remove Tab\" id=\"delete_tab" . $field_set_id . "\" style=\"display:none;\" class=\"icon_l\" /></a></li> ";
				// Field sets
				$final_sets .= "<div id=\"tab$field_set_id\" style=\"display:$display;\">";
				$final_sets .= $set_fields;
				$final_sets .= "</div>";
			}
		}
		?>
		<li id="litab_new"><a href="#" onClick="addFieldSet('10002');return false;"><img src="imgs/icon-add.png" width="16" height="16" border="0" alt="Add Set" title="Add Set" class="icon_nopad" /></a></li>
		</ul>
		
		<div class="clear"></div>
		<div class="home_box_lg" style="margin-top:-1px;"><div class="pad12">
			<?php
				echo $final_sets;
			?>
			<!--
			<div id="tab2" style="display:none;">
				<p>Tab 2</p>
			</div>
			-->
			
			<div id="tabstats">
				<div class="col50">
					
					<h3>General Statistics</h3>
					<ul class="user_permission_list">
						<li>
							<span class="stat_col_l">Joined</span>
							<span class="stat_col_r"><?php echo $db->format_date($this_data['joined']); ?></span>
						</li>
						<li>
							<span class="stat_col_l">Account Age</span>
							<span class="stat_col_r"><?php echo $this_data['time_member']; ?></span>
						</li>
						<li>
							<span class="stat_col_l">Logins</span>
							<span class="stat_col_r"><?php echo $this_data['logins']; ?></span>
						</li>
						<li>
							<span class="stat_col_l">Last Login</span>
							<span class="stat_col_r"><?php if ($this_data['last_login'] == 'n/a') { echo 'n/a'; } else { echo $db->format_date($this_data['last_login']); } ?></span>
						</li>
					</ul>
					
					
					<h3>Comments and Pages</h3>
					<ul class="user_permission_list">
						<li>
							<span class="stat_col_l"><a href="index.php?l=comments&filter=1&user=<?php echo $this_data['username']; ?>">Comments</a></span>
							<span class="stat_col_r"><?php if (empty($this_data['comments'])) { $this_data['comments'] = '0'; } echo $this_data['comments']; ?></span>
						</li>
						<li>
							<span class="stat_col_l"><a href="index.php?l=articles&filter=1&owner=<?php echo $this_data['username']; ?>">Pages</a></span>
							<span class="stat_col_r"><?php if (empty($this_data['articles'])) { $this_data['articles'] = '0'; } echo $this_data['articles']; ?></span>
						</li>
					</ul>
					
				</div>
			
				<div class="col50">
				
					<h3>User Score</h3>
					<ul class="user_permission_list">
						<li>
							<span class="stat_col_l">Score</span>
							<span class="stat_col_r"><?php echo $this_data['score']; ?></span>
						</li>
						<li>
							<span class="stat_col_l">Upvotes</span>
							<span class="stat_col_r"><?php if (empty($this_data['upvotes'])) { $this_data['upvotes'] = '0'; } echo $this_data['upvotes']; ?></span>
						</li>
						<li>
							<span class="stat_col_l">Downvotes</span>
							<span class="stat_col_r"><?php if (empty($this_data['downvotes'])) { $this_data['downvotes'] = '0'; } echo $this_data['downvotes']; ?></span>
						</li>
						<li>
							<span class="stat_col_l">Badges</span>
							<span class="stat_col_r"><?php if (empty($this_data['total_badges'])) { $this_data['total_badges'] = '0'; } echo $this_data['total_badges']; ?></span>
						</li>
						<li>
							<span class="stat_col_l">Points</span>
							<span class="stat_col_r"><input type="text" name="myScore" value="<?php echo $this_data['myScore']; ?>" style="width:100px;margin:0;" /></span>
						</li>
					<ul>
				</div>
				<div class="clear"></div>
				
			</div>
			
			
			<div id="tabbadges" style="display:none;">
			
				<h3>Give Badge</h3>
				<select id="badge_give" name="badge" style="width: 500px;"><option value=""></option><?php echo $admin->badge_list(); ?></select> <input type="button" value="Give" onclick="giveBadge();" />
			
				<h3>Existing Badges</h3>
				<?php echo $this_data['badges']; ?>
				<div id="new_badge"></div>
				
			</div>
			<div id="place_new_here" style="display:none;"></div>
		</div></div>
		
	</form>

<?php
	}
}
?>