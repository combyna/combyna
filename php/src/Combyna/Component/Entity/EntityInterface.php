<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Entity;

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Expression\StaticInterface;

/**
 * Interface EntityInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface EntityInterface
{
    const LOCAL_ENTITY_SLUG_PREFIX = 'loc-';
    const REMOTE_ENTITY_SLUG_PREFIX = 'rem-';

    /**
     * Determines whether this entity defines a command with the specified name
     *
     * @param string $commandName
     * @return bool
     */
    public function definesCommand($commandName);

    /**
     * Determines whether this entity defines a query with the specified name
     *
     * @param string $queryName
     * @return bool
     */
    public function definesQuery($queryName);

    /**
     * Returns the unique name of the model of this entity
     *
     * @return string
     */
    public function getModelName();

    /**
     * Makes a query against this entity and returns its static result
     *
     * @param string $queryName
     * @param StaticBagInterface $argumentStaticBag
     * @return StaticInterface
     */
    public function makeQuery($queryName, StaticBagInterface $argumentStaticBag);

    /**
     * Performs a command on this entity
     *
     * @param string $commandName
     * @param StaticBagInterface $argumentStaticBag
     */
    public function performCommand($commandName, StaticBagInterface $argumentStaticBag);
}
