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

## 3.1.0

### Fixed
#### Changed the cached representation of the response.
This should solve the issue where one had to use `(string) $response->getBody()` instead of `$response->getBody()->getContents()`.

### Laravel storage adapters

You can disable caching for empty responses in the config now by setting `allow_empty` to `false`.

Check out the [docs](https://github.com/hamburgscleanest/guzzle-advanced-throttle#laravel-drivers) for more information on how to set it.

----------

## 3.0.1

### Fixed
- Fixed a problem when generating the cache key for a request without parameters

----------

## 3.0.0

This release adds compatibility with Laravel 5.7 (Illuminate).

----------

## 2.0.7

### Improvement
The order of request parameters is now irrelevant for the cache.
If the values of the parameters are the same, the requests will be treated as the same, too.

For example if you request `/test?a=1&b=2`,  
the cache will know that it yields the same response as `/test?b=2&a=1`.

----------

## 2.0.6

### Fixed
- The request count was not properly reset because `RateLimiter::getCurrentRequestCount()` wasn't used internally.

Thanks to @huisman303 for finding this!

----------

## 2.0.5

### Added
- You can now provide a custom caching strategy instead of being limited to the default ones.
  
  Your custom caching strategy must implement `CacheStrategy`.
  It is suggested you use `Cacheable` for a parent class.
  This will give a good head start, see `ForceCache` and `Cache` for ideas.

  To use your custom caching strategy, you'll need to pass the fully qualified cache name to `RequestLimitRuleset`.

  ```php
    $rules = new RequestLimitRuleset([ ... ], 
                                MyCustomCacheStrategy::class, 
                                'array', 
                                new Repository(...));
                                
    $throttle = new ThrottleMiddleware($rules);                                
  ```
  
  > Thanks to @LightGuard

----------

## 2.0.4
- The middleware can now be called as a function instead of calling the `handle` method. 

``` php
 $throttle = new ThrottleMiddleware($rules);
 
 $stack->push($throttle());
```
 
---------- 

## 2.0.3

### Fixed
- Fixed issue in Redis driver 

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
