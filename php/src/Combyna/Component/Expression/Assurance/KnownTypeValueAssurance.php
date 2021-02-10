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
use Combyna\Component\Type\TypeInterface;

/**
 * Class KnownTypeValueAssurance
 *
 * Ensures that the given expression evaluates to a specific type
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class KnownTypeValueAssurance implements AssuranceInterface
{
    const TYPE = 'known-type-value';

    /**
     * @var ExpressionInterface
     */
    private $inputExpression;

    /**
     * @var TypeInterface
     */
    private $knownType;

    /**
     * @var string
     */
    private $staticName;

    /**
     * @param ExpressionInterface $inputExpression
     * @param string $name Name to expose the assured static to sub-expressions as
     * @param TypeInterface $knownType
     */
    public function __construct(
        ExpressionInterface $inputExpression,
        $name,
        TypeInterface $knownType
    ) {
        $this->inputExpression = $inputExpression;
        $this->knownType = $knownType;
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

        if (!$this->knownType->allowsStatic($resultStatic)) {
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
