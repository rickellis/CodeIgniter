<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


// --------------------------------------------------------------------

/**
 * Header Navigation
 *
 * Generates the Navigation links in the header
 *
 * @access	public
 * @return	string
 */
function header_navigation()
{
	$CI =& get_instance();

	$uri = $CI->uri->segment(1);
	
	$nav = array(
					''		 						=> 'Home', 
					'dispatches/find-newest.html'	=> 'Dispatches', 
					'daily-photo/find-newest.html' 	=> 'Daily Photo', 
					'about/intro.html' 				=> 'About Me'
				);
	
	$out = '';
	foreach ($nav as $key => $val)
	{
		$highlight = (FALSE === strpos($key, $uri)) ? FALSE : TRUE;
	
		$out .= "\n\t\t\t\t";
		$out .= '<li>';
		$out .= ($highlight) ? '<span id="header_nav_on">' : '';
		$out .= '<a href="'.site_url().$key.'">';
		$out .= $val;
		$out .= '</a>';
		$out .= ($highlight) ? '</span>' : '';
		$out .= '</li>';
	}
	$out .= "\n";
	
	return $out;	
}


// --------------------------------------------------------------------

/**
 * Transmogrifier
 *
 * Lets me use human markers in my entries, which get converted to HTML
 *
 * @access	public
 * @return	string
 */
function transmogrifier($str)
{
	$matrix = array(
						'30'		=> 'thirty',
						'30l'		=> 'thirty-leftpad',

						'33'		=> 'thirtythree',
						'33l'		=> 'thirtythree-leftpad',

						'40'		=> 'forty',
						'40l'		=> 'forty-leftpad',
	
						'50'		=> 'fifty',
						'50l'		=> 'fifty-leftpad',

						'60'		=> 'sixty',
						'60l'		=> 'sixty-leftpad',

						'67'		=> 'sixtyseven',
						'67l'		=> 'sixtyseven-leftpad',

						'70'		=> 'seventy',
						'70l'		=> 'seventy-leftpad',

						'100'		=> 'onehundred',

						'clear'		=> 'clear',
						
						'horline'	=> 'horline'
					);

	// Standardize Newlines to make matching easier
	$str = str_replace(array("\r\n", "\r"), "\n", $str);			
		
	// Reduce line breaks.  If there are more than two consecutive linebreaks
	// we'll compress them down to a maximum of two since there's no benefit to more.
	$str = preg_replace("/\n\n+/", "\n\n", $str);
	
	// And trim it
	$str = trim($str);
	
	// Swap a line containing four or more dashes to the "clear" div marker
	$str = preg_replace('#^[-]{4,}$#m', 'div:clear', $str);

	// Swap a line containing four or more dashes to the "clear" div marker
	$str = preg_replace('#^[_]{4,}$#m', 'div:horline', $str);

	// Split the text string at each occurance of div:blah
	$chars = preg_split('#^(div:[0-9a-z]+)$#m', $str, -1, PREG_SPLIT_DELIM_CAPTURE);
	
	$add_close = FALSE;
	$newstr = '';
	foreach ($chars as $val)
	{
		if (preg_match('#^div:([0-9a-z]+)#', $val, $match))
		{
			if ($add_close == TRUE)
			{
				$newstr = rtrim($newstr);
				$newstr .= "\n</div>\n\n";			
				$add_close = FALSE;
			}

			if (isset($matrix[$match[1]]))
			{
				$val = '<div class="'.$matrix[$match[1]].'">';
				$add_close = TRUE;
			}
		}
	
		$newstr .= $val;
	}
	$newstr .= "\n\n</div>\n";
	
	// Remove the whitespace from empty divs, and adds a non-breaking-space
	$newstr = preg_replace("#(<div class=['|\"].+?['|\"]>)\s+(<\/div>)#i", "$1&nbsp;$2", $newstr);
	
	return auto_typography($newstr);
}

// --------------------------------------------------------------------

/**
 * Stripper
 *
 * Strips proprietary markers when viewing plain text files
 *
 * @access	public
 * @return	void
 */
function stripper($str)
{
	$CI =& get_instance();

	$filename = end($CI->uri->segments);
	
	// If we're dealing with a text file we'll strip out
	// all the pseudo-tags and what not
	if (FALSE !== (strpos($filename, '.txt')))
	{
		// Remove dashes
		$CI->output->final_output = preg_replace('#^[-]{4,}$#m', '', $CI->output->final_output);

		// Remove horline markers
		$CI->output->final_output = preg_replace('#^[_]{4,}$#m', '', $CI->output->final_output);
		
		// Remove the divs
		$CI->output->final_output = preg_replace('#^(div:[0-9a-z]+)$#m', '', $CI->output->final_output);

		// Reduce line breaks.  If there are more than two consecutive linebreaks
		// we'll compress them down to a maximum of two since there's no benefit to more.
		$CI->output->final_output = preg_replace("/\n\n+/", "\n\n", $CI->output->final_output);
		
		$CI->output->final_output = trim($CI->output->final_output);
	}

}



