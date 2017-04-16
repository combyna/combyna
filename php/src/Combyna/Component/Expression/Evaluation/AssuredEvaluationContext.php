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
 * Class AssuredEvaluationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AssuredEvaluationContext implements EvaluationContextInterface
{
    /**
     * @var StaticBagInterface
     */
    private $assuredStaticBag;

    /**
     * @var EvaluationContextFactoryInterface
     */
    private $evaluationContextFactory;

    /**
     * @var EvaluationContextInterface
     */
    private $parentContext;

    /**
     * @param EvaluationContextFactoryInterface $evaluationContextFactory
     * @param EvaluationContextInterface $parentContext
     * @param StaticBagInterface $assuredStaticBag
     */
    public function __construct(
        EvaluationContextFactoryInterface $evaluationContextFactory,
        EvaluationContextInterface $parentContext,
        StaticBagInterface $assuredStaticBag
    ) {
        $this->assuredStaticBag = $assuredStaticBag;
        $this->evaluationContextFactory = $evaluationContextFactory;
        $this->parentContext = $parentContext;
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
    public function createSubExpressionContext(ExpressionInterface $expression)
    {
        return $this->evaluationContextFactory->createExpressionContext($this, $expression);
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
    public function getAssuredStatic($assuredStaticName)
    {
        if ($this->assuredStaticBag->hasStatic($assuredStaticName)) {
            return $this->assuredStaticBag->getStatic($assuredStaticName);
        }

        return $this->parentContext->getAssuredStatic($assuredStaticName);
    }

    /**
     * {@inheritdoc}
     */
    public function getVariable($variableName)
    {
        return $this->parentContext->getVariable($variableName);
    }

    /**
     * {@inheritdoc}
     */
    public function translate($translationKey, array $parameters = [])
    {
        return $this->parentContext->translate($translationKey, $parameters);
    }
}
