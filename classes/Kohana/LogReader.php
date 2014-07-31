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
class Kohana_LogReader
{
	/**
	 * LogReader config
	 * 
	 * @var  array
	 */
	public static $config;

	/**
	 * LogReader store
	 * 
	 * @var  LogReader_Store
	 */
	public static $store;

	/**
	 * Log message levels
	 * 
	 * @var  array
	 */
	public static $levels = array(
			'WARNING',
			'DEBUG',
			'ERROR',
			'CRITICAL',
			'EMERGENCY',
			'NOTICE',
			'INFO',
		);
	
	/**
	 * Log message styles
	 * 
	 * @var  array
	 */
	public static $styles = array(
			'WARNING' => 'warning',
			'DEBUG' => 'warning',
			'ERROR' => 'danger',
			'CRITICAL' => 'danger',
			'EMERGENCY' => 'danger',
			'NOTICE' => 'info',
			'INFO' => 'primary',
		);

	/**
	 * Initialize LogReader
	 * 
	 * @param   array  $config  Configuration for LogReader
	 * @return  void
	 */
	public static function init($config)
	{
		self::$config = $config;

		$store_class = 'LogReader_Store_' . self::$config['store']['type'];

		self::$store = new $store_class(self::$config['store']);
	}
	
	/**
	 * Returns the log message by Id
	 * 
	 * @param   string  $message_id  Id of the log message
	 * @return  array
	 */
	public static function get_message($message_id)
	{
		return self::$store->get_message($message_id);
	}
	
	/**
	 * Returns log messages
	 * 
	 * @param   string  $date_from  Start date of log messages (if not given, it starts with the first log)
	 * @param   string  $date_to    End date of log messages (if not given, it ends with the last log)
	 * @param   int     $limit      Limit
	 * @param   int     $offset     Offset
	 * @param   string  $search     The message filter
	 * @param   array   $levels     The levels filter
	 * @param   array   $ids        The ids filter
	 * @return  array   Limited matched messages and the count of matched log messages
	 */
	public static function get_messages($date_from = FALSE, $date_to = FALSE, $limit = 10, $offset = 0, $search = NULL, $levels = array(), $ids = array())
	{
		return self::$store->get_messages($date_from, $date_to, $limit, $offset, $search, $levels, $ids);
	}
	
}
