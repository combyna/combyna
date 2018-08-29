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
    private $definition;

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

        $this->definition = $eventDefinition;
        $this->payloadStaticBag = $payloadStaticBag;
    }

    /**
     * {@inheritdoc}
     */
    public function getLibraryName()
    {
        return $this->definition->getLibraryName();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->definition->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getPayloadStatic($staticName)
    {
        return $this->payloadStaticBag->getStatic($staticName);
    }
}
