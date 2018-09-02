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
use Combyna\Component\Ui\State\Store\NullViewStoreState;
use Combyna\Component\Ui\State\Store\ViewStoreState;
use Combyna\Component\Ui\State\Store\ViewStoreStateInterface;
use Combyna\Component\Ui\State\View\PageViewState;
use Combyna\Component\Ui\State\Widget\ChildReferenceWidgetState;
use Combyna\Component\Ui\State\Widget\DefinedCompoundWidgetState;
use Combyna\Component\Ui\State\Widget\DefinedPrimitiveWidgetState;
use Combyna\Component\Ui\State\Widget\RepeaterWidgetState;
use Combyna\Component\Ui\State\Widget\TextWidgetState;
use Combyna\Component\Ui\State\Widget\WidgetGroupState;
use Combyna\Component\Ui\State\Widget\WidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStatePath;
use Combyna\Component\Ui\View\PageViewInterface;
use Combyna\Component\Ui\Widget\ChildReferenceWidgetInterface;
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
        WidgetStateInterface $childWidgetState
    ) {
        return new ChildReferenceWidgetState($name, $widget, $childWidgetState);
    }

    /**
     * {@inheritdoc}
     */
    public function createDefinedCompoundWidgetState(
        $name,
        DefinedWidgetInterface $widget,
        StaticBagInterface $attributeStaticBag,
        array $childWidgetStates,
        WidgetStateInterface $rootWidgetState
    ) {
        return new DefinedCompoundWidgetState(
            $name,
            $widget,
            $attributeStaticBag,
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
        array $childWidgetStates
    ) {
        return new DefinedPrimitiveWidgetState(
            $name,
            $widget,
            $attributeStaticBag,
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
        ViewStoreStateInterface $storeState,
        WidgetStateInterface $renderedRootWidget,
        StaticBagInterface $viewAttributeStaticBag
    ) {
        return new PageViewState($this, $view, $storeState, $renderedRootWidget, $viewAttributeStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function createRepeaterWidgetState(
        $name,
        RepeaterWidgetInterface $repeaterWidget,
        array $repeatedWidgetStates
    ) {
        return new RepeaterWidgetState($name, $repeaterWidget, $repeatedWidgetStates);
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
        WidgetGroupInterface $widgetGroup
    ) {
        return new WidgetGroupState($name, $widgetGroup);
    }

    /**
     * {@inheritdoc}
     */
    public function createWidgetStatePath(array $states)
    {
        return new WidgetStatePath($this, $states);
    }
}
