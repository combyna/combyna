<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Renderer\Html\WidgetRenderer;

use Combyna\Component\Renderer\Html\GenericNode;
use Combyna\Component\Ui\State\Widget\DefinedWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStatePathInterface;
use InvalidArgumentException;

/**
 * Class GenericWidgetRenderer
 *
 * Used for any custom primitive widget where specific rendering logic is not required.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class GenericWidgetRenderer implements WidgetRendererInterface
{
    /**
     * @var DelegatingWidgetRenderer
     */
    private $delegatingWidgetRenderer;

    /**
     * @var string
     */
    private $libraryName;

    /**
     * @var string|null
     */
    private $rootChildName;

    /**
     * @var string
     */
    private $widgetDefinitionName;

    /**
     * @param DelegatingWidgetRenderer $delegatingWidgetRenderer
     * @param string $libraryName
     * @param string $widgetDefinitionName
     * @param string|null $rootChildName
     */
    public function __construct(
        DelegatingWidgetRenderer $delegatingWidgetRenderer,
        $libraryName,
        $widgetDefinitionName,
        $rootChildName = null
    ) {
        $this->delegatingWidgetRenderer = $delegatingWidgetRenderer;
        $this->libraryName = $libraryName;
        $this->rootChildName = $rootChildName;
        $this->widgetDefinitionName = $widgetDefinitionName;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetDefinitionLibraryName()
    {
        return $this->libraryName;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetDefinitionName()
    {
        return $this->widgetDefinitionName;
    }

    /**
     * {@inheritdoc}
     */
    public function renderWidget(WidgetStateInterface $widgetState, WidgetStatePathInterface $widgetStatePath)
    {
        if (
            !$widgetState instanceof DefinedWidgetStateInterface ||
            $widgetState->getWidgetDefinitionLibraryName() !== $this->getWidgetDefinitionLibraryName() ||
            $widgetState->getWidgetDefinitionName() !== $this->getWidgetDefinitionName()
        ) {
            throw new InvalidArgumentException(
                'Renderer must receive a ' .
                $this->libraryName . '.' .
                $this->widgetDefinitionName .
                ' widget'
            );
        }

        $attributes = $widgetState->getAttributeStaticBag()->toNativeArray();
        $rootChildNode = $this->rootChildName !== null ?
            $this->delegatingWidgetRenderer->renderWidget(
                $widgetStatePath->getChildStatePath($this->rootChildName)
            ) :
            null;

        return new GenericNode($widgetState, $widgetStatePath->getWidgetStatePath(), $attributes, $rootChildNode);
    }
}
