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

use Combyna\Component\Expression\BooleanExpression;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Type\StaticType;
use InvalidArgumentException;

/**
 * Class BooleanExpressionNode
 *
 * Represents true or false
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BooleanExpressionNode extends AbstractStaticExpressionNode
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
    public function getResultType(ValidationContextInterface $validationContext)
    {
        return new StaticType(BooleanExpression::class);
    }

    /**
     * {@inheritdoc}
     */
    public function toNative()
    {
        return $this->value;
    }
}
