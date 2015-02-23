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
class Kohana_LogReader_URLTest extends Kohana_Unittest_TestCase
{
	public function setUp()
	{
		parent::setUp();
		
		$this->setEnvironment(array('Kohana::$base_url' => 'http://example.com/'));
	}
	
	/**
	 * Test for base route.
	 * 
	 * @test
	 * @covers LogReader_URL::base
	 */
	public function test_base()
	{
		$config = $this->getMockBuilder('LogReader_Config')
			->setConstructorArgs(array(array()))
			->getMock();
		
		$config
			->expects($this->any())
			->method('get_route')
			->will($this->returnValue('logreader'));
		
		LogReader_URL::set_configuration($config);
		
		$this->assertSame('http://example.com/logreader/', LogReader_URL::base());
	}
	
	/**
	 * Test for api_base route.
	 * 
	 * @test
	 * @covers LogReader_URL::api_base
	 */
	public function test_api_base()
	{
		$config = $this->getMockBuilder('LogReader_Config')
			->setConstructorArgs(array(array()))
			->getMock();
		
		$config
			->expects($this->any())
			->method('get_route')
			->will($this->returnValue('logreader'));
		
		LogReader_URL::set_configuration($config);
		
		$this->assertSame('http://example.com/logreader/api/', LogReader_URL::api_base());
	}
	
	/**
	 * Data provider for test_static_base.
	 *
	 * @return  array
	 */
	public function provider_static_base()
	{
		return array(
			array('http://example.com/logreader/media/', 'logreader/media'),
			array('http://cdn.com/logreader/', 'http://cdn.com/logreader'),
		);
	}

	/**
	 * Test for static_base route.
	 * 
	 * @dataProvider provider_static_base
	 * @test
	 * @covers LogReader_URL::static_base
	 * @covers LogReader_URL::set_configuration
	 * @param  mixed   $expected      Expected result.
	 * @param  string  $static_route  Static route.
	 */
	public function test_static_base($expected, $static_route)
	{
		$config = $this->getMockBuilder('LogReader_Config')
			->setConstructorArgs(array(array()))
			->getMock();
		
		$config
			->expects($this->any())
			->method('get_static_route')
			->will($this->returnValue($static_route));
		
		LogReader_URL::set_configuration($config);
		
		$this->assertSame($expected, LogReader_URL::static_base());
	}
	
	/**
	 * Test for log_message route.
	 * 
	 * @test
	 * @covers LogReader_URL::log_message
	 */
	public function test_log_message()
	{
		$config = $this->getMockBuilder('LogReader_Config')
			->setConstructorArgs(array(array()))
			->getMock();
		
		$config
			->expects($this->any())
			->method('get_route')
			->will($this->returnValue('logreader'));
		
		LogReader_URL::set_configuration($config);
		
		$this->assertSame('http://example.com/logreader/message/dasd23', LogReader_URL::log_message('dasd23'));
	}
	
	/**
	 * Test for logout_url route.
	 * 
	 * @test
	 * @covers LogReader_URL::logout_url
	 */
	public function test_logout_url()
	{
		$config = $this->getMockBuilder('LogReader_Config')
			->setConstructorArgs(array(array()))
			->getMock();
		
		$config
			->expects($this->any())
			->method('get_route')
			->will($this->returnValue('logreader'));
		
		LogReader_URL::set_configuration($config);
		
		$this->assertSame('http://badusername:badpassword@example.com/logreader/', LogReader_URL::logout_url());
	}
	
	/**
	 * Test for string templating.
	 * 
	 * @test
	 * @covers LogReader_URL::str_template
	 */
	public function test_str_template()
	{
		$this->assertSame('http://example.com/?page=13&test=1', LogReader_URL::str_template('http://example.com/?page=%(page)s&test=1', array('page' => 13)));
	}
	
	/**
	 * Data provider for test_page_url.
	 *
	 * @return  array
	 */
	public function provider_page_url()
	{
		return array(
			array('http://example.com/page/14/', 14),
			array('http://example.com/', 1),
		);
	}
	
	/**
	 * Test for getting page url.
	 * 
	 * @dataProvider provider_page_url
	 * @test
	 * @covers LogReader_URL::page_url
	 * @param  mixed   $expected      Expected result.
	 * @param  string  $static_route  Static route.
	 */
	public function test_page_url($expected, $page)
	{
		$this->assertSame($expected, LogReader_URL::page_url($page, 'http://example.com/page/%(page)s/', 'http://example.com/'));
	}
	
	/**
	 * Data provider for test_pager.
	 *
	 * @return  array
	 */
	public function provider_pager()
	{
		return array(
			array(
				array(
					array('title' => 1, 'url' => 'http://example.com/'),
					array('title' => 2, 'url' => 'http://example.com/page/2/'),
					array('title' => 3, 'url' => 'http://example.com/page/3/'),
					array('title' => 4, 'url' => 'http://example.com/page/4/'),
					array('title' => 5, 'url' => 'http://example.com/page/5/'),
					array('title' => 6, 'url' => 'http://example.com/page/6/'),
					array('title' => 7, 'url' => 'http://example.com/page/7/'),
					array('title' => 8, 'url' => 'http://example.com/page/8/'),
					array('title' => 9, 'url' => 'http://example.com/page/9/'),
					array('title' => 10, 'url' => 'http://example.com/page/10/'),
					array('title' => 11, 'url' => 'http://example.com/page/11/'),
					array('title' => '...'),
					array('title' => 13, 'url' => 'http://example.com/page/13/'),
					array('title' => 'next', 'url' => 'http://example.com/page/2/'),
				), 1, 13
			),
			array(
				array(
					array('title' => 'previous', 'url' => 'http://example.com/page/12/'),
					array('title' => 1, 'url' => 'http://example.com/'),
					array('title' => '...'),
					array('title' => 3, 'url' => 'http://example.com/page/3/'),
					array('title' => 4, 'url' => 'http://example.com/page/4/'),
					array('title' => 5, 'url' => 'http://example.com/page/5/'),
					array('title' => 6, 'url' => 'http://example.com/page/6/'),
					array('title' => 7, 'url' => 'http://example.com/page/7/'),
					array('title' => 8, 'url' => 'http://example.com/page/8/'),
					array('title' => 9, 'url' => 'http://example.com/page/9/'),
					array('title' => 10, 'url' => 'http://example.com/page/10/'),
					array('title' => 11, 'url' => 'http://example.com/page/11/'),
					array('title' => 12, 'url' => 'http://example.com/page/12/'),
					array('title' => 13, 'url' => 'http://example.com/page/13/'),
				), 13, 13
			),
			array(
				array(
					array('title' => 'previous', 'url' => 'http://example.com/page/5/'),
					array('title' => 1, 'url' => 'http://example.com/'),
					array('title' => 2, 'url' => 'http://example.com/page/2/'),
					array('title' => 3, 'url' => 'http://example.com/page/3/'),
					array('title' => 4, 'url' => 'http://example.com/page/4/'),
					array('title' => 5, 'url' => 'http://example.com/page/5/'),
					array('title' => 6, 'url' => 'http://example.com/page/6/'),
					array('title' => 7, 'url' => 'http://example.com/page/7/'),
					array('title' => 8, 'url' => 'http://example.com/page/8/'),
					array('title' => 9, 'url' => 'http://example.com/page/9/'),
					array('title' => 10, 'url' => 'http://example.com/page/10/'),
					array('title' => 11, 'url' => 'http://example.com/page/11/'),
					array('title' => '...'),
					array('title' => 13, 'url' => 'http://example.com/page/13/'),
					array('title' => 'next', 'url' => 'http://example.com/page/7/'),
				), 6, 13
			),
			array(
				array(
					array('title' => 1, 'url' => 'http://example.com/'),
				), 1, 1
			),
			array(
				array(), 1, 0
			),
			
			array(
				array(
					array('title' => 'previous', 'url' => 'http://example.com/page/3/'),
					array('title' => 1, 'url' => 'http://example.com/'),
					array('title' => 2, 'url' => 'http://example.com/page/2/'),
					array('title' => 3, 'url' => 'http://example.com/page/3/'),
				), 4, 3
			),
		);
	}
	
	/**
	 * Test for getting page urls.
	 * 
	 * @dataProvider provider_pager
	 * @test
	 * @covers LogReader_URL::pager
	 * @param  mixed   $expected  Expected result.
	 * @param  int     $current   Current page number.
	 * @param  int     $total     Number of pages.
	 */
	public function test_pager($expected, $current, $total)
	{
		$this->assertSame($expected, LogReader_URL::pager($current, $total, 'http://example.com/page/%(page)s/', 'http://example.com/'));
	}
}
