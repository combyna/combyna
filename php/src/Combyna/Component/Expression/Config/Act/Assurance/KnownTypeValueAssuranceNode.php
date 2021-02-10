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
use Combyna\Component\Expression\Assurance\KnownTypeValueAssurance;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Validator\Type\TypeDeterminerInterface;

/**
 * Class KnownTypeValueAssuranceNode
 *
 * Ensures that the given expression evaluates to a specific type
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class KnownTypeValueAssuranceNode extends AbstractActNode implements AssuranceNodeInterface
{
    const TYPE = 'known-type-value-assurance';

    /**
     * @var ExpressionNodeInterface
     */
    private $inputExpressionNode;

    /**
     * @var TypeDeterminerInterface
     */
    private $knownTypeDeterminer;

    /**
     * @var string
     */
    private $staticName;

    /**
     * @param ExpressionNodeInterface $inputExpressionNode
     * @param string $name Name to expose the assured static to sub-expressions as
     * @param TypeDeterminerInterface $knownTypeDeterminer
     */
    public function __construct(
        ExpressionNodeInterface $inputExpressionNode,
        $name,
        TypeDeterminerInterface $knownTypeDeterminer
    ) {
        $this->inputExpressionNode = $inputExpressionNode;
        $this->knownTypeDeterminer = $knownTypeDeterminer;
        $this->staticName = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addChildNode($this->inputExpressionNode);

        // TODO: Check that the input expression can possibly resolve to the known type
        //       (the reverse of the ResultTypeConstraint) - if it cannot then there is a known bug
        //       with the logic of the app
    }

    /**
     * {@inheritdoc}
     */
    public function getAssuredStaticName()
    {
        return $this->staticName;
    }

    /**
     * {@inheritdoc}
     */
    public function getAssuredStaticTypeDeterminer()
    {
        // The only possible type this assured static can evaluate to is the known type
        return $this->knownTypeDeterminer;
    }

    /**
     * {@inheritdoc}
     */
    public function getConstraint()
    {
        return KnownTypeValueAssurance::TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function getInputExpression()
    {
        return $this->inputExpressionNode;
    }
}
