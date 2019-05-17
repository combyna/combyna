<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Validation\Context;

use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Store\Config\Act\QueryNodeInterface;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Type\UnresolvedType;
use Combyna\Component\Ui\Config\Act\ViewNodeInterface;
use Combyna\Component\Ui\Store\Validation\Query\QueryNodeQuery;
use Combyna\Component\Ui\Store\Validation\Query\ViewStoreQueryResultTypeQuery;
use Combyna\Component\Ui\Validation\Query\InsideViewQuery;
use Combyna\Component\Validator\Context\SubValidationContextInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class ViewSubValidationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ViewSubValidationContext implements ViewSubValidationContextInterface
{
    /**
     * @var SubValidationContextInterface
     */
    private $parentContext;

    /**
     * @var ActNodeInterface
     */
    private $subjectNode;

    /**
     * @var ViewNodeInterface
     */
    private $viewNode;

    /**
     * @var BehaviourSpecInterface
     */
    private $viewNodeBehaviourSpec;

    /**
     * @param SubValidationContextInterface $parentContext
     * @param ViewNodeInterface $viewNode
     * @param BehaviourSpecInterface $viewNodeBehaviourSpec
     * @param ActNodeInterface $subjectNode
     */
    public function __construct(
        SubValidationContextInterface $parentContext,
        ViewNodeInterface $viewNode,
        BehaviourSpecInterface $viewNodeBehaviourSpec,
        ActNodeInterface $subjectNode
    ) {
        $this->parentContext = $parentContext;
        $this->subjectNode = $subjectNode;
        $this->viewNode = $viewNode;
        $this->viewNodeBehaviourSpec = $viewNodeBehaviourSpec;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentActNode()
    {
        return $this->viewNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getBehaviourSpec()
    {
        return $this->viewNodeBehaviourSpec;
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

        $path .= '[view:' . $this->viewNode->getName() . ']';

        return $path;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryClassToQueryCallableMap()
    {
        return [
            InsideViewQuery::class => [$this, 'queryForInsideView'],
            QueryNodeQuery::class => [$this, 'queryForQueryNode'],
            ViewStoreQueryResultTypeQuery::class => [$this, 'queryForViewStoreQueryResultType']
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
     * Determines whether we are inside a view
     *
     * @return bool
     */
    public function queryForInsideView()
    {
        return true;
    }

    /**
     * Fetches the node for the specified query of the current view's store
     *
     * @param QueryNodeQuery $query
     * @param ValidationContextInterface $validationContext
     * @return QueryNodeInterface
     */
    public function queryForQueryNode(
        QueryNodeQuery $query,
        ValidationContextInterface $validationContext
    ) {
        return $this->viewNode->getStore()->getQueryByName(
            $query->getQueryName(),
            $validationContext->createActNodeQueryRequirement($query, $validationContext->getCurrentActNode())
        );
    }

    /**
     * Fetches the result type of a view store query
     *
     * @param ViewStoreQueryResultTypeQuery $query
     * @param ValidationContextInterface $validationContext
     * @return TypeInterface
     */
    public function queryForViewStoreQueryResultType(
        ViewStoreQueryResultTypeQuery $query,
        ValidationContextInterface $validationContext
    ) {
        $queryRequirement = $validationContext->createTypeQueryRequirement($query);

        $storeQuery = $this->viewNode->getStore()->getQueryByName(
            $query->getQueryName(),
            $queryRequirement
        );

        if ($storeQuery === null) {
            return new UnresolvedType('view store query result type');
        }

        return $validationContext->getExpressionResultType($storeQuery->getExpression());
    }
}
