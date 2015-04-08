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
	private $_errorLog = [];

	public function writeError($error, $newline = true)
	{
		$log = false;
		// strip <info> tags etc
		$error = preg_replace_callback('/<\/?error>/', function($matches) use (&$log) {
			if (!empty($matches)) {
				$log = true;
			}

			return '';
		}, $error);
		
		if ($log) {
			$this->_errorLog[] = $error;
		}
	}

	/**
	 * {@inheritDoc}
	 * 
	 * returns false to prevent any prompts.
	 */
	public function isInteractive()
	{
		return false;
	}

	/**
	 * Get the error array
	 */
	public function getErrors()
	{
		return $this->_errorLog;
	}

	/**
	 * Gets the last error
	 */
	public function getLastError()
	{
		$err = end($this->_errorLog);
		reset($this->_errorLog);

		return $err;
	}	
}