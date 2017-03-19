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

use Combyna\Component\Expression\NumberExpression;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Type\StaticType;
use InvalidArgumentException;

/**
 * Class NumberExpressionNode
 *
 * Represents a decimal or integer number
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NumberExpressionNode extends AbstractStaticExpressionNode
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
    public function getResultType(ValidationContextInterface $validationContext)
    {
        return new StaticType(NumberExpression::class);
    }

    /**
     * {@inheritdoc}
     */
    public function toNative()
    {
        return $this->number;
    }
}
