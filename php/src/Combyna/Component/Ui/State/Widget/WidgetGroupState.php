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
use Combyna\Component\Ui\Widget\DefinedWidgetInterface;
use Combyna\Component\Ui\Widget\WidgetGroupInterface;

/**
 * Class WidgetGroupState
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetGroupState implements WidgetGroupStateInterface
{
    /**
     * @var WidgetStateInterface[]
     */
    private $childWidgetStates = [];

    /**
     * @var DefinedWidgetInterface
     */
    private $widgetGroup;

    /**
     * @param DefinedWidgetInterface $widgetGroup
     */
    public function __construct(
        WidgetGroupInterface $widgetGroup
    ) {
        $this->widgetGroup = $widgetGroup;
    }

    /**
     * {@inheritdoc}
     */
    public function addChild($childName, WidgetStateInterface $childWidgetState)
    {
        $this->childWidgetStates[$childName] = $childWidgetState;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren()
    {
        return $this->childWidgetStates;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildState($name)
    {
        return $this->childWidgetStates[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getEventualRenderableDescendantStatePath()
    {
        return []; // WidgetGroups are renderable, nothing to traverse down to
    }

    /**
     * {@inheritdoc}
     */
    public function getStateName()
    {
        return $this->widgetGroup->getName();
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
        return $this->widgetGroup->getDefinitionLibraryName();
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetDefinitionName()
    {
        return $this->widgetGroup->getDefinitionName();
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetPath()
    {
        return $this->widgetGroup->getPath();
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetStatePathByPath(array $path, array $parentStates, UiStateFactoryInterface $stateFactory)
    {
        $widgetName = array_shift($path);

        if ($widgetName === $this->getStateName()) {
            if (count($path) === 0) {
                return $stateFactory->createWidgetStatePath(array_merge($parentStates, [$this]));
            }

            foreach ($this->childWidgetStates as $childWidgetState) {
                try {
                    return $childWidgetState->getWidgetStatePathByPath(
                        $path,
                        array_merge($parentStates, [$this]),
                        $stateFactory
                    );
                } catch (NotFoundException $exception) {
                }
            }
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
        $widgetStatePaths = $this->widgetGroup->hasTag($tag) ?
            [$stateFactory->createWidgetStatePath(array_merge($parentStates, [$this]))] :
            [];

        foreach ($this->childWidgetStates as $childWidgetState) {
            $widgetStatePaths = array_merge(
                $widgetStatePaths,
                $childWidgetState->getWidgetStatePathsByTag(
                    $tag,
                    array_merge($parentStates, [$this]),
                    $stateFactory
                )
            );
        }

        return $widgetStatePaths;
    }
}
