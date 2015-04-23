<?php

namespace Mothership\Up\Test;

use Mothership\Up\Up;

class UpTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		chdir(__DIR__);
	}

	public function tearDown()
	{
		$vendorRoot = __DIR__ . '/testroot/vendor';
		if (is_dir($vendorRoot)) {
			$this->rrmdir($vendorRoot);
		}

		$vendorRoot = __DIR__ . '/vendor';
		if (is_dir($vendorRoot)) {
			$this->rrmdir($vendorRoot);
		}

		$vendorRoot = __DIR__ . '/testproject';
		if (is_dir($vendorRoot)) {
			$this->rrmdir($vendorRoot);
		}
	}

	public function testUp()
	{
		$up = new Up;
		$this->assertEquals($up->update(), $up);
	}

	public function testInstall()
	{
		$up = new Up;

		$this->assertEquals($up->install(), $up);
	}

	public function testInstallBase()
	{
		$vendorRoot = __DIR__ . '/testroot';
		$up = new Up;
		$up->setBaseDir($vendorRoot);

		$this->assertEquals($up->install(), $up);
		
		$this->assertTrue(is_dir($vendorRoot . '/vendor'));
		$this->assertTrue(is_dir($vendorRoot));
	}

	public function testCreateProject()
	{
		$projectRoot = __DIR__ . '/testproject';
		mkdir($projectRoot);
		$oldDir = getcwd();
		chdir($projectRoot);

		$up = new Up;

		$result = $up
			->setBaseDir($projectRoot . '/mothership')
			->createProject('mothership-ec/mothership')
		;

		$this->assertEquals($result, $up);
		
		$this->assertTrue(is_dir($projectRoot . '/mothership'));
		$this->assertTrue(is_dir($projectRoot . '/mothership/vendor'));
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testCreateProjectThrows()
	{
		$projectRoot = __DIR__ . '/testproject';
		mkdir($projectRoot);
		$oldDir = getcwd();
		chdir($projectRoot);

		$up = new Up;

		$result = $up
			->setBaseDir($projectRoot . '/mothership')
			->createProject(null)
		;
	}

	/**
	 * Recursively remove a directory - used to remove vendor
	 */
	private function rrmdir($dir) { 
		if (is_dir($dir)) { 
			$objects = scandir($dir); 
			foreach ($objects as $object) { 
				if ($object != "." && $object != "..") { 
					if (filetype($dir."/".$object) == "dir") $this->rrmdir($dir."/".$object); else unlink($dir."/".$object); 
				} 
			} 
			reset($objects); 
			rmdir($dir); 
		} 
	} 

}