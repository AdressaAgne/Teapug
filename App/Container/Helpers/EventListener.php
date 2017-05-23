<?php

namespace App\Container\Helpers;

use RouteHandler, Closure;

class EventListener {
    
    private static $events = [];

    public static function add($event = E_AFTER, callable $callable, $id = null){
        if(RouteHandler::page() != trim($id->get_route(), '/')) return;
        
        self::$events[$event][] = (object) [
            'callable' => $callable,
            'id'       => $id,
            'event'    => $event,
        ];
    }
    
    public static function call(string $event, $obj = null){
        if(!isset(self::$events[$event])) return;
        foreach (self::$events[$event] as $url => $value) {
            call_user_func_array($value->callable, [$value->id, $value->event, $obj]);
        }
    }
    
    public static function have($event){
        return isset(self::$events[$event]);
    }
    
    public static function all(){
        return self::$events;
    }
        
}