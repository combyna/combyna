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
 * Interface ExpressionNodePromoterInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ExpressionNodePromoterInterface
{
    /**
     * Adds a promoter for a new type of expression node type
     *
     * @param ExpressionNodeTypePromoterInterface $typePromoter
     */
    public function addPromoter(ExpressionNodeTypePromoterInterface $typePromoter);

    /**
     * Promotes an ExpressionNodeInterface to an Expression
     *
     * @param ExpressionNodeInterface $expressionNode
     * @return ExpressionInterface
     */
    public function promote(ExpressionNodeInterface $expressionNode);
}
