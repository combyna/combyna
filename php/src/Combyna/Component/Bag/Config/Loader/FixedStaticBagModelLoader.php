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
            $staticType = $this->typeLoader->load($definitionConfig);

            // Allow the static to specify a default value
            $defaultExpressionNode =
                (is_array($definitionConfig) && isset($definitionConfig['default'])) ?
                $this->expressionLoader->load($definitionConfig['default']) :
                null;

            $staticDefinitionNodes[] = new FixedStaticDefinitionNode(
                $name,
                $staticType,
                $defaultExpressionNode
            );
        }

        return new FixedStaticBagModelNode($staticDefinitionNodes);
    }
}
