<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Parameter;

use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Bag\FixedStaticBagModelInterface;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class ParameterBagModel
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ParameterBagModel implements ParameterBagModelInterface
{
    /**
     * @var FixedStaticBagModelInterface
     */
    private $staticBagModel;

    /**
     * @param FixedStaticBagModelInterface $staticBagModel
     */
    public function __construct(FixedStaticBagModelInterface $staticBagModel)
    {
        $this->staticBagModel = $staticBagModel;
    }

    /**
     * {@inheritdoc}
     */
    public function assertValidArgument($name, StaticInterface $argumentStatic)
    {
        $this->staticBagModel->assertValidStatic($name, $argumentStatic);
    }

    /**
     * {@inheritdoc}
     */
    public function assertValidArgumentBag(StaticBagInterface $argumentStaticBag)
    {
        $this->staticBagModel->assertValidStaticBag($argumentStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function validateArgumentExpressionBag(
        ValidationContextInterface $validationContext,
        ExpressionBagNode $expressionBagNode
    ) {
        $this->staticBagModel->validateStaticExpressionBag(
            $validationContext,
            $expressionBagNode,
            'parameter'
        );
    }
}
