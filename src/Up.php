<?php

namespace Mothership\Up;

use Composer\Composer;
use Composer\Factory;
use Composer\Installer;
use Composer\Config;
use Composer\Command\CreateProjectCommand;
use Symfony\Component\Console\Input\InputDefinition as SymfonyInputDefinition;
use Symfony\Component\Console\Input\ArrayInput as SymfonyInput;
use Symfony\Component\Console\Input\InputArgument as SymfonyInputArgument;
use Symfony\Component\Console\Input\InputOption as SymfonyInputOption;

/**
 * @author Sam Trangmar-Keates samtkeates@gmail.com
 * 
 * This it the main class to be instansiated when running updates
 */
class Up
{
	/**
	 * An IOInterface for composer
	 * 
	 * @var \Composer\IO\IOInterface
	 */
	protected $_io;

	/**
	 * Composer Factory
	 * 
	 * @var Factory
	 */
	private $_factory;

	/**
	 * The root directory
	 * 
	 * @var string
	 */
	protected $_root = null;

	/**
	 * Composer instance used
	 * 
	 * @var the composer instance
	 */
	private $_composer;

	/**
	 * The installer options, these are set on the installer
	 * after instansiation.
	 * 
	 * @var array
	 */
	private $_installerOptions = [
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
		$this->_root     = getcwd();
		$this->_io       = new Bridge\IO\IO;
		$this->_factory  = new Factory;
	}

	/**
	 * Sets the base directory in which to run
	 * 
	 * @param string the path in which to run commands
	 */
	public function setBaseDir($path)
	{
		if (!is_dir($this->_root)) {
			throw new \LogicException($path . ' is not a valid directory! Cannot use as root');
		}
		$this->_root = $path;

		return $this;
	}

	/**
	 * Update composer dependencies
	 */
	public function update()
	{
		$this->_composer = $this->createComposer();
		$install = Installer::create($this->_io, $this->_composer);
		
		$this->_setInstallerOptions($install);
		$install->setUpdate(true);

		$result = $install->run();

		if ($result !== 0) {
			throw new Exception\ComposerException('Composer update failed: ' . $this->_io->getLastError());
		}

		return $result;
	}

	/**
	 * Install a composer project
	 */
	public function install()
	{
		$this->_composer = $this->createComposer();
		$install = Installer::create($this->_io, $this->_composer);
		
		$this->_setInstallerOptions($install);
		$install->setUpdate(false);

		$result = $install->run();

		if ($result !== 0) {
			throw new Exception\ComposerException('Composer install failed: ' . $this->_io->getLastError());
		}

		return $result;
	}

	/**
	 * Create a project from repo.
	 * 
	 * @param string the package to install
	 */
	public function createProject($package)
	{
		$projectCreator = new CreateProjectCommand;
		$composer = $this->createComposer();

		$input = new SymfonyInput([
				'--prefer-source' => $this->_installerOptions['prefer-source'],
				'--prefer-dist'   => $this->_installerOptions['prefer-dist'],
			],
			$projectCreator->getDefinition()
		);

		$result = $projectCreator->installProject(
			$this->_io,
			$this->_factory->createConfig($this->_io, $this->_root),
			$package,
			$this->_root,
			null,
			'stable',
			false,
			false,
			false,
			null,
			false,
			false,
			false,
			false,
			false,
			false,
			$input
		);

		if ($result !== 0) {
			throw new Exception\ComposerException('Composer failed to create project ' . $package . ': ' . $this->_io->getLastError());
		}

		return $result;
	}

	/**
	 * Get a composer instance.
	 * 
	 * @return Composer
	 */
	public function createComposer()
	{
		return $this->_factory->createComposer($this->_io, null, false, $this->_root);
	}
	
	protected function _setInstallerOptions($install)
	{
		$install
			->setDryRun($this->_installerOptions['dry-run'])
			->setVerbose(false)
			->setPreferSource($this->_installerOptions['prefer-source'])
			->setPreferDist($this->_installerOptions['prefer-dist'])
			->setDevMode($this->_installerOptions['dev-mode'])
			->setDumpAutoloader($this->_installerOptions['dump-autoloader'])
			->setRunScripts($this->_installerOptions['run-scripts'])
			->setOptimizeAutoloader($this->_installerOptions['optimize-autoloader'])
			->setUpdateWhitelist([])
			->setWhitelistDependencies(false)
			->setIgnorePlatformRequirements($this->_installerOptions['ignore-platform-reqs'])
			->setPreferStable($this->_installerOptions['prefer-stable'])
			->setPreferLowest($this->_installerOptions['prefer-lowest'])
		;
	}
}