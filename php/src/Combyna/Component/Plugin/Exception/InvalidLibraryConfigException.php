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
 * Class InvalidLibraryConfigException.
 *
 * Thrown when the config of a library being installed is invalid.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class InvalidLibraryConfigException extends Exception
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
    private $reason;

    /**
     * @param string $libraryName
     * @param string $reason
     * @param array $libraryConfig
     */
    public function __construct($libraryName, $reason, array $libraryConfig)
    {
        parent::__construct(sprintf(
            'Library "%s" config is invalid due to %s: %s',
            $libraryName,
            $reason,
            var_export($libraryConfig, true)
        ));

        $this->libraryConfig = $libraryConfig;
        $this->libraryName = $libraryName;
        $this->reason = $reason;
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
     * Fetches the reason why the config is invalid.
     *
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }
}
