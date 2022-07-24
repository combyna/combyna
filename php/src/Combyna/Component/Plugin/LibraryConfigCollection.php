<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Plugin;

use Combyna\Component\Plugin\Exception\InvalidLibraryConfigException;
use Combyna\Component\Plugin\Exception\LibraryAlreadyRegisteredException;
use Combyna\Component\Plugin\Exception\MismatchedLibraryNameException;

/**
 * Class LibraryConfigCollection.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class LibraryConfigCollection
{
    /**
     * @var array
     */
    private $libraryConfigs = [];

    /**
     * Adds a new library's config to the collection.
     *
     * @param string $libraryName
     * @param array $libraryConfig
     */
    public function addLibraryConfig($libraryName, array $libraryConfig)
    {
        if (!isset($libraryConfig['name'])) {
            throw new InvalidLibraryConfigException($libraryName, 'missing "name" value', $libraryConfig);
        }

        $libraryNameFromConfig = $libraryConfig['name'];

        if ($libraryNameFromConfig !== $libraryName) {
            throw new MismatchedLibraryNameException($libraryName, $libraryNameFromConfig, $libraryConfig);
        }

        if (array_key_exists($libraryName, $this->libraryConfigs)) {
            throw new LibraryAlreadyRegisteredException(sprintf(
                'A library with name "%s" was already registered',
                $libraryName
            ));
        }

        $this->libraryConfigs[$libraryName] = $libraryConfig;
    }

    /**
     * Fetches all library configs added to this collection.
     * Plugins may register zero or more libraries, see AbstractPlugin.
     *
     * @return array
     */
    public function getLibraryConfigs()
    {
        return $this->libraryConfigs;
    }
}
