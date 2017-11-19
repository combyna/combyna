<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Config\Loader;

use Combyna\Component\Bag\Config\Loader\FixedStaticBagModelLoaderInterface;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Expression\Config\Loader\ExpressionLoaderInterface;
use Combyna\Component\Ui\Config\Act\PageViewNode;
use Combyna\Component\Ui\Store\Config\Loader\ViewStoreLoaderInterface;

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
     * @var ViewStoreLoaderInterface
     */
    private $storeLoader;

    /**
     * @var WidgetLoaderInterface
     */
    private $widgetLoader;

    /**
     * @param ExpressionLoaderInterface $expressionLoader
     * @param FixedStaticBagModelLoaderInterface $fixedStaticBagModelLoader
     * @param ViewStoreLoaderInterface $storeLoader
     * @param WidgetLoaderInterface $widgetLoader
     */
    public function __construct(
        ExpressionLoaderInterface $expressionLoader,
        FixedStaticBagModelLoaderInterface $fixedStaticBagModelLoader,
        ViewStoreLoaderInterface $storeLoader,
        WidgetLoaderInterface $widgetLoader
    ) {
        $this->expressionLoader = $expressionLoader;
        $this->fixedStaticBagModelLoader = $fixedStaticBagModelLoader;
        $this->storeLoader = $storeLoader;
        $this->widgetLoader = $widgetLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function loadPageView($name, array $viewConfig, EnvironmentNode $environmentNode)
    {
        $titleExpressionNode = $this->expressionLoader->load($viewConfig['title']);
        $description = $viewConfig['description'];
        $attributeBagModelNode = $this->fixedStaticBagModelLoader->load(
            isset($viewConfig['attributes']) ? $viewConfig['attributes'] : []
        );
        $rootWidgetNode = $this->widgetLoader->loadWidget(
            $viewConfig['widget'],
            $environmentNode
        );
        $storeNode = isset($viewConfig['store']) ?
            $this->storeLoader->load($viewConfig['store']) :
            null;
        $visibilityExpressionNode = isset($viewConfig['visible']) ?
            $this->expressionLoader->load($viewConfig['visible']) :
            null;

        return new PageViewNode(
            $name,
            $titleExpressionNode,
            $description,
            $attributeBagModelNode,
            $rootWidgetNode,
            $storeNode,
            $visibilityExpressionNode
        );
    }
}
