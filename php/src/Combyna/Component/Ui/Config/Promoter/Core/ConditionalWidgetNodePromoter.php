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
use Combyna\Component\Ui\Config\Act\ConditionalWidgetNode;
use Combyna\Component\Ui\Config\Promoter\WidgetNodePromoterInterface;
use Combyna\Component\Ui\Config\Promoter\WidgetNodeTypePromoterInterface;
use Combyna\Component\Ui\View\ViewFactoryInterface;
use Combyna\Component\Ui\Widget\ConditionalWidget;
use Combyna\Component\Ui\Widget\ConditionalWidgetInterface;
use Combyna\Component\Ui\Widget\WidgetInterface;
use Combyna\Component\Validator\Query\Requirement\PromotionQueryRequirement;

/**
 * Class ConditionalWidgetNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ConditionalWidgetNodePromoter implements WidgetNodeTypePromoterInterface
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
            ConditionalWidgetNode::TYPE => [$this, 'promote']
        ];
    }

    /**
     * Promotes a ConditionalWidgetNode to a ConditionalWidget
     *
     * @param string|int $name
     * @param ConditionalWidgetNode $widgetNode
     * @param ResourceRepositoryInterface $resourceRepository
     * @param WidgetInterface|null $parentWidget
     * @return ConditionalWidgetInterface
     */
    public function promote(
        $name,
        ConditionalWidgetNode $widgetNode,
        ResourceRepositoryInterface $resourceRepository,
        WidgetInterface $parentWidget = null
    ) {
        $queryRequirement = new PromotionQueryRequirement($widgetNode);

        $conditionalWidget = $this->viewFactory->createConditionalWidget(
            $name,
            $this->expressionNodePromoter->promote($widgetNode->getConditionExpression()),
            $this->bagNodePromoter->promoteFixedStaticBagModel(
                $widgetNode->getCaptureStaticBagModel($queryRequirement)
            ),
            $this->bagNodePromoter->promoteExpressionBag(
                $widgetNode->getCaptureExpressionBag($queryRequirement)
            ),
            $parentWidget,
            $widgetNode->getTags()
        );

        $conditionalWidget->setConsequentWidget(
            $this->widgetNodePromoter->promoteWidget(
                ConditionalWidget::CONSEQUENT_WIDGET_NAME,
                $widgetNode->getConsequentWidget(),
                $resourceRepository,
                $conditionalWidget
            )
        );

        if ($widgetNode->getAlternateWidget() !== null) {
            $conditionalWidget->setAlternateWidget(
                $this->widgetNodePromoter->promoteWidget(
                    ConditionalWidget::ALTERNATE_WIDGET_NAME,
                    $widgetNode->getAlternateWidget(),
                    $resourceRepository,
                    $conditionalWidget
                )
            );
        }

        return $conditionalWidget;
    }
}
