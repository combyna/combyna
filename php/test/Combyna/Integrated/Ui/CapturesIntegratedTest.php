<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Integrated\Ui;

use Combyna\Component\App\AppInterface;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Framework\Combyna;
use Combyna\Component\Renderer\Html\HtmlRenderer;
use Combyna\Integrated\Fixtures\TestGuiWidgetProviders;
use Concise\Core\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CapturesIntegratedTest
 *
 * Tests the UI "capture" feature, used for fetching eg. the text value of a textbox input field
 * from a button that needs to dispatch a signal with its value inside the payload
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CapturesIntegratedTest extends TestCase
{
    /**
     * @var AppInterface
     */
    private $app;

    /**
     * @var Combyna
     */
    private $combyna;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var EnvironmentNode
     */
    private $environment;

    /**
     * @var HtmlRenderer
     */
    private $htmlRenderer;

    /**
     * @var TestGuiWidgetProviders
     */
    private $testGuiWidgetProviders;

    public function setUp()
    {
        global $combynaBootstrap;
        $this->container = $combynaBootstrap->createContainer();

        $this->combyna = $this->container->get('combyna');
        $this->htmlRenderer = $this->container->get('combyna.renderer.html');
        $this->testGuiWidgetProviders = $this->container->get('combyna_test.gui_widget_providers');
        $yamlParser = $this->container->get('combyna.config.yaml_parser');

        $appConfig = $yamlParser->parse(file_get_contents(__DIR__ . '/Fixtures/capturesTest.cyn.yml'));

        $this->environment = $this->combyna->createEnvironment();
        $this->app = $this->combyna->createApp($appConfig, $this->environment);
    }

    public function testRenderAppReturnsTheCorrectHtmlOnInitialLoad()
    {
        $appState = $this->app->createInitialState();

        $expectedHtml =
            '<div class="combyna-view" data-view-name="my_view">' .
            "\n" .
            '    The hardcoded value is: I am hardcoded.' .
                '<input name="combyna-widget-my_view-root-1" type="text" value="[Enter a phone number]">' .
                'I exist only to hardcode the value of a capture, as a demo' .
                'The phone number is: [Enter a phone number].' .
                'Captured things: [8, 10, 12]' .

                // Repeated fields
                'Enter a multiplier for item #1 - 4:<input name="combyna-widget-my_view-root-5-0-1" type="text" value="2">' .
                'Enter a multiplier for item #2 - 5:<input name="combyna-widget-my_view-root-5-1-1" type="text" value="2">' .
                'Enter a multiplier for item #3 - 6:<input name="combyna-widget-my_view-root-5-2-1" type="text" value="2">' .

                // Output of the capture whose setter widget is never present
                'The capture that is never set: "My default capture value"' .
                // (non-present widget is not visible)
                // Output of the capture whose setter widget is always present
                'The capture that is always set: "I will always be used as my widget is present"' .
                'I am always present, so will set the capture' . // Present capture setter widget
                "\n" .
            '</div>';
        self::assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState));
    }

    public function testRenderAppReturnsTheCorrectHtmlAfterEnteringSomeTextInThePhoneNumberTextbox()
    {
        $appState = $this->app->createInitialState();

        $this->testGuiWidgetProviders->stubTextboxTextProvider(
            function (array $widgetStatePath) {
                $widgetName = 'combyna-widget-' . implode('-', $widgetStatePath);

                switch ($widgetName) {
                    case 'combyna-widget-my_view-root-1':
                        return 'My typed phone number: (' . implode('-', $widgetStatePath) . ')';
                    default:
                        return '2';
                }
            }
        );

        // Update the state so that it will re-evaluate the widget value
        // and pull in the one we just stubbed above to simulate typing into the textbox
        $appState = $this->app->reevaluateUiState($appState);

        $expectedHtml =
            '<div class="combyna-view" data-view-name="my_view">' .
            "\n" .
            '    The hardcoded value is: I am hardcoded.' .
                '<input name="combyna-widget-my_view-root-1" type="text" value="[Enter a phone number]">' .
                'I exist only to hardcode the value of a capture, as a demo' .
                'The phone number is: My typed phone number: (my_view-root-1).' .
                'Captured things: [8, 10, 12]' .

                // Repeated fields
                'Enter a multiplier for item #1 - 4:<input name="combyna-widget-my_view-root-5-0-1" type="text" value="2">' .
                'Enter a multiplier for item #2 - 5:<input name="combyna-widget-my_view-root-5-1-1" type="text" value="2">' .
                'Enter a multiplier for item #3 - 6:<input name="combyna-widget-my_view-root-5-2-1" type="text" value="2">' .

                // Output of the capture whose setter widget is never present
                'The capture that is never set: "My default capture value"' .
                // (non-present widget is not visible)
                // Output of the capture whose setter widget is always present
                'The capture that is always set: "I will always be used as my widget is present"' .
                'I am always present, so will set the capture' . // Present capture setter widget
                "\n" .
            '</div>';
        self::assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState));
    }

    public function testRenderAppReturnsTheCorrectHtmlAfterEnteringSomeTextInTheSecondRepeatedMultiplierTextbox()
    {
        $appState = $this->app->createInitialState();

        $this->testGuiWidgetProviders->stubTextboxTextProvider(
            function (array $widgetStatePath) {
                $widgetName = 'combyna-widget-' . implode('-', $widgetStatePath);

                switch ($widgetName) {
                    case 'combyna-widget-my_view-root-1':
                        return '[Enter a phone number]'; // Leave this one unchanged
                    case 'combyna-widget-my_view-root-5-1-1':
                        return '100'; // User typed "100", so let's multiply by 100
                    default:
                        return '2';
                }
            }
        );

        // Update the state so that it will re-evaluate the widget value
        // and pull in the one we just stubbed above to simulate typing into the textbox
        $appState = $this->app->reevaluateUiState($appState);

        $expectedHtml =
            '<div class="combyna-view" data-view-name="my_view">' .
            "\n" .
            '    The hardcoded value is: I am hardcoded.' .
                '<input name="combyna-widget-my_view-root-1" type="text" value="[Enter a phone number]">' .
                'I exist only to hardcode the value of a capture, as a demo' .
                'The phone number is: [Enter a phone number].' .
                'Captured things: [8, 500, 12]' .

                // Repeated fields
                'Enter a multiplier for item #1 - 4:<input name="combyna-widget-my_view-root-5-0-1" type="text" value="2">' .
                'Enter a multiplier for item #2 - 5:<input name="combyna-widget-my_view-root-5-1-1" type="text" value="2">' .
                'Enter a multiplier for item #3 - 6:<input name="combyna-widget-my_view-root-5-2-1" type="text" value="2">' .

                // Output of the capture whose setter widget is never present
                'The capture that is never set: "My default capture value"' .
                // (non-present widget is not visible)
                // Output of the capture whose setter widget is always present
                'The capture that is always set: "I will always be used as my widget is present"' .
                'I am always present, so will set the capture' . // Present capture setter widget
                "\n" .
            '</div>';
        self::assertSame($expectedHtml, $this->htmlRenderer->renderApp($appState));
    }
}
