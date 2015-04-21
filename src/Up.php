<?php

namespace Mothership\Up;

use Composer\Composer;
use Composer\Factory;
use Composer\Installer;
use Composer\Config;
use Composer\Command\CreateProjectCommand;
use Symfony\Component\Console\Input\ArrayInput as SymfonyInput;

/**
 * @author Sam Trangmar-Keates samtkeates@gmail.com
 * 
 * This it the main class to be instanciated when running updates
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
	 * @var Composer
	 */
	private $_composer;

	/**
	 * The installer options, these are set on the installer
	 * after instanciation.
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
	 * @param string $path       The path in which to run commands
	 * @throws \LogicException   Throws exception if path is not a valid directory
	 *
	 * @return Up
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
		$composer = $this->createComposer();
		$io = $this->_io;

		$function = $this->_wrap(function () use ($composer, $io) {
			$install = Installer::create($io, $composer);

			$this->_setInstallerOptions($install);
			$install->setUpdate(true);

			$result = $install->run();

			if ($result !== 0) {
				throw new Exception\ComposerException('Composer update failed: ' . $this->_io->getLastError());
			}
		});

		$function();

		return $this;
	}

	/**
	 * Install a composer project
	 */
	public function install()
	{
		$composer = $this->createComposer();
		$io = $this->_io;

		$function = $this->_wrap(function () use ($composer, $io) {
			$install = Installer::create($io, $composer);

			$this->_setInstallerOptions($install);
			$install->setUpdate(false);

			$result = $install->run();

			if ($result !== 0) {
				throw new Exception\ComposerException('Composer install failed: ' . $io->getLastError());
			}
		});

		$function();

		return $this;
	}

	/**
	 * Create a project from repo.
	 * 
	 * @param string $package       The package to install
	 *
	 * @return int
	 */
	public function createProject($package)
	{
		$io   = $this->_io;
		$root = $this->_root;

		$function = $this->_wrap(function () use ($package, $io, $root) {
			$projectCreator = new CreateProjectCommand;

			$input = new SymfonyInput([
				'--prefer-source' => $this->_installerOptions['prefer-source'],
				'--prefer-dist' => $this->_installerOptions['prefer-dist'],
			],
				$projectCreator->getDefinition()
			);

			$result = $projectCreator->installProject(
				$this->_io,
				$this->_factory->createConfig($io, $root),
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
				throw new Exception\ComposerException('Composer failed to create project ' . $package . ': ' . $io->getLastError());
			}
		});

		$function();

		return $this;
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

	/**
	 * @param Installer $install
	 */
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

	/**
	 * Wrap a function with a memory_limit check. Set to 1G if below, and then reset after the function has been
	 * called. Returns a new closure so will still need to be run manually.
	 *
	 * @param \Closure $function
	 *
	 * @return \Closure
	 */
	private function _wrap(\Closure $function)
	{
		return function () use ($function) {
			$memory = ini_get('memory_limit');
			switch (substr($memory, -1)) {
				case 'M':
				case 'm':
					$memory = (int) $memory * 1048576;
					break;
				case 'K':
				case 'k':
					$memory = (int) $memory * 1024;
					break;
				case 'G':
				case 'g':
					$memory = (int) $memory * 1073741824;
					break;
				default:
					$memory = (int) $memory;
					break;
			}
			if ($memory < 1073741824) {
				$iniSet = ini_set('memory_limit', 1073741824);
			}

			$function();

			if (isset($iniSet)) {
				ini_set('memory_limit', $iniSet);
			}
		};
	}
}