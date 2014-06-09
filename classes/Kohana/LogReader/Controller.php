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
class Kohana_LogReader_Controller extends Controller
{
	// Authenticated user
	protected $user = NULL;
	
	public function before()
	{
		parent::before();
		
		// Authentication if required
		if (LogReader::$config['authentication'])
		{
			// Use HTTP basic authentication
			if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']))
			{
				// Try to authenticate client from config users
				foreach (LogReader::$config['users'] as $user)
				{
					if ($_SERVER['PHP_AUTH_USER'] === $user['username'] && $_SERVER['PHP_AUTH_PW'] === $user['password'])
					{
						$this->user = $user;

						break;
					}
				}

				unset($user);
			}
			
			// Set Authentication required response if needed
			if ($this->user === NULL)
			{
				throw HTTP_Exception::factory(401)->authenticate('Basic realm="Authentication required"');
			}
		}
	}
	
}
