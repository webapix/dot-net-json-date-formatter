<?php

namespace Webapix\DotNetJsonDate\Tests;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Webapix\DotNetJsonDate\Date;
use Webapix\DotNetJsonDate\InvalidJsonDateString;

class DateTest extends TestCase
{
    #[Test]
    public function it_can_parse_the_dot_net_json_date_format_with_timezone()
    {
        $dateTime = Date::toDateTime('/Date(1593432000000+0200)/');

        $this->assertInstanceOf(DateTime::class, $dateTime);
        $this->assertEquals('2020-06-29 14:00:00', $dateTime->format('Y-m-d H:i:s'));
    }

    #[Test]
    public function it_can_parse_the_dot_net_json_date_format_with_negative_timestamp()
    {
        $dateTime = Date::toDateTime('/Date(-157939200000+0000)/');

        $this->assertInstanceOf(DateTime::class, $dateTime);
        $this->assertEquals('1964-12-30 00:00:00', $dateTime->format('Y-m-d H:i:s'));
    }

    #[Test]
    public function it_can_parse_the_dot_net_json_date_format_without_timezone()
    {
        $dateTime = Date::toDateTime('/Date(501033600000)/');

        $this->assertInstanceOf(DateTime::class, $dateTime);
        $this->assertEquals('1985-11-17 00:00:00', $dateTime->format('Y-m-d H:i:s'));
    }

    #[Test]
    public function it_can_parse_the_dot_net_json_date_format_with_milliseconds()
    {
        $dateTime = Date::toDateTime('/Date(501033600022)/');

        $this->assertInstanceOf(DateTime::class, $dateTime);
        $this->assertEquals('1985-11-17 00:00:00.022000', $dateTime->format('Y-m-d H:i:s.u'));
    }

    #[Test]
    public function it_can_parse_a_negative_timestamp_with_milliseconds()
    {
        $dateTime = Date::toDateTime('/Date(-157939200022+0000)/');

        $this->assertInstanceOf(DateTime::class, $dateTime);
        $this->assertEquals('1964-12-29 23:59:59.978000', $dateTime->format('Y-m-d H:i:s.u'));
    }

    #[Test]
    public function it_can_convert_the_datetime_object_to_dot_net_json_date_format()
    {
        $dateTime = DateTime::createFromFormat(
            'Y-m-d H:i:s.u',
            '2020-06-29 12:00:00.022000',
            new DateTimeZone('+0000')
        );

        $this->assertEquals('/Date(1593432000022+0000)/', Date::toJsonDate($dateTime));
    }

    #[Test]
    public function it_can_convert_the_datetime_object_with_timezone_to_dot_net_json_date_format()
    {
        $dateTime = DateTime::createFromFormat(
            'Y-m-d H:i:s',
            '2020-06-29 12:00:00',
            new DateTimeZone('+0200')
        );

        $this->assertEquals('/Date(1593424800000+0200)/', Date::toJsonDate($dateTime));
    }

    #[Test]
    public function it_can_convert_dates_before_year_1970_to_dot_net_json_date_format()
    {
        $dateTime = DateTime::createFromFormat(
            'Y-m-d H:i:s',
            '1964-12-30 00:00:00',
            new DateTimeZone('+0000')
        );

        $this->assertEquals('/Date(-157939200000+0000)/', Date::toJsonDate($dateTime));
    }

    #[Test]
    public function it_can_convert_dates_before_year_1970_with_milliseconds()
    {
        $dateTime = DateTime::createFromFormat(
            'Y-m-d H:i:s.u',
            '1964-12-29 23:59:59.978000',
            new DateTimeZone('+0000')
        );

        $this->assertEquals('/Date(-157939200022+0000)/', Date::toJsonDate($dateTime));
    }

    #[Test]
    public function it_truncates_sub_millisecond_precision_instead_of_overflowing()
    {
        $dateTime = DateTime::createFromFormat(
            'Y-m-d H:i:s.u',
            '2020-06-29 12:00:00.999500',
            new DateTimeZone('+0000')
        );

        $this->assertEquals('/Date(1593432000999+0000)/', Date::toJsonDate($dateTime));
    }

    #[Test]
    public function it_can_convert_a_datetime_immutable_object()
    {
        $dateTime = new DateTimeImmutable('2020-06-29 12:00:00', new DateTimeZone('+0000'));

        $this->assertEquals('/Date(1593432000000+0000)/', Date::toJsonDate($dateTime));
    }

    #[Test]
    public function it_accepts_the_largest_offset_dot_net_can_emit()
    {
        $dateTime = Date::toDateTime('/Date(501033600000+1400)/');

        $this->assertEquals('1985-11-17 14:00:00', $dateTime->format('Y-m-d H:i:s'));
    }

    #[Test]
    public function it_throw_an_exception_if_json_date_format_is_invalid()
    {
        $this->expectException(InvalidJsonDateString::class);

        Date::toDateTime('/IvalidDate/');
    }

    #[Test]
    public function it_keeps_the_exception_message_short_and_free_of_control_characters()
    {
        $noisy = '/Date(1)/' . chr(10) . str_repeat('x', 500);

        try {
            Date::toDateTime($noisy);
            $this->fail('Expected an InvalidJsonDateString to be thrown.');
        } catch (InvalidJsonDateString $exception) {
            $this->assertStringNotContainsString(chr(10), $exception->getMessage());
            $this->assertLessThan(100, strlen($exception->getMessage()));
        }
    }

    #[Test]
    #[DataProvider('malformedJsonDates')]
    public function it_rejects_malformed_json_date_strings(string $date)
    {
        $this->expectException(InvalidJsonDateString::class);

        Date::toDateTime($date);
    }

    public static function malformedJsonDates(): array
    {
        return [
            'leading garbage' => ['garbage/Date(501033600000)/'],
            'trailing garbage' => ['/Date(501033600000)/garbage'],
            'missing slashes' => ['Date(501033600000)'],
            'missing leading slash' => ['Date(501033600000)/'],
            'missing trailing slash' => ['/Date(501033600000)'],
            'comma as offset sign' => ['/Date(501033600000,0200)/'],
            'truncated offset' => ['/Date(501033600000+02)/'],
            'empty timestamp' => ['/Date()/'],
            'non numeric timestamp' => ['/Date(abc)/'],
            'empty string' => [''],
            'trailing newline' => ['/Date(501033600000)/' . chr(10)],
            'leading newline' => [chr(10) . '/Date(501033600000)/'],
            'overlong timestamp' => ['/Date(9999999999999999)/'],
            'offset hours out of range' => ['/Date(501033600000+2500)/'],
            'offset minutes out of range' => ['/Date(501033600000+0060)/'],
            'absurd offset' => ['/Date(501033600000+9900)/'],
            'offset minutes only' => ['/Date(501033600000+0099)/'],
        ];
    }
}
