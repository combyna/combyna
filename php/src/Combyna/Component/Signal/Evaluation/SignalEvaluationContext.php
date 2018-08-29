<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal\Evaluation;

use Combyna\Component\Expression\Evaluation\AbstractEvaluationContext;
use Combyna\Component\Expression\Evaluation\EvaluationContextFactoryInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Signal\SignalInterface;

/**
 * Class SignalEvaluationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalEvaluationContext extends AbstractEvaluationContext
{
    /**
     * @var SignalInterface
     */
    private $signal;

    /**
     * @param EvaluationContextFactoryInterface $evaluationContextFactory
     * @param EvaluationContextInterface $parentContext
     * @param SignalInterface $signal
     */
    public function __construct(
        EvaluationContextFactoryInterface $evaluationContextFactory,
        EvaluationContextInterface $parentContext,
        SignalInterface $signal
    ) {
        parent::__construct($evaluationContextFactory, $parentContext);

        $this->signal = $signal;
    }

    /**
     * {@inheritdoc}
     */
    public function getSignalPayloadStatic($staticName)
    {
        return $this->signal->getPayloadStatic($staticName);
    }
}
