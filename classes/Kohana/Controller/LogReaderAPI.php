<?php defined('SYSPATH') or die('No direct script access.');
/**
 * LogReader
 * 
 * LogReader helps you explore Kohana Log files.
 * 
 * @package     Kohana/LogReader
 * @category    Controllers
 * @author      Milan Magyar <milan.magyar@gmail.com>
 * @copyright   (c) 2014 Milan Magyar
 * @license     MIT
 */
class Kohana_Controller_LogReaderAPI extends LogReader_Controller
{
	// API response
	protected $data;
	
	public function before()
	{
		parent::before();
		
		$this->data = array(
			'errors' => array(),
		);
	}
	
	// Create test message
	public function action_create_test_message()
	{
		if (!LogReader::$config['tester'])
		{
			array_push($this->data['errors'], array('code' => 600, 'text' => 'Tester is set to off!'));
		}
		
		Log::instance()->add(Log::NOTICE, 'Test message created! Client '. Request::$client_ip . ' User-agent ' . Request::$user_agent);
	}
	
	public function after()
	{
		$this->data = array(
			'result' => !$this->data['errors'],
			'data' => $this->data,
		);
		
		$this->response
			->headers('Content-Type', 'application/json; charset=utf-8')
			->body(json_encode($this->data));
		
		parent::after();
	}
	
}
