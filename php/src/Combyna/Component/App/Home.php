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
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Router\RouteInterface;

/**
 * Class Home
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Home implements HomeInterface
{
    /**
     * @var ExpressionBagInterface
     */
    private $attributeExpressionBag;

    /**
     * @var RouteInterface
     */
    private $route;

    /**
     * @param RouteInterface $route
     * @param ExpressionBagInterface $attributeExpressionBag
     */
    public function __construct(RouteInterface $route, ExpressionBagInterface $attributeExpressionBag)
    {
        $this->attributeExpressionBag = $attributeExpressionBag;
        $this->route = $route;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeExpressionBagToStaticBag(EvaluationContextInterface $evaluationContext)
    {
        return $this->attributeExpressionBag->toStaticBag($evaluationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeExpressionBag()
    {
        return $this->attributeExpressionBag;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoute()
    {
        return $this->route;
    }
}
