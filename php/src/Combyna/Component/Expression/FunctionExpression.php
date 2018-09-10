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

use Combyna\Component\Bag\ExpressionBagInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Type\TypeInterface;

/**
 * Class FunctionExpression
 *
 * Calls a library function and returns its result
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class FunctionExpression extends AbstractExpression
{
    const TYPE = 'function';

    /**
     * @var ExpressionBagInterface
     */
    private $argumentExpressionBag;

    /**
     * @var ExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @var string
     */
    private $functionName;

    /**
     * @var string
     */
    private $libraryName;

    /**
     * @var TypeInterface
     */
    private $returnType;

    /**
     * @param ExpressionFactoryInterface $expressionFactory
     * @param string $libraryName
     * @param string $functionName
     * @param ExpressionBagInterface $argumentExpressionBag
     * @param TypeInterface $returnType
     */
    public function __construct(
        ExpressionFactoryInterface $expressionFactory,
        $libraryName,
        $functionName,
        ExpressionBagInterface $argumentExpressionBag,
        TypeInterface $returnType
    ) {
        $this->argumentExpressionBag = $argumentExpressionBag;
        $this->expressionFactory = $expressionFactory;
        $this->functionName = $functionName;
        $this->libraryName = $libraryName;
        $this->returnType = $returnType;
    }

    /**
     * {@inheritdoc}
     */
    public function toStatic(EvaluationContextInterface $evaluationContext)
    {
        $subEvaluationContext = $evaluationContext->createSubExpressionContext($this);

        // Evaluate the arguments to statics suitable for passing to the function
        $argumentStaticBag = $this->argumentExpressionBag->toStaticBag($subEvaluationContext);

        return $evaluationContext->callFunction(
            $this->libraryName,
            $this->functionName,
            $argumentStaticBag,
            $this->returnType
        );
    }
}
