<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression;

use Combyna\Component\Bag\StaticListInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Type\TypeInterface;
use Countable;

/**
 * Class StaticListExpression.
 *
 * Represents a list of static values.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StaticListExpression extends AbstractStaticExpression implements Countable
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
     * Returns a text static with all elements of the list concatenated together.
     *
     * @param string $glue
     * @return TextExpression
     */
    public function concatenate($glue = '')
    {
        return $this->expressionFactory->createTextExpression($this->staticList->concatenate($glue));
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->staticList);
    }

    /**
     * Returns true if all the elements of this list match the provided type, false otherwise.
     *
     * @param TypeInterface $type
     * @return bool
     */
    public function elementsMatch(TypeInterface $type)
    {
        return $this->staticList->elementsMatch($type);
    }

    /**
     * {@inheritdoc}
     */
    public function equals(StaticValueInterface $otherValue)
    {
        return $otherValue instanceof self &&
            $otherValue->staticList->equals($this->staticList);
    }

    /**
     * Fetches all statics in this list.
     *
     * @return StaticInterface[]
     */
    public function getElementStatics()
    {
        return $this->staticList->getElementStatics();
    }

    /**
     * {@inheritdoc}
     */
    public function getSummary()
    {
        $elementsWereTruncated = false;
        $summaries = [];

        foreach ($this->staticList->getElementStatics() as $index => $elementStatic) {
            $summaries[] = $elementStatic->getSummary();

            // Only capture a summary for the first few elements to keep it short-ish.
            if ($index > 3) {
                $elementsWereTruncated = true;
                break;
            }
        }

        return sprintf(
            '[%s]',
            // Add an ellipsis to show that we had to truncate the element summaries when applicable.
            join(',', $summaries) . ($elementsWereTruncated ? ',...' : '')
        );
    }

    /**
     * Maps this static list to another, transforming each element with the given expression.
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
     * Maps this static list to a native array, transforming each element with the given callback.
     *
     * @param string $itemVariableName
     * @param string|null $indexVariableName
     * @param callable $mapCallback
     * @param EvaluationContextInterface $evaluationContext
     * @return array
     */
    public function mapArray(
        $itemVariableName,
        $indexVariableName,
        callable $mapCallback,
        EvaluationContextInterface $evaluationContext
    ) {
        return $this->staticList->mapArray($itemVariableName, $indexVariableName, $mapCallback, $evaluationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function toNative()
    {
        return $this->staticList->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function withElements(array $elementStatics)
    {
        $newStaticList = $this->staticList->withElements($elementStatics);

        if ($newStaticList === $this->staticList) {
            // List already contained all the statics - nothing to do.
            return $this;
        }

        return new StaticListExpression($this->expressionFactory, $newStaticList);
    }
}
