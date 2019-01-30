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

use Combyna\Component\Expression\Config\Act\ExpressionNodePromoterInterface;
use LogicException;

/**
 * Class UnknownAssuranceConstraintNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UnknownAssuranceConstraintNodePromoter implements AssuranceNodeTypePromoterInterface
{
    /**
     * @var ExpressionNodePromoterInterface
     */
    private $expressionNodePromoter;

    /**
     * @param ExpressionNodePromoterInterface $expressionNodePromoter
     */
    public function __construct(
        ExpressionNodePromoterInterface $expressionNodePromoter
    ) {
        $this->expressionNodePromoter = $expressionNodePromoter;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeToPromoterCallableMap()
    {
        return [
            UnknownAssuranceConstraintNode::TYPE => [$this, 'promote']
        ];
    }

    /**
     * Ensures that attempting to promote an UnknownAssuranceNode raises an error
     *
     * @param UnknownAssuranceConstraintNode $assuranceNode
     */
    public function promote(UnknownAssuranceConstraintNode $assuranceNode)
    {
        $constraintName = $assuranceNode->getConstraint() !== null ?
            $assuranceNode->getConstraint() :
            '[missing]';
        $assuredStaticName = $assuranceNode->getAssuredStaticName() !== null ?
            $assuranceNode->getAssuredStaticName() :
            '[missing]';

        throw new LogicException(
            sprintf(
                'Assurance for assured static "%s" with unknown constraint "%s" cannot be promoted',
                $assuredStaticName,
                $constraintName
            )
        );
    }
}
