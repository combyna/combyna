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
use Combyna\Component\Bag\Validation\Query\SiblingBagExpressionExistsQuery;
use Combyna\Component\Common\Exception\NotFoundException;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\StaticExpressionFactoryInterface;
use Combyna\Component\Expression\StaticInterface;
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
use Combyna\Component\Type\StaticType;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Type\UnresolvedType;
use Combyna\Component\Type\ValuedType;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class RouteNameExoticTypeDeterminer
 *
 * Custom type behaviour for validating that the route name passed to somewhere expecting one
 * is actually valid:
 *  - Statically resolves the route name, checks whether it exists and if it does,
 *    simply returns a static text type.
 *  - If the route does _not_ exist, then a violation is added at the candidate validation context
 *    (so that the violation's path points to the place the offending value is being passed in)
 *    to ensure validation will fail and returns an unresolved type.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RouteNameExoticTypeDeterminer implements ExoticTypeDeterminerInterface
{
    const NAME = 'route_name';

    /**
     * @var ValidationContextInterface
     */
    private $destinationValidationContext;

    /**
     * @var string
     */
    private $routeArgumentStructureStaticName;

    /**
     * @param string $routeArgumentStructureStaticName
     * @param ValidationContextInterface $destinationValidationContext
     */
    public function __construct(
        $routeArgumentStructureStaticName,
        ValidationContextInterface $destinationValidationContext
    ) {
        $this->destinationValidationContext = $destinationValidationContext;
        $this->routeArgumentStructureStaticName = $routeArgumentStructureStaticName;
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
        if (!is_string($nativeValue)) {
            throw new IncompatibleNativeForCoercionException(sprintf(
                'Route name exotic type expects a string, %s given',
                gettype($nativeValue)
            ));
        }

        $parts = explode('.', $nativeValue, 2);

        if (count($parts) < 2) {
            throw new IncompatibleNativeForCoercionException(sprintf(
                'Route name "%s" is not fully qualified (missing library name)',
                $nativeValue
            ));
        }

        list($libraryName, $routeName) = $parts;

        try {
            $evaluationContext->getRoute($libraryName, $routeName);
        } catch (NotFoundException $exception) {
            throw new IncompatibleNativeForCoercionException(sprintf(
                'Route "%s" for library "%s" does not exist',
                $routeName,
                $libraryName
            ));
        }

        return $staticExpressionFactory->createTextExpression($nativeValue);
    }

    /**
     * {@inheritdoc}
     */
    public function coerceStatic(
        StaticInterface $static,
        EvaluationContextInterface $evaluationContext
    ) {
        if (!$static instanceof TextExpression) {
            throw new IncompatibleStaticForCoercionException(sprintf(
                'Route name exotic type expects a %s expression, %s given',
                TextExpression::TYPE,
                $static->getType()
            ));
        }

        $parts = explode('.', $static->toNative(), 2);

        if (count($parts) < 2) {
            throw new IncompatibleStaticForCoercionException(sprintf(
                'Route name "%s" is not fully qualified (missing library name)',
                $static->toNative()
            ));
        }

        list($libraryName, $routeName) = $parts;

        try {
            $evaluationContext->getRoute($libraryName, $routeName);
        } catch (NotFoundException $exception) {
            throw new IncompatibleStaticForCoercionException(sprintf(
                'Route "%s" for library "%s" does not exist',
                $routeName,
                $libraryName
            ));
        }

        return $static; // No coercion needed, just the runtime validation above
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

        // Only valued types may be specified, as we need to be able to statically
        // resolve the name of the route
        if (!$candidateType instanceof ValuedType) {
            return new RestrictiveTypeDetermination(
                new UnresolvedType(
                    'Type must be valued',
                    $this->destinationValidationContext
                )
            );
        }

        $wrappedType = $candidateType->getWrappedType();

        // Only text-valued statics will be able to specify the route name
        if (!$wrappedType instanceof StaticType ||
            $wrappedType->getStaticClass() !== TextExpression::class
        ) {
            return new RestrictiveTypeDetermination(
                new UnresolvedType(
                    sprintf(
                        'Type must be a text valued type'
                    ),
                    $this->destinationValidationContext
                )
            );
        }

        $fqRouteName = $candidateType->getStaticValue()->toNative();

        $parts = explode('.', $fqRouteName, 2);

        if (count($parts) < 2) {
            return new RestrictiveTypeDetermination(
                new UnresolvedType(
                    sprintf(
                        'Route name "%s" is not fully qualified (missing library name)',
                        $fqRouteName
                    ),
                    $this->destinationValidationContext
                )
            );
        }

        list($libraryName, $routeName) = $parts;

        $routeExists = $candidateValidationContext->queryForBoolean(
            new RouteExistsQuery($libraryName, $routeName),
            $candidateValidationContext->getCurrentActNode()
        );

        if (!$routeExists) {
            // Add a violation at the candidate type's context, so that if eg.
            // the non-existent route name is being provided by a widget instance
            // the violation will point to the individual widget at fault rather than its definition
            $candidateValidationContext->addGenericViolation(sprintf(
                'Route "%s" of library "%s" does not exist',
                $routeName,
                $libraryName
            ));

            return new RestrictiveTypeDetermination(
                new UnresolvedType(
                    sprintf(
                        'Route "%s" of library "%s" does not exist',
                        $routeName,
                        $libraryName
                    ),
                    $this->destinationValidationContext
                )
            );
        }

        /** @var RouteNodeInterface $routeNode */
        // Query from the candidate validation context rather than destination,
        // as the library's validation context tree won't have access to the special `app` library
        // so validation would fail if this query is attempted from the wrong context.
        $routeNode = $candidateValidationContext->queryForActNode(
            new RouteNodeQuery($libraryName, $routeName),
            $candidateValidationContext->getCurrentActNode()
        );

        /*
         * Now check that the arguments structure is specified if the route defines parameters,
         * as the default empty structure would otherwise be invalid. Note that there is no
         * need to validate the structure itself, just that it is specified, as the arguments
         * determiner will handle that instead.
         *
         * TODO: This should not be a concern of the route name determiner, but due to
         *       the way optional statics currently work this check cannot be done
         *       from the route arguments determiner
         */
        if (!$routeNode->getParameterBagModel()->isEmpty()) {
            $routeArgumentsAreSpecified = $candidateValidationContext->queryForBoolean(
                new SiblingBagExpressionExistsQuery($this->routeArgumentStructureStaticName),
                $candidateValidationContext->getCurrentActNode()
            );

            if (!$routeArgumentsAreSpecified) {
                // Add a violation at the candidate type's context, so that if eg.
                // the non-existent route name is being provided by a widget instance
                // the violation will point to the individual widget at fault rather than its definition
                $candidateValidationContext->addGenericViolation(sprintf(
                    'Required arguments for route "%s" of library "%s" via "%s" attribute are missing',
                    $routeName,
                    $libraryName,
                    $this->routeArgumentStructureStaticName
                ));

                return new RestrictiveTypeDetermination(
                    new UnresolvedType(
                        sprintf(
                            'Required arguments for route "%s" of library "%s" via "%s" attribute are missing',
                            $routeName,
                            $libraryName,
                            $this->routeArgumentStructureStaticName
                        ),
                        $this->destinationValidationContext
                    )
                );
            }
        }

        return new RestrictiveTypeDetermination(
            new StaticType(TextExpression::class, $this->destinationValidationContext)
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
