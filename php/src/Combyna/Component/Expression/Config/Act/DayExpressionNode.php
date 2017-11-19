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

use Combyna\Component\Expression\DayExpression;
use Combyna\Component\Expression\NumberExpression;
use Combyna\Component\Expression\StaticDayExpression;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Type\StaticType;

/**
 * Class DayExpressionNode
 *
 * Evaluates to a date that represents a single but entire day
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DayExpressionNode extends AbstractExpressionNode
{
    const TYPE = DayExpression::TYPE;

    /**
     * @var ExpressionNodeInterface
     */
    private $dayExpression;

    /**
     * @var ExpressionNodeInterface
     */
    private $monthExpression;

    /**
     * @var ExpressionNodeInterface
     */
    private $yearExpression;

    /**
     * @param ExpressionNodeInterface $yearExpression
     * @param ExpressionNodeInterface $monthExpression
     * @param ExpressionNodeInterface $dayExpression
     */
    public function __construct(
        ExpressionNodeInterface $yearExpression,
        ExpressionNodeInterface $monthExpression,
        ExpressionNodeInterface $dayExpression
    ) {
        $this->dayExpression = $dayExpression;
        $this->monthExpression = $monthExpression;
        $this->yearExpression = $yearExpression;
    }

    /**
     * Fetches the day expression
     *
     * @return ExpressionNodeInterface
     */
    public function getDayExpression()
    {
        return $this->dayExpression;
    }

    /**
     * Fetches the month expression
     *
     * @return ExpressionNodeInterface
     */
    public function getMonthExpression()
    {
        return $this->monthExpression;
    }

    /**
     * Fetches the year expression
     *
     * @return ExpressionNodeInterface
     */
    public function getYearExpression()
    {
        return $this->yearExpression;
    }

    /**
     * {@inheritdoc}
     */
    public function getResultType(ValidationContextInterface $validationContext)
    {
        return new StaticType(StaticDayExpression::class);
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        $this->yearExpression->validate($subValidationContext);
        $this->monthExpression->validate($subValidationContext);
        $this->dayExpression->validate($subValidationContext);

        $subValidationContext->assertResultType(
            $this->yearExpression,
            new StaticType(NumberExpression::class),
            'year'
        );
        $subValidationContext->assertResultType(
            $this->monthExpression,
            new StaticType(NumberExpression::class),
            'month'
        );
        $subValidationContext->assertResultType(
            $this->dayExpression,
            new StaticType(NumberExpression::class),
            'day'
        );
    }
}
