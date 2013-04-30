	<script type="text/javascript">
	<!--
	function showDiscussion(show_dis) {
		if (show_dis == 1) {
		   	$('#show_dis').fadeOut('300', function () {
	   			$('#show_article').fadeIn('300');
	   		});
		} else {
		   	$('#show_article').fadeOut('300', function () {
	   			$('#show_dis').fadeIn('300')
	   		});
		}
	}
	-->
	</script>

		<div id="bd_article">
		
      		<div id="show_article">     
      		
      			<h1 style="text-align:center;">%article_name%</h1>
      			
      			%article_sublinks%
      			
    				<!-- START ARTICLE BODY -->
    				<div id="primary_article_holder">
    					%formatted_article%
    				</div>
    				<!-- END ARTICLE BODY -->
   	   			
   	   			<p id="flipflop" class="center make_button">
   	   				<a href="#" onclick="showDiscussion('0');return false;">View Discussion (%total_comments%)</a>
   	   			</p>
   	   			
   	   		</div>
		   		
   			<div id="show_dis" style="display:none;">
	   			<!-- START COMMENTS -->
	   			%discussion%
	   			<!-- END COMMENTS -->
   			</div>
   			
		</div>
