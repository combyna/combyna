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
 * Interface EventFactoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface EventFactoryInterface
{
    /**
     * Creates a new Event
     *
     * @param EventDefinitionInterface $eventDefinition
     * @param StaticBagInterface $payloadStaticBag
     * @param WidgetDefinitionInterface $widgetDefinition
     * @return EventInterface
     */
    public function createEvent(
        EventDefinitionInterface $eventDefinition,
        StaticBagInterface $payloadStaticBag,
        WidgetDefinitionInterface $widgetDefinition
    );

    /**
     * Creates a new EventDefinition
     *
     * @param string $libraryName
     * @param string $eventName
     * @param FixedStaticBagModelInterface $payloadStaticBagModel
     * @return EventDefinitionInterface
     */
    public function createEventDefinition(
        $libraryName,
        $eventName,
        FixedStaticBagModelInterface $payloadStaticBagModel
    );

    /**
     * Creates a new EventDefinitionCollection
     *
     * @param EventDefinitionInterface[] $eventDefinitions
     * @return EventDefinitionCollectionInterface
     */
    public function createEventDefinitionCollection(array $eventDefinitions, $libraryName);

    /**
     * Creates a new EventDefinitionReference
     *
     * @param string $libraryName
     * @param string $eventName
     * @return EventDefinitionReferenceInterface
     */
    public function createEventDefinitionReference($libraryName, $eventName);

    /**
     * Creates a new EventDefinitionReferenceCollection
     *
     * @param EventDefinitionReferenceInterface[] $eventDefinitionReferences
     * @param ResourceRepositoryInterface $resourceRepository
     * @return EventDefinitionReferenceCollectionInterface
     */
    public function createEventDefinitionReferenceCollection(
        array $eventDefinitionReferences,
        ResourceRepositoryInterface $resourceRepository
    );

    /**
     * Creates a new EventDefinitionRepository
     *
     * @param EnvironmentInterface $environment
     * @param EventDefinitionCollectionInterface $appEventDefinitionCollection
     * @return EventDefinitionRepositoryInterface
     */
    public function createEventDefinitionRepository(
        EnvironmentInterface $environment,
        EventDefinitionCollectionInterface $appEventDefinitionCollection
    );
}
