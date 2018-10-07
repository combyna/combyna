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
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Ui\Config\Act\CompoundWidgetDefinitionNode;
use Combyna\Component\Ui\Validation\Query\CurrentCompoundWidgetDefinitionHasChildStaticQuery;
use Combyna\Component\Ui\Validation\Query\CurrentWidgetDefinitionHasAttributeQuery;
use Combyna\Component\Ui\Validation\Query\InsideWidgetDefinitionQuery;
use Combyna\Component\Ui\Validation\Query\WidgetAttributeTypeQuery;
use Combyna\Component\Validator\Context\SubValidationContextInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class CompoundWidgetDefinitionSubValidationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CompoundWidgetDefinitionSubValidationContext implements CompoundWidgetDefinitionSubValidationContextInterface
{
    /**
     * @var CompoundWidgetDefinitionNode
     */
    private $definitionNode;

    /**
     * @var BehaviourSpecInterface
     */
    private $definitionNodeBehaviourSpec;

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
     * @param CompoundWidgetDefinitionNode $definitionNode
     * @param BehaviourSpecInterface $definitionNodeBehaviourSpec
     * @param ActNodeInterface $subjectNode
     */
    public function __construct(
        SubValidationContextInterface $parentContext,
        CompoundWidgetDefinitionNode $definitionNode,
        BehaviourSpecInterface $definitionNodeBehaviourSpec,
        ActNodeInterface $subjectNode
    ) {
        $this->definitionNode = $definitionNode;
        $this->definitionNodeBehaviourSpec = $definitionNodeBehaviourSpec;
        $this->parentContext = $parentContext;
        $this->subjectNode = $subjectNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentActNode()
    {
        return $this->definitionNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getBehaviourSpec()
    {
        return $this->definitionNodeBehaviourSpec;
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
            CurrentWidgetDefinitionHasAttributeQuery::class => [
                $this,
                'queryForWidgetDefinitionAttributeExistence'
            ],
            CurrentCompoundWidgetDefinitionHasChildStaticQuery::class => [
                $this,
                'queryForCompoundWidgetDefinitionChildExistence'
            ],
            InsideWidgetDefinitionQuery::class => [
                $this,
                'queryForInsideWidgetDefinition'
            ],
            WidgetAttributeTypeQuery::class => [$this, 'queryForWidgetAttributeType']
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
     * Determines whether the current compound widget definition defines the specified attribute
     *
     * @param CurrentWidgetDefinitionHasAttributeQuery $query
     * @return bool
     */
    public function queryForWidgetDefinitionAttributeExistence(
        CurrentWidgetDefinitionHasAttributeQuery $query
    ) {
        return $this->definitionNode->getAttributeBagModel()->definesStatic($query->getAttributeName());
    }

    /**
     * Determines whether the current compound widget definition defines the specified child
     *
     * @param CurrentCompoundWidgetDefinitionHasChildStaticQuery $query
     * @param ValidationContextInterface $validationContext
     * @return bool
     */
    public function queryForCompoundWidgetDefinitionChildExistence(
        CurrentCompoundWidgetDefinitionHasChildStaticQuery $query,
        ValidationContextInterface $validationContext
    ) {
        return $this->definitionNode->getChildDefinition(
            $query->getChildName(),
            $validationContext->createBooleanQueryRequirement($query)
        )->isDefined();
    }

    /**
     * Determines whether we are inside a compound widget definition
     *
     * @return bool
     */
    public function queryForInsideWidgetDefinition()
    {
        return true;
    }

    /**
     * Fetches the type of a widget attribute
     *
     * @param WidgetAttributeTypeQuery $query
     * @param ValidationContextInterface $validationContext
     * @return TypeInterface
     */
    public function queryForWidgetAttributeType(
        WidgetAttributeTypeQuery $query,
        ValidationContextInterface $validationContext
    ) {
        $attributeStaticDefinitionNode = $this->definitionNode->getAttributeBagModel()
            ->getStaticDefinitionByName(
                $query->getAttributeName(),
                $validationContext->createTypeQueryRequirement($query)
            );

        return $attributeStaticDefinitionNode->getStaticTypeDeterminer()->determine($validationContext);
    }
}
