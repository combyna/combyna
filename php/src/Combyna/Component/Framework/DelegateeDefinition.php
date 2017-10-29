<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Framework;

use Combyna\Component\Common\DelegatorInterface;

/**
 * Class DelegateeDefinition
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DelegateeDefinition
{
    /**
     * @var object
     */
    private $delegatee;

    /**
     * @var string
     */
    private $delegateeInstallerMethodName;

    /**
     * @var DelegatorInterface
     */
    private $delegator;

    /**
     * @param DelegatorInterface $delegator
     * @param object $delegatee
     * @param string $delegateeInstallerMethodName
     */
    public function __construct(DelegatorInterface $delegator, $delegatee, $delegateeInstallerMethodName)
    {
        $this->delegatee = $delegatee;
        $this->delegateeInstallerMethodName = $delegateeInstallerMethodName;
        $this->delegator = $delegator;
    }

    /**
     * Installs the delegatee in its delegator
     */
    public function install()
    {
        $this->delegator->{$this->delegateeInstallerMethodName}($this->delegatee);
    }
}
