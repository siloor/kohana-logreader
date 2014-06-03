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
class Kohana_LogReader
{
	/**
	 * LogReader config
	 * 
	 * @var  array
	 */
	public static $config;

	/**
	 * Log message levels
	 * 
	 * @var  array
	 */
	public static $levels = array(
			'WARNING',
			'DEBUG',
			'ERROR',
			'CRITICAL',
			'EMERGENCY',
			'NOTICE',
			'INFO',
		);
	
	/**
	 * Log message styles
	 * 
	 * @var  array
	 */
	public static $styles = array(
			'WARNING' => 'warning',
			'DEBUG' => 'warning',
			'ERROR' => 'danger',
			'CRITICAL' => 'danger',
			'EMERGENCY' => 'danger',
			'NOTICE' => 'info',
			'INFO' => 'primary',
		);
	
	/**
	 * Returns daily log messages
	 * 
	 * @param   string  $date     Date of log messages
	 * @param   int     $limit    Limit
	 * @param   int     $offset   Offset
	 * @param   array   $filters  Filters for messages
	 * @return  array   Limited matched messages and the count of matched log messages
	 * @uses    LogReader::log_file_path()
	 * @uses    LogReader::is_message_line()
	 * @uses    LogReader::is_trace_line()
	 * @uses    LogReader::check_filters()
	 */
	public static function daily_log($date, $limit = 10, $offset = 0, $filters = array())
	{
		$result = array('all_matches' => 0, 'messages' => array());
		
		$file = self::log_file_path($date);
		
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

				if (self::is_message_line($line) && self::check_filters($filters, $line))
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
				else if (self::is_message_line($line))
				{
					$read_next_line = FALSE;
				}

				if ($read_next_line)
				{
					if (self::is_message_line($line))
					{
						preg_match("/(.*) --- ([A-Z]*): ([^:]*):? ([^~]*)~? (.*)/", $line, $matches);
						
						if ($matches)
						{
							$log = array();

							$log['raw'] = $line;
							$log['date'] = date('Y.m.d.', strtotime($date));
							$log['time'] = date('H:i:s', strtotime($matches[1]));
							$log['level'] = $matches[2];
							$log['style'] = isset(self::$styles[$log['level']]) ? self::$styles[$log['level']] : 'default';
							$log['trace'] = array();
							$log['type'] = $matches[3];
							$log['message'] = $matches[4];
							$log['file'] = $matches[5];
							
							if (self::is_trace_line($log['type']))
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
					else if (self::is_trace_line($line) && $result['messages'])
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
	 * Returns path to the daily log file
	 * 
	 * @param   string  $date  Date of log messages
	 * @return  string
	 */
	public static function log_file_path($date)
	{
		$date = strtotime($date);
		
		if ($date === FALSE) return FALSE;
		
		return realpath(self::$config['path']) . DIRECTORY_SEPARATOR . date('Y', $date) . DIRECTORY_SEPARATOR . date('m', $date) . DIRECTORY_SEPARATOR . date('d', $date) . EXT;
	}
	
	/**
	 * Returns true if the given line is a log message, false otherwise
	 * 
	 * @param   string  $line  Line of text
	 * @return  boolean
	 */
	public static function is_message_line($line)
	{
		return ($line && strpos($line, '#') !== 0 && strpos($line, '<?php') !== 0);
	}
	
	/**
	 * Returns true if the given line is a log message trace, false otherwise
	 * 
	 * @param   string  $line  Line of text
	 * @return  boolean
	 */
	public static function is_trace_line($line)
	{
		return ($line && strpos($line, '#') === 0);
	}
	
	/**
	 * Returns true if the message matched the filters
	 * 
	 * @param   array   $filters  Filters to match
	 * @param   string  $message  Log message
	 * @return  boolean
	 */
	public static function check_filters($filters, $message)
	{
		if ($filters['levels'])
		{
			$level_found = FALSE;

			foreach ($filters['levels'] as $level)
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

		if ($filters['message']['text'] && $filters['message']['valid'])
		{
			if (!preg_match('/' . $filters['message']['text'] . '/i', $message))
			{
				return FALSE;
			}
		}

		return TRUE;
	}
	
	/**
	 * Returns log messages
	 * 
	 * @param   string  $date_from  Start date of log messages (if not given, it starts with the first log)
	 * @param   string  $date_to    End date of log messages (if not given, it ends with the last log)
	 * @param   int     $limit      Limit
	 * @param   int     $offset     Offset
	 * @param   array   $filters    Filters for messages
	 * @return  array   Limited matched messages and the count of matched log messages
	 */
	public static function logs($date_from = FALSE, $date_to = FALSE, $limit = 10, $offset = 0, $filters = array())
	{
		$date_from = strtotime($date_from);
		$date_to = strtotime($date_to);
		
		function list_files($dir)
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
							$files[$entry] = list_files($dir . '/' . $entry);
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
		
		$files = list_files(realpath(self::$config['path']) . DIRECTORY_SEPARATOR);
		
		$logs = array();
		
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

								if (($date_from && $date < $date_from) || ($date_to && $date > $date_to))
								{
									continue;
								}

								array_push($logs, array(
									'year' => (int) $year_name,
									'month' => (int) $month_name,
									'day' => (int) $day,
									'date' => date('Y-m-d', $date)
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
		
		function sort_logs($a, $b)
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
		
		usort($logs, 'sort_logs');
		
		$result = array('all_matches' => 0, 'messages' => array());

		foreach ($logs as $log)
		{
			$new_limit = $limit - count($result['messages']);

			if ($new_limit < 0)
			{
				$new_limit = 0;
			}

			$new_offset = $offset - $result['all_matches'];
			
			if ($new_offset < 0)
			{
				$new_offset = 0;
			}

			$daily_log = self::daily_log($log['date'], $new_limit, $new_offset, $filters);
			
			$result['all_matches'] += $daily_log['all_matches'];
			
			$result['messages'] = array_merge($result['messages'], $daily_log['messages']);

			unset($daily_log, $new_offset, $new_limit);
		}

		unset($log);
		
		return $result;
	}
	
}
