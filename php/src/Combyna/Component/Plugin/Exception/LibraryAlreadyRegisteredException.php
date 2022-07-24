<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Plugin\Exception;

use Exception;

/**
 * Class LibraryAlreadyRegisteredException.
 *
 * Thrown when an attempt is made to install a library that is already registered.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class LibraryAlreadyRegisteredException extends Exception
{
    /**
     * @var string
     */
    private $libraryName;

    /**
     * @param string $libraryName
     */
    public function __construct($libraryName)
    {
        parent::__construct(
            'A library with name "' . $libraryName . '" was already registered'
        );

        $this->libraryName = $libraryName;
    }

    /**
     * Fetches the name of the already-registered library.
     *
     * @return string
     */
    public function getLibraryName()
    {
        return $this->libraryName;
    }
}
