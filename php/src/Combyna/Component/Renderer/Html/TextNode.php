<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Renderer\Html;

/**
 * Class TextNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TextNode implements HtmlNodeInterface
{
    /**
     * @var string
     */
    private $text;

    /**
     * @param string $text
     */
    public function __construct($text)
    {
        $this->text = $text;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            'type' => 'text',
            'text' => $this->text
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function toGenericArray()
    {
        return [$this->toArray()];
    }

    /**
     * {@inheritdoc}
     */
    public function toHtml()
    {
        return $this->text;
    }
}
