<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator\Context;

use Combyna\Component\Validator\Exception\ValidationFailureException;

/**
 * Interface RootValidationContextInterface
 *
 *
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface RootValidationContextInterface extends ValidationContextInterface
{
    /**
     * Throws if any violations have been added to this context, does nothing otherwise
     *
     * @throws ValidationFailureException
     */
    public function throwIfViolated();
}
