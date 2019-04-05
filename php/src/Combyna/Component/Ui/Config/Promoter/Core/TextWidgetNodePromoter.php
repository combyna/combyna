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
use Combyna\Component\Ui\Config\Act\TextWidgetNode;
use Combyna\Component\Ui\Config\Promoter\WidgetNodeTypePromoterInterface;
use Combyna\Component\Ui\View\ViewFactoryInterface;
use Combyna\Component\Ui\Widget\TextWidgetInterface;
use Combyna\Component\Ui\Widget\WidgetInterface;
use Combyna\Component\Validator\Query\Requirement\PromotionQueryRequirement;

/**
 * Class TextWidgetNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TextWidgetNodePromoter implements WidgetNodeTypePromoterInterface
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
            TextWidgetNode::TYPE => [$this, 'promote']
        ];
    }

    /**
     * Promotes a TextWidgetNode to a TextWidget
     *
     * @param string|int $name
     * @param TextWidgetNode $widgetNode
     * @param ResourceRepositoryInterface $resourceRepository
     * @param WidgetInterface|null $parentWidget
     * @return TextWidgetInterface
     */
    public function promote(
        $name,
        TextWidgetNode $widgetNode,
        ResourceRepositoryInterface $resourceRepository,
        WidgetInterface $parentWidget = null
    ) {
        $queryRequirement = new PromotionQueryRequirement($widgetNode);
        $textExpression = $this->expressionNodePromoter->promote($widgetNode->getTextExpression());

        return $this->viewFactory->createTextWidget(
            $name,
            $textExpression,
            $this->bagNodePromoter->promoteFixedStaticBagModel($widgetNode->getCaptureStaticBagModel($queryRequirement)),
            $this->bagNodePromoter->promoteExpressionBag($widgetNode->getCaptureExpressionBag($queryRequirement)),
            $parentWidget,
            $widgetNode->getVisibilityExpression() ?
                $this->expressionNodePromoter->promote($widgetNode->getVisibilityExpression()) :
                null,
            $widgetNode->getTags()
        );
    }
}
