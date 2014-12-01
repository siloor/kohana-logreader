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
		if ($this->logreader->is_tester_available())
		{
			Log::instance()->add(Log::NOTICE, 'Test message created! Client '. Request::$client_ip . ' User-agent ' . Request::$user_agent);
		}
		else
		{
			array_push($this->data['errors'], array('code' => 600, 'text' => 'Tester is set to off!'));
		}
	}
	
	// Messages page
	public function action_messages()
	{
		// Get page number from query
		$current_page = (int) $this->request->query('page');
		
		if ($current_page < 1)
		{
			$current_page = 1;
		}
		
		$filters = $this->logreader->create_filters(
			$this->request->query('message'),
			$this->request->query('levels'),
			$this->request->query('date-from'),
			$this->request->query('date-to'),
			$this->request->query('limit')
		);
		
		$from_id = $this->request->query('last_message_id');
		
		$from_id = isset($from_id) ? $from_id : NULL;
		
		$all_matches_before_id = $from_id ? (int) $this->request->query('all_matches_before_id') : 0;
		
		$filters_for_autorefresh = $this->logreader->create_filters(
			$this->request->query('message'),
			$this->request->query('levels'),
			$this->request->query('date-from'),
			NULL,
			$this->request->query('limit')
		);

		// Create view for the messages page
		$view = View::factory('logreader/messages');

		$view->levels = $this->logreader->get_levels();
		
		$view->filters = $filters;

		$view->auto_refresh_time = $this->logreader->get_auto_refresh_interval();

		// Get log messages
		$view->messages = $this->logreader->get_messages(
			$filters['date-from'],
			$filters['date-to'],
			$filters['limit'],
			($current_page - 1) * $filters['limit'],
			$filters['message']['text'] && $filters['message']['valid'] ? $filters['message']['text'] : NULL,
			$filters['levels'],
			array(),
			$from_id
		);
		
		$view->all_matches = $view->messages['all_matches'];
		
		$view->messages = $view->messages['messages'];
		
		$view->current_page = $current_page;
		
		$view->auto_refresh_url = LogReader_URL::api_base() . 'messages/?' . $filters_for_autorefresh['query_string'];
		
		$uri = LogReader_URL::base() . "?" . $filters['query_string'];

		$view->pages = LogReader_URL::pager($current_page, ceil(($view->all_matches + $all_matches_before_id) / $filters['limit']), $uri . "&page=%(page)s", $uri);
		
		$this->data['html'] = (string) $view;
		$this->data['all_matches'] = $view->all_matches;
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
