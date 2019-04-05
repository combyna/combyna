<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\App;

use Combyna\Component\Bag\ExpressionBagInterface;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Router\RouteInterface;

/**
 * Interface HomeInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface HomeInterface
{
    /**
     * Evaluates the home route expression bag to statics
     *
     * @param EvaluationContextInterface $evaluationContext
     * @return StaticBagInterface
     */
    public function argumentExpressionBagToStaticBag(EvaluationContextInterface $evaluationContext);

    /**
     * Fetches the bag of expressions to evaluate for the route's parameter arguments
     *
     * @return ExpressionBagInterface
     */
    public function getArgumentExpressionBag();

    /**
     * Fetches the route to navigate to
     *
     * @return RouteInterface
     */
    public function getRoute();
}
