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
 * Thrown when an attempt is made to fetch a library that is not installed
 * into the current environment
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class LibraryNotInstalledException extends Exception
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
        parent::__construct('Library "' . $libraryName . '" is not installed');

        $this->libraryName = $libraryName;
    }

    /**
     * Fetches the name of the non-installed library
     *
     * @return string
     */
    public function getLibraryName()
    {
        return $this->libraryName;
    }
}
