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
use Combyna\Harness\TestCase;
use Combyna\Test\Ui\TestGuiWidgetProviders;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class WidgetDefinitionValidationIntegratedTest
 *
 * Tests the validation for the UI "widget definition" feature
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetDefinitionValidationIntegratedTest extends TestCase
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

        $environmentConfig = $yamlParser->parse(file_get_contents(__DIR__ . '/Fixtures/widgetDefinitionValidationTest.env.cyn.yml'));
        $this->appConfig = $yamlParser->parse(file_get_contents(__DIR__ . '/Fixtures/widgetDefinitionValidationTest.cyn.yml'));

        $this->environment = $this->combyna->createEnvironment($environmentConfig);
    }

    public function testWidgetDefinitionsAreValidated()
    {
        $this->expectException(ValidationFailureException::class);
        $this->expectExceptionMessage(
            'ACT node [environment].[library:widget_definitions].[primitive-widget-def:my_invalid_lib_primitive].[fixed-static-bag-model].[fixed-static-definition:my_invalid_primitive_attr]' .
            ' - Expected type not to be unresolved, but it was: unknown<No loader is registered for types of type "my_invalid_primitive_attr_type">. :: ' .

            'ACT node [environment].[library:widget_definitions].[primitive-widget-def:my_invalid_lib_primitive].[event-definition-reference]' .
            ' - Event "my_invalid_lib.my_invalid_primitive_event" is not defined. :: ' .

            'ACT node [environment].[library:widget_definitions].[primitive-widget-def:my_invalid_lib_primitive]' .
            ' - Some value(s) are missing providers: "my_invalid_primitive_value". :: ' .

            'ACT node [environment].[library:widget_definitions].[primitive-widget-def:my_invalid_lib_primitive].[fixed-static-bag-model].[fixed-static-definition:my_invalid_primitive_value]' .
            ' - Expected type not to be unresolved, but it was: unknown<No loader is registered for types of type "my_invalid_primitive_value_type">. :: ' .

            'ACT node [environment].[library:widget_definitions].[compound-widget-def:my_invalid_lib_compound].[fixed-static-bag-model].[fixed-static-definition:my_invalid_compound_attr]' .
            ' - Expected type not to be unresolved, but it was: unknown<No loader is registered for types of type "my_invalid_compound_attr_type">. :: ' .

            'ACT node [environment].[library:widget_definitions].[compound-widget-def:my_invalid_lib_compound].[event-definition-reference]' .
            ' - Event "my_invalid_lib.my_invalid_compound_event" is not defined. :: ' .

            'ACT node [environment].[library:widget_definitions].[compound-widget-def:my_invalid_lib_compound].[widget-group:root].[defined-widget:0].[unknown-library-for-widget-definition]' .
            ' - Library "my_invalid_lib" does not exist in order to define widget definition "my_invalid_definition". :: ' .

            'ACT node [environment].[library:widget_definitions].[compound-widget-def:my_invalid_lib_compound].[widget-group:root].[defined-widget:0].[widget-definition-reference]' .
            ' - Widget definition "my_invalid_lib.my_invalid_definition" is not defined. :: ' .

            'ACT node [environment].[library:widget_definitions].[compound-widget-def:my_invalid_lib_compound].[widget-group:root].[defined-widget:1]' .
            ' - attributes for primitive "my_invalid_lib_primitive" widget of library "widget_definitions" my_invalid_primitive_attr would get [unknown<Unparsable expression: My @ invalid attr (!) expression!>], expects [unknown<No loader is registered for types of type "my_invalid_primitive_attr_type">]. :: ' .

            'ACT node [environment].[library:widget_definitions].[compound-widget-def:my_invalid_lib_compound].[widget-group:root].[defined-widget:1].[expression-bag].[unparsable]' .
            ' - Unparsable expression: My @ invalid attr (!) expression!. :: ' .

            'ACT node [environment].[library:widget_definitions].[compound-widget-def:my_invalid_lib_compound].[expression-bag].[unparsable]' .
            ' - Unparsable expression: My @ invalid value (!) expression!. :: ' .

            'ACT node [app].[page-view].[view:my_view].[defined-widget:root]' .
            ' - attributes for compound "my_invalid_app_compound" widget of library "app" compounds_label would get [unknown<Unparsable expression: This (!) is not a valid expression for label!>], expects [unknown<No loader is registered for types of type "my_invalid_compound_attr_type">]. :: ' .

            'ACT node [app].[page-view].[view:my_view].[defined-widget:root]' .
            ' - Compound widget is missing required child "compounds_child". :: ' .

            'ACT node [app].[page-view].[view:my_view].[defined-widget:root].[expression-bag].[unparsable]' .
            ' - Unparsable expression: This (!) is not a valid expression for label!. :: ' .

            'ACT node [app].[primitive-widget-def:my_invalid_app_primitive].[fixed-static-bag-model].[fixed-static-definition:primitives_label]' .
            ' - Expected type not to be unresolved, but it was: unknown<No loader is registered for types of type "my_invalid_primitive_attr_type">. :: ' .

            'ACT node [app].[compound-widget-def:my_invalid_app_compound].[fixed-static-bag-model].[fixed-static-definition:compounds_label]' .
            ' - Expected type not to be unresolved, but it was: unknown<No loader is registered for types of type "my_invalid_compound_attr_type">. :: ' .

            'ACT node [app].[compound-widget-def:my_invalid_app_compound].[defined-widget:root].[unknown-library-for-widget-definition]' .
            ' - Library "my_invalid_lib" does not exist in order to define widget definition "my_invalid_primitive_widget". :: ' .

            'ACT node [app].[compound-widget-def:my_invalid_app_compound].[defined-widget:root].[widget-definition-reference]' .
            ' - Widget definition "my_invalid_lib.my_invalid_primitive_widget" is not defined. :: ' .

            'ACT node [app].[compound-widget-def:my_invalid_app_compound].[defined-widget:root].[expression-bag].[unparsable]' .
            ' - Unparsable expression: This (!) is not a valid expression inside definition!. :: ' .

            'ACT node [app].[compound-widget-def:my_invalid_app_compound].[defined-widget:root].[defined-widget:primitives_child].[unknown-library-for-widget-definition]' .
            ' - Library "my_invalid_lib" does not exist in order to define widget definition "my_invalid_widget_definition". :: ' .

            'ACT node [app].[compound-widget-def:my_invalid_app_compound].[defined-widget:root].[defined-widget:primitives_child].[widget-definition-reference]' .
            ' - Widget definition "my_invalid_lib.my_invalid_widget_definition" is not defined'
        );

        $this->combyna->createApp($this->appConfig, $this->environment);
    }
}
