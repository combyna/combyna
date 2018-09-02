<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Bag;

use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Expression\StaticExpressionFactoryInterface;
use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Type\TypeInterface;
use InvalidArgumentException;
use OutOfBoundsException;

/**
 * Interface StaticList
 *
 * Contains an ordered collection of static values
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StaticList implements StaticListInterface
{
    /**
     * @var BagFactoryInterface
     */
    private $bagFactory;

    /**
     * @var StaticExpressionFactoryInterface
     */
    private $staticExpressionFactory;

    /**
     * @var StaticInterface[]
     */
    private $statics;

    /**
     * @param BagFactoryInterface $bagFactory
     * @param StaticExpressionFactoryInterface $staticExpressionFactory
     * @param StaticInterface[] $statics
     */
    public function __construct(
        BagFactoryInterface $bagFactory,
        StaticExpressionFactoryInterface $staticExpressionFactory,
        array $statics
    ) {
        $this->assertValidStatics($statics);

        $this->bagFactory = $bagFactory;
        $this->staticExpressionFactory = $staticExpressionFactory;
        $this->statics = $statics;
    }

    /**
     * {@inheritdoc}
     */
    public function concatenate($glue = '')
    {
        $resultNatives = [];

        foreach ($this->statics as $static) {
            $resultNatives[] = $static->toNative();
        }

        return implode($glue, $resultNatives);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->statics);
    }

    /**
     * {@inheritdoc}
     */
    public function elementsMatch(TypeInterface $type)
    {
        foreach ($this->statics as $static) {
            if (!$type->allowsStatic($static)) {
                // One of the elements is not allowed by the specified type, so the match fails
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getElementStatic($index)
    {
        if (!is_int($index)) {
            throw new InvalidArgumentException('Index must be an int, ' . gettype($index) . ' given');
        }

        if (!array_key_exists($index, $this->statics)) {
            throw new OutOfBoundsException('Index is out of bounds');
        }

        return $this->statics[$index];
    }

    /**
     * {@inheritdoc}
     */
    public function map(
        $itemVariableName,
        $indexVariableName,
        ExpressionInterface $mapExpression,
        EvaluationContextInterface $evaluationContext
    ) {
        $mappedStatics = [];

        // Use the map expression as the current one
        $expressionEvaluationContext = $evaluationContext->createSubExpressionContext($mapExpression);

        foreach ($this->statics as $index => $static) {
            $variableStatics = [
                // Expose one variable with the static's value
                $itemVariableName => $static
            ];

            if ($indexVariableName !== null) {
                // Expose another variable with the current 1-based list element index as a number static
                $variableStatics[$indexVariableName] =
                    $this->staticExpressionFactory->createNumberExpression($index + 1);
            }

            $subEvaluationContext = $expressionEvaluationContext->createSubScopeContext(
                $this->bagFactory->createStaticBag($variableStatics)
            );

            $mappedStatics[] = $mapExpression->toStatic($subEvaluationContext);
        }

        return $this->bagFactory->createStaticList($mappedStatics);
    }

    /**
     * {@inheritdoc}
     */
    public function mapArray(
        $itemVariableName,
        $indexVariableName,
        callable $mapCallback,
        EvaluationContextInterface $evaluationContext
    ) {
        $resultArray = [];

        foreach ($this->statics as $index => $static) {
            $variableStatics = [
                // Expose one variable with the static's value
                $itemVariableName => $static
            ];

            if ($indexVariableName !== null) {
                // Expose another variable with the current 1-based list element index as a number static
                $variableStatics[$indexVariableName] =
                    $this->staticExpressionFactory->createNumberExpression($index + 1);
            }

            $itemEvaluationContext = $evaluationContext->createSubScopeContext(
                $this->bagFactory->createStaticBag($variableStatics)
            );

            $resultArray[] = $mapCallback($itemEvaluationContext, $static, $index);
        }

        return $resultArray;
    }

    /**
     * {@inheritdoc}
     */
    public function setElementStatic($index, StaticInterface $value)
    {
        if (!is_int($index)) {
            throw new InvalidArgumentException('Index must be an int, ' . gettype($index) . ' given');
        }

        $this->statics[$index] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $nativeArray = [];

        foreach ($this->statics as $static) {
            $nativeArray[] = $static->toNative();
        }

        return $nativeArray;
    }

    /**
     * Validates that all statics in the provided list are actually StaticInterfaces
     *
     * @param StaticInterface[] $statics
     */
    private function assertValidStatics(array $statics)
    {
        foreach ($statics as $name => $static) {
            if (!$static instanceof StaticInterface) {
                throw new InvalidArgumentException(
                    'List static "' . $name . '" is actually a ' . get_class($static)
                );
            }
        }
    }
}
