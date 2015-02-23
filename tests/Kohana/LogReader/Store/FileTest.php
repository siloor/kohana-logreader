<?php
/**
 * Test case for LogReader
 * 
 * @package     Kohana/LogReader
 * @group       kohana
 * @group       kohana.logreader
 * @category    Base
 * @author      Milan Magyar <milan.magyar@gmail.com>
 * @copyright   (c) 2014 Milan Magyar
 * @license     MIT
 */
class Kohana_LogReader_Store_FileTest extends Kohana_Unittest_TestCase
{
	protected $store;
	
	protected $test_messages =
"2013-05-01 16:20:47 --- DEBUG: #0 /var/www/domain.com/system/classes/Kohana/Request.php(979): Kohana_HTTP_Exception::factory(404, 'Unable to find ...', Array)
#1 /var/www/domain.com/index.php(118): Kohana_Request->execute()
#2 {main} in /var/www/domain.com/system/classes/Kohana/Request.php:979";
	
	public function setUp()
	{
		parent::setUp();
		
		$this->store = new LogReader_Store_File(array(
			'type' => 'File',
			'path' => realpath(__DIR__ . '/../../../test_data/logs'),
		));
	}
	
	/**
	 * Test for LogReader_Store_File class initialization.
	 *
	 * @test
	 * @covers LogReader_Store_File::__construct
	 */
	public function test_construction()
	{
		$this->assertInstanceOf('LogReader_Store_File', $this->store);
	}
	
	/**
	 * Data provider for test_get_message.
	 *
	 * @return  array
	 */
	public function provider_get_message()
	{
		return array(
			array($this->get_expected_test_message('ERROR', '201305013', '1', '2013-05-01 14:20:47'), '201305013'),
			array(NULL, '201304013'),
		);
	}
	
	/**
	 * Test for getting a log message.
	 * 
	 * @dataProvider provider_get_message
	 * @test
	 * @covers LogReader_Store_File::get_message
	 * @param  mixed   $expected    Expected result.
	 * @param  string  $message_id  Id of the log message.
	 */
	public function test_get_message($expected, $message_id)
	{
		$this->assertSame($expected, $this->store->get_message($message_id));
	}
	
	/**
	 * Returns expected message.
	 * 
	 * @param  string  $type     The type of the log message.
	 * @param  string  $id       The id of the log message.
	 * @param  string  $test_id  The id of the test log message.
	 * @param  string  $date     The date of the log message.
	 *
	 * @return  array
	 */
	protected function get_expected_test_message($type, $id, $test_id, $date)
	{
		$date = strtotime($date);
		
		if ($type === 'DEBUG') {
			return array(
				'id' => $id,
				'raw' => date('Y-m-d H:i:s', $date) . " --- DEBUG: #0 /var/www/domain.com/system/classes/Kohana/Request.php(979): test/debug" . $test_id . " Kohana_HTTP_Exception::factory(404, 'Unable to find ...', Array)",
				'date' => date('Y.m.d.', $date),
				'time' => date('H:i:s', $date),
				'level' => "DEBUG",
				'trace' => array(
					"/var/www/domain.com/system/classes/Kohana/Request.php(979): test/debug" . $test_id . " Kohana_HTTP_Exception::factory(404, 'Unable to find ...', Array)",
					"/var/www/domain.com/index.php(118): Kohana_Request->execute()",
					"{main} in /var/www/domain.com/system/classes/Kohana/Request.php:979",
				),
				'type' => "Debug",
				'message' => "/var/www/domain.com/system/classes/Kohana/Request.php(979): test/debug" . $test_id . " Kohana_HTTP_Exception::factory(404, 'Unable to find ...', Array)",
				'file' => "",
			);
		}
		else if ($type === 'ERROR') {
			return array(
				'id' => $id,
				'raw' => date('Y-m-d H:i:s', $date) . " --- ERROR: HTTP_Exception_404 [ 404 ]: Unable to find a route to match the URI: testuri/test" . $test_id . " ~ SYSPATH/classes/Kohana/HTTP/Exception.php [ 17 ] in /var/www/domain.com/system/classes/Kohana/Request.php:979",
				'date' => date('Y.m.d.', $date),
				'time' => date('H:i:s', $date),
				'level' => "ERROR",
				'trace' => array(),
				'type' => "HTTP_Exception_404 [ 404 ]",
				'message' => "Unable to find a route to match the URI: testuri/test" . $test_id . " ",
				'file' => "SYSPATH/classes/Kohana/HTTP/Exception.php [ 17 ] in /var/www/domain.com/system/classes/Kohana/Request.php:979",
			);
		}
	}
	
	/**
	 * Data provider for test_get_messages.
	 *
	 * @return  array
	 */
	public function provider_get_messages()
	{
		return array(
			// Testing default parameters.
			array(
				array(
					'all_matches' => 9,
					'messages' => array(
						$this->get_expected_test_message('ERROR', '201407123', '8', '2014-07-12 14:20:47'),
						$this->get_expected_test_message('ERROR', '201407103', '7', '2014-07-10 14:20:47'),
						$this->get_expected_test_message('ERROR', '201406208', '6', '2014-06-20 19:20:47'),
						$this->get_expected_test_message('DEBUG', '201406205', '1', '2014-06-20 16:20:47'),
						$this->get_expected_test_message('ERROR', '201406204', '5', '2014-06-20 16:20:47'),
						$this->get_expected_test_message('ERROR', '201406203', '4', '2014-06-20 14:20:47'),
						$this->get_expected_test_message('ERROR', '201305015', '3', '2013-05-01 16:20:47'),
						$this->get_expected_test_message('ERROR', '201305014', '2', '2013-05-01 15:20:47'),
						$this->get_expected_test_message('ERROR', '201305013', '1', '2013-05-01 14:20:47'),
					),
				),
				FALSE, FALSE, 10, 0, NULL, array(), array(), NULL
			),
			// Testing dates.
			array(
				array(
					'all_matches' => 3,
					'messages' => array(
						$this->get_expected_test_message('ERROR', '201305015', '3', '2013-05-01 16:20:47'),
						$this->get_expected_test_message('ERROR', '201305014', '2', '2013-05-01 15:20:47'),
						$this->get_expected_test_message('ERROR', '201305013', '1', '2013-05-01 14:20:47'),
					),
				),
				'2013-05-01', '2013-05-01 16:30:47', 10, 0, NULL, array(), array(), NULL
			),
			array(
				array(
					'all_matches' => 7,
					'messages' => array(
						$this->get_expected_test_message('ERROR', '201406208', '6', '2014-06-20 19:20:47'),
						$this->get_expected_test_message('DEBUG', '201406205', '1', '2014-06-20 16:20:47'),
						$this->get_expected_test_message('ERROR', '201406204', '5', '2014-06-20 16:20:47'),
						$this->get_expected_test_message('ERROR', '201406203', '4', '2014-06-20 14:20:47'),
						$this->get_expected_test_message('ERROR', '201305015', '3', '2013-05-01 16:20:47'),
						$this->get_expected_test_message('ERROR', '201305014', '2', '2013-05-01 15:20:47'),
						$this->get_expected_test_message('ERROR', '201305013', '1', '2013-05-01 14:20:47'),
					),
				),
				'2013-05-01 10:20:47', '2014-06-20 20:20:47', 10, 0, NULL, array(), array(), NULL
			),
			// Testing limit and offset.
			array(
				array(
					'all_matches' => 9,
					'messages' => array(
						$this->get_expected_test_message('ERROR', '201406208', '6', '2014-06-20 19:20:47'),
						$this->get_expected_test_message('DEBUG', '201406205', '1', '2014-06-20 16:20:47'),
					),
				),
				FALSE, FALSE, 2, 2, NULL, array(), array(), NULL
			),
			array(
				array(
					'all_matches' => 9,
					'messages' => array(
						$this->get_expected_test_message('ERROR', '201406208', '6', '2014-06-20 19:20:47'),
					),
				),
				FALSE, FALSE, 1, 2, NULL, array(), array(), NULL
			),
			// Testing search.
			array(
				array(
					'all_matches' => 2,
					'messages' => array(
						$this->get_expected_test_message('ERROR', '201305014', '2', '2013-05-01 15:20:47'),
						$this->get_expected_test_message('ERROR', '201305013', '1', '2013-05-01 14:20:47'),
					),
				),
				FALSE, FALSE, 10, 0, "testuri\/test(1|2)", array(), array(), NULL
			),
			// Testing levels.
			array(
				array(
					'all_matches' => 8,
					'messages' => array(
						$this->get_expected_test_message('ERROR', '201407123', '8', '2014-07-12 14:20:47'),
						$this->get_expected_test_message('ERROR', '201407103', '7', '2014-07-10 14:20:47'),
						$this->get_expected_test_message('ERROR', '201406208', '6', '2014-06-20 19:20:47'),
						$this->get_expected_test_message('ERROR', '201406204', '5', '2014-06-20 16:20:47'),
						$this->get_expected_test_message('ERROR', '201406203', '4', '2014-06-20 14:20:47'),
						$this->get_expected_test_message('ERROR', '201305015', '3', '2013-05-01 16:20:47'),
						$this->get_expected_test_message('ERROR', '201305014', '2', '2013-05-01 15:20:47'),
						$this->get_expected_test_message('ERROR', '201305013', '1', '2013-05-01 14:20:47'),
					),
				),
				FALSE, FALSE, 10, 0, NULL, array('ERROR'), array(), NULL
			),
			// Testing levels with not existing level.
			array(
				array(
					'all_matches' => 0,
					'messages' => array(),
				),
				FALSE, FALSE, 10, 0, NULL, array('NOTEXISTINGLEVEL'), array(), NULL
			),
			// Testing ids.
			array(
				array(
					'all_matches' => 3,
					'messages' => array(
						$this->get_expected_test_message('DEBUG', '201406205', '1', '2014-06-20 16:20:47'),
						$this->get_expected_test_message('ERROR', '201406204', '5', '2014-06-20 16:20:47'),
						$this->get_expected_test_message('ERROR', '201305015', '3', '2013-05-01 16:20:47'),
					),
				),
				FALSE, FALSE, 10, 0, NULL, array(), array('201305015', '201406204', '201406205'), NULL
			),
			// Testing from_id.
			array(
				array(
					'all_matches' => 5,
					'messages' => array(
						$this->get_expected_test_message('ERROR', '201407123', '8', '2014-07-12 14:20:47'),
						$this->get_expected_test_message('ERROR', '201407103', '7', '2014-07-10 14:20:47'),
						$this->get_expected_test_message('ERROR', '201406208', '6', '2014-06-20 19:20:47'),
						$this->get_expected_test_message('DEBUG', '201406205', '1', '2014-06-20 16:20:47'),
						$this->get_expected_test_message('ERROR', '201406204', '5', '2014-06-20 16:20:47'),
					),
				),
				FALSE, FALSE, 10, 0, NULL, array(), array(), '201406203'
			),
			// Testing bad from_id.
			array(
				array(
					'all_matches' => 3,
					'messages' => array(
						$this->get_expected_test_message('ERROR', '201305015', '3', '2013-05-01 16:20:47'),
						$this->get_expected_test_message('ERROR', '201305014', '2', '2013-05-01 15:20:47'),
						$this->get_expected_test_message('ERROR', '201305013', '1', '2013-05-01 14:20:47'),
					),
				),
				'2013-05-01 01:00:00', '2013-05-02 19:00:00', 10, 0, NULL, array(), array(), '201dsdas406203'
			),
			// Testing from_id from future date range.
			array(
				array(
					'all_matches' => 0,
					'messages' => array(),
				),
				'2013-05-01 01:00:00', '2013-05-02 19:00:00', 10, 0, NULL, array(), array(), '201406203'
			),
		);
	}
	
	/**
	 * Test for getting a log message.
	 * 
	 * @dataProvider provider_get_messages
	 * @test
	 * @covers LogReader_Store_File::get_messages
	 * @covers LogReader_Store_File::get_log_files
	 * @covers LogReader_Store_File::get_daily_messages
	 * @covers LogReader_Store_File::log_file_path
	 * @covers LogReader_Store_File::is_message_line
	 * @covers LogReader_Store_File::is_trace_line
	 * @covers LogReader_Store_File::encode_message_id
	 * @covers LogReader_Store_File::decode_message_id
	 * @covers LogReader_Store_File::check_filters
	 * @covers LogReader_Store_File::list_files
	 * @covers LogReader_Store_File::sort_logs
	 * 
	 * @param  mixed   $expected   Expected result.
	 * @param  string  $date_from  Start date of log messages (if not given, it starts with the first log)
	 * @param  string  $date_to    End date of log messages (if not given, it ends with the last log)
	 * @param  int     $limit      Limit
	 * @param  int     $offset     Offset
	 * @param  string  $search     The message filter
	 * @param  array   $levels     The levels filter
	 * @param  array   $ids        The ids filter
	 * @param  string  $from_id    Newer messages from specific id
	 */
	public function test_get_messages($expected, $date_from, $date_to, $limit, $offset, $search, $levels, $ids, $from_id)
	{
		$this->assertSame($expected, $this->store->get_messages($date_from, $date_to, $limit, $offset, $search, $levels, $ids, $from_id));
	}
}
