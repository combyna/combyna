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

/**
 * Class LibraryConfigCollection
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
     * Adds a new library's config to the collection
     *
     * @param array $libraryConfig
     */
    public function addLibraryConfig(array $libraryConfig)
    {
        $this->libraryConfigs[] = $libraryConfig;
    }

    /**
     * Fetches all library configs added to this collection.
     * Plugins may register zero or more libraries, see AbstractPlugin
     *
     * @return array
     */
    public function getLibraryConfigs()
    {
        return $this->libraryConfigs;
    }
}
