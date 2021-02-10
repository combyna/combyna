<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Plugin\Core\Library\List_;

use Combyna\Component\Bag\BagFactoryInterface;
use Combyna\Component\Environment\Library\NativeFunctionLocator;
use Combyna\Component\Environment\Library\NativeFunctionProviderInterface;
use Combyna\Component\Expression\StaticExpressionFactoryInterface;
use Combyna\Component\Expression\StaticListExpression;

/**
 * Class Functions
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Functions implements NativeFunctionProviderInterface
{
    /**
     * @var BagFactoryInterface
     */
    private $bagFactory;

    /**
     * @var StaticExpressionFactoryInterface
     */
    private $staticExpressionFactory;

    /**
     * @param StaticExpressionFactoryInterface $staticExpressionFactory
     * @param BagFactoryInterface $bagFactory
     */
    public function __construct(
        StaticExpressionFactoryInterface $staticExpressionFactory,
        BagFactoryInterface $bagFactory
    ) {
        $this->bagFactory = $bagFactory;
        $this->staticExpressionFactory = $staticExpressionFactory;
    }

    /**
     * Merges all the elements from every list provided into one final list
     *
     * @param StaticListExpression $listsToConcatenate
     * @return StaticListExpression
     */
    public function concat(StaticListExpression $listsToConcatenate)
    {
        $mergedElements = [];

        foreach ($listsToConcatenate->getElementStatics() as $list) {
            /** @var StaticListExpression $list */
            $mergedElements = array_merge($mergedElements, $list->getElementStatics());
        }

        return $this->staticExpressionFactory->createStaticListExpression(
            $this->bagFactory->createStaticList($mergedElements)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getNativeFunctionLocators()
    {
        return [
            new NativeFunctionLocator(
                'list',
                'concat',
                [$this, 'concat'],
                ['lists']
            )
        ];
    }
}
