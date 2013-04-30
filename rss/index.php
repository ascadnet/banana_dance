<?php

require "../config.php";

if (! empty($_GET['id'])) {
	// Categories
	$category_where = '';
	$categories = explode(',',$_GET['id']);
	foreach ($categories as $aCate) {
		$category_where .= " OR `category`='" . $db->mysql_clean($aCate) . "'";
	}
	$category_where = substr($category_where,4);
	// Begin feed
	$feed = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
	$feed .= "<rss version=\"2.0\">\n";
	$feed .= "<channel>\n";
	// Category Details
	$category = $manual->get_category($_GET['id']);
	$link = $manual->prepare_link('',$_GET['id'],$category['name']);
	// Begin RSS File
	$feed .= "<title>" . NAME . " | " . $db->get_option('company_name') . "</title>\n";
	$feed .= "<link>$link</link>\n";
	$feed .= "<description>Recently published content.</description>\n";
	$feed .= "<lastBuildDate>" . date("r",time()) . "</lastBuildDate>\n";
	$feed .= "<language>en-us</language>\n";
	// Limit articles?
	if (! empty($_GET['limit']) && intval($_GET['limit'])) {
		$limit = " LIMIT 0," . $_GET['limit'];
	} else {
		$limit = '';
	}
	// Get articles
	$q = "SELECT `id`,`category`,`name`,`created`,`meta_desc` FROM `" . TABLE_PREFIX . "articles` WHERE $category_where AND `public`='1' ORDER BY `created` DESC $limit";
//	echo $q;
	$result = $db->run_query($q);
	while ($row = mysql_fetch_array($result)) {
		$article_link = $manual->prepare_link($row['id'],$row['category'],$row['name']);
		$feed .= "<item>\n";
		$feed .= "	<title>" . $row['name'] . "</title>\n";
		$feed .= "	<link>" . $article_link . "</link>\n";
		$feed .= "	<pubDate>" . date("r",strtotime($row['created'])) . "</pubDate>\n";
		$feed .= "	<description>" . $article['meta_desc'] . "</description>\n";
		$feed .= "</item>\n";
	}
	// Close it up
	$feed .= "</channel>\n";
	$feed .= "</rss>\n";
	// Return the output
	header("Content-Type: application/xml; charset=UTF-8");
	echo $feed;
	exit;
}

?>