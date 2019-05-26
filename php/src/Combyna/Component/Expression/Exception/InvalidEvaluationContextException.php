<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Exception;

use Exception;

/**
 * Class InvalidEvaluationContextException
 *
 * Thrown when an attempt is made to evaluate something in a NullRootEvaluationContext.
 * Should never normally be thrown, either at validation or evaluation time,
 * as validation should detect any "impure" expression terms first.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class InvalidEvaluationContextException extends Exception
{
}
