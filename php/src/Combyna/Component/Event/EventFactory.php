<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Event;

use Combyna\Component\Bag\FixedStaticBagModelInterface;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Program\ResourceRepositoryInterface;
use Combyna\Component\Ui\Widget\WidgetDefinitionInterface;

/**
 * Class EventFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EventFactory implements EventFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createEvent(
        EventDefinitionInterface $eventDefinition,
        StaticBagInterface $payloadStaticBag,
        WidgetDefinitionInterface $widgetDefinition
    ) {
        return new Event($eventDefinition, $payloadStaticBag, $widgetDefinition);
    }

    /**
     * {@inheritdoc}
     */
    public function createEventDefinition($libraryName, $eventName, FixedStaticBagModelInterface $payloadStaticBagModel)
    {
        return new EventDefinition($libraryName, $eventName, $payloadStaticBagModel);
    }

    /**
     * {@inheritdoc}
     */
    public function createEventDefinitionCollection(array $eventDefinitions, $libraryName)
    {
        return new EventDefinitionCollection($eventDefinitions, $libraryName);
    }

    /**
     * {@inheritdoc}
     */
    public function createEventDefinitionReference($libraryName, $eventName)
    {
        return new EventDefinitionReference($libraryName, $eventName);
    }

    /**
     * {@inheritdoc}
     */
    public function createEventDefinitionReferenceCollection(
        array $eventDefinitionReferences,
        ResourceRepositoryInterface $resourceRepository
    ) {
        return new EventDefinitionReferenceCollection($eventDefinitionReferences, $resourceRepository);
    }

    /**
     * {@inheritdoc}
     */
    public function createEventDefinitionRepository(
        EnvironmentInterface $environment,
        EventDefinitionCollectionInterface $appEventDefinitionCollection
    ) {
        return new EventDefinitionRepository($environment, $appEventDefinitionCollection);
    }
}
