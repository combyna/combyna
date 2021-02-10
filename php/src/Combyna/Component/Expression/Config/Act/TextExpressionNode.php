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

use Combyna\Component\Expression\StaticValueInterface;
use Combyna\Component\Expression\TextExpression;
use Combyna\Component\Expression\TextValueInterface;
use Combyna\Component\Validator\Type\StaticValuedTypeDeterminer;
use InvalidArgumentException;

/**
 * Class TextExpressionNode
 *
 * Represents a string of characters
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TextExpressionNode extends AbstractStaticExpressionNode implements TextValueInterface
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
    public function equals(StaticValueInterface $otherValue)
    {
        return $otherValue instanceof TextValueInterface &&
            $otherValue->toNative() === $this->toNative();
    }

    /**
     * {@inheritdoc}
     */
    public function getResultTypeDeterminer()
    {
        return new StaticValuedTypeDeterminer(TextExpression::class, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function getSummary()
    {
        // Add an ellipsis to show that we had to truncate the text when applicable
        return strlen($this->text) > self::MAX_SUMMARY_LENGTH ?
            substr($this->text, 0, self::MAX_SUMMARY_LENGTH) . '...' :
            $this->text;
    }

    /**
     * {@inheritdoc}
     */
    public function toNative()
    {
        return $this->text;
    }
}
