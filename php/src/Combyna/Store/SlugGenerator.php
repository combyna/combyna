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

use Combyna\Component\Entity\EntityContainerInterface;

/**
 * Class SlugGenerator
 *
 * Utility class for generating unique slugs (readable basic identifiers) for entities
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SlugGenerator
{
    /**
     * Generates a unique slug for entities in the specified container
     *
     * @param EntityContainerInterface $container
     * @param string $prefix
     * @return string
     */
    public function generateSlug(EntityContainerInterface $container, $prefix)
    {
        $slug = $prefix;

        if (!$container->hasEntityBySlug($slug)) {
            return $slug;
        }

        $index = 2;

        while ($container->hasEntityBySlug($slug . '-' . $index)) {
            $index++;
        }

        return $slug . '-' . $index;
    }
}
