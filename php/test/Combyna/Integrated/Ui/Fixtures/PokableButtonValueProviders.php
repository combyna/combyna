<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Integrated\Ui\Fixtures;

use Combyna\Component\Environment\Library\WidgetValueProviderLocator;
use Combyna\Component\Environment\Library\WidgetValueProviderProviderInterface;
use Combyna\Component\Expression\StaticExpressionFactoryInterface;
use Combyna\Component\Expression\StaticInterface;

/**
 * Class PokableButtonValueProviders
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class PokableButtonValueProviders implements WidgetValueProviderProviderInterface
{
    /**
     * @var StaticExpressionFactoryInterface
     */
    private $staticExpressionFactory;

    /**
     * @param StaticExpressionFactoryInterface $staticExpressionFactory
     */
    public function __construct(StaticExpressionFactoryInterface $staticExpressionFactory)
    {
        $this->staticExpressionFactory = $staticExpressionFactory;
    }

    /**
     * Builds a string that includes the widget state path to check it is passed correctly
     *
     * @param array $widgetStatePath
     * @return StaticInterface
     */
    public function getNoise(array $widgetStatePath)
    {
        return $this->staticExpressionFactory->createTextExpression(
            'Bang: (' . implode('-', $widgetStatePath) . ')'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetValueProviderLocators()
    {
        return [
            new WidgetValueProviderLocator(
                'widget_values',
                'pokable_button',
                'noise',
                [$this, 'getNoise']
            )
        ];
    }
}
