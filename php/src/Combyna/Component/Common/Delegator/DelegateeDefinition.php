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

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class DelegateeDefinition
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DelegateeDefinition
{
    /**
     * @var string
     */
    private $delegateeId;

    /**
     * @var string
     */
    private $delegateeInstallerMethodName;

    /**
     * @var string
     */
    private $delegatorId;

    /**
     * @param string $delegatorId
     * @param string $delegateeId
     * @param string $delegateeInstallerMethodName
     */
    public function __construct($delegatorId, $delegateeId, $delegateeInstallerMethodName)
    {
        $this->delegateeId = $delegateeId;
        $this->delegateeInstallerMethodName = $delegateeInstallerMethodName;
        $this->delegatorId = $delegatorId;
    }

    /**
     * Installs the delegatee in its delegator
     *
     * @param ContainerInterface $container
     */
    public function install(ContainerInterface $container)
    {
        $delegator = $container->get($this->delegatorId);
        $delegatee = $container->get($this->delegateeId);

        $delegator->{$this->delegateeInstallerMethodName}($delegatee);
    }
}
