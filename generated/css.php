<?php

	/*
	*
	*	This file is designed to standardized
	*	fonts, colors, and other styles over
	*	multiple themes. It may or may not be
	*	compatible with your theme. Themes
	*	marked with "v2 Theme" are compatible.
	*
	*/


	// ----------------------------------------------------------
	// Body
	$background = '#fff';
	$background_type = 'color'; // color or image

	// General Padding
	$pad_less = "6px";
	$pad = "12px";
	$pad_more = "24px";
	$pad_max = "48px";

	// Links
	$link_color = '#4D90FE';
	$link_decoration = 'none'; // none or underline
	$link_color_hover = '#4D90FE';
	$link_decoration_hover = 'underline'; // none or underline

	// General Fonts
	$fonts = 'arial, verdana';
	$fonts_color = '#222';
	$fonts_color_secondary = '#666';
	$line_height = '14pt';
	$fonts_size_large = '12pt';
	$fonts_size = '10pt';
	$fonts_size_small = '9pt';
	$fonts_size_tiny = '8pt';
	
	// Lists
	$list_margin = '24px 0 24px 0'; // For ul and ol element
	$list_padding = '4px 0 4px 0'; // For li element
	$list_style = 'square';
	
	// Headings
	// h1
	$heading1_size = '25pt';
	$heading1_color = '#222';
	$heading1_font = '';
	$heading1_margin = $pad_more . ' 0 ' . $pad . ' 0';
	$heading1_padding = '0 0 ' . $pad . ' 0';
	$heading1_border = ''; // Bottom border
	$heading1_i = '';
	$heading1_b = '';
	$heading1_u = '';
	$heading1_background_type = ''; // color or image
	$heading1_background = '';
	
	// h2
	$heading2_size = '18pt';
	$heading2_color = '#222';
	$heading2_font = '';
	$heading2_margin = $pad_more . ' 0 ' . $pad . ' 0';
	$heading1_padding = '0 0 ' . $pad . ' 0';
	$heading2_border = ''; // Bottom border
	$heading2_i = '';
	$heading2_b = '';
	$heading2_u = '';
	$heading2_background_type = ''; // color or image
	$heading2_background = '';
	
	// h3
	$heading3_size = '15pt';
	$heading3_color = '#222';
	$heading3_font = '';
	$heading3_margin = $pad_more . ' 0 ' . $pad . ' 0';
	$heading1_padding = '0 0 ' . $pad . ' 0';
	$heading3_border = ''; // Bottom border
	$heading3_i = '';
	$heading3_b = '';
	$heading3_u = '';
	$heading3_background_type = ''; // color or image
	$heading3_background = '';
			
	// Quotes (blockquote)
	$quote_font = 'georgia, verdana';
	$quote_color = '#333';
	$quote_line_height = '1.6em';
	$quote_size = '0.9em';
	$quote_padding = '12px';
	$quote_margin = '24px 0 24px 0';
	$quote_border = ''; // Border
	$quote_i = '1';
	$quote_b = '';
	$quote_u = '';
	$quote_background_type = 'color'; // color or image
	$quote_background = '#F0EEE7';
	
	// Form fields
	$input_font = '';
	$input_color = '#222';
	$input_size = '10pt';
	$input_padding = '0px 10px 0 10px';
	$input_height = '27px';
	$input_line_height = '27px';
	$input_margin = '';
	$input_border_tl = '1px solid #C0C0C0'; // Border: Top and Left
	$input_border_br = '1px solid #D9D9D9'; // Border: Bottom and Right
	$input_i = '';
	$input_b = '';
	$input_u = '';
	$input_background_type = 'color'; // color or image
	$input_background = '#fff';
	$input_shadow = '1px';
	$input_shadow_blur_radius = '4px';
	$input_shadow_color = '#e1e1e1';
	$input_radius = '';
	
	$textarea_padding = '10px;';
	
	// Buttons and Submits
	$button_font = '';
	$button_color = '#fff';
	$button_size = '10pt';
	$button_padding = '0 10px 0 10px';
	$button_height = '27px';
	$button_line_height = '27px';
	$button_margin = '12px 0 0 0';
	$button_border_tl = '1px solid #3079ED'; // Border: Top and Left
	$button_border_br = '1px solid #3079ED'; // Border: Bottom and Right
	$button_i = '';
	$button_b = '1';
	$button_u = '';
	$button_background_type = 'color'; // color or image
	$button_background = '#4D90FE';
	$button_shadow = '';
	$button_shadow_blur_radius = '';
	$button_shadow_color = '';
	$button_radius = '2px';
	
	// Text shadows
	$text_shadow_opacity = '0.5';
	$text_shadow_color = ''; // black or white
	$text_shadow_offset = '1px';
	$text_shadow_weight = '';
	$text_shadow_blur_radius = '1px';
	if (! empty($text_shadow_weight)) {
		if ($text_shadow_color == 'white') { $text_shadow_color_code = '255, 255, 255'; }
		else if ($text_shadow_color == 'black') { $text_shadow_color_code = '0, 0, 0'; }
		else { $text_shadow_color_code = '255, 255, 255'; }
		$text_shadows = "-moz-text-text_shadow: $text_shadow_offset $text_shadow_offset $text_shadow_blur_radius rgba($text_shadow_color_code, $text_shadow_opacity);\n";
		$text_shadows .= "-webkit-text-text_shadow: $text_shadow_offset $text_shadow_offset $text_shadow_blur_radius rgba($text_shadow_color_code, $text_shadow_opacity);\n";
		$text_shadows .= "text-text_shadow: $text_shadow_offset $text_shadow_offset $text_shadow_blur_radius rgba($text_shadow_color_code, $text_shadow_opacity);\n";
	}
	
	// Box Rounding
	$div_rounding = '';
	$div_rounding_less = '4px';
	if (! empty($div_rounding)) {
		$rounding = "-webkit-border-radius: " . $div_rounding . ";\n";
		$rounding .= "-moz-border-radius: " . $div_rounding . ";\n";
		$rounding .= "border-radius: " . $div_rounding . ";\n";
	} else {
		$rounding = '';
	}
	if (! empty($div_rounding_less)) {
		$rounding_less = "-webkit-border-radius: " . $div_rounding_less . ";\n";
		$rounding_less .= "-moz-border-radius: " . $div_rounding_less . ";\n";
		$rounding_less .= "border-radius: " . $div_rounding_less . ";\n";
	} else {
		$rounding_less = '';
	}
	
	// Box shadows
	$shadow_opacity = '0.5';
	$shadow_color = '#e1e1e1';
	$shadow_offset = '2px';
	$shadow_blur_radius = '2px';
	if (! empty($shadow_weight) && ! empty($shadow_color)) {
		$shadows = "-moz-box-shadow: $shadow_offset $shadow_offset $shadow_blur_radius $shadow_color;\n";
		$shadows .= "-webkit-box-shadow: $shadow_offset $shadow_offset $shadow_blur_radius $shadow_color;\n";
		$shadows .= "box-shadow: $shadow_offset $shadow_offset $shadow_blur_radius $shadow_color;\n";
	} else {
		$shadows = '';
	}
	
	// ----------------------------------------------------------
	// Additional Definitions
	// These can be used throughout the other
	// style sheets.
	
	$bg1 = '#F5F5F5'; // Main content background
	$bg2 = '#4D90FE'; // Secondary background
	$bg3 = '#E5E5E5'; // // Third color background
	$borders = '1px solid #E5E5E5'; // Standardized border
	$border_color = '#E5E5E5'; // Color
	$error_color = '#EB4A4A';
	$hover_color = '#FFF5A0';

?>