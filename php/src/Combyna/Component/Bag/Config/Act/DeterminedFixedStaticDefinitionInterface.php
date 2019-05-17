<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Bag\Config\Act;

use Combyna\Component\Type\TypeInterface;

/**
 * Interface DeterminedFixedStaticDefinitionInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface DeterminedFixedStaticDefinitionInterface
{
    /**
     * Determines whether this definition would allow an expression that was valid for the given other definition
     *
     * @param DeterminedFixedStaticDefinitionInterface $otherDefinition
     * @return bool
     */
    public function allowsStaticDefinition(DeterminedFixedStaticDefinitionInterface $otherDefinition);

    /**
     * Fetches the name of the definition
     *
     * @return string
     */
    public function getName();

    /**
     * Fetches the type of the static
     *
     * @return TypeInterface
     */
    public function getStaticType();

    /**
     * Fetches the summary of the static type for this definition
     *
     * @return string
     */
    public function getStaticTypeSummary();

    /**
     * Determines whether this static must be defined in the bag or not
     *
     * @return bool
     */
    public function isRequired();
}
