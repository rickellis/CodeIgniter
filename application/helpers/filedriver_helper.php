<?php


function snippet_loader($path)
{
	$filedata = read_file($path);

	// Create email links in our file
	$filedata = preg_replace_callback("#Link:\s*([a-z0-9\+_\-]+(\.[a-z0-9\+_\-]+)*@[a-z0-9\-]+(\.[a-z]{2,6})+)\s*(\[(.+)?\]|)#i", 'emaillinker_callback', $filedata);
	
	// Create hyperlinks for any instances of the Link: construct
	$filedata = preg_replace_callback('#link:\s*(http://|https://|www\.|)([\w\/]+[^\s\[\<]+)\s*(\[([a-z0-9\s\-_\.;\'\"!/]+)\]|)#i', 'hyperlinker_callback', $filedata);

	return auto_typography($filedata);
}



	function hyperlinker_callback($matches)
    {
    	$baseuri = '';
    
		// Are we dealing with an internal or external link? The way we determine this, although,
		// it's not foolproof, is we look for either a period (indicating, presumably, a file extension)
		// or the absense of either "http://" or "www.".
		$external_link = (FALSE !== (strpos($matches[2], '.')) OR $matches[1] != '') ? TRUE : FALSE;
		
		// start building the output
		$link = '<a href="';
		
		// It's an internal link so we'll try to figure out the path
		if ($external_link == FALSE)
		{
			// Did they include the basepath? This also isn't foolproof if they indicated the
			// path incorrectly, but we'll make an assumption based on the presense of a forward slash
			if (FALSE === (strpos($matches[3], '/')))
			{
				$link .= site_url().$baseuri;
			}
			else
			{
				$link .= site_url();
			}
			
			// Grab the filename (and path if it exists) and kill the leading slash
			$filename = ltrim($matches[2], '/');

			// Format the URL suffix if specified in the config file
			$url_suffix = '.html';

			// Add the URL suffix if it's not already in place.			
			if ($url_suffix != '' AND $filename != '')
			{			
				if ( ! preg_match('#^[a-z0-9\_\-\/\.]+'.$url_suffix.'$#i', $filename))
				{
					$filename .= $url_suffix;
				}
			}
			
			// Add the filename to our link
			$link .= $filename;		
		}
		else
		{
			// It's an external link so we just add the "http://" prefix if needed
			$link .= ($matches[1] == 'www.' OR $matches[1] == '') ? 'http://www.' : $matches[1];
			$link .= ltrim($matches[2], '/');
		}
		
		// The closing tag
		$link .= '">';
		
		// Build the link "name" based on whether the user specified it or not
		if ($matches[3] != '')
		{
			$link .= str_replace(array('[', ']'), '', $matches[3]);
		}
		else
		{
			$link .= ucwords(str_replace(array('_', '-'), ' ', $matches[2]));
		}
		
		// Close it
		$link .= '</a>';
		
		return $link;
    }

	// --------------------------------------------------------------------

	/**
	 * Email Linker
	 *
	 * Callback function to automatically create email links in entries
	 *
	 * @access	private
	 * @param	string
	 * @return	string
	 */
	function emaillinker_callback($matches)
	{
		$link = '<a href="mailto:'.$matches[1].'">';
		
		if ($matches[5] != '')
		{
			$link .= $matches[5];
		}
		else
		{
			$link .= $matches[1];
		}
		
		$link .= '</a>';
		
		return $link;
	}
