<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Program\Validation\Validator;

use Combyna\Component\App\Config\Act\AppNode;
use Combyna\Component\Behaviour\Node\StructuredNodeInterface;
use Combyna\Component\Validator\Context\RootValidationContextInterface;

/**
 * Interface NodeValidatorInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface NodeValidatorInterface
{
    /**
     * Validates an ACT node inside an app, populating a ValidationContext with any violations
     *
     * @param StructuredNodeInterface $node
     * @param AppNode $appNode
     * @return RootValidationContextInterface
     */
    public function validate(
        StructuredNodeInterface $node,
        AppNode $appNode
    );
}
