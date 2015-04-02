<?php

namespace Mothership\Up\Test;

use Mothership\Up\Up;

class UpTest extends \PHPUnit_Framework_TestCase
{
	public function testUp()
	{
		chdir('./test');
		$up = new Up;

		var_dump($up->update());
	}
}