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
class LogReader_Store_SQLExample extends LogReader_Store
{
	/**
	 * Returns the log message by Id
	 * 
	 * @param   string  $message_id  Id of the log message
	 * @return  array
	 */
	public function get_message($message_id)
	{
		$query =
			"SELECT
				id, raw, date, time, level, trace, type, message, file
			FROM
				messages
			WHERE
				id = :id
			LIMIT 1;";
		
		$result = DB::query(Database::SELECT, $query)
			->parameters(array(
				':id' => $message_id,
			))
			->execute($this->config['db_name'])
			->as_array();
		
		if (!$result) return NULL;
		
		$result = $result[0];
		
		return $result;
	}

	/**
	 * Returns log messages
	 * 
	 * @param   string  $date_from  Start date of log messages (if not given, it starts with the first log)
	 * @param   string  $date_to    End date of log messages (if not given, it ends with the last log)
	 * @param   int     $limit      Limit
	 * @param   int     $offset     Offset
	 * @param   string  $search     The message filter
	 * @param   array   $levels     The levels filter
	 * @param   array   $ids        The ids filter
	 * @param   string  $from_id    Newer messages from specific id
	 * @return  array   Limited matched messages and the count of matched log messages
	 */
	public function get_messages($date_from = FALSE, $date_to = FALSE, $limit = 10, $offset = 0, $search = NULL, $levels = array(), $ids = array(), $from_id = NULL)
	{
		$result = array(
			'all_matches' => 0, 'messages' => array()
		);
		
		$parameters = array(
			':limit' => $limit,
			':offset' => $offset,
		);
		
		$where = array();
		
		if ($date_from)
		{
			array_push($where, 'date > :date_from');
			
			$parameters[':date_from'] = $date_from;
		}
		
		if ($date_to)
		{
			array_push($where, 'date < :date_to');
			
			$parameters[':date_to'] = $date_to;
		}
		
		if ($search)
		{
			array_push($where, 'message LIKE :search');
			
			$parameters[':search'] = $search;
		}
		
		if ($levels)
		{
			$levels_for_sql = array();
			
			for ($i = 0; $i < count($levels); $i++)
			{
				array_push($levels_for_sql, ':level' . $i);
				
				$parameters[':level' . $i] = $levels[$i];
			}
			
			array_push($where, 'level IN (' . implode(',', $levels_for_sql) . ')');
		}
		
		if ($ids)
		{
			$ids_for_sql = array();
			
			for ($g = 0; $g < count($ids); $g++)
			{
				array_push($ids_for_sql, ':id' . $g);
				
				$parameters[':id' . $g] = $ids[$g];
			}
			
			array_push($where, 'id IN (' . implode(',', $ids_for_sql) . ')');
		}
		
		if ($from_id)
		{
			array_push($where, 'id > :from_id');
			
			$parameters[':from_id'] = $from_id;
		}
		
		$where = $where ? 'WHERE ' . implode(' AND ', $where) : '';
		
		$count_query =
			"SELECT
				COUNT(id) AS count
			FROM
				messages
			$where
			LIMIT :limit
			OFFSET :offset;";
		
		$count_result = DB::query(Database::SELECT, $count_query)
			->parameters($parameters)
			->execute($this->config['db_name'])
			->as_array();
		
		if (!$count_result) return $result;
		
		$result['all_matches'] = (int) $result[0]['count'];
		
		if (!$result['all_matches']) return $result;
		
		$messages_query =
			"SELECT
				id, raw, date, time, level, trace, type, message, file
			FROM
				messages
			$where
			LIMIT :limit
			OFFSET :offset;";
		
		$messages_result = DB::query(Database::SELECT, $messages_query)
			->parameters($parameters)
			->execute($this->config['db_name'])
			->as_array();
		
		if (!$messages_result) return $result;
		
		$result['messages'] = $messages_result;
		
		unset($message);
		
		return $result;
	}
	
}
