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
 * Class DelegatorInitialiser
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DelegatorInitialiser
{
    const SERVICE_ID = 'combyna.framework.delegator_initialiser';

    /**
     * @var DelegateeDefinition[]
     */
    private $delegateeDefinitions = [];

    /**
     * Adds a new delegatee to be installed in the delegator when it is initialised
     *
     * @param DelegatorInterface $delegator
     * @param object $delegatee
     * @param string $delegateeInstallerMethodName
     */
    public function addDelegatee(DelegatorInterface $delegator, $delegatee, $delegateeInstallerMethodName)
    {
        $this->delegateeDefinitions[] = new DelegateeDefinition($delegator, $delegatee, $delegateeInstallerMethodName);
    }

    /**
     * Initialises all delegators with the delegatees registered
     */
    public function initialise()
    {
        foreach ($this->delegateeDefinitions as $definition) {
            $definition->install();
        }
    }
}
