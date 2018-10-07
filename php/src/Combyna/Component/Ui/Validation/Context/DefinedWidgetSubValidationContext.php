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
use Combyna\Component\Ui\Config\Act\DefinedWidgetNode;
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
     * Determines whether we are inside a defined widget
     *
     * @return bool
     */
    public function queryForInsideDefinedWidget()
    {
        return true;
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
