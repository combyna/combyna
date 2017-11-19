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

use Combyna\Component\Bag\StaticBagInterface;

/**
 * Class Event
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Event implements EventInterface
{
    /**
     * @var EventDefinitionInterface
     */
    private $eventDefinition;

    /**
     * @var StaticBagInterface
     */
    private $payloadStaticBag;

    /**
     * @param EventDefinitionInterface $eventDefinition
     * @param StaticBagInterface $payloadStaticBag
     */
    public function __construct(EventDefinitionInterface $eventDefinition, StaticBagInterface $payloadStaticBag)
    {
        $eventDefinition->assertValidPayloadStaticBag($payloadStaticBag);

        $this->eventDefinition = $eventDefinition;
        $this->payloadStaticBag = $payloadStaticBag;
    }

    /**
     * {@inheritdoc}
     */
    public function getEventLibraryName()
    {
        return $this->eventDefinition->getLibraryName();
    }

    /**
     * {@inheritdoc}
     */
    public function getEventName()
    {
        return $this->eventDefinition->getName();
    }
}
