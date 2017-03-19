<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Bag\Config\Act;

use Combyna\Component\Expression\Config\Act\ExpressionNodePromoter;
use Combyna\Component\Bag\BagFactoryInterface;
use Combyna\Component\Bag\ExpressionBagInterface;
use Combyna\Component\Bag\ExpressionListInterface;
use Combyna\Component\Bag\FixedStaticBagModel;
use Combyna\Component\Bag\FixedStaticDefinition;

/**
 * Class BagNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BagNodePromoter
{
    /**
     * @var BagFactoryInterface
     */
    private $bagFactory;

    /**
     * @param BagFactoryInterface $bagFactory
     */
    public function __construct(
        BagFactoryInterface $bagFactory
    ) {
        $this->bagFactory = $bagFactory;
    }

    /**
     * Promotes an ExpressionBagNode to an ExpressionBag
     *
     * @param ExpressionBagNode $expressionBagNode
     * @param ExpressionNodePromoter $expressionNodePromoter
     * @return ExpressionBagInterface
     */
    public function promoteExpressionBag(
        ExpressionBagNode $expressionBagNode,
        ExpressionNodePromoter $expressionNodePromoter
    ) {
        $expressions = [];

        foreach ($expressionBagNode->getExpressions() as $expressionName => $expressionNode) {
            $expressions[$expressionName] = $expressionNodePromoter->promote($expressionNode);
        }

        return $this->bagFactory->createExpressionBag($expressions);
    }

    /**
     * Promotes an ExpressionListNode to an ExpressionList
     *
     * @param ExpressionListNode $expressionListNode
     * @param ExpressionNodePromoter $expressionNodePromoter
     * @return ExpressionListInterface
     */
    public function promoteExpressionList(
        ExpressionListNode $expressionListNode,
        ExpressionNodePromoter $expressionNodePromoter
    ) {
        $expressions = [];

        foreach ($expressionListNode->getExpressions() as $expressionNode) {
            $expressions[] = $expressionNodePromoter->promote($expressionNode);
        }

        return $this->bagFactory->createExpressionList($expressions);
    }

    /**
     * Promotes a FixedStaticBagModelNode to a FixedStaticBagModel
     *
     * @param FixedStaticBagModelNode $modelNode
     * @param ExpressionNodePromoter $expressionNodePromoter
     * @return FixedStaticBagModel
     */
    public function promoteFixedStaticBagModel(
        FixedStaticBagModelNode $modelNode,
        ExpressionNodePromoter $expressionNodePromoter
    ) {
        $staticDefinitions = [];

        foreach ($modelNode->getStaticDefinitions() as $definitionNode) {
            $staticDefinitions[] = $this->promoteFixedStaticDefinition(
                $definitionNode,
                $expressionNodePromoter
            );
        }

        return $this->bagFactory->createFixedStaticBagModel($staticDefinitions);
    }

    /**
     * Promotes a FixedStaticDefinitionNode to a FixedStaticDefinition
     *
     * @param FixedStaticDefinitionNode $definitionNode
     * @param ExpressionNodePromoter $expressionNodePromoter
     * @return FixedStaticDefinition
     */
    public function promoteFixedStaticDefinition(
        FixedStaticDefinitionNode $definitionNode,
        ExpressionNodePromoter $expressionNodePromoter
    ) {
        // Allow the static to specify a default value
        $defaultExpression = $definitionNode->getDefaultExpression() ?
            $expressionNodePromoter->promote($definitionNode->getDefaultExpression()) :
            null;

        return $this->bagFactory->createFixedStaticDefinition(
            $definitionNode->getName(),
            $definitionNode->getStaticType(),
            $defaultExpression
        );
    }
}
