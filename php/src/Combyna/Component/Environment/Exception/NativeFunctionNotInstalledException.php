<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment\Exception;

use Exception;

/**
 * Class NativeFunctionNotInstalledException
 *
 * Thrown when an attempt is made to fetch a native function that is not installed
 * into a NativeFunctionNode that references it
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NativeFunctionNotInstalledException extends Exception
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
        parent::__construct('Native function "' . $libraryName . '" was never installed');

        $this->functionName = $functionName;
        $this->libraryName = $libraryName;
    }

    /**
     * Fetches the name of the library
     *
     * @return string
     */
    public function getLibraryName()
    {
        return $this->libraryName;
    }

    /**
     * Fetches the name of the non-installed function
     *
     * @return string
     */
    public function getFunctionName()
    {
        return $this->functionName;
    }
}
