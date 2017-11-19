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

/**
 * Class SignalHandlerCollection
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalHandlerCollection implements SignalHandlerCollectionInterface
{
    /**
     * @var string
     */
    private $libraryName;

    /**
     * @var SignalHandlerInterface[]
     */
    private $signalHandlers = [];

    /**
     * @param SignalHandlerInterface[] $signalHandlers
     * @param string $libraryName
     */
    public function __construct(array $signalHandlers, $libraryName)
    {
        $this->libraryName = $libraryName;

        // Index the signal handlers by name to simplify lookups
        foreach ($signalHandlers as $signalHandler) {
            $this->signalHandlers[$signalHandler->getName()] = $signalHandler;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getByName($signalName)
    {
        if (!array_key_exists($signalName, $this->signalHandlers)) {
            throw new SignalHandlerNotFoundException($this->libraryName, $signalName);
        }

        return $this->signalHandlers[$signalName];
    }
}
