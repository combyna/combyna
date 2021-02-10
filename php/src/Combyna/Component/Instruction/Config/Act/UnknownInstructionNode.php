<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Instruction\Config\Act;

use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Validator\Constraint\KnownFailureConstraint;

/**
 * Class UnknownInstructionNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UnknownInstructionNode extends AbstractActNode implements InstructionNodeInterface
{
    /**
     * @var string|null
     */
    private $type;

    /**
     * @param string|null $type
     */
    public function __construct($type = null)
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $type = $this->type !== null ? $this->type : '[missing]';

        // Make sure validation fails, as this node is invalid
        $specBuilder->addConstraint(new KnownFailureConstraint(sprintf(
            'Instruction is of unknown type "%s"',
            $type
        )));
    }
}
