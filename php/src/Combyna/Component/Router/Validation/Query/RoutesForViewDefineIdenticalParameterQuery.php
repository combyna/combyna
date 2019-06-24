<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Router\Validation\Query;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Validator\Query\BooleanQueryInterface;

/**
 * Class RoutesForViewDefineIdenticalParameterQuery
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RoutesForViewDefineIdenticalParameterQuery implements BooleanQueryInterface
{
    /**
     * @var string
     */
    private $parameterName;

    /**
     * @var string
     */
    private $viewName;

    /**
     * @param string $viewName
     * @param string $parameterName
     */
    public function __construct($viewName, $parameterName)
    {
        $this->parameterName = $parameterName;
        $this->viewName = $viewName;
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
        return sprintf(
            'Whether all routes that use the "%s" view define an identical parameter called "%s"',
            $this->viewName,
            $this->parameterName
        );
    }

    /**
     * Fetches the name of the parameter to query for
     *
     * @return string
     */
    public function getParameterName()
    {
        return $this->parameterName;
    }

    /**
     * Fetches the name of the view to check the routes for
     *
     * @return string
     */
    public function getViewName()
    {
        return $this->viewName;
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return false;
    }
}
