<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal;

use Combyna\Component\Bag\FixedMutableStaticBagInterface;

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
     * @var FixedMutableStaticBagInterface
     */
    private $payloadStaticBag;

    /**
     * @param SignalDefinitionInterface $definition
     * @param FixedMutableStaticBagInterface $payloadStaticBag
     */
    public function __construct(
        SignalDefinitionInterface $definition,
        FixedMutableStaticBagInterface $payloadStaticBag
    ) {
        $this->definition = $definition;
        $this->payloadStaticBag = $payloadStaticBag;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(SignalHandlerInterface $signalHandler)
    {
        $signalHandler->handleSignal($this->definition->getName(), $this->payloadStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->definition->getName();
    }
}
