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

use Combyna\Component\Common\Delegator\DelegatorInterface;
use Combyna\Component\Expression\Config\Act\ExpressionNodePromoterInterface;
use InvalidArgumentException;

/**
 * Class DelegatingAssuranceNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DelegatingAssuranceNodePromoter implements AssuranceNodePromoterInterface, DelegatorInterface
{
    /**
     * @var callable[]
     */
    private $assurancePromoters = [];

    /**
     * @var ExpressionNodePromoterInterface
     */
    private $expressionNodePromoter;

    /**
     * @param ExpressionNodePromoterInterface $expressionNodePromoter
     */
    public function __construct(ExpressionNodePromoterInterface $expressionNodePromoter)
    {
        $this->expressionNodePromoter = $expressionNodePromoter;
    }

    /**
     * Adds a promoter for a new type of assurance node type
     *
     * @param AssuranceNodeTypePromoterInterface $assuranceNodePromoter
     */
    public function addPromoter(AssuranceNodeTypePromoterInterface $assuranceNodePromoter)
    {
        foreach ($assuranceNodePromoter->getTypeToPromoterCallableMap() as $type => $promoterCallable) {
            $this->assurancePromoters[$type] = $promoterCallable;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function promote(AssuranceNodeInterface $assuranceNode)
    {
        if (!array_key_exists($assuranceNode->getType(), $this->assurancePromoters)) {
            throw new InvalidArgumentException(sprintf(
                'No promoter for assurances of type "%s" is registered',
                $assuranceNode->getType()
            ));
        }

        return $this->assurancePromoters[$assuranceNode->getType()]($assuranceNode);
    }

    /**
     * {@inheritdoc}
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
