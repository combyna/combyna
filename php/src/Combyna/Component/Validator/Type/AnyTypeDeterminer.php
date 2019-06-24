<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator\Type;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Type\AnyType;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Class AnyTypeDeterminer
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AnyTypeDeterminer extends AbstractTypeDeterminer
{
    /**
     * {@inheritdoc}
     */
    public function determine(ValidationContextInterface $validationContext)
    {
        return new AnyType($validationContext);
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return false;
    }
}
