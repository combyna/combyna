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

use Combyna\Component\Validator\Config\Act\DynamicActNodeAdopterInterface;

/**
 * Interface DeterminedFixedStaticBagModelInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface DeterminedFixedStaticBagModelInterface
{
    /**
     * Determines whether the required static definitions of this fixed static bag model
     * all exist in the given other model, and that there are no extras provided.
     *
     * If there are any optional static definitions in this model, the other model given
     * does not need to specify them as they can use their default values.
     *
     * @param DeterminedFixedStaticBagModelInterface $otherModel
     * @return bool
     */
    public function allowsOtherModel(DeterminedFixedStaticBagModelInterface $otherModel);

    /**
     * Returns true if this model defines a static with the specified name, false otherwise
     *
     * @param string $name
     * @return bool
     */
    public function definesStatic($name);

    /**
     * Fetches a static definition from this model by its name
     *
     * @param string $definitionName
     * @param DynamicActNodeAdopterInterface $dynamicActNodeAdopter
     * @return DeterminedFixedStaticDefinitionInterface
     */
    public function getStaticDefinitionByName($definitionName, DynamicActNodeAdopterInterface $dynamicActNodeAdopter);

    /**
     * Fetches the names of the statics in bags of this model
     *
     * @return string[]
     */
    public function getStaticDefinitionNames();

    /**
     * {@inheritdoc}
     *
     * @return DeterminedFixedStaticDefinitionInterface[]
     */
    public function getStaticDefinitions();

    /**
     * Returns a summary of the static definitions represented
     * eg. `structure{name: text, age: number}`
     *
     * @return string
     */
    public function getSummary();
}
