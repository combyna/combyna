<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\App\Exception;

use Exception;

/**
 * Class SignalDispatchFailedException
 *
 * Thrown when an attempt to dispatch a signal via the public App API fails for some reason.
 *
 * This should never be raised when the signal is raised by the app itself (eg. with a SignalInstruction)
 * as any issues should have been caught at validation time.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalDispatchFailedException extends Exception
{
}
