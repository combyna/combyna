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
 * Interface RootGenericValidationContextInterface
 *
 *
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface RootGenericValidationContextInterface extends GenericValidationContextInterface
{
    /**
     * Throws if any violations have been added to this context, does nothing otherwise
     *
     * @throws ValidationFailureException
     */
    public function throwIfViolated();
}
