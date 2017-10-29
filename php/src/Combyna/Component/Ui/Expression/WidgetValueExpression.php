<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Expression;

use Combyna\Component\Expression\AbstractExpression;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\ExpressionFactoryInterface;

/**
 * Class WidgetValueExpression
 *
 * Fetches a "value" for the current widget.
 * - Primitive widgets may define a value fetched from the native element (eg. the text inside a textbox)
 * - Compound widgets may define a value as an expression
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetValueExpression extends AbstractExpression
{
    const TYPE = 'widget-value';

    /**
     * @var ExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @var string
     */
    private $valueName;

    /**
     * @param ExpressionFactoryInterface $expressionFactory
     * @param string $valueName Name of the value to fetch for this widget - eg. "toggled" or "value"
     */
    public function __construct(
        ExpressionFactoryInterface $expressionFactory,
        $valueName
    ) {
        $this->expressionFactory = $expressionFactory;
        $this->valueName = $valueName;
    }

    /**
     * {@inheritdoc}
     */
    public function toStatic(EvaluationContextInterface $evaluationContext)
    {
        $subEvaluationContext = $evaluationContext->createSubExpressionContext($this);

        return $subEvaluationContext->getWidgetValue($this->valueName);
    }
}
