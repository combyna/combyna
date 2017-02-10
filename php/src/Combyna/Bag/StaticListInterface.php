<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Bag;

use Combyna\Expression\StaticInterface;
use Combyna\Expression\Validation\ValidationContextInterface;
use Combyna\Type\TypeInterface;
use Countable;

/**
 * Interface StaticListInterface
 *
 * Contains an ordered collection of static values
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface StaticListInterface extends Countable
{
    /**
     * Concatenates all elements of the list together
     *
     * @return string
     */
    public function concatenate();

    /**
     * Returns true if all the elements of this list match the provided type, false otherwise
     *
     * @param TypeInterface $type
     * @return bool
     */
    public function elementsMatch(TypeInterface $type);

    /**
     * Fetches a static from the specified index in this list
     *
     * @param int $index
     * @return StaticInterface
     */
    public function getElementStatic($index);

    /**
     * Fetches the type of the elements in this list
     *
     * @param ValidationContextInterface $validationContext
     * @return TypeInterface
     */
    public function getElementType(ValidationContextInterface $validationContext);

    /**
     * Assigns a new value for a static in this list
     *
     * @param int $index
     * @param StaticInterface $value
     */
    public function setElementStatic($index, StaticInterface $value);

    /**
     * Builds a native array with all native values of statics in this list
     *
     * @return array
     */
    public function toArray();
}
