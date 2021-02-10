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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class DelegateeTagDefinition
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DelegateeTagDefinition
{
    /**
     * @var string
     */
    private $delegateeInstallerMethodName;

    /**
     * @var string
     */
    private $delegatorId;

    /**
     * @var string
     */
    private $tag;

    /**
     * @param string $tag
     * @param string $delegatorId
     * @param string $delegateeInstallerMethodName
     */
    public function __construct($tag, $delegatorId, $delegateeInstallerMethodName = 'addDelegatee')
    {
        $this->delegateeInstallerMethodName = $delegateeInstallerMethodName;
        $this->delegatorId = $delegatorId;
        $this->tag = $tag;
    }

    /**
     * Add the delegatee, delegator and its installer method tuple to the initialiser
     *
     * @param ContainerBuilder $containerBuilder
     */
    public function install(ContainerBuilder $containerBuilder)
    {
        $delegatorService = $containerBuilder->findDefinition($this->delegatorId);

        if ($delegatorService->getConfigurator() === null) {
            // Make sure that when a delegator is fetched, its delegatees are registered
            $delegatorService->setConfigurator([
                new Reference(DelegatorInitialiserInterface::SERVICE_ID),
                'initialise'
            ]);
        }

        $delegatorInitialiserService = $containerBuilder->findDefinition(DelegatorInitialiserInterface::SERVICE_ID);

        foreach ($containerBuilder->findTaggedServiceIds($this->tag) as $delegateeServiceId => $tags) {
            $delegatorInitialiserService->addMethodCall(
                'addDelegatee',
                [
                    $this->delegatorId,
                    $delegateeServiceId,
                    $this->delegateeInstallerMethodName
                ]
            );
        }
    }
}
