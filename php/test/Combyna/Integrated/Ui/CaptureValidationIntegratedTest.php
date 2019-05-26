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

use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Framework\Combyna;
use Combyna\Component\Renderer\Html\HtmlRenderer;
use Combyna\Component\Validator\Exception\ValidationFailureException;
use Combyna\Integrated\Fixtures\TestGuiWidgetProviders;
use Concise\Core\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CaptureValidationIntegratedTest
 *
 * Tests the validation for the UI "capture" feature
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CaptureValidationIntegratedTest extends TestCase
{
    /**
     * @var array
     */
    private $appConfig;

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

        $this->appConfig = $yamlParser->parse(file_get_contents(__DIR__ . '/Fixtures/capturesValidationTest.cyn.yml'));

        $this->environment = $this->combyna->createEnvironment();
    }

    public function testCapturesAreValidated()
    {
        $this->setExpectedException(
            ValidationFailureException::class,

            // Test that captures must be set
            'ACT node [app].[page-view].[view:my_view].[widget-group:root]' .
            ' - Capture "a_capture_that_doesnt_get_set" should be set exactly once, but was set 0 time(s). :: ' .

            // Test that captures must only be set once
            'ACT node [app].[page-view].[view:my_view].[widget-group:root]' .
            ' - Capture "a_capture_that_gets_set_multiple_times" should be set exactly once, but was set 3 time(s). :: ' .

            // Test that capture definitions must not shadow capture definitions of ancestors
            'ACT node [app].[page-view].[view:my_view].[widget-group:root].[text-widget]' .
            ' - Capture "a_capture_that_gets_shadowed" would shadow a capture of the same name that is defined by an ancestor. :: ' .

            // Test that `capture(...)` (read) expressions must refer to defined captures
            'ACT node [app].[page-view].[view:my_view].[widget-group:root].[text-widget].[concatenation].[list].[expression-list].[capture]' .
            ' - No sub-validation context was able to handle the result type query: The type of the capture "an_undefined_capture". :: ' .
            'ACT node [app].[page-view].[view:my_view].[widget-group:root].[text-widget].[concatenation]' .
            ' - operand list would get [list<text|unknown<The type of the capture "an_undefined_capture">>], expects [list<text|number>]. :: ' .
            'ACT node [app].[page-view].[view:my_view].[widget-group:root].[text-widget].[concatenation].[list].[expression-list].[capture]' .
            ' - Capture "an_undefined_capture" is not defined. :: ' .

            // Test that captures defined outside but set inside a conditional must define a default expression
            'ACT node [app].[page-view].[view:my_view].[widget-group:root].[conditional:5].[text-widget]' .
            ' - Capture "a_capture_that_is_inside_conditional_but_has_no_default" is only set optionally as it is inside a conditional or repeater, but is missing a default expression. :: ' .

            // Test that captures defined outside but set inside a repeater must define a default expression
            'ACT node [app].[page-view].[view:my_view].[widget-group:root].[repeater:6].[scope].[text-widget]' .
            ' - Capture "a_capture_that_is_inside_repeater_but_has_no_default" is only set optionally as it is inside a conditional or repeater, but is missing a default expression. :: ' .

            // Test that captures defined & set outside, or defined & set inside a conditional/repeater
            // must _not_ define a default expression
            'ACT node [app].[page-view].[view:my_view].[widget-group:root].[conditional:7].[widget-group:consequent].[text-widget]' .
            ' - Capture "a_capture_that_is_defined_inside_conditional_but_set_unconditionally" is set unconditionally as it is not inside a conditional or repeater, but has a default expression. :: ' .

            // Test that the type of a capture defined outside but set inside a conditional or repeater
            // must allow a `list<...>` type for a repeater and `...|nothing` for a (nested) conditional
            'ACT node [app].[page-view].[view:my_view].[widget-group:root].[repeater:8].[scope].[conditional:repeated].[text-widget]' .
            ' - Capture "a_capture_that_is_set_inside_repeater_then_conditional_but_wrong_type" would get [list<text|nothing>], expects [number]'
        );

        $this->combyna->createApp($this->appConfig, $this->environment);
    }
}
