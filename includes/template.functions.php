<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Template rendering functions.
	
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


class template extends session {

	// --------------------------------------------------------------------
	// 	Get theme
	
	function get_theme() {
		$theme_array = array();
		// ---------------
		// Mobile?
		if (BD_MOBILE == '1') {
			// It should be in the globals file.
			if (array_key_exists('mobile_theme', $GLOBALS) && ! empty($GLOBALS['mobile_theme'])) {
				$theme_array['theme'] = $GLOBALS['mobile_theme'];
				$theme_array['theme_type'] = 'mobile';
			}
			// This is a fall back if there is an issue
			// with the generated/globals.php file.
			else {
				$q = "SELECT `folder_name`,`type` FROM `" . TABLE_PREFIX . "themes` WHERE `mobile`='1' AND `selected`='1' LIMIT 1";
				$result = $this->get_array($q);
				if (empty($result['folder_name'])) {
					$theme_array['theme'] = "shake_my_hand";
					$theme_array['theme_type'] = "mobile";
				} else {
					$theme_array['theme'] = $result['folder_name'];
					$theme_array['theme_type'] = $result['type'];
				}
			}
		}
		// ---------------
		// PC Browser
		else {
			// It should be in the globals file.
			if (array_key_exists('theme', $GLOBALS) && ! empty($GLOBALS['theme'])) {
				$theme_array['theme'] = $GLOBALS['theme'];
				$theme_array['theme_type'] = $GLOBALS['theme_type'];
			}
			// This is a fall back if there is an issue
			// with the generated/globals.php file.
			else {
				$q = "SELECT `folder_name`,`type` FROM `" . TABLE_PREFIX . "themes` WHERE `selected`='1' AND `mobile`!='1' LIMIT 1";
				$result = $this->get_array($q);
				if (empty($result['folder_name'])) {
					$theme_array['theme'] = "default";
					$theme_array['theme_type'] = "wiki";
				} else {
					$theme_array['theme'] = $result['folder_name'];
					$theme_array['theme_type'] = $result['type'];
				}
			}
		}
		return $theme_array;
	}
	
	
	// --------------------------------------------------------------------
	// 	Get the contents of template
	//	Generally for emails, but used on
	//	admin for editing templates.
	function get_contents($type,$name,$html_type = '0',$template_id = '',$template_info = '') {
		// Get extension
		global $theme;
		// Here we are getting a standard
		// default template.
		
		if ($type == 'email') {
				$location = PATH . "/templates/" . $type . "/" . $name . ".html";
		} else {
			if ($html_type == '0' || empty($html_type)) {
				if ($name == "style.css") {
					$type = $type . "/" . $theme;
					$ext = 'css';
					$name = "style";
				} else {
					if ($type == "html") {
						$type = $type . "/" . $theme;
						$ext = 'php';
					} else {
						$ext = 'php';
					}
				}
				$location = PATH . "/templates/" . $type . "/" . $name . "." . $ext;
			}
			// Here we are getting a custom
			// HTML template.
			else {
				if (! empty($template_info['path'])) {
					$location = PATH . "/templates/html/" . $template_info['theme'] . "/" . $template_info['path'];
				} else {
					$location = PATH . "/generated/template-" . $template_id . ".php";
				}
			}
		}
		
		if (file_exists($location) && filesize($location) > 0) {
			$file = fopen($location, 'r');
			$contents = fread($file, filesize($location));
			fclose($file);
		} else {
			$contents = "";
		}
		return $contents;
	}
	
	
	// --------------------------------------------------------------------
	//   Render an HTML template with header, footer, and content.
	
	function render_template($template_name,$username = "",$special_changes = "",$skip_headfoot = "0",$skip_widgets = "0",$user_data_in = '',$custom_template_id = '0',$skip_user_bar = '0',$skip_minify = '0') {
		// Skip head/foot?
		if ($template_name == 'header' || $template_name == 'footer') {
			$skip_headfoot = '1';
		}
		// Theme
		if (! empty($user_data_in)) {
			$user_data = $user_data_in;
			$username = $user_data_in['username'];
		} else {
			if (empty($username)) {
				global $user_data;
				$username = $user_data['username'];
			} else {
				$user_data = $this->get_user_data($username);
			}
		}
		global $theme;
		global $manual;
		// Empty theme?
		if (empty($theme)) {
			$theme = 'default';
		}
		$header = '';
		$footer = '';
		
		// Get the content
		$template_info = $this->template_data($template_name,$custom_template_id);
		if (! empty($custom_template_id)) {
			if (! empty($template_info['path'])) {
				$location = THEME_PATH . "/" . $template_info['path'];
			} else {
				$location = PATH . "/generated/template-" . $custom_template_id . ".php";
			}
			if (! file_exists($location)) {
				$location = THEME_PATH . "/" . $template_name . ".php";
			}
		} else {
			$location = THEME_PATH . "/" . $template_name . ".php";
		}
   		ob_start();
   		include($location);
   		$contents = ob_get_contents();
   		ob_end_clean();
		// User changes
		if (! empty($username)) {
			$contents = $this->process_user($contents,$username,$user_data);
		}
   		// Special caller tag changes?
   		if (! empty($special_changes)) {
   			foreach ($special_changes as $tagname => $tagvalue) {
   				$contents = str_replace($tagname,$tagvalue,$contents);
   			}
   		}
		// Standard changes
		$contents = $this->process_standard($contents,$skip_widgets,$user_data,$skip_user_bar,$skip_headfoot,$template_name);
		// Header and Footer
		if ($skip_headfoot != "1") {
			if (! empty($template_info['custom_header'])) {
				$head_id = $template_info['custom_header'];
			} else {
				$head_id = '';
			}
			if (! empty($template_info['custom_footer'])) {
				$foot_id = $template_info['custom_footer'];
			} else {
				$foot_id = '';
			}
			$header = $this->render_template('header',$username,$special_changes,'1','0','',$head_id);
			$footer = $this->render_template('footer',$username,$special_changes,'1','0','',$foot_id);
		}
		// Combine it all
		$final = $header . $contents . $footer;
		// Widgets?
    		preg_match_all('/\{\-(.*?)\-\}/', $final, $widgets);
    		foreach ($widgets['0'] as $aWidget) {
    			$no_paragraph = "1";
    			$widgets_temp = str_replace('{-','',$aWidget);
    			$widgets_temp = str_replace('-}','',$widgets_temp);
    			$widget_data = $manual->get_widget($widgets_temp);
 	   		$line = str_replace($widgets['0'],$widget_data,$line);
    		}
    		// Minify code?
    		if ($this->get_option('minify_code') == '1' && $skip_minify != '1') {
    			$final = $this->minify_code($final);
    		}
		// Return contents
		return $final;
	}


	// --------------------------------------------------------------------
	// 	Minify code
	
	function minify_code($code) {		
		// Let's remoce comments from javascript
		// while making it safe for use... or at
		// least trying to.
   		$code = str_replace("\t","  ",$code);
    		preg_match_all('/<pre(.*)<\/pre>/Uis', $code, $precode);
    		foreach ($precode['0'] as $aCode) {
    			$hold_comment = $aCode;
    			$aComment = str_replace("\n",'##|n|)',$aCode);
    			$code = str_replace($hold_comment,$aComment,$code);
    		}
   		$code = str_replace("\t","  ",$code);
    		preg_match_all('/<script(.*)<\/script>/Uis', $code, $comments);
    		foreach ($comments['0'] as $aComment) {
    			$hold_comment = $aComment;
    			$aComment = preg_replace('/\/\*(.*)\*\//Uis', '', $aComment);
    			$aComment = str_replace('<!--','',$aComment);
    			$aComment = str_replace('-->','',$aComment);
    			$cut_up = explode("\n",$aComment);
    			foreach ($cut_up as $line) {
    				$foundA = strstr($line,'// ');
    				if ($foundA) {
    					$aComment = str_replace($foundA,'',$aComment);
    				}
    			}
    			$code = str_replace($hold_comment,$aComment,$code);
    		}
		// Now let's get rid of whitespace
   		$replace = array("\n","\r",'  ');
   		$code = str_replace($replace,"",$code);
   		// $code = preg_replace('/<!--(.*)-->/Uis', '', $code);
   		//$code = str_replace('<!--',"<!--\n",$code);
   		//$code = str_replace('-->',"\n-->",$code);
   		$code = str_replace('##|n|)',"\n",$code);
   		return $code;
	}
	

	// --------------------------------------------------------------------
	// 	Process user fields on a template
	//	%field_name%
	function process_user($data,$username,$user_data = '') {
		// global $session;
   		// Get the user's data
   		if (empty($user_data)) {
   			$user_data = $this->get_user_data($username);
   		}
		foreach ($user_data as $name => $value) {
			// Special considerations
			if ($name == 'joined') {
				$value = $this->format_date($value);
			}
			// Make the change
			$data = str_replace("%$name%",$value,$data);
		}
		// Other considerations
		return $data;
	}
	
	
	// --------------------------------------------------------------------
	// 	Create a list of templates
	//	Type 3 = article templates
	
	function list_templates($template_name = 'article', $type = '3', $selected = '') {
		if (empty($selected)) {
			$list = "<option value=\"\" selected=\"selected\">Default</option>";
		} else {
			$list = "<option value=\"\"></option>";
		}
		$q = "SELECT `id`,`title` FROM `" . TABLE_PREFIX . "templates_html` WHERE `type`='$type' and `template`='$template_name'";
		$results = $this->run_query($q);
		while ($row = mysql_fetch_array($results)) {
			if ($row['id'] == $selected) {
				$list .= "<option value=\"" . $row['id'] . "\" selected=\"selected\">" . $row['title'] . "</option>";
			} else {
				$list .= "<option value=\"" . $row['id'] . "\">" . $row['title'] . "</option>";
			}
		}
		return $list;
	}
	
	
	// --------------------------------------------------------------------
	// 	Find logo
	function find_logo() {
		$logo_option = $this->get_option('logo');
		if (! empty($logo_option)) {
			return $logo_option;
		}
		else {
			$path = PATH . "/generated/logo.png";
			$path1 = PATH . "/generated/logo.jpg";
			$path2 = PATH . "/generated/logo.gif";
			if (file_exists($path)) {
				return URL . "/generated/logo.png";
			}
			else if (file_exists($path1)) {
				return URL . "/generated/logo.jpg";
			}
			else if (file_exists($path2)) {
				return URL . "/generated/logo.gif";
			}
			else {
				return '';
			}
		}
	}
	
	// --------------------------------------------------------------------
	// 	Process standard fields on a template
	function process_standard($data,$skip_widgets = '0',$user_data = '',$skip_user_bar = '0',$header = '0',$template_name = '') {
		global $theme;
	//	global $manual;
		$manual = new manual;
		global $user;
		global $user_data;
		global $privileges;
		// Variables
		$lower_js = '';
		$headers = '';
		// Date
		$date = $this->format_date($this->current_date());
		// Query
		if (isset($_GET['q'])) {
			$query = $_GET['q'];
		} else {
			$query = '';
		}
		// Special to header/footer
		if ($template_name == 'header' || $template_name == 'footer') {
	   		// Global Defined?
	   		if (defined('BD_ARTICLE_VIEWING')) {
	   			$article_view = BD_ARTICLE_VIEWING;
	   		} else {
	   			$article_view = '0';
	   		}
	   		if (defined('BD_CATEGORY_VIEWING')) {
	   			$category_view = BD_CATEGORY_VIEWING;
	   		} else {
	   			$category_view = '0';
	   		}
	   		if (defined('BD_ARTICLE_COMMENT_TYPE')) {
	   			$article_com_type = BD_ARTICLE_COMMENT_TYPE;
	   		} else {
	   			$article_com_type = '0';
	   		}
			// Inline forms?
			if ($this->get_option('popup_registration') == 'Pop Up' && BD_MOBILE != '1') {
				$final_reg_type = '1';
			} else {
				$final_reg_type = '0';
			}
			if ($this->get_option('popup_login') == 'Pop Up' && BD_MOBILE != '1') {
				$final_login_type = '1';
			} else {
				$final_login_type = '0';
			}
			// Cache code
			if ($this->get_option('cache_category_list') == '1') {
				$cache_code = "$(window).load(function () { hideCategories('0'); expandCategory('$category_view'); });";
			} else {
				$cache_code = '';
			}
		}
   		// WYSIWYG?
   		if (! empty($user_data['option_editor'])) {
   			$editor_type = $user_data['option_editor'];
   		} else {
   			$editor_type = $this->get_option('editor_type');
   		}
		// Headers: CSS and Javascript
		// Including all CSS and Javascript for plugins.
		if ($template_name == 'header') {
			// Get the program defaults.
			$headers = '';
			//	Delayed indexing here... 3 days after last update:
			//	<meta name="robots" content="noindex,nofollow" />
			$q = "SELECT `plugin_name` FROM `" . TABLE_PREFIX . "widgets` WHERE `type`='5' AND `active`='1'";
			$plugins = $this->run_query($q);
			while ($row = mysql_fetch_array($plugins)) {
				$css_file = PATH . "/addons/widgets/" . $row['plugin_name'] . "/style.css";
				if (file_exists($css_file)) {
					$headers .= "<link href=\"" . URL . "/addons/widgets/" . $row['plugin_name'] . "/style.css\" rel=\"stylesheet\" type=\"text/css\" />\n";
				}
			}
			// Combine it all
			$headers .= "<link href=\"" . THEME . "/css_style.php\" rel=\"stylesheet\" type=\"text/css\" />\n";
			// Editor type style sheets
	   		if ($editor_type == 'WYSIWYG') {
		   		$headers .= '<link rel="stylesheet" type="text/css" href="' . URL . '/js/cleditor/jquery.cleditor.css" />';
	   		}
			$headers .= "<script src=\"" . URL . "/js/jquery.js\" type=\"text/javascript\"></script>\n";
			// $headers .= "<link rel=\"canonical\" href=\"http://www.example.com/product.php?item=swedish-fish\" />";
		}
		else if ($template_name == 'footer') {
			// Primary JS
			$lower_js .= "<script src=\"" . URL . "/js/jquery.selection.js\" type=\"text/javascript\"></script>\n";
			$lower_js .= "<script src=\"" . URL . "/js/callers.js\" type=\"text/javascript\"></script>\n";
			$lower_js .= "<script type=\"text/javascript\" language=\"javascript\">\n";
			$lower_js .= "	var current_article_id = \"" . $article_view . "\";\n";
			$lower_js .= "	var current_category_id = \"" . $category_view . "\";\n";
			$lower_js .= "	var current_status = \"" . $article_com_type . "\";\n";
			$lower_js .= "	var program_url = \"" . URL . "\";\n";
			$lower_js .= "	var theme = \"" . THEME . "\";\n";
			$lower_js .= "	var allow_registration = \"" . $this->get_option('allow_registration') . "\";\n";
			$lower_js .= "	var registration_type = '$final_reg_type';\n";
			$lower_js .= "	var login_type	= '$final_login_type';\n";
			if (! empty($cache_code)) {
				$lower_js .= "	$cache_code\n";
			}
			$lower_js .= "</script>\n";
			$lower_js .= "<script src=\"" . URL . "/js/primary.js\" type=\"text/javascript\"></script>\n";
			// Google Analytics?
			$google_analytics = $this->get_option('google_analytics_id');
			if (! empty($google_analytics)) {
				$lower_js .= "<script type=\"text/javascript\">\n";
				$lower_js .= "  var _gaq = _gaq || [];\n";
				$lower_js .= "  _gaq.push(['_setAccount', '" . $google_analytics . "']);\n";
				$lower_js .= "  _gaq.push(['_trackPageview']);\n";
				$lower_js .= "  (function() {\n";
				$lower_js .= "    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;\n";
				$lower_js .= "    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';\n";
				$lower_js .= "    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);\n";
				$lower_js .= "  })();\n";
				$lower_js .= "</script>";
			}
	   		// What type of editor are we using?
	   		if ($privileges['is_admin'] == '1' || $privileges['can_create_articles'] == '1' || $privileges['can_alter_articles'] == '1') {
	   			// Auto edit?
	   			if ($_GET['edit'] == '1') {
		   			$lower_js .= "<script type=\"text/javascript\">";
		   			$lower_js .= "$(document).ready(function() {";
		   			$lower_js .= "	editArticle('" . BD_ARTICLE_VIEWING . "');";
		   			$lower_js .= "});";
		   			$lower_js .= "</script>";
	   			}
	   			if ($_GET['create'] == '1') {
		   			$lower_js .= "<script type=\"text/javascript\">";
		   			$lower_js .= "$(document).ready(function() {";
		   			$lower_js .= "	editArticle('new');";
		   			$lower_js .= "});";
		   			$lower_js .= "</script>";
	   			}
	   			if ($editor_type == 'WYSIWYG') {
		   			$lower_js .= '<script type="text/javascript" src="' . URL . '/js/cleditor/jquery.cleditor.js"></script>';
		   			$lower_js .= '<script type="text/javascript" src="' . URL . '/js/cleditor/jquery.cleditor.table.js"></script>';
	   			}
	   		}
			$lower_js .= "<script src=\"" . URL . "/js/uploader.js\" type=\"text/javascript\"></script>";
			$lower_js .= "<script src=\"" . URL . "/js/jquery.ctrl.js\" type=\"text/javascript\"></script>";
			$lower_js .= "<script src=\"" . URL . "/js/suggest.js\" type=\"text/javascript\"></script>";
			$lower_js .= "<script src=\"" . URL . "/js/aie_editor_functions.js\" type=\"text/javascript\"></script>";
		}
		else {
			$lower_js = "";
			$headers = "";
	   		// ------------------------------
			//	Custom Replacements
			//if ($skip_widgets != '1') {
				//$data = $this->custom_replacements($data);
			//}
		}
		
		// Facebook connect
		$fb_app_id = $this->get_option('fb_app_id');
		if (! empty($fb_app_id) && empty($user)) {
			$app_secret = $this->get_option('fb_app_secret');
			$fb_cookie = get_facebook_cookie($fb_app_id, $app_secret);
	   		$fb_connect = "<div id=\"fb-root\"></div><script src=\"http://connect.facebook.net/en_US/all.js\" type=\"text/javascript\"></script>";
	   		$fb_connect .= "<script type=\"text/javascript\">";
	   		$fb_connect .= "	FB.init({";
	   		$fb_connect .= "		appId: \"" . $fb_app_id . "\",";
	   		$fb_connect .= "		cookie: true,";
	   		$fb_connect .= "		status: true,";
	   		$fb_connect .= "		xfbml: true";
	   		$fb_connect .= "	});";
			if (! empty($fb_cookie['access_token'])) {
	   			$fb_connect .= "	$(document).ready(function() {";
	   			$fb_connect .= '		$("#fb_loginbutton").click(function(e) { start_fb_session(); return false; });';
	   			$fb_connect .= "	});";
			} else {
	   			$fb_connect .= "	FB.getLoginStatus(function(response) {";
	   			$fb_connect .= "    	if (response.session) { } else {";
	   			$fb_connect .= "			FB.Event.subscribe(\"auth.login\", function(rep) {";
	   			$fb_connect .= "				start_fb_session();";
	   			$fb_connect .= "			});";
	   			$fb_connect .= "		}";
	   			$fb_connect .= "	});";
   			}
	   		$fb_connect .= "</script>";
	   		$fb_connect .= "<fb:login-button scope=\"email\" id=\"fb_loginbutton\">" . lg_fb_text . "</fb:login-button>";
		} else {
			$fb_connect = '';
		}
		
		// Logo
		$company_name = $this->get_option('company_name');
		$logo_option = $this->find_logo();
		if (! empty($logo_option)) {
			if (BD_MOBILE == 1) {
				$logo = "<div style=\"background:url('$logo_option') center center no-repeat;cursor:pointer;width:100%;height:100%;background-size: auto 100%;\" onclick=\"window.location='" . URL . "';\"></div>";
			} else {
				$browser = $this->determine_browser();
				if ($browser['type'] == 'ie') {
					$logo = "<a href=\"" . URL . "\"><img src=\"$logo_option\" border=\"0\" alt=\"$company_name\" title=\"$company_name\" style=\"max-height:100%;\" /></a>";
				} else {
					$logo = "<div style=\"background:url('$logo_option') center left no-repeat;cursor:pointer;width:100%;height:100%;background-size: auto 100%;\" onclick=\"window.location='" . URL . "';\"></div>";
				}
			}
		} else {
			$logo = "<a href=\"" . URL . "\">" . $this->get_option('site_name') . "</a>";
		}
		
		// Sharing code?
		$addthis = $this->get_option('addthis_profile_id');
		if (! empty($addthis)) {
			$sharing_code = "<!-- AddThis Button BEGIN -->\n";
			$sharing_code .= "<div class=\"addthis_toolbox addthis_default_style \">\n";
			$sharing_code .= "<a style=\"display:block;\" class=\"addthis_button_facebook_like\" fb:like:layout=\"button_count\"></a>\n";
			$sharing_code .= "<a style=\"display:block;\" class=\"addthis_button_tweet\"></a>\n";
			$sharing_code .= "<a style=\"display:block;\" class=\"addthis_button_google_plusone\" g:plusone:size=\"medium\"></a>\n";
			// $sharing_code .= "<a style=\"display:block;\" class=\"addthis_counter addthis_pill_style\"></a>\n";
			$sharing_code .= "</div>\n";
			$sharing_code .= '<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=' . $addthis . '"></script>';
			$sharing_code .= "<!-- AddThis Button END -->\n";
		}
		
		// Cache control
		// <meta http-equiv="cache-control" content="no-cache" />
		// <meta http-equiv="expires" content="Fri, 30 Dec 2011 12:00:00 GMT" />
		// <meta http-equiv="expires" content="Fri, 30 Dec 2011 12:00:00 GMT" />
		// <meta http-equiv="last-modified" content="Mon, 03 Jan 2011 17:45:57 GMT" />
		$final_cache_control = '';
		
		// User information
		// Replacements
		$replace = array(
			"%company%",
			"%company_url%",
			"%program_url%",
			"%site_name%",
			"%bd_charset%",
			"%bd_language%",
			"%url%",
			"%theme%",
			"%images%",
			"%date%",
			"%query%",
			"%meta_title%",
			"%meta_keywords%",
			"%meta_desc%",
			"%cache_control%",
			"%allow_registration%",
			"%article_category%",
			"%article_id%",
			"%cache_code%",
			"%sharing_code%",
			"%main_menu_style%",
			"%fb_connect%",
			"</body>",
			"</head>",
			"%logo%",
		);;
		$replace1 = array(
			$company_name,
			$this->get_option('company_url'),
			URL,
			$this->get_option('site_name'),
			lg_charset,
			lg_language,
			URL,
			THEME,
			THEME_IMAGES,
			$date,
			$query,
			$this->get_option('site_name'),
			str_replace($this->get_an_item_option('meta_keywords','d'),', ',','),
			$this->get_an_item_option('meta_desc','d'),
			$final_cache_control,
			$this->get_option('allow_registration'),
			'0',
			'0',
			$cache_code,
			$sharing_code,
			$this->get_option('main_menu_style'),
			$fb_connect,
			"$lower_js\n</body>",
			"$headers\n</head>",
			$logo,
		);
		// Used for non-article renderings
		if ($skip_user_bar != '1') {
			$manage_bar = $manual->article_sidebar('','1','');
			$replace[] = '%manage_bar%';
			$replace1[] = $manage_bar;
		}
		$data = str_replace($replace,$replace1,$data);
		
   		// ------------------------------
   		// 	Widgets
   		if ($skip_widgets != '1') {
   			//$template_lines = explode("\n",$data);
   			//foreach ($template_lines as 
   			$data = $manual->find_widgets($data);
   		/*
	   		preg_match_all('/\{\-(.*?)\-\}/', $data, $widgets);
	   		foreach ($widgets['0'] as $aWidget) {
	   			$no_paragraph = "1";
	   			$widgets_temp = str_replace('{-','',$aWidget);
	   			$widgets_temp = str_replace('-}','',$widgets_temp);
   				if (ctype_digit($widgets_temp) === true) {
   					$widget_data = $this->get_widget($widgets_temp);
   				} else {
   					$widget_data = $this->get_widget('0',$widgets_temp);
   				}
	   			// $widget_data = $manual->get_widget($widgets_temp);
		   		$data = str_replace($widgets['0'],$widget_data,$data);
	   		}
	   	*/
   		}
		return $data;
	}
	
	
	// --------------------------------------------------------------------
	// 	Odd little function this one...
	//	It will constantly check a string
	//	for a needle, and as long as it
	//	finds one it will replace that
	//	needle with a unique ID in the string.
	//	Used for custom replacements.
	
	function multi_str_replace($caller,$data) {
		while (strpos($data,$caller) !== false) {
			$this_id = "bd" . uniqid();
			$data = preg_replace('/' . $caller . '/', $this_id, $data, 4);
		}
		return $data;
	}
	
	
	// --------------------------------------------------------------------
	// 	Get a template information
	
	function get_template_info($type,$id,$content = '0',$select = '') {
		if ($type == 'html') {
			$table = TABLE_PREFIX . "templates_html";
		} else {
			$table = TABLE_PREFIX . "templates";
		}
		if (empty($select)) {
			$select = '*';
		}
		$q = "SELECT $select FROM `$table` WHERE `id`='" . $this->mysql_clean($id) . "' LIMIT 1";
		$info = $this->get_array($q);
		// Content
		if ($content == '1') {
			$theContent = $this->get_contents('html',$info['template'],$info['type'],$id,$info);
			$info['content'] = $theContent;
		}
		return $info;
	}
	
	
	// --------------------------------------------------------------------
	//   Get template data based on non-ID.
	
	function template_data($template_name = '',$template_id = '') {
		$q = "SELECT * FROM `" . TABLE_PREFIX . "templates_html`";
		if (! empty($template_id)) {
			$q .= " WHERE `id`='$template_id'";
		} else {
			$q .= " WHERE `template`='$template_name' AND `type`!='3'";
		}
		$template_info = $this->get_array($q);
		return $template_info;
	}
	
	
	// --------------------------------------------------------------------
	// 	Check if there is an active
	//	occurance of this template.
	function active_template($template) {
		$q = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "templates` WHERE `template`='$template' AND `status`='1'";
		$count = $this->get_array($q);
		return $count['0'];
	}
	
	
	// --------------------------------------------------------------------
	//	Process caller tags
	
	function caller_tags($input,$special_changes) {
   		foreach ($special_changes as $name => $value) {
   			$input = str_replace($name,$value,$input);
   		}
   		return $input;
	}
	
	
	// --------------------------------------------------------------------
	// 	Send a template-based e-mail
	//	E-mail templates will reference the "manual_templates" table.
	//	Multiple rows can exist for the same template.
	//	If "to" is set to "%user%", it will send it to the username provided.
	//	There are fixed "templates" which serve as "custom actions" or "triggers".
	//	Each has a basic file in the "templates" folder.
	//	That file can be overriden in the database using the "override_content" column.
	//	$force_to is used mainly for e-mailing articles to friends
	
	function send_template($username = "",$template,$force_to = "",$special_changes = "") {
		// global $session;
		global $user_data;
		global $user;
		// Is this template even active?
		$active = $this->active_template($template);
    		// Continue...
		if ($active > 0) {
			// Now loop through the occurances
			$q = "SELECT * FROM `" . TABLE_PREFIX . "templates` WHERE `template`='$template' AND `status`='1'";
			$instances = $this->run_query($q);
			while ($row = mysql_fetch_array($instances)) {
				// Override content?
				if (! empty($row['override_content'])) {
					$final_contents = $row['override_content'];
	   			} else {
			   		$final_contents = $this->get_contents('email',$template);
		   		}
		   		// User information provided?
		   		if (! empty($username)) {
		   			// Replace the information on the template
		   			// Needed if $username is different from
		   			// $user (logged in user).
   					if ($username != $user) {
   						$user_data = $this->get_user_data($username);
   					}
		   			$final_contents = $this->process_user($final_contents,$username,$user_data);
		   		}
		   		// Process standard callers
		   		$final_contents = $this->process_standard($final_contents,'1',$user_data);
		   		// Special caller tag changes?
		   		if (! empty($special_changes)) {
		   			$final_contents = $this->caller_tags($final_contents,$special_changes);
		   		}
				$headers = "";
				// ID for this email
				$id = uniqid();
				// Subject
				$subject = $row['subject'];
		   		if (! empty($special_changes)) {
	   				$subject = $this->caller_tags($subject,$special_changes);
		   		}
	   			$subject = $this->process_standard($subject,'1',$user_data);
				// E-Mail Format?
				if (! empty($user_data['option_email_format'])) {
					$final_format = $user_data['option_email_format'];
				} else {
					$final_format = $row['format'];
				}
				if ($final_format == "1") {
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
					// If sending in HTML, make sure there
					// actually is HTML in the email.
					if (strlen($final_contents) == strlen(strip_tags($final_contents))) {
						$final_contents = $this->make_url_clickable($final_contents);
						$final_contents = nl2br($final_contents);
					}
				} else {
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/plain; charset=utf-8' . "\r\n";
					// We need to preserve comment formatting,
					// so replace <br /> before running the
					// strip_tags command.
					$final_contents = str_replace('<br />',"\n",$final_contents);
					$final_contents = strip_tags($final_contents);
				}
				// From
	   			$pos = strpos($row['from'],'%');
				if ($row['from'] == "%user%") {
		   			if (empty($user_data['email'])) {
		   				continue;
		   			} else {
	   					$from = $user_data['email'];
	   				}
	   			}
	   			else if ($pos !== false) {
	   				$from = $this->caller_tags($row['from'],$special_changes);
	   			}
				else if (empty($row['from'])) {
					$from = $this->get_option('default_email');
					if (empty($from)) {
						$domain = $this->get_domain();
						$from_domain = "noreply@" . $domain;
						$from = $this->get_option('site_name') . " <" . $from_domain . ">";
					}
				}
				else {
					$from = $row['from'];
				}
				$headers .= "From: " . $from . "\r\n";
				$headers .= "Reply-To: " . $from . "\r\n";
    				$headers .= "Organization: " . $this->get_option('company_name');
	   			// To
	   			$pos1 = strpos($row['to'],'%');
				if ($row['to'] == "%user%") {
		   			if (empty($user_data['email'])) {
		   				continue;
		   			} else {
	   					$to = $user_data['email'];
	   				}
	   			}
	   			else if ($pos1 !== false) {
	   				$to = $this->caller_tags($row['to'],$special_changes);
	   			}
	   			else {
	   				$to = $row['to'];
	   			}
	   			// CC
	   			if (! empty($row['cc'])) {
	   				$headers .= "\r\n";
					$headers .= "Cc: " . $row['cc'];
				}
	   			// BCC
	   			if (! empty($row['bcc'])) {
	   				$headers .= "\r\n";
					$headers .= "Bcc: " . $row['bcc'];
				}
				$send_content = $final_contents;
				// Now send it!
				mail($to, $subject, $send_content, $headers);
				// Save a log?
				if ($row['save'] == "1") {
					$q = "INSERT INTO `" . TABLE_PREFIX . "sent_emails` (`id`,`template`,`headers`,`content`,`format`,`username`) VALUES ('$id','$template','" . $this->mysql_clean($headers) . "','" . $this->mysql_clean($final_contents) . "','" . $row['format'] . "','$username')";
					$save = $this->insert($q);
				}
			} // while loop!
			return "1";
		} else {
			return "0";
		}
	}
	
	
	// -----------------------------------------------------------------------------
	// 	Detect if a user is on a mobile phone.
	
	function detect_mobile() {
		$useragent = $_SERVER['HTTP_USER_AGENT'];
		if (preg_match('/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i', substr($useragent,0,4))) {
			return '1';
		} else {
			return '0';
		}
	}
	
}

?>