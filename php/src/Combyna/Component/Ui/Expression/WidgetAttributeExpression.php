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
 * Class WidgetAttributeExpression
 *
 * Fetches an attribute for the current widget definition.
 * May be used inside a compound widget definition to refer to an attribute of it
 * either inside its root widget tree or from one of its widget value expressions.
 * May be used inside a primitive widget definition to refer to an attribute
 * from one of its widget value expressions.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetAttributeExpression extends AbstractExpression
{
    const TYPE = 'widget-attribute';

    /**
     * @var string
     */
    private $attributeName;

    /**
     * @var UiExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @param UiExpressionFactoryInterface $expressionFactory
     * @param string $attributeName Name of the attribute to fetch for this widget - eg. "label"
     */
    public function __construct(
        UiExpressionFactoryInterface $expressionFactory,
        $attributeName
    ) {
        $this->attributeName = $attributeName;
        $this->expressionFactory = $expressionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function toStatic(EvaluationContextInterface $evaluationContext)
    {
        $subEvaluationContext = $evaluationContext->createSubExpressionContext($this);

        return $subEvaluationContext->getWidgetAttribute($this->attributeName);
    }
}
