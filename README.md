# Buffer Cache

[![Latest Stable Version](https://img.shields.io/packagist/v/aalfiann/buffer-cache.svg)](https://packagist.org/packages/aalfiann/buffer-cache)
[![Total Downloads](https://img.shields.io/packagist/dt/aalfiann/buffer-cache.svg)](https://packagist.org/packages/aalfiann/buffer-cache)
[![License](https://img.shields.io/packagist/l/aalfiann/buffer-cache.svg)](https://github.com/aalfiann/buffer-cache/blob/HEAD/LICENSE.md)

Cache the response page with buffer support.  
Sometimes we just want to simply cache the output response page.

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
// With Filesystem
// use aalfiann\BufferCache\FilesystemBufferCache;

// With SQLite3
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

// With Filesystem
// $cache = new FilesystemBufferCache([
//     // Set ttl cache
//     'ttl' => 120
// ]);

// With SQLite3
$cache = new SQLiteBufferCache([
    // Set ttl cache
    'ttl' => 120
]);

// Start cache
$cache->start();

// Start buffer
//$cache->startBuffer();        // without callback
$cache->startBuffer('modify');  // with callback

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
//$cache->end();        // without callback
$cache->end('modify');  // with callback
```

## Url page with extension
This will not cache for url page with binary extension like .exe, .rar, .zip, .mp4, .mp3, etc.  
You have to whitelist the extension if you want to cache.  

```php
// Example if you want to allow cache for phyton dan text extension
$cache->addExtension('.py');
$cache->addExtension('.txt');
```

The default extensions which is already allowed are:
```php
var $ext = [
    '.htm','.html','.xhtml','.asp','.aspx','.css',
    '.php','.js','.jsp','.cfm','.md','.xml','.rss'
];
```

## Note
- Url page with parameter will not going to cache.
- I only create buffer cache with using **SQLite3** and **Filesystem**, so contribution are welcome.