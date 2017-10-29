<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
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
     * @return StaticBagInterface
     */
    public function attributeExpressionBagToStaticBag(EvaluationContextInterface $evaluationContext);

    /**
     * Fetches the bag of expressions to evaluate for the route's attributes
     *
     * @return ExpressionBagInterface
     */
    public function getAttributeExpressionBag();

    /**
     * Fetches the route to navigate to
     *
     * @return RouteInterface
     */
    public function getRoute();
}
