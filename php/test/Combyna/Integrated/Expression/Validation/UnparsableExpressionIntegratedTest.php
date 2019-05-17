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
use Symfony\Component\DependencyInjection\ContainerInterface;

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

        $this->appConfig = $yamlParser->parse(file_get_contents(__DIR__ . '/../Fixtures/unparsableExpressionValidationTest.cyn.yml'));

        $this->environment = $this->combyna->createEnvironment();
    }

    public function testExpressionsAreValidated()
    {
        $this->setExpectedException(
            ValidationFailureException::class,

            # Check that the result type for an unparsable expression is unknown
            'ACT node [app].[page-view].[view:my_view].[widget-group:root].[text-widget]' .
            ' - text would get [unknown<Unparsable expression: this @ is most definitely [ an invalid } expression #>], expects [text]. :: ' .

            'ACT node [app].[page-view].[view:my_view].[widget-group:root].[text-widget].[unparsable]' .
            ' - Unparsable expression: this @ is most definitely [ an invalid } expression #'
        );

        $this->combyna->createApp($this->appConfig, $this->environment);
    }
}
