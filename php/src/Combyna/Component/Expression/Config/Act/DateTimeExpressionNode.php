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
use Combyna\Component\Expression\DateTimeExpression;
use Combyna\Component\Expression\NumberExpression;
use Combyna\Component\Expression\StaticDateTimeExpression;
use Combyna\Component\Expression\Validation\Constraint\ResultTypeConstraint;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Validator\Type\PresolvedTypeDeterminer;

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
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addChildNode($this->yearExpression);
        $specBuilder->addChildNode($this->monthExpression);
        $specBuilder->addChildNode($this->dayExpression);
        $specBuilder->addChildNode($this->hourExpression);
        $specBuilder->addChildNode($this->minuteExpression);
        $specBuilder->addChildNode($this->secondExpression);

        if ($this->millisecondExpression !== null) {
            $specBuilder->addChildNode($this->millisecondExpression);
        }

        $specBuilder->addConstraint(
            new ResultTypeConstraint(
                $this->yearExpression,
                new PresolvedTypeDeterminer(new StaticType(NumberExpression::class)),
                'year'
            )
        );
        $specBuilder->addConstraint(
            new ResultTypeConstraint(
                $this->monthExpression,
                new PresolvedTypeDeterminer(new StaticType(NumberExpression::class)),
                'month'
            )
        );
        $specBuilder->addConstraint(
            new ResultTypeConstraint(
                $this->dayExpression,
                new PresolvedTypeDeterminer(new StaticType(NumberExpression::class)),
                'day'
            )
        );
        $specBuilder->addConstraint(
            new ResultTypeConstraint(
                $this->hourExpression,
                new PresolvedTypeDeterminer(new StaticType(NumberExpression::class)),
                'hour'
            )
        );
        $specBuilder->addConstraint(
            new ResultTypeConstraint(
                $this->minuteExpression,
                new PresolvedTypeDeterminer(new StaticType(NumberExpression::class)),
                'minute'
            )
        );
        $specBuilder->addConstraint(
            new ResultTypeConstraint(
                $this->secondExpression,
                new PresolvedTypeDeterminer(new StaticType(NumberExpression::class)),
                'second'
            )
        );

        if ($this->millisecondExpression !== null) {
            $specBuilder->addConstraint(
                new ResultTypeConstraint(
                    $this->millisecondExpression,
                    new PresolvedTypeDeterminer(new StaticType(NumberExpression::class)),
                    'millisecond'
                )
            );
        }
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
     * {@inheritdoc}
     */
    public function getResultTypeDeterminer()
    {
        return new PresolvedTypeDeterminer(new StaticType(StaticDateTimeExpression::class));
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
}
