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

use Combyna\Component\Bag\BagFactoryInterface;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\StaticExpressionFactoryInterface;
use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Expression\TextExpression;
use Combyna\Component\Framework\Combyna;
use Combyna\Component\Renderer\Html\HtmlRenderer;
use Combyna\Component\Type\Exotic\Determination\RestrictiveTypeDetermination;
use Combyna\Component\Type\Exotic\ExoticTypeDeterminerInterface;
use Combyna\Component\Type\Exotic\ExoticTypeTypeDeterminerFactoryInterface;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Validator\Exception\ValidationFailureException;
use Combyna\Harness\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class RoutingValidationIntegratedTest
 *
 * Tests the validation for the routing feature
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RoutingValidationIntegratedTest extends TestCase
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

        $this->container->get('combyna.type.exotic.exotic_type_determiner_factory')->addFactory(new class implements ExoticTypeTypeDeterminerFactoryInterface {
            /**
             * {@inheritdoc}
             */
            public function getTypeNameToFactoryCallableMap()
            {
                return [
                    'routing_test_custom_exotic' => function (/*$config, $sourceValidationContext*/) {
                        return new class implements ExoticTypeDeterminerInterface {
                            /**
                             * {@inheritdoc}
                             */
                            public function coerceNative($nativeValue, StaticExpressionFactoryInterface $staticExpressionFactory, BagFactoryInterface $bagFactory, EvaluationContextInterface $evaluationContext)
                            {
                                return $staticExpressionFactory->createTextExpression($nativeValue);
                            }

                            /**
                             * {@inheritdoc}
                             */
                            public function coerceStatic(StaticInterface $static, EvaluationContextInterface $evaluationContext)
                            {
                                return $static;
                            }

                            /**
                             * {@inheritdoc}
                             */
                            public function determine(TypeInterface $destinationType, TypeInterface $candidateType)
                            {
                                return new RestrictiveTypeDetermination(
                                    new StaticType(TextExpression::class, $candidateType->getValidationContext())
                                );
                            }

                            /**
                             * {@inheritdoc}
                             */
                            public function getName()
                            {
                                return 'routing_test_custom_exotic';
                            }
                        };
                    }
                ];
            }
        });

        $environmentConfig = $yamlParser->parse(file_get_contents(__DIR__ . '/Fixtures/routingValidationTest.env.cyn.yml'));
        $this->appConfig = $yamlParser->parse(file_get_contents(__DIR__ . '/Fixtures/routingValidationTest.cyn.yml'));

        $this->environment = $this->combyna->createEnvironment($environmentConfig);
    }

    public function testRoutesAndRelatedWidgetsAndBuiltinsAreValidated()
    {
        $this->expectException(ValidationFailureException::class);
        $this->expectExceptionMessage(
            // Test that native functions' implementations must be installed
            'ACT node [environment].[library:routing_test].[native-function:create_custom_exotic] - Native function "create_custom_exotic" for library "routing_test" was never installed. :: ' .

            'ACT node [app].[page-view].[view:my_list_view].[widget-group:root].[text-widget].[concatenation]' .
            ' - operand list would get [list<text|unknown<No route for view "my_list_view" defines the parameter "not_a_valid_parameter_name">>], expects [list<text|number>]. :: ' .

            'ACT node [app].[page-view].[view:my_list_view].[widget-group:root].[text-widget].[concatenation].[list].[expression-list].[route-argument]' .
            ' - Current view routes do not all define parameter "not_a_valid_parameter_name" or do not define it identically. :: ' .

            'ACT node [app].[page-view].[view:my_list_view].[widget-group:root].[defined-widget:2].[expression-bag].[text]' .
            ' - Route "my_invalid_route_name" of library "app" does not exist. :: ' .

            'ACT node [app].[page-view].[view:my_list_view].[widget-group:root].[defined-widget:2]' .
            ' - attributes for compound "route_link" widget of library "gui" route would get [text<app.my_invalid_route...>], expects [exotic<route_name>]. :: ' .

            'ACT node [app].[page-view].[view:my_list_view].[widget-group:root].[defined-widget:3].[expression-bag].[text]' .
            ' - Route "your_invalid_route_name" of library "app" does not exist. :: ' .

            'ACT node [app].[page-view].[view:my_list_view].[widget-group:root].[defined-widget:3]' .
            ' - attributes for compound "my_fancier_route_link" widget of library "app" route would get [text<app.your_invalid_rou...>], expects [exotic<route_name>]. :: ' .

            'ACT node [app].[page-view].[view:my_list_view].[widget-group:root].[defined-widget:5]' .
            ' - attributes for compound "my_fancier_route_link" widget of library "app" arguments would get [structure<{not_a_valid_parameter: number}><{not_a_valid_parameter:1001}>], expects [exotic<route_arguments>]. :: ' .

            'ACT node [app].[page-view].[view:my_item_view].[text-widget].[concatenation].[list].[expression-list].[route-argument]' .
            ' - Current view routes do not all define parameter "my_item_slug" or do not define it identically. :: ' .

            // Providing invalid route name statically
            'ACT node [app].[page-view].[view:my_url_generation_test_view].[widget-group:root].[text-widget].[concatenation].[list].[expression-list].[route-url].[text]' .
            ' - Route "invalid_route" of library "invalid_lib" does not exist. :: ' .
            'ACT node [app].[page-view].[view:my_url_generation_test_view].[widget-group:root].[text-widget].[concatenation].[list].[expression-list].[route-url]' .
            ' - route name expression would get [text<invalid_lib.invalid_...>], expects [exotic<route_name>]. :: ' .

            // Providing an invalid non-fully-qualified route name statically
            'ACT node [app].[page-view].[view:my_url_generation_test_view].[widget-group:root].[text-widget].[concatenation].[list].[expression-list].[route-url]' .
            ' - route name expression would get [text<no_lib_given>], expects [exotic<route_name>]. :: ' .

            // Providing invalid non-exotic and non-valued route
            'ACT node [app].[page-view].[view:my_url_generation_test_view].[widget-group:root].[text-widget].[concatenation].[list].[expression-list].[route-url]' .
            ' - route name expression would get [text], expects [exotic<route_name>]. :: ' .

            // Providing invalid exotic type
            'ACT node [app].[page-view].[view:my_url_generation_test_view].[widget-group:root].[text-widget].[concatenation].[list].[expression-list].[route-url]' .
            ' - route name expression would get [exotic<routing_test_custom_exotic>], expects [exotic<route_name>]. :: ' .

            // Test that route arguments are validated (slug must be text, but number is given)
            'ACT node [app].[page-view].[view:my_url_generation_test_view].[widget-group:root].[text-widget].[concatenation].[list].[expression-list].[route-url]' .
            ' - route arguments expression would get [structure<{my_item_slug: number}><{my_item_slug:21}>], expects [exotic<route_arguments>]. :: ' .

            // Test that route name is validated for the "navigate" instruction
            'ACT node [app].[page-view].[view:my_navigation_test_view].[widget-group:root].[defined-widget:0].[act-node].[trigger:gui.click].[navigate].[text]' .
            ' - Route "invalid_route" of library "invalid_lib" does not exist. :: ' .
            'ACT node [app].[page-view].[view:my_navigation_test_view].[widget-group:root].[defined-widget:0].[act-node].[trigger:gui.click].[navigate]' .
            ' - route name expression would get [text<invalid_lib.invalid_...>], expects [exotic<route_name>]. :: ' .

            // Test that route arguments are validated for the "navigate" instruction
            'ACT node [app].[page-view].[view:my_navigation_test_view].[widget-group:root].[defined-widget:0].[act-node].[trigger:gui.click].[navigate]' .
            ' - route arguments expression would get [structure<{my_item_slug: number}><{my_item_slug:21}>], expects [exotic<route_arguments>]. :: ' .

            'ACT node [app].[route:my_invalid_route]' .
            ' - Some URL parameter placeholder(s) are missing definitions: "my_slug_with_missing_type". :: ' .

            'ACT node [app].[route:my_invalid_route].[fixed-static-bag-model].[fixed-static-definition:my_slug_with_invalid_type]' .
            ' - Expected type not to be unresolved, but it was: unknown<No loader is registered for types of type "myinvalidtype">'
        );

        $this->combyna->createApp($this->appConfig, $this->environment);
    }
}
