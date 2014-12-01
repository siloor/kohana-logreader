<?php defined('SYSPATH') or die('No direct script access.');

// Load LogReader config
LogReader::set_configuration(Kohana::$config->load('logreader'));

// Set route to LogReader static files if static route is not a remote url
if (!Valid::url(LogReader::get_static_route()))
{
	Route::set('logreader/media', LogReader::get_static_route() . '(/<file>)', array('file' => '.+'))
		->defaults(array(
			'controller' => 'LogReader',
			'action'     => 'media',
		));
}

// Set route to the LogReader API
Route::set('logreader/api', LogReader::get_route() . '/api(/<action>)')
	->defaults(array(
		'controller' => 'LogReaderAPI',
		'action' => 'index'
	));

// Set route to the LogReader interface to a specific message
Route::set('logreader/message', LogReader::get_route() . '/message/<message>', array('message' => '[0-9]+'))
	->defaults(array(
		'controller' => 'LogReader',
		'action' => 'message'
	));

// Set route to the LogReader interface
Route::set('logreader', LogReader::get_route() . '(/<action>)', array('action' => 'about|index'))
	->defaults(array(
		'controller' => 'LogReader',
		'action' => 'index'
	));
