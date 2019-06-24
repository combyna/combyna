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

use Combyna\Component\Expression\ExpressionFactoryInterface;
use Combyna\Component\Expression\ExpressionInterface;

/**
 * Interface RouterExpressionFactoryInterface
 *
 * Creates expression or static expression objects related to routing
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface RouterExpressionFactoryInterface extends ExpressionFactoryInterface
{
    /**
     * Creates a new RouteArgumentExpression
     *
     * @param string $parameterName
     * @return RouteArgumentExpression
     */
    public function createRouteArgumentExpression($parameterName);

    /**
     * Creates a new RouteUrlExpression
     *
     * @param ExpressionInterface $nameExpression
     * @param ExpressionInterface $argumentStructureExpression
     * @return RouteUrlExpression
     */
    public function createRouteUrlExpression(
        ExpressionInterface $nameExpression,
        ExpressionInterface $argumentStructureExpression
    );
}
