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

use Combyna\Component\Expression\Assurance\KnownTypeValueAssurance;
use Combyna\Component\Expression\Config\Act\ExpressionNodePromoterInterface;
use Combyna\Component\Validator\Context\NullValidationContext;

/**
 * Class KnownTypeValueAssuranceNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class KnownTypeValueAssuranceNodePromoter implements AssuranceNodeTypePromoterInterface
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
            KnownTypeValueAssuranceNode::TYPE => [$this, 'promote']
        ];
    }

    /**
     * Promotes a KnownTypeValueAssuranceNode to a KnownTypeValueAssurance
     *
     * @param KnownTypeValueAssuranceNode $assuranceNode
     * @return KnownTypeValueAssurance
     */
    public function promote(KnownTypeValueAssuranceNode $assuranceNode)
    {
        $inputExpression = $this->expressionNodePromoter->promote($assuranceNode->getInputExpression());
        $knownType = $assuranceNode->getAssuredStaticTypeDeterminer()
            ->determine(new NullValidationContext());

        return new KnownTypeValueAssurance(
            $inputExpression,
            $assuranceNode->getAssuredStaticName(),
            $knownType
        );
    }
}
