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
use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Type\TypeInterface;
use Countable;

/**
 * Interface StaticListInterface
 *
 * Contains an ordered collection of static values
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface StaticListInterface extends Countable
{
    /**
     * Concatenates all elements of the list together using the specified glue as the delimiter
     *
     * @param string $glue
     * @return string
     */
    public function concatenate($glue = '');

    /**
     * Returns true if all the elements of this list match the provided type, false otherwise
     *
     * @param TypeInterface $type
     * @return bool
     */
    public function elementsMatch(TypeInterface $type);

    /**
     * Fetches a static from the specified index in this list
     *
     * @param int $index
     * @return StaticInterface
     */
    public function getElementStatic($index);

    /**
     * Maps this static list to another, transforming each element with the given expression
     *
     * @param string $itemVariableName
     * @param string|null $indexVariableName
     * @param ExpressionInterface $mapExpression
     * @param EvaluationContextInterface $evaluationContext
     * @return StaticListInterface
     */
    public function map(
        $itemVariableName,
        $indexVariableName,
        ExpressionInterface $mapExpression,
        EvaluationContextInterface $evaluationContext
    );

    /**
     * Maps this static list to a native array, transforming each element with the given callback
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
    );

    /**
     * Assigns a new value for a static in this list
     *
     * @param int $index
     * @param StaticInterface $value
     */
    public function setElementStatic($index, StaticInterface $value);

    /**
     * Builds a native array with all native values of statics in this list
     *
     * @return array
     */
    public function toArray();
}
