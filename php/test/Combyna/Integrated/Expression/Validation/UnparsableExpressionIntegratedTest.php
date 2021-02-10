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
use Concise\Core\TestCase;

/**
 * Class UnparsableExpressionIntegratedTest
 *
 * Tests the validation handling when an expression cannot be parsed
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UnparsableExpressionIntegratedTest extends TestCase
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
        $container = $combynaBootstrap->createContainer();

        $this->combyna = $container->get('combyna');
        $this->htmlRenderer = $container->get('combyna.renderer.html');
        $yamlParser = $container->get('combyna.config.yaml_parser');

        $this->appConfig = $yamlParser->parse(file_get_contents(__DIR__ . '/../Fixtures/unparsableExpressionValidationTest.cyn.yml'));

        $this->environment = $this->combyna->createEnvironment();
    }

    public function testExpressionsAreValidated()
    {
        $this->setExpectedException(
            ValidationFailureException::class,

            // Check that the result type for an unparsable expression is unknown
            'ACT node [app].[page-view].[view:my_view].[widget-group:root].[text-widget]' .
            ' - text would get [unknown<Unparsable expression: this @ is most definitely [ an invalid } expression #>], expects [text]. :: ' .

            'ACT node [app].[page-view].[view:my_view].[widget-group:root].[text-widget].[unparsable]' .
            ' - Unparsable expression: this @ is most definitely [ an invalid } expression #. :: ' .

            // Test that the expression must begin with an equals sign, making it a formula
            'ACT node [app].[page-view].[view:my_view].[widget-group:root].[text-widget]' .
            ' - text would get [unknown<Unparsable expression: I am missing the leading formula equals sign>], expects [text]. :: ' .
            'ACT node [app].[page-view].[view:my_view].[widget-group:root].[text-widget].[unparsable]' .
            ' - Unparsable expression: I am missing the leading formula equals sign. :: ' .

            // Test that a literal boolean may not be given
            // TODO: Consider allowing literal booleans?
            'ACT node [app].[page-view].[view:my_view].[widget-group:root].[invalid-core-widget:2]' .
            ' - Core "text" widget "2" is invalid: Wrong type of value given for argument "text": expected an expression for text expression, got boolean(false). :: ' .

            // Test that a literal number may not be given
            // TODO: Consider allowing literal numbers?
            'ACT node [app].[page-view].[view:my_view].[widget-group:root].[invalid-core-widget:3]' .
            ' - Core "text" widget "3" is invalid: Wrong type of value given for argument "text": expected an expression for text expression, got integer(21). :: ' .

            # Test that a bare string without equals sign may not be given
            # TODO: Similar to the above for literal numbers, consider allowing this?
            #       May lead to confusion however if an intended formula/equals sign is forgotten
            'ACT node [app].[page-view].[view:my_view].[widget-group:root].[text-widget]' .
            ' - text would get [unknown<Unparsable expression: iaminvalid>], expects [text]. :: ' .
            'ACT node [app].[page-view].[view:my_view].[widget-group:root].[text-widget].[unparsable]' .
            ' - Unparsable expression: iaminvalid'
        );

        $this->combyna->createApp($this->appConfig, $this->environment);
    }
}
