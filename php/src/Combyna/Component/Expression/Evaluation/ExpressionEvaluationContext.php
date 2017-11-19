<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Evaluation;

use Combyna\Component\Expression\ExpressionInterface;

/**
 * Class ExpressionEvaluationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ExpressionEvaluationContext extends AbstractEvaluationContext
{
    /**
     * @var ExpressionInterface
     */
    private $expression;

    /**
     * @param EvaluationContextFactoryInterface $evaluationContextFactory
     * @param EvaluationContextInterface $parentContext
     * @param ExpressionInterface $expression
     */
    public function __construct(
        EvaluationContextFactoryInterface $evaluationContextFactory,
        EvaluationContextInterface $parentContext,
        ExpressionInterface $expression
    ) {
        parent::__construct($evaluationContextFactory, $parentContext);

        $this->expression = $expression;
    }

    // TODO: Consider removing this class, as it does nothing
}
