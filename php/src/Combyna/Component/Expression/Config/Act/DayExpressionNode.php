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
use Combyna\Component\Expression\DayExpression;
use Combyna\Component\Expression\NumberExpression;
use Combyna\Component\Expression\StaticDayExpression;
use Combyna\Component\Expression\Validation\Constraint\ResultTypeConstraint;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Validator\Type\PresolvedTypeDeterminer;

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
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addChildNode($this->yearExpression);
        $specBuilder->addChildNode($this->monthExpression);
        $specBuilder->addChildNode($this->dayExpression);

        $specBuilder->addConstraint(
            new ResultTypeConstraint(
                $this->yearExpression,
                new StaticType(NumberExpression::class),
                'year'
            )
        );
        $specBuilder->addConstraint(
            new ResultTypeConstraint(
                $this->monthExpression,
                new StaticType(NumberExpression::class),
                'month'
            )
        );
        $specBuilder->addConstraint(
            new ResultTypeConstraint(
                $this->dayExpression,
                new StaticType(NumberExpression::class),
                'day'
            )
        );
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
    public function getResultTypeDeterminer()
    {
        return new PresolvedTypeDeterminer(new StaticType(StaticDayExpression::class));
    }
}
