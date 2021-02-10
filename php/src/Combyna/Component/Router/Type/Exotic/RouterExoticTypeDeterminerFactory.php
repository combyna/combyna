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

use Combyna\Component\Type\Exotic\ExoticTypeDeterminerInterface;
use Combyna\Component\Type\Exotic\ExoticTypeTypeDeterminerFactoryInterface;
use Combyna\Component\Type\Exotic\UnresolvedExoticTypeDeterminer;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class RouterExoticTypeDeterminerFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RouterExoticTypeDeterminerFactory implements ExoticTypeTypeDeterminerFactoryInterface
{
    const ROUTE_STATIC_CONFIG_NAME = 'route_static';
    const ROUTE_ARGUMENT_STRUCTURE_STATIC_CONFIG_NAME = 'arguments_static';

    /**
     * Creates a new RouteArgumentsExoticTypeDeterminer
     *
     * @param array $config
     * @param ValidationContextInterface $sourceValidationContext
     * @return RouteArgumentsExoticTypeDeterminer|ExoticTypeDeterminerInterface
     */
    public function createRouteArgumentsDeterminer(
        array $config,
        ValidationContextInterface $sourceValidationContext
    ) {
        if (!array_key_exists(self::ROUTE_STATIC_CONFIG_NAME, $config)) {
            return new UnresolvedExoticTypeDeterminer(
                sprintf(
                    'Missing config option "%s" for exotic type "%s"',
                    self::ROUTE_STATIC_CONFIG_NAME,
                    RouteArgumentsExoticTypeDeterminer::NAME
                ),
                $sourceValidationContext
            );
        }

        return new RouteArgumentsExoticTypeDeterminer(
            $config[self::ROUTE_STATIC_CONFIG_NAME],
            $sourceValidationContext
        );
    }

    /**
     * Creates a new RouteNameExoticTypeDeterminer
     *
     * @param array $config
     * @param ValidationContextInterface $sourceValidationContext
     * @return RouteNameExoticTypeDeterminer|ExoticTypeDeterminerInterface
     */
    public function createRouteNameDeterminer(
        array $config,
        ValidationContextInterface $sourceValidationContext
    ) {
        if (!array_key_exists(self::ROUTE_ARGUMENT_STRUCTURE_STATIC_CONFIG_NAME, $config)) {
            return new UnresolvedExoticTypeDeterminer(
                sprintf(
                    'Missing config option "%s" for exotic type "%s"',
                    self::ROUTE_ARGUMENT_STRUCTURE_STATIC_CONFIG_NAME,
                    RouteNameExoticTypeDeterminer::NAME
                ),
                $sourceValidationContext
            );
        }

        return new RouteNameExoticTypeDeterminer(
            $config[self::ROUTE_ARGUMENT_STRUCTURE_STATIC_CONFIG_NAME],
            $sourceValidationContext
        );
    }

    /**
     * Fetches a map from exotic type name to the factory callable on this service
     *
     * @return array
     */
    public function getTypeNameToFactoryCallableMap()
    {
        return [
            RouteArgumentsExoticTypeDeterminer::NAME => [$this, 'createRouteArgumentsDeterminer'],
            RouteNameExoticTypeDeterminer::NAME => [$this, 'createRouteNameDeterminer']
        ];
    }
}
