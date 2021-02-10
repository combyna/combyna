<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Validation\Context;

use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Expression\Config\Act\Assurance\AssuranceNodeInterface;
use Combyna\Component\Expression\Validation\Query\AssuranceNodeQuery;
use Combyna\Component\Expression\Validation\Query\AssuredStaticExistsQuery;
use Combyna\Component\Expression\Validation\Query\AssuredStaticTypeQuery;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Validator\Context\SubValidationContextInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class AssuredSubValidationContext
 *
 * Created while validating the inside of a guard expression,
 * to allow referring back up to its assurances
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AssuredSubValidationContext implements AssuredSubValidationContextInterface
{
    /**
     * @var AssuranceNodeInterface[]
     */
    private $assuranceNodes;

    /**
     * @var ActNodeInterface
     */
    private $guardExpressionNode;

    /**
     * @var BehaviourSpecInterface
     */
    private $guardExpressionNodeBehaviourSpec;

    /**
     * @var SubValidationContextInterface
     */
    private $parentContext;

    /**
     * @var ActNodeInterface
     */
    private $subjectNode;

    /**
     * @param SubValidationContextInterface $parentContext
     * @param ActNodeInterface $guardExpressionNode
     * @param BehaviourSpecInterface $guardExpressionNodeBehaviourSpec
     * @param AssuranceNodeInterface[] $assuranceNodes
     * @param ActNodeInterface $subjectNode
     */
    public function __construct(
        SubValidationContextInterface $parentContext,
        ActNodeInterface $guardExpressionNode,
        BehaviourSpecInterface $guardExpressionNodeBehaviourSpec,
        array $assuranceNodes,
        ActNodeInterface $subjectNode
    ) {
        $this->assuranceNodes = $assuranceNodes;
        $this->guardExpressionNode = $guardExpressionNode;
        $this->guardExpressionNodeBehaviourSpec = $guardExpressionNodeBehaviourSpec;
        $this->parentContext = $parentContext;
        $this->subjectNode = $subjectNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentActNode()
    {
        return $this->guardExpressionNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getBehaviourSpec()
    {
        return $this->guardExpressionNodeBehaviourSpec;
    }

    /**
     * {@inheritdoc}
     */
    public function getParentContext()
    {
        return $this->parentContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->parentContext->getPath();
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryClassToQueryCallableMap()
    {
        return [
            AssuranceNodeQuery::class => [$this, 'queryForAssuranceNode'],
            AssuredStaticExistsQuery::class => [$this, 'queryForAssuredStaticExistence'],
            AssuredStaticTypeQuery::class => [$this, 'queryForAssuredStaticType']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getSubjectActNode()
    {
        return $this->subjectNode;
    }

    /**
     * Fetches the assurance node for the specified assured static
     *
     * @param AssuranceNodeQuery $query
     * @return AssuranceNodeInterface|null
     */
    public function queryForAssuranceNode(AssuranceNodeQuery $query)
    {
        foreach ($this->assuranceNodes as $assuranceNode) {
            if ($assuranceNode->getAssuredStaticName() === $query->getAssuredStaticName()) {
                // We've discovered that this guard expression _does_ define the requested assured static
                return $assuranceNode;
            }
        }

        // This guard expression doesn't define the requested assured static - return null
        // so that we can bubble up to an ancestor context that does define it
        return null;
    }

    /**
     * Determines whether the specified assured static is defined
     *
     * @param AssuredStaticExistsQuery $query
     * @return bool|null
     */
    public function queryForAssuredStaticExistence(AssuredStaticExistsQuery $query)
    {
        foreach ($this->assuranceNodes as $assuranceNode) {
            if ($assuranceNode->getAssuredStaticName() === $query->getAssuredStaticName()) {
                // We've discovered that this guard expression _does_ define the requested assured static
                return true;
            }
        }

        // This guard expression doesn't define the requested assured static - return null
        // so that we can bubble up to an ancestor context that does define it
        return null;
    }

    /**
     * Fetches the type of the specified assured static
     *
     * @param AssuredStaticTypeQuery $query
     * @param ValidationContextInterface $validationContext
     * @return TypeInterface|null
     */
    public function queryForAssuredStaticType(
        AssuredStaticTypeQuery $query,
        ValidationContextInterface $validationContext
    ) {
        foreach ($this->assuranceNodes as $assuranceNode) {
            if ($assuranceNode->getAssuredStaticName() === $query->getAssuredStaticName()) {
                // We've discovered that this guard expression _does_ define the requested assured static,
                // so determine and return its type
                return $assuranceNode->getAssuredStaticTypeDeterminer()->determine($validationContext);
            }
        }

        // This guard expression doesn't define the requested assured static - return null
        // so that we can bubble up to an ancestor context that does define it
        return null;
    }
}
