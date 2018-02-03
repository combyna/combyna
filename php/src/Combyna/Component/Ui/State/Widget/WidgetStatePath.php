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

use Combyna\Component\Ui\State\UiStateFactoryInterface;
use Combyna\Component\Ui\State\UiStateInterface;
use LogicException;

/**
 * Class WidgetStatePath
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetStatePath implements WidgetStatePathInterface
{
    /**
     * @var UiStateFactoryInterface
     */
    private $stateFactory;

    /**
     * @var UiStateInterface[]
     */
    private $states;

    /**
     * @param UiStateFactoryInterface $stateFactory
     * @param UiStateInterface[] $states
     */
    public function __construct(UiStateFactoryInterface $stateFactory, array $states)
    {
        $this->stateFactory = $stateFactory;
        $this->states = $states;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildStatePath($childName)
    {
        $state = $this->getEndState();

        if (
            !$state instanceof DefinedWidgetStateInterface &&
            !$state instanceof WidgetGroupStateInterface &&
            !$state instanceof ChildReferenceWidgetStateInterface
        ) {
            throw new LogicException('Widget does not support children');
        }

        $childStates = array_merge($this->states, [$state->getChildState($childName)]);

        return $this->stateFactory->createWidgetStatePath($childStates);
    }

    /**
     * {@inheritdoc}
     *
     * @return WidgetStateInterface
     */
    public function getEndState()
    {
        return end($this->states);
    }

    /**
     * {@inheritdoc}
     */
    public function getEndStateType()
    {
        return $this->getEndState()->getType();
    }

    /**
     * {@inheritdoc}
     */
    public function getEventualEndRenderableStatePath()
    {
        $state = $this->getEndState();

        $states = array_merge($this->states, $state->getEventualRenderableDescendantStatePath());

        return $this->stateFactory->createWidgetStatePath($states);
    }

    /**
     * {@inheritdoc}
     */
    public function getSubStatePaths()
    {
        $subStatePaths = [];
        $stateSequence = [];

        foreach ($this->states as $state) {
            $stateSequence[] = $state;
            $subStatePaths[] = $this->stateFactory->createWidgetStatePath($stateSequence);
        }

        return $subStatePaths;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetDefinitionLibraryName()
    {
        return $this->getEndState()->getWidgetDefinitionLibraryName();
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetDefinitionName()
    {
        return $this->getEndState()->getWidgetDefinitionName();
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetPath()
    {
        $view = $this->states[0];
        $widget = $this->getEndState();

        return array_merge([$view->getStateName()], $widget->getWidgetPath());
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetStatePath()
    {
        $path = [];

        foreach ($this->states as $state) {
            $path[] = $state->getStateName();
        }

        return $path;
    }

    /**
     * {@inheritdoc}
     */
    public function getStates()
    {
        return $this->states;
    }
}
