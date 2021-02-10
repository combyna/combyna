<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Test\Ui;

use Combyna\Component\Environment\Library\WidgetValueProviderLocator;
use Combyna\Component\Environment\Library\WidgetValueProviderProviderInterface;
use Combyna\Component\Expression\StaticExpressionFactoryInterface;
use Combyna\Component\Expression\StaticInterface;

/**
 * Class TestGuiWidgetProviders
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TestGuiWidgetProviders implements WidgetValueProviderProviderInterface
{
    /**
     * @var StaticExpressionFactoryInterface
     */
    private $staticExpressionFactory;

    /**
     * @var callable
     */
    private $textboxTextProvider;

    /**
     * @param StaticExpressionFactoryInterface $staticExpressionFactory
     */
    public function __construct(StaticExpressionFactoryInterface $staticExpressionFactory)
    {
        $this->staticExpressionFactory = $staticExpressionFactory;

        $this->textboxTextProvider = function (array $widgetStatePath) {
            return $this->staticExpressionFactory->createTextExpression(
                'Text: (' . implode('-', $widgetStatePath) . ')'
            );
        };
    }

    /**
     * Builds a string that includes the widget state path to check it is passed correctly
     *
     * @param array $widgetStatePath
     * @return StaticInterface
     */
    public function getTextboxText(array $widgetStatePath)
    {
        $textboxTextProvider = $this->textboxTextProvider;

        return $textboxTextProvider($widgetStatePath);
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetValueProviderLocators()
    {
        return [
            new WidgetValueProviderLocator(
                'gui',
                'textbox',
                'text',
                [$this, 'getTextboxText']
            )
        ];
    }

    /**
     * @param callable $textboxTextProvider
     */
    public function stubTextboxTextProvider(callable $textboxTextProvider)
    {
        $this->textboxTextProvider = $textboxTextProvider;
    }
}
