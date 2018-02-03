<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\View;

use Combyna\Component\Bag\ExpressionBagInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Trigger\TriggerCollectionInterface;
use Combyna\Component\Ui\Store\ViewStoreInterface;
use Combyna\Component\Ui\Widget\ChildReferenceWidgetInterface;
use Combyna\Component\Ui\Widget\DefinedWidgetInterface;
use Combyna\Component\Ui\Widget\TextWidgetInterface;
use Combyna\Component\Ui\Widget\WidgetDefinitionInterface;
use Combyna\Component\Ui\Widget\WidgetGroupInterface;
use Combyna\Component\Ui\Widget\WidgetInterface;

/**
 * Interface ViewFactoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ViewFactoryInterface
{
    /**
     * Creates a new compound widget child reference widget
     *
     * @param string $name
     * @param string $childName
     * @param WidgetInterface|null $parentWidget
     * @param ExpressionInterface|null $visibilityExpression
     * @param array $tags
     * @return ChildReferenceWidgetInterface
     */
    public function createChildReferenceWidget(
        $name,
        $childName,
        WidgetInterface $parentWidget = null,
        ExpressionInterface $visibilityExpression = null,
        array $tags = []
    );

    /**
     * Creates a new defined widget
     *
     * @param string $name
     * @param WidgetDefinitionInterface $widgetDefinition
     * @param ExpressionBagInterface $attributeExpressionBag
     * @param TriggerCollectionInterface $triggerCollection
     * @param WidgetInterface|null $parentWidget
     * @param ExpressionInterface|null $visibilityExpression
     * @param array $tags
     * @return DefinedWidgetInterface
     */
    public function createDefinedWidget(
        $name,
        WidgetDefinitionInterface $widgetDefinition,
        ExpressionBagInterface $attributeExpressionBag,
        TriggerCollectionInterface $triggerCollection,
        WidgetInterface $parentWidget = null,
        ExpressionInterface $visibilityExpression = null,
        array $tags = []
    );

    /**
     * Creates a collection of overlay views
     *
     * @param OverlayViewInterface[] $overlayViews
     * @return OverlayViewCollectionInterface
     */
    public function createOverlayViewCollection(array $overlayViews);

    /**
     * Creates a new page view
     *
     * @param string $name
     * @param ExpressionInterface $titleExpression
     * @param string $description
     * @param WidgetInterface $rootWidget
     * @param ViewStoreInterface $store
     * @return PageViewInterface
     */
    public function createPageView(
        $name,
        ExpressionInterface $titleExpression,
        $description,
        WidgetInterface $rootWidget,
        ViewStoreInterface $store
    );

    /**
     * Creates a collection of page views
     *
     * @param PageViewInterface[] $pageViews
     * @return PageViewCollectionInterface
     */
    public function createPageViewCollection(array $pageViews);

    /**
     * Creates a new text widget
     *
     * @param string $name
     * @param ExpressionInterface $textExpression
     * @param WidgetInterface|null $parentWidget
     * @param ExpressionInterface|null $visibilityExpression
     * @param array $tags
     * @return TextWidgetInterface
     */
    public function createTextWidget(
        $name,
        ExpressionInterface $textExpression,
        WidgetInterface $parentWidget = null,
        ExpressionInterface $visibilityExpression = null,
        array $tags = []
    );

    /**
     * Creates a new widget group
     *
     * @param string $name
     * @param WidgetInterface|null $parentWidget
     * @param ExpressionInterface|null $visibilityExpression
     * @param array $tags
     * @return WidgetGroupInterface
     */
    public function createWidgetGroup(
        $name,
        WidgetInterface $parentWidget = null,
        ExpressionInterface $visibilityExpression = null,
        array $tags = []
    );
}
