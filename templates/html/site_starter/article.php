

   	<div id="main_right">
   		%links%
   		<div id="article_info">
   			%creator_panel%
   		</div>
   		
   		<span class="right_title">Page Links</span>
   		%article_sublinks%
   		
   		<span class="right_title">Related Links</span>
   		%article_related%
   		
   		<span class="right_title">Tags</span>
   		%article_tags%
   		
   		%sharing_code%
   		
   	</div>
   	
	<div id="main_center">
		
		<h1 class="no_margin">%article_name%</h1>
		
   		<div id="primary_article_holder">
   			%formatted_article%
   		</div>
   		<div class="clear"></div>
   		
   		<div class="space"></div>
   		
   		<div id="article_bottom">
	   		<p class="small" style="float:left;">Rate this page: <a href="#" onclick="ratePage('%article_id%','1');return false;">Helpful (<span id="current_ups">%article_ups%</span>)</a> - <a href="#" onclick="ratePage('%article_id%','-1');return false;">Not Helpful (<span id="current_downs">%article_downs%</span>)</a></p>
	   		<p class="small right">Last updated %last_updated% by <a href="%program_url%/user/%last_updated_by%">%last_updated_by%</a></p>
   		</div>
   		
   		<div class="less_space"></div>
   	
	   	<!-- START COMMENTS -->
	   	%discussion%
	   	<!-- END COMMENTS -->
	   	
	</div>


