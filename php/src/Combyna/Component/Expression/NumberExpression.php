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

use InvalidArgumentException;

/**
 * Class NumberExpression
 *
 * Represents a decimal or integer number
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NumberExpression extends AbstractStaticExpression implements NumberValueInterface
{
    const TYPE = 'number';

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
                'NumberExpression expects a float or int, ' . gettype($number) . ' given'
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
