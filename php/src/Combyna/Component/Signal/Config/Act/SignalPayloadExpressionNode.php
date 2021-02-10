<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal\Config\Act;

use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Expression\Config\Act\AbstractExpressionNode;
use Combyna\Component\Signal\Expression\SignalPayloadExpression;
use Combyna\Component\Signal\Validation\Constraint\InsideSignalHandlerConstraint;
use Combyna\Component\Signal\Validation\Constraint\SourceSignalHasPayloadStaticConstraint;
use Combyna\Component\Signal\Validation\Query\SourceSignalPayloadStaticTypeQuery;
use Combyna\Component\Validator\Type\QueriedResultTypeDeterminer;

/**
 * Class SignalPayloadExpressionNode
 *
 * Fetches a static from the current signal's payload
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalPayloadExpressionNode extends AbstractExpressionNode
{
    const TYPE = SignalPayloadExpression::TYPE;

    /**
     * @var string
     */
    private $staticName;

    /**
     * @param string $staticName
     */
    public function __construct($staticName)
    {
        $this->staticName = $staticName;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addConstraint(new InsideSignalHandlerConstraint());
        $specBuilder->addConstraint(new SourceSignalHasPayloadStaticConstraint($this->staticName));
    }

    /**
     * {@inheritdoc}
     */
    public function getResultTypeDeterminer()
    {
        return new QueriedResultTypeDeterminer(new SourceSignalPayloadStaticTypeQuery($this->staticName), $this);
    }

    /**
     * Fetches the name of the payload static to be fetched
     *
     * @return string
     */
    public function getStaticName()
    {
        return $this->staticName;
    }
}
