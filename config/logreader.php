<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
	/**
	 * The following options are available for LogReader
	 *
	 * integer  limit           messages per page
	 * string   path            path to the log files
	 * string   route           route to the LogReader interface
	 * string   static_route    route to LogReader static files
	 * boolean  authentication  authentication required using users
	 * array    users           available users for authentication
	 */
	'limit' => 40,
	'path' => APPPATH . 'logs',
	'route' => 'logreader',
	'static_route' => 'logreader/media',
	'authentication' => FALSE,
	'users' => array(
		/**
		 * The following options are available for users
		 *
		 * string  username
		 * string  password
		 */
		/*array(
			'username' => 'admin',
			'password' => '123456',
		),*/
	),
);
