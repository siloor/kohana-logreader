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
	// Authenticated user
	protected $user = NULL;
	
	public function before()
	{
		parent::before();
		
		// Authentication if required
		if (LogReader::is_authentication_required())
		{
			// Use HTTP basic authentication
			if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']))
			{
				$this->user = LogReader::get_user_by_username_and_password($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
			}
			
			// Set Authentication required response if needed
			if ($this->user === NULL)
			{
				throw HTTP_Exception::factory(401)->authenticate('Basic realm="Authentication required"');
			}
		}
	}
	
}
