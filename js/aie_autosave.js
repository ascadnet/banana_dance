
	// --------------------------------------------
	//	Auto-save
	function timerMethod() {
	    saveArticle('0','1');
	}
	var timer = setInterval(timerMethod, 120000); // milliseconds = every 2 minutes