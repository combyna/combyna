<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Act;

use Combyna\Component\Expression\ConversionExpression;
use Combyna\Component\Expression\NumberExpression;
use Combyna\Component\Expression\TextExpression;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Type\StaticType;
use InvalidArgumentException;

/**
 * Class ConversionExpressionNode
 *
 * Converts between different types of static expression
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ConversionExpressionNode extends AbstractExpressionNode
{
    const TYPE = ConversionExpression::TYPE;

    /**
     * @var string
     */
    private $conversion;

    /**
     * @var ExpressionNodeInterface
     */
    private $expression;

    /**
     * @param ExpressionNodeInterface $expression
     * @param string $conversion
     */
    public function __construct(
        ExpressionNodeInterface $expression,
        $conversion
    ) {
        $this->conversion = $conversion;
        $this->expression = $expression;
    }

    /**
     * Fetches the type of conversion to perform
     *
     * @return string
     */
    public function getConversion()
    {
        return $this->conversion;
    }

    /**
     * Fetches the expression to evaluate and convert the resulting static of
     *
     * @return ExpressionNodeInterface
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * {@inheritdoc}
     */
    public function getResultType(ValidationContextInterface $validationContext)
    {
        switch ($this->conversion) {
            case ConversionExpression::NUMBER_TO_TEXT:
                return new StaticType(TextExpression::class);
            case ConversionExpression::TEXT_TO_NUMBER:
                return new StaticType(NumberExpression::class);
            default:
                throw new InvalidArgumentException(
                    'ConversionExpressionNode :: Invalid conversion "' . $this->conversion . '" provided'
                );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        $this->expression->validate($subValidationContext);

        switch ($this->conversion) {
            case ConversionExpression::NUMBER_TO_TEXT:
                $allowedInputType = NumberExpression::class;
                break;
            case ConversionExpression::TEXT_TO_NUMBER:
                $allowedInputType = TextExpression::class;
                break;
            default:
                throw new InvalidArgumentException(
                    'ConversionExpressionNode :: Invalid conversion "' . $this->conversion . '" provided'
                );
        }

        // Ensure the input expression can only ever evaluate
        // to the expected types for the specified conversion
        $subValidationContext->assertResultType(
            $this->expression,
            new StaticType($allowedInputType),
            'expression'
        );
    }
}
