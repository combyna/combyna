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
use Combyna\Component\Expression\Config\Act\DelegatingExpressionNodePromoter;
use Combyna\Component\Program\ResourceRepositoryInterface;
use Combyna\Component\Trigger\Config\Act\TriggerNodePromoter;
use Combyna\Component\Ui\View\ViewFactoryInterface;
use Combyna\Component\Ui\Widget\DefinedWidgetInterface;
use Combyna\Component\Ui\Widget\WidgetGroupInterface;
use Combyna\Component\Ui\Widget\WidgetInterface;
use InvalidArgumentException;

/**
 * Class WidgetNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetNodePromoter
{
    /**
     * @var BagNodePromoter
     */
    private $bagNodePromoter;

    /**
     * @var DelegatingExpressionNodePromoter
     */
    private $expressionNodePromoter;

    /**
     * @var TriggerNodePromoter
     */
    private $triggerNodePromoter;

    /**
     * @var ViewFactoryInterface
     */
    private $viewFactory;

    /**
     * @param ViewFactoryInterface $viewFactory
     * @param BagNodePromoter $bagNodePromoter
     * @param DelegatingExpressionNodePromoter $expressionNodePromoter
     * @param TriggerNodePromoter $triggerNodePromoter
     */
    public function __construct(
        ViewFactoryInterface $viewFactory,
        BagNodePromoter $bagNodePromoter,
        DelegatingExpressionNodePromoter $expressionNodePromoter,
        TriggerNodePromoter $triggerNodePromoter
    ) {
        $this->bagNodePromoter = $bagNodePromoter;
        $this->expressionNodePromoter = $expressionNodePromoter;
        $this->triggerNodePromoter = $triggerNodePromoter;
        $this->viewFactory = $viewFactory;
    }

    /**
     * Promotes a WidgetNode to a Widget
     *
     * @param string $name
     * @param WidgetNodeInterface $widgetNode
     * @param ResourceRepositoryInterface $resourceRepository
     * @param WidgetInterface|null $parentWidget
     * @return WidgetInterface
     */
    public function promoteWidget(
        $name,
        WidgetNodeInterface $widgetNode,
        ResourceRepositoryInterface $resourceRepository,
        WidgetInterface $parentWidget = null
    ) {
        if ($widgetNode instanceof WidgetGroupNode) {
            /** @var WidgetGroupNode $widgetNode */
            $widget = $this->promoteWidgetGroupNode($name, $widgetNode, $resourceRepository, $parentWidget);
        } elseif ($widgetNode instanceof WidgetNode) {
            /** @var WidgetNode $widgetNode */
            $widget = $this->promoteWidgetNode($name, $widgetNode, $resourceRepository, $parentWidget);
        } else {
            throw new InvalidArgumentException('Unsupported widget type given');
        }

        return $widget;
    }

    /**
     * Promotes a WidgetNode to a Widget
     *
     * @param string $name
     * @param WidgetNode $widgetNode
     * @param ResourceRepositoryInterface $resourceRepository
     * @param WidgetInterface|null $parentWidget
     * @return DefinedWidgetInterface
     */
    private function promoteWidgetNode(
        $name,
        WidgetNode $widgetNode,
        ResourceRepositoryInterface $resourceRepository,
        WidgetInterface $parentWidget = null
    ) {
        $widgetDefinition = $resourceRepository->getWidgetDefinitionByName(
            $widgetNode->getLibraryName(),
            $widgetNode->getWidgetDefinitionName()
        );
        $triggerCollection = $this->triggerNodePromoter->promoteCollection(
            $widgetNode->getTriggers(),
            $resourceRepository
        );

        $widget = $this->viewFactory->createDefinedWidget(
            $name,
            $widgetDefinition,
            $this->bagNodePromoter->promoteExpressionBag(
                $widgetNode->getAttributeExpressionBag()
            ),
            $triggerCollection,
            $parentWidget,
            $widgetNode->getVisibilityExpression() ?
                $this->expressionNodePromoter->promote($widgetNode->getVisibilityExpression()) :
                null,
            $widgetNode->getTags()
        );

        foreach ($widgetNode->getChildWidgets() as $childWidgetName => $childWidgetNode) {
            $childWidget = $this->promoteWidget($childWidgetName, $childWidgetNode, $resourceRepository, $widget);

            $widget->addChildWidget($childWidget);
        }

        return $widget;
    }

    /**
     * Promotes a WidgetGroupNode to a WidgetGroup
     *
     * @param string $name
     * @param WidgetGroupNode $widgetGroupNode
     * @param ResourceRepositoryInterface $resourceRepository
     * @param WidgetInterface|null $parentWidget
     * @return WidgetGroupInterface
     */
    private function promoteWidgetGroupNode(
        $name,
        WidgetGroupNode $widgetGroupNode,
        ResourceRepositoryInterface $resourceRepository,
        WidgetInterface $parentWidget = null
    ) {
        $widget = $this->viewFactory->createWidgetGroup(
            $name,
            $parentWidget,
            $widgetGroupNode->getVisibilityExpression() ?
                $this->expressionNodePromoter->promote($widgetGroupNode->getVisibilityExpression()) :
                null,
            $widgetGroupNode->getTags()
        );

        foreach ($widgetGroupNode->getChildWidgets() as $childWidgetName => $childWidgetNode) {
            $childWidget = $this->promoteWidget($childWidgetName, $childWidgetNode, $resourceRepository, $widget);

            $widget->addChildWidget($childWidget);
        }

        return $widget;
    }
}
