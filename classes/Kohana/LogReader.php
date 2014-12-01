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
	protected static $config;

	/**
	 * LogReader store
	 * 
	 * @var  LogReader_Store
	 */
	protected $store;

	// Log message level constants
	const LEVEL_WARNING   = 'WARNING';
	const LEVEL_DEBUG     = 'DEBUG';
	const LEVEL_ERROR     = 'ERROR';
	const LEVEL_CRITICAL  = 'CRITICAL';
	const LEVEL_EMERGENCY = 'EMERGENCY';
	const LEVEL_NOTICE    = 'NOTICE';
	const LEVEL_INFO      = 'INFO';

	/**
	 * Log message levels
	 * 
	 * @var  array
	 */
	protected $levels = array(
			self::LEVEL_WARNING,
			self::LEVEL_DEBUG,
			self::LEVEL_ERROR,
			self::LEVEL_CRITICAL,
			self::LEVEL_EMERGENCY,
			self::LEVEL_NOTICE,
			self::LEVEL_INFO,
		);
	
	/**
	 * Log message styles
	 * 
	 * @var  array
	 */
	protected $styles = array(
			self::LEVEL_WARNING   => 'warning',
			self::LEVEL_DEBUG     => 'warning',
			self::LEVEL_ERROR     => 'danger',
			self::LEVEL_CRITICAL  => 'danger',
			self::LEVEL_EMERGENCY => 'danger',
			self::LEVEL_NOTICE    => 'info',
			self::LEVEL_INFO      => 'primary',
		);
	
	/**
	 * Constructs the LogReader object.
	 *
	 * @param array Configuration for the reader
	 */
	public function __construct()
	{
		$store_class = 'LogReader_Store_' . static::$config['store']['type'];
		
		$this->store = new $store_class(static::$config['store']);
	}
	
	/**
	 * Sets LogReader configuration.
	 * 
	 * @return  void
	 */
	public static function set_configuration($config)
	{
		static::$config = $config;
	}
	
	/**
	 * Returns LogReader route.
	 * 
	 * @return  string
	 */
	public static function get_route()
	{
		return static::$config['route'];
	}
	
	/**
	 * Returns LogReader static route.
	 * 
	 * @return  string
	 */
	public static function get_static_route()
	{
		return static::$config['static_route'];
	}
	
	/**
	 * Returns LogReader log message levels.
	 * 
	 * @return  array
	 */
	public function get_levels()
	{
		return $this->levels;
	}
	
	/**
	 * Returns true, if tester is available.
	 * 
	 * @return  boolean
	 */
	public function is_tester_available()
	{
		return static::$config['tester'];
	}
	
	/**
	 * Returns true, if authentication is required.
	 * 
	 * @return  boolean
	 */
	public function is_authentication_required()
	{
		return static::$config['authentication'];
	}
	
	/**
	 * Returns user data by the given username and password.
	 * 
	 * @param   string  $username  Username of the user.
	 * @param   string  $password  Password of the user.
	 * @return  array
	 */
	public function get_user_by_username_and_password($username, $password)
	{
		foreach (static::$config['users'] as $user)
		{
			if ($user['username'] === $username && $user['password'] === $password)
			{
				return $user;
			}
		}
		
		return NULL;
	}
	
	/**
	 * Returns LogReader auto refresh interval.
	 * 
	 * @return  int
	 */
	public function get_auto_refresh_interval()
	{
		return static::$config['auto_refresh_interval'];
	}
	
	/**
	 * Validate and extend the given filters.
	 * 
	 * @param   string  $search     The message filter
	 * @param   array   $levels     The levels filter
	 * @param   string  $date_from  Start date of log messages (if not given, it starts with the first log)
	 * @param   string  $date_to    End date of log messages (if not given, it ends with the last log)
	 * @param   int     $limit      Limit for messages
	 * @return  array
	 */
	public function create_filters($message = NULL, $levels = array(), $date_from = NULL, $date_to = NULL, $limit = 0)
	{
		// Use parameters in query string
		$use_in_qs = array();
		
		// Get filters from query parameters
		$filters = array();
		$filters['message'] = array('text' => $message);
		$filters['levels'] = $levels;
		$filters['date-from'] = $date_from;
		$filters['date-to'] = $date_to;
		$filters['limit'] = (int) $limit;
		
		// Create query string to use the same filters on other pages
		$filters['query_string'] = '';
		
		// Get maximum number of messages from config
		$filters['limit'] = $filters['limit'] ? $filters['limit'] : static::$config['limit'];
		
		$use_in_qs['limit'] = $filters['limit'] !== static::$config['limit'];
		
		if ($use_in_qs['limit'])
		{
			$filters['query_string'] .= '&limit=' . $filters['limit'];
		}

		// Validate message filter
		if (!isset($filters['message']['text']) || !is_string($filters['message']['text']))
		{
			$filters['message']['text'] = '';
		}
		
		$use_in_qs['message'] = (bool) $filters['message']['text'];

		$filters['message']['valid'] = @preg_match('/' . $filters['message']['text'] . '/i', NULL) !== FALSE;
		
		if ($use_in_qs['message'])
		{
			$filters['query_string'] .= '&message=' . $filters['message']['text'];
		}

		// Validate levels filter
		if (isset($filters['levels']) && $filters['levels'] && is_array($filters['levels']))
		{
			foreach ($filters['levels'] as $key => $level)
			{
				if (!in_array($level, $this->get_levels(), TRUE))
				{
					unset($filters['levels'][$key]);
				}
				else
				{
					$filters['query_string'] .= '&levels[]=' . $level;
				}
			}
			
			unset($key, $level);
		}
		else
		{
			$filters['levels'] = array();
		}
		
		// Validate date parameters
		$filters['date-from'] = strtotime($filters['date-from']);
		$filters['date-to'] = strtotime($filters['date-to']);
		
		$use_in_qs['date-from'] = $filters['date-from'] !== FALSE;
		$use_in_qs['date-to'] = $filters['date-to'] !== FALSE;

		// If date-from and date-to are not given use current date
		if ($filters['date-from'] === FALSE && $filters['date-to'] === FALSE)
		{
			$filters['date-from'] = time();
			$filters['date-to'] = time();
		}
		// If date-from is not given use 1900.01.01.
		else if ($filters['date-from'] === FALSE)
		{
			$filters['date-from'] = strtotime('1900-01-01');
		}
		// If date-to is not given use current date
		else if ($filters['date-to'] === FALSE)
		{
			$filters['date-to'] = time();
		}

		// If date-from is greater than date-to change their values
		if ($filters['date-to'] < $filters['date-from'])
		{
			$date_dummy = $filters['date-to'];

			$filters['date-to'] = $filters['date-from'];

			$filters['date-from'] = $date_dummy;

			unset($date_dummy);
		}

		$filters['date-from'] = date('Y-m-d', $filters['date-from']);
		$filters['date-to'] = date('Y-m-d', $filters['date-to']);

		if ($use_in_qs['date-from'])
		{
			$filters['query_string'] .= '&date-from=' . $filters['date-from'];
		}
		
		if ($use_in_qs['date-to'])
		{
			$filters['query_string'] .= '&date-to=' . $filters['date-to'];
		}
		
		$filters['query_string'] = substr($filters['query_string'], 1);
		
		return $filters;
	}
	
	/**
	 * Returns the log message by Id.
	 * 
	 * @param   string  $message_id  Id of the log message
	 * @return  array
	 */
	public function get_message($message_id)
	{
		$message = $this->store->get_message($message_id);
		
		if ($message === NULL) return $message;
		
		$message['style'] = isset($this->styles[$message['level']]) ? $this->styles[$message['level']] : 'default';
		
		return $message;
	}
	
	/**
	 * Returns log messages.
	 * 
	 * @param   string  $date_from  Start date of log messages (if not given, it starts with the first log)
	 * @param   string  $date_to    End date of log messages (if not given, it ends with the last log)
	 * @param   int     $limit      Limit
	 * @param   int     $offset     Offset
	 * @param   string  $search     The message filter
	 * @param   array   $levels     The levels filter
	 * @param   array   $ids        The ids filter
	 * @param   array   $from_id    Newer messages from specific id
	 * @return  array   Limited matched messages and the count of matched log messages
	 */
	public function get_messages($date_from = FALSE, $date_to = FALSE, $limit = 10, $offset = 0, $search = NULL, $levels = array(), $ids = array(), $from_id = NULL)
	{
		$messages = $this->store->get_messages($date_from, $date_to, $limit, $offset, $search, $levels, $ids, $from_id);
		
		foreach ($messages['messages'] as &$message)
		{
			$message['style'] = isset($this->styles[$message['level']]) ? $this->styles[$message['level']] : 'default';
		}
		
		unset($message);
		
		return $messages;
	}
	
}
