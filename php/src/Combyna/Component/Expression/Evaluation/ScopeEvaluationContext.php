<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Evaluation;

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Expression\ExpressionInterface;

/**
 * Class ScopeEvaluationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ScopeEvaluationContext implements EvaluationContextInterface
{
    /**
     * @var EvaluationContextFactoryInterface
     */
    private $evaluationContextFactory;

    /**
     * @var EvaluationContextInterface
     */
    private $parentContext;

    /**
     * @var StaticBagInterface
     */
    private $variableStaticBag;

    /**
     * @param EvaluationContextFactoryInterface $evaluationContextFactory
     * @param EvaluationContextInterface $parentContext
     * @param StaticBagInterface $variableStaticBag
     */
    public function __construct(
        EvaluationContextFactoryInterface $evaluationContextFactory,
        EvaluationContextInterface $parentContext,
        StaticBagInterface $variableStaticBag
    ) {
        $this->evaluationContextFactory = $evaluationContextFactory;
        $this->parentContext = $parentContext;
        $this->variableStaticBag = $variableStaticBag;
    }

    /**
     * {@inheritdoc}
     */
    public function callFunction($libraryName, $functionName, StaticBagInterface $argumentStaticBag)
    {
        return $this->parentContext->callFunction($libraryName, $functionName, $argumentStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function createSubAssuredContext(StaticBagInterface $assuredStaticBag)
    {
        return $this->evaluationContextFactory->createAssuredContext($this, $assuredStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function createSubScopeContext(StaticBagInterface $variableStaticBag)
    {
        return $this->evaluationContextFactory->createScopeContext($this, $variableStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function createSubExpressionContext(ExpressionInterface $expression)
    {
        return $this->evaluationContextFactory->createExpressionContext($this, $expression);
    }

    /**
     * {@inheritdoc}
     */
    public function getAssuredStatic($assuredStaticName)
    {
        return $this->parentContext->getAssuredStatic($assuredStaticName);
    }

    /**
     * {@inheritdoc}
     */
    public function getVariable($variableName)
    {
        if ($this->variableStaticBag->hasStatic($variableName)) {
            return $this->variableStaticBag->getStatic($variableName);
        }

        return $this->parentContext->getVariable($variableName);
    }
}
