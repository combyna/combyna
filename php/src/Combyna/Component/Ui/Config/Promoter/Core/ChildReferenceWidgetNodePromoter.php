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
use Combyna\Component\Ui\Config\Act\ChildReferenceWidgetNode;
use Combyna\Component\Ui\Config\Promoter\WidgetNodeTypePromoterInterface;
use Combyna\Component\Ui\View\ViewFactoryInterface;
use Combyna\Component\Ui\Widget\ChildReferenceWidgetInterface;
use Combyna\Component\Ui\Widget\WidgetInterface;
use Combyna\Component\Validator\Query\Requirement\PromotionQueryRequirement;

/**
 * Class ChildReferenceWidgetNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ChildReferenceWidgetNodePromoter implements WidgetNodeTypePromoterInterface
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
     * @param ViewFactoryInterface $viewFactory
     * @param BagNodePromoter $bagNodePromoter
     * @param DelegatingExpressionNodePromoter $expressionNodePromoter
     */
    public function __construct(
        ViewFactoryInterface $viewFactory,
        BagNodePromoter $bagNodePromoter,
        DelegatingExpressionNodePromoter $expressionNodePromoter
    ) {
        $this->bagNodePromoter = $bagNodePromoter;
        $this->expressionNodePromoter = $expressionNodePromoter;
        $this->viewFactory = $viewFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeToPromoterCallableMap()
    {
        return [
            ChildReferenceWidgetNode::TYPE => [$this, 'promote']
        ];
    }

    /**
     * Promotes a ChildReferenceWidgetNode to a ChildReferenceWidget
     *
     * @param string|int $name
     * @param ChildReferenceWidgetNode $widgetNode
     * @param ResourceRepositoryInterface $resourceRepository
     * @param WidgetInterface|null $parentWidget
     * @return ChildReferenceWidgetInterface
     */
    public function promote(
        $name,
        ChildReferenceWidgetNode $widgetNode,
        ResourceRepositoryInterface $resourceRepository,
        WidgetInterface $parentWidget = null
    ) {
        $queryRequirement = new PromotionQueryRequirement($widgetNode);

        return $this->viewFactory->createChildReferenceWidget(
            $name,
            $widgetNode->getChildName(),
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
    }
}
