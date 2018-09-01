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

use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Expression\ConversionExpression;
use Combyna\Component\Expression\NumberExpression;
use Combyna\Component\Expression\TextExpression;
use Combyna\Component\Expression\Validation\Constraint\ResultTypeConstraint;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Type\UnresolvedType;
use Combyna\Component\Validator\Type\PresolvedTypeDeterminer;
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
    private $expressionNode;

    /**
     * @param ExpressionNodeInterface $expression
     * @param string $conversion
     */
    public function __construct(
        ExpressionNodeInterface $expression,
        $conversion
    ) {
        $this->conversion = $conversion;
        $this->expressionNode = $expression;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addChildNode($this->expressionNode);

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
        // to the expected type for the specified conversion
        $specBuilder->addConstraint(
            new ResultTypeConstraint(
                $this->expressionNode,
                new PresolvedTypeDeterminer(new StaticType($allowedInputType)),
                'expression'
            )
        );
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
        return $this->expressionNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getResultTypeDeterminer()
    {
        switch ($this->conversion) {
            case ConversionExpression::NUMBER_TO_TEXT:
                return new PresolvedTypeDeterminer(new StaticType(TextExpression::class));
            case ConversionExpression::TEXT_TO_NUMBER:
                return new PresolvedTypeDeterminer(new StaticType(NumberExpression::class));
            default:
                return new PresolvedTypeDeterminer(
                    new UnresolvedType(
                        sprintf(
                            'Invalid conversion "%s" provided',
                            $this->conversion
                        )
                    )
                );
        }
    }
}
