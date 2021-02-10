<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Config\Promoter\Core;

use Combyna\Component\Bag\Config\Act\BagNodePromoter;
use Combyna\Component\Expression\Config\Act\DelegatingExpressionNodePromoter;
use Combyna\Component\Program\ResourceRepositoryInterface;
use Combyna\Component\Ui\Config\Act\WidgetGroupNode;
use Combyna\Component\Ui\Config\Promoter\WidgetNodePromoterInterface;
use Combyna\Component\Ui\Config\Promoter\WidgetNodeTypePromoterInterface;
use Combyna\Component\Ui\View\ViewFactoryInterface;
use Combyna\Component\Ui\Widget\WidgetGroupInterface;
use Combyna\Component\Ui\Widget\WidgetInterface;

/**
 * Class WidgetGroupNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetGroupNodePromoter implements WidgetNodeTypePromoterInterface
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
     */
    public function __construct(
        ViewFactoryInterface $viewFactory,
        BagNodePromoter $bagNodePromoter,
        DelegatingExpressionNodePromoter $expressionNodePromoter,
        WidgetNodePromoterInterface $widgetNodePromoter
    ) {
        $this->bagNodePromoter = $bagNodePromoter;
        $this->expressionNodePromoter = $expressionNodePromoter;
        $this->viewFactory = $viewFactory;
        $this->widgetNodePromoter = $widgetNodePromoter;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeToPromoterCallableMap()
    {
        return [
            WidgetGroupNode::TYPE => [$this, 'promote']
        ];
    }

    /**
     * Promotes a WidgetGroupNode to a WidgetGroup
     *
     * @param string|int $name
     * @param WidgetGroupNode $widgetNode
     * @param ResourceRepositoryInterface $resourceRepository
     * @param WidgetInterface|null $parentWidget
     * @return WidgetGroupInterface
     */
    public function promote(
        $name,
        WidgetGroupNode $widgetNode,
        ResourceRepositoryInterface $resourceRepository,
        WidgetInterface $parentWidget = null
    ) {
        $widget = $this->viewFactory->createWidgetGroup(
            $name,
            $this->bagNodePromoter->promoteFixedStaticBagModel(
                $widgetNode->getCaptureStaticBagModel()
            ),
            $this->bagNodePromoter->promoteExpressionBag(
                $widgetNode->getCaptureExpressionBag()
            ),
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
