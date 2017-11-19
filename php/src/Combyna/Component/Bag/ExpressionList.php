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
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Type\TypeInterface;
use InvalidArgumentException;

/**
 * Class ExpressionList
 *
 * Contains a list of expressions
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ExpressionList implements ExpressionListInterface
{
    /**
     * @var BagFactoryInterface
     */
    private $bagFactory;

    /**
     * @var ExpressionInterface[]
     */
    private $expressions;

    /**
     * @param BagFactoryInterface $bagFactory
     * @param ExpressionInterface[] $expressions
     * @throws InvalidArgumentException Throws when no expressions are specified
     */
    public function __construct(BagFactoryInterface $bagFactory, array $expressions)
    {
        $this->validateExpressions($expressions);

        if (count($expressions) === 0) {
            throw new InvalidArgumentException('An expression list cannot be empty');
        }

        $this->bagFactory = $bagFactory;
        $this->expressions = $expressions;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->expressions);
    }

    /**
     * {@inheritdoc}
     */
    public function getElementResultType(ValidationContextInterface $validationContext)
    {
        /** @var TypeInterface|null $resultType */
        $resultType = null;

        foreach ($this->expressions as $expression) {
            $elementResultType = $expression->getResultType($validationContext);

            if ($resultType === null) {
                $resultType = $elementResultType;
            } else {
                $resultType = $resultType->mergeWith($elementResultType);
            }
        }

        // An expression list should never be empty, so this should never return null
        return $resultType;
    }

    /**
     * {@inheritdoc}
     */
    public function toStaticList(EvaluationContextInterface $evaluationContext)
    {
        $statics = [];

        foreach ($this->expressions as $expression) {
            $statics[] = $expression->toStatic($evaluationContext);
        }

        return $this->bagFactory->createStaticList($statics);
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        foreach ($this->expressions as $expression) {
            $expression->validate($validationContext);
        }
    }

    /**
     * Validates that all expressions in the provided bag are actually ExpressionInterfaces
     *
     * @param ExpressionInterface[] $expressions
     */
    private function validateExpressions(array $expressions)
    {
        foreach ($expressions as $name => $expression) {
            if (!$expression instanceof ExpressionInterface) {
                throw new InvalidArgumentException(
                    'List expression "' . $name . '" is actually a ' . get_class($expression)
                );
            }
        }
    }
}
