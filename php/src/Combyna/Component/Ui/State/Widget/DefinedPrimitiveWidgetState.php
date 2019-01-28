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
     * @var string|int
     */
    private $name;

    /**
     * @var StaticBagInterface
     */
    private $valueStaticBag;

    /**
     * @var DefinedWidgetInterface
     */
    private $widget;

    /**
     * @param string|int $name
     * @param DefinedWidgetInterface $widget
     * @param StaticBagInterface $attributeStaticBag
     * @param StaticBagInterface $valueStaticBag
     * @param WidgetStateInterface[] $childWidgetStates
     */
    public function __construct(
        $name,
        DefinedWidgetInterface $widget,
        StaticBagInterface $attributeStaticBag,
        StaticBagInterface $valueStaticBag,
        array $childWidgetStates
    ) {
        $widget->assertValidAttributeStaticBag($attributeStaticBag);

        $this->attributeStaticBag = $attributeStaticBag;
        $this->childWidgetStates = $childWidgetStates;
        $this->name = $name;
//        $this->storeStateCollection = $storeStateCollection;
        $this->valueStaticBag = $valueStaticBag;

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
    public function getChildStates()
    {
        return $this->childWidgetStates;
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
    public function getValue($name)
    {
        return $this->valueStaticBag->getStatic($name);
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

    /**
     * {@inheritdoc}
     */
    public function hasChildState($name)
    {
        return array_key_exists($name, $this->childWidgetStates);
    }

    /**
     * {@inheritdoc}
     */
    public function with(
        StaticBagInterface $attributeStaticBag,
        StaticBagInterface $valueStaticBag,
        array $childWidgetStates
    ) {
        // Sub-state objects will all be immutable, so we only need to compare them for identity
        // TODO: Standardise on an `->equals(...)` or `->isEqualTo(...)` method for bag comparisons like this
        if ($this->attributeStaticBag->toNativeArray() === $attributeStaticBag->toNativeArray() &&
            $this->valueStaticBag->toNativeArray() === $valueStaticBag->toNativeArray() &&
            $this->childWidgetStates === $childWidgetStates
        ) {
            // This state already has all of the specified sub-components of state: no need to create a new one
            return $this;
        }

        // At least one sub-component of the state has changed, so we need to create a new one
        return new self(
            $this->name,
            $this->widget,
            $attributeStaticBag,
            $valueStaticBag,
            $childWidgetStates
        );
    }
}
