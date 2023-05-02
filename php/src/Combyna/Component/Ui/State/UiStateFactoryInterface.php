<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\State;

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Router\State\RouterStateInterface;
use Combyna\Component\Ui\State\Store\NullViewStoreStateInterface;
use Combyna\Component\Ui\State\Store\ViewStoreStateInterface;
use Combyna\Component\Ui\State\View\ViewStateInterface;
use Combyna\Component\Ui\State\Widget\ChildReferenceWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\ConditionalWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\DefinedCompoundWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\DefinedPrimitiveWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\RepeaterWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\TextWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetGroupStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStatePathInterface;
use Combyna\Component\Ui\View\PageViewInterface;
use Combyna\Component\Ui\Widget\ChildReferenceWidgetInterface;
use Combyna\Component\Ui\Widget\ConditionalWidgetInterface;
use Combyna\Component\Ui\Widget\DefinedWidgetInterface;
use Combyna\Component\Ui\Widget\RepeaterWidgetInterface;
use Combyna\Component\Ui\Widget\TextWidgetInterface;
use Combyna\Component\Ui\Widget\WidgetGroupInterface;

/**
 * Interface UiStateFactoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface UiStateFactoryInterface
{
    /**
     * Creates a ChildReferenceWidgetState
     *
     * @param string|int $name
     * @param ChildReferenceWidgetInterface $widget
     * @param WidgetStateInterface $referencedChildWidgetState The state of the referenced compound widget child
     * @return ChildReferenceWidgetStateInterface
     */
    public function createChildReferenceWidgetState(
        $name,
        ChildReferenceWidgetInterface $widget,
        WidgetStateInterface $referencedChildWidgetState
    );

    /**
     * Creates a ConditionalWidgetState
     *
     * @param string|int $name
     * @param ConditionalWidgetInterface $widget
     * @param WidgetStateInterface|null $consequentChildWidgetState The state of the consequent widget, if present
     * @param WidgetStateInterface|null $alternateChildWidgetState The state of the alternate widget, if defined & present
     * @return ConditionalWidgetStateInterface
     */
    public function createConditionalWidgetState(
        $name,
        ConditionalWidgetInterface $widget,
        WidgetStateInterface $consequentChildWidgetState = null,
        WidgetStateInterface $alternateChildWidgetState = null
    );

    /**
     * Creates a DefinedCompoundWidgetState
     *
     * @param string|int $name
     * @param DefinedWidgetInterface $widget
     * @param StaticBagInterface $attributeStaticBag
     * @param StaticBagInterface $valueStaticBag
     * @param WidgetStateInterface[] $childWidgetStates
     * @param WidgetStateInterface $rootWidgetState
     * @return DefinedCompoundWidgetStateInterface
     */
    public function createDefinedCompoundWidgetState(
        $name,
        DefinedWidgetInterface $widget,
        StaticBagInterface $attributeStaticBag,
        StaticBagInterface $valueStaticBag,
        array $childWidgetStates,
        WidgetStateInterface $rootWidgetState
    );

    /**
     * Creates a DefinedPrimitiveWidgetState
     *
     * @param string|int $name
     * @param DefinedWidgetInterface $widget
     * @param StaticBagInterface $attributeStaticBag
     * @param StaticBagInterface $valueStaticBag
     * @param WidgetStateInterface[] $childWidgetStates
     * @return DefinedPrimitiveWidgetStateInterface
     */
    public function createDefinedPrimitiveWidgetState(
        $name,
        DefinedWidgetInterface $widget,
        StaticBagInterface $attributeStaticBag,
        StaticBagInterface $valueStaticBag,
        array $childWidgetStates
    );

    /**
     * Creates a NullViewStoreState
     *
     * @param string $storeViewName
     * @return NullViewStoreStateInterface
     */
    public function createNullViewStoreState($storeViewName);

    /**
     * Creates a PageViewState.
     *
     * @param PageViewInterface $view
     * @param string $title
     * @param RouterStateInterface $routerState
     * @param ViewStoreStateInterface $storeState
     * @param WidgetStateInterface $renderedRootWidget
     * @param StaticBagInterface $viewAttributeStaticBag
     * @return ViewStateInterface
     */
    public function createPageViewState(
        PageViewInterface $view,
        $title,
        RouterStateInterface $routerState,
        ViewStoreStateInterface $storeState,
        WidgetStateInterface $renderedRootWidget,
        StaticBagInterface $viewAttributeStaticBag
    );

    /**
     * Creates a RepeaterWidgetState
     *
     * @param string|int $name
     * @param RepeaterWidgetInterface $repeaterWidget
     * @param StaticInterface[] $itemStatics
     * @param array $repeatedWidgetStates
     * @return RepeaterWidgetStateInterface
     */
    public function createRepeaterWidgetState(
        $name,
        RepeaterWidgetInterface $repeaterWidget,
        array $itemStatics,
        array $repeatedWidgetStates
    );

    /**
     * Creates a TextWidgetState
     *
     * @param string|int $name
     * @param TextWidgetInterface $textWidget
     * @param string $text
     * @return TextWidgetStateInterface
     */
    public function createTextWidgetState($name, TextWidgetInterface $textWidget, $text);

    /**
     * Creates a ViewStoreState
     *
     * @param string $storeViewName
     * @param StaticBagInterface $slotStaticBag
     * @return ViewStoreStateInterface
     */
    public function createViewStoreState(
        $storeViewName,
        StaticBagInterface $slotStaticBag
    );

    /**
     * Creates a WidgetGroupState
     *
     * @param string|int $name
     * @param WidgetGroupInterface $widgetGroup
     * @param WidgetStateInterface[] $childWidgetStates
     * @return WidgetGroupStateInterface
     */
    public function createWidgetGroupState(
        $name,
        WidgetGroupInterface $widgetGroup,
        array $childWidgetStates
    );

    /**
     * Creates a WidgetStatePath
     *
     * @param UiStateInterface[] $states
     * @return WidgetStatePathInterface
     */
    public function createWidgetStatePath(array $states);
}
