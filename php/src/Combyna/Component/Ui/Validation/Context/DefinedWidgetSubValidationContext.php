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
use Combyna\Component\Ui\Config\Act\DefinedWidgetNode;
use Combyna\Component\Ui\Validation\Query\CaptureDefinitionNodeQuery;
use Combyna\Component\Ui\Validation\Query\CaptureHasOptionalAncestorWidgetQuery;
use Combyna\Component\Ui\Validation\Query\CaptureIsDefinedQuery;
use Combyna\Component\Ui\Validation\Query\CaptureTypeQuery;
use Combyna\Component\Ui\Validation\Query\CorrectCaptureTypeQuery;
use Combyna\Component\Ui\Validation\Query\InsideDefinedWidgetQuery;
use Combyna\Component\Ui\Validation\Query\WidgetDefinitionHasValueQuery;
use Combyna\Component\Ui\Validation\Query\WidgetDefinitionValueTypeQuery;
use Combyna\Component\Ui\Validation\Query\WidgetHasValueQuery;
use Combyna\Component\Ui\Validation\Query\WidgetValueTypeQuery;
use Combyna\Component\Validator\Context\SubValidationContextInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class DefinedWidgetSubValidationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DefinedWidgetSubValidationContext implements DefinedWidgetSubValidationContextInterface
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
     * @var DefinedWidgetNode
     */
    private $widgetNode;

    /**
     * @var BehaviourSpecInterface
     */
    private $widgetNodeBehaviourSpec;

    /**
     * @param SubValidationContextInterface $parentContext
     * @param DefinedWidgetNode $widgetNode
     * @param BehaviourSpecInterface $widgetNodeBehaviourSpec
     * @param ActNodeInterface $subjectNode
     */
    public function __construct(
        SubValidationContextInterface $parentContext,
        DefinedWidgetNode $widgetNode,
        BehaviourSpecInterface $widgetNodeBehaviourSpec,
        ActNodeInterface $subjectNode
    ) {
        $this->parentContext = $parentContext;
        $this->subjectNode = $subjectNode;
        $this->widgetNode = $widgetNode;
        $this->widgetNodeBehaviourSpec = $widgetNodeBehaviourSpec;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentActNode()
    {
        return $this->widgetNode;
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
        return $this->parentContext->getPath();
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
            ],
            InsideDefinedWidgetQuery::class => [
                $this,
                'queryForInsideDefinedWidget'
            ],
            WidgetHasValueQuery::class => [
                $this,
                'queryForWidgetValueExistence'
            ],
            WidgetValueTypeQuery::class => [$this, 'queryForWidgetValueType']
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
     * Fetches a FixedStaticDefinitionNode that defines a capture for the widget
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
        if (!$this->widgetNode->getCaptureStaticBagModel()->definesStatic($query->getCaptureName())) {
            // The widget does not define the capture - nothing to do
            return null;
        }

        return $this->widgetNode
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
        return $this->widgetNode->getCaptureStaticBagModel()->definesStatic($query->getCaptureName()) ?: null;
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
        $captureStaticBagModel = $this->widgetNode->getCaptureStaticBagModel();

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
        $captureStaticBagModel = $this->widgetNode->getCaptureStaticBagModel();

        if ($captureStaticBagModel->definesStatic($query->getCaptureName())) {
            // This widget defines the capture - we can determine the correct type
            return $query->determineCorrectType($validationContext);
        }

        // Otherwise, this widget does not define the capture, so we must continue up the tree
        return null;
    }

    /**
     * Determines whether we are inside a defined widget
     *
     * @return bool
     */
    public function queryForInsideDefinedWidget()
    {
        return true;
    }

    /**
     * Determines whether the current widget is optional or defines the specified capture
     *
     * @param CaptureHasOptionalAncestorWidgetQuery $query
     * @return bool|null
     */
    public function queryForWhetherCaptureHasOptionalAncestor(CaptureHasOptionalAncestorWidgetQuery $query)
    {
        /*
         * If this widget defines the capture, return false as if this node was reached, no optional widget
         * was found between its setter widget and this one.
         * Otherwise, return null so that the query will continue on up the tree
         * until either an optional widget is found or the widget that defines the capture is found.
         */
        return $this->widgetNode->getCaptureStaticBagModel()->definesStatic($query->getCaptureName()) ?
            false :
            null;
    }

    /**
     * Determines whether the current widget's definition defines the specified value
     *
     * @param WidgetHasValueQuery $query
     * @param ValidationContextInterface $validationContext
     * @return bool
     */
    public function queryForWidgetValueExistence(
        WidgetHasValueQuery $query,
        ValidationContextInterface $validationContext
    ) {
        return $validationContext->queryForBoolean(
            // Go back up to either the app or environment to get details on the widget definition
            new WidgetDefinitionHasValueQuery(
                $this->widgetNode->getLibraryName(),
                $this->widgetNode->getWidgetDefinitionName(),
                $query->getValueName()
            ),
            $validationContext->getCurrentActNode()
        );
    }

    /**
     * Fetches the type of a widget value
     *
     * @param WidgetValueTypeQuery $query
     * @param ValidationContextInterface $validationContext
     * @return TypeInterface
     */
    public function queryForWidgetValueType(
        WidgetValueTypeQuery $query,
        ValidationContextInterface $validationContext
    ) {
        return $validationContext->queryForResultType(
            // Go back up to either the app or environment to get details on the widget definition
            new WidgetDefinitionValueTypeQuery(
                $this->widgetNode->getLibraryName(),
                $this->widgetNode->getWidgetDefinitionName(),
                $query->getValueName()
            ),
            $validationContext->getCurrentActNode()
        );
    }
}
