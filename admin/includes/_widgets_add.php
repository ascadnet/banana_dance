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
	    editID('new');
	});
	
	function toggleShow(showing) {
		$('#type_ul li').each(function(index) {
		    id = $(this).attr('id');
		    together = id + "_options";
		    $('#' + together).hide();
		});
		$('#' + showing).show();
	}
	
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
   		<img src="imgs/icon-save.png" width="16" height="16" border="0" onclick="editID('new');" />
		<div class="submit_split"></div>
   		<a href="http://www.doyoubananadance.com/Pages/Widgets" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
   	</div>
   	
   	
	<h1>Creating a Widget</h1>
	
	<form id="edit" onsubmit="return editID('new');">
	<input type="hidden" name="id" value="new" />
	<input type="hidden" name="action" value="add_widget" />
	
	
		<div class="col_left_sm"><div class="col_pad">
			
			<h2 style="margin-top:0px;">Basic Settings</h2>
			
    			<label>Name</label>
    			<input type="text" name="name" style="width:97%;" value="" />
    			<p class="field_desc">Input a reference name for this widget.</p>
    			
    			<label>Type</label>
    			
			<ul class="option_list" id="type_ul">
				<li id="type1">
					<input type="radio" name="type" value="1" onclick="toggleShow('type1_options');" /> Category Page Index
				</li>
				<li id="type2">
					<input type="radio" name="type" value="2" onclick="toggleShow('type2_options');" /> Recent Comments to Page
				</li>
				<li id="type4">
					<input type="radio" name="type" value="4" onclick="toggleShow('type4_options');" /> Recent Pages in Category
				</li>
				<li id="type3">
					<input type="radio" name="type" value="3" onclick="toggleShow('type3_options');" /> Custom HTML
				</li>
				<li id="type6">
					<input type="radio" name="type" value="6" onclick="toggleShow('type6_options');" /> Recent Users
				</li>
			</ul>
			
		</div></div>
		<div class="col_right_sm" id="right_side"><div class="col_pad">
			
			<!-- start:element -->
			<div id="type1_options" style="display:none;">
				<h2>Category Page Index</h2>
				
		    			<label>Category</label>
					<select name="category_1" id="category_1" style="width:97%;">
					<?php
					$categories = $manual->category_select();
					echo $categories;
					?>
					</select>
		    			<p class="field_desc">Which category would you like to display pages for?</p>
		    			
		    			<label>Columns?</label>
   	    				<input type="text" name="columns_1" id="columns_1" value="1" style="width:100px;" maxlength="1" />
		    			<p class="field_desc">If you would like to the display the index in columns, indicate how many above.</p>
		    			
		    			<label>Include sub-categories?</label>
   	    				<input type="radio" name="sub_categories_1" id="sub_categories_1" value="1" /> Yes <input type="radio" name="sub_categories_1" id="sub_categories_1" value="0"  checked="checked" /> No
		    			<p class="field_desc">Would you like to display sub-categories pages within this category?</p>
		    			
		    			<label>Limit Results</label>
		    			<input type="text" name="limit_1" id="limit_1" style="width:50px;" value="10" />
		    			<p class="field_desc">How many pages would you like to display?</p>

		    			<label>Order By</label>
		    			<select name="order_1" id="order_1" style="width:97%;">
		    			<option value="order">Order in Category</option>
		    			<option value="name">Name</option>
		    			<option value="views">Views</option>
		    			<option value="created">Date Created</option>
		    			<option value="last_updated">Date Last Updated</option>
		    			<option value="score">Score</option>
		    			</select>
		    			<p class="field_desc">How should pages be ordered?</p>
		    			
		    			<label>Order Direction</label>
		    			<select name="dir_1" id="dir_1" style="width:97%;">
		    			<option value="ASC">Ascending Order</option>
		    			<option value="DESC">Descending Order</option>
		    			</select>
		    			<p class="field_desc">How should pages be ordered?</p>

	    			
			</div>
			<!-- close:element -->
			
			<!-- start:element -->
			<div id="type2_options" style="display:none;">
				<h2>Recent Comments to Page</h2>
				
		    			<label>Page</label>
		    			<input type="text" name="article_name" id="article_name_pos" onkeyup="suggest('<?php echo TABLE_PREFIX; ?>articles',this.value,'name','article_name_pos','id','name');" value="" style="width:97%;" />
					<input type="hidden" name="article_2" id="article_name_pos_val" />
		    			<p class="field_desc">Display comments from this page.</p>
		    			
		    			<label>Limit Results</label>
		    			<input type="text" name="limit_2" id="limit_2" style="width:50px;" value="10" />
		    			<p class="field_desc">How many comments would you like to display?</p>
		    			
		    			<label>Shorten Long Comments</label>
		    			<input type="text" name="trim_2" id="trim_2" style="width:50px;" value="100" />
		    			<p class="field_desc">For long comments, shorten the comment after how many characters?</p>

		    			<label>Order By</label>
		    			<select name="order_2" id="order_2" style="width:97%;">
		    			<option value="date">Date Posted</option>
		    			<option value="score">Score</option>
		    			</select>
		    			<p class="field_desc">How should comments be ordered?</p>
		    			
		    			<label>Order Direction</label>
		    			<select name="dir_2" id="dir_2" style="width:97%;">
		    			<option value="ASC">Ascending Order</option>
		    			<option value="DESC">Descending Order</option>
		    			</select>
		    			<p class="field_desc">How should comments be ordered?</p>

	    			
	    			<h2>Widget List HTML</h2>

<textarea name="html_2" id="html_2" style="width:97%;height:75px;" cols="1" rows="1">
<ul class="bd_widget_ul">
  %entries%
</ul>
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
<textarea name="html_insert_2" id="html_insert_2" style="width:97%;height:150px;" cols="1" rows="1">
<li>
  <span class="bg_widget_list_title">Posted to %article_link% by %user_link% on %comment_date%</span><br />
  <span class="bd_widget_list_sub">%comment%</span>
</li>
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
   	      		<td valign="top">Fully formatted HTML link to the page with page name included. Example:<br />&lt;a href="<?php echo URL; ?>/page/link/here">Page Name&lt;/a></td>
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
			
			<!-- start:element -->
			<div id="type4_options" style="display:none;">
				<h2>Recent Pages in Category</h2>
				
		    			<label>Category</label>
					<select name="category_4" id="category_4" style="width:97%;">
					<?php
					$categories = $manual->category_select();
					echo $categories;
					?>
					</select>
		    			<p class="field_desc">Which category would you like to display pages for?</p>
		    			
		    			<label>Limit Results</label>
		    			<input type="text" name="limit_4" id="limit_4" style="width:50px;" value="10" />
		    			<p class="field_desc">How many pages would you like to display?</p>

		    			<label>Order By</label>
		    			<select name="order_4" id="order_4" style="width:97%;">
		    			<option value="order">Order in Category</option>
		    			<option value="name">Name</option>
		    			<option value="views">Views</option>
		    			<option value="created">Date Created</option>
		    			<option value="last_updated">Date Last Updated</option>
		    			<option value="score">Score</option>
		    			</select>
		    			<p class="field_desc">How should pages be ordered?</p>
		    			
		    			<label>Order Direction</label>
		    			<select name="dir_4" id="dir_4" style="width:97%;">
		    			<option value="ASC">Ascending Order</option>
		    			<option value="DESC">Descending Order</option>
		    			</select>
		    			<p class="field_desc">How should pages be ordered?</p>
	    			
	    			<h2>Widget List HTML</h2>

<textarea name="html_4" id="html_4" style="width:97%;height:75px;" cols="1" rows="1">
<ul class="bd_widget_ul">
  %entries%
</ul>
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
<textarea name="html_insert_4" id="html_insert_4" style="width:97%;height:150px;" cols="1" rows="1">
<li>
  <span class="bg_widget_list_title">%article_link%</span>
  <span class="bd_widget_list_sub">%article_created% by %user_link%</span>
</li>
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
   	      		<td valign="top">Fully formatted HTML link to the page with page name included. Example:<br />&lt;a href="<?php echo URL; ?>/page/link/here">Page Name&lt;/a></td>
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
   	      		<td valign="top">Date page was created.</td>
   	      		</tr>
   	      		<tr>
   	      		<td valign="top"><a href="#" onClick="addCaller('html_insert_4','%article_score%');return false;">%article_score%</td>
   	      		<td valign="top">Page's score.</td>
   	      		</tr>
   	      		<tr>
   	      		<td valign="top"><a href="#" onClick="addCaller('html_insert_4','%article_comments%');return false;">%article_comments%</td>
   	      		<td valign="top">Total comments posted to this page.</td>
   	      		</tr>
   	      		<tr>
   	      		<td valign="top"><a href="#" onClick="addCaller('html_insert_4','%username%');return false;">%username%</td>
   	      		<td valign="top">Username of user who created the page.</td>
   	      		</tr>
   	      		<tr>
   	      		<td valign="top"><a href="#" onClick="addCaller('html_insert_4','%user_link%');return false;">%user_link%</td>
   	      		<td valign="top">Fully formatted HTML link to the user's page with username included. Example:<br />&lt;a href="<?php echo URL; ?>/user/Username">Username&lt;/a></td>
   	      		</tr>
   	      		<tr>
   	      		<td valign="top">%<i>field_name_here</i>%</td>
   	      		<td valign="top">You can also include any field associated with the user who created the page.</td>
   	      		</tr>
		 		</tbody></table>
		 		
			</div>
			<!-- close:element -->
			
			<!-- start:element -->
			<div id="type3_options" style="display:none;">
				<h2>Custom HTML</h2>
   			
   	    			<label>Content Format Type</label>
   	    			<input type="radio" name="format_3" id="format_3" value="0" onclick="show('format_guide_3');" checked="checked" /> Wiki Syntax <input type="radio" name="format_3" id="format_3" value="1" onclick="hide('format_guide_3');" /> Full HTML
		    		<p class="field_desc">Would you like to compose this in full HTML or using simplified wiki syntax?</p>
		    		
	    			<h2>Widget List HTML</h2>
				<textarea name="html_3" id="html_3" style="width:97%;height:300px;" cols="1" rows="1"></textarea>
				
			</div>
			<!-- close:element -->
			
			<!-- start:element -->
			<div id="type6_options" style="display:none;">
				<h2>Recent Users</h2>
				
		    			<label>User Type</label>
					<select name="user_type_6" id="user_type_6" style="width:97%;">
					<option value="">All user types</option>
					<?php
					$q = "SELECT `name`,`id` FROM `" . TABLE_PREFIX . "user_types` ORDER BY `name` ASC";
					$uTypes = $db->run_query($q);
					while ($row = mysql_fetch_array($uTypes)) {
						echo "<option value=\"" . $row['id'] . "\">" . $row['name'] . "</option>";
					}
					?>
					</select>
		    			<p class="field_desc">Would you like to only display users of a specific type?</p>
		    			
		    			<label>Limit Results</label>
		    			<input type="text" name="limit_6" id="limit_6" style="width:50px;" value="10" />
		    			<p class="field_desc">How many users would you like to display?</p>

		    			<label>Order By</label>
		    			<select name="order_6" id="order_6" style="width:97%;">
		    			<option value="joined">Date Joined</option>
		    			<option value="username">Username</option>
		    			<option value="score">Comment Score</option>
		    			<option value="name">Name</option>
		    			</select>
		    			<p class="field_desc">How should users be ordered?</p>
		    			
		    			<label>Order Direction</label>
		    			<select name="dir_6" id="dir_6" style="width:97%;">
		    			<option value="ASC">Ascending Order</option>
		    			<option value="DESC">Descending Order</option>
		    			</select>
		    			<p class="field_desc">How should users be ordered?</p>

	    			
	    			<h2>Widget List HTML</h2>

<textarea name="html_6" id="html_6" style="width:97%;height:75px;" cols="1" rows="1">
<ul class="bd_widget_ul">
  %entries%
</ul>
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
<textarea name="html_insert_6" id="html_insert_6" style="width:97%;height:150px;" cols="1" rows="1">
<li>
  <span class="bg_widget_list_title">%user_link%</span>
  <span class="bd_widget_list_sub">%joined%</span>
</li>
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
			
		</div></div>
		<div class="clear"></div>
	
	</form>

</div>

<?php
}
?>