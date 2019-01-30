<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Assurance;

use Combyna\Component\Bag\MutableStaticBagInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Expression\NumberExpression;
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
    const TYPE = 'non-zero-number';

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
    public function evaluate(EvaluationContextInterface $evaluationContext, MutableStaticBagInterface $staticBag)
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
        return self::TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredAssuredStaticNames()
    {
        return [$this->staticName];
    }
}
