<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Bag\Exception;

use Exception;

/**
 * Class StaticIsRequiredException
 *
 * Thrown when attempting to fetch the default expression for a static
 * that does not have one specified, making it required.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StaticIsRequiredException extends Exception
{
}
