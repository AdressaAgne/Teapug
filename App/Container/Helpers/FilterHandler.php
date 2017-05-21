<?php

namespace App\Container\Helpers;

use Protocol, Cache;

class FilterHandler {
    
    private static $filters = [];
    
    private static function filter_setup(){
        self::addFilter('http_code', function($filter, $data){
            Protocol::send($filter);
        });
        
        self::addFilter('cache', function($filter, $data){
            $cache = new Cache();
            $cache->cache_file($data);
        });
        
        self::addFilter('after', function($filter, $data, $request){
            call_user_func_array($filter, [(object)$request]);
        });
    }
    
    public static function filter($filter, $data, $request){
        self::filter_setup();
        
        foreach (self::$filters as $key => $value) {
            if(isset($filter[$key])){
                call_user_func_array($value, [$filter[$key], $data, $request]);
            }
        }
    }
    
    private static function addFilter($name, callable $callable){
        self::$filters[$name] = $callable;
    }
    
}