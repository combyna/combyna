<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Expression;

use Combyna\Component\Expression\AbstractExpression;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;

/**
 * Class CaptureExpression
 *
 * Fetches a captured static value.
 * - A parent widget or view will have defined the capture and its type
 * - A descendant of the widget or view that defined the capture will assign to it
 * - The widget that reads a capture does not need to be a sibling of the one defining it
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CaptureExpression extends AbstractExpression
{
    const TYPE = 'capture';

    /**
     * @var string
     */
    private $captureName;

    /**
     * @param string $captureName Name of the capture to fetch - eg. "user_name" or "primary_telephone"
     */
    public function __construct($captureName)
    {
        $this->captureName = $captureName;
    }

    /**
     * {@inheritdoc}
     */
    public function toStatic(EvaluationContextInterface $evaluationContext)
    {
        $subEvaluationContext = $evaluationContext->createSubExpressionContext($this);

        return $subEvaluationContext->getCaptureRootwise($this->captureName);
    }
}
