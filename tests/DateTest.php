<?php
namespace Webapix\DotNetJsonDate\Tests;

use DateTime;
use DateTimeZone;
use PHPUnit\Framework\TestCase;
use Webapix\DotNetJsonDate\Date;
use Webapix\DotNetJsonDate\InvalidJsonDateString;

class DateTest extends TestCase
{
    /** @test */
    public function it_can_parse_the_dot_net_json_date_format_with_timezone()
    {
        $dotNetJsonDate = '/Date(1593432000000+0200)/';

        $dateTime = Date::toDateTime($dotNetJsonDate);

        $this->assertInstanceOf(DateTime::class, $dateTime);
        $this->assertEquals('2020-06-29 14:00:00', $dateTime->format('Y-m-d H:i:s'));
    }

    /** @test */
    public function it_can_parse_the_dot_net_json_date_format_with_negative_timestamp()
    {
        $dotNetJsonDate = '/Date(-157939200000+0000)/';

        $dateTime = Date::toDateTime($dotNetJsonDate);

        $this->assertInstanceOf(DateTime::class, $dateTime);
        $this->assertEquals('1964-12-30 00:00:00', $dateTime->format('Y-m-d H:i:s'));
    }

    /** @test */
    public function it_can_parse_the_dot_net_json_date_format_without_timezone()
    {
        $dotNetJsonDate = '/Date(501033600000)/';

        $dateTime = Date::toDateTime($dotNetJsonDate);

        $this->assertInstanceOf(DateTime::class, $dateTime);
        $this->assertEquals('1985-11-17 00:00:00', $dateTime->format('Y-m-d H:i:s'));
    }

    /** @test */
    public function it_can_parse_the_dot_net_json_date_format_with_milliseconds()
    {
        $dotNetJsonDate = '/Date(501033600022)/';

        $dateTime = Date::toDateTime($dotNetJsonDate);

        $this->assertInstanceOf(DateTime::class, $dateTime);
        $this->assertEquals('1985-11-17 00:00:00.022000', $dateTime->format('Y-m-d H:i:s.u'));
    }

    /** @test */
    public function it_can_convert_the_datetime_object_to_dot_net_json_date_format()
    {
        $dateTime = DateTime::createFromFormat(
            'Y-m-d H:i:s.u', '2020-06-29 12:00:00.022000',
            new DateTimeZone('+0000')
        );

        $this->assertEquals('/Date(1593432000022+0000)/', Date::toJsonDate($dateTime));
    }

    /** @test */
    public function it_can_convert_the_datetime_object_with_timezone_to_dot_net_json_date_format()
    {
        $dateTime = DateTime::createFromFormat(
            'Y-m-d H:i:s',
            '2020-06-29 12:00:00',
            new DateTimeZone('+0200')
        );

        $this->assertEquals('/Date(1593424800000+0200)/', Date::toJsonDate($dateTime));
    }

    /** @test */
    public function it_can_convert_dates_before_year_1970_to_dot_net_json_date_format()
    {
        $dateTime = DateTime::createFromFormat(
            'Y-m-d H:i:s',
            '1964-12-30 00:00:00',
            new DateTimeZone('+0000')
        );

        $this->assertEquals('/Date(-157939200000+0000)/', Date::toJsonDate($dateTime));
    }

    /** @test */
    public function it_throw_an_exception_if_json_date_format_is_invalid()
    {
        $this->expectException(InvalidJsonDateString::class);

        $invalidJsonDate = '/IvalidDate/';

        Date::toDateTime($invalidJsonDate);
    }
}
