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
