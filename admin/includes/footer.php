			</div>
			<div class="clear"></div>
		
			</div></div>
		</div>
		<div class="clear"></div>
	</div>
</div>

<div id="footer"><div id="footer_pad">
	<div class="foot_col">
		<h2>Learn the Basics</h2>
		<ul>
		<li><a href="">Getting Started</a></li>
		<li><a href="http://www.bananadance.org/Product-Manual/Intro-to-Banana-Dance">Intro to Banana Dance</a></li>
		<li><a href="">Video Guides</a></li>
		</ul>
	</div>
	<div class="foot_col">
		<h2>Get Help</h2>
		<ul>
		<li><a href="http://www.bananadance.org/Product-Manual/Product-Manual" target="_blank">Product Manual</a></li>
		<li><a href="http://www.bananadance.org/Community/General-Discussion" target="_blank">Discussion</a></li>
		<li><a href="http://www.bananadance.org/Support/Support-Options" target="_blank">Online Support</a></li>
		</ul>

	</div>
	<div class="foot_col">
		<h2>Extend</h2>
		<ul>
		<li><a href="http://www.bananadance.org/Download/Theme-Library" target="_blank">Themes</a></li>
		<li><a href="http://www.bananadance.org/Download/Mobile-Theme-Library" target="_blank">Mobile Themes</a></li>
		<li><a href="http://www.bananadance.org/Download/Plugin-Library" target="_blank">Plugins</a></li>
		</ul>

	</div>
	<div class="foot_col_last">&copy; <?php echo date('Y'); ?><br /><br />Distributed under <a href="http://www.bananadance.org/Download/GNU-License-Overview" target="_blank">GLPv2</a><br /><br />Running v<?php echo $db->get_option('version'); ?><br /><?php
		if ($check_update['0'] == "1") {
			echo "<a href=\"index.php?l=update\">Update Program</a>";
		}
		?>
	</div>
	<div class="clear"></div>
</div></div>

<div id="search_q_hover">
  <div id="search_q_top"></div>
  <div id="search_q_body"></div>
</div>
<div id="saved" >Saved!</div>
<div id="loading"></div>
<div id="directions"></div>
<div id="error" onClick="admin_close_error();"></div>

<?php
$manage = $manual->article_sidebar('','1','','1');
echo $manage;
?>

</body>
</html>