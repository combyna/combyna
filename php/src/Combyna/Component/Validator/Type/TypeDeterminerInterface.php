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
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Interface TypeDeterminerInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface TypeDeterminerInterface
{
    /**
     * Determines the actual type
     *
     * @param ValidationContextInterface $validationContext
     * @return TypeInterface
     */
    public function determine(ValidationContextInterface $validationContext);

    /**
     * Determines whether this determiner makes the specified query
     *
     * @param QuerySpecifierInterface $querySpecifier
     * @return bool
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier);
}
