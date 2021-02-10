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
use Combyna\Component\Ui\Expression\CaptureExpression;
use Combyna\Component\Ui\Validation\Constraint\CaptureIsDefinedConstraint;
use Combyna\Component\Ui\Validation\Query\CaptureTypeQuery;
use Combyna\Component\Validator\Type\QueriedResultTypeDeterminer;

/**
 * Class CaptureExpressionNode
 *
 * Fetches a capture for the current point in the ACT
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CaptureExpressionNode extends AbstractExpressionNode
{
    const TYPE = CaptureExpression::TYPE;

    /**
     * @var string
     */
    private $captureName;

    /**
     * @param string $captureName
     */
    public function __construct($captureName)
    {
        $this->captureName = $captureName;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addConstraint(
            CaptureIsDefinedConstraint::createIntendingToRead($this->captureName)
        );
    }

    /**
     * Fetches the name of the capture to fetch
     *
     * @return string
     */
    public function getCaptureName()
    {
        return $this->captureName;
    }

    /**
     * {@inheritdoc}
     */
    public function getResultTypeDeterminer()
    {
        return new QueriedResultTypeDeterminer(
            new CaptureTypeQuery($this->captureName),
            $this
        );
    }
}
