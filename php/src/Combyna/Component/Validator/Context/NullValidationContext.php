<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator\Context;

use Combyna\Component\Behaviour\Node\StructuredNodeInterface;
use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Validator\Context\Specifier\SubValidationContextSpecifierInterface;
use Combyna\Component\Validator\Query\ActNodeQueryInterface;
use Combyna\Component\Validator\Query\BooleanQueryInterface;
use Combyna\Component\Validator\Query\ResultTypeQueryInterface;
use Combyna\Component\Validator\ViolationInterface;
use RuntimeException;

/**
 * Class NullValidationContext
 *
 * Represents a fake validation state, for use at production runtime when no validation is performed
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NullValidationContext implements ValidationContextInterface
{
    const MESSAGE = 'Non-validation context cannot perform validation';

    /**
     * {@inheritdoc}
     */
    public function addDivisionByZeroViolation()
    {
        throw new RuntimeException(self::MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function addGenericViolation($description)
    {
        throw new RuntimeException(self::MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function addTypeMismatchViolation(
        TypeInterface $expectedType,
        TypeInterface $actualType,
        $contextDescription
    ) {
        throw new RuntimeException(self::MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function addViolation(ViolationInterface $violation)
    {
        throw new RuntimeException(self::MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function createActNodeQueryRequirement(ActNodeQueryInterface $query, ActNodeInterface $nodeToQueryFrom)
    {
        throw new RuntimeException(self::MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function createBooleanQueryRequirement(BooleanQueryInterface $query)
    {
        throw new RuntimeException(self::MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function createSubContext(
        SubValidationContextSpecifierInterface $subContextSpecifier,
        StructuredNodeInterface $structuredNode,
        BehaviourSpecInterface $behaviourSpec
    ) {
        throw new RuntimeException(self::MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function createTypeQueryRequirement(ResultTypeQueryInterface $query)
    {
        throw new RuntimeException(self::MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentActNode()
    {
        throw new RuntimeException(self::MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentParentActNode()
    {
        throw new RuntimeException(self::MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescendantSpecsWithQuery(QuerySpecifierInterface $querySpecifier)
    {
        throw new RuntimeException(self::MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function getExpressionResultType(ExpressionNodeInterface $expressionNode)
    {
        throw new RuntimeException(self::MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        throw new RuntimeException(self::MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function getSubjectActNode()
    {
        throw new RuntimeException(self::MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function getSubValidationContext()
    {
        throw new RuntimeException(self::MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function queryForActNode(
        ActNodeQueryInterface $actNodeQuery,
        ActNodeInterface $nodeToQueryFrom
    ) {
        throw new RuntimeException(self::MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function queryForBoolean(
        BooleanQueryInterface $booleanQuery,
        ActNodeInterface $nodeToQueryFrom
    ) {
        throw new RuntimeException(self::MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function queryForResultType(
        ResultTypeQueryInterface $resultTypeQuery,
        ActNodeInterface $nodeToQueryFrom
    ) {
        throw new RuntimeException(self::MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function throwIfViolated()
    {
        throw new RuntimeException(self::MESSAGE);
    }
}
