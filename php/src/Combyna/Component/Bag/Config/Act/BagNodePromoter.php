<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Bag\Config\Act;

use Combyna\Component\Bag\BagFactoryInterface;
use Combyna\Component\Bag\ExpressionBagInterface;
use Combyna\Component\Bag\ExpressionListInterface;
use Combyna\Component\Bag\FixedStaticBagModelInterface;
use Combyna\Component\Bag\FixedStaticDefinition;
use Combyna\Component\Expression\Config\Act\DelegatingExpressionNodePromoter;
use Combyna\Component\Framework\Context\ModeContext;
use Combyna\Component\Type\Config\Act\TypePromoterInterface;

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
     * @var DelegatingExpressionNodePromoter
     */
    private $expressionNodePromoter;

    /**
     * @var ModeContext
     */
    private $modeContext;

    /**
     * @var TypePromoterInterface
     */
    private $typePromoter;

    /**
     * @param BagFactoryInterface $bagFactory
     * @param DelegatingExpressionNodePromoter $expressionNodePromoter
     * @param TypePromoterInterface $typePromoter
     * @param ModeContext $modeContext
     */
    public function __construct(
        BagFactoryInterface $bagFactory,
        DelegatingExpressionNodePromoter $expressionNodePromoter,
        TypePromoterInterface $typePromoter,
        ModeContext $modeContext
    ) {
        $this->bagFactory = $bagFactory;
        $this->expressionNodePromoter = $expressionNodePromoter;
        $this->modeContext = $modeContext;
        $this->typePromoter = $typePromoter;
    }

    /**
     * Promotes an ExpressionBagNode to an ExpressionBag
     *
     * @param ExpressionBagNode $expressionBagNode
     * @return ExpressionBagInterface
     */
    public function promoteExpressionBag(ExpressionBagNode $expressionBagNode)
    {
        $expressions = [];

        foreach ($expressionBagNode->getExpressions() as $expressionName => $expressionNode) {
            $expressions[$expressionName] = $this->expressionNodePromoter->promote($expressionNode);
        }

        return $this->bagFactory->createExpressionBag($expressions);
    }

    /**
     * Promotes an ExpressionListNode to an ExpressionList
     *
     * @param ExpressionListNode $expressionListNode
     * @return ExpressionListInterface
     */
    public function promoteExpressionList(ExpressionListNode $expressionListNode)
    {
        $expressions = [];

        foreach ($expressionListNode->getExpressions() as $expressionNode) {
            $expressions[] = $this->expressionNodePromoter->promote($expressionNode);
        }

        return $this->bagFactory->createExpressionList($expressions);
    }

    /**
     * Promotes a FixedStaticBagModelNode to a FixedStaticBagModel
     *
     * @param FixedStaticBagModelNodeInterface $modelNode
     * @return FixedStaticBagModelInterface
     */
    public function promoteFixedStaticBagModel(FixedStaticBagModelNodeInterface $modelNode)
    {
        $staticDefinitions = [];

        foreach ($modelNode->getStaticDefinitions() as $definitionNode) {
            $staticDefinitions[] = $this->promoteFixedStaticDefinition($definitionNode);
        }

        return $this->bagFactory->createFixedStaticBagModel($staticDefinitions);
    }

    /**
     * Promotes a FixedStaticDefinitionNode to a FixedStaticDefinition
     *
     * @param FixedStaticDefinitionNodeInterface $definitionNode
     * @return FixedStaticDefinition
     */
    public function promoteFixedStaticDefinition(FixedStaticDefinitionNodeInterface $definitionNode)
    {
        // Allow the static to specify a default value
        $defaultExpression = $definitionNode->getDefaultExpression() ?
            $this->expressionNodePromoter->promote($definitionNode->getDefaultExpression()) :
            null;

        return $this->bagFactory->createFixedStaticDefinition(
            $definitionNode->getName(),
            // In development mode, fetch the resolved type of the static
            // (which can depend on the other statics in the bag)
            // - otherwise for production mode, the behaviour spec tree won't have been loaded
            // so we use the type if it is a simple one, otherwise just allow any type
            $this->typePromoter->promote($definitionNode->getResolvedStaticType()),
            $defaultExpression
        );
    }
}
