<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Media library features.
	
	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 2 of the License, or
	(at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.

====================================================== */


require "../config.php";

if (empty($user)) {
   echo "0+++" . lg_login_req;
   exit;
}


// --------------------------------------

require "../includes/image.functions.php";
$image = new image;

// Called after an upload or edit to
// replace the media gallery view.
if ($_POST['refresh_list'] == '1') {
   	$list = $image->get_media_library($_POST['tag'],$_POST['view']);
	echo "1+++";
   	echo $list;
   	exit;
}

else if ($_POST['refresh_file_list'] == '1') {
   	$list = $image->get_file_library();
	echo "1+++";
   	echo $list;
   	exit;
}

else if ($_POST['action'] == 'create_gallery') {
	if ($_POST['lock_date'] == '1') {
		$date_before = $db->current_date();
	}
	$create = $image->create_gallery($_POST['title'],$_POST['tags'],$_POST['strict'],$_POST['width'],$_POST['columns'],$date_before);
	exit;
}

else if ($_POST['action'] == 'delete') {
   	$list = $image->delete_media($_POST['delid']);
   	exit;
}

else if ($_POST['action'] == 'delete_file') {
   	$list = $image->delete_file($_POST['delid']);
   	exit;
}

// Edit a piece of content
else if (! empty($_POST['id'])) {
	$thisImg = $image->get_image($_POST['id'],'1');
	header('Content-type: application/json');
	echo json_encode($thisImg);
	exit;
}

else if (! empty($_POST['file_id'])) {
	$thisFile = $db->get_file_info($_POST['file_id']);
	header('Content-type: application/json');
	echo json_encode($thisFile);
	exit;
}

// Display the media gallery.
else {

	// Permissions?
	if ($_POST['type'] == 'files' && $privileges['upload_files'] != "1") {
		echo "0+++" . lg_no_permissions;
		exit;
	}
	else {
		if ($privileges['upload_images'] != "1") {
			echo "0+++" . lg_no_permissions;
			exit;
		}
	}
	

echo "1+++";
echo "	
<div id=\"media_box\">

<style type=\"text/css\">
<!--
#box_top {
	font-family: tahoma, arial !important;
	height: 39px;
	line-height: 39px;
	background: url('" . URL . "/templates/html/_imgs/editor/back_main.png') repeat-x #000;
	border-top: 1px solid #000;
	border-bottom: 1px solid #000;
	color: #fff;
	font-size: 10pt;
	padding: 0 20px 0 20px;
	z-index: 14;
	-moz-text-shadow: 1px 1px 0 rgba(0, 0, 0, 0.75);
	-webkit-text-shadow: 1px 1px 0 rgba(0, 0, 0, 0.75);
	text-shadow: 1px 1px 0 rgba(0, 0, 0, 0.75);
}

#box_top span {
	margin-right: 20px;
}

#media_box {
	font-family: tahoma, arial !important;
	position: fixed;
	margin: 0;
	width: 100%;
	height: 470px;
	bottom: 0px;
	left: 0px;
	z-index: 5050;
	padding-bottom: 84px;
	background: url('" . URL . "/templates/html/_imgs/media_gallery/gray_back.png') top left;
}

#media_box input[type=text], #media_box input[type=file], #media_box textarea, select {
	border: 1px solid #000;
	padding: 3px;
	background: url('" . URL . "/templates/html/_imgs/input-back.png') #fff;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	border-radius: 5px;
	margin: 0;
}

#media_box h1 {
	margin: 15px 0 5px 0;
	padding: 0;
	font-weight: normal;
	font-size: 19pt;
}

.nomarg_top {
	margin-top: 0px !important; 
}

a.cancelButton {
	float: right;
	font-size: 9pt;
	font-weight: bold;
}

#media_box h2 {
	margin: 15px 0 5px 0;
	padding: 0;
	font-weight: normal;
	font-size: 15pt;
}

.pad {
	padding: 20px;
}

#mb_left {
	overflow: hidden;
}

#put_media {
	height: 470px;
	position: relative;
";

if ($_POST['type'] == 'file') {
	echo "overflow-y: auto;";
} else {
	echo "overflow-x: auto;";
}

echo "
}

#mb_right {
	position: absolute;
	top: 41px;
	right: 20px;
	width: 600px;
	z-index: 20;
	color: #fff !important;
	margin-right: 20px;
	border: 0px;
	z-index: 10;
	background-color: #111213;
	-moz-border-radius-bottomright: 10px;
	-moz-border-radius-bottomleft: 10px;
	-webkit-border-bottom-left-radius: 10px;
	-webkit-border-bottom-right-radius: 10px;
	border-bottom-left-radius: 10px;
	border-bottom-right-radius: 10px;
	-moz-box-shadow: 5px 5px 5px #CED2D4, -5px 5px 5px #CED2D4;
	-webkit-box-shadow: 5px 5px 5px #CED2D4, -5px 5px 5px #CED2D4;
	box-shadow: 5px 5px 5px #CED2D4, -5px 5px 5px #CED2D4;
	-moz-text-shadow: 1px 1px 0 rgba(0, 0, 0, 0.75);
	-webkit-text-shadow: 1px 1px 0 rgba(0, 0, 0, 0.75);
	text-shadow: 1px 1px 0 rgba(0, 0, 0, 0.75);
}

#mb_right label {
	color: #fff;
}

.fld_col {
	float: left;
	width: 260px;
	margin-right: 20px;
}

.fld_col2 {
	float: left;
	width: 260px;
}

#mb_right_inner {
	border: 0px;
	border-left: 1px solid #ccc;
	border-right: 1px solid #ccc;
	border-bottom: 1px solid #ccc;
}

#upload_error_display {
	display: none;
	border: 1px solid red;
	margin-bottom: 20px;
	color: red;
	padding: 10px;
}

.img_options {
	margin: 5px 0 0 0;
}

.img_options span {
	margin-right: 8px;
	font-size: 8pt;
	font-weight: bold;
	height: 16px;
	line-height: 16px;
}

.img_options span.add_img {
	background: url('" . URL . "/templates/html/_imgs/media_gallery/add.png') center left no-repeat;
	padding-left: 13px;
}

.img_options span.edit_img {
	background: url('" . URL . "/templates/html/_imgs/media_gallery/edit.png') center left no-repeat;
	padding-left: 13px;
}

.img_options span.del_img {
	background: url('" . URL . "/templates/html/_imgs/media_gallery/delete.png') center left no-repeat;
	padding-left: 13px;
}

.rlink {
	float: right;
	font-size: 8pt;
	font-weight: bold;
	margin-left: 20px;
}

p.caption {
	font-size: 8pt;
	color: #777;
}

.image_entry {
	float: left;
	width: 220px;
	text-align: center;
	border: 1px solid #DEDEDE;
	padding: 8px;
	background-color: #fff;
	margin: 0 10px 10px 0;
	border-top: 1px solid #f1f1f1;
	border-left: 1px solid #f1f1f1;
	border-bottom: 1px solid #ccc;
	border-right: 1px solid #ccc;
	-webkit-border-radius: 10px;
	-moz-border-radius: 10px;
	border-radius: 10px;
	-moz-box-shadow: 5px 5px 5px #CED2D4;
	-webkit-box-shadow: 5px 5px 5px #CED2D4;
	box-shadow: 5px 5px 5px #CED2D4;
}

#file_table img, .image_entry img {
	border-top: 1px solid #f1f1f1;
	border-left: 1px solid #f1f1f1;
	border-bottom: 1px solid #ccc;
	border-right: 1px solid #ccc;
	-webkit-border-radius: 10px;
	-moz-border-radius: 10px;
	border-radius: 10px;
}

table#file_table {
	font-size: 10pt;
	font-family: tahoma, arial;
}

table#file_table th {
	border-bottom: 2px solid #DEDEDE;
	padding: 5px;
	font-weight: bold;
	text-align: left;
}

table#file_table td {
	border-bottom: 1px solid #DEDEDE;
	border-top: 1px solid #F8F8F8;
	padding: 5px;
}

a.uploadlink {
	font-weight: bold;
	color: #F7E440;	
}

.onmedia {
	background-color: #F9FFBA;
	border: 2px solid #FFF396;
}

.clear {
	clear: both;
}

.error {
	color: red !important;
	border: 3px solid red !important;
}

label {
	margin-top: 9px;
}

.mg_help {
	font-size: 8pt !important;
	margin: 4px 0 5px 0 !important;
	padding: 0 !important;
}

#media_onserver_entry, #media_upload_entry {
	margin-top: 5px;
}

span.tag {
	margin-right: 2px;
	background-color: #f1f1f1;
	padding: 3px;
	font-size: 8pt;
}

p.directions {
	text-align: center;
	margin: 10px 0 10px 0;
	padding: 5px;
	background-color: #fff;
	border: 1px solid #777;
	font-size: 9pt;
}

#img_dimensions {
	margin-bottom: 5px;
	font-size: 9pt;
}

.icon {
	vertical-align: middle;
	margin-right: 4px;
}

#new_file {
	
}

.white_text,
.white_text h1,
.white_text h2,
.white_text h3,
.white_text label,
.white_text div,
.white_text p {
	color: #fff !important;
}

.fld_colA {
	float: left;
	width: 160px;
	margin-right: 20px;
}

.fld_colB {
	float: left;
	width: 360px;
}


.right_entry {
	margin-bottom: 6px;
	font-size: 0.8em;
}

.ftit {
	float: left;
	width: 90px;
}

.ffit {
	margin-left: 100px;
}

-->
</style>
<link href=\"" . URL . "/templates/html/_css/fileuploader.css\" rel=\"stylesheet\" type=\"text/css\" />

<script>
$(document).ready(function() {
	height = $('.bd_options').outerHeight();
	$('#media_box').css('bottom',height);
});
</script>

<script src=\"" . URL . "/js/callers.js\" type=\"text/javascript\"></script>
<script type=\"text/javascript\">
   $(document).ready(function() {
   	$(\"input[name=file_location]\").change(function() {
   		if ($(this).val() == 'upload') {
   			$('#media_upload_entry').show();
   			$('#media_onserver_entry').hide();
   		} else {
   			$('#media_upload_entry').hide();
   			$('#media_onserver_entry').show();
   		}
   	});
   	
   	$('.image_entry').dblclick(function() {
   		select_id = this.id.substr(3);
   		addMedia(id);
   	});
   	
   });
</script>	


	<form action=\"#\" onsubmit=\"return refreshMedia('";
	
	if ($_POST['type'] == 'file') {
		echo "file";
	} else {
		echo "media";
	}
	
	echo "');\">
		<div id=\"box_top\">
			<a href=\"#\" onclick=\"return closeMedia();\" class=\"rlink\"><img src=\"" . URL . "/templates/html/_imgs/media_gallery/close.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"Close\" class=\"icon\" />" . lg_close . "</a>
			<a href=\"#\" onclick=\"return openUpload();\" class=\"rlink uploadlink\"><img src=\"" . URL . "/templates/html/_imgs/media_gallery/up.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"Upload\" class=\"icon\" />" . lg_mg_upload . "</a>";
			
			if ($_POST['type'] != 'file') {
				echo "<a href=\"#\" onclick=\"return addGallery();\" class=\"rlink\"><img src=\"" . URL . "/templates/html/_imgs/media_gallery/gallery.png\" width=\"10\" height=\"10\" border=\"0\" alt=\"Gallery\" class=\"icon\" />" . lg_mg_gallery . "</a>";
				echo "<span>" . lg_media_gallery . "</span>";
				echo "<a href=\"#\" onclick=\"return refreshMedia('gallery');\"><img src=\"" . URL . "/templates/html/_imgs/media_gallery/thumb-view.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"Thumbnail View\" title=\"Thumbnail View\" class=\"icon\" /></a>";
				echo "<span><a href=\"#\" onclick=\"return refreshMedia('list');\"><img src=\"" . URL . "/templates/html/_imgs/media_gallery/list-view.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"List View\" title=\"List View\" class=\"icon\" /></a></span>";
				echo "<span>" . lg_tag_search . " <input class=\"sys_field\" type=\"text\" name=\"filter_tag\" id=\"filter_tag\" autocomplete=\"off\" style=\"width:150px;\" value=\"\" /></span>";
				echo "<span><a href=\"#\" onclick=\"return switchView('files');\"><img src=\"" . URL . "/templates/html/_imgs/manage_bar/downloads.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"Switch to Download Library\" title=\"Switch to Download Library\" class=\"icon\" /> Switch to Download Library</a></span>";
			} else {
				echo "<span>" . lg_file_gallery . "</span>";
				echo "<span><a href=\"#\" onclick=\"return switchView('gallery');\"><img src=\"" . URL . "/templates/html/_imgs/manage_bar/downloads.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"Switch to Media Library\" title=\"Switch to Media Library\" class=\"icon\" /> Switch to Media Library</a></span>";
			}
			
			echo "
		</div>
	</form>
	
	<div id=\"showDivUpload\" style=\"display:none;\">
	<div id=\"mb_right\"><div id=\"mb_right_inner\">
		<div class=\"pad\">
		
			<div id=\"upload_error_display\"></div>
			
			<form action=\"#\" method=\"post\" id=\"add_form_x01\" onsubmit=\"return addMedia();\">
			<div id=\"add_file\" style=\"display:none;\" class=\"white_text\">
				<a href=\"#\" class=\"cancelButton\" onclick=\"return closeFloats();\">Cancel</a>
				<h1 class=\"nomarg_top\" id=\"right_header2\">Add Media to Page</h1>
				<div class=\"fld_col\">
					<label>Width (px)</label>
					<input class=\"sys_field\" type=\"text\" name=\"width\" autocomplete=\"off\" id=\"img_width\" value=\"\" style=\"width:75px;\" />
				</div>
				<div class=\"fld_col2\">
				<label>Height (px)</label>
					<input class=\"sys_field\" type=\"text\" name=\"height\" autocomplete=\"off\" id=\"img_height\" value=\"\" style=\"width:75px;\" />
				</div>
				<div class=\"clear\"></div>
				
				<p style=\"margin-top:10px;\"><input class=\"sys_field\" type=\"checkbox\" id=\"scale_proportions\" name=\"scale_proportions\" checked=\"checked\" /> Maintain scaling</p>
				
				<p style=\"text-align:center;\"><input class=\"sys_field\" type=\"submit\" class=\"img_submit\" value=\"Add to Page\" style=\"margin-top:15px;\" /></p>
			</div>
			</form>

			<form action=\"#\" method=\"post\" id=\"add_gallery_x01\" onsubmit=\"return finalizeGallery();\">
			<div id=\"img_create_gal\" style=\"display:none;\" class=\"white_text\">
				<a href=\"#\" class=\"cancelButton\" onclick=\"return closeFloats();\">Cancel</a>
				<h1 class=\"nomarg_top\">Add Existing Gallery</h1>
				<select name=\"img_existing_gallery\" id=\"img_existing_gallery\" style=\"width:400px;\">
				<option value=\"\"></option>
				";
				
				$galleries = $image->get_galleries();
				echo $galleries;
				
				echo "
				</select>
				<p class=\"mg_help\">Select an existing gallery to add to the page above or create a new one below.<br /><b>Tip:</b> Galleries are widgets and can be edited from the admin control panel.</p>
				
				<h1>Create New Gallery</h1>
				
				<div class=\"fld_col\">
					<label>Title</label>
					<input class=\"sys_field\" type=\"text\" name=\"title\" id=\"img_title1\" autocomplete=off value=\"\" style=\"width:240px;\" />
				</div>
				<div class=\"fld_col2\">
					<label>Tags</label>
					<input class=\"sys_field\" type=\"text\" name=\"tags\" id=\"img_tags1\" autocomplete=off value=\"\" style=\"width:240px;\" />
					<p class=\"mg_help\">Comma-separated. Controls which photos will appear in the gallery.</p>
					
					<p class=\"mg_help\">
					<input class=\"sys_field\" type=\"checkbox\" id=\"img_strict\" name=\"strict\" value=\"1\" checked=\"checked\" /> Photos must match all tags.<br />
					<input class=\"sys_field\" type=\"checkbox\" name=\"lock_date\" id=\"lock_date\" value=\"1\" checked=\"checked\" /> Only display existing images in the gallery.
					</p>
					
				</div>
				<div class=\"clear\"></div>
				
				<div class=\"fld_col\">
					<label>Thumbnail Width</label>
					<input class=\"sys_field\" type=\"text\" name=\"width\" id=\"img_thumbwidth1\" autocomplete=off value=\"200\" style=\"width:50px;\" />px
				</div>
				<div class=\"fld_col2\">
					<label>Images Per Row (columns)</label>
					<input class=\"sys_field\" type=\"text\" name=\"columns\" id=\"img_per_row1\" autocomplete=off value=\"2\" style=\"width:50px;\" />
				</div>
				<div class=\"clear\"></div>
					
				<p style=\"text-align:center;\"><input class=\"sys_field\" type=\"submit\" class=\"img_submit\" value=\"Create Gallery\" style=\"margin-top:15px;\" /></p>
			</div>
			</form>

			
			<form enctype=\"multipart/form-data\" method=\"post\" action=\"" . URL . "/functions/upload_file.php\" id=\"uploader_form\" name=\"uploader_form\" target=\"putUpload\">
			<div id=\"new_file\" class=\"white_text\">
				<a href=\"#\" class=\"cancelButton\" onclick=\"closeFloats();return false;\">Cancel</a>
				<h1 class=\"nomarg_top\" id=\"right_header\">" . lg_mg_upload . "</h1>
				<iframe src=\"" . URL . "/functions/upload_file.php?none=1\" width=\"0\" height=\"0\" frameborder=\"0\" name=\"putUpload\" id=\"putUpload\">Your browser does not support iFrames.</iframe>
				<input class=\"sys_field\" type=\"hidden\" id=\"img_edit_id\" name=\"edit_id\" value=\"\" />";
				
				if ($_POST['type'] == 'file') {
					echo "
					<input class=\"sys_field\" type=\"hidden\" name=\"type\" value=\"file\" />
					";
				} else {
					echo "
					<input class=\"sys_field\" type=\"hidden\" name=\"type\" value=\"image\" />
					";
				}
				
	echo "
				<div class=\"fld_colA\" id=\"editUps\">
					<div id=\"new_files\">
						<script src=\"" . URL . "/js/fileuploader.js\" type=\"text/javascript\"></script>
						<script>
							var uploader = new qq.FileUploader({
								element: document.getElementById('fileuploader'),
								action: '" . URL . "/functions/drag_upload.php',
								debug: true,
								";
					if ($_POST['type'] != 'file') {
						echo "
								allowedExtensions: ['jpg','jpeg','png','gif','tif','tiff'],";
					}
								echo "
							});
						</script>
						<h2>File Upload</h2>
						<p class=\"mg_help\">Drad and drop files to upload.</p>
						<div id=\"fileuploader\"><noscript>Enable JavaScript to use this feature.</noscript></div>
					</div>
					<div id=\"media_upload_entry\" style=\"display:none;\">
					
						<input class=\"sys_field\" type=\"file\" id=\"up_file\" name=\"file\" style=\"width:120px;\" />
						
						<p style=\"text-align:center;\"><input class=\"sys_field\" type=\"submit\" id=\"img_submit\" value=\"" . lg_mg_upload . "\" style=\"margin-top:15px;\" /></p>
					</div>
				</div>
				<div class=\"fld_colB\" id=\"editDets\">
   					<div class=\"right_entry\">
   						<div class=\"ftit\">Location</div>
   						<div class=\"ffit\" id=\"img_location\">&nbsp;</div>
   					</div>
					<div class=\"right_entry\">
						<div class=\"ftit\">Basics</div>
						<div class=\"ffit\" id=\"img_filename\">&nbsp;</div>
					</div>
					<div class=\"right_entry\">
						<div class=\"ftit\">Dimesions</div>
						<div class=\"ffit\" id=\"img_dimensions\">N/A</div>
					</div>
					<div class=\"right_entry\">
						<div class=\"ftit\">Title</div>
						<div class=\"ffit\"><input class=\"sys_field\" type=\"text\" name=\"title\" id=\"img_title\" autocomplete=off value=\"\" style=\"width:200px;\" /></div>
					</div>
					";
					

				if ($_POST['type'] != 'file') {
					
					echo "
					<div class=\"right_entry\">
						<div class=\"ftit\">Caption</div>
						<div class=\"ffit\"><input class=\"sys_field\" type=\"text\" name=\"caption\" id=\"img_caption\" autocomplete=off value=\"\" style=\"width:200px;\" /></div>
					</div>
					
					<div class=\"right_entry\">
						<div class=\"ftit\">Tags</div>
						<div class=\"ffit\"><input class=\"sys_field\" type=\"text\" name=\"tags\" id=\"img_tags\" autocomplete=off value=\"\" style=\"width:200px;\" /><p class=\"mg_help\">Enter as comma separated values.</p></div>
					</div>
					
					<div class=\"right_entry\">
						<div class=\"ftit\">Access</div>
						<div class=\"ffit\"><input class=\"sys_field\" type=\"checkbox\" id=\"img_public\" name=\"public\" value=\"1\" checked=\"checked\" /> Make this a public image, meaning that anyone with access to the media gallery can edit it.</p></div>
					</div>
					
					";
					
				} else {
				
					echo "
					<div class=\"right_entry\">
						<div class=\"ftit\">Limit Downloads?</div>
						<div class=\"ffit\"><input class=\"sys_field\" type=\"text\" name=\"limit_dls\" id=\"img_limit_dls\" autocomplete=off value=\"0\" style=\"width:70px;\" /><p class=\"mg_help\">Enter zero \"0\" for unlimited..</p></div>
					</div>
					
					<div class=\"right_entry\">
						<div class=\"ftit\">Login Required?</div>
						<div class=\"ffit\"><input class=\"sys_field\" type=\"checkbox\" id=\"img_login_req\" name=\"login_req\" value=\"1\" checked=\"checked\" /> Login required to download.</div>
					</div>
					";
				
				}
					
					echo "
				</div>
				<div class=\"clear\"></div>
	";
				
				echo "
				</div>
			</div>
			</form>
			
	</div></div>
	</div>
	
	<div id=\"mb_left\">
		<div class=\"pad\" id=\"put_media\">
		";
	
	// List files in library
	if ($_POST['type'] == 'file') {
		$list = $image->get_file_library();
	}
	// List images in library
	else {
		$list = $image->get_media_library($_POST['tag']);
	}
	echo $list;
	
	echo "
	</div>
	<div class=\"clear\"></div>
	
</div></div>

</body>
</html>
";
exit;

}


function refresh_list() {
   	echo "<html>
   	<body>
   	<head>
   	<script src=\"" . URL . "/js/jquery.js\" type=\"text/javascript\"></script>
   	<script type=\"text/javascript\">
   		parent.refreshMedia('');
   	</script>
   	</head>
   	</body>
   	</html>
   	";
   	exit;
}

function iframe_error($error) {
	echo "<html>
	<body>
	<head>
	<script src=\"" . URL . "/js/jquery.js\" type=\"text/javascript\"></script>
	<script type=\"text/javascript\">
	$('#upload_error_display',parent.document.body).show();
	$('#upload_error_display',parent.document.body).html('$error');
	</script>
	</head>
	</body>
	</html>
	";
	exit;
}

?>