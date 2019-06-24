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

use Combyna\Component\Expression\BooleanExpression;
use Combyna\Component\Expression\BooleanValueInterface;
use Combyna\Component\Expression\StaticValueInterface;
use Combyna\Component\Validator\Type\StaticValuedTypeDeterminer;
use InvalidArgumentException;

/**
 * Class BooleanExpressionNode
 *
 * Represents true or false
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BooleanExpressionNode extends AbstractStaticExpressionNode implements BooleanValueInterface
{
    const TYPE = BooleanExpression::TYPE;

    /**
     * @var bool
     */
    private $value;

    /**
     * @param bool $value
     */
    public function __construct($value)
    {
        if (!is_bool($value)) {
            throw new InvalidArgumentException(
                'BooleanExpressionNode expects a boolean, ' . gettype($value) . ' given'
            );
        }

        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function equals(StaticValueInterface $otherValue)
    {
        return $otherValue instanceof BooleanValueInterface &&
            $otherValue->toNative() === $this->toNative();
    }

    /**
     * {@inheritdoc}
     */
    public function getResultTypeDeterminer()
    {
        return new StaticValuedTypeDeterminer(
            BooleanExpression::class,
            $this
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getSummary()
    {
        return $this->value ? 'true' : 'false';
    }

    /**
     * {@inheritdoc}
     */
    public function toNative()
    {
        return $this->value;
    }
}
