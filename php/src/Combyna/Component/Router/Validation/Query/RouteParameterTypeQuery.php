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
use Combyna\Component\Validator\Query\ResultTypeQueryInterface;

/**
 * Class RouteParameterTypeQuery
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RouteParameterTypeQuery implements ResultTypeQueryInterface
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
    public function getDescription()
    {
        return sprintf(
            'The type of the route parameter "%s" used by the routes for the view "%s"',
            $this->parameterName,
            $this->viewName
        );
    }

    /**
     * Fetches the name of the parameter to query the type of
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
