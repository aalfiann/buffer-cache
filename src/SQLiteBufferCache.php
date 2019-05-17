<?php
namespace aalfiann\BufferCache;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\Common\Cache\SQLite3Cache;
use Symfony\Component\Cache\Adapter\DoctrineAdapter;

/**
 * SQLiteBufferCache class
 *
 * @package    aalfiann/buffer-cache
 * @author     M ABD AZIZ ALFIAN <github.com/aalfiann>
 * @copyright  Copyright (c) 2019 M ABD AZIZ ALFIAN
 * @license    https://github.com/aalfiann/buffer-cache/blob/master/LICENSE.md  MIT License
 */
class SQLiteBufferCache extends Helper {

    /**
     * @var string $namespace   this is the namespace for cache
     */
    var $namespace = 'page';
    /**
     * @var string $table   this is the table name for cache
     */
    var $table = 'cache';
    /**
     * @var string $path    this is sqlite file location
     */
    var $path = 'cache/page/page_cache.sqlite3';
    /**
     * @var int $ttl    time to live of the cache
     */
    var $ttl = 18000;
    /**
     * @var array $ext  Only cache for spesific extension
     */
    var $ext = [
        '.htm','.html','.xhtml','.asp','.aspx','.css',
        '.php','.js','.jsp','.cfm','.md','.xml','.rss'
    ];
    /**
     * @var object class (construct)
     */
    var $_provider;
    /**
     * @var object class (construct)
     */
    var $_cache;
    /**
     * @var string keycache (construct)
     */
    var $_keycache;
    /**
     * @var bool 
     */
    var $_cancelBuffer = false;

    function __construct($options=array()) {
        if(!empty($options)){
            foreach ($options as $key => $value) {
                $this->{$key} = $value;
            }
        }
        if(!file_exists(dirname($this->path))) mkdir(dirname($this->path), 0777, true);
        $this->_provider = new SQLite3Cache(new \SQLite3($this->path), $this->table);
        $this->_cache = new DoctrineAdapter($this->_provider,$this->namespace,0);
        $this->_keycache = str_replace(['{','}','(',')','/','\'','@','?','*',':','<','>','|',' '],'.',strtolower($this->namespace.'.'.$_SERVER['REQUEST_URI']));
    }

    /**
     * Start to writing page cache, put this on top of your script to cache
     */
    public function start(){;
        if ($this->_cache->hasItem($this->_keycache)) {
            $data = $this->_cache->getItem($this->_keycache);
            echo base64_decode($data->get());
            exit;
        }
    }

    /**
     * Buffer to writing page cache
     * 
     * @param callback $buffer
     */
    public function startBuffer($buffer=null){;
        ob_start($buffer);
    }

    /**
     * Cancel Buffer
     * 
     * @param callback $buffer
     */
    public function cancelBuffer(){;
        ob_end_flush();
        $this->_cancelBuffer = true;
    }

    /**
     * This function will write the cache, so put this on very bottom on your script
     * 
     * @param callback $buffer
     */
    public function end($buffer=null){
        if(!$this->_cancelBuffer){
            if(!$this->isHaveParam()){
                if(!$this->isExtension() || ($this->isExtension() && $this->isExtensionAllowed($this->ext))){
                    $data = base64_encode((!empty($buffer) ? $buffer(ob_get_contents()) : ob_get_contents()));
                    $newdata = $this->_cache->getItem($this->_keycache);
                    $newdata->set($data)->expiresAfter($this->ttl);
                    $this->_cache->save($newdata);
                }
            }
            ob_end_flush();
        }
    }

}
