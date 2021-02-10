<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Validation\Query;

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Validator\Query\BooleanQueryInterface;

/**
 * Class PageViewExistsQuery
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class PageViewExistsQuery implements BooleanQueryInterface
{
    /**
     * @var string
     */
    private $pageViewName;

    /**
     * @param string $pageViewName
     */
    public function __construct($pageViewName)
    {
        $this->pageViewName = $pageViewName;
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
        return 'Whether the page view "' . $this->pageViewName . '" exists';
    }

    /**
     * Fetches the name of the page view to query the existence of
     *
     * @return string
     */
    public function getPageViewName()
    {
        return $this->pageViewName;
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return false;
    }
}
