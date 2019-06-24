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

use Combyna\Component\Environment\Library\LibraryInterface;
use Combyna\Component\State\Exception\AncestorStateUnavailableException;
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

        if (!$state instanceof ParentWidgetStateInterface) {
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
    public function getParentState()
    {
        if (count($this->states) <= 1) {
            throw new AncestorStateUnavailableException('Parent state unavailable');
        }

        return $this->states[count($this->states) - 2];
    }

    /**
     * {@inheritdoc}
     */
    public function getParentStateType()
    {
        return $this->getParentState()->getType();
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
        // -2 so that we don't resolve via the definition when the end of the path
        // points to a compound widget (rather than a descendant of one)
        for ($i = count($this->states) - 2; $i >= 0; $i--) {
            $state = $this->states[$i];

            if ($state instanceof ChildReferenceWidgetStateInterface) {
                // This state is for a child passed into a compound widget, so skip past its compound widget parent
                if (!$this->states[$i - 1] instanceof DefinedCompoundWidgetStateInterface) {
                    throw new LogicException(sprintf(
                        'Expected parent to be a %s but it was a %s',
                        DefinedCompoundWidgetStateInterface::class,
                        get_class($this->states[$i - 1])
                    ));
                }

                $i--;
                continue;
            }

            if ($state instanceof DefinedCompoundWidgetStateInterface) {
                // This state is for a widget of a compound widget definition,
                // so the path needs to first point to the definition itself
                // and then to the widget within its root tree
                $path = [
                    $state->getWidgetDefinitionLibraryName(),
                    self::WIDGET_DEFINITION_PATH_TYPE,
                    $state->getWidgetDefinitionName()
                ];

                $relativePath = $this->getEndState()->getWidgetPath();

                for ($i++; $i < count($this->states) - count($relativePath); $i++) {
                    $path[] = $this->states[$i]->getStateName();
                }

                $path = array_merge($path, $relativePath);

                return $path;
            }
        }

        $view = $this->states[0];
        $widgetState = $this->getEndState();

        return array_merge(
            [
                LibraryInterface::APP,
                self::VIEW_PATH_TYPE,
                $view->getStateName()
            ],
            $widgetState->getWidgetPath()
        );
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

    /**
     * {@inheritdoc}
     */
    public function hasParent()
    {
        return count($this->states) > 1;
    }
}
