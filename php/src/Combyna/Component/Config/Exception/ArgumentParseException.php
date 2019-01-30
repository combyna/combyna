<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Config\Exception;

use Exception;

/**
 * Class ArgumentParseException
 *
 * Thrown when a set of arguments is parsed from a config array using
 * a list of parameter specifications, but one of the following occurs:
 *
 * - A required argument is missing
 * - Extra arguments were passed, but no ExtraParameter was specified
 * - An optional or required argument was passed but with the wrong type
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ArgumentParseException extends Exception
{
}
