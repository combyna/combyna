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

use Combyna\Component\Bag\StaticBagInterface;

/**
 * Class ScopeEvaluationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ScopeEvaluationContext extends AbstractEvaluationContext
{
    /**
     * @var StaticBagInterface
     */
    private $variableStaticBag;

    /**
     * @param EvaluationContextFactoryInterface $evaluationContextFactory
     * @param EvaluationContextInterface $parentContext
     * @param StaticBagInterface $variableStaticBag
     */
    public function __construct(
        EvaluationContextFactoryInterface $evaluationContextFactory,
        EvaluationContextInterface $parentContext,
        StaticBagInterface $variableStaticBag
    ) {
        parent::__construct($evaluationContextFactory, $parentContext);

        $this->variableStaticBag = $variableStaticBag;
    }

    /**
     * {@inheritdoc}
     */
    public function getVariable($variableName)
    {
        if ($this->variableStaticBag->hasStatic($variableName)) {
            return $this->variableStaticBag->getStatic($variableName);
        }

        return $this->parentContext->getVariable($variableName);
    }
}
