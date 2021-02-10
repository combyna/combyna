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
 * Class RenderedWidget
 *
 * Represents a widget that has been rendered to a HTML element tree.
 * For example, if a widget is rendered to two <div> elements,
 * it will have one RenderedWidget as the container
 * which will then contain a DocumentFragment with the two elements inside.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RenderedWidget implements HtmlNodeInterface
{
    /**
     * @var HtmlNodeInterface
     */
    private $rootNode;

    /**
     * @var WidgetStateInterface
     */
    private $widgetState;

    /**
     * @param WidgetStateInterface $widgetState
     * @param HtmlNodeInterface $rootNode
     */
    public function __construct(WidgetStateInterface $widgetState, HtmlNodeInterface $rootNode)
    {
        $this->rootNode = $rootNode;
        $this->widgetState = $widgetState;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            'type' => 'widget',
            'library' => $this->widgetState->getWidgetDefinitionLibraryName(),
            'widget' => $this->widgetState->getWidgetDefinitionName(),
            'root' => $this->rootNode->toArray()
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
        return $this->rootNode->toHtml();
    }
}
