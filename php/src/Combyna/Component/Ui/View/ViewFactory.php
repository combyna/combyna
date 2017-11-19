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

use Combyna\Component\Bag\BagFactoryInterface;
use Combyna\Component\Bag\ExpressionBagInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Trigger\TriggerCollectionInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextFactoryInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextTreeFactoryInterface;
use Combyna\Component\Ui\State\UiStateFactoryInterface;
use Combyna\Component\Ui\Store\ViewStoreInterface;
use Combyna\Component\Ui\Widget\DefinedWidget;
use Combyna\Component\Ui\Widget\WidgetDefinitionInterface;
use Combyna\Component\Ui\Widget\WidgetGroup;
use Combyna\Component\Ui\Widget\WidgetInterface;

/**
 * Class ViewFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ViewFactory implements ViewFactoryInterface
{
    /**
     * @var BagFactoryInterface
     */
    private $bagFactory;

    /**
     * @var UiEvaluationContextFactoryInterface
     */
    private $uiEvaluationContextFactory;

    /**
     * @var UiEvaluationContextTreeFactoryInterface
     */
    private $uiEvaluationContextTreeFactory;

    /**
     * @var UiStateFactoryInterface
     */
    private $uiStateFactory;

    /**
     * @param UiStateFactoryInterface $uiStateFactory
     * @param UiEvaluationContextFactoryInterface $uiEvaluationContextFactory
     * @param UiEvaluationContextTreeFactoryInterface $uiEvaluationContextTreeFactory
     * @param BagFactoryInterface $bagFactory
     */
    public function __construct(
        UiStateFactoryInterface $uiStateFactory,
        UiEvaluationContextFactoryInterface $uiEvaluationContextFactory,
        UiEvaluationContextTreeFactoryInterface $uiEvaluationContextTreeFactory,
        BagFactoryInterface $bagFactory
    ) {
        $this->bagFactory = $bagFactory;
        $this->uiEvaluationContextFactory = $uiEvaluationContextFactory;
        $this->uiEvaluationContextTreeFactory = $uiEvaluationContextTreeFactory;
        $this->uiStateFactory = $uiStateFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createDefinedWidget(
        $name,
        WidgetDefinitionInterface $widgetDefinition,
        ExpressionBagInterface $attributeExpressionBag,
        TriggerCollectionInterface $triggerCollection,
        WidgetInterface $parentWidget = null,
        ExpressionInterface $visibilityExpression = null,
        array $tags = []
    ) {
        return new DefinedWidget(
            $parentWidget,
            $name,
            $widgetDefinition,
            $attributeExpressionBag,
            $this->uiStateFactory,
            $triggerCollection,
            $visibilityExpression,
            $tags
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createOverlayViewCollection(array $overlayViews)
    {
        return new OverlayViewCollection($overlayViews);
    }

    /**
     * {@inheritdoc}
     */
    public function createPageView(
        $name,
        ExpressionInterface $titleExpression,
        $description,
        WidgetInterface $rootWidget,
        ViewStoreInterface $store
    ) {
        return new PageView(
            $name,
            $titleExpression,
            $description,
            $rootWidget,
            $store,
            $this->bagFactory,
            $this->uiStateFactory,
            $this->uiEvaluationContextFactory,
            $this->uiEvaluationContextTreeFactory
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createPageViewCollection(array $pageViews)
    {
        return new PageViewCollection($pageViews);
    }

    /**
     * {@inheritdoc}
     */
    public function createWidgetGroup(
        $name,
        WidgetInterface $parentWidget = null,
        ExpressionInterface $visibilityExpression = null,
        array $tags = []
    ) {
        return new WidgetGroup($this->uiStateFactory, $name, $parentWidget, $visibilityExpression, $tags);
    }
}
