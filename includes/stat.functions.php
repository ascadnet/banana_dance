<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: Statistics and graph rendering functions.
	
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


class stats extends db {

	// ---------------------------------------------------------------------------
	// 	Get stats for the homepage
	
	function compile_home_stats() {
		$stats = array();
		// Comments
		$q = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "comments` WHERE `pending`!='1'";
		$count1 = $this->get_array($q);
		$q1 = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "comments` WHERE `pending`='1'";
		$count2 = $this->get_array($q1);
		$q1a = "SELECT SUM(up+down) FROM `" . TABLE_PREFIX . "comments`";
		$count2a = $this->get_array($q1a);
		$stats['comments'] = $count1['0']+$count2['0'];
		$stats['comments_live'] = $count1['0'];
		$stats['comments_pending'] = $count2['0'];
		$stats['comment_votes'] = $count2a['0'];
		// Categories
		$q2 = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "categories`";
		$count3 = $this->get_array($q2);
		$stats['categories'] = $count3['0'];
		// Articles
		$q3 = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "articles` WHERE `public`!='1'";
		$count4 = $this->get_array($q3);
		$q4 = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "articles` WHERE `public`='1'";
		$count5 = $this->get_array($q4);
		$q4a = "SELECT SUM(`views`) FROM `" . TABLE_PREFIX . "articles`";
		$count5a = $this->get_array($q4a);
		$stats['articles'] = $count4['0']+$count5['0'];
		$stats['articles_private'] = $count4['0'];
		$stats['articles_public'] = $count5['0'];
		$stats['views'] = $count5a['0'];
		// Users
		$q5 = "SELECT COUNT(*) FROM `" . TABLE_PREFIX . "users";
		$count6 = $this->get_array($q5);
		$stats['users'] = $count6['0'];
		return $stats;
	}
	
	
	// ---------------------------------------------------------------------------
	// 	Get daily stats for last "x" days
	
	function stats_daily($days = '7',$table = '',$field = 'date') {
		if (empty($table)) {
			$table = TABLE_PREFIX . 'comments';
		}
		// Add one day, this will exclude
		// today which isn't finished.
		$time_sub = time() - ($days*86400) + 86400;
		$current = 0;
		$send_back = array();
		while ($days > 0) {
			$get_day = $time_sub + ($current*86400);
			$format_date = date('Y-m-d',$get_day);
			$show_date = date('F jS',$get_day);
			$q = "SELECT COUNT(*) FROM `$table` WHERE `$field` LIKE '$format_date%'";
			$count = $this->get_array($q);
			$send_back[$show_date] = $count['0'];
			$current++;
			$days--;
		}
		return $send_back;
	}
	
	
	// ---------------------------------------------------------------------------
	// 	Generate Bar Graph
	
	function bar_graph_js($data,$name = 'data') {
		$current = 0;
		foreach ($data as $date => $value) {
			$js .= $name . ".setValue($current, 0, '$date');\n";
			$js .= $name . ".setValue($current, 1, $value);\n";
			$current++;
		}
		return $js;
	}

}

?>