# Dotnet JSON date
[![Build Status](https://travis-ci.com/webapix/dot-net-json-date-formatter.svg?branch=master)](https://travis-ci.com/webapix/dot-net-json-date-formatter)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

Parse [.Net JSON dates](https://docs.microsoft.com/en-us/previous-versions/dotnet/articles/bb299886(v=msdn.10)#from-javascript-literals-to-json) to [DateTime](https://www.php.net/manual/en/class.datetime.php) object, and convert [DateTime](https://www.php.net/manual/en/class.datetime.php) object to .Net JSON date format.

## Installation

You can install the package via composer:

```bash
composer require webapix/dot-net-json-date-formatter
```

## Usage

To parse .NET JSON dates:
``` php
use \Webapix\DotNetJsonDate\Date;

Date::toDateTime('/Date(1593432000000+0200)/'); // return with \DateTime object
```

If the Json string is invalid, it will throw an \Webapix\DotNetJsonDate\InvalidJsonDateString exception.

To convert DateTime to JSON date:
``` php
use \Webapix\DotNetJsonDate\Date;

$dateTime = DateTime::createFromFormat('Y-m-d H:i:s', '2020-06-29 12:00:00');
Date::toJsonDate($dateTime); // return with: /Date(1593432000000+0000)/
```

## Testing

``` bash
composer test
```

## Postcardware
According to the postcardware concept, if you use the software for your project(s) we would appreciate to receive a postcard of your hometown.

Please send it to:

WEBAPIX KFT.   
PF.: 941   
1535 Budapest   
Hungary

## Contributing

Contributions are welcome! When contributing to this repository, please first discuss the change you wish to make via issue, email, or any other method with the owners of this repository before making a change.

## Security

If you discover any security related issues, please email pdo@webapix.hu instead of using the issue tracker.

## Credits

- [WEBAPIX Kft.](https://webapix.hu)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.