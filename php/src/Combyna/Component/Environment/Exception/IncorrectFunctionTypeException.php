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

use Combyna\Component\Environment\Library\FunctionInterface;
use Combyna\Component\Environment\Library\LibraryInterface;
use Exception;

/**
 * Class IncorrectFunctionTypeException
 *
 * Thrown when an attempt is made to fetch a function from a library
 * but that function is of a different type to the one requested
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class IncorrectFunctionTypeException extends Exception
{
    /**
     * @var string
     */
    private $actualType;

    /**
     * @var FunctionInterface
     */
    private $function;

    /**
     * @var LibraryInterface
     */
    private $library;

    /**
     * @var Exception
     */
    private $requestedType;

    /**
     * @param LibraryInterface $library
     * @param FunctionInterface $function
     * @param Exception $requestedType
     * @param string $actualType
     */
    public function __construct(LibraryInterface $library, FunctionInterface $function, $requestedType, $actualType)
    {
        parent::__construct(
            'Library "' . $library->getName() . '" defines "' . $function->getName() . '" ' .
            'of type "' . $actualType . '" but needs to be of type "' . $requestedType . '"'
        );

        $this->actualType = $actualType;
        $this->function = $function;
        $this->library = $library;
        $this->requestedType = $requestedType;
    }

    /**
     * Fetches the function
     *
     * @return FunctionInterface
     */
    public function getFunction()
    {
        return $this->function;
    }

    /**
     * Fetches the library
     *
     * @return LibraryInterface
     */
    public function getLibrary()
    {
        return $this->library;
    }
}
