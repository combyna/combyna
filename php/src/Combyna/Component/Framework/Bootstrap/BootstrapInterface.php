<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Framework\Bootstrap;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Interface BootstrapInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface BootstrapInterface
{
    /**
     * Fetches the service container, creating it on first access
     *
     * @return ContainerInterface
     */
    public function getContainer();

    /**
     * Fetches the service container builder
     *
     * @return ContainerBuilder
     */
    public function getContainerBuilder();

    /**
     * Warms up the cache
     */
    public function warmUp();
}
