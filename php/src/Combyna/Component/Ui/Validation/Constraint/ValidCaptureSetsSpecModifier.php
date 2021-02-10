<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Validation\Constraint;

use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Behaviour\Spec\BehaviourSpecModifierInterface;

/**
 * Class ValidCaptureSetsSpecModifier
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ValidCaptureSetsSpecModifier implements BehaviourSpecModifierInterface
{
    /**
     * @var ExpressionBagNode
     */
    private $captureExpressionBagNode;

    /**
     * @param ExpressionBagNode $captureExpressionBagNode
     */
    public function __construct(ExpressionBagNode $captureExpressionBagNode)
    {
        $this->captureExpressionBagNode = $captureExpressionBagNode;
    }

    /**
     * {@inheritdoc}
     */
    public function modifySpecBuilder(BehaviourSpecBuilderInterface $specBuilder)
    {
        foreach ($this->captureExpressionBagNode->getExpressionNames() as $captureName) {
            // Make sure that any captures this widget sets are defined by an ancestor
            $specBuilder->addConstraint(
                CaptureIsDefinedConstraint::createIntendingToSet($captureName)
            );

            $specBuilder->addConstraint(
                new CaptureHasDefaultIfOptionalConstraint($captureName)
            );

            $specBuilder->addConstraint(
                new CaptureHasNoDefaultIfNotOptionalConstraint($captureName)
            );

            // Make sure that any captures this widget sets are defined with the correct type
            $specBuilder->addConstraint(
                new CaptureHasCorrectTypeConstraint(
                    $captureName,
                    $this->captureExpressionBagNode->getExpression($captureName)
                )
            );
        }
    }
}
