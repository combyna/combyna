<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Config\Act;

use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Interface ActNodeInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ActNodeInterface
{
    /**
     * Fetches the type of node, eg. `fixed-static-bag-model`
     *
     * @return string
     */
    public function getType();

    /**
     * Checks that all operands/arguments for this node validate recursively and that they will only
     * resolve to the expected types of static expression
     *
     * @param ValidationContextInterface $validationContext
     */
    public function validate(ValidationContextInterface $validationContext);
}
