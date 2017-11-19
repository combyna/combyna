<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment\Exception;

use Exception;

/**
 * Class LibraryNotInstalledException
 *
 * Thrown when an attempt is made to fetch a function from a library
 * when that library implements no such function
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class FunctionNotSupportedException extends Exception
{
    /**
     * @var string
     */
    private $functionName;

    /**
     * @var string
     */
    private $libraryName;

    /**
     * @param string $libraryName
     * @param string $functionName
     */
    public function __construct($libraryName, $functionName)
    {
        parent::__construct(
            'Library "' . $libraryName . '" does not define function "' . $functionName . '"'
        );

        $this->functionName = $functionName;
        $this->libraryName = $libraryName;
    }

    /**
     * Fetches the name of the unsupported function
     *
     * @return string
     */
    public function getFunctionName()
    {
        return $this->functionName;
    }

    /**
     * Fetches the name of the library that does not support the requested function
     *
     * @return string
     */
    public function getLibraryName()
    {
        return $this->libraryName;
    }
}
