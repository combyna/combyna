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
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Expression\StaticExpressionFactoryInterface;
use Combyna\Component\Expression\StaticStructureExpression;
use LogicException;

/**
 * Class RouteUrlExpression
 *
 * Builds a URL for a route
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RouteUrlExpression extends AbstractExpression
{
    const TYPE = 'route-url';

    /**
     * @var ExpressionInterface
     */
    private $argumentStructureExpression;

    /**
     * @var StaticExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @var ExpressionInterface
     */
    private $nameExpression;

    /**
     * @param StaticExpressionFactoryInterface $expressionFactory
     * @param ExpressionInterface $nameExpression
     * @param ExpressionInterface $argumentStructureExpression
     */
    public function __construct(
        StaticExpressionFactoryInterface $expressionFactory,
        ExpressionInterface $nameExpression,
        ExpressionInterface $argumentStructureExpression
    ) {
        $this->argumentStructureExpression = $argumentStructureExpression;
        $this->expressionFactory = $expressionFactory;
        $this->nameExpression = $nameExpression;
    }

    /**
     * {@inheritdoc}
     */
    public function toStatic(EvaluationContextInterface $evaluationContext)
    {
        $subEvaluationContext = $evaluationContext->createSubExpressionContext($this);

        $fqRouteName = $this->nameExpression->toStatic($subEvaluationContext)->toNative();

        list($libraryName, $routeName) = explode('.', $fqRouteName, 2);

        $argumentStructureStatic = $this->argumentStructureExpression->toStatic($subEvaluationContext);

        if (!$argumentStructureStatic instanceof StaticStructureExpression) {
            // This should have been caught by validation
            throw new LogicException(sprintf(
                'Expected a "%s" expression, got a "%s"',
                StaticStructureExpression::TYPE,
                $argumentStructureStatic->getType()
            ));
        }

        $url = $subEvaluationContext->buildRouteUrl(
            $libraryName,
            $routeName,
            $argumentStructureStatic->getAttributeStaticBag()
        );

        return $this->expressionFactory->createTextExpression($url);
    }
}
