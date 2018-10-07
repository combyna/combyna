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
 * Class WidgetHasValueQuery
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetHasValueQuery implements BooleanQueryInterface
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
    public function getDefaultResult()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Whether the widget has a value called "' . $this->valueName . '"';
    }

    /**
     * Fetches the name of the value to query the existence of
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
