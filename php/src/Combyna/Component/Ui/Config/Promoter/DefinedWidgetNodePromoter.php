<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Config\Promoter;

use Combyna\Component\Bag\Config\Act\BagNodePromoter;
use Combyna\Component\Expression\Config\Act\DelegatingExpressionNodePromoter;
use Combyna\Component\Program\ResourceRepositoryInterface;
use Combyna\Component\Trigger\Config\Act\TriggerNodePromoter;
use Combyna\Component\Ui\Config\Act\DefinedWidgetNode;
use Combyna\Component\Ui\View\ViewFactoryInterface;
use Combyna\Component\Ui\Widget\DefinedWidgetInterface;
use Combyna\Component\Ui\Widget\WidgetInterface;
use Combyna\Component\Validator\Query\Requirement\PromotionQueryRequirement;

/**
 * Class DefinedWidgetNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DefinedWidgetNodePromoter implements WidgetNodeTypePromoterInterface
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
     * @var WidgetNodePromoterInterface
     */
    private $widgetNodePromoter;

    /**
     * @param ViewFactoryInterface $viewFactory
     * @param BagNodePromoter $bagNodePromoter
     * @param DelegatingExpressionNodePromoter $expressionNodePromoter
     * @param WidgetNodePromoterInterface $widgetNodePromoter
     * @param TriggerNodePromoter $triggerNodePromoter
     */
    public function __construct(
        ViewFactoryInterface $viewFactory,
        BagNodePromoter $bagNodePromoter,
        DelegatingExpressionNodePromoter $expressionNodePromoter,
        WidgetNodePromoterInterface $widgetNodePromoter,
        TriggerNodePromoter $triggerNodePromoter
    ) {
        $this->bagNodePromoter = $bagNodePromoter;
        $this->expressionNodePromoter = $expressionNodePromoter;
        $this->triggerNodePromoter = $triggerNodePromoter;
        $this->viewFactory = $viewFactory;
        $this->widgetNodePromoter = $widgetNodePromoter;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeToPromoterCallableMap()
    {
        return [
            DefinedWidgetNode::TYPE => [$this, 'promote']
        ];
    }

    /**
     * Promotes a DefinedWidgetNode to a DefinedWidget
     *
     * @param string|int $name
     * @param DefinedWidgetNode $widgetNode
     * @param ResourceRepositoryInterface $resourceRepository
     * @param WidgetInterface|null $parentWidget
     * @return DefinedWidgetInterface
     */
    public function promote(
        $name,
        DefinedWidgetNode $widgetNode,
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
        $queryRequirement = new PromotionQueryRequirement($widgetNode);

        $widget = $this->viewFactory->createDefinedWidget(
            $name,
            $widgetDefinition,
            $this->bagNodePromoter->promoteExpressionBag(
                $widgetNode->getAttributeExpressionBag()
            ),
            $triggerCollection,
            $this->bagNodePromoter->promoteFixedStaticBagModel($widgetNode->getCaptureStaticBagModel($queryRequirement)),
            $this->bagNodePromoter->promoteExpressionBag($widgetNode->getCaptureExpressionBag($queryRequirement)),
            $parentWidget,
            $widgetNode->getVisibilityExpression() ?
                $this->expressionNodePromoter->promote($widgetNode->getVisibilityExpression()) :
                null,
            $widgetNode->getTags()
        );

        foreach ($widgetNode->getChildWidgets() as $childWidgetName => $childWidgetNode) {
            $childWidget = $this->widgetNodePromoter->promoteWidget(
                $childWidgetName,
                $childWidgetNode,
                $resourceRepository,
                $widget
            );

            $widget->addChildWidget($childWidget);
        }

        return $widget;
    }
}
