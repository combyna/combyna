<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator\Context;

use Combyna\Component\Type\TypeInterface;

/**
 * Interface ScopeValidationContextInterface
 *
 *
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ScopeValidationContextInterface extends ValidationContextInterface
{
    /**
     * Defines a variable that will exist in this context at run-time.
     * A violation will be added if any parent context already defines a variable
     * with the specified name, to prevent any confusion caused by shadowing
     *
     * @param string $variableName
     * @param TypeInterface $type
     */
    public function defineVariable($variableName, TypeInterface $type);
}
