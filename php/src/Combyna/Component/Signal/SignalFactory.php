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
use Combyna\Component\Environment\EnvironmentInterface;

/**
 * Class SignalFactory
 *
 * Creates Signal objects, used to broadcast events throughout the system
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalFactory implements SignalFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createSignal(SignalDefinitionInterface $signalDefinition, StaticBagInterface $payloadStaticBag)
    {
        return new Signal($signalDefinition, $payloadStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function createSignalDefinition(
        $libraryName,
        $signalName,
        FixedStaticBagModelInterface $payloadStaticBagModel,
        $isBroadcast
    ) {
        return new SignalDefinition($libraryName, $signalName, $payloadStaticBagModel, $isBroadcast);
    }

    /**
     * {@inheritdoc}
     */
    public function createSignalDefinitionCollection(array $signalDefinitions, $libraryName)
    {
        return new SignalDefinitionCollection($signalDefinitions, $libraryName);
    }

    /**
     * {@inheritdoc}
     */
    public function createSignalDefinitionReference($libraryName, $signalName)
    {
        return new SignalDefinitionReference($libraryName, $signalName);
    }

    /**
     * {@inheritdoc}
     */
    public function createSignalDefinitionRepository(
        EnvironmentInterface $environment,
        SignalDefinitionCollectionInterface $appSignalDefinitionCollection
    ) {
        return new SignalDefinitionRepository($environment, $appSignalDefinitionCollection);
    }
}
