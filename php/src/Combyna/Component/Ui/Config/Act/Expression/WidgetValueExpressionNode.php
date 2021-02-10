<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Config\Act\Expression;

use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Expression\Config\Act\AbstractExpressionNode;
use Combyna\Component\Ui\Expression\WidgetValueExpression;
use Combyna\Component\Ui\Validation\Constraint\InsideDefinedWidgetConstraint;
use Combyna\Component\Ui\Validation\Constraint\WidgetHasValueConstraint;
use Combyna\Component\Ui\Validation\Query\WidgetValueTypeQuery;
use Combyna\Component\Validator\Type\QueriedResultTypeDeterminer;

/**
 * Class WidgetValueExpressionNode
 *
 * Fetches a value for the current defined widget
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetValueExpressionNode extends AbstractExpressionNode
{
    const TYPE = WidgetValueExpression::TYPE;

    /**
     * @var string
     */
    private $valueName;

    /**
     * @param string $valueName
     */
    public function __construct($valueName)
    {
        $this->valueName = $valueName;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addConstraint(
            new InsideDefinedWidgetConstraint()
        );
        $specBuilder->addConstraint(
            new WidgetHasValueConstraint($this->valueName)
        );
    }

    /**
     * Fetches the name of the value to fetch
     *
     * @return string
     */
    public function getValueName()
    {
        return $this->valueName;
    }

    /**
     * {@inheritdoc}
     */
    public function getResultTypeDeterminer()
    {
        return new QueriedResultTypeDeterminer(
            new WidgetValueTypeQuery($this->valueName),
            $this
        );
    }
}
