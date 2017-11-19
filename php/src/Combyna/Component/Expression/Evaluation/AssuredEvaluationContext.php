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
 * Class AssuredEvaluationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AssuredEvaluationContext extends AbstractEvaluationContext
{
    /**
     * @var StaticBagInterface
     */
    private $assuredStaticBag;

    /**
     * @param EvaluationContextFactoryInterface $evaluationContextFactory
     * @param EvaluationContextInterface $parentContext
     * @param StaticBagInterface $assuredStaticBag
     */
    public function __construct(
        EvaluationContextFactoryInterface $evaluationContextFactory,
        EvaluationContextInterface $parentContext,
        StaticBagInterface $assuredStaticBag
    ) {
        parent::__construct($evaluationContextFactory, $parentContext);

        $this->assuredStaticBag = $assuredStaticBag;
    }

    /**
     * {@inheritdoc}
     */
    public function getAssuredStatic($assuredStaticName)
    {
        if ($this->assuredStaticBag->hasStatic($assuredStaticName)) {
            return $this->assuredStaticBag->getStatic($assuredStaticName);
        }

        return $this->parentContext->getAssuredStatic($assuredStaticName);
    }
}
