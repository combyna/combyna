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

use Combyna\Component\Bag\Config\Act\FixedStaticDefinitionNodeInterface;
use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Ui\Config\Act\ConditionalWidgetNode;
use Combyna\Component\Ui\Validation\Query\CaptureDefinitionNodeQuery;
use Combyna\Component\Ui\Validation\Query\CaptureHasOptionalAncestorWidgetQuery;
use Combyna\Component\Ui\Validation\Query\CaptureIsDefinedQuery;
use Combyna\Component\Ui\Validation\Query\CaptureTypeQuery;
use Combyna\Component\Ui\Validation\Query\CorrectCaptureTypeQuery;
use Combyna\Component\Validator\Context\SubValidationContextInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class ConditionalWidgetSubValidationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ConditionalWidgetSubValidationContext implements ConditionalWidgetSubValidationContextInterface
{
    /**
     * @var ConditionalWidgetNode
     */
    private $conditionalWidgetNode;

    /**
     * @var SubValidationContextInterface
     */
    private $parentContext;

    /**
     * @var ActNodeInterface
     */
    private $subjectNode;

    /**
     * @var BehaviourSpecInterface
     */
    private $widgetNodeBehaviourSpec;

    /**
     * @param SubValidationContextInterface $parentContext
     * @param ConditionalWidgetNode $conditionalWidgetNode
     * @param BehaviourSpecInterface $widgetNodeBehaviourSpec
     * @param ActNodeInterface $subjectNode
     */
    public function __construct(
        SubValidationContextInterface $parentContext,
        ConditionalWidgetNode $conditionalWidgetNode,
        BehaviourSpecInterface $widgetNodeBehaviourSpec,
        ActNodeInterface $subjectNode
    ) {
        $this->conditionalWidgetNode = $conditionalWidgetNode;
        $this->parentContext = $parentContext;
        $this->subjectNode = $subjectNode;
        $this->widgetNodeBehaviourSpec = $widgetNodeBehaviourSpec;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentActNode()
    {
        return $this->conditionalWidgetNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getBehaviourSpec()
    {
        return $this->widgetNodeBehaviourSpec;
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

        $path .= '[' . $this->conditionalWidgetNode->getIdentifier() . ']';

        return $path;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryClassToQueryCallableMap()
    {
        return [
            CaptureDefinitionNodeQuery::class => [
                $this,
                'queryForCaptureDefinitionNode'
            ],
            CaptureHasOptionalAncestorWidgetQuery::class => [
                $this,
                'queryForWhetherCaptureHasOptionalAncestor'
            ],
            CaptureIsDefinedQuery::class => [
                $this,
                'queryForCaptureExistence'
            ],
            CaptureTypeQuery::class => [
                $this,
                'queryForCaptureType'
            ],
            CorrectCaptureTypeQuery::class => [
                $this,
                'queryForCorrectCaptureType'
            ]
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
     * Fetches a FixedStaticDefinitionNode that defines a capture for the conditional widget
     *
     * @param CaptureDefinitionNodeQuery $query
     * @param ValidationContextInterface $validationContext
     * @param ActNodeInterface $nodeQueriedFrom
     * @return FixedStaticDefinitionNodeInterface|null
     */
    public function queryForCaptureDefinitionNode(
        CaptureDefinitionNodeQuery $query,
        ValidationContextInterface $validationContext,
        ActNodeInterface $nodeQueriedFrom
    ) {
        $queryRequirement = $validationContext->createActNodeQueryRequirement($query, $nodeQueriedFrom);

        if (!$this->conditionalWidgetNode->getCaptureStaticBagModel($queryRequirement)
            ->definesStatic($query->getCaptureName())
        ) {
            // The conditional widget does not define the capture - nothing to do
            return null;
        }

        return $this->conditionalWidgetNode
            ->getCaptureStaticBagModel($queryRequirement)
            ->getStaticDefinitionByName(
                $query->getCaptureName(),
                $queryRequirement
            );
    }

    /**
     * Determines whether the current widget's definition defines the specified capture
     *
     * @param CaptureIsDefinedQuery $query
     * @param ValidationContextInterface $validationContext
     * @return bool|null
     */
    public function queryForCaptureExistence(
        CaptureIsDefinedQuery $query,
        ValidationContextInterface $validationContext
    ) {
        return $this->conditionalWidgetNode
            ->getCaptureStaticBagModel(
                $validationContext->createBooleanQueryRequirement($query)
            )
            ->definesStatic($query->getCaptureName()) ?: null;
    }

    /**
     * Fetches the actual defined type of a capture
     *
     * @param CaptureTypeQuery $query
     * @param ValidationContextInterface $validationContext
     * @return TypeInterface|null
     */
    public function queryForCaptureType(
        CaptureTypeQuery $query,
        ValidationContextInterface $validationContext
    ) {
        $queryRequirement = $validationContext->createTypeQueryRequirement($query);

        $captureStaticBagModel = $this->conditionalWidgetNode->getCaptureStaticBagModel($queryRequirement);

        if (!$captureStaticBagModel->definesStatic($query->getCaptureName())) {
            return null;
        }

        return $captureStaticBagModel
            ->getStaticDefinitionByName(
                $query->getCaptureName(),
                $queryRequirement
            )
            ->getStaticTypeDeterminer()->determine($validationContext);
    }

    /**
     * Fetches the expected type of a capture
     *
     * @param CorrectCaptureTypeQuery $query
     * @param ValidationContextInterface $validationContext
     * @return TypeInterface|null
     */
    public function queryForCorrectCaptureType(
        CorrectCaptureTypeQuery $query,
        ValidationContextInterface $validationContext
    ) {
        $queryRequirement = $validationContext->createTypeQueryRequirement($query);
        $queryWithConditional = $query->withOptionalAncestor($this->conditionalWidgetNode);

        $captureStaticBagModel = $this->conditionalWidgetNode->getCaptureStaticBagModel($queryRequirement);

        if ($captureStaticBagModel->definesStatic($query->getCaptureName())) {
            // This widget defines the capture - we can determine the correct type
            return $queryWithConditional->determineCorrectType($validationContext);
        }

        // Otherwise, this widget does not define the capture, so we must continue up the tree
        return $validationContext->queryForResultType(
            $queryWithConditional,
            $this->parentContext->getCurrentActNode()
        );
    }

    /**
     * Determines whether the current widget is optional or defines the specified capture
     *
     * @return bool|null
     */
    public function queryForWhetherCaptureHasOptionalAncestor()
    {
        /*
         * Either the current widget defines the capture or one of its ancestors does -
         * as it is a conditional widget, this means the capture may sometimes not be set
         * (when the condition is true but the capture is set inside the alternate widget,
         * or the condition is false but the capture is set inside the consequent widget),
         * so it has an optional ancestor
         */
        return true;
    }
}
