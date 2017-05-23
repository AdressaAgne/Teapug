<?php

namespace App\Container\Routing;

use Config, EventListener, Protocol, Cache;

class Direct extends Route{
    
    private $middleware = [];
    private $route = '';
    private $type = '';
    
    
    public function __construct(string $route, $callback, string $type){
        
        $regex = "/([a-zA-Z0-9*])\/(\{(.*)\})/";

        $var_regex = '/\{(.*?)\}/';
        
        preg_match_all($var_regex, $route, $vars);
        
        $route = preg_replace($regex, "$1", $route);
        
        $this->route = $route;
        $this->type = $type;
    
        $callback = (gettype($callback) == 'string') ? Config::$controllers.$callback : $callback;
        
        parent::$routes[$type][$route] = [
            'callback' => $callback,
            'vars' => $vars[1],
            'filter' => [],
        ];

    }
    
    public function get_route(){
        return $this->route;
    }
    
    /**
     * redirect to a page
     * @param string $page
     */
    public static function re(string $page){
        header("location: {$page}");
    }
    

    /**
     * Create a new Direct
     * @param  string  $a URI
     * @param  callback $b 
     * @return object   Direct Object
     * and so on...
     */
    public static function get(string $a, $b){
        return new Direct($a, $b, GET);
    }
    
    public static function delete(string $a, $b){
        return new Direct($a, $b, DELETE);
    }
    
    public static function put(string $a, $b){
        return new Direct($a, $b, PUT);
    }
    
    public static function patch(string $a, $b){
        return new Direct($a, $b, PATCH);
    }
   
    public static function post(string $a, $b){
        return new Direct($a, $b, POST);
    }
    
    public static function error(string $a, $b){
        return new Direct($a, $b, ERROR);
    }
    
    public static function debug(string $a, $b, array $http = [GET]){
        if(Config::$debug_mode) return self::on($http, $a, $b);
    }
    
    public static function all(string $a, $b){
        foreach ([GET, POST, PATCH, PUT, DELETE] as $value) {
            call_user_func_array("Direct::$value", [$a, $b]);
        }
    }
    
    public static function on(array $http, string $a, $b) {
        foreach ($http as $value) {
            if(!in_array($value, [GET, POST, PATCH, PUT, ERROR, DELETE])) continue;
            call_user_func_array("Direct::$value", [$a, $b]);
        }
    }
    
    
    public static function stack(string $url, string $controller){
        //Overlook page
        self::get($url, "$controller@index");
        
        // Item Page
        self::get("$url/{id}", "$controller@item");
        
        // Delete page
        self::delete($url, "$controller@delete")->auth();
        
        //Edit page
        self::patch("$url/edit", "$controller@patch")->auth();
        self::get("$url/edit/{id}", "$controller@edit")->auth();
        
        // Update Page
        self::put("$url/create", "$controller@put")->auth();
    }
    
    private function addEventListener($event = E_AFTER, $callback, $id){
        EventListener::add($event, $callback, $id);
        return $this;
    }
    
    public function http_code(int $code, $msg = null){
        return $this->addEventListener(E_AFTER, function() use($code, $msg){
            Protocol::send($code, $msg);
        }, $this);
    }
    
    private function Authenticate($grade, callable $callback = null){
        if(is_callable($callback)) return $this->addEventListener(E_AUTH, $callback, $this);
        
        return $this->addEventListener(E_AUTH, function(){
            if(isset($_SESSION['uuid']))
                Direct::re('/login');
        }, $this);
    }
    
    public function Cache(callable $callable = null){
        $cache = function() {};
        if(is_callable($callable) && call_user_func($callable)) return $this->addEventListener(E_AFTER, $cache, $this);
        if(!is_callable($callable)) return $this->addEventListener(E_AFTER, $cache, $this);
    }
    
    public function Auth(callable $callback = null){
        return $this->Authenticate(3, $callback);
    }
    
    public function Mod(callable $callback = null){
        return $this->Authenticate(2, $callback);
    }
    
    public function Admin(callable $callback = null){
        return $this->Authenticate(1, $callback);
    }
    
    public function before(callable $callable = null){
        if(is_callable($callable))
            $this->addEventListener(E_BEFORE, $callable, $this);
        return $this;
    }
    
    public function after(callable $callable = null){
        if(is_callable($callable))
            $this->addEventListener(E_AFTER, $callable, $this);
        return $this;
    }
    
    public function render(callable $callable = null){
        if(is_callable($callable))
            $this->addEventListener(E_RENDER, $callable, $this);
        return $this;
    }
    
    
    /**
     * Gets called when a method on \App\Direct does not exist
     * @private
     * @param string $func 
     * @param string $args 
     */
    public function __call($func, $args){
        dd($func."(".count($args)." args) is not a method of Direct");
    }
    
}