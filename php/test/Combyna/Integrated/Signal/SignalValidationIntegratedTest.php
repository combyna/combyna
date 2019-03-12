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
use Concise\Core\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SignalValidationIntegratedTest
 *
 * Tests the validation for the "signal" feature
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalValidationIntegratedTest extends TestCase
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

    public function setUp()
    {
        global $combynaBootstrap;
        $this->container = $combynaBootstrap->createContainer();

        $this->combyna = $this->container->get('combyna');
        $this->htmlRenderer = $this->container->get('combyna.renderer.html');
        $yamlParser = $this->container->get('combyna.config.yaml_parser');

        $this->appConfig = $yamlParser->parse(file_get_contents(__DIR__ . '/Fixtures/signalValidationTest.cyn.yml'));

        $this->environment = $this->combyna->createEnvironment();
    }

    public function testSignalDefinitionAndUsesAreValidated()
    {
        $this->setExpectedException(
            ValidationFailureException::class,

            // Test that signal payload statics being set must be defined by the signal definition
            'ACT node [app].[page-view].[view:my_view].[widget-group:root].[defined-widget:0].[act-node].[trigger:gui.click].[signal]' .
            ' - Payload does not define a static with name "an_undefined_static". :: ' .

            // Test that signal handlers must refer to a defined signal
            'ACT node [app].[page-view].[view:my_view].[view-store].[view store].[signal:app.my_unknown_signal handler].[signal-definition-reference]' .
            ' - Signal "app.my_unknown_signal" is not defined. :: ' .

            // Test that view store slot instructions must refer to a defined slot
            'ACT node [app].[page-view].[view:my_view].[view-store].[view store].[signal:app.my_unknown_signal handler].[set_slot]' .
            ' - View store does not contain a slot with name "an_unknown_slot". :: ' .

            // Test that view store slot instruction expressions must result in a type allowed by the slot
            'ACT node [app].[page-view].[view:my_view].[view-store].[view store].[signal:app.my_unknown_signal handler].[set_slot]' .
            ' - value expression result type must match slot type would get' .
            ' [unknown<Payload static "an_unknown_payload_static" for undefined signal "my_unknown_signal" of defined library "app">],' .
            ' expects [unknown<Unknown fixed static "an_unknown_slot">]. :: ' .

            // Test that signal payload expressions must refer to a static that is defined for the signal's payload
            'ACT node [app].[page-view].[view:my_view].[view-store].[view store].[signal:app.my_unknown_signal handler].[set_slot].[signal-payload-static]' .
            ' - Payload does not contain a static with name "an_unknown_payload_static". :: ' .

            // Test that the "broadcast" property for a signal definition must be a boolean
            'ACT node [app].[invalid-signal-definition] - Invalid signal "my_signal_with_invalid_broadcast_value" of library "app"' .
            ' - Config element "broadcast" should be of one of the type(s) ["boolean"] but is "string" for whether to broadcast the signal externally'
        );

        $this->combyna->createApp($this->appConfig, $this->environment);
    }
}
