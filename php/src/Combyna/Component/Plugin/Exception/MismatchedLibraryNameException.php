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
 * Class MismatchedLibraryNameException.
 *
 * Thrown when the config specifies a different library name.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class MismatchedLibraryNameException extends Exception
{
    /**
     * @var array
     */
    private $libraryConfig;
    /**
     * @var string
     */
    private $libraryName;
    /**
     * @var string
     */
    private $libraryNameFromConfig;

    /**
     * @param string $libraryName
     * @param string $libraryNameFromConfig
     * @param array $libraryConfig
     */
    public function __construct($libraryName, $libraryNameFromConfig, array $libraryConfig)
    {
        parent::__construct(sprintf(
            'Mismatched "name" value for library "%s", "%s" given in config',
            $libraryName,
            $libraryNameFromConfig
        ));

        $this->libraryConfig = $libraryConfig;
        $this->libraryName = $libraryName;
        $this->libraryNameFromConfig = $libraryNameFromConfig;
    }

    /**
     * Fetches the invalid config that was given.
     *
     * @return array
     */
    public function getLibraryConfig()
    {
        return $this->libraryConfig;
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

    /**
     * Fetches the library name that was given in the config.
     *
     * @return string
     */
    public function getLibraryNameFromConfig()
    {
        return $this->libraryNameFromConfig;
    }
}
