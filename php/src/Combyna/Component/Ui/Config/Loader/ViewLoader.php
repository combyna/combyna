<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Config\Loader;

use Combyna\Component\Bag\Config\Loader\FixedStaticBagModelLoaderInterface;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Expression\Config\Loader\ExpressionLoaderInterface;
use Combyna\Component\Ui\Config\Act\ViewNode;

/**
 * Class ViewLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ViewLoader implements ViewLoaderInterface
{
    /**
     * @var ExpressionLoaderInterface
     */
    private $expressionLoader;

    /**
     * @var FixedStaticBagModelLoaderInterface
     */
    private $fixedStaticBagModelLoader;

    /**
     * @var WidgetLoaderInterface
     */
    private $widgetLoader;

    /**
     * @param ExpressionLoaderInterface $expressionLoader
     * @param FixedStaticBagModelLoaderInterface $fixedStaticBagModelLoader
     * @param WidgetLoaderInterface $widgetLoader
     */
    public function __construct(
        ExpressionLoaderInterface $expressionLoader,
        FixedStaticBagModelLoaderInterface $fixedStaticBagModelLoader,
        WidgetLoaderInterface $widgetLoader
    ) {
        $this->expressionLoader = $expressionLoader;
        $this->fixedStaticBagModelLoader = $fixedStaticBagModelLoader;
        $this->widgetLoader = $widgetLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function loadView($name, array $viewConfig, EnvironmentNode $environmentNode)
    {
        $titleExpressionNode = $this->expressionLoader->load($viewConfig['title']);
        $description = $viewConfig['description'];
        $attributeBagModelNode = $this->fixedStaticBagModelLoader->load(
            array_key_exists('attributes', $viewConfig) ? $viewConfig['attributes'] : []
        );
        $rootWidgetNode = $this->widgetLoader->loadWidget(
            $viewConfig['widget'],
            $environmentNode
        );
        $visibilityExpressionNode = array_key_exists('visible', $viewConfig) ?
            $this->expressionLoader->load($viewConfig['visible']) :
            null;

        return new ViewNode(
            $name,
            $titleExpressionNode,
            $description,
            $attributeBagModelNode,
            $rootWidgetNode,
            $visibilityExpressionNode
        );
    }
}
