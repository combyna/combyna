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
interface StaticExpressionFactoryInterface
{
    /**
     * Creates a BooleanExpression
     *
     * @param bool $value
     * @return BooleanExpression
     */
    public function createBooleanExpression($value);

    /**
     * Creates a NumberExpression
     *
     * @param int|float $number
     * @return NumberExpression
     */
    public function createNumberExpression($number);

    /**
     * Creates a StaticDateTimeExpression
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @param int $minute
     * @param int $second
     * @param int $millisecond
     * @return StaticDateTimeExpression
     */
    public function createStaticDateTimeExpression($year, $month, $day, $hour, $minute, $second, $millisecond);

    /**
     * Creates a StaticDayExpression
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @return StaticDayExpression
     */
    public function createStaticDayExpression($year, $month, $day);

    /**
     * Creates a StaticListExpression
     *
     * @param StaticListInterface $elementStaticList
     * @return StaticListExpression
     */
    public function createStaticListExpression(StaticListInterface $elementStaticList);

    /**
     * Creates a TextExpression
     *
     * @param string $text
     * @return TextExpression
     */
    public function createTextExpression($text);
}
