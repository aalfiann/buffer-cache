<?php
namespace aalfiann\BufferCache;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\Common\Cache\FilesystemCache;
use Symfony\Component\Cache\Adapter\DoctrineAdapter;

/**
 * FilesystemBufferCache class
 *
 * @package    aalfiann/buffer-cache
 * @author     M ABD AZIZ ALFIAN <github.com/aalfiann>
 * @copyright  Copyright (c) 2020 M ABD AZIZ ALFIAN
 * @license    https://github.com/aalfiann/buffer-cache/blob/master/LICENSE.md  MIT License
 */
class FilesystemBufferCache extends Helper {

    /**
     * @var object class (construct)
     */
    var $_provider;
    /**
     * @var object class (construct)
     */
    var $_cache;
    /**
     * @var string class (construct)
     */
    var $_keycache;
    /**
     * @var string class (construct)
     */
    var $_etag;
    /**
     * @var bool $_cancelBuffer this is to cancel buffer cache
     */
    var $_cancelBuffer = false;
    

    function __construct($options=array()) {
        if(!empty($options)){
            foreach ($options as $key => $value) {
                $this->{$key} = $value;
            }
        }
        if(!file_exists(dirname($this->filesystem['path']))) mkdir(dirname($this->filesystem['path']), 0777, true);
        $this->_provider = new FilesystemCache($this->filesystem['path']);
        $this->_cache = new DoctrineAdapter($this->_provider,$this->namespace,0);
        $this->_keycache = str_replace(['{','}','(',')','/','\'','@','?','*',':','<','>','|',' '],'.',strtolower($this->namespace.'.'.$_SERVER['REQUEST_URI']));
        $this->_etag = '"'.md5($this->_keycache).'"';
    }

    /**
     * Start to writing page cache, put this on top of your script to cache
     * 
     * @var callback $cb    this is the for doing anonymous function when cache is found
     */
    public function start(callable $cb=null){
        if ($this->_cache->hasItem($this->_keycache)) {
            if($this->isHttpCache()) $this->checkEtag();
            $cb;
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
    public function startBuffer(callable $buffer=null){
        ob_start($buffer);
    }

    /**
     * Cancel Buffer
     */
    public function cancelBuffer(){
        ob_end_flush();
        $this->_cancelBuffer = true;
    }

    /**
     * This function will write the cache, so put this on very bottom on your script
     * 
     * @param callback $buffer
     */
    public function end(callable $buffer=null){
        if(!$this->_cancelBuffer){
            if(!$this->isHaveParam()){
                if(!$this->isExtension() || ($this->isExtension() && $this->isExtensionAllowed($this->ext))){
                    if(!empty(ob_get_contents())) {
                        $data = base64_encode((!empty($buffer) ? $buffer(ob_get_contents()) : ob_get_contents()));
                        $newdata = $this->_cache->getItem($this->_keycache);
                        $newdata->set($data)->expiresAfter($this->ttl);
                        $this->_cache->save($newdata);
                        if($this->isHttpCache()) $this->withHttpCache();
                    } else {
                        if($this->cache_empty_content) {
                            $data = base64_encode((!empty($buffer) ? $buffer(ob_get_contents()) : ob_get_contents()));
                            $newdata = $this->_cache->getItem($this->_keycache);
                            $newdata->set($data)->expiresAfter($this->ttl);
                            $this->_cache->save($newdata);
                            if($this->isHttpCache()) $this->withHttpCache();
                        }
                    }
                }
            }
            ob_end_flush();
        }
    }

}
