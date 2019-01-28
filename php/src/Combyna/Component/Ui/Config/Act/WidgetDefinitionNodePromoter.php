<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Config\Act;

use Combyna\Component\Bag\Config\Act\BagNodePromoter;
use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Event\Config\Act\EventDefinitionReferenceNodePromoter;
use Combyna\Component\Expression\StaticExpressionFactoryInterface;
use Combyna\Component\Ui\Widget\WidgetDefinitionFactoryInterface;
use Combyna\Component\Ui\Widget\WidgetDefinitionInterface;
use LogicException;

/**
 * Class WidgetDefinitionNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetDefinitionNodePromoter
{
    /**
     * @var BagNodePromoter
     */
    private $bagNodePromoter;

    /**
     * @var EventDefinitionReferenceNodePromoter
     */
    private $eventDefinitionReferenceNodePromoter;

    /**
     * @var StaticExpressionFactoryInterface
     */
    private $staticExpressionFactory;

    /**
     * @var WidgetDefinitionFactoryInterface
     */
    private $widgetDefinitionFactory;

    /**
     * @var WidgetNodePromoter
     */
    private $widgetNodePromoter;

    /**
     * @param BagNodePromoter $bagNodePromoter
     * @param EventDefinitionReferenceNodePromoter $eventDefinitionReferenceNodePromoter
     * @param StaticExpressionFactoryInterface $staticExpressionFactory
     * @param WidgetDefinitionFactoryInterface $widgetDefinitionFactory
     * @param WidgetNodePromoter $widgetNodePromoter
     */
    public function __construct(
        BagNodePromoter $bagNodePromoter,
        EventDefinitionReferenceNodePromoter $eventDefinitionReferenceNodePromoter,
        StaticExpressionFactoryInterface $staticExpressionFactory,
        WidgetDefinitionFactoryInterface $widgetDefinitionFactory,
        WidgetNodePromoter $widgetNodePromoter
    ) {
        $this->bagNodePromoter = $bagNodePromoter;
        $this->eventDefinitionReferenceNodePromoter = $eventDefinitionReferenceNodePromoter;
        $this->staticExpressionFactory = $staticExpressionFactory;
        $this->widgetDefinitionFactory = $widgetDefinitionFactory;
        $this->widgetNodePromoter = $widgetNodePromoter;
    }

    /**
     * Creates a widget definition from its ACT node
     *
     * @param WidgetDefinitionNodeInterface $widgetDefinitionNode
     * @param EnvironmentInterface $environment
     * @return WidgetDefinitionInterface
     */
    public function promoteDefinition(
        WidgetDefinitionNodeInterface $widgetDefinitionNode,
        EnvironmentInterface $environment
    ) {
        $attributeBagModel = $this->bagNodePromoter->promoteFixedStaticBagModel(
            $widgetDefinitionNode->getAttributeBagModel()
        );
        $eventDefinitionReferenceCollection = $this->eventDefinitionReferenceNodePromoter->promoteCollection(
            $widgetDefinitionNode->getEventDefinitionReferences(),
            $environment
        );

        if ($widgetDefinitionNode instanceof CompoundWidgetDefinitionNode) {
            $valueExpressionBag = $this->bagNodePromoter->promoteExpressionBag(
                $widgetDefinitionNode->getValueExpressionBag()
            );

            return $this->widgetDefinitionFactory->createCompoundWidgetDefinition(
                $eventDefinitionReferenceCollection,
                $widgetDefinitionNode->getLibraryName(),
                $widgetDefinitionNode->getWidgetDefinitionName(),
                $attributeBagModel,
                $valueExpressionBag,
                $this->widgetNodePromoter->promoteWidget(
                    'root',
                    $widgetDefinitionNode->getRootWidget(),
                    $environment
                )
            );
        }

        if ($widgetDefinitionNode instanceof PrimitiveWidgetDefinitionNode) {
            $valueBagModel = $this->bagNodePromoter->promoteFixedStaticBagModel(
                $widgetDefinitionNode->getValueBagModel()
            );

            return $this->widgetDefinitionFactory->createPrimitiveWidgetDefinition(
                $eventDefinitionReferenceCollection,
                $widgetDefinitionNode->getLibraryName(),
                $widgetDefinitionNode->getWidgetDefinitionName(),
                $attributeBagModel,
                $valueBagModel,
                $this->staticExpressionFactory,
                $widgetDefinitionNode->getValueNameToProviderCallableMap()
            );
        }

        throw new LogicException('Unknown widget definition type');
    }
}
