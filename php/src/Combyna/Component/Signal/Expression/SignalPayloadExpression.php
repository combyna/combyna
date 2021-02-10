<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal\Expression;

use Combyna\Component\Expression\AbstractExpression;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;

/**
 * Class SignalPayloadExpression
 *
 * Fetches a static from the current signal's payload
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalPayloadExpression extends AbstractExpression
{
    const TYPE = 'signal-payload-static';

    /**
     * @var string
     */
    private $staticName;

    /**
     * @param string $staticName
     */
    public function __construct($staticName)
    {
        $this->staticName = $staticName;
    }

    /**
     * {@inheritdoc}
     */
    public function toStatic(EvaluationContextInterface $evaluationContext)
    {
        return $evaluationContext->getSignalPayloadStatic($this->staticName);
    }
}
