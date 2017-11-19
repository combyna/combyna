<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\App\Config\Loader;

use Combyna\Component\App\Config\Act\HomeNode;
use Combyna\Component\Bag\Config\Loader\ExpressionBagLoader;
use InvalidArgumentException;

/**
 * Class HomeLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class HomeLoader implements HomeLoaderInterface
{
    /**
     * @var ExpressionBagLoader
     */
    private $expressionBagLoader;

    /**
     * @param ExpressionBagLoader $expressionBagLoader
     */
    public function __construct(ExpressionBagLoader $expressionBagLoader)
    {
        $this->expressionBagLoader = $expressionBagLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function loadHome(array $homeConfig)
    {
        $parts = explode('.', $homeConfig['route'], 2);

        if (count($parts) < 2) {
            throw new InvalidArgumentException(
                'Home route name must be in format <library>.<name>, received "' . $homeConfig['route'] . '"'
            );
        }

        list($routeLibraryName, $routeName) = $parts;

        $attributeExpressionBagNode = $this->expressionBagLoader->load(
            array_key_exists('attributes', $homeConfig) ? $homeConfig['attributes'] : []
        );

        return new HomeNode($routeLibraryName, $routeName, $attributeExpressionBagNode);
    }
}
