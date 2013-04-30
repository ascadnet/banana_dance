<?php

/*	====================================================

	BANANA DANCE by Jon Belelieu
	http://www.bananadance.org/
	Copyright (C) 2011 Jon Belelieu
	
	File Function: CAPTCHA functions.
	
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

class captcha {

	// -----------------------------------------------------------------------------
	//	Generate a CAPTCHA string
	
	function generate_captcha($type = "random") {
		if ($type == "random") {
   			$theCaptcha = substr(md5(md5(rand(100000,999999))),0,8);
   			$send_captcha = $theCaptcha;
   		} else {
   			$word1 = $this->possible_word('short');
   			$word2 = $this->possible_word('long');
   			$theCaptcha = $word1 . $word2;
   			$send_captcha = $word1 . "|" . $word2;
   		}
   		return $send_captcha;
		// $this->generate_captcha_image($send_captcha,$type);
	}
	
	// -----------------------------------------------------------------------------
	// 	Generate the actual CAPTCHA file
	
	function generate_captcha_image($text, $type = "random", $width = "200", $height = "50") {
		header('Content-Type: image/png');
		$im = imagecreatetruecolor($width, $height);
		// Create some colors
		$white = imagecolorallocate($im, 255, 255, 255);
		$grey = imagecolorallocate($im, 225, 225, 225);
		$black = imagecolorallocate($im, 0, 0, 0);
		$blue = imagecolorallocate($im, 111, 164, 252);
		imagefilledrectangle($im, 0, 0, $width, $height, $white);
		// Get the font file.
		$font = PATH . "/includes/fonts/handsean.ttf";
		if ($type == "random") {
			$set_one = substr($text,0,4);
			$set_two = substr($text,4,3);
			$push_left = "65";
		} else {
			$exp_words = explode('|',$text);
			$set_one = $exp_words['0'];
			$word_len = strlen($set_one);
			$push_left = ($word_len * 12) + 10;
			$set_two = $exp_words['1'];
		}
		// Background distraction font
		$font_sizeA = "50";
		$fake_text = substr(md5(time()),0,12);
		imagettftext($im, $font_sizeA, -6, 0, 40, $grey, $font, $fake_text);
		// Left side black font
		$font_sizeB = "20";
		imagettftext($im, $font_sizeB, -3, 2, 30, $black, $font, $set_one);
		// Right side blue font
		$font_sizeC = "25";
		imagettftext($im, $font_sizeC, 4, $push_left, 40, $blue, $font, $set_two);
		imagepng($im);
		imagedestroy($im);
	}

	// -----------------------------------------------------------------------------
	// 	Possible Words
	
	function possible_word($type = "short") {
		$short_words = array(
			'red','orange','yellow','green','blue','purple','gray','short','cold','hot',
			'angry','itchy','lazy','scary','brave','calm','eager','happy','jolly','kind',
			'nice','proud','silly','witty','loud','noisy','raspy','tall','tiny','small'
		);
		$long_words = array(
			'alarm','animal','aunt','bait','balloon','bath','bead','beam','bean',
			'bedroom','boot','bread','brick','brother','camp','chicken','children',
			'crook','deer','dock','doctor','downtown','drum','dust','eye','family',
			'father','fight','flesh','food','frog','goose','grade',
			'grape','grass','hook','horse','jail','jam','kiss','kitten','light','loaf','lock',
			'lunch','lunchroom','meal','mother','notebook','owl','pail','parent','park','plot',
			'rabbit','rake','robin','actor','airplane','airport','army','baseball','beef',
			'birthday','boy','brush','bushes','butter','cast','cave','cent',
			'cherries','cherry','cobweb','coil','cracker','dinner','eggnog','elbow',
			'face','fireman','flavor','gate','glove','glue','goldfish','goose',
			'grain','hair','haircut','hobbies','holiday','hot','jellyfish','ladybug',
			'mailbox','number','oatmeal','pail','pancake','pear','sack','sail','scale'
		);
		if ($type == "short") {
			$ary_len = sizeof($short_words) - 1;
			$random = rand(0,$ary_len);
			$pick_word = $short_words[$random];
		} else {
			$ary_len = sizeof($long_words) - 1;
			$random = rand(0,$ary_len);
			$pick_word = $long_words[$random];
		}
		return $pick_word;
	}
	
	
}

?>