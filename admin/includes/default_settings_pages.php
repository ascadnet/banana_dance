<?php

// Generate an options menu
$option_names = array(
	'category_default' => lg_article_option_catehome,
	'show_stats' => lg_article_option_stats,
	'login_to_view' => lg_article_option_login_req,
	'display_on_sidebar' => lg_article_option_primary_nav,
	'sharing_options' => lg_article_option_sharing,
   	'in_widgets' => lg_article_option_inwidgets,
);

$option_categories = array(
	'allow_comments' => lg_article_option_ac,
	'email_comment_posted' => lg_article_option_email_comment,
	'login_to_comment' => lg_article_option_login_comment,
	'allow_comment_edits' => lg_article_option_comment_editing,
);

$current = 1;

$defaults = $db->get_item_options($act_id,$type);

?>

	<input type="hidden" name="type" value="<?php echo $type; ?>" />
	<input type="hidden" name="act_id" value="<?php echo $act_id; ?>" />
		
		<?php
		
			$options .= "
				<!-- Start tab -->
				<div id=\"tabmeta\">
			";
		
			$options .= "<h2>Meta Tags</h2>";
		
			$options .= "<div class=\"col50\">";
   			$options .= "<label>Meta Title</label>";
   			$options .= "<input type=\"text\" name=\"meta_title\" style=\"width:97%;\" value=\"" . $defaults['meta_title'] . "\" /><p class=\"field_desc\">It is recommended that you leave this blank, thereby allow the program to generate a title based on the page's breadcrumb trail.</p>";
   			
			$options .= "</div><div class=\"col50\">";
			
   			$options .= "<label>Meta Keywords</label>";
   			$options .= "<input type=\"text\" name=\"meta_keywords\" style=\"width:97%;\" value=\"" . $defaults['meta_keywords'] . "\" /><p class=\"field_desc\">Enter as a comma-separated list.</p>";
   			
			$options .= "</div><div class=\"clear\"></div>";
			
   			$options .= "<label>Meta Description</label>";
   			$options .= "<input type=\"text\" name=\"meta_desc\" style=\"width:97%;\" value=\"" . $defaults['meta_desc'] . "\" />";
   			
   			
   			
			$options .= "
				</div>
				<!-- End Tag -->
				<!-- Start tab -->
				<div id=\"tablook\" style=\"display:none;\">
			";
   			
   			
			$options .= "<h2>Look and Feel</h2>";
		
   			$templates = $template->list_templates('article','3',$defaults['template']);
   			$options .= "<label>" . lg_template . "<span class=\"help\" id=\"h-20\">(?)</span><div class=\"help_bubble\" id=\"h-20b\"><div class=\"hbpad\">Controls which template will be used to render the page.</div></div></label>";
   			$options .= "<select name=\"template\" id=\"template\" style=\"width:97%;\">" . $templates . "</select>";
   			
   			$options .= "
   				<label>Composition Style<span class=\"help\" id=\"h-21\">(?)</span><div class=\"help_bubble\" id=\"h-21b\"><div class=\"hbpad\">\"Wiki-syntax\" allows for simplified formatting, while \"Full HTML\" requires knowledge of HTML code to create the page.</div></div></label>
				<ul class=\"option_list\" id=\"A\">
					<li";
   			if ($defaults['format_type'] == '1') {
				$options .= " class=\"checked\"";
			}
			$options .= ">
						<input type=\"radio\" name=\"format_type\" value=\"1\"";
   			if ($defaults['format_type'] == '1') {
				$options .= " checked=\"checked\"";
			}
			$options .= " /> Wiki-syntax
					</li>
					<li";
   			if ($defaults['format_type'] != '1') {
				$options .= " class=\"checked\"";
			}
			$options .= ">
						<input type=\"radio\" name=\"format_type\" value=\"2\"";
   			if ($defaults['format_type'] != '1') {
				$options .= " checked=\"checked\"";
			}
			$options .= " /> Full HTML
					</li>
				</ul>
			";
 			
   			
   			
			$options .= "
				</div>
				<!-- End Tag -->
				<!-- Start tab -->
				<div id=\"tabaccess\" style=\"display:none;\">
			";
			
 			
			$options .= "<h2>Page Access Controls</h2>";
			
   			$options .= "
   				<label>" . lg_article_option_public . "</label>
				<ul class=\"option_list\" id=\"C\">
					<li";
   			if ($defaults['public'] == '1') {
				$options .= " class=\"checked\"";
			}
			$options .= ">
						<input type=\"radio\" name=\"public\" value=\"1\"";
   			if ($defaults['public'] == '1') {
				$options .= " checked=\"checked\"";
			}
			$options .= " /> Public<span class=\"help\" id=\"h-2\">(?)</span><div class=\"help_bubble\" id=\"h-2b\"><div class=\"hbpad\">Makes the page open to all users and visitors. <b>Note:</b> If you set \"Login Requirement\" to \"Yes\" above, even if a page is public, users will still need to log in to view the page.</div></div>
					</li>
					<li";
   			if ($defaults['public'] == '2') {
				$options .= " class=\"checked\"";
			}
			$options .= ">
						<input type=\"radio\" name=\"public\" value=\"2\"";
   			if ($defaults['public'] == '2') {
				$options .= " checked=\"checked\"";
			}
			$options .= " /> Restricted Access<span class=\"help\" id=\"h-3\">(?)</span><div class=\"help_bubble\" id=\"h-3b\"><div class=\"hbpad\">Limits access to specific users and user types. You can also set user access controls for individual pages by editing the page settings.</div></div>
					</li>
					<li";
   			if ($defaults['public'] == '0') {
				$options .= " class=\"checked\"";
			}
			$options .= ">
						<input type=\"radio\" name=\"public\" value=\"0\"";
   			if ($defaults['public'] == '0') {
				$options .= " checked=\"checked\"";
			}
			$options .= " /> Private<span class=\"help\" id=\"h-4\">(?)</span><div class=\"help_bubble\" id=\"h-4b\"><div class=\"hbpad\">Limits visibility to administrators, the page's creator, and user types with access to private pages only.</div></div>
					</li>
				</ul>
			";
   		
   		
   			
			$options .= "
				</div>
				<!-- End Tag -->
				<!-- Start tab -->
				<div id=\"tabcomments\" style=\"display:none;\">
			";
			
			$options .= "<h2>Comments</h2>";

			$status = $admin->get_comment_statuses($defaults['default_comment_type_show'],'select');
   			$options .= "<label>Default comment type to display<span class=\"help\" id=\"h-401\">(?)</span><div class=\"help_bubble\" id=\"h-401b\"><div class=\"hbpad\">Controls which set of comments is displayed when the page is initially loaded.</div></div></label>";
   			$options .= "<select name=\"default_comment_type_show\" id=\"default_comment_type_show\" style=\"width:97%;\">" . $status . "</select>
			<div class=\"clear\"></div>";
			
   			$options .= "
   				<label>" . lg_article_option_thread_style . "</label>
				<ul class=\"option_list\" id=\"E\">
					<li";
   			if ($defaults['comment_thread_style'] == 'Tree') {
				$options .= " class=\"checked\"";
			}
			$options .= ">
						<input type=\"radio\" name=\"comment_thread_style\" value=\"Tree\"";
   			if ($defaults['comment_thread_style'] == 'Tree') {
				$options .= " checked=\"checked\"";
			}
			$options .= " /> " . lg_comment_style_tree . "
					</li>
					<li";
   			if ($defaults['comment_thread_style'] != 'Tree') {
				$options .= " class=\"checked\"";
			}
			$options .= ">
						<input type=\"radio\" name=\"comment_thread_style\" value=\"Forum\"";
   			if ($defaults['comment_thread_style'] != 'Tree') {
				$options .= " checked=\"checked\"";
			}
			$options .= " /> " . lg_comment_style_forum . "
					</li>
				</ul>
			";
			
			$options .= "<div class=\"col50\">
				<label>" . lg_article_options_max_thread . " (Tree-style comments only)</label> <input type=\"text\" name=\"max_threading\" maxlength=\"2\" value=\"" . $defaults['max_threading'] . "\" style=\"width:50px;margin-bottom:0px;\" />
			</div>
			<div class=\"col50\">
				<label>" . lg_article_options_hide_commments . "</label> <input type=\"text\" name=\"comment_hide_threshold\" maxlength=\"4\" value=\"" . $defaults['comment_hide_threshold'] . "\" style=\"width:50px;margin-bottom:0px;\" />
			</div>
			<div class=\"clear\"></div>";
			
			
   			foreach ($option_categories as $anOption => $showName) {
   				$current++;
	   			$options .= "
	   				<label>$showName</label>
					<ul class=\"option_list\" id=\"A$current\">
						<li";
   			if ($defaults[$anOption] == '1') {
				$options .= " class=\"checked\"";
			}
			$options .= ">
							<input type=\"radio\" name=\"$anOption\" value=\"1\"";
	   			if ($defaults[$anOption] == '1') {
					$options .= " checked=\"checked\"";
				}
				$options .= " /> " . lg_yes . "
						</li>
						<li";
   			if ($defaults[$anOption] != '1') {
				$options .= " class=\"checked\"";
			}
			$options .= ">
							<input type=\"radio\" name=\"$anOption\" value=\"0\"";
	   			if ($defaults[$anOption] != '1') {
					$options .= " checked=\"checked\"";
				}
				$options .= " /> " . lg_no . "
						</li>
					</ul>
				";
   			}
   			
   			
   			
			$options .= "
				</div>
				<!-- End Tag -->
				<!-- Start tab -->
				<div id=\"tabpreformat\" style=\"display:none;\">
			";
			$options .= "<h2>Pre-formatting</h2>";
   			$options .= "<label>Pre-Populate New Articles</label>";
   			$options .= "<textarea name=\"pre_populate\" style=\"width:95%;height:300px;\">" . $defaults['pre_populate'] . "</textarea>";
			$options .= "
				</div>
				<!-- End Tag -->
			";

			$options .= "
				<!-- Start tab -->
				<div id=\"tabother\" style=\"display:none;\">
			";
			$options .= "<h2>Other</h2>";
   			
   			foreach ($option_names as $anOption => $showName) {
   				$current++;
	   			$options .= "
	   				<label>$showName</label>
					<ul class=\"option_list\" id=\"V$current\">
						<li";
   			if ($defaults[$anOption] == '1') {
				$options .= " class=\"checked\"";
			}
			$options .= ">
							<input type=\"radio\" name=\"$anOption\" value=\"1\"";
	   			if ($defaults[$anOption] == '1') {
					$options .= " checked=\"checked\"";
				}
				$options .= " /> " . lg_yes . "
						</li>
						<li";
   			if ($defaults[$anOption] != '1') {
				$options .= " class=\"checked\"";
			}
			$options .= ">
							<input type=\"radio\" name=\"$anOption\" value=\"0\"";
	   			if ($defaults[$anOption] != '1') {
					$options .= " checked=\"checked\"";
				}
				$options .= " /> " . lg_no . "
						</li>
					</ul>
				";
   			}
   			
			$options .= "
				</div>
				<!-- End Tag -->
			";
   			
   			echo $options;
		
		?>
	