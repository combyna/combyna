<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store\Validation\Context;

use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Type\UnresolvedType;
use Combyna\Component\Ui\Store\Config\Act\ViewStoreNode;
use Combyna\Component\Ui\Store\Validation\Query\InsideViewStoreQuery;
use Combyna\Component\Ui\Store\Validation\Query\ViewStoreHasSlotQuery;
use Combyna\Component\Ui\Store\Validation\Query\ViewStoreSlotTypeQuery;
use Combyna\Component\Validator\Context\SubValidationContextInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class ViewStoreSubValidationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ViewStoreSubValidationContext implements ViewStoreSubValidationContextInterface
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
     * @var ViewStoreNode
     */
    private $viewStoreNode;

    /**
     * @var BehaviourSpecInterface
     */
    private $viewStoreNodeBehaviourSpec;

    /**
     * @param SubValidationContextInterface $parentContext
     * @param ViewStoreNode $viewStoreNode
     * @param BehaviourSpecInterface $viewStoreNodeBehaviourSpec
     * @param ActNodeInterface $subjectNode
     */
    public function __construct(
        SubValidationContextInterface $parentContext,
        ViewStoreNode $viewStoreNode,
        BehaviourSpecInterface $viewStoreNodeBehaviourSpec,
        ActNodeInterface $subjectNode
    ) {
        $this->parentContext = $parentContext;
        $this->subjectNode = $subjectNode;
        $this->viewStoreNode = $viewStoreNode;
        $this->viewStoreNodeBehaviourSpec = $viewStoreNodeBehaviourSpec;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentActNode()
    {
        return $this->viewStoreNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getBehaviourSpec()
    {
        return $this->viewStoreNodeBehaviourSpec;
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

        $path .= '[view store]';

        return $path;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryClassToQueryCallableMap()
    {
        return [
            InsideViewStoreQuery::class => [$this, 'queryForInsideViewStore'],
            ViewStoreHasSlotQuery::class => [$this, 'queryForViewStoreSlotExistence'],
            ViewStoreSlotTypeQuery::class => [$this, 'queryForViewStoreSlotType']
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
     * Determines whether or not we are inside a view store
     *
     * @return bool
     */
    public function queryForInsideViewStore()
    {
        return true;
    }

    /**
     * Determines whether the view store defines the specified slot
     *
     * @param ViewStoreHasSlotQuery $query
     * @return bool
     */
    public function queryForViewStoreSlotExistence(ViewStoreHasSlotQuery $query)
    {
        return $this->viewStoreNode->getSlotBagModel()->definesStatic($query->getSlotName());
    }

    /**
     * Fetches the static type of a view store slot
     *
     * @param ViewStoreSlotTypeQuery $query
     * @param ValidationContextInterface $validationContext
     * @return TypeInterface
     */
    public function queryForViewStoreSlotType(
        ViewStoreSlotTypeQuery $query,
        ValidationContextInterface $validationContext
    ) {
        $staticType = $this->viewStoreNode->getSlotStaticType(
            $query->getSlotName(),
            $validationContext->createTypeQueryRequirement($query)
        );

        if ($staticType === null) {
            return new UnresolvedType('view store slot static type');
        }

        return $staticType;
    }
}
