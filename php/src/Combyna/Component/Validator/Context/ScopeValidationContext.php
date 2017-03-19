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
 * Class ScopeValidationContext
 *
 *
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ScopeValidationContext extends AbstractSpecificValidationContext
    implements ScopeValidationContextInterface
{
    /**
     * {@inheritdoc}
     */
    public function defineVariable($variableName, TypeInterface $type)
    {
        $this->genericContext->defineVariable($variableName, $type);
    }
}
