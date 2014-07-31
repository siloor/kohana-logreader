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
class Kohana_LogReader_Store
{
	/**
	 * LogReader_Store config
	 * 
	 * @var  array
	 */
	protected $config;
	
	/**
	 * Constructor method
	 *
	 * @param  array  $config  LogReader_Store config
	 */
	public function __construct($config)
	{
		$this->config = $config;
	}
	
}
