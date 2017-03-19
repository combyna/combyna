<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression;

use Combyna\Component\Bag\StaticListInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Type\TypeInterface;

/**
 * Class StaticListExpression
 *
 * Represents a list of static values
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StaticListExpression extends AbstractStaticExpression
{
    const TYPE = 'static-list';

    /**
     * @var StaticExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @var StaticListInterface
     */
    private $staticList;

    /**
     * @param StaticExpressionFactoryInterface $expressionFactory
     * @param StaticListInterface $staticList
     */
    public function __construct(StaticExpressionFactoryInterface $expressionFactory, StaticListInterface $staticList)
    {
        $this->expressionFactory = $expressionFactory;
        $this->staticList = $staticList;
    }

    /**
     * Returns a text static with all elements of the list concatenated together
     *
     * @param string $glue
     * @return TextExpression
     */
    public function concatenate($glue = '')
    {
        return $this->expressionFactory->createTextExpression($this->staticList->concatenate($glue));
    }

    /**
     * Returns true if all the elements of this list match the provided type, false otherwise
     *
     * @param TypeInterface $type
     * @return bool
     */
    public function elementsMatch(TypeInterface $type)
    {
        return $this->staticList->elementsMatch($type);
    }

    /**
     * Maps this static list to another, transforming each element with the given expression
     *
     * @param string $itemVariableName
     * @param string|null $indexVariableName
     * @param ExpressionInterface $mapExpression
     * @param EvaluationContextInterface $evaluationContext
     * @return StaticListExpression
     */
    public function map(
        $itemVariableName,
        $indexVariableName,
        ExpressionInterface $mapExpression,
        EvaluationContextInterface $evaluationContext
    ) {
        $staticList = $this->staticList->map(
            $itemVariableName,
            $indexVariableName,
            $mapExpression,
            $evaluationContext
        );

        return $this->expressionFactory->createStaticListExpression($staticList);
    }

    /**
     * {@inheritdoc}
     */
    public function toNative()
    {
        return $this->staticList->toArray();
    }
}
