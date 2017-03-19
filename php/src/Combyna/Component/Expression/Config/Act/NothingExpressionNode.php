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

use Combyna\Component\Expression\NothingExpression;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Type\StaticType;

/**
 * Class NothingExpressionNode
 *
 * Represents a missing or unspecified value (rarely used)
 *
 * @author Dan Phillimore <dan@ovms.co>
 * @TODO: Try to remove this concept if possible - could do something (for Entity attrs)
 *        like have a guard expression for checking whether the attr is set before reading it
 *        (should only be required for nullable attrs to avoid complicating)
 */
class NothingExpressionNode extends AbstractStaticExpressionNode
{
    const TYPE = NothingExpression::TYPE;

    /**
     * {@inheritdoc}
     */
    public function getResultType(ValidationContextInterface $validationContext)
    {
        return new StaticType(NothingExpression::class);
    }

    /**
     * {@inheritdoc}
     */
    public function toNative()
    {
        return null;
    }
}
