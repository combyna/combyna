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
use Combyna\Component\Environment\Library\LibraryInterface;
use Combyna\Component\Event\Config\Act\EventDefinitionReferenceNodePromoter;
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
     * @var WidgetDefinitionFactoryInterface
     */
    private $widgetDefinitionFactory;

    /**
     * @param BagNodePromoter $bagNodePromoter
     * @param EventDefinitionReferenceNodePromoter $eventDefinitionReferenceNodePromoter
     * @param WidgetDefinitionFactoryInterface $widgetDefinitionFactory
     */
    public function __construct(
        BagNodePromoter $bagNodePromoter,
        EventDefinitionReferenceNodePromoter $eventDefinitionReferenceNodePromoter,
        WidgetDefinitionFactoryInterface $widgetDefinitionFactory
    ) {
        $this->bagNodePromoter = $bagNodePromoter;
        $this->eventDefinitionReferenceNodePromoter = $eventDefinitionReferenceNodePromoter;
        $this->widgetDefinitionFactory = $widgetDefinitionFactory;
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
            return $this->widgetDefinitionFactory->createCompoundWidgetDefinition(
                $eventDefinitionReferenceCollection,
                $widgetDefinitionNode->getLibraryName(),
                $widgetDefinitionNode->getWidgetDefinitionName(),
                $attributeBagModel,
                $widgetDefinitionNode->getLabels()
            );
        }

        if ($widgetDefinitionNode instanceof PrimitiveWidgetDefinitionNode) {
            return $this->widgetDefinitionFactory->createPrimitiveWidgetDefinition(
                $eventDefinitionReferenceCollection,
                $widgetDefinitionNode->getLibraryName(),
                $widgetDefinitionNode->getWidgetDefinitionName(),
                $attributeBagModel,
                $widgetDefinitionNode->getLabels()
            );
        }

        throw new LogicException('Unknown widget definition type');
    }
}
