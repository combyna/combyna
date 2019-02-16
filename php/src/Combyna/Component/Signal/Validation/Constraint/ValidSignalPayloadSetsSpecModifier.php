<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal\Validation\Constraint;

use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Behaviour\Spec\BehaviourSpecModifierInterface;

/**
 * Class ValidSignalPayloadSetsSpecModifier
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ValidSignalPayloadSetsSpecModifier implements BehaviourSpecModifierInterface
{
    /**
     * @var ExpressionBagNode
     */
    private $payloadExpressionBagNode;

    /**
     * @var string
     */
    private $signalLibraryName;

    /**
     * @var string
     */
    private $signalName;

    /**
     * @param string $signalLibraryName
     * @param string $signalName
     * @param ExpressionBagNode $payloadExpressionBagNode
     */
    public function __construct(
        $signalLibraryName,
        $signalName,
        ExpressionBagNode $payloadExpressionBagNode
    ) {
        $this->payloadExpressionBagNode = $payloadExpressionBagNode;
        $this->signalLibraryName = $signalLibraryName;
        $this->signalName = $signalName;
    }

    /**
     * {@inheritdoc}
     */
    public function modifySpecBuilder(BehaviourSpecBuilderInterface $specBuilder)
    {
        foreach ($this->payloadExpressionBagNode->getExpressionNames() as $staticName) {
            // Make sure that any payload statics this bag sets are defined by the signal definition
            $specBuilder->addConstraint(
                new SignalDefinitionHasPayloadStaticConstraint(
                    $this->signalLibraryName,
                    $this->signalName,
                    $staticName
                )
            );
        }
    }
}
