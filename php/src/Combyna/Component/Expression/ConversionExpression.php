<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression;

use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use InvalidArgumentException;
use LogicException;

/**
 * Class ConversionExpression
 *
 * Converts between different types of static expression
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ConversionExpression extends AbstractExpression
{
    const TYPE = 'conversion';

    const NUMBER_TO_TEXT = 'number->text';
    const TEXT_TO_NUMBER = 'text->number';

    /**
     * @var string
     */
    private $conversion;

    /**
     * @var ExpressionInterface
     */
    private $expression;

    /**
     * @var ExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @param ExpressionFactoryInterface $expressionFactory
     * @param ExpressionInterface $expression
     * @param string $conversion
     */
    public function __construct(
        ExpressionFactoryInterface $expressionFactory,
        ExpressionInterface $expression,
        $conversion
    ) {
        $this->conversion = $conversion;
        $this->expression = $expression;
        $this->expressionFactory = $expressionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function toStatic(EvaluationContextInterface $evaluationContext)
    {
        $subEvaluationContext = $evaluationContext->createSubExpressionContext($this);
        $inputStatic = $this->expression->toStatic($subEvaluationContext);

        switch ($this->conversion) {
            case self::NUMBER_TO_TEXT:
                if (!$inputStatic instanceof NumberExpression) {
                    throw new LogicException(
                        'ConversionExpression :: Input can only evaluate to a number static ' .
                        'for number->text conversion, but got a(n) "' . $inputStatic->getType() . '"'
                    );
                }

                // Just use PHP's standard number-to-string coercion
                return $this->expressionFactory->createTextExpression(strval($inputStatic->toNative()));
            case self::TEXT_TO_NUMBER:
                if (!$inputStatic instanceof TextExpression) {
                    throw new LogicException(
                        'ConversionExpression :: Input can only evaluate to a text static ' .
                        'for text->number conversion, but got a(n) "' . $inputStatic->getType() . '"'
                    );
                }

                $nativeNumber = $inputStatic->toNative();

                // Just use PHP's standard string-to-number coercion
                // TODO: Extend in future with options for how to behave when no number can be coerced out
                return $this->expressionFactory->createNumberExpression(
                    // Cast to the relevant type (float for a decimal, int otherwise)
                    is_numeric($nativeNumber) ? $nativeNumber * 1 : 0
                );
            default:
                throw new InvalidArgumentException(
                    'ConversionExpression :: Invalid conversion "' . $this->conversion . '" provided'
                );
        }
    }
}
