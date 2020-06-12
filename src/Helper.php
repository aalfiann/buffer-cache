<?php
namespace aalfiann\BufferCache;

/**
 * Helper class
 *
 * @package    aalfiann/buffer-cache
 * @author     M ABD AZIZ ALFIAN <github.com/aalfiann>
 * @copyright  Copyright (c) 2019 M ABD AZIZ ALFIAN
 * @license    https://github.com/aalfiann/buffer-cache/blob/master/LICENSE.md  MIT License
 */
class Helper {

    /**
     * @var string $namespace   this is the namespace for cache
     */
    var $namespace = 'page';
    /**
     * @var int $ttl                    Time to live of the cache
     */
    var $ttl = 18000;
    /**
     * @var array $ext                  Only cache for spesific extension
     */
    var $ext = [
        '.htm','.html','.xhtml','.asp','.aspx','.css',
        '.php','.js','.jsp','.cfm','.md','.xml','.rss'
    ];
    /**
     * @var bool $http_cache            To activate HTTP Cache
     */
    var $http_cache = false;
    /**
     * @var integer $http_maxage        Set maxage of HTTP Cache
     */
    var $http_maxage = 3600;
    /**
     * @var bool $cache_empty_content   Cache empty content 
     */
    var $cache_empty_content = false;
    /**
     * @var bool $cache_query_param     Cache url with query parameter 
     */
    var $cache_query_param = false;
    /**
     * @var array $filesystem           Filesystem parameters or options
     */
    var $filesystem = [
        'path' => 'cache/page'
    ];
    /**
     * @var array $sqlite3              SQLite3 parameters or options
     */
    var $sqlite3 = [
        'table' => 'cache',
        'path' => 'cache/page/page_cache.sqlite3'
    ];
    /**
     * @var array $predis               Predis parameters or options. See https://packagist.org/packages/predis/predis.
     */
    var $predis = [
        'scheme' => 'tcp',
        'host'   => '127.0.0.1',
        'port'   => 6379
    ];

    /**
     * Using HTTP Cache
     * @return this
     */
    public function useHttpCache($maxage) {
        $this->http_cache = true;
        $this->http_maxage = $maxage;
        return $this;
    }

    /**
     * Is using HTTP Cache
     * @return bool
     */
    public function isHttpCache() {
        return $this->http_cache;
    }

    /**
     * Check HTTP Cache by Etag
     */
    public function checkEtag() {
        if(isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) == $this->_etag) { 
            header("HTTP/1.1 304 Not Modified"); 
            exit;
        }
    }

    /**
     * Add response with HTTP Cache
     */
    public function withHttpCache() {
        $expires = (time() + $this->http_maxage);
        header("Cache-Control: public, must-revalidate, max-age=".$this->http_maxage);
        header("Expires: ".gmdate('D, d M Y H:i:s',$expires)." GMT");
        header('Etag: '.$this->_etag);
    }

    /**
     * Add Extension Type
     * 
     * @param string $ext   this is an extension with .(dot). Example .py for phyton extension.
     * @return this
     */
    public function addExtension($ext){
        $this->ext[] = $ext;
        return $this;
    }

    /**
     * Get File Extension
     * 
     * @return string
     */
    public function getFileExtension() {
        return strrchr($this->_keycache,'.');
    }

    /**
     * Check the page type is extension or not
     * 
     * @return bool
     */
    public function isExtension(){
        $url = trim ( $_SERVER['REQUEST_URI'] ,'/' );
        if(strpos(substr($url, -6),'.') !== false){
            return true;
        }
        return false;
    }

    /**
     * Check the extension is allowed or not 
     * 
     * @param array $ext    this is an array for type extension
     * @return bool
     */
    public function isExtensionAllowed($ext){
        $url = trim ( $_SERVER['REQUEST_URI'] ,'/' );
        if($this->isExtension($url)){
            for($i=-2;$i>=-6;$i--){
                if(in_array(substr($url, $i),$ext)){
                    return true;
                }   
            }
        }
        return false;
    }

    /**
     * Check the url is have param or not
     * 
     * @return bool
     */
    public function isHaveParam(){
        // Detect url file
        $url = trim ( $_SERVER['REQUEST_URI'] ,'/' );
        // Build path file
        $link_array = explode('/',$url);
        $file = "";
        foreach ($link_array as $key){
            $file .= $key.'-';
        }
        $file = substr($file, 0, -1);
        if(strpos($file,'?') !== false ){
            return true;
        }
        return false;
    }

}
