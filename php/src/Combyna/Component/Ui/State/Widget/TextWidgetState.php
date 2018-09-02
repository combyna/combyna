<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\State\Widget;

use Combyna\Component\Common\Exception\NotFoundException;
use Combyna\Component\Ui\State\UiStateFactoryInterface;
use Combyna\Component\Ui\Widget\TextWidgetInterface;

/**
 * Class TextWidgetState
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TextWidgetState implements TextWidgetStateInterface
{
    /**
     * @var int|string
     */
    private $name;

    /**
     * @var string
     */
    private $text;

    /**
     * @var TextWidgetInterface
     */
    private $textWidget;

    /**
     * @param string|int $name
     * @param TextWidgetInterface $textWidget
     * @param string $text
     */
    public function __construct(
        $name,
        TextWidgetInterface $textWidget,
        $text
    ) {
        $this->name = $name;
        $this->text = $text;

        // FIXME: Remove references from state objects back to the entities like this!
        $this->textWidget = $textWidget;
    }

    /**
     * {@inheritdoc}
     */
    public function getEventualRenderableDescendantStatePath()
    {
        return []; // TextWidgets are renderable, nothing to traverse down to
    }

    /**
     * {@inheritdoc}
     */
    public function getStateName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetDefinitionLibraryName()
    {
        return $this->textWidget->getDefinitionLibraryName();
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetDefinitionName()
    {
        return $this->textWidget->getDefinitionName();
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetPath()
    {
        return $this->textWidget->getPath();
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetStatePathByPath(array $path, array $parentStates, UiStateFactoryInterface $stateFactory)
    {
        if ($path === [$this->getStateName()]) {
            return $stateFactory->createWidgetStatePath(array_merge($parentStates, [$this]));
        }

        throw new NotFoundException(
            'Widget "' . $this->getStateName() . '" does not contain widget with path "' . implode('-', $path) . '"'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetStatePathsByTag($tag, array $parentStates, UiStateFactoryInterface $stateFactory)
    {
        return $this->textWidget->hasTag($tag) ?
            [$stateFactory->createWidgetStatePath(array_merge($parentStates, [$this]))] :
            [];
    }
}
