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

use InvalidArgumentException;

/**
 * Class NumberExpression
 *
 * Represents a decimal or integer number
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NumberExpression extends AbstractStaticExpression
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
    public function toNative()
    {
        return $this->number;
    }
}
