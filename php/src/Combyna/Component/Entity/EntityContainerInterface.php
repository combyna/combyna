<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Entity;

/**
 * Interface EntityContainerInterface
 *
 *
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface EntityContainerInterface
{
    /**
     * Determines whether this contains an entity with the given slug
     *
     * @param string $slug
     * @return bool
     */
    public function hasEntityBySlug($slug);
}
