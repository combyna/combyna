<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Expression\Assurance;

use Combyna\Bag\StaticBagInterface;
use Combyna\Evaluation\EvaluationContextInterface;
use Combyna\Expression\ExpressionInterface;
use Combyna\Expression\NumberExpression;
use Combyna\Expression\Validation\ValidationContextInterface;
use Combyna\Type\StaticType;
use LogicException;

/**
 * Class NonZeroNumberAssurance
 *
 * Ensures that the given expression doesn't evaluate to zero
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NonZeroNumberAssurance implements AssuranceInterface
{
    /**
     * @var ExpressionInterface
     */
    private $inputExpression;

    /**
     * @var string
     */
    private $staticName;

    /**
     * @param ExpressionInterface $inputExpression
     * @param string $name Name to expose the assured static to sub-expressions as
     */
    public function __construct(ExpressionInterface $inputExpression, $name)
    {
        $this->inputExpression = $inputExpression;
        $this->staticName = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function definesStatic($staticName)
    {
        return $this->staticName === $staticName;
    }

    /**
     * {@inheritdoc}
     */
    public function evaluate(EvaluationContextInterface $evaluationContext, StaticBagInterface $staticBag)
    {
        $resultStatic = $this->inputExpression->toStatic($evaluationContext);

        if (!$resultStatic instanceof NumberExpression) {
            // This should be prevented at the validation stage, but check just to be sure
            throw new LogicException(
                'NonZeroNumberAssurance should receive a number, but got "' . $resultStatic->getType() . '"'
            );
        }

        if ($resultStatic->toNative() === 0) {
            // Constraint not met
            return false;
        }

        $staticBag->setStatic($this->staticName, $resultStatic);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getConstraint()
    {
        return self::NON_ZERO_NUMBER;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredAssuredStaticNames()
    {
        return [$this->staticName];
    }

    /**
     * {@inheritdoc}
     */
    public function getStaticType(ValidationContextInterface $validationContext, $assuredStaticName)
    {
        if ($assuredStaticName !== $this->staticName) {
            throw new LogicException(
                'NonZeroNumberAssurance only defines static "' . $this->staticName .
                '" but was asked about "' . $assuredStaticName . '"'
            );
        }

        // The only possible type this assured static can evaluate to is the result type of its expression
        return $this->inputExpression->getResultType($validationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $this->inputExpression->validate($validationContext);

        // Check at compile-time that the expression can only resolve to a number
        $validationContext->assertResultType(
            $this->inputExpression,
            new StaticType(NumberExpression::class),
            'non-zero assurance'
        );
    }
}
