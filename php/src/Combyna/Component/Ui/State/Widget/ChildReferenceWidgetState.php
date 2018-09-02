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
     * @var WidgetStateInterface
     */
    private $childWidgetState;

    /**
     * @var string|int
     */
    private $name;

    /**
     * @param string|int $name
     * @param ChildReferenceWidgetInterface $childReferenceWidget
     * @param WidgetStateInterface $childWidgetState
     */
    public function __construct(
        $name,
        ChildReferenceWidgetInterface $childReferenceWidget,
        WidgetStateInterface $childWidgetState
    ) {
        // FIXME: Remove references from state objects back to the entities like this!
        $this->childReferenceWidget = $childReferenceWidget;
        $this->childWidgetState = $childWidgetState;
        $this->name = $name;
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

        return $this->childWidgetState;
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
        return $this->childReferenceWidget->hasTag($tag) ?
            [$stateFactory->createWidgetStatePath(array_merge($parentStates, [$this]))] :
            [];
    }
}
