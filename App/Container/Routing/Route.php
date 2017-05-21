<?php
namespace App\Container\Routing;

use Config, Protocol;

class Route {
    
    public static $routes = [
        GET       => [],
        POST      => [],
        PATCH     => [],
        PUT       => [],
        DELETE    => [],
        ERROR     => [],
    ];
    
    /**
     * Store all Directs in a array
     * @param  object $route Direct
     * @return string URI
     */
    public static function getCurrentRoute($route){
        
        Config::$route = $route;
        
        if(Config::$debug_mode){
            self::checkForMissingMethods();
        }
        
        if($_SERVER['REQUEST_METHOD'] == "POST"){
            //CSRF token
            if(!isset($_POST['_token'])) return self::set_error('401', ['Missing token']);
            
            if($_POST['_token'] != $_SESSION['_token']){
               return self::set_error('418');
            } 

            switch(strtoupper($_POST['_method'])) {
                    
                case PUT:
                    return self::method(PUT, $route);
                break;

                case PATCH:
                    return self::method(PATCH, $route);
                break;

                case DELETE:
                    return self::method(DELETE, $route);
                break;
              
                case POST:
                    return self::method(POST, $route);
                break;

                default:
                    return self::set_error('405');
                break;
            }
        } else {
            return self::method(GET, $route);
        }
    }
    
    private static function checkForMissingMethods(){
        $missing = [];
            
        foreach(self::$routes as $key => $http){
            foreach($http as $class){
                if(gettype($class['callback']) == 'string') {
                    $class = explode('@', $class['callback']);
                    if(count($class) == 2 && !method_exists($class[0], $class[1])){
                        $missing[] = $class;
                    }
                }
            }
        }
        if(!empty($missing)){
            print_r($missing);
            die("Missing controllers");
        }
    }
    
    
    public static function method($method, $route){
        
        if(array_key_exists($route, self::$routes[$method])){
            $key = self::$routes[$method][$route];
            
            if(isset($key['filter']['auth'])){
                if(!isset($_SESSION['uuid'])){
                    if(isset($key['filter']['callback'])){
                        return call_user_func_array($key['filter']['callback'], [(object)$key]);   
                    }
                    return self::set_error('403');   
                }
            }
            
            if(isset($key['filter']['before'])){
                call_user_func_array($key['filter']['before'], [(object)$key]);
            }
            
            return self::$routes[$method][$route];
        } else {
            return self::set_error('404');
        }
    }
    
    public static function set_error($error, $route = ''){
        Protocol::send($error);
        return array_key_exists($error, self::$routes[ERROR]) ? self::$routes[ERROR][$error] : ['error' => "$error: Please set up a $error page"];    
    }
    
    
    public static function lists(){
        return self::$routes;
    }
}