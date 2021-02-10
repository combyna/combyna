<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Entity;

use Combyna\Component\Bag\FixedStaticBagModelInterface;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\ExpressionInterface;

/**
 * Class QueryMethod
 *
 * Defines an entrypoint for expressions outside the entity to interrogate it for information
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class QueryMethod implements QueryMethodInterface
{
    /**
     * @var ExpressionInterface
     */
    private $expression;

    /**
     * @var string
     */
    private $name;

    /**
     * @var FixedStaticBagModelInterface
     */
    private $parameterBagModel;

    /**
     * @param string $name
     * @param FixedStaticBagModelInterface $parameterBagModel
     * @param ExpressionInterface $expression
     */
    public function __construct(
        $name,
        FixedStaticBagModelInterface $parameterBagModel,
        ExpressionInterface $expression
    ) {
        $this->name = $name;
        $this->parameterBagModel = $parameterBagModel;
        $this->expression = $expression;
    }

    /**
     * {@inheritdoc}
     */
    public function make(
        StaticBagInterface $argumentStaticBag,
        EvaluationContextInterface $evaluationContext
    ) {
        $this->parameterBagModel->assertValidArgumentBag($argumentStaticBag);

        // Create a sub-evaluation context that includes the arguments as variables
        $subEvaluationContext = $evaluationContext->createSubScopeContext($argumentStaticBag);

        return $this->expression->toStatic($subEvaluationContext);
    }
}
