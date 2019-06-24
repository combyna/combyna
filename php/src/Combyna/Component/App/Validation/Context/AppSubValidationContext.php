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
use Combyna\Component\Router\Config\Act\RouteNodeInterface;
use Combyna\Component\Router\Validation\Query\RouteExistsQuery;
use Combyna\Component\Router\Validation\Query\RouteNodeQuery;
use Combyna\Component\Router\Validation\Query\RouteParameterTypeQuery;
use Combyna\Component\Router\Validation\Query\RoutesForViewDefineIdenticalParameterQuery;
use Combyna\Component\Signal\Validation\Query\SignalDefinitionExistsQuery;
use Combyna\Component\Signal\Validation\Query\SignalDefinitionHasPayloadStaticQuery;
use Combyna\Component\Signal\Validation\Query\SignalDefinitionPayloadStaticTypeQuery;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Type\UnresolvedType;
use Combyna\Component\Ui\Config\Act\WidgetDefinitionNodeInterface;
use Combyna\Component\Ui\Validation\Query\PageViewExistsQuery;
use Combyna\Component\Ui\Validation\Query\WidgetDefinitionExistsQuery;
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
            RouteExistsQuery::class => [$this, 'queryForRouteExistence'],
            RouteNodeQuery::class => [$this, 'queryForRouteNode'],
            RouteParameterTypeQuery::class => [$this, 'queryForRouteParameterType'],
            RoutesForViewDefineIdenticalParameterQuery::class => [$this, 'queryForIdenticalRouteParameterExistence'],
            SignalDefinitionExistsQuery::class => [$this, 'queryForSignalDefinitionExistence'],
            SignalDefinitionHasPayloadStaticQuery::class => [$this, 'queryForSignalPayloadStaticExistence'],
            SignalDefinitionPayloadStaticTypeQuery::class => [$this, 'queryForSignalPayloadStaticType'],
            WidgetDefinitionExistsQuery::class => [$this, 'queryForWidgetDefinitionExistence'],
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
     * Determines whether all routes that use the specified view define a parameter
     * with the specified name and with identical types
     *
     * @param RoutesForViewDefineIdenticalParameterQuery $query
     * @param ValidationContextInterface $validationContext
     * @return bool
     */
    public function queryForIdenticalRouteParameterExistence(
        RoutesForViewDefineIdenticalParameterQuery $query,
        ValidationContextInterface $validationContext
    ) {
        $queryRequirement = $validationContext->createBooleanQueryRequirement($query);

        /** @var TypeInterface|null $firstRouteParameterType */
        $firstRouteParameterType = null;

        foreach ($this->appNode->getRoutes() as $routeNode) {
            if ($routeNode->getPageViewName() !== $query->getViewName()) {
                continue; // This route does not reference the specified view
            }

            if (!$routeNode->getParameterBagModel()->definesStatic($query->getParameterName())) {
                return false; // This route doesn't define the parameter at all
            }

            $routeParameterType = $routeNode->getParameterBagModel()
                ->getStaticDefinitionByName($query->getParameterName(), $queryRequirement)
                ->getStaticTypeDeterminer()
                ->determine($validationContext);

            if ($firstRouteParameterType === null) {
                $firstRouteParameterType = $routeParameterType;
            } else {
                // TODO: Add TypeInterface::equals(...) rather than doing this
                if (!$routeParameterType->allows($firstRouteParameterType) ||
                    !$firstRouteParameterType->allows($routeParameterType)
                ) {
                    return false; // Types differ
                }
            }
        }

        // Ensure the parameter is defined at least once
        return $firstRouteParameterType !== null;
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
     * Determines whether the specified route exists
     *
     * @param RouteExistsQuery $query
     * @return bool|null
     */
    public function queryForRouteExistence(RouteExistsQuery $query)
    {
        // Only handle routes defined by the special "app" library for the current app
        if ($query->getLibraryName() !== LibraryInterface::APP) {
            // App doesn't define the specified route - return null, so that we can bubble
            // up to the parent node (the Environment) to look for the route
            return null;
        }

        if (array_key_exists($query->getRouteName(), $this->appNode->getRoutes())) {
            // We've discovered that the app _does_ define the requested route
            return true;
        }

        // The app doesn't define the requested route - return null
        // so that we can bubble up to an ancestor context that does define it
        return null;
    }

    /**
     * Fetches the ACT node for the specified route
     *
     * @param RouteNodeQuery $query
     * @return RouteNodeInterface|null
     */
    public function queryForRouteNode(RouteNodeQuery $query)
    {
        // Only handle routes defined by the special "app" library for the current app
        if ($query->getLibraryName() !== LibraryInterface::APP) {
            // App doesn't define the specified route - return null, so that we can bubble
            // up to the parent node (the Environment) to look for the route
            return null;
        }

        if (array_key_exists($query->getRouteName(), $this->appNode->getRoutes())) {
            // We've discovered that the app _does_ define the requested route
            return $this->appNode->getRoutes()[$query->getRouteName()];
        }

        // The app doesn't define the requested route - return null
        // so that we can bubble up to an ancestor context that does define it
        return null;
    }

    /**
     * Fetches the type of a parameter for the routes that use the specified view
     *
     * @param RouteParameterTypeQuery $query
     * @param ValidationContextInterface $validationContext
     * @return TypeInterface
     */
    public function queryForRouteParameterType(
        RouteParameterTypeQuery $query,
        ValidationContextInterface $validationContext
    ) {
        $queryRequirement = $validationContext->createTypeQueryRequirement($query);

        foreach ($this->appNode->getRoutes() as $routeNode) {
            if ($routeNode->getPageViewName() !== $query->getViewName()) {
                continue; // This route does not reference the specified view
            }

            if (!$routeNode->getParameterBagModel()->definesStatic($query->getParameterName())) {
                // This route doesn't define the parameter at all - this is an invalid state
                // for the app to be in (as all routes that use a view must define the same set of parameters)
                // but that is handled by validation constraints, so for the purposes of resolving a type
                // we just continue until/if we find a route using the view that _does_ define the parameter
                continue;
            }

            $routeParameterType = $routeNode->getParameterBagModel()
                ->getStaticDefinitionByName($query->getParameterName(), $queryRequirement)
                ->getStaticTypeDeterminer()
                ->determine($validationContext);

            return $routeParameterType;
        }

        // Ensure the parameter is defined at least once
        return new UnresolvedType(
            sprintf(
                'No route for view "%s" defines the parameter "%s"',
                $query->getViewName(),
                $query->getParameterName()
            ),
            $validationContext
        );
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
            ->getPayloadStaticBagModel()
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

        return $signalDefinitionNode->getPayloadStaticType($query->getPayloadStaticName());
    }

    /**
     * Determines whether the specified widget definition exists
     *
     * @param WidgetDefinitionExistsQuery $query
     * @return bool|null
     */
    public function queryForWidgetDefinitionExistence(WidgetDefinitionExistsQuery $query)
    {
        // Only handle widget definitions defined by the special "app" library for the current app
        if ($query->getLibraryName() !== LibraryInterface::APP) {
            // App doesn't define the specified widget definition - return null, so that we can bubble
            // up to the parent node (the Environment) to look for the widget definition
            return null;
        }

        if (array_key_exists($query->getWidgetDefinitionName(), $this->appNode->getWidgetDefinitions())) {
            // We've discovered that the app _does_ define the requested widget definition
            return true;
        }

        // The app doesn't define the requested widget definition - return null
        // so that we can bubble up to an ancestor context that does define it
        return null;
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

        return $widgetDefinitionNode->getValueType($query->getValueName());
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
