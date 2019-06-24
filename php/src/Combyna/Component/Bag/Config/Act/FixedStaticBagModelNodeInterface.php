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

use Combyna\Component\Config\Act\ActNodeInterface;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Validator\Config\Act\DynamicActNodeAdopterInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;

/**
 * Interface FixedStaticBagModelNodeInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface FixedStaticBagModelNodeInterface extends ActNodeInterface
{
    /**
     * Determines whether this bag model defines a static with the given name
     *
     * @param string $name
     * @return bool
     */
    public function definesStatic($name);

    /**
     * Determines all types for the static definitions in this model,
     * returning a new DeterminedFixedStaticBagModelNode
     *
     * @param ValidationContextInterface $validationContext
     * @return DeterminedFixedStaticBagModelNode
     */
    public function determine(ValidationContextInterface $validationContext);

    /**
     * Fetches the definition for a static in bags of this model
     *
     * @param string $definitionName
     * @param DynamicActNodeAdopterInterface $dynamicActNodeAdopter
     * @return FixedStaticDefinitionNodeInterface
     */
    public function getStaticDefinitionByName($definitionName, DynamicActNodeAdopterInterface $dynamicActNodeAdopter);

    /**
     * Fetches the names of the statics in bags of this model
     *
     * @return string[]
     */
    public function getStaticDefinitionNames();

    /**
     * Fetches the definitions for statics in bags of this model
     *
     * @return FixedStaticDefinitionNodeInterface[]
     */
    public function getStaticDefinitions();

    /**
     * Fetches the type of a definition
     *
     * @param string $definitionName
     * @return TypeInterface
     */
    public function getStaticDefinitionType($definitionName);

    /**
     * Determines whether or not this model is empty (defines no statics)
     *
     * @return bool
     */
    public function isEmpty();

    /**
     * Checks that all expressions in the provided bag evaluate to statics that match
     * the types for their corresponding parameters, and that there are no extra arguments
     * with no matching parameter or required parameters that are missing an argument in the bag
     *
     * @param ValidationContextInterface $validationContext
     * @param ExpressionBagNode $expressionBagNode
     * @param string $contextDescription
     */
    public function validateStaticExpressionBag(
        ValidationContextInterface $validationContext,
        ExpressionBagNode $expressionBagNode,
        $contextDescription
    );
}
