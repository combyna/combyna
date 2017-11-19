<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Act;

use Combyna\Component\Expression\ExpressionInterface;

/**
 * Interface ExpressionNodeTypePromoterInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ExpressionNodeTypePromoterInterface
{
    /**
     * Fetches the types of expression this promoter can promote
     *
     * @return string[]
     */
    public function getTypes();

    /**
     * Promotes an expression ACT node to a real expression object
     *
     * @param ExpressionNodeInterface $expressionNode
     * @return ExpressionInterface
     */
    public function promote(ExpressionNodeInterface $expressionNode);
}
