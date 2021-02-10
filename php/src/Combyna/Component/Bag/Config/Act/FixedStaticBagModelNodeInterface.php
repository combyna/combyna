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
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Validator\Query\Requirement\QueryRequirementInterface;

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
     * Fetches the definition for a static in bags of this model
     *
     * @param string $definitionName
     * @param QueryRequirementInterface $queryRequirement
     * @return FixedStaticDefinitionNodeInterface
     */
    public function getStaticDefinitionByName($definitionName, QueryRequirementInterface $queryRequirement);

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
