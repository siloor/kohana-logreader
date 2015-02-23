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
class Kohana_LogReader_ConfigTest extends Kohana_Unittest_TestCase
{
	protected $config;
	
	public function setUp()
	{
		parent::setUp();
		
		$this->config = new LogReader_Config(array(
			'limit' => 40,
			'auto_refresh_interval' => 5,
			'store' => array(
				'type' => 'File',
				'path' => APPPATH . 'logs',
			),
			'route' => 'logreader',
			'static_route' => 'logreader/media',
			'tester' => FALSE,
			'authentication' => TRUE,
			'users' => array(
				array(
					'username' => 'admin',
					'password' => '123456',
				),
			),
		));
	}

	/**
	 * Test for LogReader_Config class initialization.
	 *
	 * @test
	 * @covers LogReader_Config::__construct
	 */
	public function test_construction()
	{
		$this->assertInstanceOf('LogReader_Config', $this->config);
	}
	
	/**
	 * Test for getting LogReader store.
	 * 
	 * @test
	 * @covers LogReader_Config::get_store
	 */
	public function test_get_store()
	{
		$this->assertSame(array(
				'type' => 'File',
				'path' => APPPATH . 'logs',
			),
			$this->config->get_store()
		);
	}
	
	/**
	 * Test for getting LogReader route.
	 * 
	 * @test
	 * @covers LogReader_Config::get_route
	 */
	public function test_get_route()
	{
		$this->assertSame('logreader', $this->config->get_route());
	}
	
	/**
	 * Test for getting LogReader static route.
	 * 
	 * @test
	 * @covers LogReader_Config::get_static_route
	 */
	public function test_get_static_route()
	{
		$this->assertSame('logreader/media', $this->config->get_static_route());
	}
	
	/**
	 * Test for getting tester available status.
	 * 
	 * @test
	 * @covers LogReader_Config::is_tester_available
	 */
	public function test_is_tester_available()
	{
		$this->assertSame(FALSE, $this->config->is_tester_available());
	}
	
	/**
	 * Test for getting authentication requirement.
	 * 
	 * @test
	 * @covers LogReader_Config::is_authentication_required
	 */
	public function test_is_authentication_required()
	{
		$this->assertSame(TRUE, $this->config->is_authentication_required());
	}
	
	/**
	 * Test for getting LogReader users.
	 * 
	 * @test
	 * @covers LogReader_Config::get_users
	 */
	public function test_get_users()
	{
		$this->assertSame(array(
				array(
					'username' => 'admin',
					'password' => '123456',
				),
			),
			$this->config->get_users()
		);
	}
	
	/**
	 * Test for getting LogReader auto refresh interval.
	 * 
	 * @test
	 * @covers LogReader_Config::get_auto_refresh_interval
	 */
	public function test_get_auto_refresh_interval()
	{
		$this->assertSame(5, $this->config->get_auto_refresh_interval());
	}
	
	/**
	 * Test for getting LogReader message limit.
	 * 
	 * @test
	 * @covers LogReader_Config::get_message_limit
	 */
	public function test_get_message_limit()
	{
		$this->assertSame(40, $this->config->get_message_limit());
	}
}
