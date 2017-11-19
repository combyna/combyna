<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Event\Evaluation;

use Combyna\Component\Event\EventInterface;
use Combyna\Component\Expression\Evaluation\AbstractEvaluationContext;
use Combyna\Component\Expression\Evaluation\EvaluationContextFactoryInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;

/**
 * Class EventEvaluationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EventEvaluationContext extends AbstractEvaluationContext
{
    /**
     * @param EvaluationContextFactoryInterface $evaluationContextFactory
     * @param EvaluationContextInterface $parentContext
     * @param EventInterface $event
     */
    public function __construct(
        EvaluationContextFactoryInterface $evaluationContextFactory,
        EvaluationContextInterface $parentContext,
        EventInterface $event
    ) {
        parent::__construct($evaluationContextFactory, $parentContext);
    }
}
