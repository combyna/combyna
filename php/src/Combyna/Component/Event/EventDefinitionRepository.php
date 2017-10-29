<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Event;

use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Environment\Library\LibraryInterface;

/**
 * Class EventDefinitionRepository
 *
 * A facade to allow addressing all event definitions defined by installed libraries or the app itself
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EventDefinitionRepository implements EventDefinitionRepositoryInterface
{
    /**
     * @var EventDefinitionCollectionInterface
     */
    private $appEventDefinitionCollection;

    /**
     * @var EnvironmentInterface
     */
    private $environment;

    /**
     * @param EnvironmentInterface $environment
     * @param EventDefinitionCollectionInterface $appEventDefinitionCollection
     */
    public function __construct(
        EnvironmentInterface $environment,
        EventDefinitionCollectionInterface $appEventDefinitionCollection
    ) {
        $this->appEventDefinitionCollection = $appEventDefinitionCollection;
        $this->environment = $environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getByName($libraryName, $eventName)
    {
        if ($libraryName === LibraryInterface::APP) {
            return $this->appEventDefinitionCollection->getByName($eventName);
        }

        return $this->environment->getEventDefinitionByName($libraryName, $eventName);
    }
}
