<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Event\Expression;

use Combyna\Component\Expression\AbstractExpression;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;

/**
 * Class EventPayloadExpression
 *
 * Fetches a static from the current event's payload
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EventPayloadExpression extends AbstractExpression
{
    const TYPE = 'event-payload-static';

    /**
     * @var EventExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @var string
     */
    private $staticName;

    /**
     * @param EventExpressionFactoryInterface $expressionFactory
     * @param string $staticName
     */
    public function __construct(
        EventExpressionFactoryInterface $expressionFactory,
        $staticName
    ) {
        $this->expressionFactory = $expressionFactory;
        $this->staticName = $staticName;
    }

    /**
     * {@inheritdoc}
     */
    public function toStatic(EvaluationContextInterface $evaluationContext)
    {
        return $evaluationContext->getEventPayloadStatic($this->staticName);
    }
}
