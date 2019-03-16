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
use Combyna\Component\Ui\Widget\RepeaterWidgetInterface;

/**
 * Class RepeaterWidgetState
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RepeaterWidgetState implements RepeaterWidgetStateInterface
{
    /**
     * @var int|string
     */
    private $name;

    /**
     * @var WidgetStateInterface[]
     */
    private $repeatedWidgetStates;

    /**
     * @var RepeaterWidgetInterface
     */
    private $widget;

    /**
     * @param string|int $name
     * @param RepeaterWidgetInterface $widget
     * @param WidgetStateInterface[] $repeatedWidgetStates
     */
    public function __construct(
        $name,
        RepeaterWidgetInterface $widget,
        array $repeatedWidgetStates
    ) {
        $this->name = $name;
        $this->repeatedWidgetStates = $repeatedWidgetStates;

        // FIXME: Should not have access to the widget
        $this->widget = $widget;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildState($name)
    {
        return $this->repeatedWidgetStates[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getChildStates()
    {
        return $this->repeatedWidgetStates;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepeatedWidgetStates()
    {
        return $this->repeatedWidgetStates;
    }

    /**
     * {@inheritdoc}
     */
    public function getEventualRenderableDescendantStatePath()
    {
        return []; // RepeaterWidgets are renderable, nothing to traverse down to
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
    public function getType()
    {
        return self::TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetDefinitionLibraryName()
    {
        return $this->widget->getDefinitionLibraryName();
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetDefinitionName()
    {
        return $this->widget->getDefinitionName();
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetPath()
    {
        return $this->widget->getPath();
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

            foreach ($this->repeatedWidgetStates as $repeatedWidgetState) {
                try {
                    return $repeatedWidgetState->getWidgetStatePathByPath(
                        $path,
                        array_merge($parentStates, [$this]),
                        $stateFactory
                    );
                } catch (NotFoundException $exception) {
                }
            }
        }

        throw new NotFoundException(
            'Repeater widget "' . $this->getStateName() .
            '" does not contain repeated widget with path "' .
            implode('-', $path) . '"'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetStatePathsByTag($tag, array $parentStates, UiStateFactoryInterface $stateFactory)
    {
        $widgetStatePaths = $this->widget->hasTag($tag) ?
            [$stateFactory->createWidgetStatePath(array_merge($parentStates, [$this]))] :
            [];

        foreach ($this->repeatedWidgetStates as $repeatedWidgetState) {
            $widgetStatePaths = array_merge(
                $widgetStatePaths,
                $repeatedWidgetState->getWidgetStatePathsByTag(
                    $tag,
                    array_merge($parentStates, [$this]),
                    $stateFactory
                )
            );
        }

        return $widgetStatePaths;
    }

    /**
     * {@inheritdoc}
     */
    public function hasChildState($name)
    {
        return array_key_exists($name, $this->repeatedWidgetStates);
    }

    /**
     * {@inheritdoc}
     */
    public function with(array $repeatedWidgetStates)
    {
        // Sub-state objects will all be immutable, so we only need to compare them for identity
        if ($this->repeatedWidgetStates === $repeatedWidgetStates) {
            // This state already has all of the specified sub-components of state: no need to create a new one
            return $this;
        }

        // At least one sub-component of the state has changed, so we need to create a new one
        return new self($this->name, $this->widget, $repeatedWidgetStates);
    }
}
