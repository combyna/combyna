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
use Combyna\Component\Expression\Behaviour\Query\Specifier\VariableExistsQuerySpecifier;
use Combyna\Component\Validator\Query\BooleanQueryInterface;

/**
 * Class VariableExistsQuery
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class VariableExistsQuery implements BooleanQueryInterface
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
    public function getDefaultResult()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Whether the contextual variable "' . $this->variableName . '" exists';
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

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return $querySpecifier instanceof VariableExistsQuerySpecifier &&
            $querySpecifier->getVariableName() === $this->variableName;
    }
}
