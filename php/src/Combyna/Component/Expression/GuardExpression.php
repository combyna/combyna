<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression;

use Combyna\Component\Bag\BagFactoryInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextFactoryInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\Assurance\AssuranceInterface;
use Combyna\Component\Validator\ValidationFactoryInterface;

/**
 * Class GuardExpression
 *
 * Evaluates a set of assurances and checks they meet a series of constraints.
 * If all results meet the assurance constraints, the consequent expression is returned,
 * otherwise the alternate one is
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class GuardExpression extends AbstractExpression
{
    const TYPE = 'guard';

    /**
     * @var ExpressionInterface
     */
    private $alternateExpression;

    /**
     * @var AssuranceInterface[]
     */
    private $assurances;

    /**
     * @var BagFactoryInterface
     */
    private $bagFactory;

    /**
     * @var ExpressionInterface
     */
    private $consequentExpression;

    /**
     * @var EvaluationContextFactoryInterface
     */
    private $evaluationContextFactory;

    /**
     * @var ExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @var ValidationFactoryInterface
     */
    private $validationFactory;

    /**
     * @param ExpressionFactoryInterface $expressionFactory
     * @param BagFactoryInterface $bagFactory
     * @param EvaluationContextFactoryInterface $evaluationContextFactory
     * @param ValidationFactoryInterface $validationFactory
     * @param AssuranceInterface[] $assurances
     * @param ExpressionInterface $consequentExpression
     * @param ExpressionInterface $alternateExpression
     */
    public function __construct(
        ExpressionFactoryInterface $expressionFactory,
        BagFactoryInterface $bagFactory,
        EvaluationContextFactoryInterface $evaluationContextFactory,
        ValidationFactoryInterface $validationFactory,
        array $assurances,
        ExpressionInterface $consequentExpression,
        ExpressionInterface $alternateExpression
    ) {
        $this->alternateExpression = $alternateExpression;
        $this->assurances = $assurances;
        $this->bagFactory = $bagFactory;
        $this->consequentExpression = $consequentExpression;
        $this->evaluationContextFactory = $evaluationContextFactory;
        $this->expressionFactory = $expressionFactory;
        $this->validationFactory = $validationFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function toStatic(EvaluationContextInterface $evaluationContext)
    {
        $subEvaluationContext = $evaluationContext->createSubExpressionContext($this);
        $assuredStaticBag = $this->bagFactory->createMutableStaticBag();

        foreach ($this->assurances as $assurance) {
            if ($assurance->evaluate($subEvaluationContext, $assuredStaticBag) === false) {
                // Assurance was not met, evaluate and return the alternate expression
                return $this->alternateExpression->toStatic($subEvaluationContext);
            }
        }

        // All assurances were met, so we can evaluate and return the consequent expression
        // with all the assured statics brought into scope

        $assuredEvaluationContext = $subEvaluationContext->createSubAssuredContext(
            $assuredStaticBag
        );

        return $this->consequentExpression->toStatic($assuredEvaluationContext);
    }
}
