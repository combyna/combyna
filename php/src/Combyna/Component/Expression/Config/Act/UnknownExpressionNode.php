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
use Combyna\Component\Config\Act\DynamicActNodeInterface;
use Combyna\Component\Validator\Config\Act\DynamicActNodeAdopterInterface;
use Combyna\Component\Validator\Constraint\KnownFailureConstraint;
use Combyna\Component\Validator\Type\UnresolvedTypeDeterminer;

/**
 * Class UnknownExpressionNode
 *
 * Represents an expression node in the ACT that cannot be loaded for some reason
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UnknownExpressionNode extends AbstractExpressionNode implements DynamicActNodeInterface, InvalidExpressionNodeInterface
{
    const TYPE = 'unknown';

    /**
     * @var string
     */
    private $contextDescription;

    /**
     * @param string $contextDescription
     * @param DynamicActNodeAdopterInterface $dynamicActNodeAdopter
     */
    public function __construct($contextDescription, DynamicActNodeAdopterInterface $dynamicActNodeAdopter)
    {
        $this->contextDescription = $contextDescription;

        $dynamicActNodeAdopter->adoptDynamicActNode($this);
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addConstraint(new KnownFailureConstraint($this->contextDescription));
    }

    /**
     * {@inheritdoc}
     */
    public function getResultTypeDeterminer()
    {
        return new UnresolvedTypeDeterminer($this->contextDescription);
    }
}
