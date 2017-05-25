<?php
namespace App\Container\Routing;

use Config, Protocol, EventListener;

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

        if(Config::$debug_mode)
            self::checkForMissingMethods();

        if($_SERVER['REQUEST_METHOD'] == POST){
            //CSRF token
            if(!isset($_POST['_token']))
                return self::set_error('400');

            if($_POST['_token'] != $_SESSION['_token'])
                return self::set_error('418');

            if(in_array(strtoupper($_POST['_method']), [PUT, PATCH, DELETE, POST]))
                return self::method(strtoupper($_POST['_method']), $route);

            return self::set_error('405');
        }

        return self::method(GET, $route);
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
            dd("Missing controllers", $missing);
        }
    }


    public static function method($method, $route){
        //reset csrf
        $_SESSION['_token'] = uniqid();
        Config::$form_token = $_SESSION['_token'];

        if(array_key_exists($route, self::$routes[$method])){

            if(EventListener::have(E_AUTH)){
                EventListener::call(E_AUTH);
                return self::set_error('403');
            }

            EventListener::call(E_BEFORE);

            return self::$routes[$method][$route];
        }
        return self::set_error('404');
    }

    public static function set_error($error, $route = ''){
        Protocol::send($error);
        return array_key_exists($error, self::$routes[ERROR]) ? self::$routes[ERROR][$error] : ['error' => "$error: Please set up a $error page"];
    }

    public static function lists(){
        return self::$routes;
    }
}