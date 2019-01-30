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

use Combyna\Component\Bag\BagFactoryInterface;
use Combyna\Component\Bag\ExpressionBagInterface;
use Combyna\Component\Bag\ExpressionListInterface;
use Combyna\Component\Expression\Assurance\NonZeroNumberAssurance;
use Combyna\Component\Expression\Evaluation\EvaluationContextFactoryInterface;
use Combyna\Component\Type\TypeInterface;
use InvalidArgumentException;

/**
 * Class ExpressionFactory
 *
 * Creates expression or static expression objects
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ExpressionFactory implements ExpressionFactoryInterface
{
    /**
     * @var BagFactoryInterface
     */
    private $bagFactory;

    /**
     * @var EvaluationContextFactoryInterface
     */
    private $evaluationContextFactory;

    /**
     * @var StaticExpressionFactoryInterface
     */
    private $staticExpressionFactory;

    /**
     * @param StaticExpressionFactoryInterface $staticExpressionFactory
     * @param BagFactoryInterface $bagFactory
     * @param EvaluationContextFactoryInterface $evaluationContextFactory
     */
    public function __construct(
        StaticExpressionFactoryInterface $staticExpressionFactory,
        BagFactoryInterface $bagFactory,
        EvaluationContextFactoryInterface $evaluationContextFactory
    ) {
        $this->bagFactory = $bagFactory;
        $this->evaluationContextFactory = $evaluationContextFactory;
        $this->staticExpressionFactory = $staticExpressionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createAssuredExpression($assuredStaticName)
    {
        return new AssuredExpression($this, $assuredStaticName);
    }

    /**
     * {@inheritdoc}
     */
    public function createBinaryArithmeticExpression(
        ExpressionInterface $leftOperandExpression,
        $operator,
        ExpressionInterface $rightOperandExpression
    ) {
        return new BinaryArithmeticExpression(
            $this,
            $leftOperandExpression,
            $operator,
            $rightOperandExpression
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createBooleanExpression($value)
    {
        return $this->staticExpressionFactory->createBooleanExpression($value);
    }

    /**
     * {@inheritdoc}
     */
    public function createComparisonExpression(
        ExpressionInterface $leftOperandExpression,
        $operator,
        ExpressionInterface $rightOperandExpression
    ) {
        return new ComparisonExpression(
            $this,
            $leftOperandExpression,
            $operator,
            $rightOperandExpression
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createConcatenationExpression(
        ExpressionInterface $operandListExpression,
        ExpressionInterface $glueExpression = null
    ) {
        return new ConcatenationExpression($this, $operandListExpression, $glueExpression);
    }

    /**
     * {@inheritdoc}
     */
    public function createConditionalExpression(
        ExpressionInterface $conditionExpression,
        ExpressionInterface $consequentExpression,
        ExpressionInterface $alternateExpression
    ) {
        return new ConditionalExpression(
            $this,
            $conditionExpression,
            $consequentExpression,
            $alternateExpression
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createConversionExpression(
        ExpressionInterface $expression,
        $conversion
    ) {
        return new ConversionExpression($this, $expression, $conversion);
    }

    /**
     * {@inheritdoc}
     */
    public function createDateTimeExpression(
        ExpressionInterface $yearExpression,
        ExpressionInterface $monthExpression,
        ExpressionInterface $dayExpression,
        ExpressionInterface $hourExpression,
        ExpressionInterface $minuteExpression,
        ExpressionInterface $secondExpression,
        ExpressionInterface $millisecondExpression = null
    ) {
        return new DateTimeExpression(
            $this->staticExpressionFactory,
            $yearExpression,
            $monthExpression,
            $dayExpression,
            $hourExpression,
            $minuteExpression,
            $secondExpression,
            $millisecondExpression
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createDayExpression(
        ExpressionInterface $yearExpression,
        ExpressionInterface $monthExpression,
        ExpressionInterface $dayExpression
    ) {
        return new DayExpression(
            $this->staticExpressionFactory,
            $yearExpression,
            $monthExpression,
            $dayExpression
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createFunctionExpression(
        $libraryName,
        $functionName,
        ExpressionBagInterface $argumentExpressionBag,
        TypeInterface $returnType
    ) {
        return new FunctionExpression($this, $libraryName, $functionName, $argumentExpressionBag, $returnType);
    }

    /**
     * {@inheritdoc}
     */
    public function createGuardAssurance(
        ExpressionInterface $expression,
        $constraint,
        $assuredStaticName
    ) {
        switch ($constraint) {
            case NonZeroNumberAssurance::TYPE:
                return new NonZeroNumberAssurance($expression, $assuredStaticName);
            default:
                throw new InvalidArgumentException(
                    'Invalid assurance constraint "' . $constraint . '" given'
                );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createGuardExpression(
        array $assurances,
        ExpressionInterface $consequentExpression,
        ExpressionInterface $alternateExpression
    ) {
        return new GuardExpression(
            $this,
            $this->bagFactory,
            $this->evaluationContextFactory,
            $assurances,
            $consequentExpression,
            $alternateExpression
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createListExpression(ExpressionListInterface $elementExpressionList)
    {
        return new ListExpression($this, $this->staticExpressionFactory, $elementExpressionList);
    }

    /**
     * {@inheritdoc}
     */
    public function createMapExpression(
        ExpressionInterface $listExpression,
        $itemVariableName,
        $indexVariableName,
        ExpressionInterface $mapExpression
    ) {
        return new MapExpression($this, $listExpression, $itemVariableName, $indexVariableName, $mapExpression);
    }

    /**
     * {@inheritdoc}
     */
    public function createNothingExpression()
    {
        return new NothingExpression();
    }

    /**
     * {@inheritdoc}
     */
    public function createNumberExpression($number)
    {
        return $this->staticExpressionFactory->createNumberExpression($number);
    }

    /**
     * {@inheritdoc}
     */
    public function createTextExpression($text)
    {
        return $this->staticExpressionFactory->createTextExpression($text);
    }

    /**
     * {@inheritdoc}
     */
    public function createTranslationExpression($translationKey, ExpressionBagInterface $argumentExpressionBag = null)
    {
        if ($argumentExpressionBag === null) {
            $argumentExpressionBag = $this->bagFactory->createExpressionBag([]);
        }

        return new TranslationExpression($this, $translationKey, $argumentExpressionBag);
    }

    /**
     * {@inheritdoc}
     */
    public function createVariableExpression($variableName)
    {
        return new VariableExpression($variableName);
    }
}
