<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression;

use DateTime;

/**
 * Class StaticDayExpression
 *
 * Represents a single day
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StaticDayExpression extends AbstractStaticExpression
{
    const TYPE = 'static-day';

    /**
     * @var int
     */
    private $day;

    /**
     * @var int
     */
    private $month;

    /**
     * @var int
     */
    private $year;

    /**
     * @param int $year
     * @param int $month
     * @param int $day
     */
    public function __construct($year, $month, $day)
    {
        $this->day = $day;
        $this->month = $month;
        $this->year = $year;
    }

    /**
     * {@inheritdoc}
     */
    public function toNative()
    {
        return DateTime::createFromFormat('yyyy-mm-dd', implode('-', [$this->year, $this->month, $this->day]));
    }
}
