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
-->
</script>

<div id="content_overlay">

   	<div class="submit">
   		<img src="imgs/icon-save.png" width="16" height="16" border="0" onclick="editID('new');" />
		<div class="submit_split"></div>
   		<a href="http://www.doyoubananadance.com/Users/User-Types" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
   	</div>
   	
   	
	<h1>Add a User Type</h1>
	
	<form id="edit" onsubmit="return editID('new');">
	<input type="hidden" name="id" value="new" />
	<input type="hidden" name="action" value="add_user_type" />
	
	
			<h2>General Settings</h2>
			
			<div class="col50">
	   			<label>Name</label>
	   			<input type="text" name="name" style="width:90%;" value="" />
	   			
	   			<label>Short Form</label>
	   			<input type="text" name="short_form" style="width:150px;" value="" />
	   			<p class="field_desc">Short-form name displayed next to a username in the comments.</p>
			</div>
			
			<div class="col50">
	   			<label>Font Color</label>
	   			#<input type="text" name="font_color" style="width:100px;" maxlength="6" value="" />
	   			<p class="field_desc">Color of the user's username in the comments.</p>

	   			<label>Background Color</label>
	   			#<input type="text" name="color" style="width:100px;" maxlength="6" value="" />
	   			<p class="field_desc">Background color of the user's username in the comments.</p>
			</div>
		
		
			<h2>Options</h2>
		
			<div class="col50">
		
				<label>Track Tasks</label>
	    			<ul id="A" class="option_list">
					<li>
						<input type="radio" name="track_tasks" value="1" /> Yes
					</li>
					<li class="selected">
						<input type="radio" name="track_tasks" value="0" checked="checked" /> No
					</li>
				</ul>
				
			</div>
			<div class="col50">
				
				<label>Status of new pages</label>
	    			<ul id="B" class="option_list">
					<li>
						<input type="radio" name="new_articles_public" value="1" /> Public
					</li>
					<li class="selected">
						<input type="radio" name="new_articles_public" value="0" checked="checked" /> Pending Approval
					</li>
				</ul>
				
			</div>
			<div class="clear"></div>
			
			
	   		<script language="JavaScript" src="<?php echo URL ?>/js/admin_fields.js"></script> 
	   	
			<h2>Privileges</h2>
	   		<p>All users can alter their own submissions. The following privileges are for content owned by other users.</p>
			
			<ul id="inner_page_tabs_top">
				<li id="litabadmin" class="on"><a href="#" onClick="swapTab('admin');return false;">Adminstrative</a></li>
				<li id="litabcommenting"><a href="#" onClick="swapTab('commenting');return false;">Commenting</a></li>
				<li id="litabuploading"><a href="#" onClick="swapTab('uploading');return false;">Uploading</a></li>
				<li id="litabpages"><a href="#" onClick="swapTab('pages');return false;">Pages</a></li>
				<li id="litabcategories"><a href="#" onClick="swapTab('categories');return false;">Categories</a></li>
			</ul>
			
			<div class="home_box" style="margin-top:-1px;"><div class="pad12">
		
				<!-- Start tab -->
				<div id="tabadmin">
					
					<div class="col50">
						
						<label>Is an admin</label>
			    			<ul id="C" class="option_list">
							<li>
							<input type="radio" name="is_admin" value="1" /> Yes
							</li>
							<li class="selected">
							<input type="radio" name="is_admin" value="0" checked="checked" /> No
							</li>
						</ul>
					
						<!--
						<label>Can Access Control Panel</label>
			    			<ul id="D" class="option_list">
							<li>
							<input type="radio" name="cp_access" value="1" /> Yes
							</li>
							<li class="selected">
							<input type="radio" name="cp_access" value="0" checked="checked" /> No
							</li>
						</ul>
						-->
						
					</div>
					<div class="col50">
					
						<label>Can Ban Users</label>
			    			<ul id="E" class="option_list">
							<li>
							<input type="radio" name="can_ban" value="1" /> Yes
							</li>
							<li class="selected">
							<input type="radio" name="can_ban" value="0" checked="checked" /> No
							</li>
						</ul>
					</div>
					<div class="clear"></div>
	
				</div>
				<!-- end tab -->
		
				<!-- Start tab -->
				<div id="tabcommenting" style="display:none;">

					<div class="col50">
					
						<label>New Comment Status</label>
			    			<ul id="F" class="option_list">
							<li class="selected">
							<input type="radio" name="new_comments_approved" value="1" checked="checked" /> Approved
							</li>
							<li>
							<input type="radio" name="new_comments_approved" value="0" /> Pending
							</li>
						</ul>
						
						<label>Alter Comments</label>
			    			<ul id="G" class="option_list">
							<li>
							<input type="radio" name="can_alter_comments" value="1" /> Yes
							</li>
							<li class="selected">
							<input type="radio" name="can_alter_comments" value="0" checked="checked" /> No
							</li>
						</ul>
						
					</div>
					<div class="col50">
					
						<label>Alter Comment Classification</label>
			    			<ul id="H" class="option_list">
							<li>
							<input type="radio" name="edit_comment_status" value="1" /> Yes
							</li>
							<li class="selected">
							<input type="radio" name="edit_comment_status" value="0" checked="checked" /> No
							</li>
						</ul>
						
						<label>Post HTML in comments (including links)?</label>
			    			<ul id="I" class="option_list">
							<li>
							<input type="radio" name="post_code" value="1" /> Yes
							</li>
							<li class="selected">
							<input type="radio" name="post_code" value="0" checked="checked" /> No
							</li>
						</ul>
					</div>
					<div class="clear"></div>
						
				</div>
				<!-- end tab -->
				
		
				<!-- Start tab -->
				<div id="tabuploading" style="display:none;">

					<div class="col50">
					
						<label>Upload Files</label>
			    			<ul id="J" class="option_list">
							<li>
							<input type="radio" name="upload_files" value="1" /> Yes
							</li>
							<li class="selected">
							<input type="radio" name="upload_files" value="0" checked="checked" /> No
							</li>
						</ul>
						
					</div>
					<div class="col50">
						
						<label>Upload Images</label>
			    			<ul id="K" class="option_list">
							<li>
							<input type="radio" name="upload_images" value="1" /> Yes
							</li>
							<li class="selected">
							<input type="radio" name="upload_images" value="0" checked="checked" /> No
							</li>
						</ul>
						
					</div>
					<div class="clear"></div>
					
				</div>
				<!-- end tab -->
				
		
				<!-- Start tab -->
				<div id="tabpages" style="display:none;">

					<div class="col50">
					
						<label>Can Create Pages</label>
			    			<ul id="L" class="option_list">
							<li>
							<input type="radio" name="can_create_articles" value="1" /> Yes
							</li>
							<li class="selected">
							<input type="radio" name="can_create_articles" value="0" checked="checked" /> No
							</li>
						</ul>
						
						<label>Can Alter Pages</label>
			    			<ul id="M" class="option_list">
							<li>
							<input type="radio" name="can_alter_articles" value="1" /> Yes 
							</li>
							<li class="selected">
							<input type="radio" name="can_alter_articles" value="0" checked="checked" /> No
							</li>
						</ul>
						
						<label>Can Alter Page Options</label>
			    			<ul id="M" class="option_list">
							<li>
							<input type="radio" name="can_alter_article_options" value="1" /> Yes 
							</li>
							<li class="checked">
							<input type="radio" name="can_alter_article_options" value="0" checked="checked" /> No
							</li>
						</ul>
						
					</div>
					<div class="col50">
					
						<label>Can Delete Pages</label>
			    			<ul id="N" class="option_list">
							<li>
							<input type="radio" name="can_delete_articles" value="1" /> Yes 
							</li>
							<li class="selected">
							<input type="radio" name="can_delete_articles" value="0" checked="checked" /> No
							</li>
						</ul>
						
						<label>Can View Private Pages</label>
			    			<ul id="O" class="option_list">
							<li>
							<input type="radio" name="can_view_private" value="1" /> Yes 
							</li>
							<li class="selected">
							<input type="radio" name="can_view_private" value="0" checked="checked" /> No
							</li>
						</ul>
					
					</div>
					<div class="clear"></div>

				</div>
				<!-- end tab -->
				
		
				<!-- Start tab -->
				<div id="tabcategories" style="display:none;">

					<div class="col50">
					
						<label>Can Alter Categories</label>
			    			<ul id="Q" class="option_list">
							<li>
							<input type="radio" name="can_alter_categories" value="1" /> Yes 
							</li>
							<li class="selected">
							<input type="radio" name="can_alter_categories" value="0" checked="checked" /> No
							</li>
						</ul>
						
						<label>Can Create Categories</label>
						
			    			<ul id="R" class="option_list">
							<li>
							<input type="radio" name="can_create_categories" value="1" /> Yes
							</li>
							<li class="selected">
							<input type="radio" name="can_create_categories" value="0" checked="checked" /> No
							</li>
						</ul>
						
					</div>
					<div class="col50">
					
						<label>Can Delete Categories</label>
			    			<ul id="S" class="option_list">
							<li>
							<input type="radio" name="can_delete_categories" value="1" /> Yes
							</li>
							<li class="selected">
							<input type="radio" name="can_delete_categories" value="0" checked="checked" /> No
							</li>
						</ul>
						
					</div>
					<div class="clear"></div>
				</div>
				<!-- end tab -->
				
			</div>
			</div>
			
	</form>

</div>

<?php
}
?>