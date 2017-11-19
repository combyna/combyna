<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment\Config\Act;

use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Environment\Library\FunctionFactoryInterface;
use Combyna\Component\Environment\Library\LibraryFactoryInterface;
use Combyna\Component\Environment\Library\LibraryInterface;
use Combyna\Component\Event\Config\Act\EventDefinitionNodePromoter;
use Combyna\Component\Signal\Config\Act\SignalDefinitionNodePromoter;
use Combyna\Component\Ui\Config\Act\WidgetDefinitionNodePromoter;

/**
 * Class LibraryNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class LibraryNodePromoter
{
    /**
     * @var EventDefinitionNodePromoter
     */
    private $eventDefinitionNodePromoter;

    /**
     * @var FunctionFactoryInterface
     */
    private $functionFactory;

    /**
     * @var FunctionNodePromoter
     */
    private $functionPromoter;

    /**
     * @var LibraryFactoryInterface
     */
    private $libraryFactory;

    /**
     * @var SignalDefinitionNodePromoter
     */
    private $signalDefinitionNodePromoter;

    /**
     * @var WidgetDefinitionNodePromoter
     */
    private $widgetDefinitionNodePromoter;

    /**
     * @param FunctionFactoryInterface $functionFactory
     * @param FunctionNodePromoter $functionPromoter
     * @param EventDefinitionNodePromoter $eventDefinitionNodePromoter
     * @param SignalDefinitionNodePromoter $signalDefinitionNodePromoter
     * @param WidgetDefinitionNodePromoter $widgetDefinitionNodePromoter
     * @param LibraryFactoryInterface $libraryFactory
     */
    public function __construct(
        FunctionFactoryInterface $functionFactory,
        FunctionNodePromoter $functionPromoter,
        EventDefinitionNodePromoter $eventDefinitionNodePromoter,
        SignalDefinitionNodePromoter $signalDefinitionNodePromoter,
        WidgetDefinitionNodePromoter $widgetDefinitionNodePromoter,
        LibraryFactoryInterface $libraryFactory
    ) {
        $this->eventDefinitionNodePromoter = $eventDefinitionNodePromoter;
        $this->functionFactory = $functionFactory;
        $this->functionPromoter = $functionPromoter;
        $this->libraryFactory = $libraryFactory;
        $this->signalDefinitionNodePromoter = $signalDefinitionNodePromoter;
        $this->widgetDefinitionNodePromoter = $widgetDefinitionNodePromoter;
    }

    /**
     * Creates a Library from its ACT node
     *
     * @param LibraryNode $libraryNode
     * @param EnvironmentInterface $environment
     * @return LibraryInterface
     */
    public function promoteLibrary(LibraryNode $libraryNode, EnvironmentInterface $environment)
    {
        $functionCollection = $this->functionPromoter->promoteCollection(
            $libraryNode->getFunctions(),
            $libraryNode->getName()
        );
        $eventDefinitionCollection = $this->eventDefinitionNodePromoter->promoteCollection(
            $libraryNode->getEventDefinitions(),
            $libraryNode->getName()
        );
        $signalDefinitionCollection = $this->signalDefinitionNodePromoter->promoteCollection(
            $libraryNode->getSignalDefinitions(),
            $libraryNode->getName()
        );

        $widgetDefinitions = [];

        foreach ($libraryNode->getWidgetDefinitions() as $widgetDefinitionNode) {
            $widgetDefinitions[] = $this->widgetDefinitionNodePromoter->promoteDefinition(
                $widgetDefinitionNode,
                $environment
            );
        }

        return $this->libraryFactory->create(
            $libraryNode->getName(),
            $functionCollection,
            $eventDefinitionCollection,
            $signalDefinitionCollection,
            $widgetDefinitions
        );
    }
}
