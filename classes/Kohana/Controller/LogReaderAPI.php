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
		if (LogReader::$config['tester'])
		{
			$location = '';
			
			$ch = curl_init();
			
			curl_setopt($ch, CURLOPT_URL, 'http://ip-api.com/json/' . Request::$client_ip);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			
			$ip_infos = curl_exec($ch);
			
			curl_close($ch);
			
			if ($ip_infos && ($ip_infos = json_decode($ip_infos, TRUE)) && $ip_infos['status'] === 'success')
			{
				$location = $ip_infos['country'] . '/' . $ip_infos['regionName'];
			}
			
			Log::instance()->add(Log::NOTICE, 'Test message created! Client '. Request::$client_ip . ' Location ' . $location . ' User-agent ' . Request::$user_agent);
		}
		else
		{
			array_push($this->data['errors'], array('code' => 600, 'text' => 'Tester is set to off!'));
		}
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
