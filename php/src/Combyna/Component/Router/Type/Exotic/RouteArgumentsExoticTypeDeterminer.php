<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Router\Type\Exotic;

use Combyna\Component\Bag\BagFactoryInterface;
use Combyna\Component\Bag\Validation\Query\InsideExpressionBagQuery;
use Combyna\Component\Bag\Validation\Query\SiblingBagExpressionNodeQuery;
use Combyna\Component\Common\Exception\NotFoundException;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Expression\Config\Act\UnknownExpressionNode;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\StaticExpressionFactoryInterface;
use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Expression\StaticStructureExpression;
use Combyna\Component\Expression\TextExpression;
use Combyna\Component\Router\Config\Act\RouteNodeInterface;
use Combyna\Component\Router\Validation\Query\RouteExistsQuery;
use Combyna\Component\Router\Validation\Query\RouteNodeQuery;
use Combyna\Component\Type\Exception\IncompatibleNativeForCoercionException;
use Combyna\Component\Type\Exception\IncompatibleStaticForCoercionException;
use Combyna\Component\Type\Exotic\Determination\RestrictiveTypeDetermination;
use Combyna\Component\Type\Exotic\Determination\UnrestrictiveTypeDetermination;
use Combyna\Component\Type\Exotic\ExoticTypeDeterminerInterface;
use Combyna\Component\Type\ExoticType;
use Combyna\Component\Type\StaticStructureType;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Type\UnresolvedType;
use Combyna\Component\Type\ValuedType;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use stdClass;

/**
 * Class RouteArgumentsExoticTypeDeterminer
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RouteArgumentsExoticTypeDeterminer implements ExoticTypeDeterminerInterface
{
    const NAME = 'route_arguments';

    /**
     * @var ValidationContextInterface
     */
    private $destinationValidationContext;

    /**
     * @var string
     */
    private $routeNameStaticName;

    /**
     * @param string $routeNameStaticName
     * @param ValidationContextInterface $destinationValidationContext
     */
    public function __construct($routeNameStaticName, ValidationContextInterface $destinationValidationContext)
    {
        $this->destinationValidationContext = $destinationValidationContext;
        $this->routeNameStaticName = $routeNameStaticName;
    }

    /**
     * {@inheritdoc}
     */
    public function coerceNative(
        $nativeValue,
        StaticExpressionFactoryInterface $staticExpressionFactory,
        BagFactoryInterface $bagFactory,
        EvaluationContextInterface $evaluationContext
    ) {
        if (!is_array($nativeValue) && (!is_object($nativeValue) || !$nativeValue instanceof stdClass)) {
            throw new IncompatibleNativeForCoercionException(sprintf(
                'Route arguments exotic type expects an array or stdClass instance, %s given',
                gettype($nativeValue)
            ));
        }

        try {
            $fqRouteName = $evaluationContext->getSiblingBagStatic($this->routeNameStaticName)->toNative();
        } catch (NotFoundException $exception) {
            throw new IncompatibleNativeForCoercionException(sprintf(
                'Current bag does not define static "%s" for route name',
                $this->routeNameStaticName
            ));
        }

        $parts = explode('.', $fqRouteName, 2);

        if (count($parts) < 2) {
            throw new IncompatibleNativeForCoercionException(sprintf(
                'Route name "%s" is not fully qualified (missing library name)',
                $fqRouteName
            ));
        }

        list($libraryName, $routeName) = $parts;

        try {
            $route = $evaluationContext->getRoute($libraryName, $routeName);
        } catch (NotFoundException $exception) {
            throw new IncompatibleNativeForCoercionException(sprintf(
                'Route "%s" for library "%s" does not exist',
                $routeName,
                $libraryName
            ));
        }

        return $staticExpressionFactory->createStaticStructureExpression(
            $route->getParameterBagModel()->coerceNativeArrayToBag(
                (array)$nativeValue,
                $evaluationContext
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function coerceStatic(
        StaticInterface $static,
        EvaluationContextInterface $evaluationContext
    ) {
        if (!$static instanceof StaticStructureExpression) {
            throw new IncompatibleStaticForCoercionException(sprintf(
                'Route arguments exotic type expects a %s expression, %s given',
                StaticStructureExpression::TYPE,
                $static->getType()
            ));
        }

        try {
            $fqRouteName = $evaluationContext->getSiblingBagStatic($this->routeNameStaticName)->toNative();
        } catch (NotFoundException $exception) {
            throw new IncompatibleStaticForCoercionException(sprintf(
                'Current bag does not define static "%s" for route name',
                $this->routeNameStaticName
            ));
        }

        $parts = explode('.', $fqRouteName, 2);

        if (count($parts) < 2) {
            throw new IncompatibleStaticForCoercionException(sprintf(
                'Route name "%s" is not fully qualified (missing library name)',
                $fqRouteName
            ));
        }

        list($libraryName, $routeName) = $parts;

        try {
            $route = $evaluationContext->getRoute($libraryName, $routeName);
        } catch (NotFoundException $exception) {
            throw new IncompatibleStaticForCoercionException(sprintf(
                'Route "%s" for library "%s" does not exist',
                $routeName,
                $libraryName
            ));
        }

        // Coerce the route argument statics as per the route's parameter bag model
        return new StaticStructureExpression(
            $route->getParameterBagModel()->coerceStaticBag(
                $static->getAttributeStaticBag(),
                $evaluationContext
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function determine(TypeInterface $destinationType, TypeInterface $candidateType)
    {
        $candidateValidationContext = $candidateType->getValidationContext();

        // Allow an equivalent exotic type to this one through
        // (eg. when a valid value is "passed through", like from one widget definition to another)
        if ($candidateType instanceof ExoticType &&
            $candidateType->getExoticTypeName() === $this->getName()
        ) {
            return new UnrestrictiveTypeDetermination();
        }

        $insideExpressionBag = $candidateValidationContext->queryForBoolean(
            new InsideExpressionBagQuery(),
            $candidateValidationContext->getCurrentActNode()
        );

        if (!$insideExpressionBag) {
            // We're not inside a bag providing a value for the route arguments,
            // so don't attempt to resolve the route name
            return new RestrictiveTypeDetermination($candidateType);
        }

        // Fetch the expression that specifies the route name in the expression bag
        $routeNameExpression = $candidateValidationContext->queryForActNode(
            new SiblingBagExpressionNodeQuery($this->routeNameStaticName),
            $candidateValidationContext->getCurrentActNode()
        );

        if (!$routeNameExpression instanceof ExpressionNodeInterface ||
            $routeNameExpression instanceof UnknownExpressionNode
        ) {
            return new RestrictiveTypeDetermination(
                new UnresolvedType(
                    'Route name expression could not be fetched',
                    $this->destinationValidationContext
                )
            );
        }

        $routeNameExpressionType = $candidateValidationContext->getExpressionResultType($routeNameExpression);

        // Only valued types may be specified, as we need to be able to statically
        // resolve the name of the route
        if (!$routeNameExpressionType instanceof ValuedType) {
            // Route name is not provided as a valid static value - however, log no violation
            // here, as the route name argument's determiner will handle this
            return new UnrestrictiveTypeDetermination();
        }

        $wrappedType = $routeNameExpressionType->getWrappedType();

        // Only text-valued statics will be able to specify the route name
        if (!$wrappedType instanceof StaticType ||
            $wrappedType->getStaticClass() !== TextExpression::class
        ) {
            // Route name is not provided as a valid static value - however, log no violation
            // here, as the route name argument's determiner will handle this
            return new UnrestrictiveTypeDetermination();
        }

        $fqRouteName = $routeNameExpressionType->getStaticValue()->toNative();

        $parts = explode('.', $fqRouteName, 2);

        if (count($parts) < 2) {
            // Route name is not fully qualified (missing library name) - however, log no violation
            // here, as the route name argument's determiner will handle this
            return new UnrestrictiveTypeDetermination();
        }

        list($libraryName, $routeName) = $parts;

        $routeExists = $candidateValidationContext->queryForBoolean(
            new RouteExistsQuery($libraryName, $routeName),
            $candidateValidationContext->getCurrentActNode()
        );

        if (!$routeExists) {
            // Route does not exist - however, log no violation here,
            // as the route name argument's determiner will handle this
            return new UnrestrictiveTypeDetermination();
        }

        $routeNode = $candidateValidationContext->queryForActNode(
            new RouteNodeQuery($libraryName, $routeName),
            $candidateValidationContext->getCurrentActNode()
        );

        if (!$routeNode instanceof RouteNodeInterface) {
            // Route node could not be fetched - however, log no violation here,
            // as the route name argument's determiner will handle this
            return new UnrestrictiveTypeDetermination();
        }

        $routeNodeValidationContext = $this->destinationValidationContext->createValidationContextForActNode($routeNode);
        $determinedRouteParameterBagModel = $routeNode->getParameterBagModel()
            ->determine($routeNodeValidationContext);

        return new RestrictiveTypeDetermination(
            new StaticStructureType($determinedRouteParameterBagModel, $this->destinationValidationContext)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }
}
