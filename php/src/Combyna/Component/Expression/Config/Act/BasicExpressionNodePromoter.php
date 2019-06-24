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

use Combyna\Component\Bag\Config\Act\BagNodePromoter;
use Combyna\Component\Expression\AttributeExpression;
use Combyna\Component\Expression\BinaryArithmeticExpression;
use Combyna\Component\Expression\BooleanExpression;
use Combyna\Component\Expression\ComparisonExpression;
use Combyna\Component\Expression\ConcatenationExpression;
use Combyna\Component\Expression\ConditionalExpression;
use Combyna\Component\Expression\ConversionExpression;
use Combyna\Component\Expression\DateTimeExpression;
use Combyna\Component\Expression\DayExpression;
use Combyna\Component\Expression\ExpressionFactoryInterface;
use Combyna\Component\Expression\FunctionExpression;
use Combyna\Component\Expression\ListExpression;
use Combyna\Component\Expression\MapExpression;
use Combyna\Component\Expression\NothingExpression;
use Combyna\Component\Expression\NumberExpression;
use Combyna\Component\Expression\StructureExpression;
use Combyna\Component\Expression\TextExpression;
use Combyna\Component\Expression\TranslationExpression;
use Combyna\Component\Expression\VariableExpression;
use Combyna\Component\Framework\Context\ModeContext;
use Combyna\Component\Type\AnyType;
use Combyna\Component\Validator\Context\NullValidationContext;
use InvalidArgumentException;

/**
 * Class BasicExpressionNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BasicExpressionNodePromoter implements ExpressionNodeTypePromoterInterface
{
    /**
     * @var BagNodePromoter
     */
    private $bagNodePromoter;

    /**
     * @var ExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @var DelegatingExpressionNodePromoter
     */
    private $expressionNodePromoter;

    /**
     * @var ModeContext
     */
    private $modeContext;

    private static $typesToMethods = [
        AttributeExpressionNode::TYPE => 'promoteAttributeExpression',
        BinaryArithmeticExpressionNode::TYPE => 'promoteBinaryArithmeticExpression',
        BooleanExpressionNode::TYPE => 'promoteBooleanExpression',
        ComparisonExpressionNode::TYPE => 'promoteComparisonExpression',
        ConcatenationExpressionNode::TYPE => 'promoteConcatenationExpression',
        ConditionalExpressionNode::TYPE => 'promoteConditionalExpression',
        ConversionExpressionNode::TYPE => 'promoteConversionExpression',
        DateTimeExpressionNode::TYPE => 'promoteDateTimeExpression',
        DayExpressionNode::TYPE => 'promoteDayExpression',
        FunctionExpressionNode::TYPE => 'promoteFunctionExpression',
        ListExpressionNode::TYPE => 'promoteListExpression',
        MapExpressionNode::TYPE => 'promoteMapExpression',
        NothingExpressionNode::TYPE => 'promoteNothingExpression',
        NumberExpressionNode::TYPE => 'promoteNumberExpression',
        StructureExpressionNode::TYPE => 'promoteStructureExpression',
        TextExpressionNode::TYPE => 'promoteTextExpression',
        TranslationExpressionNode::TYPE => 'promoteTranslationExpression',
        VariableExpressionNode::TYPE => 'promoteVariableExpression'
    ];

    /**
     * @param ExpressionFactoryInterface $expressionFactory
     * @param DelegatingExpressionNodePromoter $expressionNodePromoter
     * @param BagNodePromoter $bagNodePromoter
     * @param ModeContext $modeContext
     */
    public function __construct(
        ExpressionFactoryInterface $expressionFactory,
        DelegatingExpressionNodePromoter $expressionNodePromoter,
        BagNodePromoter $bagNodePromoter,
        ModeContext $modeContext
    ) {
        $this->expressionFactory = $expressionFactory;
        $this->expressionNodePromoter = $expressionNodePromoter;
        $this->bagNodePromoter = $bagNodePromoter;
        $this->modeContext = $modeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes()
    {
        return array_keys(self::$typesToMethods);
    }

    /**
     * {@inheritdoc}
     *
     * @uses promoteAttributeExpression
     * @uses promoteBinaryArithmeticExpression
     * @uses promoteBooleanExpression
     * @uses promoteComparisonExpression
     * @uses promoteConcatenationExpression
     * @uses promoteConditionalExpression
     * @uses promoteConversionExpression
     * @uses promoteDateTimeExpression
     * @uses promoteDayExpression
     * @uses promoteFunctionExpression
     * @uses promoteListExpression
     * @uses promoteMapExpression
     * @uses promoteNothingExpression
     * @uses promoteNumberExpression
     * @uses promoteStructureExpression
     * @uses promoteTextExpression
     * @uses promoteTranslationExpression
     * @uses promoteVariableExpression
     */
    public function promote(ExpressionNodeInterface $expressionNode)
    {
        if (!array_key_exists($expressionNode->getType(), self::$typesToMethods)) {
            throw new InvalidArgumentException(
                'Expression node of unsupported type "' . $expressionNode->getType() . '" given'
            );
        }

        return $this->{self::$typesToMethods[$expressionNode->getType()]}($expressionNode);
    }

    /**
     * Promotes the specified node to an AttributeExpression
     *
     * @param AttributeExpressionNode $expressionNode
     * @return AttributeExpression
     * @used-by promote
     */
    private function promoteAttributeExpression(AttributeExpressionNode $expressionNode)
    {
        return $this->expressionFactory->createAttributeExpression(
            $this->expressionNodePromoter->promote($expressionNode->getStructure()),
            $expressionNode->getAttributeName()
        );
    }

    /**
     * Promotes the specified node to a BinaryArithmeticExpression
     *
     * @param BinaryArithmeticExpressionNode $expressionNode
     * @return BinaryArithmeticExpression
     * @used-by promote
     */
    private function promoteBinaryArithmeticExpression(BinaryArithmeticExpressionNode $expressionNode)
    {
        return $this->expressionFactory->createBinaryArithmeticExpression(
            $this->expressionNodePromoter->promote($expressionNode->getLeftOperandExpression()),
            $expressionNode->getOperator(),
            $this->expressionNodePromoter->promote($expressionNode->getRightOperandExpression())
        );
    }

    /**
     * Promotes the specified node to an actual BooleanExpression
     *
     * @param BooleanExpressionNode $expressionNode
     * @return BooleanExpression
     * @used-by promote
     */
    private function promoteBooleanExpression(BooleanExpressionNode $expressionNode)
    {
        return $this->expressionFactory->createBooleanExpression($expressionNode->toNative());
    }

    /**
     * Promotes the specified node to an actual ComparisonExpression
     *
     * @param ComparisonExpressionNode $expressionNode
     * @return ComparisonExpression
     * @used-by promote
     */
    private function promoteComparisonExpression(ComparisonExpressionNode $expressionNode)
    {
        return $this->expressionFactory->createComparisonExpression(
            $this->expressionNodePromoter->promote($expressionNode->getLeftOperandExpression()),
            $expressionNode->getOperator(),
            $this->expressionNodePromoter->promote($expressionNode->getRightOperandExpression())
        );
    }

    /**
     * Promotes the specified node to an actual ConcatenationExpression
     *
     * @param ConcatenationExpressionNode $expressionNode
     * @return ConcatenationExpression
     * @used-by promote
     */
    private function promoteConcatenationExpression(ConcatenationExpressionNode $expressionNode)
    {
        $glueExpression = $expressionNode->getGlueExpression() ?
            $this->expressionNodePromoter->promote($expressionNode->getGlueExpression()) :
            null;

        return $this->expressionFactory->createConcatenationExpression(
            $this->expressionNodePromoter->promote($expressionNode->getOperandListExpression()),
            $glueExpression
        );
    }

    /**
     * Promotes the specified node to an actual ConditionalExpression
     *
     * @param ConditionalExpressionNode $expressionNode
     * @return ConditionalExpression
     * @used-by promote
     */
    private function promoteConditionalExpression(ConditionalExpressionNode $expressionNode)
    {
        return $this->expressionFactory->createConditionalExpression(
            $this->expressionNodePromoter->promote($expressionNode->getConditionExpression()),
            $this->expressionNodePromoter->promote($expressionNode->getConsequentExpression()),
            $this->expressionNodePromoter->promote($expressionNode->getAlternateExpression())
        );
    }

    /**
     * Promotes the specified node to an actual ConversionExpression
     *
     * @param ConversionExpressionNode $expressionNode
     * @return ConversionExpression
     * @used-by promote
     */
    private function promoteConversionExpression(ConversionExpressionNode $expressionNode)
    {
        return $this->expressionFactory->createConversionExpression(
            $this->expressionNodePromoter->promote($expressionNode->getExpression()),
            $expressionNode->getConversion()
        );
    }

    /**
     * Promotes the specified node to an actual DateTimeExpression
     *
     * @param DateTimeExpressionNode $expressionNode
     * @return DateTimeExpression
     * @used-by promote
     */
    private function promoteDateTimeExpression(DateTimeExpressionNode $expressionNode)
    {
        return $this->expressionFactory->createDateTimeExpression(
            $this->expressionNodePromoter->promote($expressionNode->getYearExpression()),
            $this->expressionNodePromoter->promote($expressionNode->getMonthExpression()),
            $this->expressionNodePromoter->promote($expressionNode->getDayExpression()),
            $this->expressionNodePromoter->promote($expressionNode->getHourExpression()),
            $this->expressionNodePromoter->promote($expressionNode->getMinuteExpression()),
            $this->expressionNodePromoter->promote($expressionNode->getSecondExpression()),
            $expressionNode->getMillisecondExpression() !== null ?
                $this->expressionNodePromoter->promote($expressionNode->getMillisecondExpression()) :
                null
        );
    }

    /**
     * Promotes the specified node to an actual DayExpression
     *
     * @param DayExpressionNode $expressionNode
     * @return DayExpression
     * @used-by promote
     */
    private function promoteDayExpression(DayExpressionNode $expressionNode)
    {
        return $this->expressionFactory->createDayExpression(
            $this->expressionNodePromoter->promote($expressionNode->getYearExpression()),
            $this->expressionNodePromoter->promote($expressionNode->getMonthExpression()),
            $this->expressionNodePromoter->promote($expressionNode->getDayExpression())
        );
    }

    /**
     * Promotes the specified node to an actual FunctionExpression
     *
     * @param FunctionExpressionNode $expressionNode
     * @return FunctionExpression
     * @used-by promote
     */
    private function promoteFunctionExpression(FunctionExpressionNode $expressionNode)
    {
        return $this->expressionFactory->createFunctionExpression(
            $expressionNode->getLibraryName(),
            $expressionNode->getFunctionName(),
            $this->bagNodePromoter->promoteExpressionBag(
                $expressionNode->getArgumentExpressionBag()
            ),
            // In development mode, fetch the resolved result type of the function call
            // (which can depend on the arguments being passed in)
            // - otherwise for production mode, the behaviour spec tree won't have been loaded
            // so we just allow any type
            $this->modeContext->getMode()->isDevelopment() ?
                $expressionNode->getResolvedResultType() :
                new AnyType(new NullValidationContext())
        );
    }

    /**
     * Promotes the specified node to an actual ListExpression
     *
     * @param ListExpressionNode $expressionNode
     * @return ListExpression
     * @used-by promote
     */
    private function promoteListExpression(ListExpressionNode $expressionNode)
    {
        return $this->expressionFactory->createListExpression(
            $this->bagNodePromoter->promoteExpressionList(
                $expressionNode->getExpressionList()
            )
        );
    }

    /**
     * Promotes the specified node to an actual MapExpression
     *
     * @param MapExpressionNode $expressionNode
     * @return MapExpression
     * @used-by promote
     */
    private function promoteMapExpression(MapExpressionNode $expressionNode)
    {
        return $this->expressionFactory->createMapExpression(
            $this->expressionNodePromoter->promote($expressionNode->getListExpression()),
            $expressionNode->getItemVariableName(),
            $expressionNode->getIndexVariableName(),
            $this->expressionNodePromoter->promote($expressionNode->getMapExpression())
        );
    }

    /**
     * Promotes the specified node to an actual NothingExpression
     *
     * @return NothingExpression
     * @used-by promote
     */
    private function promoteNothingExpression()
    {
        return $this->expressionFactory->createNothingExpression();
    }

    /**
     * Promotes the specified node to an actual NumberExpression
     *
     * @param NumberExpressionNode $expressionNode
     * @return NumberExpression
     * @used-by promote
     */
    private function promoteNumberExpression(NumberExpressionNode $expressionNode)
    {
        return $this->expressionFactory->createNumberExpression($expressionNode->toNative());
    }

    /**
     * Promotes the specified node to an actual StructureExpression
     *
     * @param StructureExpressionNode $expressionNode
     * @return StructureExpression
     * @used-by promote
     */
    private function promoteStructureExpression(StructureExpressionNode $expressionNode)
    {
        $attributeBag = $this->bagNodePromoter->promoteExpressionBag(
            $expressionNode->getExpressionBag()
        );

        return $this->expressionFactory->createStructureExpression($attributeBag);
    }

    /**
     * Promotes the specified node to an actual TextExpression
     *
     * @param TextExpressionNode $expressionNode
     * @return TextExpression
     * @used-by promote
     */
    private function promoteTextExpression(TextExpressionNode $expressionNode)
    {
        return $this->expressionFactory->createTextExpression($expressionNode->toNative());
    }

    /**
     * Promotes the specified node to an actual TranslationExpression
     *
     * @param TranslationExpressionNode $expressionNode
     * @return TranslationExpression
     * @used-by promote
     */
    private function promoteTranslationExpression(TranslationExpressionNode $expressionNode)
    {
        return $this->expressionFactory->createTranslationExpression(
            $expressionNode->getTranslationKey(),
            $this->bagNodePromoter->promoteExpressionBag($expressionNode->getArgumentExpressionBag())
        );
    }

    /**
     * Promotes the specified node to an actual VariableExpression
     *
     * @param VariableExpressionNode $expressionNode
     * @return VariableExpression
     * @used-by promote
     */
    private function promoteVariableExpression(VariableExpressionNode $expressionNode)
    {
        return $this->expressionFactory->createVariableExpression($expressionNode->getVariableName());
    }
}
