<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Act\Assurance;

use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Config\Act\AbstractActNode;
use Combyna\Component\Type\UnresolvedType;
use Combyna\Component\Validator\Constraint\KnownFailureConstraint;
use Combyna\Component\Validator\Type\PresolvedTypeDeterminer;

/**
 * Class UnknownAssuranceConstraintNode
 *
 * Represents an assurance node in the ACT with an unknown type, making it invalid
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UnknownAssuranceConstraintNode extends AbstractActNode implements AssuranceNodeInterface
{
    const TYPE = 'unknown';

    /**
     * @var string
     */
    private $assuredStaticName;

    /**
     * @var string
     */
    private $constraintName;

    /**
     * @param string|null $assuredStaticName
     * @param string|null $constraintName
     */
    public function __construct($assuredStaticName, $constraintName)
    {
        $this->assuredStaticName = $assuredStaticName;
        $this->constraintName = $constraintName;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $constraintName = $this->constraintName !== null ? $this->constraintName : '[missing]';
        $assuredStaticName = $this->constraintName !== null ? $this->constraintName : '[missing]';

        $specBuilder->addConstraint(
            new KnownFailureConstraint(
                sprintf(
                    'Assurance for assured static "%s" has unknown constraint "%s"',
                    $assuredStaticName,
                    $constraintName
                )
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAssuredStaticName()
    {
        return $this->assuredStaticName;
    }

    /**
     * {@inheritdoc}
     */
    public function getAssuredStaticTypeDeterminer()
    {
        $constraintName = $this->constraintName !== null ? $this->constraintName : '[missing]';

        return new PresolvedTypeDeterminer(new UnresolvedType(sprintf(
            'Unknown assurance constraint "%s"',
            $constraintName
        )));
    }

    /**
     * {@inheritdoc}
     */
    public function getConstraint()
    {
        return $this->constraintName;
    }
}
