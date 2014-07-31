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
		// Get maximum number of messages from config
		$limit = LogReader::$config['limit'];
		
		// Get page number from query
		$current_page = (int) $this->request->query('page');
		
		if ($current_page < 1)
		{
			$current_page = 1;
		}

		// Create query string to use the same filters on other pages
		$query_string = '';

		// Get filters from query parameters
		$filters = array();
		$filters['message'] = array('text' => $this->request->query('message'));
		$filters['levels'] = $this->request->query('levels');
		$filters['date-from'] = $this->request->query('date-from');
		$filters['date-to'] = $this->request->query('date-to');
		
		// Validate message filter
		if (!isset($filters['message']['text']) || !is_string($filters['message']['text']))
		{
			$filters['message']['text'] = '';
		}

		$filters['message']['valid'] = @preg_match('/' . $filters['message']['text'] . '/i', NULL) !== FALSE;
		
		$query_string .= '&message=' . $filters['message']['text'];

		// Validate levels filter
		if (isset($filters['levels']) && $filters['levels'] && is_array($filters['levels']))
		{
			foreach ($filters['levels'] as $key => $level)
			{
				if (!in_array($level, LogReader::$levels, TRUE))
				{
					unset($filters['levels'][$key]);
				}
				else
				{
					$query_string .= '&levels[]=' . $level;
				}
			}
			
			unset($key, $level);
		}
		else
		{
			$filters['levels'] = array();
		}
		
		// Validate date parameters
		$filters['date-from'] = strtotime($filters['date-from']);
		$filters['date-to'] = strtotime($filters['date-to']);

		// If date-from and date-to are not given use current date
		if ($filters['date-from'] === FALSE && $filters['date-to'] === FALSE)
		{
			$filters['date-from'] = time();
			$filters['date-to'] = time();
		}
		// If date-from is not given use 1900.01.01.
		else if ($filters['date-from'] === FALSE)
		{
			$filters['date-from'] = strtotime('1900-01-01');
		}
		// If date-to is not given use current date
		else if ($filters['date-to'] === FALSE)
		{
			$filters['date-to'] = time();
		}

		// If date-from is greater than date-to change their values
		if ($filters['date-to'] < $filters['date-from'])
		{
			$date_dummy = $filters['date-to'];

			$filters['date-to'] = $filters['date-from'];

			$filters['date-from'] = $date_dummy;

			unset($date_dummy);
		}

		$filters['date-from'] = date('Y-m-d', $filters['date-from']);
		$filters['date-to'] = date('Y-m-d', $filters['date-to']);

		$query_string .= '&date-from=' . $filters['date-from'];
		$query_string .= '&date-to=' . $filters['date-to'];

		// Create view for the messages page
		$view = View::factory('logreader/index');

		$view->user = $this->user;

		$view->content = View::factory('logreader/messages');

		$view->content->name = 'messages';
		
		$view->content->filters = $filters;

		// Get log messages
		$view->content->messages = LogReader::get_messages(
			$filters['date-from'],
			$filters['date-to'],
			$limit,
			($current_page - 1) * $limit,
			$filters['message']['text'] && $filters['message']['valid'] ? $filters['message']['text'] : NULL,
			$filters['levels'],
			array()
		);
		
		$view->content->all_matches = $view->content->messages['all_matches'];
		
		$view->content->messages = $view->content->messages['messages'];
		
		$view->content->current_page = $current_page;
		
		$uri = LogReader_URL::base() . "?" . substr($query_string, 1);

		$view->content->pages = LogReader_URL::pager($current_page, ceil($view->content->all_matches / $limit), $uri . "&page=%(page)s", $uri);
		
		$this->response->body($view);
	}

	// About page
	public function action_about()
	{
		// Create view for the about page
		$view = View::factory('logreader/index');
		
		$view->user = $this->user;

		$view->content = View::factory('logreader/about');

		$view->content->name = 'about';
		
		$this->response->body($view);
	}

	// Message page
	public function action_message()
	{
		// Create view for the about page
		$view = View::factory('logreader/index');
		
		$view->user = $this->user;

		$view->content = View::factory('logreader/message');

		$view->content->name = 'message';

		$view->content->message = LogReader::get_message($this->request->param('message'));
		
		$this->response->body($view);
	}
	
	// Serving static files
	public function action_media()
	{
		// Get the file path from the request
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
			// Return a 404 status
			$this->response->status(404);
		}
	}
	
}
