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
 * Class StaticDateTimeExpression
 *
 * Evaluates to a date and a specific time of day
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StaticDateTimeExpression extends AbstractStaticExpression
{
    const TYPE = 'static-date-time';

    /**
     * @var int
     */
    private $day;

    /**
     * @var int
     */
    private $hour;

    /**
     * @var int
     */
    private $millisecond;

    /**
     * @var int
     */
    private $minute;

    /**
     * @var int
     */
    private $month;

    /**
     * @var int
     */
    private $second;

    /**
     * @var int
     */
    private $year;

    /**
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @param int $minute
     * @param int $second
     * @param int $millisecond
     */
    public function __construct($year, $month, $day, $hour, $minute, $second, $millisecond)
    {
        $this->day = $day;
        $this->hour = $hour;
        $this->millisecond = $millisecond;
        $this->minute = $minute;
        $this->month = $month;
        $this->second = $second;
        $this->year = $year;
    }

    /**
     * {@inheritdoc}
     */
    public function toNative()
    {
        return DateTime::createFromFormat(
            'yyyy-mm-dd-h-i-s',
            implode('-', [
                $this->year,
                $this->month,
                $this->day,
                $this->hour,
                $this->minute,
                $this->second + ($this->millisecond / 1000)
            ])
        );
    }
}
