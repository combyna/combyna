<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Integrated\Expression\Validation;

use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Framework\Combyna;
use Combyna\Component\Renderer\Html\HtmlRenderer;
use Combyna\Component\Validator\Exception\ValidationFailureException;
use Combyna\Harness\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class StructureExpressionIntegratedTest
 *
 * Tests the validation for "structure" expressions
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StructureExpressionIntegratedTest extends TestCase
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

        $this->appConfig = $yamlParser->parse(file_get_contents(__DIR__ . '/../Fixtures/structureExpressionValidationTest.cyn.yml'));

        $this->environment = $this->combyna->createEnvironment();
    }

    public function testStructureExpressionsAndTypesAreValidated()
    {
        $this->expectException(ValidationFailureException::class);
        $this->expectExceptionMessage(
            'ACT node [app].[page-view].[view:my_view].[widget-group:root].[fixed-static-bag-model].[fixed-static-definition:my_capture].[type:structure].[fixed-static-bag-model].[fixed-static-definition:my_attr_with_invalid_type]' .
            ' - Expected type not to be unresolved, but it was: unknown<No loader is registered for types of type "my_invalid_attr_type">. :: ' .

            'ACT node [app].[page-view].[view:my_view].[widget-group:root].[fixed-static-bag-model].[fixed-static-definition:my_capture].[type:structure].[fixed-static-bag-model].[fixed-static-definition:my_attr_with_invalid_default]' .
            ' - default expression would get [unknown<Unparsable expression: my @ unparsable default expression>], expects [text]. :: ' .

            'ACT node [app].[page-view].[view:my_view].[widget-group:root].[fixed-static-bag-model].[fixed-static-definition:my_capture].[type:structure].[fixed-static-bag-model].[fixed-static-definition:my_attr_with_invalid_default].[unparsable]' .
            ' - Unparsable expression: my @ unparsable default expression. :: ' .

            'ACT node [app].[page-view].[view:my_view].[widget-group:root].[defined-widget:0]' .
            ' - Capture "my_capture" would get [structure<{my_attr_with_invalid_type: text, my_attr_with_invalid_default: number, my_valid_attr: text}>], expects [structure<{my_attr_with_invalid_type: unknown<No loader is registered for types of type "my_invalid_attr_type">, my_attr_with_invalid_default: text, my_valid_attr: number}>]. :: ' .

            'ACT node [app].[page-view].[view:my_view].[widget-group:root].[text-widget].[concatenation]' .
            ' - operand list would get [list<text|unknown<Unknown fixed static "my_undefined_attr">>], expects [list<text|number>]. :: ' .

            'ACT node [app].[page-view].[view:my_view].[widget-group:root].[text-widget].[concatenation].[list].[expression-list].[attribute]' .
            ' - Structure does not define an attribute with name "my_undefined_attr"'
        );

        $this->combyna->createApp($this->appConfig, $this->environment);
    }
}
