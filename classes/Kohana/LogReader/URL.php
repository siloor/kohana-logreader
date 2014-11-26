<?php defined('SYSPATH') or die('No direct script access.');
/**
 * LogReader
 * 
 * LogReader helps you explore Kohana Log files.
 * 
 * @package     Kohana/LogReader
 * @category    Base
 * @author      Milan Magyar <milan.magyar@gmail.com>
 * @copyright   (c) 2014 Milan Magyar
 * @license     MIT
 */
class Kohana_LogReader_URL
{
	/**
	 * Returns LogReader base url
	 * 
	 * @return  string
	 * @uses    URL::base()
	 */
	public static function base()
	{
		return URL::base(Request::current()) . Route::get('logreader')->uri() . '/';
	}
	
	/**
	 * Returns LogReader API url
	 * 
	 * @return  string
	 * @uses    URL::base()
	 */
	public static function api_base()
	{
		return URL::base(Request::current()) . Route::get('logreader/api')->uri() . '/';
	}
	
	/**
	 * Returns LogReader static url
	 * 
	 * @return  string
	 * @uses    URL::base()
	 */
	public static function static_base()
	{
		if (Valid::url(LogReader::get_static_route()))
		{
			return LogReader::get_static_route() . '/';
		}
		else
		{
			return URL::base(Request::current()) . Route::get('logreader/media')->uri() . '/';
		}
	}
	
	/**
	 * Returns Log message url
	 * @param   string  $message_id  Id of the log message
	 * @return  string
	 * @uses    URL::base()
	 */
	public static function log_message($message_id)
	{
		return URL::base(Request::current()) . Route::get('logreader/message')->uri(array('message' => $message_id));
	}

	/**
	 * Returns LogReader base url with bad username and password to log user out from HTTP basic authentication
	 * 
	 * @return  string
	 * @uses    LogReader_URL::base()
	 */
	public static function logout_url()
	{
		return preg_replace('(://)', '://badusername:badpassword@', static::base(), 1);
	}
	
	/**
	 * Replace parameters in string by keys
	 * 
	 * @param   string  $text  Template text
	 * @param   array   $args  Array of replacements (keys are the parameters to change, values are the replacements)
	 * @return  string
	 */
	public static function str_template($text, $args = array())
	{
		$text = preg_replace("/%(?!\((.*?)\))/i", '%%', $text);

		preg_match_all('/%\((.*?)\)/', $text, $matches, PREG_SET_ORDER);
		
		$values = array();
		
		foreach ($matches as $match)
		{
			array_push($values, $args[$match[1]]);
		}

		unset($match);
		
		return vsprintf(preg_replace('/%\((.*?)\)/', '%', $text), $values);
	}
	
	/**
	 * Returns page url from the template
	 * 
	 * @param   integer  $page       Page number
	 * @param   string   $url        Url template
	 * @param   string   $first_url  First page url template (if it is different)
	 * @return  string
	 * @uses    LogReader_URL::str_template()
	 */
	public static function page_url($page, $url, $first_url = NULL)
	{
		return static::str_template($first_url && $page === 1 ? $first_url : $url, array('page' => $page));
	}
	
	/**
	 * Returns urls and titles to the pager in the View
	 * 
	 * @param   integer  $current_original  Current page number
	 * @param   integer  $total             Number of pages
	 * @param   string   $url               Url template
	 * @param   string   $first_url         First page url template (if it is different)
	 * @return  array
	 * @uses    LogReader_URL::page_url()
	 */
	public static function pager($current_original, $total, $url, $first_url = NULL)
	{
		$pages = array();
		
		$current_original = (int) $current_original;
		$total = (int) $total;
		
		if (!$total)
		{
			return array();
		}
		
		$current = $current_original;
		
		if ($current > $total)
		{
			$current = 1;
		}
		
		$start = ($total - $current) > 5 ? $current - 5 : $current - 10 + ($total - $current);
		
		if ($start < 1)
		{
			$start = 1;
		}
		
		$end = $start + 10;
		
		if ($end > $total)
		{
			$end = $total;
		}
		
		if ($start !== 1)
		{
			array_push($pages, array('title' => 1, 'url' => static::page_url(1, $url, $first_url)));
			
			if ($start !== 2)
			{
				array_push($pages, array('title' => '...'));
			}
		}
		
		for ($i = $start; $i <= $end; $i++)
		{
			array_push($pages, array('title' => $i, 'url' => static::page_url($i, $url, $first_url)));
		}
		
		if ($end !== $total)
		{
			if ($end !== ($total - 1))
			{
				array_push($pages, array('title' => '...'));
			}
			
			array_push($pages, array('title' => $total, 'url' => static::page_url($total, $url, $first_url)));
		}
		
		$previous = $current_original - 1;
		
		if ($previous < 1 || $previous > $total)
		{
			$previous = NULL;
		}
		else
		{
			array_unshift($pages, array('title' => 'previous', 'url' => static::page_url($previous, $url, $first_url)));
		}
		
		$next = $current_original + 1;
		
		if ($next > $total)
		{
			$next = NULL;
		}
		else
		{
			array_push($pages, array('title' => 'next', 'url' => static::page_url($next, $url, $first_url)));
		}
		
		return $pages;
	}
	
}
