<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Act;

use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Validator\Constraint\KnownFailureConstraint;
use Combyna\Component\Validator\Type\UnresolvedTypeDeterminer;

/**
 * Class UnknownExpressionTypeNode
 *
 * Represents an expression node in the ACT with an unknown type, making it invalid
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UnknownExpressionTypeNode extends AbstractExpressionNode implements InvalidExpressionNodeInterface
{
    const TYPE = 'unknown';

    /**
     * @var string|null
     */
    private $type;

    /**
     * @param string|null $type
     */
    public function __construct($type)
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $type = $this->type !== null ? $this->type : '[missing]';

        $specBuilder->addConstraint(new KnownFailureConstraint('Expression is of unknown type "' . $type . '"'));
    }

    /**
     * {@inheritdoc}
     */
    public function getResultTypeDeterminer()
    {
        $type = $this->type !== null ? $this->type : '[missing]';

        return new UnresolvedTypeDeterminer('Expression type "' . $type . '"');
    }

    /**
     * Fetches the type of expression that is unknown, eg. `some_unknown_type`
     *
     * @return string
     */
    public function getUnknownType()
    {
        return $this->type;
    }
}
