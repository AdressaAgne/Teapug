<?php

namespace App\Container\Helpers;

use Config, RouteHandler;

/**
 * 
 */
class Cache extends RouteHandler {
    
    private $cached_file_name;
    
    function __construct() {
        $this->cached_file_name = Config::$cache_folder.'cached_';
        $this->cached_file_name .= trim(str_replace('/', '_', $this->get_path()), '.').".html";
    }
    
    public function has_cached_file(){
        return file_exists($this->cached_file_name) && (filemtime($this->cached_file_name) + Config::$cache_time > time());
    }

    public function get_cached_file(){
        if($this->has_cached_file()) return file_get_contents($this->cached_file_name);
    }
    
    public function cache_file(string $data){
        $this->make_cache_folder();
        
        $file = fopen($this->cached_file_name, 'w');
        
        $w = fwrite($file, "<!--- Cached Version ".date('H:i:s - d/m/y', time())." --->\n".$data);
        
        fclose($file);
    }
    
    public function make_cache_folder(){
        if(!file_exists(Config::$cache_folder)){
            mkdir(Config::$cache_folder, 0777, true);
        }
    }
    
}
