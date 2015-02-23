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
class Kohana_LogReaderTest extends Kohana_Unittest_TestCase
{
	protected $logreader;
	
	protected $config;
	
	protected $store;
	
	public function setUp()
	{
		parent::setUp();
		
		$this->config = $this->getMockBuilder('LogReader_Config')
			->setConstructorArgs(array(array()))
			->getMock();
		
		$this->config
			->expects($this->any())
			->method('get_store')
			->will($this->returnValue(array(
				'type' => 'File',
				'path' => APPPATH . 'logs',
			)));
		
		$this->store = $this->getMockBuilder('LogReader_Store')
			->setConstructorArgs(array($this->config->get_store()))
			->getMockForAbstractClass();
		
		$this->logreader = new LogReader($this->config, $this->store);
	}

	/**
	 * Test for LogReader class initialization.
	 *
	 * @test
	 * @covers LogReader::__construct
	 */
	public function test_construction()
	{
		$this->assertInstanceOf('LogReader', $this->logreader);
	}
	
	/**
	 * Test for getting log levels.
	 * 
	 * @test
	 * @covers LogReader::get_levels
	 */
	public function test_get_levels()
	{
		$this->assertSame(
			array('WARNING', 'DEBUG', 'ERROR', 'CRITICAL', 'EMERGENCY', 'NOTICE', 'INFO'),
			$this->logreader->get_levels()
		);
	}
	
	/**
	 * Data provider for test_get_user_by_username_and_password.
	 *
	 * @return  array
	 */
	public function provider_get_user_by_username_and_password()
	{
		return array(
			array(NULL, 'badusername', 'badpassword'),
			array(NULL, 'admin', 'badpassword'),
			array(
				array(
					'username' => 'admin',
					'password' => '123456',
				),
				'admin',
				'123456',
			),
		);
	}
	
	/**
	 * Test for user authentication.
	 * 
	 * @dataProvider provider_get_user_by_username_and_password
	 * @test
	 * @covers LogReader::get_user_by_username_and_password
	 * @param  mixed   $expected  Expected result.
	 * @param  string  $username  Username of the user.
	 * @param  string  $password  Password of the user.
	 */
	public function test_get_user_by_username_and_password($expected, $username, $password)
	{
		$this->config
			->expects($this->any())
			->method('get_users')
			->will($this->returnValue(array(
				array(
					'username' => 'admin',
					'password' => '123456',
				),
			)));
		
		$this->assertSame(
			$expected,
			$this->logreader->get_user_by_username_and_password($username, $password)
		);
	}
	
	/**
	 * Data provider for test_create_filters
	 *
	 * @return  array
	 */
	public function provider_create_filters()
	{
		return array(
			array(
				array(
					'message' => array(
						'text' => '',
						'valid' => TRUE,
					),
					'levels' => array(),
					'date-from' => date('Y-m-d 00:00:00', time()),
					'date-to' => date('Y-m-d H:i:s', strtotime(date('Y-m-d 00:00:00', time()) . ' +1 day')),
					'limit' => 40,
					'query_string' => '',
				),
				NULL, array(), NULL, NULL, 0
			),
			array(
				array(
					'message' => array(
						'text' => 'search text',
						'valid' => TRUE,
					),
					'levels' => array('DEBUG'),
					'date-from' => '2014-09-02 00:00:00',
					'date-to' => '2014-09-03 00:00:00',
					'limit' => 50,
					'query_string' => 'limit=50&message=search text&levels[]=DEBUG&date-from=2014-09-02 00:00:00&date-to=2014-09-03 00:00:00',
				),
				'search text', array('DEBUG'), '2014-09-03', '2014-09-02', 50
			),
			array(
				array(
					'message' => array(
						'text' => 'bad / search text',
						'valid' => FALSE,
					),
					'levels' => array('DEBUG'),
					'date-from' => date('Y-m-d 00:00:00', time()),
					'date-to' => date('Y-m-d H:i:s', strtotime(date('Y-m-d 00:00:00', time()) . ' +1 day')),
					'limit' => 50,
					'query_string' => 'limit=50&message=bad / search text&levels[]=DEBUG',
				),
				'bad / search text', array('DEBUG', 'BADLEVEL'), 'baddate', 'baddate', 50
			),
			array(
				array(
					'message' => array(
						'text' => 'search text',
						'valid' => TRUE,
					),
					'levels' => array('DEBUG'),
					'date-from' => '2014-09-02 00:00:00',
					'date-to' => date('Y-m-d H:i:s', strtotime(date('Y-m-d 00:00:00', time()) . ' +1 day')),
					'limit' => 50,
					'query_string' => 'limit=50&message=search text&levels[]=DEBUG&date-from=2014-09-02 00:00:00',
				),
				'search text', array('DEBUG'), '2014-09-02', NULL, 50
			),
			array(
				array(
					'message' => array(
						'text' => 'search text',
						'valid' => TRUE,
					),
					'levels' => array('DEBUG'),
					'date-from' => '1980-01-01 00:00:00',
					'date-to' => '2014-09-02 00:00:00',
					'limit' => 50,
					'query_string' => 'limit=50&message=search text&levels[]=DEBUG&date-to=2014-09-02 00:00:00',
				),
				'search text', array('DEBUG'), NULL, '2014-09-02', 50
			),
		);
	}
	
	/**
	 * Test for user authentication.
	 * 
	 * @dataProvider provider_create_filters
	 * @test
	 * @covers LogReader::create_filters
	 * @param  mixed   $expected   Expected result.
	 * @param  string  $message    The message filter
	 * @param  array   $levels     The levels filter
	 * @param  string  $date_from  Start date of log messages (if not given, it starts with the first log)
	 * @param  string  $date_to    End date of log messages (if not given, it ends with the last log)
	 * @param  int     $limit      Limit for messages
	 */
	public function test_create_filters($expected, $message, array $levels, $date_from, $date_to, $limit)
	{
		$this->config
			->expects($this->any())
			->method('get_message_limit')
			->will($this->returnValue(40));
		
		$result = $this->logreader->create_filters($message, $levels, $date_from, $date_to, $limit);
		
		$this->assertSame(
			$expected,
			$result
		);
	}
	
	/**
	 * Data provider for test_get_message
	 *
	 * @return  array
	 */
	public function provider_get_message()
	{
		$test_message = array(
			'id' => '2015020783',
			'raw' => '2015-02-07 18:51:10 --- DEBUG: #0 [internal function]: Kohana_Core::shutdown_handler()',
			'date' => '2015.02.07.',
			'time' => '18:51:10',
			'level' => 'DEBUG',
			'trace' => 
			array(
				'[internal function]: Kohana_Core::shutdown_handler()',
				'{main} in file:line',
			),
			'type' => 'Debug',
			'message' => '[internal function]: Kohana_Core::shutdown_handler()',
			'file' => '',
		);
		
		return array(
			array(
				array_merge($test_message, array('style' => 'warning')),
				$test_message,
			),
			array(
				array_merge($test_message, array('level' => 'TYPENOTEXISTS', 'style' => 'default')),
				array_merge($test_message, array('level' => 'TYPENOTEXISTS')),
			),
		);
	}
	
	/**
	 * Test for getting a message.
	 * 
	 * @dataProvider provider_get_message
	 * @test
	 * @covers LogReader::get_message
	 * @param  mixed  $expected   Expected result.
	 * @param  mixed  $message    The returned message from the LogReader_Store.
	 */
	public function test_get_message($expected, $message)
	{
		$this->store
			->expects($this->any())
			->method('get_message')
			->with('2015020783')
			->will($this->returnValue($message));
		
		$this->assertSame(
			$expected,
			$this->logreader->get_message('2015020783')
		);
	}
	
	/**
	 * Test for getting messages.
	 * 
	 * @test
	 * @covers LogReader::get_messages
	 */
	public function test_get_messages()
	{
		$messages = array(
			'all_matches' => 36,
			'messages' => array(
				array(
					'id' => '2015020792',
					'raw' => '2015-02-07 19:43:28 --- DEBUG: #0 [internal function]: Kohana_Core::shutdown_handler()',
					'date' => '2015.02.07.',
					'time' => '19:43:28',
					'level' => 'DEBUG',
					'trace' => array(),
					'type' => 'Debug',
					'message' => '[internal function]: Kohana_Core::shutdown_handler()',
					'file' => '',
				),
				array(
					'id' => '2015020791',
					'raw' => '2015-02-07 19:43:28 --- CRITICAL: ErrorException [ 1 ]: Call to undefined method Mock_LogReader_Store_f48583a1::method() ~ MODPATH\logreader\tests\Kohana\LogReaderTest.php [ 244 ] in file:line',
					'date' => '2015.02.07.',
					'time' => '19:43:28',
					'level' => 'CRITICAL',
					'trace' => array(),
					'type' => 'ErrorException [ 1 ]',
					'message' => 'Call to undefined method Mock_LogReader_Store_f48583a1::method() ',
					'file' => 'MODPATH\logreader\tests\Kohana\LogReaderTest.php [ 244 ] in file:line',
				),
			),
		);
		
		$expected = $messages;
		
		$expected['messages'][0]['style'] = 'warning';
		$expected['messages'][1]['style'] = 'danger';
		
		$this->store
			->expects($this->any())
			->method('get_messages')
			->with()
			->will($this->returnValue($messages));
		
		$this->assertSame(
			$expected,
			$this->logreader->get_messages()
		);
	}
}
