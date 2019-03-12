<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Loader;

use Combyna\Component\Common\Delegator\DelegatorInterface;
use Combyna\Component\Expression\Config\Act\UnknownExpressionTypeNode;

/**
 * Class DelegatingExpressionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DelegatingExpressionLoader implements ExpressionLoaderInterface, DelegatorInterface
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
            // Missing "type" element
            return new UnknownExpressionTypeNode(null);
        }

        $type = $config['type'];

        if (!array_key_exists($type, $this->loaders)) {
            // No loader is registered for expressions of this type
            return new UnknownExpressionTypeNode($type);
        }

        return $this->loaders[$type]->load($config);
    }
}
