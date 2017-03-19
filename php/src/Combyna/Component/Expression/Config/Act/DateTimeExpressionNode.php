<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Act;

use Combyna\Component\Expression\DateTimeExpression;
use Combyna\Component\Expression\NumberExpression;
use Combyna\Component\Expression\StaticDateTimeExpression;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Type\StaticType;

/**
 * Class DateTimeExpressionNode
 *
 * Evaluates to a date and a specific time of day
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DateTimeExpressionNode extends AbstractExpressionNode
{
    const TYPE = DateTimeExpression::TYPE;

    /**
     * @var ExpressionNodeInterface
     */
    private $dayExpression;

    /**
     * @var ExpressionNodeInterface
     */
    private $hourExpression;

    /**
     * @var ExpressionNodeInterface|null
     */
    private $millisecondExpression;

    /**
     * @var ExpressionNodeInterface
     */
    private $minuteExpression;

    /**
     * @var ExpressionNodeInterface
     */
    private $monthExpression;

    /**
     * @var ExpressionNodeInterface
     */
    private $secondExpression;

    /**
     * @var ExpressionNodeInterface
     */
    private $yearExpression;

    /**
     * @param ExpressionNodeInterface $yearExpression
     * @param ExpressionNodeInterface $monthExpression
     * @param ExpressionNodeInterface $dayExpression
     * @param ExpressionNodeInterface $hourExpression
     * @param ExpressionNodeInterface $minuteExpression
     * @param ExpressionNodeInterface $secondExpression
     * @param ExpressionNodeInterface|null $millisecondExpression
     */
    public function __construct(
        ExpressionNodeInterface $yearExpression,
        ExpressionNodeInterface $monthExpression,
        ExpressionNodeInterface $dayExpression,
        ExpressionNodeInterface $hourExpression,
        ExpressionNodeInterface $minuteExpression,
        ExpressionNodeInterface $secondExpression,
        ExpressionNodeInterface $millisecondExpression = null
    ) {
        $this->dayExpression = $dayExpression;
        $this->hourExpression = $hourExpression;
        $this->millisecondExpression = $millisecondExpression;
        $this->minuteExpression = $minuteExpression;
        $this->monthExpression = $monthExpression;
        $this->secondExpression = $secondExpression;
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
     * Fetches the hour expression
     *
     * @return ExpressionNodeInterface
     */
    public function getHourExpression()
    {
        return $this->hourExpression;
    }

    /**
     * Fetches the millisecond expression, if set
     *
     * @return ExpressionNodeInterface|null
     */
    public function getMillisecondExpression()
    {
        return $this->millisecondExpression;
    }

    /**
     * Fetches the minute expression
     *
     * @return ExpressionNodeInterface
     */
    public function getMinuteExpression()
    {
        return $this->minuteExpression;
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
     * Fetches the second expression
     *
     * @return ExpressionNodeInterface
     */
    public function getSecondExpression()
    {
        return $this->secondExpression;
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
        return new StaticType(StaticDateTimeExpression::class);
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
        $this->hourExpression->validate($subValidationContext);
        $this->minuteExpression->validate($subValidationContext);
        $this->secondExpression->validate($subValidationContext);

        if ($this->millisecondExpression !== null) {
            $this->millisecondExpression->validate($subValidationContext);
        }

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
        $subValidationContext->assertResultType(
            $this->hourExpression,
            new StaticType(NumberExpression::class),
            'hour'
        );
        $subValidationContext->assertResultType(
            $this->minuteExpression,
            new StaticType(NumberExpression::class),
            'minute'
        );
        $subValidationContext->assertResultType(
            $this->secondExpression,
            new StaticType(NumberExpression::class),
            'second'
        );

        if ($this->millisecondExpression !== null) {
            $subValidationContext->assertResultType(
                $this->millisecondExpression,
                new StaticType(NumberExpression::class),
                'millisecond'
            );
        }
    }
}
