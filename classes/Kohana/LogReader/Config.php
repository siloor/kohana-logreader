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
class Kohana_LogReader_Config
{
	/**
	 * LogReader config parameters
	 * 
	 * @var  $config  array
	 */
	protected $config;
	
	/**
	 * Constructor method
	 *
	 * @param  array  $config  LogReader config parameters
	 */
	public function __construct(array $config)
	{
		$this->config = $config;
	}
	
	/**
	 * Returns LogReader store.
	 * 
	 * @return  array
	 */
	public function get_store()
	{
		return $this->config['store'];
	}
	
	/**
	 * Returns LogReader route.
	 * 
	 * @return  string
	 */
	public function get_route()
	{
		return $this->config['route'];
	}
	
	/**
	 * Returns LogReader static route.
	 * 
	 * @return  string
	 */
	public function get_static_route()
	{
		return $this->config['static_route'];
	}
	
	/**
	 * Returns true, if tester is available.
	 * 
	 * @return  boolean
	 */
	public function is_tester_available()
	{
		return $this->config['tester'];
	}
	
	/**
	 * Returns true, if authentication is required.
	 * 
	 * @return  boolean
	 */
	public function is_authentication_required()
	{
		return $this->config['authentication'];
	}
	
	/**
	 * Returns user data by the given username and password.
	 * 
	 * @return  array
	 */
	public function get_users()
	{
		return $this->config['users'];
	}
	
	/**
	 * Returns LogReader auto refresh interval.
	 * 
	 * @return  int
	 */
	public function get_auto_refresh_interval()
	{
		return $this->config['auto_refresh_interval'];
	}
	
	/**
	 * Returns LogReader message limit.
	 * 
	 * @return  int
	 */
	public function get_message_limit()
	{
		return $this->config['limit'];
	}
	
}
