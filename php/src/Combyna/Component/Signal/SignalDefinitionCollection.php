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

use Combyna\Component\Signal\Exception\SignalDefinitionNotFoundException;

/**
 * Class SignalDefinitionCollection
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalDefinitionCollection implements SignalDefinitionCollectionInterface
{
    /**
     * @var string
     */
    private $libraryName;

    /**
     * @var SignalDefinitionInterface[]
     */
    private $signalDefinitions = [];

    /**
     * @param SignalDefinitionInterface[] $signalDefinitions
     * @param string $libraryName
     */
    public function __construct(array $signalDefinitions, $libraryName)
    {
        $this->libraryName = $libraryName;

        // Index the signal definitions by name to simplify lookups
        foreach ($signalDefinitions as $signalDefinition) {
            $this->signalDefinitions[$signalDefinition->getName()] = $signalDefinition;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getByName($signalName)
    {
        if (!array_key_exists($signalName, $this->signalDefinitions)) {
            throw new SignalDefinitionNotFoundException($this->libraryName, $signalName);
        }

        return $this->signalDefinitions[$signalName];
    }
}
