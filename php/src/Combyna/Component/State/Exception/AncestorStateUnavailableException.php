<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\State\Exception;

use Exception;

/**
 * Interface AncestorStateUnavailableException
 *
 * Thrown when a state path only contains the current state, but the parent is requested,
 * or an ancestor state is requested when there are not enough states available
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AncestorStateUnavailableException extends Exception
{
}
