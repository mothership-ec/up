<?php

namespace Mothership\Up\Bridge\IO;

use Composer\IO\NullIO;

/**
 * @author Sam Trangmar-Keates samtkeates@gmail.com
 * 
 * This class extends NullIO as we don't want anything to write to in/out, HOWEVER
 * we may wish to throw some exceptions based on in/output.
 */
class IO extends NullIO
{
	public function writeError($error, $newline = true)
	{
		// strip <info> tags etc
		$error = preg_replace_callback('/<\/?error>/', function($matches) {
			foreach ($matches as $match) {
				throw new \Up\Exception\ComposerException($error);
			}
		}, $error);

	}	
}