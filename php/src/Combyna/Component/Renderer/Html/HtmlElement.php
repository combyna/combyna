<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Renderer\Html;

use LogicException;

/**
 * Class HtmlElement
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class HtmlElement implements HtmlNodeInterface
{
    /**
     * @var string[]
     */
    private $attributes;

    /**
     * @var HtmlNodeInterface[]
     */
    private $childNodes;

    /**
     * @var string[]
     */
    private static $selfClosingTags = ['img'];

    /**
     * @var string
     */
    private $tagName;

    /**
     * @param string $tagName
     * @param string[] $attributes
     * @param HtmlNodeInterface[] $childNodes
     */
    public function __construct($tagName, array $attributes, array $childNodes = [])
    {
        $this->attributes = $attributes;
        $this->childNodes = $childNodes;
        $this->tagName = $tagName;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $childNodeArrays = [];

        foreach ($this->childNodes as $childNode) {
            $childNodeArrays[] = $childNode->toArray();
        }

        return [
            'type' => 'element',
            'tag' => $this->tagName,
            'attributes' => $this->attributes,
            'children' => $childNodeArrays
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function toHtml()
    {
        $childHtml = '';

        foreach ($this->childNodes as $childNode) {
            $childHtml .= $childNode->toHtml();
        }

        $html = '<' . $this->tagName;

        foreach ($this->attributes as $name => $value) {
            $html .= ' ' . htmlentities($name) . '="' . htmlentities($value) . '"';
        }

        $html .= '>' . $childHtml;

        if (!in_array($this->tagName, self::$selfClosingTags, true)) {
            $html .= '</' . $this->tagName . '>';
        } elseif (count($this->childNodes) > 0) {
            throw new LogicException('<' . $this->tagName . '> elements should have no children');
        }

        return $html;
    }
}
