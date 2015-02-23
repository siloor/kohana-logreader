<?php defined('SYSPATH') or die('No direct script access.');
/**
 * LogReader
 * 
 * LogReader helps you explore Kohana Log files.
 * 
 * @package     Kohana/LogReader
 * @category    Base
 * @author      Milan Magyar <milan.magyar@gmail.com>
 * @copyright   (c) 2014 Milan Magyar
 * @license     MIT
 */
class Kohana_LogReader_Store_File extends LogReader_Store
{
	/**
	 * Returns the log message by Id.
	 * 
	 * @param   string  $message_id  Id of the log message.
	 * @return  array
	 */
	public function get_message($message_id)
	{
		$message = $this->decode_message_id($message_id);

		if (!$message) return NULL;

		$message = $this->get_messages($message['date'], date('Y-m-d', strtotime($message['date'] . ' +1 day')), 1, 0, NULL, array(), array($this->encode_message_id($message['date'], $message['line_number'])));

		return $message['messages'] ? $message['messages'][0] : NULL;
	}

	/**
	 * Returns log messages.
	 * 
	 * @param   string  $date_from  Start date of log messages (if not given, it starts with the first log).
	 * @param   string  $date_to    End date of log messages (if not given, it ends with the last log).
	 * @param   int     $limit      Limit.
	 * @param   int     $offset     Offset.
	 * @param   string  $search     The message filter.
	 * @param   array   $levels     The levels filter.
	 * @param   array   $ids        The ids filter.
	 * @param   string  $from_id    Newer messages from specific id.
	 * @return  array   Limited matched messages and the count of matched log messages.
	 */
	public function get_messages($date_from = NULL, $date_to = NULL, $limit = 10, $offset = 0, $search = NULL, array $levels = array(), array $ids = array(), $from_id = NULL)
	{
		$result = array('all_matches' => 0, 'messages' => array());
		
		$date_from = strtotime($date_from);
		$date_to = strtotime($date_to);
		
		if ($date_from === FALSE)
		{
			$date_from = NULL;
		}
		
		if ($date_to === FALSE)
		{
			$date_to = NULL;
		}

		if ($from_id)
		{
			$from_id_date = $this->decode_message_id($from_id);

			$from_id_date = strtotime($from_id_date['date']);

			if ($date_from && $date_from < $from_id_date)
			{
				$date_from = $from_id_date;
			}

			if ($date_to && $date_to < $from_id_date)
			{
				return $result;
			}
		}
		
		$daily_logs = $this->get_log_files($date_from, $date_to);

		foreach ($daily_logs as $daily_log)
		{
			$new_limit = $limit - count($result['messages']);

			$new_offset = $offset - $result['all_matches'];
			
			if ($new_offset < 0)
			{
				$new_offset = 0;
			}

			$daily_messages = $this->get_daily_messages($daily_log['date'], $date_from, $date_to, $new_limit, $new_offset, $search, $levels, $ids, $from_id);
			
			$result['all_matches'] += $daily_messages['all_matches'];
			
			$result['messages'] = array_merge($result['messages'], $daily_messages['messages']);

			unset($daily_messages, $new_offset, $new_limit);
		}

		unset($daily_log);
		
		return $result;
	}
	
	/**
	 * Returns available log files.
	 * 
	 * @param   int  $date_from  Start date of log messages (if not given, it starts with the first log).
	 * @param   int  $date_to    End date of log messages (if not given, it ends with the last log).
	 * @return  array
	 */
	protected function get_log_files($date_from = NULL, $date_to = NULL)
	{
		$day_from = $date_from ? strtotime(date('Y-m-d', $date_from)) : NULL;
		$day_to = $date_to ? strtotime(date('Y-m-d', $date_to)) : NULL;
		
		$logs = array();
		
		$files = $this->list_files(realpath($this->config['path']) . DIRECTORY_SEPARATOR);
		
		foreach ($files as $year_name => $year)
		{
			if (is_array($year) && $year)
			{
				foreach ($year as $month_name => $month)
				{
					if (is_array($month) && $month)
					{
						foreach ($month as $day)
						{
							if (!is_array($day) && $day)
							{
								$date = strtotime(((int) $year_name) . '-' . ((int) $month_name) . '-' . ((int) $day));

								if (($day_from && $date < $day_from) || ($day_to && $date > $day_to))
								{
									continue;
								}

								array_push($logs, array(
									'year' => (int) $year_name,
									'month' => (int) $month_name,
									'day' => (int) $day,
									'date' => date('Y-m-d', $date),
								));
							}
						}

						unset($day, $date);
					}
				}

				unset($month_name, $month);
			}
		}

		unset($year_name, $year);
		
		usort($logs, array('LogReader_Store_File', 'sort_logs'));
		
		return $logs;
	}

	/**
	 * Returns daily log messages.
	 * 
	 * @param   string  $date       Date of log messages.
	 * @param   int     $date_from  Start date of log messages (if not given, it starts with the first log).
	 * @param   int     $date_to    End date of log messages (if not given, it ends with the last log).
	 * @param   int     $limit      Limit.
	 * @param   int     $offset     Offset.
	 * @param   string  $search     The message filter.
	 * @param   array   $levels     The levels filter.
	 * @param   array   $ids        The ids filter.
	 * @param   string  $from_id    Newer messages from specific id.
	 * @return  array   Limited matched messages and the count of matched log messages.
	 */
	protected function get_daily_messages($date, $date_from = NULL, $date_to = NULL, $limit = 10, $offset = 0, $search = NULL, array $levels = array(), array $ids = array(), $from_id = NULL)
	{
		$result = array('all_matches' => 0, 'messages' => array());
		
		if ($date_from && $date_from <= strtotime($date))
		{
			$date_from = NULL;
		}
		
		if ($date_to && $date_to >= strtotime($date . ' +1 day'))
		{
			$date_to = NULL;
		}
		
		$file = $this->log_file_path($date);
		
		if ($file === FALSE) return FALSE;
		
		$matched_lines = array();
		
		$handle = @fopen($file, 'r');

		if ($handle)
		{
			$cursor = 0;

			while (($line = fgets($handle, 4096)) !== FALSE)
			{
				$cursor++;

				$line = trim($line);

				if ($this->is_message_line($line) && $this->check_filters($this->encode_message_id($date, $cursor), $line, $date_from, $date_to, $search, $levels, $ids, $from_id))
				{
					$result['all_matches']++;
					
					if ($limit)
					{
						array_push($matched_lines, $cursor);

						if (count($matched_lines) > ($limit + $offset))
						{
							array_shift($matched_lines);
						}
					}
				}
			}
			
			fclose($handle);

			unset($cursor, $line);
		}
		
		unset($handle);
		
		if (count($matched_lines) > $offset)
		{
			$matched_lines = array_slice($matched_lines, 0, count($matched_lines) - $offset);
		}
		else
		{
			$matched_lines = array();
		}
		
		if (!$matched_lines) return $result;
		
		$handle = @fopen($file, 'r');
		
		if ($handle)
		{
			$cursor = 0;

			$read_next_line = FALSE;

			while (($line = fgets($handle, 4096)) !== FALSE)
			{
				$cursor++;

				$line = trim($line);
				
				if (in_array($cursor, $matched_lines))
				{
					$read_next_line = TRUE;
				}
				else if ($this->is_message_line($line))
				{
					$read_next_line = FALSE;
				}

				if ($read_next_line)
				{
					if ($this->is_message_line($line))
					{
						preg_match("/(.*) --- ([A-Z]*): ([^:]*):? ([^~]*)~? (.*)/", $line, $matches);
						
						if ($matches)
						{
							$log = array();

							$log['id'] = $this->encode_message_id($date, $cursor);
							$log['raw'] = $line;
							$log['date'] = date('Y.m.d.', strtotime($date));
							$log['time'] = date('H:i:s', strtotime($matches[1]));
							$log['level'] = $matches[2];
							$log['trace'] = array();
							$log['type'] = $matches[3];
							$log['message'] = $matches[4];
							$log['file'] = $matches[5];
							
							if ($this->is_trace_line($log['type']))
							{
								$log['message'] = preg_replace('/.*#\d* /', '', $line);
								$log['file'] = '';
								$log['type'] = 'Debug';
								
								array_push($log['trace'], $log['message']);
							}
							
							array_push($result['messages'], $log);
						}

						unset($matches);
					}
					else if ($this->is_trace_line($line) && $result['messages'])
					{
						array_push($result['messages'][count($result['messages']) - 1]['trace'], preg_replace('/#\d* /', '', $line));
					}
				}
			}
			
			fclose($handle);

			unset($cursor, $read_next_line, $line);
		}
		
		unset($handle);

		$result['messages'] = array_reverse($result['messages']);
		
		return $result;
	}
	
	/**
	 * Returns path to the daily log file.
	 * 
	 * @param   string  $date  Date of log messages.
	 * @return  string
	 */
	protected function log_file_path($date)
	{
		$date = strtotime($date);
		
		if ($date === FALSE) return FALSE;
		
		return realpath($this->config['path']) . DIRECTORY_SEPARATOR . date('Y', $date) . DIRECTORY_SEPARATOR . date('m', $date) . DIRECTORY_SEPARATOR . date('d', $date) . EXT;
	}
	
	/**
	 * Returns true if the given line is a log message, false otherwise.
	 * 
	 * @param   string  $line  Line of text.
	 * @return  boolean
	 */
	protected function is_message_line($line)
	{
		return ($line && strpos($line, '#') !== 0 && strpos($line, '<?php') !== 0);
	}
	
	/**
	 * Returns true if the given line is a log message trace, false otherwise.
	 * 
	 * @param   string  $line  Line of text.
	 * @return  boolean
	 */
	protected function is_trace_line($line)
	{
		return ($line && strpos($line, '#') === 0);
	}
	
	/**
	 * Create message id from date and line number.
	 * 
	 * @param   string  $date         Date of the message.
	 * @param   int     $line_number  Line number of the message.
	 * @return  string
	 */
	protected function encode_message_id($date, $line_number)
	{
		$date = strtotime($date);

		$line_number = (int) $line_number;

		if ($date === FALSE || !$line_number) return FALSE;

		return date('Ymd', $date) . $line_number;
	}
	
	/**
	 * Decode message id to date and line_number.
	 * 
	 * @param   string  $message  Id of the message.
	 * @return  array
	 */
	protected function decode_message_id($message)
	{
		$message = array(
			'date' => strtotime(substr($message, 0, 8)),
			'line_number' => (int) substr($message, 8),
		);

		if ($message['date'] !== FALSE && $message['line_number'])
		{
			$message['date'] = date('Y-m-d', $message['date']);

			return $message;
		}

		return FALSE;
	}
	
	/**
	 * Returns true if the message matched the filters.
	 * 
	 * @param   string  $id         Log message id.
	 * @param   string  $message    Log message.
	 * @param   int     $date_from  Start date of log messages (if not given, it starts with the first log).
	 * @param   int     $date_to    End date of log messages (if not given, it ends with the last log).
	 * @param   string  $search     The message filter.
	 * @param   array   $levels     The levels filter.
	 * @param   array   $ids        The ids filter.
	 * @param   string  $from_id    Newer messages from specific id.
	 * @return  boolean
	 */
	protected function check_filters($id, $message, $date_from = NULL, $date_to = NULL, $search, array $levels, array $ids, $from_id)
	{
		if ($date_from || $date_to)
		{
			if (!preg_match("/(.*) ---/", $message, $matches) || !isset($matches[1])) return FALSE;
			
			$date = strtotime($matches[1]);
			
			unset($matches);
			
			if ($date_from && $date < $date_from) return FALSE;
			
			if ($date_to && $date > $date_to) return FALSE;
		}
		
		if ($levels)
		{
			$level_found = FALSE;

			foreach ($levels as $level)
			{
				if (preg_match('/--- ' . $level . '/i', $message))
				{
					$level_found = TRUE;

					break;
				}
			}

			unset($level);

			if (!$level_found) return FALSE;
		}

		if ($ids)
		{
			if (!in_array($id, $ids, TRUE)) return FALSE;
		}
		
		if ($from_id)
		{
			$decoded_id = $this->decode_message_id($id);
			
			$decoded_from_id = $this->decode_message_id($from_id);
			
			if ($decoded_id['date'] === $decoded_from_id['date'])
			{
				if ($decoded_id['line_number'] <= $decoded_from_id['line_number'])
				{
					return FALSE;
				}
			}
			else if (strtotime($decoded_id['date']) < strtotime($decoded_from_id['date']))
			{
				return FALSE;
			}
		}

		if ($search)
		{
			if (!preg_match('/' . $search . '/i', $message)) return FALSE;
		}

		return TRUE;
	}
	
	/**
	 * Returns the list of files and directories in a folder.
	 * 
	 * @param   string  $dir  The path to the root directory.
	 * @return  array
	 */
	protected function list_files($dir)
	{
		$files = array();
		
		if ($handle = opendir($dir))
		{
			while (($entry = readdir($handle)) !== FALSE)
			{
				if ($entry !== '.' && $entry !== '..')
				{
					if (is_dir($dir . '/' . $entry))
					{
						$files[$entry] = $this->list_files($dir . '/' . $entry);
					}
					else
					{
						array_push($files, $entry);
					}
				}
			}
			
			closedir($handle);

			unset($handle, $entry);
		}
		
		return $files;
	}
	
	/**
	 * Sorts log files.
	 * 
	 * @param   array  $a  The first log file.
	 * @param   array  $b  The second log file.
	 * @return  int
	 */
	protected static function sort_logs($a, $b)
	{
		if ($a['year'] === $b['year'])
		{
			if ($a['month'] === $b['month'])
			{
				if ($a['day'] === $b['day']) return 0;
				
				return ($a['day'] < $b['day']) ? 1 : -1;
			}
			
			return ($a['month'] < $b['month']) ? 1 : -1;
		}
		
		return ($a['year'] < $b['year']) ? 1 : -1;
	}
	
}
