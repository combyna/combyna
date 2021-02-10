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
use Combyna\Component\Expression\AssuredExpression;
use Combyna\Component\Expression\Validation\Constraint\AssuredStaticExistsConstraint;
use Combyna\Component\Expression\Validation\Query\AssuredStaticTypeQuery;
use Combyna\Component\Validator\Type\QueriedResultTypeDeterminer;

/**
 * Class AssuredExpressionNode
 *
 * Returns an "assured" static, evaluated by an ancestor expression
 * and guaranteed to satisfy a condition
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AssuredExpressionNode extends AbstractExpressionNode
{
    const TYPE = AssuredExpression::TYPE;

    /**
     * @var string
     */
    private $assuredStaticName;

    /**
     * @param string $assuredStaticName
     */
    public function __construct($assuredStaticName)
    {
        $this->assuredStaticName = $assuredStaticName;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addConstraint(
            new AssuredStaticExistsConstraint($this->assuredStaticName)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getResultTypeDeterminer()
    {
        return new QueriedResultTypeDeterminer(
            new AssuredStaticTypeQuery(
                $this->assuredStaticName
            ),
            $this
        );
    }

    /**
     * Fetches the name of the assured static
     *
     * @return string
     */
    public function getAssuredStaticName()
    {
        return $this->assuredStaticName;
    }
}
