<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Interface CombynaBootstrapInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface CombynaBootstrapInterface
{
    /**
     * Allows the compiled Symfony DI container to be customised
     *
     * @param string|null $cachePath
     * @param string|null $compiledContainerNamespace
     * @param string $compiledContainerClass
     */
    public function configureContainer(
        $cachePath = null,
        $compiledContainerNamespace = null,
        $compiledContainerClass = null
    );

    /**
     * Creates a new Combyna service container
     *
     * @return ContainerInterface
     */
    public function createContainer();

    /**
     * Fetches the path to the common cache
     *
     * @return string
     */
    public function getCommonCachePath();

    /**
     * Fetches the service container builder
     *
     * @return ContainerBuilder
     */
    public function getContainerBuilder();

    /**
     * Fetches the path to the container cache
     *
     * @return string
     */
    public function getContainerCachePath();

    /**
     * Warms up the cache
     */
    public function warmUp();
}
