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
     * @var string[]|int[]
     */
    private $path;

    /**
     * @var HtmlNodeInterface|null
     */
    private $rootChildNode;

    /**
     * @var WidgetStateInterface
     */
    private $widgetState;

    /**
     * @param WidgetStateInterface $widgetState
     * @param string[]|int[] $path
     * @param array $attributes
     * @param HtmlNodeInterface|null $rootChildNode
     */
    public function __construct(
        WidgetStateInterface $widgetState,
        array $path,
        array $attributes,
        HtmlNodeInterface $rootChildNode = null
    ) {
        $this->attributes = $attributes;
        $this->path = $path;
        $this->rootChildNode = $rootChildNode;
        $this->widgetState = $widgetState;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            'type' => 'generic',
            'library' => $this->widgetState->getWidgetDefinitionLibraryName(),
            'widget' => $this->widgetState->getWidgetDefinitionName(),
            'path' => $this->path,
            'attributes' => $this->attributes,
            'children' => $this->rootChildNode !== null ? $this->rootChildNode->toGenericArray() : []
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
        /*
         * TODO: Implement this.
         *
         * This may eventually always forward rendering on to a server-side
         * virtual DOM rendering library, such as React running under a Node server.
         */

        return '[Generic node: toHtml() not yet implemented]';
    }
}
