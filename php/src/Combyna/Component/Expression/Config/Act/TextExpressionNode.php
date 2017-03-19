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

use Combyna\Component\Expression\TextExpression;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Type\StaticType;
use InvalidArgumentException;

/**
 * Class TextExpressionNode
 *
 * Represents a string of characters
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TextExpressionNode extends AbstractStaticExpressionNode
{
    const TYPE = TextExpression::TYPE;

    /**
     * @var string
     */
    private $text;

    /**
     * @param string $text
     */
    public function __construct($text)
    {
        if (!is_string($text)) {
            throw new InvalidArgumentException(
                'TextExpressionNode expects a string, ' . gettype($text) . ' given'
            );
        }

        $this->text = $text;
    }

    /**
     * {@inheritdoc}
     */
    public function getResultType(ValidationContextInterface $validationContext)
    {
        return new StaticType(TextExpression::class);
    }

    /**
     * {@inheritdoc}
     */
    public function toNative()
    {
        return $this->text;
    }
}
