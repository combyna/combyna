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
use Combyna\Component\Expression\Config\Act\ExpressionNodePromoter;
use Combyna\Component\Ui\WidgetDefinitionFactoryInterface;
use Combyna\Component\Ui\WidgetDefinitionInterface;
use LogicException;

/**
 * Class WidgetDefinitionPromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetDefinitionPromoter
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
     * @var WidgetDefinitionFactoryInterface
     */
    private $widgetDefinitionFactory;

    /**
     * @param BagNodePromoter $bagNodePromoter
     * @param ExpressionNodePromoter $expressionNodePromoter
     * @param WidgetDefinitionFactoryInterface $widgetDefinitionFactory
     */
    public function __construct(
        BagNodePromoter $bagNodePromoter,
        ExpressionNodePromoter $expressionNodePromoter,
        WidgetDefinitionFactoryInterface $widgetDefinitionFactory
    ) {
        $this->bagNodePromoter = $bagNodePromoter;
        $this->expressionNodePromoter = $expressionNodePromoter;
        $this->widgetDefinitionFactory = $widgetDefinitionFactory;
    }

    /**
     * Creates a widget definition from its ACT node
     *
     * @param WidgetDefinitionNodeInterface $widgetDefinitionNode
     * @return WidgetDefinitionInterface
     */
    public function promoteDefinition(WidgetDefinitionNodeInterface $widgetDefinitionNode)
    {
        $attributeBagModel = $this->bagNodePromoter->promoteFixedStaticBagModel(
            $widgetDefinitionNode->getAttributeBagModel(),
            $this->expressionNodePromoter
        );

        if ($widgetDefinitionNode instanceof CompoundWidgetDefinitionNode) {
            return $this->widgetDefinitionFactory->createCompoundWidgetDefinition(
                $widgetDefinitionNode->getLibraryName(),
                $widgetDefinitionNode->getWidgetDefinitionName(),
                $attributeBagModel
            );
        }

        if ($widgetDefinitionNode instanceof CoreWidgetDefinitionNode) {
            return $this->widgetDefinitionFactory->createCoreWidgetDefinition(
                $widgetDefinitionNode->getLibraryName(),
                $widgetDefinitionNode->getWidgetDefinitionName(),
                $attributeBagModel
            );
        }

        throw new LogicException('Unknown widget definition type');
    }
}
