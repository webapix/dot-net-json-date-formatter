# Changelog

All notable changes to `dot-net-json-date-formatter` will be documented in this file

## 2.0.0 - unreleased

### Changed
- **BC break:** the minimum supported PHP version is now 8.2
- **BC break:** `Date::toDateTime()` only accepts the exact `/Date(<milliseconds>[<+|-><hhmm>])/` format; strings with surrounding characters, a trailing newline, a missing slash, or a truncated offset are now rejected
- **BC break:** the timezone offset is validated against the range .Net can emit (hours `00`-`14`, minutes `00`-`59`); values such as `+9900`, `+2500` and `+0060` used to be silently accepted and produced a wrong wall-clock time
- **BC break:** timestamps longer than 15 digits are rejected instead of silently saturating to `PHP_INT_MAX`
- a 64-bit PHP build is now required; millisecond timestamps exceed the 32-bit integer range
- the exception message quotes at most 50 characters of the offending input, with control characters stripped
- **BC break:** `Date::toDateTime()` declares a `string` parameter type, so it no longer silently accepts null or int
- `Date::toJsonDate()` now accepts any `DateTimeInterface`, including `DateTimeImmutable`
- sub-millisecond precision is truncated rather than rounded

### Fixed
- parsing a negative timestamp that carries milliseconds no longer throws
- converting a date with more than 999500 microseconds no longer emits a corrupt four-digit millisecond field
- converting a pre-1970 date that carries milliseconds no longer produces a wrong timestamp

## 1.1.0 - 2022-01-03

- add support for PHP 8.1

## 1.0.1 - 2021-07-06

- fix negative timestamp parsing

## 1.0.0 - 2020-07-07

- initial release