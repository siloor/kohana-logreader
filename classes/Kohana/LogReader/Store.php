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
abstract class Kohana_LogReader_Store
{
	/**
	 * LogReader_Store config
	 * 
	 * @var  array
	 */
	protected $config;
	
	/**
	 * Constructor method
	 *
	 * @param  array  $config  LogReader_Store config
	 */
	public function __construct(array $config)
	{
		$this->config = $config;
	}
	
	/**
	 * Returns the log message by Id
	 * 
	 * @param   string  $message_id  Id of the log message
	 * @return  array
	 */
	abstract public function get_message($message_id);

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
	 * @param   string  $from_id    Newer messages from specific id
	 * @return  array   Limited matched messages and the count of matched log messages
	 */
	abstract public function get_messages($date_from = FALSE, $date_to = FALSE, $limit = 10, $offset = 0, $search = NULL, array $levels = array(), array $ids = array(), $from_id = NULL);
	
}
