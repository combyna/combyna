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
 * Interface RepositoryPageInterface
 *
 * Holds a page of entities of a given model for a repository
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface RepositoryPageInterface
{
    /**
     * Determines whether this repository contains an entity with the given slug
     *
     * @param string $slug
     * @return bool
     */
    public function hasEntityBySlug($slug);
}
