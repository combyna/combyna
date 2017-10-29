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

use Combyna\Component\Bag\FixedStaticBagModelInterface;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Environment\EnvironmentInterface;

/**
 * Interface SignalFactoryInterface
 *
 * Creates Signal objects, used to broadcast events throughout the system
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface SignalFactoryInterface
{
    /**
     * Creates a new Signal
     *
     * @param SignalDefinitionInterface $signalDefinition
     * @param StaticBagInterface $payloadStaticBag
     * @return SignalInterface
     */
    public function createSignal(SignalDefinitionInterface $signalDefinition, StaticBagInterface $payloadStaticBag);

    /**
     * Creates a new SignalDefinition
     *
     * @param string $libraryName
     * @param string $signalName
     * @param FixedStaticBagModelInterface $payloadStaticBagModel
     * @return SignalDefinitionInterface
     */
    public function createSignalDefinition(
        $libraryName,
        $signalName,
        FixedStaticBagModelInterface $payloadStaticBagModel
    );

    /**
     * Creates a new SignalDefinitionCollection
     *
     * @param SignalDefinitionInterface[] $signalDefinitions
     * @param string $libraryName
     * @return SignalDefinitionCollectionInterface
     */
    public function createSignalDefinitionCollection(array $signalDefinitions, $libraryName);

    /**
     * Creates a new SignalDefinitionReference
     *
     * @param string $libraryName
     * @param string $signalName
     * @return SignalDefinitionReferenceInterface
     */
    public function createSignalDefinitionReference($libraryName, $signalName);

    /**
     * Creates a new SignalDefinitionRepository
     *
     * @param EnvironmentInterface $environment
     * @param SignalDefinitionCollectionInterface $appSignalDefinitionCollection
     * @return SignalDefinitionRepositoryInterface
     */
    public function createSignalDefinitionRepository(
        EnvironmentInterface $environment,
        SignalDefinitionCollectionInterface $appSignalDefinitionCollection
    );
}
