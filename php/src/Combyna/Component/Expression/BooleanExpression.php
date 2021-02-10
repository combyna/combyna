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
 * Class BooleanExpression
 *
 * Represents true or false
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BooleanExpression extends AbstractStaticExpression implements BooleanValueInterface
{
    const TYPE = 'boolean';

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
                'BooleanExpression expects a boolean, ' . gettype($value) . ' given'
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
