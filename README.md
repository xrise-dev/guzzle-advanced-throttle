# hamburgscleanest/guzzle-advanced-throttle

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

A Guzzle middleware that can throttle requests according to (multiple) defined rules. 

It is also possible to define a caching strategy, 
e.g. get response from cache when rate limit is exceeded or always get cached value to spare your rate limits.

## Install

Via Composer

``` bash
$ composer require hamburgscleanest/guzzle-advanced-throttle
```

## Usage

### General use

 - TODO

### Caching

#### Available storage adapters

- array
- laravel

#### Without caching

Just throttle the requests.

``` php
TODO
```

#### With caching

Use cached responses when your defined rate limit is exceeded.

``` php
TODO
```

#### With forced caching

Always use cached responses when available to spare your rate limits.

``` php
TODO
```

## Changes

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email chroma91@gmail.com instead of using the issue tracker.

## Credits

- [Timo Prüße][link-author]
- [Andre Biel][link-andre]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/hamburgscleanest/guzzle-advanced-throttle.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/hamburgscleanest/guzzle-advanced-throttle/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/hamburgscleanest/guzzle-advanced-throttle.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/hamburgscleanest/guzzle-advanced-throttle.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/hamburgscleanest/guzzle-advanced-throttle.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/hamburgscleanest/guzzle-advanced-throttle
[link-travis]: https://travis-ci.org/hamburgscleanest/guzzle-advanced-throttle
[link-scrutinizer]: https://scrutinizer-ci.com/g/hamburgscleanest/guzzle-advanced-throttle/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/hamburgscleanest/guzzle-advanced-throttle
[link-downloads]: https://packagist.org/packages/hamburgscleanest/guzzle-advanced-throttle
[link-author]: https://github.com/Chroma91
[link-andre]: https://github.com/karllson
[link-contributors]: ../../contributors
