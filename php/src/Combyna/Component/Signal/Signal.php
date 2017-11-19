<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal;

use Combyna\Component\Bag\StaticBagInterface;

/**
 * Class Signal
 *
 * Represents an event that has occurred or a request that has been made within the system
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Signal implements SignalInterface
{
    /**
     * @var SignalDefinitionInterface
     */
    private $definition;

    /**
     * @var StaticBagInterface
     */
    private $payloadStaticBag;

    /**
     * @param SignalDefinitionInterface $definition
     * @param StaticBagInterface $payloadStaticBag
     */
    public function __construct(
        SignalDefinitionInterface $definition,
        StaticBagInterface $payloadStaticBag
    ) {
        $definition->assertValidPayloadStaticBag($payloadStaticBag);

        $this->definition = $definition;
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
    public function getPayloadStaticBag()
    {
        return $this->payloadStaticBag;
    }
}
