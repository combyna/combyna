<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Validation\Constraint;

use Combyna\Component\Validator\Constraint\ConstraintInterface;

/**
 * Class PageViewExistsConstraint
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class PageViewExistsConstraint implements ConstraintInterface
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
     * Fetches the name of the page view being referenced
     *
     * @return string
     */
    public function getPageViewName()
    {
        return $this->pageViewName;
    }
}
