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
 * Class EarlyDelegateeTagDefinition
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EarlyDelegateeTagDefinition
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
     * Add the delegatee to the delegator
     *
     * @param ContainerBuilder $containerBuilder
     */
    public function install(ContainerBuilder $containerBuilder)
    {
        $delegatorService = $containerBuilder->findDefinition($this->delegatorId);

        foreach ($containerBuilder->findTaggedServiceIds($this->tag) as $delegateeServiceId => $tags) {
            $delegatorService->addMethodCall(
                $this->delegateeInstallerMethodName,
                [
                    new Reference($delegateeServiceId)
                ]
            );
        }
    }
}
