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

use Combyna\Component\Expression\Config\Act\DelegatingExpressionNodePromoter;
use Combyna\Component\Expression\Assurance\AssuranceInterface;
use Combyna\Component\Expression\ExpressionFactoryInterface;

/**
 * Class AssuranceNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AssuranceNodePromoter
{
    /**
     * @var ExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @var DelegatingExpressionNodePromoter
     */
    private $expressionNodePromoter;

    /**
     * @param ExpressionFactoryInterface $expressionFactory
     * @param DelegatingExpressionNodePromoter $expressionNodePromoter
     */
    public function __construct(
        ExpressionFactoryInterface $expressionFactory,
        DelegatingExpressionNodePromoter $expressionNodePromoter
    ) {
        $this->expressionFactory = $expressionFactory;
        $this->expressionNodePromoter = $expressionNodePromoter;
    }

    /**
     * Promotes the provided node to an actual Assurance
     *
     * @param AssuranceNodeInterface $assuranceNode
     * @return AssuranceInterface
     */
    public function promote(AssuranceNodeInterface $assuranceNode)
    {
        return $assuranceNode->promote(
            $this->expressionFactory,
            $this->expressionNodePromoter
        );
    }

    /**
     * Promotes the provided list of nodes to actual Assurances
     *
     * @param AssuranceNodeInterface[] $assuranceNodes
     * @return AssuranceInterface[]
     */
    public function promoteCollection(array $assuranceNodes)
    {
        $assurances = [];

        foreach ($assuranceNodes as $assuranceNode) {
            $assurances[] = $this->promote($assuranceNode);
        }

        return $assurances;
    }
}
