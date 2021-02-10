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
use Combyna\Component\Ui\Config\Act\RepeaterWidgetNode;
use Combyna\Component\Ui\Validation\Query\CaptureDefinitionNodeQuery;
use Combyna\Component\Ui\Validation\Query\CaptureHasOptionalAncestorWidgetQuery;
use Combyna\Component\Ui\Validation\Query\CaptureIsDefinedQuery;
use Combyna\Component\Ui\Validation\Query\CaptureTypeQuery;
use Combyna\Component\Ui\Validation\Query\CorrectCaptureTypeQuery;
use Combyna\Component\Validator\Context\SubValidationContextInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class RepeaterWidgetSubValidationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RepeaterWidgetSubValidationContext implements RepeaterWidgetSubValidationContextInterface
{
    /**
     * @var SubValidationContextInterface
     */
    private $parentContext;

    /**
     * @var RepeaterWidgetNode
     */
    private $repeaterWidgetNode;

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
     * @param RepeaterWidgetNode $repeaterWidgetNode
     * @param BehaviourSpecInterface $widgetNodeBehaviourSpec
     * @param ActNodeInterface $subjectNode
     */
    public function __construct(
        SubValidationContextInterface $parentContext,
        RepeaterWidgetNode $repeaterWidgetNode,
        BehaviourSpecInterface $widgetNodeBehaviourSpec,
        ActNodeInterface $subjectNode
    ) {
        $this->parentContext = $parentContext;
        $this->repeaterWidgetNode = $repeaterWidgetNode;
        $this->subjectNode = $subjectNode;
        $this->widgetNodeBehaviourSpec = $widgetNodeBehaviourSpec;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentActNode()
    {
        return $this->repeaterWidgetNode;
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

        $path .= '[' . $this->repeaterWidgetNode->getIdentifier() . ']';

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
     * Fetches a FixedStaticDefinitionNode that defines a capture for the repeater
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
        if (!$this->repeaterWidgetNode->getCaptureStaticBagModel()->definesStatic($query->getCaptureName())) {
            // The repeater does not define the capture - nothing to do
            return null;
        }

        return $this->repeaterWidgetNode
            ->getCaptureStaticBagModel()
            ->getStaticDefinitionByName(
                $query->getCaptureName(),
                $validationContext->createActNodeQueryRequirement($query, $nodeQueriedFrom)
            );
    }

    /**
     * Determines whether the current widget's definition defines the specified capture
     *
     * @param CaptureIsDefinedQuery $query
     * @return bool|null
     */
    public function queryForCaptureExistence(CaptureIsDefinedQuery $query)
    {
        return $this->repeaterWidgetNode->getCaptureStaticBagModel()->definesStatic($query->getCaptureName()) ?: null;
    }

    /**
     * Fetches the type of a capture
     *
     * @param CaptureTypeQuery $query
     * @param ValidationContextInterface $validationContext
     * @return TypeInterface|null
     */
    public function queryForCaptureType(
        CaptureTypeQuery $query,
        ValidationContextInterface $validationContext
    ) {
        $captureStaticBagModel = $this->repeaterWidgetNode->getCaptureStaticBagModel();

        if (!$captureStaticBagModel->definesStatic($query->getCaptureName())) {
            return null;
        }

        return $captureStaticBagModel
            ->getStaticDefinitionByName(
                $query->getCaptureName(),
                $validationContext->createTypeQueryRequirement($query)
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
        $queryWithRepeater = $query->withOptionalAncestor($this->repeaterWidgetNode);

        $captureStaticBagModel = $this->repeaterWidgetNode->getCaptureStaticBagModel();

        if ($captureStaticBagModel->definesStatic($query->getCaptureName())) {
            // This widget defines the capture - we can determine the correct type
            return $queryWithRepeater->determineCorrectType($validationContext);
        }

        // Otherwise, this widget does not define the capture, so we must continue up the tree
        return $validationContext->queryForResultType(
            $queryWithRepeater,
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
        // Either the current widget defines the capture or one of its ancestors does -
        // as it is a repeater widget, this means the capture may sometimes not be set (when the list is empty),
        // so it has an optional ancestor
        return true;
    }
}
