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
		if ($this->logreader_config->is_tester_available())
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
		$current_page = (int) $this->request->query('page');
		$message = $this->request->query('message');
		$levels = $this->request->query('levels');
		$date_from = $this->request->query('date-from');
		$date_to = $this->request->query('date-to');
		$limit = $this->request->query('limit');
		$from_id = $this->request->query('last_message_id');
		$all_matches_before_id = (int) $this->request->query('all_matches_before_id');
		
		if ($current_page < 1)
		{
			$current_page = 1;
		}
		
		if (!is_array($levels))
		{
			$levels = array();
		}
		
		$all_matches_before_id = $from_id ? $all_matches_before_id : 0;
		
		$filters = $this->logreader->create_filters($message, $levels, $date_from, $date_to, $limit);
		$filters_for_autorefresh = $this->logreader->create_filters($message, $levels, $date_from, NULL, $limit);

		$view = View::factory('logreader/messages');

		$view->levels = $this->logreader->get_levels();
		
		$view->filters = $filters;

		$view->auto_refresh_time = $this->logreader_config->get_auto_refresh_interval();

		$offset = ($current_page - 1) * $filters['limit'];
		
		$view->messages = $this->logreader->get_messages(
			$filters['date-from'],
			$filters['date-to'],
			$filters['limit'],
			$offset,
			$filters['message']['text'] && $filters['message']['valid'] ? $filters['message']['text'] : NULL,
			$filters['levels'],
			array(),
			$from_id
		);
		
		$view->all_matches = $view->messages['all_matches'];
		$view->all_matches_before_id = $view->all_matches - $offset;
		
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
