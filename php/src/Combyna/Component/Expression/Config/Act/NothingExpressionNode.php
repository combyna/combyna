<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Act;

use Combyna\Component\Expression\NothingExpression;
use Combyna\Component\Expression\NothingValueInterface;
use Combyna\Component\Expression\StaticValueInterface;
use Combyna\Component\Validator\Type\StaticValuedTypeDeterminer;

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
class NothingExpressionNode extends AbstractStaticExpressionNode implements NothingValueInterface
{
    const TYPE = NothingExpression::TYPE;

    /**
     * {@inheritdoc}
     */
    public function equals(StaticValueInterface $otherValue)
    {
        return $otherValue instanceof NothingValueInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function getResultTypeDeterminer()
    {
        return new StaticValuedTypeDeterminer(NothingExpression::class, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function getSummary()
    {
        return '(none)';
    }

    /**
     * {@inheritdoc}
     */
    public function toNative()
    {
        return null;
    }
}
