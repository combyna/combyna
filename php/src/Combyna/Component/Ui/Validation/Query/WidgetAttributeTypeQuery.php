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
use Combyna\Component\Validator\Query\ResultTypeQueryInterface;

/**
 * Class WidgetAttributeTypeQuery
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetAttributeTypeQuery implements ResultTypeQueryInterface
{
    /**
     * @var string
     */
    private $attributeName;

    /**
     * @param string $attributeName
     */
    public function __construct($attributeName)
    {
        $this->attributeName = $attributeName;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return sprintf(
            'The type of the widget attribute "%s"',
            $this->attributeName
        );
    }

    /**
     * Fetches the name of the attribute to fetch the type of
     *
     * @return string
     */
    public function getAttributeName()
    {
        return $this->attributeName;
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return false;
    }
}
