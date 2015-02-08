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
class Kohana_Controller_LogReader extends LogReader_Controller
{
	// Messages page
	public function action_index()
	{
		$current_page = (int) $this->request->query('page');
		$message = $this->request->query('message');
		$levels = $this->request->query('levels');
		$date_from = $this->request->query('date-from');
		$date_to = $this->request->query('date-to');
		$limit = $this->request->query('limit');
		
		if ($current_page < 1)
		{
			$current_page = 1;
		}
		
		if (!is_array($levels))
		{
			$levels = array();
		}
		
		$filters = $this->logreader->create_filters($message, $levels, $date_from, $date_to, $limit);
		$filters_for_autorefresh = $this->logreader->create_filters($message, $levels, $date_from, NULL, $limit);
		
		$view = View::factory('logreader/index');
		
		$view->stylesheets = array(
			LogReader_URL::static_base() . 'css/messages.css',
		);
		
		$view->javascripts = array(
			LogReader_URL::static_base() . 'js/messages.js',
		);

		$view->user = $this->user;
		
		$view->is_tester_available = $this->logreader_config->is_tester_available();

		$view->content = View::factory('logreader/messages');

		$view->content->name = 'messages';

		$view->content->levels = $this->logreader->get_levels();

		$view->content->filters = $filters;

		$view->content->auto_refresh_time = $this->logreader_config->get_auto_refresh_interval();

		$offset = ($current_page - 1) * $filters['limit'];
		
		$view->content->messages = $this->logreader->get_messages(
			$filters['date-from'],
			$filters['date-to'],
			$filters['limit'],
			$offset,
			$filters['message']['text'] && $filters['message']['valid'] ? $filters['message']['text'] : NULL,
			$filters['levels'],
			array(),
			NULL
		);
		
		$view->content->all_matches = $view->content->messages['all_matches'];
		$view->content->all_matches_before_id = $view->content->all_matches - $offset;
		
		$view->content->messages = $view->content->messages['messages'];
		
		$view->content->current_page = $current_page;
		
		$view->content->auto_refresh_url = LogReader_URL::api_base() . 'messages/?' . $filters_for_autorefresh['query_string'];
		
		$uri = LogReader_URL::base() . "?" . $filters['query_string'];

		$view->content->pages = LogReader_URL::pager($current_page, ceil($view->content->all_matches / $filters['limit']), $uri . "&page=%(page)s", $uri);
		
		$this->response->body($view);
	}

	// About page
	public function action_about()
	{
		$view = View::factory('logreader/index');
		
		$view->user = $this->user;

		$view->content = View::factory('logreader/about');

		$view->content->name = 'about';
		
		$this->response->body($view);
	}

	// Message page
	public function action_message()
	{
		$view = View::factory('logreader/index');

		$view->user = $this->user;

		$view->content = View::factory('logreader/message');

		$view->content->name = 'message';

		$view->content->message = $this->logreader->get_message($this->request->param('message'));
		
		$this->response->body($view);
	}
	
	// Serving static files
	public function action_media()
	{
		$file = $this->request->param('file');

		// Find the file extension
		$ext = pathinfo($file, PATHINFO_EXTENSION);

		// Remove the extension from the filename
		$file = substr($file, 0, -(strlen($ext) + 1));

		if ($file = Kohana::find_file('media/logreader', $file, $ext))
		{
			// Check if the browser sent an "if-none-match: <etag>" header, and tell if the file hasn't changed
			$this->check_cache(sha1($this->request->uri()) . filemtime($file));
			
			// Send the file content as the response
			$this->response->body(file_get_contents($file));

			// Set the proper headers to allow caching
			$this->response->headers(array(
				'content-type' => File::mime_by_ext($ext),
				'last-modified' => date('r', filemtime($file)),
			));
		}
		else
		{
			$this->response->status(404);
		}
	}
	
}
