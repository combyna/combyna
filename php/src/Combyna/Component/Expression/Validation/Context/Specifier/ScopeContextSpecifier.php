<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Validation\Context\Specifier;

use Combyna\Component\Validator\Context\Specifier\SubValidationContextSpecifierInterface;
use Combyna\Component\Validator\Type\TypeDeterminerInterface;
use InvalidArgumentException;

/**
 * Class ScopeContextSpecifier
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ScopeContextSpecifier implements SubValidationContextSpecifierInterface
{
    /**
     * @var TypeDeterminerInterface[]
     */
    private $variableTypeDeterminers = [];

    /**
     * Defines a new variable in the scope
     *
     * @param string $name
     * @param TypeDeterminerInterface $typeDeterminer
     */
    public function defineVariable($name, TypeDeterminerInterface $typeDeterminer)
    {
        if (array_key_exists($name, $this->variableTypeDeterminers)) {
            throw new InvalidArgumentException('Scope already has a variable "' . $name . '"');
        }

        $this->variableTypeDeterminers[$name] = $typeDeterminer;
    }

    /**
     * Fetches all variables defined for this scope, along with their type determiners
     *
     * @return TypeDeterminerInterface[]
     */
    public function getVariableTypeDeterminers()
    {
        return $this->variableTypeDeterminers;
    }
}
