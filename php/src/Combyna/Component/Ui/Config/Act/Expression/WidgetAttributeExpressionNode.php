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
use Combyna\Component\Ui\Expression\WidgetAttributeExpression;
use Combyna\Component\Ui\Validation\Constraint\CompoundWidgetDefinitionHasAttributeConstraint;
use Combyna\Component\Ui\Validation\Constraint\InsideCompoundWidgetDefinitionRootWidgetConstraint;
use Combyna\Component\Ui\Validation\Query\WidgetAttributeTypeQuery;
use Combyna\Component\Validator\Type\QueriedResultTypeDeterminer;

/**
 * Class WidgetAttributeExpressionNode
 *
 * Fetches the value of an attribute specified for the compound widget
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetAttributeExpressionNode extends AbstractExpressionNode
{
    const TYPE = WidgetAttributeExpression::TYPE;

    /**
     * @var string
     */
    private $attributeName;

    /**
     * @param string $attributeName
     */
    public function __construct($attributeName)
    {
        $this->attributeName = $attributeName;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addConstraint(
            new InsideCompoundWidgetDefinitionRootWidgetConstraint()
        );
        $specBuilder->addConstraint(
            new CompoundWidgetDefinitionHasAttributeConstraint($this->attributeName)
        );
    }

    /**
     * Fetches the name of the attribute to fetch
     *
     * @return string
     */
    public function getAttributeName()
    {
        return $this->attributeName;
    }

    /**
     * {@inheritdoc}
     */
    public function getResultTypeDeterminer()
    {
        return new QueriedResultTypeDeterminer(
            new WidgetAttributeTypeQuery($this->attributeName),
            $this
        );
    }
}
