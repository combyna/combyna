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
use Combyna\Component\Expression\Validation\Constraint\VariableExistsConstraint;
use Combyna\Component\Expression\Validation\Query\VariableTypeQuery;
use Combyna\Component\Expression\VariableExpression;
use Combyna\Component\Validator\Type\QueriedResultTypeDeterminer;

/**
 * Class VariableExpressionNode
 *
 * Returns the static value of a variable in the current scope
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class VariableExpressionNode extends AbstractExpressionNode
{
    const TYPE = VariableExpression::TYPE;

    /**
     * @var string
     */
    private $variableName;

    /**
     * @param string $variableName
     */
    public function __construct($variableName)
    {
        $this->variableName = $variableName;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addConstraint(new VariableExistsConstraint($this->variableName));
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return self::TYPE . ':' . $this->variableName;
    }

    /**
     * {@inheritdoc}
     */
    public function getResultTypeDeterminer()
    {
        return new QueriedResultTypeDeterminer(new VariableTypeQuery($this->variableName), $this);
    }

    /**
     * Fetches the name of the variable this expression should fetch
     *
     * @return string
     */
    public function getVariableName()
    {
        return $this->variableName;
    }
}
