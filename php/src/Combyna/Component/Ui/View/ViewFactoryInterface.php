<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\View;

use Combyna\Component\Bag\ExpressionBagInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Trigger\TriggerCollectionInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextTreeFactoryInterface;
use Combyna\Component\Ui\Store\ViewStoreInterface;
use Combyna\Component\Ui\Widget\DefinedWidgetInterface;
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
