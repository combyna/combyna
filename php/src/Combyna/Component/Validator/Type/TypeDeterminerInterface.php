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

use Combyna\Component\Behaviour\Node\StructuredNodeInterface;
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
     * Fetches any structured child nodes of this type
     * (eg. for a "structure" type determiner, this returns the fixed static bag model node,
     *      but note that "structure" here is not directly related to the "structure" of the ACT)
     *
     * @return StructuredNodeInterface[]
     */
    public function getStructuredChildNodes();

    /**
     * Fetches a short summary for the type determiner
     *
     * @return string
     */
    public function getSummary();

    /**
     * Determines whether this determiner makes the specified query
     *
     * @param QuerySpecifierInterface $querySpecifier
     * @return bool
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier);
}
