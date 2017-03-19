<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression;

use Combyna\Component\Bag\StaticListInterface;

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
