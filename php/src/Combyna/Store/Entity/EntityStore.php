<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Store\Entity;

/**
 * Class EntityStore
 *
 * Contains a set of entity repositories, each of which holds an entity model and sets of entities of that model
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EntityStore
{
    /**
     * @var EntityRepository[]
     */
    private $entityRepositories = [];
}
