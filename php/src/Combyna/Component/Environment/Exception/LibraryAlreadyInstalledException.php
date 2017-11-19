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
 * Class LibraryAlreadyInstalledException
 *
 * Thrown when an attempt is made to install a library that is already installed
 * in the current environment
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class LibraryAlreadyInstalledException extends Exception
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
            'A library with name "' . $libraryName . '" is already installed'
        );

        $this->libraryName = $libraryName;
    }

    /**
     * Fetches the name of the already-installed library
     *
     * @return string
     */
    public function getLibraryName()
    {
        return $this->libraryName;
    }
}
