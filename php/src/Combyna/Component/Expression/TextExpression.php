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
 * Class TextExpression
 *
 * Represents a string of characters
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TextExpression extends AbstractStaticExpression implements TextValueInterface
{
    const TYPE = 'text';

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
                'TextExpression expects a string, ' . gettype($text) . ' given'
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
