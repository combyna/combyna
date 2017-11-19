<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression;

use Combyna\Component\Bag\StaticListInterface;
use InvalidArgumentException;

/**
 * Interface StaticExpressionFactoryInterface
 *
 * Creates static versions of bag expressions
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StaticExpressionFactory implements StaticExpressionFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function coerce($value)
    {
        if ($value instanceof StaticInterface) {
            // Already a static - nothing to do
            return $value;
        }

        if (is_string($value)) {
            return $this->createTextExpression($value);
        }

        if (is_int($value) || is_float($value)) {
            return $this->createNumberExpression($value);
        }

        throw new InvalidArgumentException(sprintf('Cannot coerce native value of type "%s"', gettype($value)));
    }

    /**
     * {@inheritdoc}
     */
    public function createBooleanExpression($value)
    {
        return new BooleanExpression($value);
    }

    /**
     * {@inheritdoc}
     */
    public function createNumberExpression($number)
    {
        return new NumberExpression($number);
    }

    /**
     * {@inheritdoc}
     */
    public function createStaticDateTimeExpression($year, $month, $day, $hour, $minute, $second, $millisecond)
    {
        return new StaticDateTimeExpression($year, $month, $day, $hour, $minute, $second, $millisecond);
    }

    /**
     * {@inheritdoc}
     */
    public function createStaticDayExpression($year, $month, $day)
    {
        return new StaticDayExpression($year, $month, $day);
    }

    /**
     * {@inheritdoc}
     */
    public function createStaticListExpression(StaticListInterface $elementStaticList)
    {
        return new StaticListExpression($this, $elementStaticList);
    }

    /**
     * {@inheritdoc}
     */
    public function createTextExpression($text)
    {
        return new TextExpression($text);
    }
}
