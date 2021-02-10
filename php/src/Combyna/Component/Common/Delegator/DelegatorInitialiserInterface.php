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

/**
 * INterface DelegatorInitialiserInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface DelegatorInitialiserInterface
{
    const SERVICE_ID = 'combyna.common.delegator_initialiser';

    /**
     * Adds a new delegatee to be installed in the delegator when it is initialised
     *
     * @param string $delegatorId
     * @param string $delegateeId
     * @param string $delegateeInstallerMethodName
     */
    public function addDelegatee($delegatorId, $delegateeId, $delegateeInstallerMethodName);

    /**
     * Initialises the specified delegator's delegatees
     *
     * @param DelegatorInterface $delegator
     */
    public function initialise(DelegatorInterface $delegator);
}
