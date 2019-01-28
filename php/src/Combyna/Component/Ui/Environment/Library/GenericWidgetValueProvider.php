<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Environment\Library;

use Combyna\Component\Environment\Library\WidgetValueProviderLocator;
use Combyna\Component\Environment\Library\WidgetValueProviderLocatorInterface;
use Combyna\Component\Expression\StaticExpressionFactoryInterface;

/**
 * Class GenericWidgetValueProvider
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class GenericWidgetValueProvider implements GenericWidgetValueProviderInterface
{
    /**
     * @var StaticExpressionFactoryInterface
     */
    private $staticExpressionFactory;

    /**
     * @var WidgetValueProviderLocatorInterface[]
     */
    private $valueProviderLocators = [];

    /**
     * @param StaticExpressionFactoryInterface $staticExpressionFactory
     */
    public function __construct(StaticExpressionFactoryInterface $staticExpressionFactory)
    {
        $this->staticExpressionFactory = $staticExpressionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function addProvider($libraryName, $widgetDefinitionName, $valueName, callable $callable)
    {
        $this->valueProviderLocators[] = new WidgetValueProviderLocator(
            $libraryName,
            $widgetDefinitionName,
            $valueName,
            $callable
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetValueProviderLocators()
    {
        return $this->valueProviderLocators;
    }
}
