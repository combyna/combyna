<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Validation\Query;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Expression\Behaviour\Query\Specifier\VariableTypeQuerySpecifier;
use Combyna\Component\Validator\Query\ResultTypeQueryInterface;

/**
 * Class VariableTypeQuery
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class VariableTypeQuery implements ResultTypeQueryInterface
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
        return 'The type of the contextual variable "' . $this->variableName . '"';
    }

    /**
     * Fetches the name of the variable to query the type of
     *
     * @return string
     */
    public function getVariableName()
    {
        return $this->variableName;
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return $querySpecifier instanceof VariableTypeQuerySpecifier &&
            $querySpecifier->getVariableName() === $this->variableName;
    }
}
