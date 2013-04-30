<?php
require PATH . "/includes/stat.functions.php";
$stats = new stats;

$home_stats = $stats->compile_home_stats();

$get_days = "7";
$comments_seven_days = $stats->stats_daily($get_days,TABLE_PREFIX . 'comments','date');
$users_seven_days = $stats->stats_daily($get_days,TABLE_PREFIX . 'users','joined');
$articles_seven_days = $stats->stats_daily($get_days,TABLE_PREFIX . 'articles','created');
?>
		
<script type="text/javascript" src="../js/jquery.charts.js"></script>
<script type="text/javascript">
	// http://code.google.com/apis/chart/interactive/docs/gallery/columnchart.html#Example
	google.load("visualization", "1", {packages:["corechart"]});
	google.setOnLoadCallback(drawChart);
	function drawChart() {
		// Get the proper width
		var show_width = $('#get_width').width();
		
		// Graph 1
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Date');
		data.addColumn('number', 'Comments');
		data.addRows(<?php echo $get_days; ?>);
		<?php
		$graph1 = $stats->bar_graph_js($comments_seven_days,'data');
		echo $graph1;
		?>
		var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
		chart.draw(data, {
			width: show_width, height: 200, colors:['#F7DA41'], fontName:'tahoma', legend:'none'
		});
		
		// Graph 2
		var dataA = new google.visualization.DataTable();
		dataA.addColumn('string', 'Date');
		dataA.addColumn('number', 'Users');
		dataA.addRows(<?php echo $get_days; ?>);
		<?php
		$graph2 = $stats->bar_graph_js($users_seven_days,'dataA');
		echo $graph2;
		?>
		var chartA = new google.visualization.ColumnChart(document.getElementById('chart_divA'));
		chartA.draw(dataA, {
			width: show_width, height: 200, colors:['#F7DA41'], fontName:'tahoma', legend:'none'
		});
		
		// Graph 3
		var dataB = new google.visualization.DataTable();
		dataB.addColumn('string', 'Date');
		dataB.addColumn('number', 'Pages');
		dataB.addRows(<?php echo $get_days; ?>);
		<?php
		$graph3 = $stats->bar_graph_js($articles_seven_days,'dataB');
		echo $graph3;
		?>
		var chartB = new google.visualization.ColumnChart(document.getElementById('chart_divB'));
		chartB.draw(dataB, {
			width: show_width, height: 200, colors:['#F7DA41'], fontName:'tahoma', legend:'none'
		});
	}
</script>



   	<div class="submit">
   		<a href="http://www.doyoubananadance.com/Product-Manual/" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
   	</div>

	<div id="actions_right">
		<ul>
			<li><a href="<?php echo URL; ?>">Website Home</a></li>
			<li><a href="<?php echo ADMIN_URL; ?>/index.php?l=themes">Theme</a></li>
		</ul>
	</div>
	<h1>Let's dance...</h1>
		
	<ul id="inner_page_tabs_top">
		<li id="litab1" class="on"><a href="#" onClick="swapTab('1');return false;">Overview</a></li>
		<li id="litab2"><a href="#" onClick="swapTab('2');return false;">Comments</a></li>
		<li id="litab3"><a href="#" onClick="swapTab('3');return false;">Pages</a></li>
		<li id="litab4"><a href="#" onClick="swapTab('4');return false;">Users</a></li>
	</ul>
	
	
		<div id="tab1">
		
			<div class="home_box"><div class="pad12" id="get_width">
		
				<div class="col50">
						<h3 class="shadow less_margin">Comments</h3>
						<p class="home_stat">
							<span class="home_count"><?php echo $home_stats['comments']; ?></span>
							<span class="home_name">Total</span>
						</p>
						<p class="home_stat">
							<span class="home_count"><?php echo $home_stats['comments_live']; ?></span>
							<span class="home_name">Live</span>
						</p>
						<p class="home_stat">
							<span class="home_count"><?php echo $home_stats['comments_pending']; ?></span>
							<span class="home_name">Pending</span>
						</p>
						<p class="home_stat">
							<span class="home_count"><?php echo $home_stats['comment_votes']; ?></span>
							<span class="home_name">Votes</span>
						</p>
						<p class="home_stat">
							<span class="home_count"><?php echo $home_stats['users']; ?></span>
							<span class="home_name">Users</span>
						</p>
				</div>
				<div class="col50">
						<h3 class="shadow less_margin">Pages</h3>
						<p class="home_stat">
							<span class="home_count"><?php echo $home_stats['articles']; ?></span>
							<span class="home_name">Total</span>
						</p>
						<p class="home_stat">
							<span class="home_count"><?php echo $home_stats['articles_public']; ?></span>
							<span class="home_name">Public</span>
						</p>
						<p class="home_stat">
							<span class="home_count"><?php echo $home_stats['articles_private']; ?></span>
							<span class="home_name">Private</span>
						</p>
						<p class="home_stat">
							<span class="home_count"><?php echo $home_stats['views']; ?></span>
							<span class="home_name">Views</span>
						</p>
						<p class="home_stat">
							<span class="home_count"><?php echo $home_stats['categories']; ?></span>
							<span class="home_name">Categories</span>
						</p>
				</div>
				<div class="clear"></div>
			</div></div>
			
			<h3>News From Banana Dance</h3>
			
			<div id="newsbox">
			<?php
				$last_checked = $db->get_option('last_news_update');
				if (! empty($last_checked)) {
					echo "<p class=\"center small details\">Feed last updated on " . $db->format_date($db->get_option('last_news_update')) . "</p>";
				} else {
					echo "<p class=\"small\">Feed has not been updated.</p>";
				}
				$feed = $admin->get_news_feed();
				echo $feed;
			?>
			</div>
	
		</div>
		

		
		<div id="tab2" style="display:none;">
			<div class="home_box"><div class="pad12">
				<div id="chart_div"></div>
			</div></div>
			
			<h3>Recent Comments</h3>
			
			<?php
			$q = "SELECT * FROM `" . TABLE_PREFIX . "comments` ORDER BY `date` DESC LIMIT 15";
			$results = $db->run_query($q);
	    		while ($row = mysql_fetch_array($results)) {
				// Status
				if ($this_comment['pending'] == "1") {
					$show_status = "<li>Pending</li>";
				}
				else {
					$show_status = "";
				}
				// Score
				$score = $row['up'] - $row['down'];
				
		 		$home_article = $manual->get_article($row['article'],'1','id,name,category');
		 		$link = $manual->prepare_link($home_article['id'],$home_article['category'],$home_article['name']);
		 		$show_article = "<a href=\"$link\">" . $home_article['name'] . "</a>";
	    			// Show it...
	    			echo "<div id=\"" . $row['id'] . "\" class=\"home_comment\">\n";
				echo "<ul class=\"bubble_options\">";
				echo "<li>$score</li>";
				echo $show_status;
				echo "<li><a href=\"index.php?l=comment_edit&id=" . $row['id'] . "\">Edit</a></li>";
				echo "<li><a href=\"#\" onClick=\"deleteID('" . TABLE_PREFIX . "comments','" . $row['id'] . "');return false;\">Delete</a></li>";
				echo "</ul>";
	    			echo "<p class=\"home_comment_title\">By <a href=\"index.php?l=users_edit&id=" . $row['user'] . "\">" . $row['user'] . "</a> to $show_article</p><p class=\"home_comment\">" . $manual->format_comment($row['comment']) . "</p>";
	    			echo "</div>\n";
	    		}
			?>
			
			<p class="submit small"><a href="index.php?l=comments">View All</a><!--<br /><a href="#" onClick="showCustomize('home_comments');return false;">Customize--></p>
			<div class="col_pad" id="cusomtize_home_comments" style="display:none;">
				<label>Show Comments</label>
				<input type="text" name="home_comments" value="10" />
			</div>
		</div>
		
		
		
		<div id="tab3" style="display:none;">
		
			<div class="home_box"><div class="pad12">
				<div id="chart_divB"></div>
			</div></div>
			
		
			<h1 class="shadow less_margin">Recent Pages</h1>
			<?php
			$q = "SELECT `id`,`created`,`owner`,`name`,`category` FROM `" . TABLE_PREFIX . "articles` ORDER BY `created` DESC LIMIT 10";
			$results = $db->run_query($q);
	    		while ($row = mysql_fetch_array($results)) {
		 		$link = $manual->prepare_link($row['id'],$row['category'],$row['name']);
	    			$found = '1';
	    			$user_id = $session->get_user_id($row['owner']);
	    			echo "<div class=\"home_article\">\n";
	    			echo "<p class=\"home_comment_title\"><a href=\"$link\" target=\"_blank\">" . $row['name'] . "</a></p>\n";
	    			echo "<p class=\"home_comment\">On " . $db->format_date($row['created']) . " by <a href=\"index.php?l=users_edit&id=" . $user_id . "\">" . $row['owner'] . "</a></p>\n";
	    			echo "</div>\n";
	    		}
	    		if ($found != '1') {
	    			echo "<i>None found.</i>";
	    		}
			?>
			<p class="submit small"><a href="index.php?l=articles&filter=1&public=2">View All</a><!--<br /><a href="#" onClick="showCustomize('home_articles');return false;">Customize--></p>

		</div>
		
		
		
		<div id="tab4" style="display:none;">
		
			<div class="home_box"><div class="pad12">
			<div id="chart_divA"></div>
			</div></div>
			
			<h1 class="shadow less_margin">Recent Users</h1>
			<?php
			$q = "SELECT `id`,`username`,`joined` FROM `" . TABLE_PREFIX . "users` ORDER BY `joined` DESC LIMIT 10";
			$results = $db->run_query($q);
	    		while ($row = mysql_fetch_array($results)) {
	    			$found = '1';
	    			echo "<div class=\"home_article\">\n";
	    			echo "<p class=\"home_comment_title\"><a href=\"index.php?l=users_edit&id=" . $row['id'] . "\" target=\"_blank\">" . $row['username'] . "</a> on " . $db->format_date($row['joined']) . "</p>\n";
	    			echo "</div>\n";
	    		}
	    		if ($found != '1') {
	    			echo "<i>None found.</i>";
	    		}
			?>
			<p class="submit small"><a href="index.php?l=articles&filter=1&public=2">View All</a><!--<br /><a href="#" onClick="showCustomize('home_articles');return false;">Customize--></p>

			
		</div>
		