<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Store;

/**
 * Interface RemoteRepositoryPageInterface
 *
 * Holds a single page of entities of a given model for a repository
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface RemoteRepositoryPageInterface extends RepositoryPageInterface
{
    /**
     * Fetches the index of this page
     *
     * @return int
     */
    public function getIndex();
}
