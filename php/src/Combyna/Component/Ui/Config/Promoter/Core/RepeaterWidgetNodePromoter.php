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
use Combyna\Component\Ui\Config\Act\RepeaterWidgetNode;
use Combyna\Component\Ui\Config\Promoter\WidgetNodePromoterInterface;
use Combyna\Component\Ui\Config\Promoter\WidgetNodeTypePromoterInterface;
use Combyna\Component\Ui\View\ViewFactoryInterface;
use Combyna\Component\Ui\Widget\RepeaterWidget;
use Combyna\Component\Ui\Widget\RepeaterWidgetInterface;
use Combyna\Component\Ui\Widget\WidgetInterface;
use Combyna\Component\Validator\Query\Requirement\PromotionQueryRequirement;

/**
 * Class RepeaterWidgetNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RepeaterWidgetNodePromoter implements WidgetNodeTypePromoterInterface
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
            RepeaterWidgetNode::TYPE => [$this, 'promote']
        ];
    }

    /**
     * Promotes a RepeaterWidgetNode to a RepeaterWidget
     *
     * @param string|int $name
     * @param RepeaterWidgetNode $widgetNode
     * @param ResourceRepositoryInterface $resourceRepository
     * @param WidgetInterface|null $parentWidget
     * @return RepeaterWidgetInterface
     */
    public function promote(
        $name,
        RepeaterWidgetNode $widgetNode,
        ResourceRepositoryInterface $resourceRepository,
        WidgetInterface $parentWidget = null
    ) {
        $queryRequirement = new PromotionQueryRequirement($widgetNode);

        $repeaterWidget = $this->viewFactory->createRepeaterWidget(
            $name,
            $this->expressionNodePromoter->promote($widgetNode->getItemListExpression()),
            $widgetNode->getIndexVariableName(),
            $widgetNode->getItemVariableName(),
            $this->bagNodePromoter->promoteFixedStaticBagModel(
                $widgetNode->getCaptureStaticBagModel($queryRequirement)
            ),
            $this->bagNodePromoter->promoteExpressionBag(
                $widgetNode->getCaptureExpressionBag($queryRequirement)
            ),
            $parentWidget,
            $widgetNode->getVisibilityExpression() ?
                $this->expressionNodePromoter->promote($widgetNode->getVisibilityExpression()) :
                null,
            $widgetNode->getTags()
        );

        $repeaterWidget->setRepeatedWidget(
            $this->widgetNodePromoter->promoteWidget(
                RepeaterWidget::REPEATED_WIDGET_NAME,
                $widgetNode->getRepeatedWidget(),
                $resourceRepository,
                $repeaterWidget
            )
        );

        return $repeaterWidget;
    }
}
