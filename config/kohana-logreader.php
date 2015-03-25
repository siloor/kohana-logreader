<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
	/**
	 * The following options are available for LogReader
	 *
	 * integer  limit                  messages per page
	 * integer  auto_refresh_interval  the interval for auto refresh in seconds
	 * string   store                  store of log messages
	 * string   route                  route to the LogReader interface
	 * string   static_route           route to LogReader static files, it could be a remote url
	 * boolean  tester                 show log message tester button
	 * boolean  authentication         authentication required using users
	 * array    users                  available users for authentication
	 */
	'limit' => 40,
	'auto_refresh_interval' => 5,
	'store' => array(
		/**
		 * Configuration options for the LogReader store
		 *
		 * string  type  The type of the LogReader store.
		 * string  path  Path to the log files
		 */
		'type' => 'File',
		'path' => APPPATH . 'logs',
		/**
		 * You can create your own Store for your log solution. There is an example store
		 * called SQLExample in the application directory. The configuration for the
		 * example could look like this.
		 *
		 * 'type'    => 'SQLExample',
		 * 'db_name' => 'default',
		 */
	),
	'route' => 'logreader',
	'static_route' => 'logreader/media',
	'tester' => FALSE,
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
