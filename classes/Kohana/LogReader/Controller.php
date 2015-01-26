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
class Kohana_LogReader_Controller extends Kohana_Controller
{
	/**
	 * Autheticated user object.
	 * 
	 * @var  array
	 */
	protected $user = NULL;
	
	/**
	 * LogReader object.
	 * 
	 * @var  LogReader
	 */
	protected $logreader;
	
	/**
	 * LogReader config object.
	 * 
	 * @var  LogReader_Config
	 */
	protected $logreader_config;
	
	public function before()
	{
		parent::before();
		
		$this->logreader_config = new LogReader_Config(Kohana::$config->load('logreader'));
		
		$store = $this->logreader_config->get_store();
		
		$store_class = 'LogReader_Store_' . $store['type'];
		
		$logreader_store = new $store_class($store);
		
		$this->logreader = new LogReader($this->logreader_config, $logreader_store);
		
		// Authentication if required
		if ($this->logreader_config->is_authentication_required())
		{
			// Use HTTP basic authentication
			if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']))
			{
				$this->user = $this->logreader->get_user_by_username_and_password($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
			}
			
			// Set Authentication required response if needed
			if ($this->user === NULL)
			{
				throw HTTP_Exception::factory(401)->authenticate('Basic realm="Authentication required"');
			}
		}
	}
	
}
