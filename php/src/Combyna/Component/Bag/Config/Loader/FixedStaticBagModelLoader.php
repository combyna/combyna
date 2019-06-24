<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Bag\Config\Loader;

use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNode;
use Combyna\Component\Bag\Config\Act\FixedStaticDefinitionNode;
use Combyna\Component\Expression\Config\Loader\ExpressionLoaderInterface;
use Combyna\Component\Type\Config\Loader\TypeLoaderInterface;

/**
 * Class FixedStaticBagModelLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class FixedStaticBagModelLoader implements FixedStaticBagModelLoaderInterface
{
    /**
     * @var ExpressionLoaderInterface
     */
    private $expressionLoader;

    /**
     * @var TypeLoaderInterface
     */
    private $typeLoader;

    /**
     * @param ExpressionLoaderInterface $expressionLoader
     * @param TypeLoaderInterface $typeLoader
     */
    public function __construct(
        ExpressionLoaderInterface $expressionLoader,
        TypeLoaderInterface $typeLoader
    ) {
        $this->expressionLoader = $expressionLoader;
        $this->typeLoader = $typeLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $modelConfig)
    {
        $staticDefinitionNodes = [];

        foreach ($modelConfig as $name => $definitionConfig) {
            // Allow the static to specify a default value
            if (is_array($definitionConfig) && isset($definitionConfig['default'])) {
                $defaultExpressionNode = $this->expressionLoader->load($definitionConfig['default']);

                // Remove the "default" element as the type loader won't be expecting it
                unset($definitionConfig['default']);
            } else {
                $defaultExpressionNode = null;
            }

            $staticTypeDeterminer = $this->typeLoader->load($definitionConfig);

            $staticDefinitionNodes[] = new FixedStaticDefinitionNode(
                $name,
                $staticTypeDeterminer,
                $defaultExpressionNode
            );
        }

        return new FixedStaticBagModelNode($staticDefinitionNodes);
    }
}
