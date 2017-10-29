<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Integrated;

use Combyna\CombynaBootstrap;
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
        $container = (new CombynaBootstrap())->getContainer();

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
        $calls = [];

        /** @var Container $container */
        $container = (new CombynaBootstrap())->getContainer();

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
