<?php

namespace Webapix\DotNetJsonDate;

use DateTime;
use DateTimeZone;
use Exception;

class Date
{
    /**
     * @param DateTime $dateTime
     * @return string
     */
    public static function toJsonDate(DateTime $dateTime)
    {
        return sprintf(
            '/Date(%d%03d%s)/',
            $dateTime->getTimestamp(),
            round($dateTime->format('u') / 1000),
            $dateTime->format('O')
        );
    }

    /**
     * @param $date
     * @return DateTime
     * @throws InvalidJsonDateString
     */
    public static function toDateTime($date)
    {
        preg_match('/Date\(([-]?\d+)([+,-]?\d*)\)/', $date, $matches);

        if (count($matches) < 2) {
            throw new InvalidJsonDateString('Invalid date format!');
        }

        $dateTime = DateTime::createFromFormat(
            'U.u',
            sprintf("%d.%06d", $matches[1] / 1000, ($matches[1] % 1000) * 1000)
        );

        if (! $dateTime) {
            throw new InvalidJsonDateString('Invalid date format!');
        }

        if (empty($matches[2])) {
            return $dateTime;
        }

        try {
            return $dateTime->setTimezone(new DateTimeZone($matches[2]));
        } catch (Exception $exception) {
            throw new InvalidJsonDateString($exception->getMessage());
        }
    }
}
