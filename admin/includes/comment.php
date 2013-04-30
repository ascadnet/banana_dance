<?php

if ($privileges['edit_comment_status'] != "1") {
	$db->admin_inline_error('You do not have the privileges to perform this task.','1');
} else {

   	if (! empty($_GET['id'])) {
   		$comment = $manual->get_a_comment($_GET['id']);
   		$article = $manual->get_article($comment['article']);
   	}
   	
?>

<script>
<!--
	// --------------------------------------------
	//	CTRL-S Saves a Form
	$.ctrl('S', function() {
	    saveCommentChanges('<?php echo $_GET['id']; ?>');
	});
	
	var current_function = 'comment_edit';
	var additional = '';
-->
</script>
<script type="text/javascript" src="<?php echo URL; ?>/js/suggest.js"></script>

<form id="edit" onsubmit="return saveCommentChanges('<?php echo $_GET['id']; ?>');">
<input type="hidden" name="id" value="<?php echo $comment['id']; ?>" />

	<div class="submit">
		<img src="imgs/icon-save.png" width="16" height="16" border="0" onclick="saveCommentChanges('<?php echo $_GET['id']; ?>');" />
   		<div class="submit_split"></div>
   		<a href="http://www.doyoubananadance.com/Comments/" target="_blank"><img src="imgs/icon-help.png" width="16" height="16" border="0" title="Help" alt="Help" /></a>
	</div>
		
	<h1>Comment by <a href="index.php?l=users_edit&id=<?php echo $comment['user']; ?>"><?php echo $comment['user']; ?></a> on <?php echo $db->format_date($comment['date']); ?></h1>
	
		<h2>Overview</h2>
		
		<label>Comment Content</label>
		<textarea name="comment" id="comment" style="width:97%;height:150px;"><?php echo $comment['comment']; ?></textarea>
		
		<label>Comment Type<span class="help" id="h-3">(?)</span><div class="help_bubble" id="h-3b"><div class="hbpad">Comments are grouped into "Comment Types". Select the grouping into which this comment should be placed.</div></div></label>
		<ul class="option_list" id="A">
		<?php
		$status = $admin->get_comment_statuses($comment['status']);
		echo $status;
		?>
		</ul>
		
		<div class="col50">
			<label>Status</label>
			<ul class="option_list" id="B">
				<li<?php if ($comment['pending'] != "1") { echo " class=\"selected\""; } ?>>
					<input type="radio" name="pending" value="0"<?php if ($comment['pending'] != "1") { echo " checked=\"checked\""; } ?> /> Approved
				</li>
				<li<?php if ($comment['pending'] == "1") { echo " class=\"selected\""; } ?>>
					<input type="radio" name="pending" value="1"<?php if ($comment['pending'] == "1") { echo " checked=\"checked\""; } ?> /> Pending
				</li>
			</ul>
		</div>
		<div class="col50">
			<label>Contract Subcomments?<span class="help" id="h-2">(?)</span><div class="help_bubble" id="h-2b"><div class="hbpad">If set to "Yes", all replies to primary comments will be hidden until the user expands them.</div></div></label>
			
			<ul class="option_list" id="C">
				<li<?php if ($comment['contract_subcomments'] == "1") { echo " class=\"selected\""; } ?>>
					<input type="radio" name="contract_subcomments" value="1"<?php if ($comment['contract_subcomments'] == "1") { echo " checked=\"checked\""; } ?> /> Yes
				</li>
				<li<?php if ($comment['contract_subcomments'] != "1") { echo " class=\"selected\""; } ?>>
					<input type="radio" name="contract_subcomments" value="0"<?php if ($comment['contract_subcomments'] != "1") { echo " checked=\"checked\""; } ?> /> No
				</li>
			</ul>
		</div>
		<div class="clear"></div>
		
		<div class="divide"></div>
		
		<h2>Comment Location</h2>
		
		<div class="col_left_sm">
			<label>Current Page</label>
			<?php
				$article = $manual->get_article($comment['article'],'1','id,name');
				echo "<a href=\"index.php?l=article_edit&id=" . $article['id'] . "\">" . $article['name'] . "</a>";
			?>
		</div>
		<div class="col_right_sm">
			<label>Move to New Page<span class="help" id="h-1">(?)</span><div class="help_bubble" id="h-1b"><div class="hbpad">1. Type the name of the page you want to move this comment.<br />2. Select the new target page from the results.<br />3. Save the changes.</div></div></label>
			<input type="text" name="article_name" id="article_name_pos" onkeyup="suggest('<?php echo TABLE_PREFIX; ?>articles',this.value,'name','article_name_pos','id','name');" value="<?php echo $article['name']; ?>" style="width:97%;" />
			<input type="hidden" name="article" id="article_id" value="<?php echo $comment['article']; ?>" />
		</div>
		<div class="clear"></div>

<?php
}
?>