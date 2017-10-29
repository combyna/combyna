<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
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
use Combyna\Component\Ui\State\Widget\DefinedWidgetState;
use Combyna\Component\Ui\State\Widget\WidgetGroupState;
use Combyna\Component\Ui\State\Widget\WidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStatePath;
use Combyna\Component\Ui\View\PageViewInterface;
use Combyna\Component\Ui\Widget\DefinedWidgetInterface;
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
    public function createDefinedWidgetState(
        DefinedWidgetInterface $widget,
        StaticBagInterface $attributeStaticBag
    ) {
        return new DefinedWidgetState($widget, $attributeStaticBag);
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
    public function createViewStoreState($storeViewName, StaticBagInterface $slotStaticBag)
    {
        return new ViewStoreState($storeViewName, $slotStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function createWidgetGroupState(
        WidgetGroupInterface $widgetGroup
    ) {
        return new WidgetGroupState($widgetGroup);
    }

    /**
     * {@inheritdoc}
     */
    public function createWidgetStatePath(array $states)
    {
        return new WidgetStatePath($this, $states);
    }
}
