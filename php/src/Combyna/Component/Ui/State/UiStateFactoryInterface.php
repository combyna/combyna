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
use Combyna\Component\Ui\State\Store\NullViewStoreStateInterface;
use Combyna\Component\Ui\State\Store\ViewStoreStateInterface;
use Combyna\Component\Ui\State\View\ViewStateInterface;
use Combyna\Component\Ui\State\Widget\ChildReferenceWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\DefinedCompoundWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\DefinedPrimitiveWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\RepeaterWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\TextWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetGroupStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStatePathInterface;
use Combyna\Component\Ui\View\PageViewInterface;
use Combyna\Component\Ui\Widget\ChildReferenceWidgetInterface;
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
     * @param WidgetStateInterface $childWidgetState
     * @return ChildReferenceWidgetStateInterface
     */
    public function createChildReferenceWidgetState(
        $name,
        ChildReferenceWidgetInterface $widget,
        WidgetStateInterface $childWidgetState
    );

    /**
     * Creates a DefinedCompoundWidgetState
     *
     * @param string|int $name
     * @param DefinedWidgetInterface $widget
     * @param StaticBagInterface $attributeStaticBag
     * @param WidgetStateInterface[] $childWidgetStates
     * @param WidgetStateInterface $rootWidgetState
     * @return DefinedCompoundWidgetStateInterface
     */
    public function createDefinedCompoundWidgetState(
        $name,
        DefinedWidgetInterface $widget,
        StaticBagInterface $attributeStaticBag,
        array $childWidgetStates,
        WidgetStateInterface $rootWidgetState
    );

    /**
     * Creates a DefinedPrimitiveWidgetState
     *
     * @param string|int $name
     * @param DefinedWidgetInterface $widget
     * @param StaticBagInterface $attributeStaticBag
     * @param WidgetStateInterface[] $childWidgetStates
     * @return DefinedPrimitiveWidgetStateInterface
     */
    public function createDefinedPrimitiveWidgetState(
        $name,
        DefinedWidgetInterface $widget,
        StaticBagInterface $attributeStaticBag,
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
     * Creates a PageViewState
     *
     * @param PageViewInterface $view
     * @param ViewStoreStateInterface $storeState
     * @param WidgetStateInterface $renderedRootWidget
     * @param StaticBagInterface $viewAttributeStaticBag
     * @return ViewStateInterface
     */
    public function createPageViewState(
        PageViewInterface $view,
        ViewStoreStateInterface $storeState,
        WidgetStateInterface $renderedRootWidget,
        StaticBagInterface $viewAttributeStaticBag
    );

    /**
     * Creates a RepeaterWidgetState
     *
     * @param string|int $name
     * @param RepeaterWidgetInterface $repeaterWidget
     * @param array $repeatedWidgetStates
     * @return RepeaterWidgetStateInterface
     */
    public function createRepeaterWidgetState(
        $name,
        RepeaterWidgetInterface $repeaterWidget,
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
     * @return WidgetGroupStateInterface
     */
    public function createWidgetGroupState(
        $name,
        WidgetGroupInterface $widgetGroup
    );

    /**
     * Creates a WidgetStatePath
     *
     * @param UiStateInterface[] $states
     * @return WidgetStatePathInterface
     */
    public function createWidgetStatePath(array $states);
}
