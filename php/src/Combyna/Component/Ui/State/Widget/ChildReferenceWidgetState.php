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
use Combyna\Component\Ui\Widget\ChildReferenceWidgetInterface;
use InvalidArgumentException;

/**
 * Class ChildReferenceWidgetState
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ChildReferenceWidgetState implements ChildReferenceWidgetStateInterface
{
    /**
     * @var ChildReferenceWidgetInterface
     */
    private $childReferenceWidget;

    /**
     * @var string|int
     */
    private $name;

    /**
     * @var WidgetStateInterface
     */
    private $referencedChildWidgetState;

    /**
     * @param string|int $name
     * @param ChildReferenceWidgetInterface $childReferenceWidget
     * @param WidgetStateInterface $referencedChildWidgetState
     */
    public function __construct(
        $name,
        ChildReferenceWidgetInterface $childReferenceWidget,
        WidgetStateInterface $referencedChildWidgetState
    ) {
        // FIXME: Remove references from state objects back to the entities like this!
        $this->childReferenceWidget = $childReferenceWidget;
        $this->name = $name;
        $this->referencedChildWidgetState = $referencedChildWidgetState;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildName()
    {
        return $this->childReferenceWidget->getChildName();
    }

    /**
     * {@inheritdoc}
     */
    public function getChildState($name)
    {
        if ($name !== 'child') {
            throw new InvalidArgumentException('Only the "child" child is supported for ChildReferenceWidgetStates');
        }

        return $this->referencedChildWidgetState;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildStates()
    {
        return [$this->referencedChildWidgetState];
    }

    /**
     * {@inheritdoc}
     */
    public function getEventualRenderableDescendantStatePath()
    {
        // FIXME: Is this correct?

        return []; // ChildReferenceWidgets are renderable, nothing to traverse down to
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
        return $this->childReferenceWidget->getDefinitionLibraryName();
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetDefinitionName()
    {
        return $this->childReferenceWidget->getDefinitionName();
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetPath()
    {
        return $this->childReferenceWidget->getPath();
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
        $parentStatesForChild = array_merge($parentStates, [$this]);
        $widgetStatePaths = $this->childReferenceWidget->hasTag($tag) ?
            [$stateFactory->createWidgetStatePath($parentStatesForChild)] :
            [];

        $widgetStatePaths = array_merge(
            $widgetStatePaths,
            $this->referencedChildWidgetState->getWidgetStatePathsByTag($tag, $parentStatesForChild, $stateFactory)
        );

        return $widgetStatePaths;
    }

    /**
     * {@inheritdoc}
     */
    public function hasChildState($name)
    {
        // Only the "child" child is supported for ChildReferenceWidgetStates
        return $name === 'child';
    }

    /**
     * {@inheritdoc}
     */
    public function with(WidgetStateInterface $referencedWidgetState)
    {
        // Sub-state objects will all be immutable, so we only need to compare them for identity
        if ($this->referencedChildWidgetState === $referencedWidgetState) {
            // This state already has all of the specified sub-components of state: no need to create a new one
            return $this;
        }

        // At least one sub-component of the state has changed, so we need to create a new one
        return new self($this->name, $this->childReferenceWidget, $referencedWidgetState);
    }
}
