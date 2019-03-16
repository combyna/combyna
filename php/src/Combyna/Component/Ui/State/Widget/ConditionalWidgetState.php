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
use Combyna\Component\Ui\Widget\ConditionalWidgetInterface;
use LogicException;

/**
 * Class ConditionalWidgetState
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ConditionalWidgetState implements ConditionalWidgetStateInterface
{
    /**
     * @var WidgetStateInterface|null
     */
    private $alternateWidgetState;

    /**
     * @var WidgetStateInterface|null
     */
    private $consequentWidgetState;

    /**
     * @var int|string
     */
    private $name;

    /**
     * @var ConditionalWidgetInterface
     */
    private $widget;

    /**
     * @param string|int $name
     * @param ConditionalWidgetInterface $widget
     * @param WidgetStateInterface|null $consequentWidgetState
     * @param WidgetStateInterface|null $alternateWidgetState
     */
    public function __construct(
        $name,
        ConditionalWidgetInterface $widget,
        WidgetStateInterface $consequentWidgetState = null,
        WidgetStateInterface $alternateWidgetState = null
    ) {
        $this->alternateWidgetState = $alternateWidgetState;
        $this->consequentWidgetState = $consequentWidgetState;
        $this->name = $name;

        // FIXME: Should not have access to the widget
        $this->widget = $widget;
    }

    /**
     * {@inheritdoc}
     */
    public function getAlternateWidgetState()
    {
        return $this->alternateWidgetState;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildState($name)
    {
        if ($name === self::CONSEQUENT_CHILD_NAME) {
            if ($this->consequentWidgetState === null) {
                throw new LogicException('Consequent widget is not present');
            }

            return $this->consequentWidgetState;
        }

        if ($name === self::ALTERNATE_CHILD_NAME) {
            if ($this->alternateWidgetState === null) {
                throw new LogicException('Alternate widget is not present');
            }

            return $this->alternateWidgetState;
        }

        throw new LogicException(sprintf('Unknown child "%s" of conditional widget', $name));
    }

    /**
     * {@inheritdoc}
     */
    public function getChildStates()
    {
        $childStates = [];

        if ($this->consequentWidgetState !== null) {
            $childStates[self::CONSEQUENT_CHILD_NAME] = $this->consequentWidgetState;
        }

        if ($this->alternateWidgetState !== null) {
            $childStates[self::ALTERNATE_CHILD_NAME] = $this->alternateWidgetState;
        }

        return $childStates;
    }

    /**
     * {@inheritdoc}
     */
    public function getConsequentWidgetState()
    {
        return $this->consequentWidgetState;
    }

    /**
     * {@inheritdoc}
     */
    public function getEventualRenderableDescendantStatePath()
    {
        return []; // ConditionalWidgets are renderable, nothing to traverse down to
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

            if ($this->consequentWidgetState !== null) {
                try {
                    return $this->consequentWidgetState->getWidgetStatePathByPath(
                        $path,
                        array_merge($parentStates, [$this]),
                        $stateFactory
                    );
                } catch (NotFoundException $exception) {
                }
            }

            if ($this->alternateWidgetState !== null) {
                try {
                    return $this->alternateWidgetState->getWidgetStatePathByPath(
                        $path,
                        array_merge($parentStates, [$this]),
                        $stateFactory
                    );
                } catch (NotFoundException $exception) {
                }
            }
        }

        throw new NotFoundException(
            'Conditional widget "' . $this->getStateName() .
            '" does not contain widget with path "' .
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

        if ($this->consequentWidgetState !== null) {
            $widgetStatePaths = array_merge(
                $widgetStatePaths,
                $this->consequentWidgetState->getWidgetStatePathsByTag(
                    $tag,
                    array_merge($parentStates, [$this]),
                    $stateFactory
                )
            );
        }

        if ($this->alternateWidgetState !== null) {
            $widgetStatePaths = array_merge(
                $widgetStatePaths,
                $this->alternateWidgetState->getWidgetStatePathsByTag(
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
        return $name === self::CONSEQUENT_CHILD_NAME || $name === self::ALTERNATE_CHILD_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function with(
        WidgetStateInterface $consequentWidgetState = null,
        WidgetStateInterface $alternateWidgetState = null
    ) {
        // Sub-state objects will all be immutable, so we only need to compare them for identity
        if ($this->consequentWidgetState === $consequentWidgetState &&
            $this->alternateWidgetState === $alternateWidgetState
        ) {
            // This state already has all of the specified sub-components of state: no need to create a new one
            return $this;
        }

        // At least one sub-component of the state has changed, so we need to create a new one
        return new self($this->name, $this->widget, $consequentWidgetState, $alternateWidgetState);
    }
}
