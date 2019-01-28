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
     * @param string|null $compiledContainerPath
     * @param string|null $compiledContainerNamespace
     * @param string $compiledContainerClass
     */
    public function configureContainer(
        $compiledContainerPath = null,
        $compiledContainerNamespace = null,
        $compiledContainerClass = null
    );

    /**
     * Fetches or builds the service container
     *
     * @param bool $isDebug
     * @return ContainerInterface
     */
    public function getContainer($isDebug = true);
}
