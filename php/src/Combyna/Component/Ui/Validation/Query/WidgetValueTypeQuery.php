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
 * Class WidgetValueTypeQuery
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetValueTypeQuery implements ResultTypeQueryInterface
{
    /**
     * @var string
     */
    private $valueName;

    /**
     * @param string $valueName
     */
    public function __construct($valueName)
    {
        $this->valueName = $valueName;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return sprintf(
            'The type of the widget value "%s"',
            $this->valueName
        );
    }

    /**
     * Fetches the name of the value to fetch the type of
     *
     * @return string
     */
    public function getValueName()
    {
        return $this->valueName;
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return false;
    }
}
