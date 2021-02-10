<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Act\Assurance;

use Combyna\Component\Expression\Assurance\AssuranceInterface;

/**
 * Interface AssuranceNodePromoterInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface AssuranceNodePromoterInterface
{
    /**
     * Promotes the provided node to an actual Assurance
     *
     * @param AssuranceNodeInterface $assuranceNode
     * @return AssuranceInterface
     */
    public function promote(AssuranceNodeInterface $assuranceNode);

    /**
     * Promotes the provided list of nodes to actual Assurances
     *
     * @param AssuranceNodeInterface[] $assuranceNodes
     * @return AssuranceInterface[]
     */
    public function promoteCollection(array $assuranceNodes);
}
