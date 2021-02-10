<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Router\Expression;

use Combyna\Component\Expression\AbstractExpression;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;

/**
 * Class RouteArgumentExpression
 *
 * Fetches an argument given for a route parameter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RouteArgumentExpression extends AbstractExpression
{
    const TYPE = 'route-argument';

    /**
     * @var string
     */
    private $parameterName;

    /**
     * @param string $parameterName
     */
    public function __construct($parameterName)
    {
        $this->parameterName = $parameterName;
    }

    /**
     * {@inheritdoc}
     */
    public function toStatic(EvaluationContextInterface $evaluationContext)
    {
        $subEvaluationContext = $evaluationContext->createSubExpressionContext($this);

        return $subEvaluationContext->getRouteArgument($this->parameterName);
    }
}
