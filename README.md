# Buffer Cache

[![Latest Stable Version](https://img.shields.io/packagist/v/aalfiann/buffer-cache.svg)](https://packagist.org/packages/aalfiann/buffer-cache)
[![Total Downloads](https://img.shields.io/packagist/dt/aalfiann/buffer-cache.svg)](https://packagist.org/packages/aalfiann/buffer-cache)
[![License](https://img.shields.io/packagist/l/aalfiann/buffer-cache.svg)](https://github.com/aalfiann/buffer-cache/blob/HEAD/LICENSE.md)

Cache the response page with buffer support.  
Sometimes we just want to simply cache the response page.

## Dependencies
- Symfony Cache >> symfony/cache
- Doctrine Cache >> doctrine/cache

## Installation

Install this package via [Composer](https://getcomposer.org/).
```
composer install aalfiann/buffer-cache
```

## Usage
```php
use aalfiann\BufferCache\SQLiteBufferCache;
require 'vendor/autoload.php';

// Callback to modify html source before cache
function modify($buffer) {
    // Test inject javascript
    $javascript = '<script>console.log("Cache was generated at '.date('Y-m-d H:i:s').'")</script>';
    $buffer = explode('</body>',$buffer);
    $buffer = implode($javascript.'</body>',$buffer);
    return $buffer;
}

$cache = new SQLiteBufferCache([
    // Set ttl cache
    'ttl' => 120
]);

// Start cache
$cache->start();

// Start buffer
$cache->startBuffer('modify');  //just set to null if you don't have buffer callback

// Example to render page
echo '<html>
    <head>
        <title>Test Page</title>
    </head>
    <body>Just test to cache the response page</body>
</html>';

// for condition if page failed to render
//$cache->cancelBuffer();

// End cache
$cache->end('modify');
```

## Note
- Url page with parameter will not going to cache.
- I only create buffer cache for **SQLite3**, so contribution are welcome.