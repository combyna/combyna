<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\App\Validation\Context;

use Combyna\Component\App\Config\Act\AppNode;
use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Environment\Library\LibraryInterface;
use Combyna\Component\Expression\Validation\Query\FunctionReturnTypeQuery;
use Combyna\Component\Signal\Validation\Query\SignalDefinitionExistsQuery;
use Combyna\Component\Signal\Validation\Query\SignalDefinitionHasPayloadStaticQuery;
use Combyna\Component\Signal\Validation\Query\SignalDefinitionPayloadStaticTypeQuery;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Ui\Config\Act\WidgetDefinitionNodeInterface;
use Combyna\Component\Ui\Validation\Query\PageViewExistsQuery;
use Combyna\Component\Ui\Validation\Query\WidgetDefinitionHasValueQuery;
use Combyna\Component\Ui\Validation\Query\WidgetDefinitionNodeQuery;
use Combyna\Component\Ui\Validation\Query\WidgetDefinitionValueTypeQuery;
use Combyna\Component\Validator\Context\SubValidationContextInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class AppSubValidationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AppSubValidationContext implements AppSubValidationContextInterface
{
    /**
     * @var AppNode
     */
    private $appNode;

    /**
     * @var BehaviourSpecInterface
     */
    private $appNodeBehaviourSpec;

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
     * @param AppNode $appNode
     * @param BehaviourSpecInterface $appNodeBehaviourSpec
     * @param ActNodeInterface $subjectNode
     */
    public function __construct(
        SubValidationContextInterface $parentContext,
        AppNode $appNode,
        BehaviourSpecInterface $appNodeBehaviourSpec,
        ActNodeInterface $subjectNode
    ) {
        $this->appNode = $appNode;
        $this->appNodeBehaviourSpec = $appNodeBehaviourSpec;
        $this->parentContext = $parentContext;
        $this->subjectNode = $subjectNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentActNode()
    {
        return $this->appNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getBehaviourSpec()
    {
        return $this->appNodeBehaviourSpec;
    }

    /**
     * {@inheritdoc}
     */
    public function getParentContext()
    {
        // If the app doesn't define a resource, check the environment next
        return $this->parentContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return '[app]';
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryClassToQueryCallableMap()
    {
        return [
            FunctionReturnTypeQuery::class => [$this, 'queryForFunctionReturnType'],
            PageViewExistsQuery::class => [$this, 'queryForPageViewExistence'],
            SignalDefinitionExistsQuery::class => [$this, 'queryForSignalDefinitionExistence'],
            SignalDefinitionHasPayloadStaticQuery::class => [$this, 'queryForSignalPayloadStaticExistence'],
            SignalDefinitionPayloadStaticTypeQuery::class => [$this, 'queryForSignalPayloadStaticType'],
            WidgetDefinitionHasValueQuery::class => [$this, 'queryForWidgetValueExistence'],
            WidgetDefinitionNodeQuery::class => [$this, 'queryForWidgetDefinitionNode'],
            WidgetDefinitionValueTypeQuery::class => [$this, 'queryForWidgetValueType']
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
     * Fetches the return type of the specified function
     *
     * @param FunctionReturnTypeQuery $query
     * @param ValidationContextInterface $validationContext
     * @return TypeInterface|null
     */
    public function queryForFunctionReturnType(
        FunctionReturnTypeQuery $query,
        ValidationContextInterface $validationContext
    ) {
        if ($query->getLibraryName() !== LibraryInterface::APP) {
            // App doesn't define the specified function - return null, so that we can bubble
            // up to the parent node (the Environment) to look for the function
            return null;
        }

        $functionNode = $this->appNode->getGenericFunction(
            $query->getLibraryName(),
            $query->getFunctionName(),
            $validationContext->createTypeQueryRequirement($query)
        );

        return $functionNode->getReturnTypeDeterminer()->determine($validationContext);
    }

    /**
     * Determines whether the specified page view exists
     *
     * @param PageViewExistsQuery $query
     * @return bool
     */
    public function queryForPageViewExistence(PageViewExistsQuery $query)
    {
        return array_key_exists($query->getPageViewName(), $this->appNode->getPageViews());
    }

    /**
     * Determines whether the specified signal definition exists
     *
     * @param SignalDefinitionExistsQuery $query
     * @return bool|null
     */
    public function queryForSignalDefinitionExistence(SignalDefinitionExistsQuery $query)
    {
        // Only handle signals defined by the special "app" library for the current app
        if ($query->getLibraryName() !== LibraryInterface::APP) {
            // App doesn't define the specified signal - return null, so that we can bubble
            // up to the parent node (the Environment) to look for the signal definition
            return null;
        }

        if (array_key_exists($query->getSignalName(), $this->appNode->getSignalDefinitions())) {
            // We've discovered that the app _does_ define the requested signal definition
            return true;
        }

        // The app doesn't define the requested signal definition - return null
        // so that we can bubble up to an ancestor context that does define it
        return null;
    }

    /**
     * Determines whether the specified signal defines the specified payload,
     * where the signal is defined by the app itself (vs. a library)
     *
     * @param SignalDefinitionHasPayloadStaticQuery $query
     * @param ValidationContextInterface $validationContext
     * @return bool
     */
    public function queryForSignalPayloadStaticExistence(
        SignalDefinitionHasPayloadStaticQuery $query,
        ValidationContextInterface $validationContext
    ) {
        // Only handle signals defined by the special "app" library for the current app
        if ($query->getSignalLibraryName() !== LibraryInterface::APP) {
            // App doesn't define the specified signal - return null, so that we can bubble
            // up to the parent node (the Environment) to look for the signal definition
            return null;
        }

        $queryRequirement = $validationContext->createBooleanQueryRequirement($query);

        $signalDefinitionNode = $this->appNode->getSignalDefinition(
            $query->getSignalName(),
            $queryRequirement
        );

        return $signalDefinitionNode
            ->getPayloadStaticBagModel($queryRequirement)
            ->definesStatic($query->getPayloadStaticName());
    }

    /**
     * Fetches the return type of the specified payload static of a signal
     * defined by the app itself (vs. a library)
     *
     * @param SignalDefinitionPayloadStaticTypeQuery $query
     * @param ValidationContextInterface $validationContext
     * @return TypeInterface|null
     */
    public function queryForSignalPayloadStaticType(
        SignalDefinitionPayloadStaticTypeQuery $query,
        ValidationContextInterface $validationContext
    ) {
        // Only handle signals defined by the special "app" library for the current app
        if ($query->getSignalLibraryName() !== LibraryInterface::APP) {
            // App doesn't define the specified signal - return null, so that we can bubble
            // up to the parent node (the Environment) to look for the signal definition
            return null;
        }

        $queryRequirement = $validationContext->createTypeQueryRequirement($query);

        $signalDefinitionNode = $this->appNode->getSignalDefinition(
            $query->getSignalName(),
            $queryRequirement
        );

        return $signalDefinitionNode->getPayloadStaticType($query->getPayloadStaticName(), $queryRequirement);
    }

    /**
     * Fetches a WidgetDefinitionNode defined by the app itself (vs. a library)
     *
     * @param WidgetDefinitionNodeQuery $query
     * @param ValidationContextInterface $validationContext
     * @param ActNodeInterface $nodeQueriedFrom
     * @return WidgetDefinitionNodeInterface|null
     */
    public function queryForWidgetDefinitionNode(
        WidgetDefinitionNodeQuery $query,
        ValidationContextInterface $validationContext,
        ActNodeInterface $nodeQueriedFrom
    ) {
        // Only handle widget definitions defined by the special "app" library for the current app
        if ($query->getLibraryName() !== LibraryInterface::APP) {
            // App doesn't define the specified widget definition - return null, so that we can bubble
            // up to the parent node (the Environment) to look for the widget definition
            return null;
        }

        return $this->appNode->getWidgetDefinition(
            $query->getWidgetDefinitionName(),
            $validationContext->createActNodeQueryRequirement($query, $nodeQueriedFrom)
        );
    }

    /**
     * Fetches the return type of the specified value of a widget defined by a library
     *
     * @param WidgetDefinitionValueTypeQuery $query
     * @param ValidationContextInterface $validationContext
     * @return TypeInterface
     */
    public function queryForWidgetValueType(
        WidgetDefinitionValueTypeQuery $query,
        ValidationContextInterface $validationContext
    ) {
        // Only handle widget definitions defined by the special "app" library for the current app
        if ($query->getLibraryName() !== LibraryInterface::APP) {
            // App doesn't define the specified widget definition - return null, so that we can bubble
            // up to the parent node (the Environment) to look for the widget definition
            return null;
        }

        $queryRequirement = $validationContext->createTypeQueryRequirement($query);

        $widgetDefinitionNode = $this->appNode->getWidgetDefinition(
            $query->getWidgetDefinitionName(),
            $queryRequirement
        );

        return $widgetDefinitionNode->getValueType($query->getValueName(), $queryRequirement);
    }

    /**
     * Determines whether the specified widget definition defines the specified value
     *
     * @param WidgetDefinitionHasValueQuery $query
     * @param ValidationContextInterface $validationContext
     * @return bool
     */
    public function queryForWidgetValueExistence(
        WidgetDefinitionHasValueQuery $query,
        ValidationContextInterface $validationContext
    ) {
        // Only handle widget definitions defined by the special "app" library for the current app
        if ($query->getLibraryName() !== LibraryInterface::APP) {
            // App doesn't define the specified widget definition - return null, so that we can bubble
            // up to the parent node (the Environment) to look for the widget definition
            return null;
        }

        $widgetDefinitionNode = $this->appNode->getWidgetDefinition(
            $query->getWidgetDefinitionName(),
            $validationContext->createBooleanQueryRequirement($query)
        );

        return $widgetDefinitionNode->definesValue($query->getValueName());
    }
}
