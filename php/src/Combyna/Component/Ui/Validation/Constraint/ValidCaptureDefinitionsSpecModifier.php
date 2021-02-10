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

use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNodeInterface;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Behaviour\Spec\BehaviourSpecModifierInterface;

/**
 * Class ValidCaptureDefinitionsSpecModifier
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ValidCaptureDefinitionsSpecModifier implements BehaviourSpecModifierInterface
{
    /**
     * @var FixedStaticBagModelNodeInterface
     */
    private $captureStaticBagModelNode;

    /**
     * @param FixedStaticBagModelNodeInterface $captureStaticBagModelNode
     */
    public function __construct(FixedStaticBagModelNodeInterface $captureStaticBagModelNode)
    {
        $this->captureStaticBagModelNode = $captureStaticBagModelNode;
    }

    /**
     * {@inheritdoc}
     */
    public function modifySpecBuilder(BehaviourSpecBuilderInterface $specBuilder)
    {
        foreach ($this->captureStaticBagModelNode->getStaticDefinitionNames() as $captureName) {
            // Make sure that this widget defines no capture that an ancestor widget also defines
            $specBuilder->addConstraint(new CaptureIsNotShadowedConstraint($captureName));

            // Make sure that any captures this widget defines are set exactly once by a single descendant
            $specBuilder->addConstraint(new CaptureIsSetExactlyOnceConstraint($captureName));
        }
    }
}
