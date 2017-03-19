<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\ExpressionLanguage;

use Combyna\Component\Expression\TextExpression;
use Combyna\Component\Expression\ExpressionFactoryInterface;
use Combyna\Harness\TestCase;
use Combyna\Component\Expression\Config\Loader\TextExpressionLoader;
use Combyna\Component\Config\Loader\ConfigParser;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class TextExpressionLoaderTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TextExpressionLoaderTest extends TestCase
{
    /**
     * @var ObjectProphecy|ConfigParser
     */
    private $configParser;

    /**
     * @var ObjectProphecy|ExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @var TextExpressionLoader
     */
    private $loader;

    public function setUp()
    {
        $this->configParser = $this->prophesize(ConfigParser::class);
        $this->expressionFactory = $this->prophesize(ExpressionFactoryInterface::class);

        $this->expressionFactory->createTextExpression(Argument::any())
            ->will($this->noBind(function (array $args) {
                /** @var ObjectProphecy|TextExpression $textExpression */
                $textExpression = $this->prophesize(TextExpression::class);
                $textExpression->toNative()->willReturn($args[0]);

                return $textExpression;
            }));

        $this->loader = new TextExpressionLoader(
            $this->configParser->reveal(),
            $this->expressionFactory->reveal()
        );
    }

    /**
     * @dataProvider textProvider
     * @param string $text
     */
    public function testLoadReturnsATextExpressionWithTheCorrectNativeValue($text)
    {
        $config = [
            'type' => 'text',
            'text' => $text
        ];
        $this->configParser->getElement($config, 'text', Argument::any())
            ->willReturn($text);

        $textExpression = $this->loader->load($config);

        $this->assert($textExpression)->isAnInstanceOf(TextExpression::class);
        $this->assert($textExpression->toNative())->exactlyEquals($text);
    }

    /**
     * @return array
     */
    public function textProvider()
    {
        return [
            ['hello'],
            ['world']
        ];
    }

    public function testGetTypeReturnsTheCorrectType()
    {
        $this->assert($this->loader->getType())->exactlyEquals('text');
    }
}
