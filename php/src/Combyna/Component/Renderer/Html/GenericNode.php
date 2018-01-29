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

use Combyna\Component\Ui\State\Widget\WidgetStateInterface;

/**
 * Class GenericNode
 *
 * Represents a rendered widget that does not map directly to an HTML element.
 * This is used to pass more complex primitive widgets almost directly down to the rendering side
 * (eg. React in JS), where they can be rendered as needed
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class GenericNode implements HtmlNodeInterface
{
    /**
     * @var array
     */
    private $attributes;

    /**
     * @var HtmlNodeInterface[]
     */
    private $childNodes;

    /**
     * @var WidgetStateInterface
     */
    private $widgetState;

    /**
     * @param WidgetStateInterface $widgetState
     * @param array $attributes
     * @param HtmlNodeInterface[] $childNodes
     */
    public function __construct(WidgetStateInterface $widgetState, array $attributes, array $childNodes)
    {
        $this->attributes = $attributes;
        $this->childNodes = $childNodes;
        $this->widgetState = $widgetState;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $children = [];

        foreach ($this->childNodes as $childName => $childNode) {
            $children[$childName] = $childNode->toArray();
        }

        return [
            'type' => 'generic',
            'library' => $this->widgetState->getWidgetDefinitionLibraryName(),
            'widget' => $this->widgetState->getWidgetDefinitionName(),
            'attributes' => $this->attributes,
            'children' => $children
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function toHtml()
    {
        /*
         * TODO: Implement this.
         *
         * This may eventually always forward rendering on to a server-side
         * virtual DOM rendering library, such as React running under a Node server.
         */

        return '[Generic node: toHtml() not yet implemented]';
    }
}
