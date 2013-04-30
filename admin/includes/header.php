<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<title>Banana Dance Administrative</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="author" content="Ascad Networks" />
	<meta name="description" content="Banana Dance administrative control panel." />
	<link href="<?php echo ADMIN_URL; ?>/css/admin_primary.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo ADMIN_URL; ?>/css/_admin_style.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo URL; ?>/templates/html/_css/css_system.css" rel="stylesheet" type="text/css" />
	<script src="<?php echo URL; ?>/js/jquery.js" type="text/javascript"></script>
	<script src="<?php echo URL; ?>/js/jquery.ctrl.js" type="text/javascript"></script>
	<script src="<?php echo URL; ?>/js/callers.js" type="text/javascript"></script>
	<script src="<?php echo URL; ?>/js/admin.js" type="text/javascript"></script>
	<script src="<?php echo URL; ?>/js/jquery.ui.js" type="text/javascript"></script>
	<script src="<?php echo URL; ?>/js/jquery.tablesorter.min.js" type="text/javascript"></script> 

<?php
	if ($db->get_option('editor_type') == 'WYSIWYG') {
	echo '<link rel="stylesheet" type="text/css" href="' . URL . '/js/cleditor/jquery.cleditor.css" />';
	echo '<script type="text/javascript" src="' . URL . '/js/cleditor/jquery.cleditor.js"></script>';
	echo '<script type="text/javascript" src="' . URL . '/js/cleditor/jquery.cleditor.table.js"></script>';
	}
?>
   		
	<script type="text/javascript">
	<!--
	
   	// --------------------------------------------
   	//	Ready to go!
	
	$(document).ready(function() {
	
		<?php
			if (! empty($_GET['help'])) {
		?>
		
			var help_object = "<?php echo $_GET['help'] ?>";
			flash_div(help_object);

		<?php	
			}
		?>
	
	   	// --------------------------------------------
	   	//	Top Dropdown
	   	//	Makes the links appear when a mouse
	   	//	goes over a main nav link in the corner.
	   	//	I have no idea if this comment makes
	   	//	sense. It is 2:04am and I just replied
	   	//	to some guy who thought my whole company
	   	//	was a lie and I was really a crook from
	   	//	Kazakhstan. Go figure eh?
	   	//
	   	//	tl;dr: Makes links for you to click. And
	   	//	no I'm not from Kazakhstan, although I'm
	   	//	sure it's a great place.
   	
   		$('#main_nav li').hover(
			function () {
   				li_parent = $(this).attr('id');
   				ul_id = li_parent + '_ul';
   				$('#' + ul_id).show();
			},
			function () {
   				li_parent = $(this).attr('id');
   				ul_id = li_parent + '_ul';
   				$('#' + ul_id).hide();
			}
   		);
   		
	   	// --------------------------------------------
	   	//	Inputs
	   		
   		$(".option_list input").click(function() {
   			li_parent = $(this).closest('ul').attr('id');
   			
   			$("#" + li_parent + " li").removeClass("selected");
   			$("#" + li_parent + " li").removeClass("checked");
   			$(this).parent().addClass("selected");
   		});
   		
	
	   	// --------------------------------------------
	   	//	Help Bubbles
	   	
		$(".help").hover(
			function () {
				id = $(this).attr('id');
				show = id + 'b';
				var offset = $('#' + id).offset();
				left = offset.left + 10;
				topG = offset.top - 60;
				$('#' + show).css('top',topG);
				$('#' + show).css('left',left);
				$('#' + show).fadeIn('200');
			},
			function () {
				id = $(this).attr('id');
				show = id + 'b';
				$('#' + show).fadeOut('200');
			}
		);
	
	
	   	// --------------------------------------------
	   	//	Re-order pages
	   	
		$(function() {
			$('.main_left ul').sortable({
				connectWith: $('.inner'),
				toleranceElement: 'div',
				update: function(event, ui) {
					var addit = 'values=';
					// Organize the items
					var myOrder = new Array();
					$(".main_left li").each(function() {
						current_id = $(this).attr("id");
						parent = $('#' + current_id).closest('ul').attr('id');
						if (current_id === undefined || current_id == 'category_0' || current_id == 'ex_0') {
							// Nothing...
						} else {
							if (parent === undefined) {
								parent = 'c-0';
							}
							addit += "----" + current_id + ':' + parent;
						}
					});
					// Send to script
					$.post('functions/reorder_pages.php', addit, function(data) {
						showSaved();
					});
				}
			});
		});
		
	});
	
	
   	// --------------------------------------------
   	//	Flash a div
	
	function flash_div(help_object) {
		$('#' + help_object).fadeOut(150).fadeIn(150).fadeOut(150).fadeIn(150).fadeOut(150).fadeIn(150);
	}
	
   	// --------------------------------------------
   	//	Website Map
   	
	var toggled = 0;
	var on = '';
	function expandCate(id) {
		if ($('#ex_' + id).is(":visible")) {
			$('#ex_' + id).slideUp();
			$('#ex_' + id + '_img').attr('src','imgs/icon_expand.png');
		} else {
			$('#ex_' + id).slideDown();
			$('#ex_' + id + '_img').attr('src','imgs/icon_contract.png');
		}
	}
	
	function toggleAll() {
		if (toggled == 1) {
			$('.subcategory').not('.toplevel').hide();
			$('.expand').attr('src','imgs/icon_expand.png');
			toggled = 0;
		} else {
			$('.subcategory').not('.toplevel').show();
			$('.expand').attr('src','imgs/icon_contract.png');
			toggled = 1;
		}
	}
	
	
   	// --------------------------------------------
   	//	Refresh Map
   	
	function refreshMap() {
	   	$.post('functions/get_tree.php', '', function(data) {
	   		$('#update_map').fadeOut('300', function() {
	   			$('#update_map').html(data);
	   			$('#update_map').fadeIn('300');
	   		});
	   	});
	}
	
	
   	// --------------------------------------------
   	//	Load a page
   	
	function loadPage(id,type) {
		$('ul li').removeClass('on');
		if (type == 'category') {
			fid = 'category_' + id;
			file = "category_edit";
		} else {
			fid = 'page_' + id;
			file = "article_edit";
		}
		$('#' + fid).addClass('on');
		get = "id=" +  id;
		loadLink(file,get);
	
	}
	
	function loadLink(link,get) {
		$('#loading').show();
		get_data = "l=" + link;
		if (get) {
			get_data += '&' + get;
		}
		url = "loader.php";
	    	$.get(url, get_data, function(theResponse) {
	    		updateContent(theResponse);
			$('#loading').hide();
	    	});
	}
	
	function updateContent(data) {
		$('#primary_content').html(data);
	}
	


   	// --------------------------------------------
   	//	CTRL-R returns to last page.
   	
   	$.shift('R', function() {
   	    window.location='functions/return_to_last_viewed.php';
   	});
	
	-->
	</script>

</head>
<body>

<div id="logo" onclick="window.location='index.php?l=home';"></div>
<div id="banana"><img src="imgs/bd_top_right.png" border="0" alt="This is one smart banana!" title="This is one smart banana!" /></div>
<div id="banana_text"><?php echo $admin->random_quote(); ?></div>


<div id="topbar">
	<div id="topleft">
		<span>Welcome <?php echo $user; ?></span>
		<span><a href="<?php echo URL; ?>">Website Home</a></span>
	</div>
	<ul id="main_nav">
		<li id="nav_pages">
			<a href="index.php?l=articles" id="nav_page">Pages</a>
			<ul id="nav_pages_ul" class="subnavul">
				<li><a href="index.php?l=categories">Categories</a></li>
				<li class="padl"><a href="index.php?l=category_add">New</a></li>
				<li><a href="index.php?l=downloads">Downloads</a></li>
				<li class="padl"><a href="index.php?l=download_activity">Activity</a></li>
				<li><a href="index.php?l=comments">Comments</a></li>
				<li><a href="index.php?l=comment_types">Comment Types</a></li>
				<li class="padl"><a href="index.php?l=comment_types_add">New</a></li>
				<li><a href="index.php?l=widgets">Widgets</a></li>
				<li><a href="index.php?l=plugins">Plugins</a></li>
				<li><a href="index.php?l=replacements">Custom Callers</a></li>
				<li class="padl"><a href="index.php?l=caller_add">New</a></li>
			</ul>
		</li>
		<li id="nav_users"><a href="index.php?l=users">Users</a>
			<ul id="nav_users_ul" class="subnavul">
				<li><a href="index.php?l=users_add">Add User</a></li>
				<li><a href="index.php?l=users_banned">Banned Users</a></li>
				<li><a href="index.php?l=user_types">User Types</a></li>
				<li class="padl"><a href="index.php?l=user_types_add">New</a></li>
				<li><a href="index.php?l=points">Point Values</a></li>
				<li><a href="index.php?l=badges">Badges</a></li>
			</ul>
		</li>
		<li id="nav_lookfeel"><a href="index.php?l=themes">Look &amp; Feel</a>
			<ul id="nav_lookfeel_ul" class="subnavul">
				<li><a href="index.php?l=themes">Theme</a></li>
				<li><a href="index.php?l=themes_mobile">Mobile Theme</a></li>
				<li><a href="index.php?l=templates_html">HTML Templates</a></li>
				<li><a href="index.php?l=templates_email">E-Mail Templates</a></li>
				<li><a href="index.php?l=logo">Logo</a></li>
				<!--<li><a href="index.php?l=styles">Theme Styles</a></li>-->
				<!--<li><a href="index.php?l=fields">Registration Fields</a></li>-->
			</ul>
		</li>
		<li id="more_space"><a href="index.php?l=options&set=1">Settings</a>
			<ul id="more_space_ul" class="subnavul">
				<?php
					$q = "SELECT `key`,`value` FROM `" . TABLE_PREFIX . "options` WHERE `type`='2' ORDER BY `value` ASC";
					$option_sets = $db->run_query($q);
					while ($row = mysql_fetch_array($option_sets)) {
						echo "<li><a href=\"index.php?l=options&set=" . $row['key'] . "\">" . $row['value'] . "</a></li>";
					}
				?>
				<li><a href="index.php?l=backup_db">Backup Content</a></li>
				<li><a href="index.php?l=update">Update Program</a></li>
				<li class="padl"><a href="index.php?l=update&no_files=1">Database Only</a></li>
				<li class="padl"><a href="index.php?l=update&no_db=1">Files Only</a></li>
			</ul>
		</li>
		<li id="icon_logout" onclick="window.location='functions/return_to_last_viewed.php';"><a href="functions/return_to_last_viewed.php"><img src="imgs/blank.gif" alt="Close Control Panel" title="Close Control Panel" border="0" width="20" height="20" /></a></li>
	</ul>
</div>

<div id="blue">
	<div id="clouds"></div>
</div>
<div class="clear"></div>

<div class="contain">
	
	<div id="content">
		<div class="main_left box medium_text" id="site_map_area">
			<p class="sitemap">Site Map</p>
			<p class="toggle"><a href="#" onclick="toggleAll();return false;">Toggle All</a></p>

			<span id="update_map">
			<?php
			$totals_components = $admin->totals();
			$map = $admin->website_tree();
			echo $map;
			if ($totals_components['all'] <= 30) {
				echo "<script type=\"text/javascript\">toggleAll();</script>";
			}
			?>
			</span>
			
		</div>
		<div class="main_right box"><div class="box_pad">
			<div id="primary_content">
				<div id="right_contain">
			