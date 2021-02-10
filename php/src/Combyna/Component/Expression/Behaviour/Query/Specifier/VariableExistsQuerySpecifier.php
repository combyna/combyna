<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Behaviour\Query\Specifier;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;

/**
 * Class VariableExistsQuerySpecifier
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class VariableExistsQuerySpecifier implements QuerySpecifierInterface
{
    /**
     * @var string
     */
    private $variableName;

    /**
     * @param string $variableName
     */
    public function __construct($variableName)
    {
        $this->variableName = $variableName;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'A query for whether the contextual variable "' . $this->variableName . '" exists';
    }

    /**
     * Fetches the name of the variable to query the existence of
     *
     * @return string
     */
    public function getVariableName()
    {
        return $this->variableName;
    }
}
