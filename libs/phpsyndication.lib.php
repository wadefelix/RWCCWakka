<?php
// +--------------------------------------------------------------------------+
// | phpSyndication version 0.0.8 - 2001/04/23                                |
// +--------------------------------------------------------------------------+
// | Copyright (c) 2000-2001 The phpHeaven-team                               |
// +--------------------------------------------------------------------------+
// | License:  GNU/GPL - http://www.gnu.org/copyleft/gpl.html                 |
// +--------------------------------------------------------------------------+
// | Library that helps manage RSS data for syndication                       |
// |                                                                          |
// | usage:    - set the parameters to be used by this libary with following  |
// |             definitions of constants:                                    |
// |                 _PHPSYNDICATION_CACHEDIR                                 |
// |                 _PHPSYNDICATION_CONNECT_TIMEOUT                          |
// |                 _PHPSYNDICATION_KEEP_WORDS                               |
// |                 _PHPSYNDICATION_CONNECTED                                |
// |           - use the class as in the following example                    |
// |                                                                          |
// | example:  require_once('phpSyndication.lib.php');                        |
// |           $s = new RSStoHTML('http://www.phpheaven.net/backend_en.rss'); |
// |           echo($s->getHtml('_blank'));                                   |
// |                                                                          |
// | required: - PHP                                                          |
// |           - Snoopy (find it here: http://freshmeat.net/projects/snoopy)  |
// |                                                                          |
// | to do:    - use cURL support if available, instead of Snoopy             |
// |           - use true XML functions to parse data                         |
// |                                                                          |
// | changes:                                                                 |
// |           version 0.0.8 - 2001/04/23                                     |
// |           - added truncateText method                                    |
// |           - PEAR style comments                                          |
// |                                                                          |
// |           version 0.0.7 - 2001/04/05                                     |
// |           - added getTitle method                                        |
// +--------------------------------------------------------------------------+
// | Last release available on phpHeaven:                                     |
// |    http://www.phpheaven.net/resources/libraries/phpSyndication/          |
// |                                                                          |
// | Author:   Nicolas Hoizey <nhoizey@phpheaven.net>                         |
// +--------------------------------------------------------------------------+


/*
if(!defined('_PHPSYNDICATION_CACHEDIR'))
	define('_PHPSYNDICATION_CACHEDIR',"./libs/cache/");
*/

if(!defined('_LIB_PHPSYNDICATION_LOADED'))
{	define('_LIB_PHPSYNDICATION_LOADED', true);

	/**
	 * CONFIGURATION: timeout for the remote connection
	 *
	 * @const _PHPSYNDICATION_CONNECT_TIMEOUT
	 */
	if(!defined('_PHPSYNDICATION_CONNECT_TIMEOUT'))
		define('_PHPSYNDICATION_CONNECT_TIMEOUT', 5);

	/**
	 * CONFIGURATION: keep full words when truncating ?
	 *
	 * @const _PHPSYNDICATION_KEEP_WORDS
	 */
	if(!defined('_PHPSYNDICATION_KEEP_WORDS'))
		define('_PHPSYNDICATION_KEEP_WORDS', false);

	/**
	 * CONFIGURATION: server is connected to internet (can update cache) ?
	 *
	 * @const _PHPSYNDICATION_CONNECTED
	 */
	if(!defined('_PHPSYNDICATION_CONNECTED'))
		define('_PHPSYNDICATION_CONNECTED', true);

	/**
	 * includes Snoopy class for remote file access
	 */
	require("./libs/snoopy.class.php");

	class RSStoHTML
	{
		var $sourceUrl;		// location of the source RSS file
		var $cacheFile;		// file where will be stored cached data
		var $cacheTimeout;	// cache timeout in seconds (default to 1 hour)
		var $nbItems;		// number of items to show
		var $itemLength;	// maximum length of each item label
		
		/**
		 * Object constructor
		 *
		 * @param	string	URL of the source RSS
		 * @param	string	name of the cache file
		 * @param	integer	cache file life time
		 * @param	integer	number of items to show
		 * @param	integer	maximum size of each item
		 *
		 * @access	public
		 */
		function RSStoHTML($sourceUrl = "", $cacheDir = "", $cacheFile = "", $cacheTimeout = 3600, $nbItems = 10, $itemLength = 20)
		{
			$this->sourceUrl = $sourceUrl;
			$this->cacheDir = $cacheDir;
			if($cacheFile != "")
			{
				$this->cacheFile = $cacheFile;
			}
			else
			{
				$this->cacheFile = ereg_replace("[^a-zA-Z0-1]+", "_", $sourceUrl);
			}
			$this->cacheTimeout = $cacheTimeout;
			$this->nbItems = $nbItems;
			$this->itemLength = $itemLength;
		}

		/**
		 * truncateText
		 *
		 * @return	string	truncated text, keeping words if needed
		 *
		 * @access	private
		 */
		function truncateText($in)
		{
			if(_PHPSYNDICATION_KEEP_WORDS)
			{
				$length = strlen($in);
				$size = min($this->itemLength, $length);
				$p = $size;
				while($p < $length && !ereg("[,.:; ]", $in[$p]))
				{
					$p++;
				}
				if($p < $length)
				{
					$out = substr($in, 0, $p).'...';
				}
				else
				{
					$out = $in;
				}
			}
			else
			{
				$out = substr(strip_tags($in), 0, $this->itemLength - 3).'...';
			}
			return $out;
		}

		/**
		 * Data retriever
		 *
		 * @return	string	data stored in cache file, updated if needed
		 *
		 * @access	private
		 */
		function getData($forcecache=false)
		{
			if(_PHPSYNDICATION_CONNECTED && $forcecache != true && (!file_exists($this->cacheDir.$this->cacheFile) || (filemtime($this->cacheDir.$this->cacheFile) + $this->cacheTimeout - time()) < 0))
			{
				$snoopy = new Snoopy;
				$snoopy->fetch($this->sourceUrl);
				$data = $snoopy->results;

				$cacheFile = fopen($this->cacheDir.$this->cacheFile, "w");
				fwrite($cacheFile, $data);
				fclose($cacheFile);
			}
			// fsockopen failed the last time, so force cache
			elseif ( $forcecache == true )
			{
				if (file_exists($this->cacheDir.$this->cacheFile)) {
					$data = implode('', file($this->cacheDir.$this->cacheFile));
					// set the modified time to a future time, and let the server have time to come up again
					touch($this->cacheDir.$this->cacheFile, time() + $this->cacheTimeout);
				} else {
					$data = "";
				}
			} else {
				$data = implode('', file($this->cacheDir.$this->cacheFile));
			}
			return $data;
		}
		
		/**
		 * Get HTML presentation
		 *
		 * @param	string	specific target attribute for links
		 *
		 * @return	string	HTML presentation
		 *
		 * @access	public
		 */
		function getHtml($fromcache=false)
		{
			$data = $this->getData($fromcache);
			$ret = '';
			$i = 0;
			if(phpversion() >= 4)
			{
				$trans = array_flip(get_html_translation_table(HTML_ENTITIES));
			}
			while(($pos = strpos($data, "<item>")) && ($i++ < $this->nbItems))
			{
				$data = substr($data, $pos + 6);
				$pos = strpos($data, "<title>");
				$data = substr($data, $pos + 7);
				$pos = strpos($data, "</title>");
				$label = substr($data, 0, $pos);
				if(phpversion() >= 4)
				{
					$label = strtr($label, $trans); 
				}
				
				$pos = strpos($data, "<link>");
				$data = substr($data, $pos + 6);
				$pos = strpos($data, "</link>");
				$link = substr($data, 0, $pos);
	
				if($i > 1) $ret .= '<br />';
				$ret .= "- <a href='".$link."' title='".ereg_replace("\"", "", $label)."' target='_blank'>";
				//if(strlen($label) > $this->itemLength) $label = $this->truncateText($label);
				$ret .= $label.'</a>';
			}
			return $ret;
		}

		/**
		 * Get source title
		 *
		 * @param	boolean	prepare it as a link or not
		 * @param	string	specific target attribute for links
		 *
		 * @return	string	HTML presentation
		 *
		 * @access	public
		 */
		function getTitle($fromcache=false)
		{
			$data = $this->getData($fromcache=false);
			$ret = '';
			if(eregi("<title>([^<]+)</title>", $data, $regs))
			{
				$title = $regs[1];
				if(phpversion() >= 4)
				{
					$trans = array_flip(get_html_translation_table(HTML_ENTITIES));
					$title = strtr($title, $trans); 
				}
				$fullTitle = $title;
				if(strlen($title) > $this->itemLength) $title = $this->truncateText($title);
				if(eregi("<link>([^<]+)</link>", $data, $regs))
				{
					$link = "<a href='".$regs[1]."' target='_blank' title='".$fullTitle."'>".$title."</a>";
					$ret = $link;
				}
				else
				{
					$ret = $title;
				}
			}
			return $ret;
		}
	}
}
?>
