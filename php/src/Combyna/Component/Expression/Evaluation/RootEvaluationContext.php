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
use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Expression\ExpressionInterface;
use LogicException;

/**
 * Class RootEvaluationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RootEvaluationContext implements EvaluationContextInterface
{
    /**
     * @var EnvironmentInterface
     */
    private $environment;

    /**
     * @var EvaluationContextFactoryInterface
     */
    private $evaluationContextFactory;

    /**
     * @param EvaluationContextFactoryInterface $evaluationContextFactory
     * @param EnvironmentInterface $environment
     */
    public function __construct(
        EvaluationContextFactoryInterface $evaluationContextFactory,
        EnvironmentInterface $environment
    ) {
        $this->environment = $environment;
        $this->evaluationContextFactory = $evaluationContextFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function callFunction($libraryName, $functionName, StaticBagInterface $argumentStaticBag)
    {
        $function = $this->environment->getGenericFunction($libraryName, $functionName);

        return $function->call($argumentStaticBag);
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
        throw new LogicException('No assured static is defined with name "' . $assuredStaticName . '"');
    }

    /**
     * {@inheritdoc}
     */
    public function getVariable($variableName)
    {
        throw new LogicException('No variable is defined with name "' . $variableName . '"');
    }
}
