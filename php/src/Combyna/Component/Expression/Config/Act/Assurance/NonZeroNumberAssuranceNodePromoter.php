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

use Combyna\Component\Expression\Assurance\NonZeroNumberAssurance;
use Combyna\Component\Expression\Config\Act\ExpressionNodePromoterInterface;

/**
 * Class NonZeroNumberAssuranceNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NonZeroNumberAssuranceNodePromoter implements AssuranceNodeTypePromoterInterface
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
            NonZeroNumberAssuranceNode::TYPE => [$this, 'promote']
        ];
    }

    /**
     * Promotes a NonZeroNumberAssuranceNode to a NonZeroNumberAssurance
     *
     * @param NonZeroNumberAssuranceNode $assuranceNode
     * @return NonZeroNumberAssurance
     */
    public function promote(NonZeroNumberAssuranceNode $assuranceNode)
    {
        $inputExpression = $this->expressionNodePromoter->promote($assuranceNode->getInputExpression());

        return new NonZeroNumberAssurance(
            $inputExpression,
            $assuranceNode->getAssuredStaticName()
        );
    }
}
