<?php

namespace Mothership\Up\Test;

use Mothership\Up\Up;

class UpTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		chdir(__DIR__);
	}

	public function testUp()
	{
		$up = new Up;

		$this->assertEquals($up->update(), 0);
		
		$this->assertTrue(is_dir(__DIR__ . '/vendor'));
		if (is_dir(__DIR__ . '/vendor')) {
			$this->rrmdir(__DIR__ . '/vendor');
		}
	}

	public function testInstall()
	{
		$up = new Up;

		$this->assertEquals($up->install(), 0);
		
		$this->assertTrue(is_dir(__DIR__ . '/vendor'));
		if (is_dir(__DIR__ . '/vendor')) {
			$this->rrmdir(__DIR__ . '/vendor');
		}
	}

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