<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Loader;

use InvalidArgumentException;

/**
 * Class DelegatingExpressionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DelegatingExpressionLoader implements ExpressionLoaderInterface
{
    /**
     * @var ExpressionTypeLoaderInterface[]
     */
    private $loaders = [];

    /**
     * @param ExpressionTypeLoaderInterface $expressionTypeLoader
     */
    public function addLoader(ExpressionTypeLoaderInterface $expressionTypeLoader)
    {
        $this->loaders[$expressionTypeLoader->getType()] = $expressionTypeLoader;
    }
    
    /**
     * {@inheritdoc}
     */
    public function load(array $config)
    {
        if (!array_key_exists('type', $config)) {
            throw new InvalidArgumentException('Missing "type" element');
        }

        $type = $config['type'];

        if (!array_key_exists($type, $this->loaders)) {
            throw new InvalidArgumentException(
                'No loader is registered for expressions of type "' . $type . '"'
            );
        }

        return $this->loaders[$type]->load($config);
    }
}
