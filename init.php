<?php defined('SYSPATH') or die('No direct script access.');

// Load LogReader config
LogReader::$config = Kohana::$config->load('logreader');

// Set route to the LogReader interface
Route::set('logreader', LogReader::$config['route'] . '(/<action>)')
	->defaults(array(
		'controller' => 'LogReader',
		'action' => 'index'
	));

// Set route to LogReader static files
Route::set('logreader/media', LogReader::$config['static_route'] . '(/<file>)', array('file' => '.+'))
	->defaults(array(
		'controller' => 'LogReader',
		'action'     => 'media',
	));

