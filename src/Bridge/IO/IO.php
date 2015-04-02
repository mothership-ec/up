<?php

namespace Up\Bridge\IO;

use Composer\IO\NullIO;

/**
 * @author Sam Trangmar-Keates samtkeates@gmail.com
 * 
 * This class extends NullIO as we don't want anything to write to in/out, HOWEVER
 * we may wish to throw some exceptions based on in/output.
 */
class IO extends NullIO
{}