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

use Combyna\Component\Expression\AbstractExpressionFactory;
use Combyna\Component\Expression\ExpressionFactoryInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Expression\StaticExpressionFactoryInterface;

/**
 * Interface RouterExpressionFactoryInterface
 *
 * Creates expression or static expression objects related to routing
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RouterExpressionFactory extends AbstractExpressionFactory implements RouterExpressionFactoryInterface
{
    /**
     * @var StaticExpressionFactoryInterface
     */
    private $staticExpressionFactory;

    /**
     * @param ExpressionFactoryInterface $parentExpressionFactory
     * @param StaticExpressionFactoryInterface $staticExpressionFactory
     */
    public function __construct(
        ExpressionFactoryInterface $parentExpressionFactory,
        StaticExpressionFactoryInterface $staticExpressionFactory
    ) {
        parent::__construct($parentExpressionFactory);

        $this->staticExpressionFactory = $staticExpressionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createRouteArgumentExpression($parameterName)
    {
        return new RouteArgumentExpression($parameterName);
    }

    /**
     * {@inheritdoc}
     */
    public function createRouteUrlExpression(
        ExpressionInterface $nameExpression,
        ExpressionInterface $argumentStructureExpression
    ) {
        return new RouteUrlExpression($this->staticExpressionFactory, $nameExpression, $argumentStructureExpression);
    }
}
