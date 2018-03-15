# Changelog

All Notable changes to `guzzle-advanced-throttle` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## Next

### Added
- Host wildcards: [WILDCARDS](README.md#Wildcards)
- More flexible configuration: [USAGE](README.md#Usage)

### Deprecated
- Nothing

### Fixed
- Nothing

### Removed
- Nothing

### Security
- Nothing

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
