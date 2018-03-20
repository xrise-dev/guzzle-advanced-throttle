# Changelog

All Notable changes to `guzzle-advanced-throttle` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## Next

### Added
- Nothing

### Deprecated
- Nothing

### Fixed
- Nothing

### Removed
- Nothing

### Security
- Nothing

----------

## 2.0.2

### Fixed
- Fixed problems with Laravel cache drivers

----------


## 2.0.1

### Fixed
There was a problem in the composer.json that for example broke the compatibility to Drupal 8. This is fixed in this release. 

Thanks to @berenddeboer !

----------

## 2.0.0

### Added
- Host wildcards: [WILDCARDS](README.md#wildcards)
- More flexible configuration: [USAGE](README.md#usage) - **BREAKING**

----------

## 1.0.7

### Added

- Support for Laravel 5.5
----------

## 1.0.6

### Fixed

- Respect request parameters (query or body) for caching

----------

## 1.0.5

### Fixed

- Do not only cache responses with status code 200 but rather filter out responses with error status codes: 4xx, 5xx

----------

## 1.0.4

### Added
- Possibility to define the TTL for the cache in the config 

----------

## 1.0.3

### Added
- Simplified the config format of the laravel cache adapter

----------

## 1.0.2

### Added
- Possibility to configure the laravel cache adapter

----------

## 1.0.1 

### Fixed
- Host not recognized

----------

## 1.0.0

Initial release
