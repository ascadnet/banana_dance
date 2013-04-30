
		<div class="usersort_filters"><a href="#" class="filters_show" onClick="showFilters();return false;"><img src="imgs/icon-filters.png" width="16" height="16" border="0" alt="Filters" title="Filters" /></a></div>
		<?php
		if ($_GET['filter'] == "1") {
		?>
		<div class="usersort_filters"><a href="index.php?l=<?php echo $page_name; ?>"><img src="imgs/icon-filters-clear.png" width="16" height="16" border="0" alt="Clear Filters" title="Clear Filters" /></a></div>
		<?php
		}
		?>
		<div class="usersort_left">
			<b>Pages:</b> <?php echo $pagination; ?><br />
			<?php echo $az_list; ?><br />
		</div>
		<div class="usersort_right">
			<form action="index.php" method="get">
			<?php
			echo $fields_list;
			?>
			Search: <input type="text" name="q" style="width:100px;" id="search_q" onMouseOver="showSearch('Searching for matching records in the <i><?php
			$total = 0;
			foreach ($default_search as $field) {
				$ffields .= ", $field";
				$total++;
			}
			$ffields = substr($ffields,1);
			echo $ffields;
			?></i> field<?php if ($total > 1) { echo "s"; } ?>.','search_q');" onMouseOut="clearSearch();" value="<?php echo $search_show; ?>" />
			Display: <input type="text" name="d" style="width:40px;" value="<?php echo $display; ?>" /> <button type="submit" class="small_button">Go</button>
		</div>