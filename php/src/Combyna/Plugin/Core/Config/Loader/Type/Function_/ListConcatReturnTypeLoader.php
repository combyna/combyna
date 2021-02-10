<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Plugin\Core\Config\Loader\Type\Function_;

use Combyna\Component\Type\Config\Loader\TypeTypeLoaderInterface;
use Combyna\Plugin\Core\Type\Function_\ListConcatReturnTypeDeterminer;

/**
 * Class ListConcatReturnTypeLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ListConcatReturnTypeLoader implements TypeTypeLoaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config)
    {
        return new ListConcatReturnTypeDeterminer();
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes()
    {
        return ['list.concat'];
    }
}
