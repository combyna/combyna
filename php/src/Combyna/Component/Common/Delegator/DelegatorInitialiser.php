<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Common\Delegator;

use Closure;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class DelegatorInitialiser
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DelegatorInitialiser implements DelegatorInitialiserInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var DelegateeDefinition[]
     */
    private $delegateeDefinitions = [];

    /**
     * @var bool
     */
    private $done = false;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function addDelegatee($delegatorId, $delegateeId, $delegateeInstallerMethodName)
    {
        $this->delegateeDefinitions[] = new DelegateeDefinition(
            $delegatorId,
            $delegateeId,
            $delegateeInstallerMethodName
        );
    }

    /**
     * {@inheritdoc}
     */
    public function initialise(DelegatorInterface $delegator)
    {
        if ($this->done) {
            return;
        }

        // Ensure these delegatees are only registered once
        $this->done = true;

        // For now, just initialise _all_ delegators with the delegatees registered
        // to solve any circular references

        /**
         * WARNING - This logic monkey-patches the behaviour of Symfony\Component\DependencyInjection\Container
         *           by modifying a protected property. This makes it coupled to implementation details
         *           of the Symfony framework and gives it the potential to break when used with future versions
         *           of Symfony. We should investigate replacing this with a cleaner hook-based approach,
         *           which may involve changes to Symfony internals.
         */

        // We need this closure as it can stay bound to the current object & class,
        // as we need to call out from the closure below that gets bound to the service container
        $initialise = function () {
            $this->doInitialise();
        };

        // Avoid a circular dependency issue if this delegator is depended on by anything in the tree
        $makeLoadingHookable = Closure::bind(function () use ($initialise) {
            $entryServiceId = array_keys($this->loading)[0];
            $hookableLoadingList = new HookableArrayObject($this->loading);

            $this->loading = $hookableLoadingList;

            $hookableLoadingList->onIsSet(function ($serviceId, $isSet) use ($hookableLoadingList) {
                if ($isSet) {
                    // Symfony has detected a circular service reference -
                    // restore the ->loading prop to an array
                    // so that it can be passed to array_keys(...)
                    $this->loading = $hookableLoadingList->toArray();
                }
            });
            $hookableLoadingList->onUnset(
                function (
                    $serviceId
                ) use (
                    $entryServiceId,
                    $hookableLoadingList,
                    $initialise
                ) {
                    if ($serviceId === $entryServiceId) {
                        // The service that was initially requested has finished loading,
                        // so we can initialise all of the delegators with their delegatees
                        $initialise();

                        // Restore the ->loading prop back to an array,
                        // so that we no longer impact the performance of the service container
                        $this->loading = $hookableLoadingList->toArray();
                    }
                }
            );
        }, $this->container, Container::class);

        $makeLoadingHookable();
    }

    private function doInitialise()
    {
        foreach ($this->delegateeDefinitions as $definition) {
            $definition->install($this->container);
        }
    }
}
