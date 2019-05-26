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

use Combyna\Component\Expression\NumberExpression;
use Combyna\Component\Expression\NumberValueInterface;
use Combyna\Component\Expression\StaticValueInterface;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Type\ValuedType;
use Combyna\Component\Validator\Type\PresolvedTypeDeterminer;
use InvalidArgumentException;

/**
 * Class NumberExpressionNode
 *
 * Represents a decimal or integer number
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NumberExpressionNode extends AbstractStaticExpressionNode implements NumberValueInterface
{
    const TYPE = NumberExpression::TYPE;

    /**
     * @var float|int
     */
    private $number;

    /**
     * @param float|int $number
     */
    public function __construct($number)
    {
        if (!is_float($number) && !is_int($number)) {
            throw new InvalidArgumentException(
                'NumberExpressionNode expects a float or int, ' . gettype($number) . ' given'
            );
        }

        $this->number = $number;
    }

    /**
     * {@inheritdoc}
     */
    public function equals(StaticValueInterface $otherValue)
    {
        return $otherValue instanceof NumberValueInterface &&
            $otherValue->toNative() === $this->toNative();
    }

    /**
     * {@inheritdoc}
     */
    public function getResultTypeDeterminer()
    {
        return new PresolvedTypeDeterminer(
            new ValuedType(
                new StaticType(NumberExpression::class),
                $this
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getSummary()
    {
        return $this->number;
    }

    /**
     * {@inheritdoc}
     */
    public function toNative()
    {
        return $this->number;
    }
}
