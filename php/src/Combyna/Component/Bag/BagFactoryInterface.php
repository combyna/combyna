<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Bag;

use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Type\TypeInterface;

/**
 * Interface BagFactoryInterface
 *
 * Creates objects related to bags and lists
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface BagFactoryInterface
{
    /**
     * Coerces an associative array of native values and their names to a StaticBag
     *
     * @param array $natives
     * @return StaticBagInterface
     */
    public function coerceStaticBag(array $natives);

    /**
     * Creates an ExpressionBag
     *
     * @param ExpressionInterface[] $expressions
     * @return ExpressionBagInterface
     */
    public function createExpressionBag(array $expressions);

    /**
     * Creates an ExpressionList
     *
     * @param ExpressionInterface[] $expressions
     * @return ExpressionListInterface
     */
    public function createExpressionList(array $expressions);

    /**
     * Creates a FixedStaticDefinition
     *
     * @param string $name
     * @param TypeInterface $type
     * @param ExpressionInterface|null $defaultExpression
     * @return FixedStaticDefinition
     */
    public function createFixedStaticDefinition(
        $name,
        TypeInterface $type,
        ExpressionInterface $defaultExpression = null
    );

    /**
     * Creates a FixedStaticBagModel
     *
     * @param FixedStaticDefinition[] $staticDefinitions
     * @return FixedStaticBagModelInterface
     */
    public function createFixedStaticBagModel(array $staticDefinitions);

    /**
     * Creates a MutableStaticBag
     *
     * @param StaticInterface[] $statics
     * @return MutableStaticBagInterface
     */
    public function createMutableStaticBag(array $statics = []);

    /**
     * Creates a StaticBag
     *
     * @param StaticInterface[] $statics
     * @return StaticBagInterface
     */
    public function createStaticBag(array $statics);

    /**
     * Creates a StaticList
     *
     * @param StaticInterface[] $statics
     * @return StaticListInterface
     */
    public function createStaticList(array $statics);
}
