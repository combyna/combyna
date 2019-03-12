<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Integrated;

use Combyna\Harness\TestCase;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class ContainerIntegratedTest
 *
 * Checks that all defined services may be instantiated
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ContainerIntegratedTest extends TestCase
{
    /**
     * @dataProvider serviceIdProvider
     * @param string $serviceId
     */
    public function testServiceCanBeCreated($serviceId)
    {
        global $combynaBootstrap; // Use the one from bootstrap.php so that all the test plugins are loaded etc.
        $container = $combynaBootstrap->createContainer();

        // Try to get the service to ensure it can be constructed
        $container->get($serviceId);
    }

    /**
     * Returns the service IDs to test
     *
     * @return array
     */
    public function serviceIdProvider()
    {
        global $combynaBootstrap; // Use the one from bootstrap.php so that all the test plugins are loaded etc.
        $calls = [];

        /** @var Container $container */
        $container = $combynaBootstrap->createContainer();

        foreach ($container->getServiceIds() as $id) {
            if (
                // Ignore Symfony's special `request` service
                $id === 'request'
            ) {
                continue;
            }

            $calls['service #' . $id] = [$id];
        }

        return $calls;
    }
}
