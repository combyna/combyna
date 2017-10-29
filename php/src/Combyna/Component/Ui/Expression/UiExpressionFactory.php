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

use Combyna\Component\Bag\ExpressionBagInterface;
use Combyna\Component\Bag\ExpressionListInterface;
use Combyna\Component\Expression\ExpressionFactoryInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Ui\Store\Expression\SlotExpression;
use Combyna\Component\Ui\Store\Expression\ViewStoreQueryExpression;

/**
 * Class UiExpressionFactory
 *
 * Creates expression or static expression objects inside UIs
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UiExpressionFactory implements UiExpressionFactoryInterface
{
    /**
     * @var ExpressionFactoryInterface
     */
    private $parentExpressionFactory;

    /**
     * @param ExpressionFactoryInterface $parentExpressionFactory
     */
    public function __construct(ExpressionFactoryInterface $parentExpressionFactory)
    {
        $this->parentExpressionFactory = $parentExpressionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createAssuredExpression($assuredStaticName)
    {
        return $this->parentExpressionFactory->createAssuredExpression($assuredStaticName);
    }

    /**
     * {@inheritdoc}
     */
    public function createBinaryArithmeticExpression(
        ExpressionInterface $leftOperandExpression,
        $operator,
        ExpressionInterface $rightOperandExpression
    ) {
        return $this->parentExpressionFactory->createBinaryArithmeticExpression(
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
        return $this->parentExpressionFactory->createBooleanExpression($value);
    }

    /**
     * {@inheritdoc}
     */
    public function createComparisonExpression(
        ExpressionInterface $leftOperandExpression,
        $operator,
        ExpressionInterface $rightOperandExpression
    ) {
        return $this->parentExpressionFactory->createComparisonExpression(
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
        return $this->parentExpressionFactory->createConcatenationExpression(
            $operandListExpression,
            $glueExpression
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createConditionalExpression(
        ExpressionInterface $conditionExpression,
        ExpressionInterface $consequentExpression,
        ExpressionInterface $alternateExpression
    ) {
        return $this->parentExpressionFactory->createConditionalExpression(
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
        return $this->parentExpressionFactory->createConversionExpression($expression, $conversion);
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
        return $this->parentExpressionFactory->createDateTimeExpression(
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
        return $this->parentExpressionFactory->createDayExpression(
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
        ExpressionBagInterface $argumentExpressionBag
    ) {
        return $this->parentExpressionFactory->createFunctionExpression(
            $libraryName,
            $functionName,
            $argumentExpressionBag
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createGuardAssurance(
        ExpressionInterface $expression,
        $constraint,
        $assuredStaticName
    ) {
        return $this->parentExpressionFactory->createGuardAssurance(
            $expression,
            $constraint,
            $assuredStaticName
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createGuardExpression(
        array $assurances,
        ExpressionInterface $consequentExpression,
        ExpressionInterface $alternateExpression
    ) {
        return $this->parentExpressionFactory->createGuardExpression(
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
        return $this->parentExpressionFactory->createListExpression($elementExpressionList);
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
        return $this->parentExpressionFactory->createMapExpression(
            $listExpression,
            $itemVariableName,
            $indexVariableName,
            $mapExpression
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createNothingExpression()
    {
        return $this->parentExpressionFactory->createNothingExpression();
    }

    /**
     * {@inheritdoc}
     */
    public function createNumberExpression($number)
    {
        return $this->parentExpressionFactory->createNumberExpression($number);
    }

    /**
     * {@inheritdoc}
     */
    public function createStoreSlotExpression($slotName)
    {
        return new SlotExpression($slotName);
    }

    /**
     * {@inheritdoc}
     */
    public function createTextExpression($text)
    {
        return $this->parentExpressionFactory->createTextExpression($text);
    }

    /**
     * {@inheritdoc}
     */
    public function createTranslationExpression($translationKey, ExpressionBagInterface $argumentExpressionBag = null)
    {
        return $this->parentExpressionFactory->createTranslationExpression($translationKey, $argumentExpressionBag);
    }

    /**
     * {@inheritdoc}
     */
    public function createVariableExpression($variableName)
    {
        return $this->parentExpressionFactory->createVariableExpression($variableName);
    }

    /**
     * {@inheritdoc}
     */
    public function createViewStoreQueryExpression($queryName, ExpressionBagInterface $argumentExpressionBag)
    {
        return new ViewStoreQueryExpression($this, $queryName, $argumentExpressionBag);
    }
}
