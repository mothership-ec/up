<?php

namespace Up;

use Composer\Command;

/**
 * @author Sam Trangmar-Keates samtkeates@gmail.com
 * 
 * This it the main class to be instansiated when running updates
 */
class Up
{
	/**
	 * Composer instance
	 * 
	 * @var Composer\Composer
	 */
	private $_composer;

	/**
	 * An IOInterface for composer
	 * 
	 * @var \Composer\IO\IOInterface
	 */
	private $_io;

	public function __construct()
	{
		$this->_composer = Factory::create($this->io, null, $disablePlugins);
		$this->_io = new Bridge\IO\IO;
	}

	public function update()
	{

	}
}