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
 * Class TextExpression
 *
 * Represents a string of characters
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TextExpression extends AbstractStaticExpression
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
    public function toNative()
    {
        return $this->text;
    }
}
