<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Image processing functions.
	
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

// Up the memory limit for larger images
ini_set('memory_limit','128M');

class image extends db {

	// ---------------------------------------------------------------------------
	// 	Crop an image
	//	$input_filename	-> Current location
	//	$output_filename	-> New location
	//	$new_w			-> New width in pixels
	//	$new_h			-> New height in pixels
	//	$ext				-> Extension (optional)
	
	function crop_image($input_filename,$output_filename,$new_w,$new_h,$ext = '',$force_dimensions = '0',$larger_wins = '0',$true_crop = '0') {

		list($old_x, $old_y, $type, $attr) = getimagesize($input_filename);
		// Do we even need to?
		$go = 1;
		if ($old_x <= $new_w && empty($new_h)) {
			$go = 0;
		}
		else if ($old_y <= $new_h && empty($new_w)) {
			$go = 0;
		}
		else if ($old_y <= $new_h && $old_y <= $new_h) {
			$go = 0;
		}
		else {
			$go = 1;
		}

   		if ($go == '1') {
   		
			// Extension?
			// Returned in lower case
			if (empty($ext)) {
				$ext = $this->get_extension($input_filename);
			}
			
			// JPG or PNG?
			if(! strcmp("jpg",$ext) || ! strcmp("jpeg",$ext)) {
				$src_img = imagecreatefromjpeg($input_filename);
			}
			if(! strcmp("png",$ext)) {
				$src_img = imagecreatefrompng($input_filename);
			}
			//gets the dimensions of the image
			// $old_x = imageSX($src_img);
			// $old_y = imageSY($src_img);
		
			if ($force_dimensions == "1") {
				$final_width = $new_w;
				$final_height = $new_h;
			} else {
				$resize = $this->get_resize($input_filename,$new_w,$new_h,$larger_wins);
				$final_width = $resize['0'];
				$final_height = $resize['1'];
			}
			
			if  ($final_width >= $old_x) {
				$final_width = $old_x;
			}
			if ($final_height >= $old_y) {
				$final_height = $old_y;
			}
			
			// Create new image with correct dimensions
			$dst_img = ImageCreateTrueColor($final_width,$final_height);
			
			// dest image, src image, 
			if ($true_crop == '1') {
				imagecopyresampled($dst_img,$src_img,0,0,0,0,$final_width,$final_height,$old_x,$old_y); 
			} else {
				imagecopyresampled($dst_img,$src_img,0,0,0,0,$final_width,$final_height,$old_x,$old_y); 
			}
			
			// Outtput the created image.
			if(! strcmp("png",$ext)) {
				imagepng($dst_img,$output_filename); 
			} else {
				imagejpeg($dst_img,$output_filename);
			}
			
			// Destroy what we don't need
			imagedestroy($dst_img); 
			imagedestroy($src_img);
			
			return "1";
			
		} else {
		
			return "0";
			
		}
		
	}
	

	// ---------------------------------------------------------------------------
	// 	Resize an image according to an input
	
	function get_resize($file_location, $new_w = '', $new_h = '', $larger_wins = '0', $max_height = '') {
	
		//gets the dimmensions of the image
		list($old_x, $old_y, $type, $attr) = getimagesize($file_location);

		$go = 1;
		if ($old_x <= $new_w && empty($new_h)) {
			$go = 0;
		}
		else if ($old_y <= $new_h && empty($new_w)) {
			$go = 0;
		}
		else if ($old_y <= $new_h && $old_y <= $new_h) {
			$go = 0;
		}
		else {
			$go = 1;
		}
		
		// Old bigger than new?
		if ($go == '1') {
		
			// Here we only have one of the two dimensions
			// submitted
			if ((empty($new_w) || empty($new_h)) || $larger_wins != '1') {

				$force_dimensions = '0';
				if ($larger_wins == '1') {
					if ($new_w > $new_h) {
						$new_w = '';
					} else {
						$new_h = '';
					}
				}
				
				if (! empty($new_w)) {
		   			$ratio1 = $old_x / $new_w;
		   			$use_width = '1';
		   			$final_width = $new_w;
		   			$final_height = $old_y / $ratio1;
				}
				else if (! empty($new_h)) {
		   			$ratio2 = $old_y / $new_h;
		   			$use_height = '1';
		   			$final_height = $new_h;
		   			$final_width = $old_x / $ratio2;
				}
				else {
		   			$final_width = $old_w;
		   			$final_height = $old_h;
				}
			}
			// Here we have both new dimensions
			// submitted.
			else {
			
		   		$ratio1 = $old_x / $new_w;
		   		$ratio2 = $old_y / $new_h;
		   		if ($ratio1 > $ratio2)	{
		   			$use_width = '1';
		   			$final_width = $new_w;
		   			$final_height = $old_y / $ratio1;
		   		} else {
		   			$use_height = '1';
		   			$final_height = $new_h;
		   			$final_width = $old_x / $ratio2;
		   		}
	   		}
	   		
	   		if (! empty($max_height) && $final_height > $max_height) {
	   			$final_height = $max_height;
	   		}
	   		
	   		if (! empty($max_height) && $final_height > $max_height) {
	   			$final_height = $max_height;
	   		}
	   	
   			$array_put = array(ceil($final_width),ceil($final_height));
   		
   		} else {
   		
   			$array_put = array($old_x,$old_y);
   			
   		}
   		return $array_put;
	}


	// ---------------------------------------------------------------------------
	// 	Get a file's extension
	
	function get_extension($file) {
		$exp_file = explode('.',$file);
		$elements = sizeof($exp_file)-1;
		$ext = strtolower($exp_file[$elements]);
		return strtolower($ext);
	}


	// -----------------------------------------------------------------------------
	// 	Get image tags
	
	function get_tags($id) {
		$tags = array();
		$q = "SELECT `tag` FROM `" . TABLE_PREFIX . "media_tags` WHERE `img_id`='" . $this->mysql_clean($id) . "'";
		$results = $this->run_query($q);
		while ($row = mysql_fetch_array($results)) {
			$tags[] = $row['tag'];
		}
		return $tags;
	} 
	
	
	// -----------------------------------------------------------------------------
	// 	Get image details
		
	function get_image($id = '',$tags = '1',$path = '') {
		if (empty($id)) {
			$q = "SELECT * FROM `" . TABLE_PREFIX . "media` WHERE `location`='" . $this->mysql_clean($path) . "'";
		} else {
			$q = "SELECT * FROM `" . TABLE_PREFIX . "media` WHERE `id`='" . $this->mysql_clean($id) . "'";
		}
		$img_info = $this->get_array($q);
		if ($tags == '1') {
			$theTags = $this->get_tags($id);
			$img_info['tags'] = $theTags;
		}
		// Width/Height
		$hold_location = $img_info['location'];
		$path = PATH . $img_info['location'];
		if (! file_exists($path)) {
			$use_path = $_SERVER['DOCUMENT_ROOT'];
			$path = $_SERVER['DOCUMENT_ROOT'] . $img_info['location'];
			$img_info['location'] = URL . $img_info['location'];
		} else {
			$use_path = PATH;
			$img_info['location'] = URL . $img_info['location'];
		}
		list($picx, $picy, $type, $attr) = @getimagesize($path);
		$img_info['width'] = $picx;
		$img_info['height'] = $picy;
		//$img_info['location'] = $img_info['location'];
		$img_info['path'] = $path;
		$img_info['url'] = URL . $img_info['location'];
		
		// Get paths
		$full_size = "<img src=\"" . $img_info['location'] . "\" alt=\"" . $img_info['title'] . "\" title=\"" . $img_info['title'] . "\" border=\"0\"";
		if (! empty($img_info['width']) && ! empty($img_info['height'])) {
			$full_size .= " width=\"" . $img_info['width'] . "\" height=\"" . $img_info['height'] . "\"";
		}
		$full_size .= " />";
		$img_info['image'] = $full_size;
		// Thumbnail
		$thumb_name = "tb-" . $img_info['filename'];
		$thumb = str_replace($img_info['filename'],$thumb_name,$img_info['location']);
		$check_thumb = str_replace(URL,PATH,$thumb);
		// Thumbnail
   		if (! file_exists($check_thumb)) {
   			$thumb_path = $use_path . "/generated/media/tb-" . $img_info['filename'];
   			if (! file_exists($thumb_path)) {
   				$full_path = $use_path . $img_info['location'];
   				$thumbnail = @$this->crop_image($full_path,$thumb_path,'250','','','0','0');
   				if ($thumbnail == '1') {
					$img_info['thumbnail'] = $use_path . "/generated/media/tb-" . $img_info['filename'];
					$img_info['thumbnail_url'] = URL . "/generated/media/tb-" . $img_info['filename'];
   				} else {
					$img_info['thumbnail'] = $path;
					$img_info['thumbnail_url'] = $thumb;
   				}
   			}
   		} else {
			$img_info['thumbnail'] = $thumb;
			$img_info['thumbnail_url'] = $thumb;
   		}
		return $img_info;
	}
	

	// -----------------------------------------------------------------------------
	// 	Mainly for widgets, gets images by tag.

	function get_images_by_tag($tags,$strict = '1',$date_before = '') {
		// $strict = '1';
		$where_add = '';
   		$theTags = explode(',',$tags);
   		if ($strict == '1') {
   			$total_tags = sizeof($theTags);
   		} else {
   			$total_tags = '';
   		}
   		foreach ($theTags as $tag) {
   			$tag = trim($tag);
   			$where_add .= " OR ";
   			$where_add .= TABLE_PREFIX . "media_tags.tag='" . $this->mysql_clean($tag) . "'";
   		}
   		$where_add = ltrim($where_add,' OR ');
   		$where_add = "(" . $where_add . ")";
   		// Date restrictions?
   		if (! empty($date_before) && $date_before != '0000-00-00 00:00:00') {
   			$where_add .= " AND `date`<='" . $this->mysql_clean($date_before) . "'";
   		}
	   	$final_array = array();
		// For strict tagging, all tags must
		// be matched. So we need to filter
		// all entries based on whether they
		// have returned enough results from
		// the database
		if ($strict == '1') {
			$q = "
				SELECT " . TABLE_PREFIX . "media.id
				FROM `" . TABLE_PREFIX . "media`
				INNER JOIN `" . TABLE_PREFIX . "media_tags`
				ON " . TABLE_PREFIX . "media.id=" . TABLE_PREFIX . "media_tags.img_id AND" . $where_add;
			$results = $this->run_query($q);
			// Add returned IDs to an array
			$final_ids = array();
			while ($row = mysql_fetch_array($results)) {
				$final_ids[] = $row['id'];
			}
			// Find all that match all of the tags.
	   		$counts = array_count_values($final_ids);
	   		foreach ($counts as $id => $total) {
	   			if ($total >= $total_tags) {
	   				$final_array[] = $id;
	   			}
	   		}
		}
		// For non-strict tagging, any tag must
		// match. So we need to get all results
		// and remove duplicates.
		else {
			$q = "
				SELECT " . TABLE_PREFIX . "media.id, GROUP_CONCAT(" . TABLE_PREFIX . "media.id separator ',') as THEIDS
				FROM `" . TABLE_PREFIX . "media`
				INNER JOIN `" . TABLE_PREFIX . "media_tags`
				ON " . TABLE_PREFIX . "media.id=" . TABLE_PREFIX . "media_tags.img_id AND " . $where_add;
			$results = $this->get_array($q);
			// Create an array with the results
			$final_ids = explode(',',$results['THEIDS']);
			// Get only unique IDs
			$final_array = array_unique($final_ids);
		}
		// Return the array
		return $final_array;
	}
	

	// -----------------------------------------------------------------------------
	// 	Get content to display in the media library

	function get_media_library($tags = '',$view_type = 'list') {
		global $user;
		global $privileges;
		$where = '';
		// Privs?
		if ($privileges['is_admin'] != '1') {
			$where = " WHERE (" . TABLE_PREFIX . "media.public='1' OR " . TABLE_PREFIX . "media.owner='$user')";
		} else {
			$where = '';
		}
		// Filter by tags?
		if (! empty($tags)) {
			$theTags = explode(',',$tags);
			foreach ($theTags as $tag) {
				$where_add .= " OR " . TABLE_PREFIX . "media_tags.tag='" . $this->mysql_clean($tag) . "'";
				$where_add1 .= " OR " . TABLE_PREFIX . "media.filename LIKE '%" . $this->mysql_clean($tag) . "%'";
			}
			$where_add = ltrim($where_add,' OR ');
			$where_add1 = ltrim($where_add1,' OR ');
			if (empty($where)) {
				$where = " WHERE (" . $where_add . ") OR (" . $where_add1 . ")";
			} else {
				$where = $where . " AND (" . $where_add . ") OR (" . $where_add1 . ")";
			}
			$q = "SELECT " . TABLE_PREFIX . "media.id FROM `" . TABLE_PREFIX . "media`
				INNER JOIN `" . TABLE_PREFIX . "media_tags`
				ON " . TABLE_PREFIX . "media.id=" . TABLE_PREFIX . "media_tags.img_id" . $where;
		}
		// No tags, just get content
		else {
			$q = "SELECT `id` FROM `" . TABLE_PREFIX . "media` $where ORDER BY `date` ASC"; // LIMIT 50
		}
		$results = $this->run_query($q);
		$found_all = array();
		$current = 0;
		if ($view_type == 'gallery') {
			$library = "<table><tbody><tr>";
		} else {
			$library = "
				<script language=\"JavaScript\" src=\"" . URL . "/js/jquery.tablesorter.min.js\"></script> 
				<script type=\"text/javascript\" language=\"JavaScript\">
				<!--
				$(document).ready(function() {
					// call the tablesorter plugin
					$(\"#file_table\").tablesorter({
						// sort on the first column order desc
						sortList: [[1,0]],
						headers: { 4:{sorter: false}, 5:{sorter: false}}
					});
				});
				-->
				</script>
			";
			$library .= "<table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" border=\"0\" id=\"file_table\">";
			$library .= "<thead><tr>";
			$library .= "<th width=\"100\">Thumbnail</th>";
			$library .= "<th>Name</th>";
			$library .= "<th>Title</th>";
			$library .= "<th>Caption</th>";
			$library .= "<th>Tags</th>";
			$library .= "<th>Options</th>";
			$library .= "</tr></thead><tbody>";
		}
		while ($row = mysql_fetch_array($results)) {
			if (! in_array($row['id'],$found_all)) {
				$tags = '';
				$found_all[] = $row['id'];
				$thisImg = $this->get_image($row['id'],'1');
				$cpath = $thisImg['path'];
				if (file_exists($cpath)) {
	   				$path =  $thisImg['thumbnail'];
   	   				if ($view_type == 'gallery') {
   	   					$thumb = @$this->get_resize($path,'200','50','0','50');
   	   				} else {
   	   					$thumb = @$this->get_resize($path,'85','50','0','50');
   	   				}
   					$add_class = "";
   					$options = "
   					<span class=\"add_img\"><a href=\"#\" onclick=\"editImage('" . $row['id'] . "','add');return false;\">Add to Page</a></span>
   					<span class=\"edit_img\"><a href=\"#\" onclick=\"editImage('" . $row['id'] . "','edit');return false;\">Edit</a></span>
   					<span class=\"del_img\"><a href=\"#\" onclick=\"deleteMedia('" . $row['id'] . "');return false;\">Delete</a></span>
   					";
   				} else {
   					$add_class = "error";
   					$options = "
   					<span class=\"edit_img\"><a href=\"#\" onclick=\"editImage('" . $row['id'] . "','edit');return false;\">Edit</a></span>
   					<span class=\"del_img\"><a href=\"#\" onclick=\"deleteMedia('" . $row['id'] . "');return false;\">Delete</a></span>
   					";
   				}
   				
   				// Tags
    				if (! empty($thisImg['tags'])) {
    					$tags .= "<div class=\"tag\">";
    					foreach ($thisImg['tags'] as $aTag) {
    						$tags .= "<span class=\"tag\">$aTag</span>";
    					}
    					$tags .= "</div>";
    				}
   				
   				// Display Item
				if ($view_type == 'gallery') {
   				
	   				$library .= "<td id=\"imgtd" . $row['id'] . "\" class=\"img_td\"><div class=\"image_entry $add_class\" id=\"img" . $row['id'] . "\">";
	   				// <a href=\"" . URL . $thisImg['location'] . "\" target=\"_blank\">
	   				$library .= "<a href=\"#\" onclick=\"viewFullImage('" . $thisImg['location'] . "','" . $thisImg['title'] . "','" . $thisImg['title'] . "','" . $thisImg['width'] . "','" . $thisImg['height'] . "');\"><img src=\"" . $thisImg['thumbnail_url'] . "\" width=\"" . $thumb['0'] . "\" height=\"" . $thumb['1'] . "\" alt=\"" . $thisImg['title'] . "\" title=\"" . $thisImg['title'] . "\" border=\"0\" /></a>";
	   				$library .= "<div class=\"img_options\">$options</div>";
	   				if (! empty($thisImg['caption'])) {
	   					$library .= "<p class=\"caption\">" . $thisImg['caption'] . "</p>";
	   				} else {
	   					$library .= "<p class=\"caption\"><i>No caption</i></p>";
	   				}
	   				$library .= $tags;
	   				if ($add_class == 'error') {
	   					$library .= "<p class=\"caption\" style=\"color:red;\">File not found on your server.</p>";
	   				}
	   				$library .= "</div></td>";
   				
   				} else {
   				
		   			$library .= "<tr id=\"imgtd" . $row['id'] . "\" class=\"img_td\">";
		   			$library .= "<td valign=\"_top\"><a href=\"#\" onclick=\"viewFullImage('" . $thisImg['location'] . "','" . $thisImg['title'] . "','" . $thisImg['title'] . "','" . $thisImg['width'] . "','" . $thisImg['height'] . "');\"><img src=\"" . $thisImg['thumbnail_url'] . "\" width=\"" . $thumb['0'] . "\" height=\"" . $thumb['1'] . "\" alt=\"" . $row['title'] . "\" title=\"" . $row['title'] . "\" border=\"0\" /></a></td>";
		   			$library .= "<td valign=\"top\"><a href=\"" . URL . $thisImg['location'] . "\" target=\"_blank\">" . $thisImg['filename'] . "</a></td>";
		   			$library .= "<td valign=\"top\">" . $thisImg['title'] . "</td>";
		   			$library .= "<td valign=\"top\">" . $thisImg['caption'] . "</td>";
		   			$library .= "<td valign=\"top\">" . $tags . "</td>";
		   			$library .= "<td valign=\"top\" class=\"img_options\">$options</td>";
		   			$library .= "</tr>";
   					
   				}
				
			}
		}
		$library .= "</tr></tbody></table>";
		return $library;
	}
	
	// -----------------------------------------------------------------------------
	// 	Delete an image
	
	function delete_media($id) {
		global $user;
		global $privileges;
		$thisImg = $this->get_image($id,'0');
		if ($privileges['is_admin'] == '1' || $thisImg['owner'] == $user) {
			// Delete the media entry
			$q = "DELETE FROM `" . TABLE_PREFIX . "media` WHERE `id`='" . $this->mysql_clean($id) . "' LIMIT 1";
			$delete = $this->delete($q);
			// Delete the tags
			$q1 = "DELETE FROM `" . TABLE_PREFIX . "media_tags` WHERE `img_id`='" . $this->mysql_clean($id) . "'";
			$delete1 = $this->delete($q1);
			// Delete the physical file
			$path = PATH . $thisImg['location'];
			$unlink = @unlink($path);
			// Reply
			echo "1+++Deleted";
			exit;
		} else {
			echo "0+++" . lg_no_permissions;
			exit;
		}
	}


	// -----------------------------------------------------------------------------
	// 	Get file library of downloads
	
	function get_file_library() {
		global $user;
		global $privileges;
		$where = '';
		// Privs?
		if ($privileges['is_admin'] != '1') {
			$where = " WHERE `owner`='$user'";
		} else {
			$where = '';
		}
		$q = "SELECT `id` FROM `" . TABLE_PREFIX . "attachments` $where ORDER BY `filename` DESC"; // LIMIT 50
		$results = $this->run_query($q);
		$found_all = array();
		$current = 0;
		// Begin the table
   		$library = "
   			<script language=\"JavaScript\" src=\"" . URL . "/js/jquery.tablesorter.min.js\"></script> 
   			<script type=\"text/javascript\" language=\"JavaScript\">
   			<!--
   			$(document).ready(function() {
   				// call the tablesorter plugin
   				$(\"#file_table\").tablesorter({
   					// sort on the first column order desc
   					sortList: [[0,0]],
   					headers: { 3:{sorter: false}, 4:{sorter: false}}
   				});
   			});
   			-->
   			</script>
   		";
		$library .= "<table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" border=\"0\" id=\"file_table\">";
		$library .= "<thead><tr>";
		$library .= "<th>Name</th>";
		$library .= "<th>Access</th>";
		$library .= "<th>Limit per User</th>";
		$library .= "<th>Downloaded</th>";
		$library .= "<th>Option</th>";
		$library .= "</tr></thead><tbody>";
		while ($row = mysql_fetch_array($results)) {
   			$this_file = $this->get_file_info($row['id']);
   			if ($this_file['login'] == '1') {
   				$status = "Login Protected";
   			} else {
   				$status = "Public";
   			}
   			$this_file = $this->get_file_info($row['id']);
   			$library .= "<tr id=\"imgtd" . $row['id'] . "\" class=\"file_entry\">";
   			$library .= "<td><a href=\"" . $this_file['url'] . "\" target=\"_blank\">" . $this_file['name'] . "</a></td>";
   			$library .= "<td>" . $status . "</td>";
   			$library .= "<td>" . $this_file['limit'] . "</td>";
   			$library .= "<td>" . $this_file['downloads'] . "</td>";
   			$library .= "<td class=\"img_options\">
   				<span class=\"add_img\"><a href=\"#\" onclick=\"addFileToPage('" . $this_file['id'] . "');return false;\">Add to Page</a></span>
   				<span class=\"edit_img\"><a href=\"#\" onclick=\"editFile('" . $row['id'] . "','edit');return false;\">Edit</a></span>
   				<span class=\"del_img\"><a href=\"#\" onclick=\"deleteFile('" . $row['id'] . "');return false;\" >Delete</a></span>
   			</td>";
   			$library .= "</tr>";
		}
		$library .= "</tbody></table>";
		return $library;
	}


	// -----------------------------------------------------------------------------
	// 	Delete File
	
	function delete_file($id) {
		global $user;
		global $privileges;
		$this_file = $this->get_file_info($id);
		if ($privileges['is_admin'] == '1' || $this_file['owner'] == $user) {
			// Delete the media entry
			$q = "DELETE FROM `" . TABLE_PREFIX . "attachments` WHERE `id`='" . $this->mysql_clean($id) . "' LIMIT 1";
			$delete = $this->delete($q);
			// Delete the physical file
			$path = $this_file['path'];
			$unlink = @unlink($path);
			// Reply
			echo "1+++Deleted";
			exit;
		} else {
			echo "0+++" . lg_no_permissions;
			exit;
		}
	}
	
	// -----------------------------------------------------------------------------
	// 	Create a gallery
	
	function create_gallery($title,$tags,$tags_strict = '0',$thumb_width = '200',$cols = '2',$date_before = '') {
		global $user;
		global $privileges;
		// HTML
		$html = "<ul class=\"img_gallery\">";
		$html .= "%entries%";
		$html .= "</ul>";
		// HTML Entries
		if ($cols == '1') {
			$html_insert = "<li>%image%</li>";
		} else {
			$col_style = (100 / $cols) - 3;
			$col_style_put = " style=\"";
			if ($cols > 1) {
				$col_style_put .= "float:left;";
			}
			$col_style_put .= "width:$col_style% !important;margin-right: 2% !important;\"";
			$html_insert = "<li $col_style_put>%image%</li>";
		}
		// Options
		$options_array = array(
			'tags' => $tags,
			'strict' => $strict,
			'thumb_width' => $thumb_width,
			'columns' => $cols,
			'not_after' => $date_before,
		);
		$options = serialize($options_array);
		// Create widget
		$q = "
		INSERT INTO `" . TABLE_PREFIX . "widgets` (`date`,`name`,`owner`,`type`,`html`,`html_insert`,`active`,`options`)
		VALUES ('" . $this->current_date() . "','" . $this->mysql_clean($title) . "','$user','7','$html','$html_insert','1','" . $this->mysql_clean($options) . "')
		";
		$insert = $this->insert($q);
		// Reply
		echo "1+++$insert";
		exit;
	}

	// -----------------------------------------------------------------------------
	// 	Get list of galleries

	function get_galleries() {
		global $user;
		global $privileges;
		$list = '';
		if ($privileges['is_admin'] == '1') {
			$add_where = "";
		} else {
			$add_where = " AND `owner`='$user'";
		}
		$q = "SELECT `id`,`name` FROM `" . TABLE_PREFIX . "widgets` WHERE `type`='7'$add_where ORDER BY `name` DESC";
		$results = $this->run_query($q);
		while ($row = mysql_fetch_array($results)) {
			$list .= "<option value=\"" . $row['id'] . "\">" . $row['name'] . "</option>";
		}
		return $list;
	}

	
	// -----------------------------------------------------------------------------
	// 	Import Images from Directory
	
	function get_images_in_dir($dir,$plain_name = '') {
		global $theme;
		global $db;
		// Run command
		if ($handle = opendir($dir)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					$full = $dir . "/" . $file;
					
					$cut_path = str_replace(PATH,'',$full);
					$ext = $this->get_extension($file);
					
					if (is_dir($full)) {
						$this->get_images_in_dir($full,$file);
					}
					else if ($ext != 'jpg' && $ext != 'jpeg' && $ext != 'png' && $ext != 'gif') {
						continue;
					}
					else {
						$q = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "media` WHERE `location`='$cut_path'";
						$found = $db->get_array($q);
						if ($found['0'] <= 0) {
							// Import to media center
							$q1 = "
								INSERT INTO `" . TABLE_PREFIX . "media` (`location`,`filename`,`owner`,`date`,`public`,`folder`)
								VALUES ('$cut_path','$file','1','" . $db->current_date() . "','1','" . $theme . "')
							";
							$insert = $db->insert($q1);
							// Tags
							$q2 = "
								INSERT INTO `" . TABLE_PREFIX . "media_tags` (`img_id`,`tag`)
								VALUES ('$insert','" . $theme . "')
							";
							if (! empty($plain_name)) {
								$q2 .= ", ('$insert','" . $db->mysql_clean($plain_name) . "')";
							}
							$insert1 = $db->insert($q2);
						}
					}
				}
			}
			closedir($handle);
		}
	}

}

?>