<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression;

use Combyna\Component\Bag\ExpressionBagInterface;
use Combyna\Component\Bag\ExpressionListInterface;
use Combyna\Component\Expression\Assurance\AssuranceInterface;

/**
 * Interface ExpressionFactoryInterface
 *
 * Creates expression or static expression objects
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ExpressionFactoryInterface
{
    /**
     * Creates an AssuredExpression
     *
     * @param string $assuredStaticName
     * @return AssuredExpression
     */
    public function createAssuredExpression($assuredStaticName);

    /**
     * Creates a BinaryArithmeticExpression, which performs an arithmetic calculation with two operands
     * 
     * @param ExpressionInterface $leftOperandExpression
     * @param string $operator
     * @param ExpressionInterface $rightOperandExpression
     * @return BinaryArithmeticExpression
     */
    public function createBinaryArithmeticExpression(
        ExpressionInterface $leftOperandExpression,
        $operator,
        ExpressionInterface $rightOperandExpression
    );
    
    /**
     * Creates a BooleanExpression
     *
     * @param bool $value
     * @return BooleanExpression
     */
    public function createBooleanExpression($value);

    /**
     * Creates a ComparisonExpression, used for comparing two expressions
     *
     * @param ExpressionInterface $leftOperandExpression
     * @param string $operator
     * @param ExpressionInterface $rightOperandExpression
     * @return ComparisonExpression
     */
    public function createComparisonExpression(
        ExpressionInterface $leftOperandExpression,
        $operator,
        ExpressionInterface $rightOperandExpression
    );

    /**
     * Creates a ConcatenationExpression
     *
     * @param ExpressionInterface $operandListExpression
     * @param ExpressionInterface|null $glueExpression
     * @return ConcatenationExpression
     */
    public function createConcatenationExpression(
        ExpressionInterface $operandListExpression,
        ExpressionInterface $glueExpression = null
    );

    /**
     * Creates a ConditionalExpression, which returns one expression or a different one
     * depending on whether a third expression returns true or false.
     * This is the functional equivalent of an `if (...)` statement
     *
     * @param ExpressionInterface $conditionExpression
     * @param ExpressionInterface $consequentExpression
     * @param ExpressionInterface $alternateExpression
     * @return ConditionalExpression
     */
    public function createConditionalExpression(
        ExpressionInterface $conditionExpression,
        ExpressionInterface $consequentExpression,
        ExpressionInterface $alternateExpression
    );

    /**
     * Creates a ConversionExpression, which converts or coerces between two different static expression types
     *
     * @param ExpressionInterface $expression
     * @param string $conversion
     * @return ConversionExpression
     */
    public function createConversionExpression(
        ExpressionInterface $expression,
        $conversion
    );

    /**
     * Creates a DateTimeExpression
     *
     * @param ExpressionInterface $yearExpression
     * @param ExpressionInterface $monthExpression
     * @param ExpressionInterface $dayExpression
     * @param ExpressionInterface $hourExpression
     * @param ExpressionInterface $minuteExpression
     * @param ExpressionInterface $secondExpression
     * @param ExpressionInterface|null $millisecondExpression
     * @return DateTimeExpression
     */
    public function createDateTimeExpression(
        ExpressionInterface $yearExpression,
        ExpressionInterface $monthExpression,
        ExpressionInterface $dayExpression,
        ExpressionInterface $hourExpression,
        ExpressionInterface $minuteExpression,
        ExpressionInterface $secondExpression,
        ExpressionInterface $millisecondExpression = null
    );

    /**
     * Creates a DayExpression
     *
     * @param ExpressionInterface $yearExpression
     * @param ExpressionInterface $monthExpression
     * @param ExpressionInterface $dayExpression
     * @return DayExpression
     */
    public function createDayExpression(
        ExpressionInterface $yearExpression,
        ExpressionInterface $monthExpression,
        ExpressionInterface $dayExpression
    );

    /**
     * Creates a FunctionExpression
     *
     * @param string $libraryName
     * @param string $functionName
     * @param ExpressionBagInterface $argumentExpressionBag
     * @return FunctionExpression
     */
    public function createFunctionExpression(
        $libraryName,
        $functionName,
        ExpressionBagInterface $argumentExpressionBag
    );

    /**
     * Creates an assurance for a guard expression
     *
     * @param ExpressionInterface $expression
     * @param string $constraint
     * @param string $assuredStaticName
     * @return AssuranceInterface
     */
    public function createGuardAssurance(
        ExpressionInterface $expression,
        $constraint,
        $assuredStaticName
    );

    /**
     * Creates a GuardExpression
     *
     * @param AssuranceInterface[] $assurances
     * @param ExpressionInterface $consequentExpression
     * @param ExpressionInterface $alternateExpression
     * @return GuardExpression
     */
    public function createGuardExpression(
        array $assurances,
        ExpressionInterface $consequentExpression,
        ExpressionInterface $alternateExpression
    );

    /**
     * Creates a ListExpression
     *
     * @param ExpressionListInterface $elementExpressionList
     * @return ListExpression
     */
    public function createListExpression(ExpressionListInterface $elementExpressionList);

    /**
     * Creates a MapExpression
     *
     * @param ExpressionInterface $listExpression
     * @param string $itemVariableName
     * @param string|null $indexVariableName
     * @param ExpressionInterface $mapExpression
     * @return MapExpression
     */
    public function createMapExpression(
        ExpressionInterface $listExpression,
        $itemVariableName,
        $indexVariableName,
        ExpressionInterface $mapExpression
    );

    /**
     * Creates a NothingExpression
     *
     * @return NothingExpression
     */
    public function createNothingExpression();

    /**
     * Creates a NumberExpression
     *
     * @param int|float $number
     * @return NumberExpression
     */
    public function createNumberExpression($number);

    /**
     * Creates a TextExpression
     *
     * @param string $text
     * @return TextExpression
     */
    public function createTextExpression($text);

    /**
     * Creates a TranslationExpression
     *
     * @param string $translationKey
     * @param ExpressionBagInterface|null $argumentExpressionBag
     * @return TranslationExpression
     */
    public function createTranslationExpression($translationKey, ExpressionBagInterface $argumentExpressionBag = null);

    /**
     * Creates a VariableExpression
     *
     * @param string $variableName
     * @return VariableExpression
     */
    public function createVariableExpression($variableName);
}
