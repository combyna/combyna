<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Bag\Validation\Context;

use Combyna\Component\Bag\Validation\Query\InsideExpressionBagQuery;
use Combyna\Component\Bag\Validation\Query\SiblingBagExpressionExistsQuery;
use Combyna\Component\Bag\Validation\Query\SiblingBagExpressionNodeQuery;
use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Expression\Config\Act\UnknownExpressionNode;
use Combyna\Component\Validator\Context\SubValidationContextInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class ExpressionBagSubValidationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ExpressionBagSubValidationContext implements ExpressionBagSubValidationContextInterface
{
    /**
     * @var ActNodeInterface
     */
    private $containerNode;

    /**
     * @var BehaviourSpecInterface
     */
    private $containerNodeBehaviourSpec;

    /**
     * @var ExpressionNodeInterface[]
     */
    private $expressionNodes;

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
     * @param ActNodeInterface $containerNode
     * @param BehaviourSpecInterface $containerNodeBehaviourSpec
     * @param ActNodeInterface $subjectNode
     * @param ExpressionNodeInterface[] $expressionNodes
     */
    public function __construct(
        SubValidationContextInterface $parentContext,
        ActNodeInterface $containerNode,
        BehaviourSpecInterface $containerNodeBehaviourSpec,
        ActNodeInterface $subjectNode,
        array $expressionNodes
    ) {
        $this->containerNode = $containerNode;
        $this->containerNodeBehaviourSpec = $containerNodeBehaviourSpec;
        $this->expressionNodes = $expressionNodes;
        $this->parentContext = $parentContext;
        $this->subjectNode = $subjectNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getBehaviourSpec()
    {
        return $this->containerNodeBehaviourSpec;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentActNode()
    {
        return $this->containerNode;
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
        $path = $this->parentContext->getPath();

        if ($path !== '') {
            $path .= '.';
        }

        $path .= '[' . $this->containerNode->getIdentifier() . ']';

        return $path;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryClassToQueryCallableMap()
    {
        return [
            InsideExpressionBagQuery::class => [$this, 'queryForInsideExpressionBag'],
            SiblingBagExpressionExistsQuery::class => [$this, 'queryForSiblingBagExpressionExistence'],
            SiblingBagExpressionNodeQuery::class => [$this, 'queryForSiblingBagExpressionNode']
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
     * Determines whether we are inside an expression bag
     *
     * @return bool
     */
    public function queryForInsideExpressionBag()
    {
        return true;
    }

    /**
     * Determines whether the expression bag has the specified expression
     *
     * @param SiblingBagExpressionExistsQuery $query
     * @return bool
     */
    public function queryForSiblingBagExpressionExistence(SiblingBagExpressionExistsQuery $query)
    {
        return array_key_exists($query->getExpressionName(), $this->expressionNodes);
    }

    /**
     * Fetches an ExpressionNode that specifies the value for an expression in the bag
     *
     * @param SiblingBagExpressionNodeQuery $query
     * @param ValidationContextInterface $validationContext
     * @param ActNodeInterface $nodeQueriedFrom
     * @return ExpressionNodeInterface|null
     */
    public function queryForSiblingBagExpressionNode(
        SiblingBagExpressionNodeQuery $query,
        ValidationContextInterface $validationContext,
        ActNodeInterface $nodeQueriedFrom
    ) {
        $queryRequirement = $validationContext->createActNodeQueryRequirement($query, $nodeQueriedFrom);

        if (!array_key_exists($query->getExpressionName(), $this->expressionNodes)) {
            // The bag does not contain the expression
            return new UnknownExpressionNode(
                sprintf(
                    'Expression bag does not define expression "%s"',
                    $query->getExpressionName()
                ),
                $queryRequirement
            );
        }

        return $this->expressionNodes[$query->getExpressionName()];
    }
}
