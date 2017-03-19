<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Act;

use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class AbstractStaticExpressionNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
abstract class AbstractStaticExpressionNode implements StaticNodeInterface
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return static::TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        // Nothing to validate, a static expression should have no operands as it is already evaluated
    }
}
