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

## Available Constructor
1. Cache with SQLite3
    ```php
    $cache = new SQLiteBufferCache([
        // options here
    ]);
    ```

2. Cache with Filesystem
    ```php
    $cache = new FilesystemBufferCache([
        // options here
    ]);
    ```

## Options in constructor class
Here is the default options in constructor class
```php
[
    'ttl' => 18000,         // time to live cache
    'http_cache' => false,  // use http cache
    'http_maxage' => 3600,  // maxage of http cache
    'ext' => [              // Allow cache for url with extension 
        '.htm','.html','.xhtml','.asp','.aspx','.css',
        '.php','.js','.jsp','.cfm','.md','.xml','.rss'
    ]
]
```

## Url page with extension
This will not cache for url page with binary extension like .exe, .rar, .zip, .mp4, .mp3, etc.  
You have to whitelist the extension if you want to cache.  

The default extensions which is already allowed are:
```php
var $ext = [
    '.htm','.html','.xhtml','.asp','.aspx','.css',
    '.php','.js','.jsp','.cfm','.md','.xml','.rss'
];
```

Example if you want to add more `.py` and `.txt`.
```php
$cache->addExtension('.py');
$cache->addExtension('.txt');
```

Example if you want just allow `.js` and `.css` only.

1. By options in constructor class
    ```php
    $cache = new SQLiteBufferCache([
        'ext' => ['.js', '.css']
    ]);
    ```

2. Or by properties
    ```php
    $cache->ext = ['.js', '.css'];
    ```

## Http Cache
This library is support http cache but inactivated by default.  
If you want to use this, there is three ways :

1. By options in constructor class
    ```php
    $cache = new SQLiteBufferCache([
        'http_cache' => true,
        'http_maxage' => 3600
    ]);
    ```

2. Or by function
    ```php
    $cache->useHttpCache(3600);
    ```

3. Or by properties
    ```php
    $cache->http_cache = true;
    $cache->http_maxage = 3600;
    ```


## Note
- Like the browser, url pages with parameters will not be cached.
- I only create buffer cache with using **SQLite3** and **Filesystem**, so contribution are welcome.