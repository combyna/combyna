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

use Combyna\Component\Bag\FixedStaticBagModelInterface;
use Combyna\Component\Bag\StaticBagInterface;

/**
 * Class SignalDefinition
 *
 * Defines the name and payload structure for an event that could occur
 * or a request that could be made within the system
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalDefinition implements SignalDefinitionInterface
{
    /**
     * @var bool
     */
    private $isBroadcast;

    /**
     * @var string
     */
    private $libraryName;

    /**
     * A unique name for the signal type within the system
     *
     * @var string
     */
    private $name;

    /**
     * Defines the statics that must be provided as the payload to go along with the signal
     *
     * @var FixedStaticBagModelInterface
     */
    private $payloadStaticBagModel;

    /**
     * @param string $libraryName
     * @param string $name
     * @param FixedStaticBagModelInterface $payloadStaticBagModel
     * @param bool $isBroadcast
     */
    public function __construct($libraryName, $name, FixedStaticBagModelInterface $payloadStaticBagModel, $isBroadcast)
    {
        $this->isBroadcast = (bool) $isBroadcast;
        $this->libraryName = $libraryName;
        $this->name = $name;
        $this->payloadStaticBagModel = $payloadStaticBagModel;
    }

    /**
     * {@inheritdoc}
     */
    public function assertValidPayloadStaticBag(StaticBagInterface $payloadStaticBag)
    {
        $this->payloadStaticBagModel->assertValidStaticBag($payloadStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function getLibraryName()
    {
        return $this->libraryName;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getPayloadStaticBagModel()
    {
        return $this->payloadStaticBagModel;
    }

    /**
     * {@inheritdoc}
     */
    public function isBroadcast()
    {
        return $this->isBroadcast;
    }
}
