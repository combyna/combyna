<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator;

use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Validator\Context\RootValidationContextInterface;

/**
 * Interface ValidatorInterface
 *
 *
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ValidatorInterface
{
    /**
     * Validates an expression, populating a ValidationContext with any violations
     *
     * @param ActNodeInterface $actNode
     * @param EnvironmentNode $environmentNode
     * @return RootValidationContextInterface
     */
    public function validate(ActNodeInterface $actNode, EnvironmentNode $environmentNode);
}
