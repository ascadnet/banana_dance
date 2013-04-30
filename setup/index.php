<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	http://www.ascadnetworks.com/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Program setup.
	
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

// Load the setup functions
require "functions.php";

$_GET['admin'] = '';

// Get the paths
$setup_folder = dirname(__FILE__);
$main_folder = str_replace('/setup','',$setup_folder);
$base_folder = getcwd();
$base_folder = str_replace("/setup","",$base_folder);
$folder_name = str_replace($_SERVER['DOCUMENT_ROOT'] . '/','',$base_folder);
$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/";

// URL
$url = (!empty($_SERVER['HTTPS'])) ? "https://" . $_SERVER['SERVER_NAME'] : "http://" . $_SERVER['SERVER_NAME'];
$second_part = str_replace('/setup/index.php','',$_SERVER['PHP_SELF']);
$main_url = $url . "/" . ltrim($second_part,'/');

// User's browser
$browser = $db->determine_browser();

setup_header();
?>

<script>

// ----------------------------------------------------------------
//   Check password stength

function checkPassword(pwd) { 
	var strength = document.getElementById('password_check');
	var strongRegex = new RegExp("^(?=.{8,})(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*\\W).*$", "g");
	var mediumRegex = new RegExp("^(?=.{7,})(((?=.*[A-Z])(?=.*[a-z]))|((?=.*[A-Z])(?=.*[0-9]))|((?=.*[a-z])(?=.*[0-9]))).*$", "g");
	var enoughRegex = new RegExp("(?=.{6,}).*", "g");
	if (pwd.length==0) {
		strength.innerHTML = 'Input a password';
		document.getElementById('password_check').className = "error";
	} else if (false == enoughRegex.test(pwd)) {
		strength.innerHTML = 'Recommendation: Add more characters';
		document.getElementById('password_check').className = "error";
	} else if (strongRegex.test(pwd)) {
		strength.innerHTML = '';
	} else if (mediumRegex.test(pwd)) {
		strength.innerHTML = '';
	} else { 
		strength.innerHTML = 'Weak Password (Tip: Add symbols, numbers, lower and upper case letters.)';
		document.getElementById('password_check').className = "error";
	}
}


// ----------------------------------------------------------------
//	Check password match

function checkPasswordMatch() {
	pass = document.getElementById('password').value;
	pass1 = document.getElementById('repeat_password').value;
	if (pass != pass1) {
		document.getElementById('repeat_pwd_check').innerHTML = 'Passwords do not match.';
		document.getElementById('repeat_pwd_check').className = "reg_page_notice_error";
	} else {
		document.getElementById('repeat_pwd_check').innerHTML = 'Passwords match.';
		document.getElementById('repeat_pwd_check').className = "reg_page_notice";
	}
}
</script>

				<p style="text-align:center;">Before we start dancing, please fill out the following information!</p>
				
				<fieldset>
					<legend>System Checks</legend>
					<p>Make sure everything in this section is green before proceeding.</p>

					<label>Server Software</label>
					<?php
					$lower_soft = strtolower($_SERVER['SERVER_SOFTWARE']);
					if (stristr($lower_soft,"unix") || stristr($lower_soft,"linux") || stristr($lower_soft,"apache")) {
						echo "<span class=\"good\">" . $_SERVER['SERVER_SOFTWARE'] . "</span>";
					} else {
						echo "<span class=\"bad\">" . $_SERVER['SERVER_SOFTWARE'] . "</span>";
					}
					?>
					<div class="clear"></div>
					
					<label>Your Browser</label>
					<?php
					if ($browser['status'] == '1') {
						echo "<span class=\"good\">" . $browser['name'] . "</span>";
					} else {
						echo "<span class=\"bad\">" . $browser['name'] . "...time to upgrade pal! Try FireFox or Chrome.</span>";
					}
					?>
					<div class="clear"></div>
					
					<label>"generated" folder</label>
					<?php
					if (is_writable($base_folder . '/generated')) {
						echo "<span class=\"good\">Writable</span>";
					} else {
						echo "<span class=\"bad\">Not writable: set permissions to 777</span>";
					}
					?>
					<div class="clear"></div>

				</fieldset>

				<fieldset>
					<legend>MySQL Database</legend>
					<p>Tell us all about your database. <b>Note:</b> You must pre-create this database from your website's control panel. Contact your web hosting provider if you are unsure how to do this.</p>

					<label>Table Prefix</label>
					<input type="text" name="db_prefix" style="width:300px;" value="bd_" />
					<div class="clear"></div>

					<label>Server Host</label>
					<input type="text" name="db_host" style="width:300px;" value="localhost" />
					<div class="clear"></div>

					<label>Name</label>
					<input type="text" name="db_name" style="width:300px;" />
					<p class="field_desc"><span class="bad"><b>Important:</b></span> You have to create the database from your website control panel before running the setup!</p>
					<div class="clear"></div>

					<label>Username</label>
					<input type="text" name="db_user" style="width:300px;" />
					<div class="clear"></div>

					<label>Password</label>
					<input type="password" name="db_pass" style="width:300px;" />
					<div class="clear"></div>
				</fieldset>

				<fieldset>
					<legend>Paths &amp; URLs</legend>
					<p>Tell us more about the paths on your server and the program's URL. Generally these are correctly determined by the program, but on the off-chance that they were not, please correct them below.</p>
					
					<label>Base Path</label>
					<input type="text" name="path" style="width:300px;" value="<?php echo $main_folder; ?>" />
					<p class="field_desc">Input the absolute path to the base directory into which Banana Dance is installed.<br />This should not be the "setup" directory!</p>
					<div class="clear"></div>
					
					<label>Base URL</label>
					<input type="text" name="url" style="width:300px;" value="<?php echo $main_url; ?>" />
					<p class="field_desc">Input the URL to the base directory into which Banana Dance is installed.</p>
					<div class="clear"></div>

					<label>Admin Directory</label>
					<input type="text" name="admin_url" style="width:300px;" value="admin" />
					<p class="field_desc">What is the name of the admin directory?</p>
					<div class="clear"></div>
				</fieldset>

				<fieldset>
					<legend>Basic Information</legend>
					<p>This controls general information about your website and program.</p>

					<label>Make website public or private?</label>
					<select name="public">
					<option value="1" selected="selected">Public: anyone can access the site.</option>
					<option value="0">Private: users must be logged in to view the site.</option>
					</select>
					<p class="field_desc">This controls whether the entire site is private or public. Even if you set the site to public, you can still set sub-pages and sub-categories to private if you wish. This setting can be changed from the admin control panel by editing the home category of your website.</p>
					<div class="clear"></div>
					
					<label>How are you using Banana Dance?</label>
					<select name="purpose">
					<option value="wiki">Full Wiki: Full array of wiki-features.</option>
					<option value="starter">Combo Website/Wiki: Wiki-features but looks like a website.</option>
					<option value="website">Full Website (CMS-style): Full website, no wiki-features.</option>
					</select>
					<p class="field_desc">How do you plan on using Banana Dance? This will help the setup select an appropriate theme, plugins, and default options for you.</p>
					<div class="clear"></div>
					
					<label>Character Encoding</label>
					<select name="language">
						<optgroup label="Common Character Sets">
							<option value="english" selected="selected">English</option>
							<option value="various">Other latin-based language</option>
						</optgroup>
						<optgroup label="Specialized Character Sets">
							<option value="czech">Czech</option>
							<option value="danish">Danish</option>
							<option value="esperanto">Esperanto</option>
							<option value="estonian">Estonian</option>
							<option value="hungarian">Hungarian</option>
							<option value="icelandic">Icelandic</option>
							<option value="latvian">Latvian</option>
							<option value="lithuanian">Lithuanian</option>
							<option value="persian">Persian</option>
							<option value="polish">Polish</option>
							<option value="romanian">Romanian</option>
							<option value="slovak">Slovak</option>
							<option value="slovenian">Slovenian</option>
							<option value="spanish">Spanish</option>
							<option value="swedish">Swedish</option>
							<option value="turkish">Turkish</option>
						</optgroup>
					</select>
					<p class="field_desc">What language are you running your site in?</p>
					<div class="clear"></div>
					
					<label>Time (hour) Offset?</label>
					<select name="offset_time_2">
					<option>-11</option>
					<option>-10</option>
					<option>-9</option>
					<option>-8</option>
					<option>-7</option>
					<option>-6</option>
					<option>-5</option>
					<option>-4</option>
					<option>-3</option>
					<option>-2</option>
					<option>-1</option>
					<option selected="selected">0</option>
					<option>+1</option>
					<option>+2</option>
					<option>+3</option>
					<option>+4</option>
					<option>+5</option>
					<option>+6</option>
					<option>+7</option>
					<option>+8</option>
					<option>+9</option>
					<option>+10</option>
					<option>+11</option>
					</select>
					<p class="field_desc">The program current thinks it is <?php echo date('Y-m-d H:i:s'); ?>. Please adjust the time accordingly.</p>
					<div class="clear"></div>
					
					<label>Site Name</label>
					<input type="text" name="site_name" style="width:300px;" value="" />
					<p class="field_desc">The name of your website. Used in webpage titles for SEO-friendliness. It should be short (3-6 words) and identify what your website is.</p>
					<div class="clear"></div>

					<label>Company Name</label>
					<input type="text" name="company_name" style="width:300px;" />
					<p class="field_desc">What is the name of your company? This on outgoing emails on some other spots throughout the program.</p>
					<div class="clear"></div>
					
					<label>Company URL</label>
					<input type="text" name="company_url" style="width:300px;" />
					<p class="field_desc">What is the company URL?</p>
					<div class="clear"></div>
				</fieldset>


				<fieldset>
					<legend>Website Meta Information</legend>
					<p>Please input your desired "default" meta information for the website. This is important for SEO and general usability. These settings can be updated at any time in the future, and you can even select custom meta information for specific pages and categories as your create them. Defaults just help ensure that all pages have something in the meta tags.</p>

					<label>Meta Title</label>
					<input type="text" name="meta_title" style="width:300px;" value="Auto-generated by the program." disabled="disabled" />
					<div class="clear"></div>

					<label>Meta Description</label>
					<input type="text" name="meta_desc" style="width:300px;" />
					<div class="clear"></div>
					
					<label>Meta Keywords</label>
					<input type="text" name="meta_keywords" style="width:300px;" />
					<div class="clear"></div>
					
				</fieldset>
				
				
				<fieldset<?php if ($_GET['admin'] == "1") { echo " class=\"highlight\""; } ?>>
					<legend>Master Administrator</legend>
					<p>Select the credentials for your master administrator below.</p>

					<label>Username</label>
					<input type="text" name="username" value="admin" style="width:300px;" />
					<div class="clear"></div>

					<label>Password</label>
					<input type="password" name="password" id="password" onkeyup="checkPassword(this.value);" value="" style="width:300px;" /> <span id="password_check"></span>
					<p class="field_desc">A strong password should be 8-12 characters and have letters (upper/lower case), numbers, and symbols.</p>
					<div class="clear"></div>

					<label>Repeat Password</label>
					<input type="password" name="repeat_password" id="repeat_password" value="" onkeyup="checkPasswordMatch();" style="width:300px;" /> <span id="repeat_pwd_check"></span>
					<div class="clear"></div>
					
					<label>Email</label>
					<input type="text" name="email" value="" style="width:300px;" />
					<p class="field_desc">This is used for comment and article notifications.</p>
					<div class="clear"></div>
				</fieldset>

				<fieldset>
					<legend>Finalize</legend>
					<p style="text-align:center;">Re-check your work above and complete the process when you're ready!</p>
					<center><input type="submit" value="Complete Setup... Let's Dance!" /></center>
				</fieldset>

<?php
setup_footer();
?>