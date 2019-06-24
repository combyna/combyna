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
use Combyna\Component\Router\State\RouterStateInterface;
use Combyna\Component\Ui\State\Store\NullViewStoreState;
use Combyna\Component\Ui\State\Store\ViewStoreState;
use Combyna\Component\Ui\State\Store\ViewStoreStateInterface;
use Combyna\Component\Ui\State\View\PageViewState;
use Combyna\Component\Ui\State\Widget\ChildReferenceWidgetState;
use Combyna\Component\Ui\State\Widget\ConditionalWidgetState;
use Combyna\Component\Ui\State\Widget\DefinedCompoundWidgetState;
use Combyna\Component\Ui\State\Widget\DefinedPrimitiveWidgetState;
use Combyna\Component\Ui\State\Widget\RepeaterWidgetState;
use Combyna\Component\Ui\State\Widget\TextWidgetState;
use Combyna\Component\Ui\State\Widget\WidgetGroupState;
use Combyna\Component\Ui\State\Widget\WidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStatePath;
use Combyna\Component\Ui\View\PageViewInterface;
use Combyna\Component\Ui\Widget\ChildReferenceWidgetInterface;
use Combyna\Component\Ui\Widget\ConditionalWidgetInterface;
use Combyna\Component\Ui\Widget\DefinedWidgetInterface;
use Combyna\Component\Ui\Widget\RepeaterWidgetInterface;
use Combyna\Component\Ui\Widget\TextWidgetInterface;
use Combyna\Component\Ui\Widget\WidgetGroupInterface;

/**
 * Class UiStateFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UiStateFactory implements UiStateFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createChildReferenceWidgetState(
        $name,
        ChildReferenceWidgetInterface $widget,
        WidgetStateInterface $referencedChildWidgetState
    ) {
        return new ChildReferenceWidgetState($name, $widget, $referencedChildWidgetState);
    }

    /**
     * {@inheritdoc}
     */
    public function createConditionalWidgetState(
        $name,
        ConditionalWidgetInterface $widget,
        WidgetStateInterface $consequentChildWidgetState = null,
        WidgetStateInterface $alternateChildWidgetState = null
    ) {
        return new ConditionalWidgetState($name, $widget, $consequentChildWidgetState, $alternateChildWidgetState);
    }

    /**
     * {@inheritdoc}
     */
    public function createDefinedCompoundWidgetState(
        $name,
        DefinedWidgetInterface $widget,
        StaticBagInterface $attributeStaticBag,
        StaticBagInterface $valueStaticBag,
        array $childWidgetStates,
        WidgetStateInterface $rootWidgetState
    ) {
        return new DefinedCompoundWidgetState(
            $name,
            $widget,
            $attributeStaticBag,
            $valueStaticBag,
            $childWidgetStates,
            $rootWidgetState
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createDefinedPrimitiveWidgetState(
        $name,
        DefinedWidgetInterface $widget,
        StaticBagInterface $attributeStaticBag,
        StaticBagInterface $valueStaticBag,
        array $childWidgetStates
    ) {
        return new DefinedPrimitiveWidgetState(
            $name,
            $widget,
            $attributeStaticBag,
            $valueStaticBag,
            $childWidgetStates
        );
    }

//    /**
//     * {@inheritdoc}
//     */
//    public function createEmbedViewState(
//        PageViewInterface $view,
//        StaticBagInterface $attributeStaticBag,
//        WidgetStateInterface $rootWidgetState
//    ) {
//
//    }

    /**
     * {@inheritdoc}
     */
    public function createNullViewStoreState($storeViewName)
    {
        return new NullViewStoreState($storeViewName);
    }

    /**
     * {@inheritdoc}
     */
    public function createPageViewState(
        PageViewInterface $view,
        RouterStateInterface $routerState,
        ViewStoreStateInterface $storeState,
        WidgetStateInterface $renderedRootWidget,
        StaticBagInterface $viewAttributeStaticBag
    ) {
        return new PageViewState(
            $this,
            $view,
            $routerState,
            $storeState,
            $renderedRootWidget,
            $viewAttributeStaticBag
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createRepeaterWidgetState(
        $name,
        RepeaterWidgetInterface $repeaterWidget,
        array $itemStatics,
        array $repeatedWidgetStates
    ) {
        return new RepeaterWidgetState($name, $repeaterWidget, $itemStatics, $repeatedWidgetStates);
    }

    /**
     * {@inheritdoc}
     */
    public function createTextWidgetState($name, TextWidgetInterface $textWidget, $text)
    {
        return new TextWidgetState($name, $textWidget, $text);
    }

    /**
     * {@inheritdoc}
     */
    public function createViewStoreState($storeViewName, StaticBagInterface $slotStaticBag)
    {
        return new ViewStoreState($storeViewName, $slotStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function createWidgetGroupState(
        $name,
        WidgetGroupInterface $widgetGroup,
        array $childWidgetStates
    ) {
        return new WidgetGroupState($name, $widgetGroup, $childWidgetStates);
    }

    /**
     * {@inheritdoc}
     */
    public function createWidgetStatePath(array $states)
    {
        return new WidgetStatePath($this, $states);
    }
}
