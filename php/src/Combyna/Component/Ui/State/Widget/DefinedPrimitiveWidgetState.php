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

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Common\Exception\NotFoundException;
use Combyna\Component\Ui\State\UiStateFactoryInterface;
use Combyna\Component\Ui\Widget\DefinedWidgetInterface;

/**
 * Class DefinedPrimitiveWidgetState
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DefinedPrimitiveWidgetState implements DefinedPrimitiveWidgetStateInterface
{
    /**
     * @var StaticBagInterface
     */
    private $attributeStaticBag;

    /**
     * @var WidgetStateInterface[]
     */
    private $childWidgetStates;

    /**
     * @var DefinedWidgetInterface
     */
    private $widget;

    /**
     * @param DefinedWidgetInterface $widget
     * @param StaticBagInterface $attributeStaticBag
     * @param WidgetStateInterface[] $childWidgetStates
     */
    public function __construct(
        DefinedWidgetInterface $widget,
        StaticBagInterface $attributeStaticBag,
        array $childWidgetStates
    ) {
        $widget->assertValidAttributeStaticBag($attributeStaticBag);

        $this->attributeStaticBag = $attributeStaticBag;
        $this->childWidgetStates = $childWidgetStates;
//        $this->storeStateCollection = $storeStateCollection;

        // FIXME: Should not have access to the widget
        $this->widget = $widget;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($name)
    {
        return $this->attributeStaticBag->getStatic($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeNames()
    {
        return array_keys($this->attributeStaticBag->toNativeArray());
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeStaticBag()
    {
        return $this->attributeStaticBag;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildNames()
    {
        return array_keys($this->childWidgetStates);
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
        return []; // Primitive widgets can always be rendered: no need to resolve further
    }

    /**
     * {@inheritdoc}
     */
    public function getStateName()
    {
        return $this->widget->getName();
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
        $widgetStatePaths = $this->widget->hasTag($tag) ?
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
