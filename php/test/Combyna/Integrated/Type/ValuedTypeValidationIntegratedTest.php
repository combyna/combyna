<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Integrated\Type;

use Combyna\Component\Config\YamlParser;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Framework\Combyna;
use Combyna\Component\Renderer\Html\HtmlRenderer;
use Combyna\Component\Validator\Exception\ValidationFailureException;
use Combyna\Harness\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ValuedTypeValidationIntegratedTest
 *
 * Tests the validation for the "valued" type feature
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ValuedTypeValidationIntegratedTest extends TestCase
{
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
     * @var YamlParser
     */
    private $yamlParser;

    public function setUp()
    {
        global $combynaBootstrap;
        $this->container = $combynaBootstrap->createContainer();

        $this->combyna = $this->container->get('combyna');
        $this->htmlRenderer = $this->container->get('combyna.renderer.html');
        $this->yamlParser = $this->container->get('combyna.config.yaml_parser');

        $this->environment = $this->combyna->createEnvironment();
    }

    public function testValuedTypesCanBeUsedToSpecifyAWhitelistOfPossibleValuesForStaticAnalysis()
    {
        $appConfig = $this->yamlParser->parse(file_get_contents(__DIR__ . '/Fixtures/valuedTypeValidationTest.cyn.yml'));

        $this->expectException(ValidationFailureException::class);
        $this->expectExceptionMessage(
            // Test that a valued type's value expression must be valid
            'ACT node [app].[page-view].[view:my_view].[widget-group:root].[fixed-static-bag-model].[fixed-static-definition:a_single_valued_type_with_invalid_value_expression]' .
            ' - Expected type not to be unresolved, but it was: unknown<Impure value expression given for valued type>. :: ' .
            'ACT node [app].[page-view].[view:my_view].[widget-group:root].[fixed-static-bag-model].[fixed-static-definition:a_single_valued_type_with_invalid_value_expression].[type:valued].[unparsable]' .
            ' - Unparsable expression: I am definitely @ not (!) a valid expression. :: ' .

            // Test that a valued type's value must match exactly as well as its wrapped type
            'ACT node [app].[page-view].[view:my_view].[widget-group:root].[text-widget]' .
            ' - Capture "a_single_valued_type_that_matches_type_but_not_value" would get [text<I do not match the e...>], expects [text<I must be given exac...>]. :: ' .

            // Test that a valued type's wrapped type must match
            'ACT node [app].[page-view].[view:my_view].[widget-group:root].[text-widget]' .
            ' - Capture "a_single_valued_type_that_matches_neither_type_nor_value" would get [number<21>], expects [text<I must be given exac...>]. :: ' .

            // Test that a valued type's wrapped value resolves to the correct type
            // (TODO: this is only an indirect check - see the comment in ValuedType's ctor)
            'ACT node [app].[page-view].[view:my_view].[widget-group:root].[text-widget]' .
            ' - Capture "a_single_valued_type_whose_value_doesnt_match_wrapped_type" would get [text<I am not a valid val...>], expects [number<I am not a valid val...>]. :: ' .

            // (Continued) test that a valued type's value expression must be valid
            'ACT node [app].[page-view].[view:my_view].[widget-group:root].[text-widget]' .
            ' - Capture "a_single_valued_type_with_invalid_value_expression" would get [number], expects [unknown<Impure value expression given for valued type>]. :: ' .

            // Test that multiple types may provide a list of possible scalar-valued types
            'ACT node [app].[page-view].[view:my_view].[widget-group:root].[text-widget]' .
            ' - Capture "a_multiple_scalar_valued_type_that_matches_type_but_not_value" would get [text<Not an option>], expects [text<First option>|text<Second option>]. :: ' .

            // Test that multiple types may provide a list of possible structure-valued types
            'ACT node [app].[page-view].[view:my_view].[widget-group:root].[text-widget]' .
            ' - Capture "a_multiple_structure_valued_type_that_matches_type_but_not_value" would get [structure<{my_attr: text}><{my_attr:Not an option}>], expects [structure<{my_attr: text}><{my_attr:First option}>|structure<{my_attr: text}><{my_attr:Second option}>]. :: ' .

            // Test that multiple types may provide a list of possible list-valued types
            'ACT node [app].[page-view].[view:my_view].[widget-group:root].[text-widget]' .
            ' - Capture "a_multiple_list_valued_type_that_matches_type_but_not_value" would get [list<text|number><[Not an option,9009]>], expects [list<text|number><[First option, part 1,3003]>|list<text|number><[Second option, part ...,4004]>]'
        );

        $this->combyna->createApp($appConfig, $this->environment);
    }
}
