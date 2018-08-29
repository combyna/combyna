<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Config\Act;

use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Validator\Constraint\KnownFailureConstraint;

/**
 * Class UnknownNode
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UnknownNode extends AbstractActNode
{
    const TYPE = 'unknown';

    /**
     * @var string
     */
    private $contextDescription;

    /**
     * @param string $contextDescription
     */
    public function __construct($contextDescription)
    {
        $this->contextDescription = $contextDescription;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        // Make sure validation fails, because this node is invalid
        $specBuilder->addConstraint(
            new KnownFailureConstraint(
                '[Unknown node] ' .
                $this->contextDescription
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::TYPE;
    }
}
