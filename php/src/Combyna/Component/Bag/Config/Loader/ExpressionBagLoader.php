<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Bag\Config\Loader;

use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Expression\Config\Loader\ExpressionLoaderInterface;

/**
 * Class ExpressionBagLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ExpressionBagLoader implements ExpressionBagLoaderInterface
{
    /**
     * @var ExpressionLoaderInterface
     */
    private $expressionLoader;

    /**
     * @param ExpressionLoaderInterface $expressionLoader
     */
    public function __construct(ExpressionLoaderInterface $expressionLoader)
    {
        $this->expressionLoader = $expressionLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $bagConfig)
    {
        $expressionNodes = [];

        foreach ($bagConfig as $name => $expressionConfig) {
            $expressionNodes[$name] = $this->expressionLoader->load($expressionConfig);
        }

        return new ExpressionBagNode($expressionNodes);
    }
}
