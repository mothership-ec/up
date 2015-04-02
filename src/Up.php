<?php

namespace Mothership\Up;

use Composer\Composer;
use Composer\Factory;
use Composer\Installer;

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
	 * @var Composer
	 */
	protected $_composer;

	/**
	 * An IOInterface for composer
	 * 
	 * @var \Composer\IO\IOInterface
	 */
	protected $_io;

	/**
	 * The root directory
	 * @var string
	 */
	private $_root;

	private $_options = [
		'dry-run'              => false,
		'prefer-source'        => false,
		'prefer-dist'          => true,
		'dev-mode'             => false,
		'dump-autoloader'      => true,
		'run-scripts'          => true,
		'optimize-autoloader'  => true,
		'ignore-platform-reqs' => false,
		'prefer-stable'        => true,
		'prefer-lowest'        => false,
	];

	public function __construct()
	{
		$this->_io = new Bridge\IO\IO;
		$this->_composer = Factory::create($this->_io, null, false);
	}

	public function setComposerRoot($root)
	{
		$this->_root = $root;
	}

	/**
	 * Update composer dependencies
	 */
	public function update()
	{
		$install = Installer::create($this->_io, $this->_composer);
		
		$this->_setInstallerOptions($install);
		$install->setUpdate(true);

		return $install->run();
	}

	/**
	 * Install a composer project
	 */
	public function install()
	{
		$install = Installer::create($this->_io, $this->_composer);
		
		$this->_setInstallerOptions($install);
		$install->setUpdate(false);

		return $install->run();
	}

	protected function _setInstallerOptions($install)
	{
		$install
			->setDryRun($this->_options['dry-run'])
			->setVerbose(false)
			->setPreferSource($this->_options['prefer-source'])
			->setPreferDist($this->_options['prefer-dist'])
			->setDevMode($this->_options['dev-mode'])
			->setDumpAutoloader($this->_options['dump-autoloader'])
			->setRunScripts($this->_options['run-scripts'])
			->setOptimizeAutoloader($this->_options['optimize-autoloader'])
			->setUpdateWhitelist([])
			->setWhitelistDependencies(false)
			->setIgnorePlatformRequirements($this->_options['ignore-platform-reqs'])
			->setPreferStable($this->_options['prefer-stable'])
			->setPreferLowest($this->_options['prefer-lowest'])
		;
	} 
}