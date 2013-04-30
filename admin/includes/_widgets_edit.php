<?php

if ($privileges['is_admin'] != "1") {
	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
} else {

	$widget = $manual->widget_info($_GET['id']);
	$widoptions = unserialize($widget['options']);
	
?>

<script>
<!--
	// --------------------------------------------
	//	CTRL-S Saves a Form
	$.ctrl('S', function() {
	    editID('<?php echo $widget['id']; ?>');
	});
	var current_function = 'widget';
	
	$(document).ready(function() {
   		width = $('#right_side').width();
   		height = $('#right_side').height();
   		var adjust_width = width - 20;
      	$("textarea").cleditor({width:adjust_width, height:'300'});
   	});
   	
-->
</script>
<script type="text/javascript" src="<?php echo URL; ?>/js/suggest.js"></script>

<div id="content_overlay">

   	<div class="submit">
		<img src="imgs/icon-save.png" width="16" height="16" border="0" onclick="editID('<?php echo $widget['id']; ?>');" />
		<div class="submit_split"></div>
   		<a href="http://www.doyoubananadance.com/Pages/Widgets" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
   	</div>
   	
	<h1>Editing Widget</h1>
	
	<form id="edit" onsubmit="return editID('<?php echo $widget['id']; ?>');">
	<input type="hidden" name="id" value="<?php echo $widget['id']; ?>" />
	<input type="hidden" name="action" value="add_widget" />
			
	<div class="main_pad">
	
		<div class="col_left_sm"><div class="col_pad">
			
			<h2 style="margin-top:0px;">Basic Settings</h2>
			
    			<label>Name</label>
    			<input type="text" name="name" style="width:95%;" value="<?php echo $widget['name']; ?>" />
    			<p class="field_desc">Input a reference name for this widget.</p>
    			
    			<label>Type</label>
    			<ul id="type_ul" class="field_option">
    			<?php
    			if ($widget['type'] == '1') {
    				echo "<li id=\"type1\" class=\"selected\">Category Page Index</li>";
    			}
    			else if ($widget['type'] == '2') {
    				echo "<li id=\"type2\" class=\"selected\">Recent Comments to Page</li>";
    			}
    			else if ($widget['type'] == '3') {
    				echo "<li id=\"type3\" class=\"selected\">Custom HTML</li>";
    			}
    			else if ($widget['type'] == '4') {
    				echo "<li id=\"type4\" class=\"selected\">Recent Pages in Category</li>";
    			}
    			else if ($widget['type'] == '6') {
    				echo "<li id=\"type6\" class=\"selected\">Recent Users</li>";
    			}
    			else if ($widget['type'] == '7') {
    				echo "<li id=\"type7\" class=\"selected\">Image Gallery</li>";
    			}
    			?>
    			</ul>
    			<input type="hidden" id="type" name="type" value="<?php echo $widget['type']; ?>" />
			
		</div></div>
		<div class="col_right_sm white_box drop_shadow"><div class="col_pad">
			
			<?php
			
    			// -----------------------------------------------------------------------
    			//	Category article index
    			
    			if ($widget['type'] == '1') {
    			?>


			<!-- start:element -->
			<div id="type1_options">
				<h2>Category Page Index</h2>
				
		    			<label>Category</label>
					<select name="category_1" id="category_1" style="width:300px;">
					<?php
					$categories = $manual->category_select($widget['category']);
					echo $categories;
					?>
					</select>
		    			<p class="field_desc">Which category would you like to display pages for?</p>
		    			
		    			<label>Columns?</label>
   	    				<input type="text" name="columns_1" id="columns_1" value="<?php echo $widoptions['4']; ?>" style="width:100px;" maxlength="1" />
		    			<p class="small" style="margin-top:2px;">If you would like to the display the index in columns, indicate how many above.</p>
		    			
		    			<label>Include sub-categories?</label>
   	    				<input type="radio" name="sub_categories_1" id="sub_categories_1" value="1"<?php if ($widoptions['1'] == '1') { echo " checked=\"checked\""; } ?> /> Yes <input type="radio" name="sub_categories_1" id="sub_categories_1" value="0"<?php if ($widoptions['1'] != '1') { echo " checked=\"checked\""; } ?> /> No
		    			<p class="small" style="margin-top:2px;">Would you like to display sub-categories pages within this category?</p>
		    			
		    			<label>Limit Results</label>
		    			<input type="text" name="limit_1" id="limit_1" style="width:50px;" value="<?php echo $widoptions['0']; ?>" />
		    			<p class="field_desc">How many pages would you like to display?</p>

		    			<label>Order By</label>
		    			<select name="order_1" id="order_1" style="width:300px;">
		    			<option value="order"<?php if ($widoptions['2'] == 'order') { echo " selected=\"selected\""; } ?>>Order in Category</option>
		    			<option value="name"<?php if ($widoptions['2'] == 'name') { echo " selected=\"selected\""; } ?>>Name</option>
		    			<option value="views"<?php if ($widoptions['2'] == 'views') { echo " selected=\"selected\""; } ?>>Views</option>
		    			<option value="created"<?php if ($widoptions['2'] == 'created') { echo " selected=\"selected\""; } ?>>Date Created</option>
		    			<option value="last_updated"<?php if ($widoptions['2'] == 'last_updated') { echo " selected=\"selected\""; } ?>>Date Last Updated</option>
		    			<option value="score"<?php if ($widoptions['2'] == 'score') { echo " selected=\"selected\""; } ?>>Score</option>
		    			</select>
		    			<p class="field_desc">How should pages be ordered?</p>
		    			
		    			<label>Order Direction</label>
		    			<select name="dir_1" id="dir_1" style="width:300px;">
		    			<option value="ASC"<?php if ($widoptions['3'] == 'ASC') { echo " selected=\"selected\""; } ?>>Ascending Order</option>
		    			<option value="DESC"<?php if ($widoptions['3'] == 'DESC') { echo " selected=\"selected\""; } ?>>Descending Order</option>
		    			</select>
		    			<p class="field_desc">How should pages be ordered?</p>

	    			<h2>Widget HTML</h2>
	    			<p>Widget will be formatted according to a standard category tree listing.</p>
	    			
			</div>
			<!-- close:element -->
    			
    			<?php
    			}
    			
    			// -----------------------------------------------------------------------
    			//	Recent comments
    			
    			else if ($widget['type'] == '2') {
    			?>

			<!-- start:element -->
			<div id="type2_options">
				<h2>Recent Comments to Page</h2>
				
		    			<label>Page</label>
		    			<?php
		    				$article_name = $manual->get_article($widoptions['1'],'1','name');
		    			?>
		    			<input type="text" name="article_name" id="article_name_pos" onkeyup="suggest('<?php echo TABLE_PREFIX; ?>articles',this.value,'name','article_name_pos','id','name');" value="<?php echo $article_name['name']; ?>" style="width:97%;" />
					<input type="hidden" name="article_2" id="article_name_pos_val" value="<?php echo $widoptions['1']; ?>" />
		    			
		    			<p class="field_desc">Display comments from this article.</p>
		    			
		    			<label>Limit Results</label>
		    			<input type="text" name="limit_2" id="limit_2" style="width:50px;" value="<?php echo $widoptions['0']; ?>" />
		    			<p class="field_desc">How many comments would you like to display?</p>
		    			
		    			<label>Shorten Long Comments</label>
		    			<input type="text" name="trim_2" id="trim_2" style="width:50px;" value="100" value="<?php echo $widoptions['4']; ?>" />
		    			<p class="field_desc">For long comments, shorten the comment after how many characters?</p>
	    		
		    			<label>Order By</label>
		    			<select name="order_2" id="order_2" style="width:300px;">
		    			<option value="date"<?php if ($widoptions['2'] == 'date') { echo " selected=\"selected\""; } ?>>Date Posted</option>
		    			<option value="score"<?php if ($widoptions['2'] == 'score') { echo " selected=\"selected\""; } ?>>Score</option>
		    			</select>
		    			<p class="field_desc">How should comments be ordered?</p>
		    			
		    			<label>Order Direction</label>
		    			<select name="dir_2" id="dir_2" style="width:300px;">
		    			<option value="ASC"<?php if ($widoptions['3'] == 'ASC') { echo " selected=\"selected\""; } ?>>Ascending Order</option>
		    			<option value="DESC"<?php if ($widoptions['3'] == 'DESC') { echo " selected=\"selected\""; } ?>>Descending Order</option>
		    			</select>
		    			<p class="field_desc">How should comments be ordered?</p>
	  
	    			
	    			<h2>Widget List HTML</h2>

<textarea name="html_2" id="html_2" style="width:95%;height:75px;" cols="1" rows="1">
<?php echo $widget['html']; ?>
</textarea>

		 		<table cellspacing=0 callpadding=0 border=0 class="callers"><thead><tr>
		 		<th>Caller Tag</th>
		 		<th>Description</th>
		 		</tr></thead><tbody>
   	      		<tr>
   	      		<td valign="top"><a href="#" onClick="addCaller('html_2','%entries%');return false;">%entries%</td>
   	      		<td valign="top">List entries, as created using the "Widget Entry HTML" content below.</td>
   	      		</tr>
		 		</tbody></table>
	    			
	    			<h2>Widget Entry HTML</h2>
<textarea name="html_insert_2" id="html_insert_2" style="width:95%;height:150px;" cols="1" rows="1">
<?php echo $widget['html_insert']; ?>
</textarea>
		 		<table cellspacing=0 callpadding=0 border=0 class="callers"><thead><tr>
		 		<th>Caller Tag</th>
		 		<th>Description</th>
		 		</tr></thead><tbody>
   	      		<tr>
   	      		<td valign="top"><a href="#" onClick="addCaller('html_insert_2','%article_title%');return false;">%article_title%</td>
   	      		<td valign="top">Page name to which the comment belongs.</td>
   	      		</tr>
   	      		<tr>
   	      		<td valign="top"><a href="#" onClick="addCaller('html_insert_2','%article_link%');return false;">%article_link%</td>
   	      		<td valign="top">Fully formatted HTML link to the article with article name included. Example:<br />&lt;a href="<?php echo URL; ?>/article/link/here">Page Name&lt;/a></td>
   	      		</tr>
   	      		<tr>
   	      		<td valign="top"><a href="#" onClick="addCaller('html_insert_2','%comment_date%');return false;">%comment_date%</td>
   	      		<td valign="top">Date comment was posted.</td>
   	      		</tr>
   	      		<tr>
   	      		<td valign="top"><a href="#" onClick="addCaller('html_insert_2','%comment_score%');return false;">%comment_score%</td>
   	      		<td valign="top">Comment score.</td>
   	      		</tr>
   	      		<tr>
   	      		<td valign="top"><a href="#" onClick="addCaller('html_insert_2','%comment%');return false;">%comment%</td>
   	      		<td valign="top">Comment content.</td>
   	      		</tr>
   	      		<tr>
   	      		<td valign="top"><a href="#" onClick="addCaller('html_insert_2','%comment_type%');return false;">%comment_type%</td>
   	      		<td valign="top">Comment type name.</td>
   	      		</tr>
   	      		<tr>
   	      		<td valign="top"><a href="#" onClick="addCaller('html_insert_2','%username%');return false;">%username%</td>
   	      		<td valign="top">Username of user who posted this comment.</td>
   	      		</tr>
   	      		<tr>
   	      		<td valign="top"><a href="#" onClick="addCaller('html_insert_2','%user_link%');return false;">%user_link%</td>
   	      		<td valign="top">Fully formatted HTML link to the user's page with username included. Example:<br />&lt;a href="<?php echo URL; ?>/user/Username">Username&lt;/a></td>
   	      		</tr>
   	      		<tr>
   	      		<td valign="top">%<i>field_name_here</i>%</td>
   	      		<td valign="top">You can also include any field associated with the user who posted the comment.</td>
   	      		</tr>
		 		</tbody></table>
				
			</div>
			<!-- close:element -->
    			
    			<?php
    			}
    			
    			// -----------------------------------------------------------------------
    			//	Custom HTML
    			
    			else if ($widget['type'] == '3') {
    			?>

			<!-- start:element -->
			<div id="type3_options">
				<h2>Custom HTML</h2>
   			
   	    			<label>Content Format Type</label>
   	    			<input type="radio" name="format_3" id="format_3" value="0" onclick="show('format_guide_3');"<?php if ($widoptions['0'] != '1') { echo " checked=\"checked\""; } ?> /> Wiki Syntax <input type="radio" name="format_3" id="format_3" value="1"<?php if ($widoptions['0'] == '1') { echo " checked=\"checked\""; } ?> onclick="hide('format_guide_3');" /> Full HTML
		    		<p class="small" style="margin-top:2px;">Would you like to compose this in full HTML or using simplified wiki syntax?</p>
		    		
	    			<h2>Widget List HTML</h2>
				<textarea name="html_3" id="html_3" style="width:95%;height:300px;" cols="1" rows="1"><?php echo $widget['html']; ?></textarea>
				
			</div>
			<!-- close:element -->
    			
    			<?php
    			}
    			
    			// -----------------------------------------------------------------------
    			//	Recent Pages
    			
    			else if ($widget['type'] == '4') {
    			?>

			<!-- start:element -->
			<div id="type4_options">
				<h2>Recent Pages in Category</h2>
				
		    			<label>Category</label>
					<select name="category_4" id="category_4" style="width:300px;">
					<?php
					$categories = $manual->category_select($widget['category']);
					echo $categories;
					?>
					</select>
		    			<p class="field_desc">Which category would you like to display pages for?</p>
		    			
		    			<label>Limit Results</label>
		    			<input type="text" name="limit_4" id="limit_4" style="width:50px;" value="<?php echo $widoptions['0']; ?>" />
		    			<p class="field_desc">How many pages would you like to display?</p>

		    			<label>Order By</label>
		    			<select name="order_4" id="order_4" style="width:300px;">
		    			<option value="order"<?php if ($widoptions['2'] == 'order') { echo " selected=\"selected\""; } ?>>Order in Category</option>
		    			<option value="name"<?php if ($widoptions['2'] == 'name') { echo " selected=\"selected\""; } ?>>Name</option>
		    			<option value="views"<?php if ($widoptions['2'] == 'views') { echo " selected=\"selected\""; } ?>>Views</option>
		    			<option value="created"<?php if ($widoptions['2'] == 'created') { echo " selected=\"selected\""; } ?>>Date Created</option>
		    			<option value="last_updated"<?php if ($widoptions['2'] == 'last_updated') { echo " selected=\"selected\""; } ?>>Date Last Updated</option>
		    			<option value="score"<?php if ($widoptions['2'] == 'score') { echo " selected=\"selected\""; } ?>>Score</option>
		    			</select>
		    			<p class="field_desc">How should pages be ordered?</p>
		    			
		    			<label>Order Direction</label>
		    			<select name="dir_4" id="dir_4" style="width:300px;">
		    			<option value="ASC"<?php if ($widoptions['3'] == 'ASC') { echo " selected=\"selected\""; } ?>>Ascending Order</option>
		    			<option value="DESC"<?php if ($widoptions['3'] == 'DESC') { echo " selected=\"selected\""; } ?>>Descending Order</option>
		    			</select>
		    			<p class="field_desc">How should pages be ordered?</p>

	    			
	    			<h2>Widget List HTML</h2>

<textarea name="html_4" id="html_4" style="width:95%;height:75px;" cols="1" rows="1">
<?php echo $widget['html']; ?>
</textarea>

		 		<table cellspacing=0 callpadding=0 border=0 class="callers"><thead><tr>
		 		<th>Caller Tag</th>
		 		<th>Description</th>
		 		</tr></thead><tbody>
   	      		<tr>
   	      		<td valign="top"><a href="#" onClick="addCaller('html_4','%entries%');return false;">%entries%</td>
   	      		<td valign="top">List entries, as created using the "Widget Entry HTML" content below.</td>
   	      		</tr>
		 		</tbody></table>
	    			
	    			<h2>Widget Entry HTML</h2>
<textarea name="html_insert_4" id="html_insert_4" style="width:95%;height:150px;" cols="1" rows="1">
<?php echo $widget['html_insert']; ?>
</textarea>
		 		<table cellspacing=0 callpadding=0 border=0 class="callers"><thead><tr>
		 		<th>Caller Tag</th>
		 		<th>Description</th>
		 		</tr></thead><tbody>
   	      		<tr>
   	      		<td valign="top"><a href="#" onClick="addCaller('html_insert_4','%article_title%');return false;">%article_title%</td>
   	      		<td valign="top">Page name to which the comment belongs.</td>
   	      		</tr>
   	      		<tr>
   	      		<td valign="top"><a href="#" onClick="addCaller('html_insert_4','%article_link%');return false;">%article_link%</td>
   	      		<td valign="top">Fully formatted HTML link to the article with article name included. Example:<br />&lt;a href="<?php echo URL; ?>/article/link/here">Page Name&lt;/a></td>
   	      		</tr>
   	      		<tr>
   	      		<td valign="top"><a href="#" onClick="addCaller('html_insert_2','%article_snippet%');return false;">%article_snippet%</td>
   	      		<td valign="top">The first paragraph, unformatted, of this page.</td>
   	      		</tr>
   	      		<tr>
   	      		<td valign="top"><a href="#" onClick="addCaller('html_insert_4','%article_description%');return false;">%article_description%</td>
   	      		<td valign="top">Page meta description.</td>
   	      		</tr>
   	      		<tr>
   	      		<td valign="top"><a href="#" onClick="addCaller('html_insert_4','%article_category%');return false;">%article_category%</td>
   	      		<td valign="top">Page category ID.</td>
   	      		</tr>
   	      		<tr>
   	      		<td valign="top"><a href="#" onClick="addCaller('html_insert_4','%article_created%');return false;">%article_created%</td>
   	      		<td valign="top">Date article was created.</td>
   	      		</tr>
   	      		<tr>
   	      		<td valign="top"><a href="#" onClick="addCaller('html_insert_4','%article_score%');return false;">%article_score%</td>
   	      		<td valign="top">Page's score.</td>
   	      		</tr>
   	      		<tr>
   	      		<td valign="top"><a href="#" onClick="addCaller('html_insert_4','%article_comments%');return false;">%article_comments%</td>
   	      		<td valign="top">Total comments posted to this article.</td>
   	      		</tr>
   	      		<tr>
   	      		<td valign="top"><a href="#" onClick="addCaller('html_insert_4','%username%');return false;">%username%</td>
   	      		<td valign="top">Username of user who created the article.</td>
   	      		</tr>
   	      		<tr>
   	      		<td valign="top"><a href="#" onClick="addCaller('html_insert_4','%user_link%');return false;">%user_link%</td>
   	      		<td valign="top">Fully formatted HTML link to the user's page with username included. Example:<br />&lt;a href="<?php echo URL; ?>/user/Username">Username&lt;/a></td>
   	      		</tr>
   	      		<tr>
   	      		<td valign="top">%<i>field_name_here</i>%</td>
   	      		<td valign="top">You can also include any field associated with the user who created the article.</td>
   	      		</tr>
		 		</tbody></table>
		 		
			</div>
			<!-- close:element -->
    			
    			<?php
    			}
    			
    			// -----------------------------------------------------------------------
    			//	Recent User
    			
    			else if ($widget['type'] == '6') {
    			?>

			<!-- start:element -->
			<div id="type6_options">
				<h2>Recent Users</h2>
				
		    			<label>User Type</label>
					<select name="user_type_6" id="user_type_6" style="width:300px;">
					<option value=""<?php if ($widoptions['1'] == '') { echo " selected=\"selected\""; } ?>>All user types</option>
					<?php
					$q = "SELECT `name`,`id` FROM `" . TABLE_PREFIX . "user_types` ORDER BY `name` ASC";
					$uTypes = $db->run_query($q);
					while ($row = mysql_fetch_array($uTypes)) {
						if ($widoptions['1'] == $row['id']) {
							echo "<option value=\"" . $row['id'] . "\" selected=\"selected\">" . $row['name'] . "</option>";
						} else {
							echo "<option value=\"" . $row['id'] . "\">" . $row['name'] . "</option>";
						}
					}
					?>
					</select>
		    			<p class="field_desc">Would you like to only display users of a specific type?</p>
		    			
		    			<label>Limit Results</label>
		    			<input type="text" name="limit_6" id="limit_6" style="width:50px;" value="<?php echo $widoptions['0']; ?>" />
		    			<p class="field_desc">How many users would you like to display?</p>

		    			<label>Order By</label>
		    			<select name="order_6" id="order_6" style="width:300px;">
		    			<option value="joined"<?php if ($widoptions['2'] == 'joined') { echo " selected=\"selected\""; } ?>>Date Joined</option>
		    			<option value="username"<?php if ($widoptions['2'] == 'username') { echo " selected=\"selected\""; } ?>>Username</option>
		    			<option value="score"<?php if ($widoptions['2'] == 'score') { echo " selected=\"selected\""; } ?>>Comment Score</option>
		    			<option value="name"<?php if ($widoptions['2'] == 'name') { echo " selected=\"selected\""; } ?>>Name</option>
		    			</select>
		    			<p class="field_desc">How should users be ordered?</p>
		    			
		    			<label>Order Direction</label>
		    			<select name="dir_6" id="dir_6" style="width:300px;">
		    			<option value="ASC"<?php if ($widoptions['3'] == 'ASC') { echo " selected=\"selected\""; } ?>>Ascending Order</option>
		    			<option value="DESC"<?php if ($widoptions['3'] == 'DESC') { echo " selected=\"selected\""; } ?>>Descending Order</option>
		    			</select>
		    			<p class="field_desc">How should users be ordered?</p>

	    			
	    			<h2>Widget List HTML</h2>

<textarea name="html_6" id="html_6" style="width:95%;height:75px;" cols="1" rows="1">
<?php echo $widget['html']; ?>
</textarea>

		 		<table cellspacing=0 callpadding=0 border=0 class="callers"><thead><tr>
		 		<th>Caller Tag</th>
		 		<th>Description</th>
		 		</tr></thead><tbody>
   	      		<tr>
   	      		<td valign="top"><a href="#" onClick="addCaller('html_6','%entries%');return false;">%entries%</td>
   	      		<td valign="top">List entries, as created using the "Widget Entry HTML" content below.</td>
   	      		</tr>
		 		</tbody></table>
	    			
	    			<h2>Widget Entry HTML</h2>
<textarea name="html_insert_6" id="html_insert_6" style="width:95%;height:150px;" cols="1" rows="1">
<?php echo $widget['html_insert']; ?>
</textarea>
		 		<table cellspacing=0 callpadding=0 border=0 class="callers"><thead><tr>
		 		<th>Caller Tag</th>
		 		<th>Description</th>
		 		</tr></thead><tbody>
   	      		<tr>
   	      		<td valign="top"><a href="#" onClick="addCaller('html_insert_6','%user_link%');return false;">%user_link%</td>
   	      		<td valign="top">Fully formatted HTML link to the user's page with username included. Example:<br />&lt;a href="<?php echo URL; ?>/user/Username">Username&lt;/a></td>
   	      		</tr>
   	      		<tr>
   	      		<td valign="top">%<i>field_name_here</i>%</td>
   	      		<td valign="top">You can include any field associated with the user.</td>
   	      		</tr>
		 		</tbody></table>
				
			</div>
			<!-- close:element -->
    			
    			<?php
    			}
			
    			// -----------------------------------------------------------------------
    			//	Image Gallery
    			
    			else if ($widget['type'] == '7') {
    			?>

			<!-- start:element -->
			<div id="type6_options">
				<h2>Image Gallery</h2>
				
   	    			<label>Tags</label>
   	    			<input type="text" name="tags_7" id="tags_7" style="width:95%;" value="<?php echo $widoptions['tags']; ?>" />
   	    			<p class="field_desc">Comma-separated. Controls which photos will appear in the gallery.</p>
   	    			
   	    			<label>Tag Matching Strictness</label>
   				<input type="radio" name="strict_7" value="1"<?php if ($widoptions['strict'] == '1') { echo " checked=\"checked\""; } ?> /> Photos must match all tags to appear in the gallery. <input type="radio" name="strict_7" value="0"<?php if ($widoptions['strict'] != '1') { echo " checked=\"checked\""; } ?> /> Photos can match any tag to appear in the gallery.
	    		
	    		
		    			<label>Thumbnail Width</label>
		    			<input type="text" name="thumb_width_7" id="thumb_width_7" style="width:50px;" value="<?php echo $widoptions['thumb_width']; ?>" />px
		    			
		    			<label>Images per row (columns)</label>
		    			<input type="text" name="cols_7" id="cols_7" style="width:50px;" value="<?php echo $widoptions['columns']; ?>" />

	    			
	    			<?php
	    			
	    				if (! empty($widoptions['not_after']) && $widoptions['not_after'] != '0000-00-00 00:00:00') {
?>

		    		<label>Refresh List?</label>
				<input type="checkbox" name="refresh_list_7" id="refresh_list_7" value="1" /> <b>Renew the gallery's images.</b> This gallery is currently only showing images uploaded prior to <?php echo $db->format_date($widoptions['not_after']); ?>. If you wish to reset this to today's date, check the above checkbox.
		    		
<?php
	    				} else {
?>

		    		<label>Lock List?</label>
				<input type="checkbox" name="lock_list_7" id="lock_list_7" value="1" /> <b>Lock images.</b> If you wish to prevent any newly uploaded images from appearing in this gallery, check the checkbox above.

<?php
	    				}
	    			
	    			?>
		    		
					
	    			<h2>Widget List HTML</h2>

<textarea name="html_7" id="html_7" style="width:95%;height:75px;" cols="1" rows="1">
<?php echo $widget['html']; ?>
</textarea>

		 		<table cellspacing=0 callpadding=0 border=0 class="callers"><thead><tr>
		 		<th>Caller Tag</th>
		 		<th>Description</th>
		 		</tr></thead><tbody>
   	      		<tr>
   	      		<td valign="top"><a href="#" onClick="addCaller('html_6','%entries%');return false;">%entries%</td>
   	      		<td valign="top">List entries, as created using the "Widget Entry HTML" content below.</td>
   	      		</tr>
		 		</tbody></table>
	    			
	    			<h2 style="margin-top:20px;">Widget Entry HTML</h2>
<textarea name="html_insert_7" id="html_insert_7" style="width:95%;height:150px;" cols="1" rows="1">
<?php echo $widget['html_insert']; ?>
</textarea>
		 		<table cellspacing=0 callpadding=0 border=0 class="callers"><thead><tr>
		 		<th>Caller Tag</th>
		 		<th>Description</th>
		 		</tr></thead><tbody>
   	      		<tr>
   	      		<td valign="top"><a href="#" onClick="addCaller('html_insert_6','%user_link%');return false;">%image%</td>
   	      		<td valign="top">HTML image tags with correct image sizing and expand code.</td>
   	      		</tr><tr>
   	      		<td valign="top"><a href="#" onClick="addCaller('html_insert_6','%user_link%');return false;">%caption%</td>
   	      		<td valign="top">Image caption</td>
   	      		</tr><tr>
   	      		<td valign="top">%<i>field_name_here</i>%</td>
   	      		<td valign="top">You can include any field associated with the user.</td>
   	      		</tr>
		 		</tbody></table>
				
			</div>
			<!-- close:element -->
    			
    			<?php
    			}
			
			?>
			
		</div></div>
		<div class="clear"></div>
	
	</div>
	</form>

</div>

<?php
}
?>