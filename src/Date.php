<?php

namespace Webapix\DotNetJsonDate;

use DateTime;
use DateTimeInterface;
use DateTimeZone;

class Date
{
    /**
     * The .Net JSON date format: /Date(<milliseconds>[<+|-><hhmm>])/
     *
     * The offset is bound to what .Net itself can emit: hours 00-14, minutes 00-59.
     * The timestamp is capped at 15 digits, so that it always survives the int cast.
     */
    private const PATTERN = '#^/Date\((-?\d{1,15})([+-](?:0\d|1[0-4])[0-5]\d)?\)/\z#';

    /**
     * How much of the offending input is quoted back in the exception message.
     */
    private const EXCERPT_LENGTH = 50;

    public static function toJsonDate(DateTimeInterface $dateTime): string
    {
        $milliseconds = $dateTime->getTimestamp() * 1000
            + intdiv((int) $dateTime->format('u'), 1000);

        return sprintf('/Date(%d%s)/', $milliseconds, $dateTime->format('O'));
    }

    /**
     * @throws InvalidJsonDateString
     */
    public static function toDateTime(string $date): DateTime
    {
        if (! preg_match(self::PATTERN, $date, $matches)) {
            throw self::invalid($date);
        }

        $milliseconds = (int) $matches[1];
        $seconds = intdiv($milliseconds, 1000);
        $remainder = $milliseconds % 1000;

        // Keep the sub-second part positive, so that pre-epoch dates stay exact.
        if ($remainder < 0) {
            $seconds--;
            $remainder += 1000;
        }

        $dateTime = DateTime::createFromFormat('U.u', sprintf('%d.%06d', $seconds, $remainder * 1000));

        if (! $dateTime) {
            throw self::invalid($date);
        }

        if (! isset($matches[2])) {
            return $dateTime;
        }

        // The pattern already limits the offset to a range DateTimeZone accepts.
        return $dateTime->setTimezone(new DateTimeZone($matches[2]));
    }

    private static function invalid(string $date): InvalidJsonDateString
    {
        $excerpt = preg_replace('/[[:cntrl:]]/', '', substr($date, 0, self::EXCERPT_LENGTH));

        return new InvalidJsonDateString(sprintf('Invalid .Net JSON date string: "%s"!', $excerpt));
    }
}
