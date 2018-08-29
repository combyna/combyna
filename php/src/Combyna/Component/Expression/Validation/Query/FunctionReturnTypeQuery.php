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
use Combyna\Component\Validator\Query\ResultTypeQueryInterface;

/**
 * Class FunctionReturnTypeQuery
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class FunctionReturnTypeQuery implements ResultTypeQueryInterface
{
    /**
     * @var string
     */
    private $functionName;

    /**
     * @var string
     */
    private $libraryName;

    /**
     * @param string $libraryName
     * @param string $functionName
     */
    public function __construct($libraryName, $functionName)
    {
        $this->functionName = $functionName;
        $this->libraryName = $libraryName;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'The return type of the function "' . $this->libraryName . '.' . $this->functionName . '"';
    }

    /**
     * Fetches the name of the function
     *
     * @return string
     */
    public function getFunctionName()
    {
        return $this->functionName;
    }

    /**
     * Fetches the name of the library that defines the function
     *
     * @return string
     */
    public function getLibraryName()
    {
        return $this->libraryName;
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return false;
    }
}
