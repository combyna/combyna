<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal\Exception;

use Exception;

/**
 * Class SignalDefinitionNotFoundException
 *
 * Thrown when an attempt is made to fetch a signal definition from a library
 * when that library implements no such signal definition
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalDefinitionNotFoundException extends Exception
{
    /**
     * @var string
     */
    private $libraryName;

    /**
     * @var string
     */
    private $signalName;

    /**
     * @param string $libraryName
     * @param string $signalName
     */
    public function __construct($libraryName, $signalName)
    {
        parent::__construct(
            'Library "' . $libraryName . '" does not define signal definition "' . $signalName . '"'
        );

        $this->libraryName = $libraryName;
        $this->signalName = $signalName;
    }

    /**
     * Fetches the name of the library that does not support the requested signal definition
     *
     * @return string
     */
    public function getLibraryName()
    {
        return $this->libraryName;
    }

    /**
     * Fetches the name of the unsupported signal definition
     *
     * @return string
     */
    public function getSignalDefinitionName()
    {
        return $this->signalName;
    }
}
