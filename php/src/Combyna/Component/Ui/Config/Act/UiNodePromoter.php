<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Config\Act;

use Combyna\Component\Bag\Config\Act\BagNodePromoter;
use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Expression\Config\Act\ExpressionNodePromoter;
use Combyna\Component\Ui\ViewCollection;
use Combyna\Component\Ui\ViewFactoryInterface;
use Combyna\Component\Ui\ViewInterface;
use Combyna\Component\Ui\WidgetInterface;

/**
 * Class UiNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UiNodePromoter
{
    /**
     * @var BagNodePromoter
     */
    private $bagNodePromoter;

    /**
     * @var ExpressionNodePromoter
     */
    private $expressionNodePromoter;

    /**
     * @var ViewFactoryInterface
     */
    private $viewFactory;

    /**
     * @param ViewFactoryInterface $viewFactory
     * @param BagNodePromoter $bagNodePromoter
     * @param ExpressionNodePromoter $expressionNodePromoter
     */
    public function __construct(
        ViewFactoryInterface $viewFactory,
        BagNodePromoter $bagNodePromoter,
        ExpressionNodePromoter $expressionNodePromoter
    ) {
        $this->bagNodePromoter = $bagNodePromoter;
        $this->expressionNodePromoter = $expressionNodePromoter;
        $this->viewFactory = $viewFactory;
    }

    /**
     * Promotes a ViewNode to a View
     *
     * @param ViewNode $viewNode
     * @param EnvironmentInterface $environment
     * @return ViewInterface
     */
    public function promoteView(
        ViewNode $viewNode,
        EnvironmentInterface $environment
    ) {
        return $this->viewFactory->createView(
            $viewNode->getName(),
            $this->expressionNodePromoter->promote($viewNode->getTitleExpression()),
            $viewNode->getDescription(),
            $this->bagNodePromoter->promoteFixedStaticBagModel(
                $viewNode->getAttributeBagModel(),
                $this->expressionNodePromoter
            ),
            $this->promoteWidget($viewNode->getRootWidget(), $environment),
            $viewNode->getVisibilityExpression() ?
                $this->expressionNodePromoter->promote($viewNode->getVisibilityExpression()) :
                null
        );
    }

    /**
     * Promotes a ViewCollectionNode to a ViewCollection
     *
     * @param ViewCollectionNode $viewCollectionNode
     * @param EnvironmentInterface $environment
     * @return ViewCollection
     */
    public function promoteViewCollection(
        ViewCollectionNode $viewCollectionNode,
        EnvironmentInterface $environment
    ) {
        $views = [];

        foreach ($viewCollectionNode->getViews() as $viewNode) {
            $views[] = $this->promoteView($viewNode, $environment);
        }

        return $this->viewFactory->createViewCollection($views);
    }

    /**
     * Promotes a WidgetNode to a Widget
     *
     * @param WidgetNode $widgetNode
     * @param EnvironmentInterface $environment
     * @param WidgetInterface|null $parentWidget
     * @return WidgetInterface
     */
    public function promoteWidget(
        WidgetNode $widgetNode,
        EnvironmentInterface $environment,
        WidgetInterface $parentWidget = null
    ) {
        $widgetDefinition = $environment->getWidgetDefinition(
            $widgetNode->getLibraryName(),
            $widgetNode->getWidgetDefinitionName()
        );

        $widget = $this->viewFactory->createWidget(
            $widgetDefinition,
            $this->bagNodePromoter->promoteExpressionBag(
                $widgetNode->getAttributeExpressionBag(),
                $this->expressionNodePromoter
            ),
            $parentWidget,
            $widgetNode->getVisibilityExpression() ?
                $this->expressionNodePromoter->promote($widgetNode->getVisibilityExpression()) :
                null
        );

        foreach ($widgetNode->getChildWidgets() as $childWidgetName => $childWidgetNode) {
            $childWidget = $this->promoteWidget($childWidgetNode, $environment, $widget);

            $widget->addChild($childWidgetName, $childWidget);
        }

        return $widget;
    }
}
