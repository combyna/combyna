<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment\Library;

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Type\TypeInterface;

/**
 * Interface FunctionInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface FunctionInterface
{
    /**
     * Calls the function, passing it its arguments evaluated to static values,
     * and returns the static value it evaluates to. The return type must be passed in
     * as it can be different depending on the types of the arguments provided
     * (eg. if a custom TypeDeterminer is used for a parameter's type)
     *
     * @param StaticBagInterface $argumentStaticBag
     * @param TypeInterface $returnType
     * @return StaticInterface
     */
    public function call(StaticBagInterface $argumentStaticBag, TypeInterface $returnType);

    /**
     * Fetches the name of this function, which must be unique within its library
     *
     * @return string
     */
    public function getName();
}
