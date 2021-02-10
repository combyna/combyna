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

use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Renderer\Html\GenericNode;
use Combyna\Component\Renderer\Html\UiRendererInterface;
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
     * @var string
     */
    private $libraryName;

    /**
     * @var string|null
     */
    private $rootChildName;

    /**
     * @var UiRendererInterface
     */
    private $uiRenderer;

    /**
     * @var string
     */
    private $widgetDefinitionName;

    /**
     * @param UiRendererInterface $uiRenderer
     * @param string $libraryName
     * @param string $widgetDefinitionName
     * @param string|null $rootChildName
     */
    public function __construct(
        UiRendererInterface $uiRenderer,
        $libraryName,
        $widgetDefinitionName,
        $rootChildName = null
    ) {
        $this->libraryName = $libraryName;
        $this->rootChildName = $rootChildName;
        $this->uiRenderer = $uiRenderer;
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
    public function renderWidget(
        WidgetStateInterface $widgetState,
        WidgetStatePathInterface $widgetStatePath,
        ProgramInterface $program
    ) {
        if (
            !$widgetState instanceof DefinedWidgetStateInterface ||
            $widgetState->getWidgetDefinitionLibraryName() !== $this->getWidgetDefinitionLibraryName() ||
            $widgetState->getWidgetDefinitionName() !== $this->getWidgetDefinitionName()
        ) {
            throw new InvalidArgumentException(
                'Renderer must receive a ' .
                $this->libraryName . '.' .
                $this->widgetDefinitionName .
                ' widget state'
            );
        }

        $attributes = $widgetState->getAttributeStaticBag()->toNativeArray();
        $triggers = $this->uiRenderer->renderTriggers($widgetStatePath, $program);
        $rootChildNode = $this->rootChildName !== null ?
            $this->uiRenderer->renderWidget(
                $widgetStatePath->getChildStatePath($this->rootChildName),
                $program
            ) :
            null;

        return new GenericNode(
            $widgetState,
            $widgetStatePath->getWidgetStatePath(),
            $attributes,
            $triggers,
            $rootChildNode
        );
    }
}
