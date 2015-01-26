<?php defined('SYSPATH') or die('No direct script access.');

$logreader_config = new LogReader_Config(Kohana::$config->load('logreader'));

LogReader_URL::$static_route = $logreader_config->get_static_route();

// Set route to LogReader static files if static route is not a remote url
if (!Valid::url($logreader_config->get_static_route()))
{
	Route::set('logreader/media', $logreader_config->get_static_route() . '(/<file>)', array('file' => '.+'))
		->defaults(array(
			'controller' => 'LogReader',
			'action'     => 'media',
		));
}

// Set route to the LogReader API
Route::set('logreader/api', $logreader_config->get_route() . '/api(/<action>)')
	->defaults(array(
		'controller' => 'LogReaderAPI',
		'action' => 'index'
	));

// Set route to the LogReader interface to a specific message
Route::set('logreader/message', $logreader_config->get_route() . '/message/<message>', array('message' => '[0-9]+'))
	->defaults(array(
		'controller' => 'LogReader',
		'action' => 'message'
	));

// Set route to the LogReader interface
Route::set('logreader', $logreader_config->get_route() . '(/<action>)', array('action' => 'about|index'))
	->defaults(array(
		'controller' => 'LogReader',
		'action' => 'index'
	));
